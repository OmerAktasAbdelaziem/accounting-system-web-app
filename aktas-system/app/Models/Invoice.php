<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'date',
        'sub_total',
        'tax',
        'total',
        'status',
        'branch_id',
    ];

    protected $casts = [
        'date' => 'date',
        'sub_total' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Add line item to invoice
     */
    public function addItem($productId, $quantity, $unitPrice, $description = null)
    {
        $lineTotal = $quantity * $unitPrice;

        return $this->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
            'description' => $description,
        ]);
    }

    /**
     * Recalculate invoice totals from items
     */
    public function recalculateTotals(): void
    {
        $subTotal = $this->items->sum('line_total');
        $tax = $subTotal * 0.15; // 15% default tax
        $total = $subTotal + $tax;

        $this->update([
            'sub_total' => $subTotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }
}
