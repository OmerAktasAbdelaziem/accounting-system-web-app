<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'quantity',
        'transfer_date',
        'created_by',
        'status',
        'reference_number',
        'notes',
        'notes_ar',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the source warehouse
     */
    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    /**
     * Get the destination warehouse
     */
    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    /**
     * Get the user who created this transfer
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Complete the warehouse transfer
     */
    public function complete(): bool
    {
        if ($this->status !== 'in_transit') {
            return false;
        }

        // Update source warehouse inventory
        $fromInventory = WarehouseInventory::where('warehouse_id', $this->from_warehouse_id)
            ->where('product_id', $this->product_id)
            ->first();

        if (!$fromInventory || $fromInventory->quantity < $this->quantity) {
            return false;
        }

        $fromInventory->decrement('quantity', $this->quantity);

        // Update destination warehouse inventory
        $toInventory = WarehouseInventory::where('warehouse_id', $this->to_warehouse_id)
            ->where('product_id', $this->product_id)
            ->first();

        if ($toInventory) {
            $toInventory->increment('quantity', $this->quantity);
        } else {
            WarehouseInventory::create([
                'warehouse_id' => $this->to_warehouse_id,
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
            ]);
        }

        $this->update(['status' => 'received']);
        return true;
    }

    /**
     * Reject the warehouse transfer
     */
    public function reject(): bool
    {
        if (!in_array($this->status, ['pending', 'in_transit'])) {
            return false;
        }

        $this->update(['status' => 'rejected']);
        return true;
    }

    /**
     * Scope to get pending transfers
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get transfers in a date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transfer_date', [$startDate, $endDate]);
    }
}
