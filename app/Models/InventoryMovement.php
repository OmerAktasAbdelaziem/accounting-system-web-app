<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'movement_type',
        'movement_type_ar',
        'quantity',
        'unit_price',
        'reference_type',
        'reference_id',
        'notes',
        'notes_ar',
        'created_by',
        'stock_before',
        'stock_after',
    ];

    /**
     * Get the product associated with this movement
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who created this movement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the movement type label in English/Arabic
     */
    public function getMovementLabel(): string
    {
        $labels = [
            'incoming' => 'Incoming',
            'outgoing' => 'Outgoing',
            'waste' => 'Waste',
            'return' => 'Return',
            'adjustment' => 'Adjustment',
            'transfer_out' => 'Transfer (Out)',
            'transfer_in' => 'Transfer (In)',
        ];
        return $labels[$this->movement_type] ?? $this->movement_type;
    }

    /**
     * Calculate the total value of this movement
     */
    public function getTotalValueAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Scope to get movements in a date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get movements by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('movement_type', $type);
    }
}
