<?php

namespace App\Models;

use App\Models\Concerns\HasBranches;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;
    use \App\Models\Concerns\HasBranches;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'date',
        'sub_total',
        'tax',
        'vat_rate',
        'vat_amount',
        'total',
        'branch_id',
        'merchant_id',
    ];

    protected $casts = [
        'date' => 'date',
        'sub_total' => 'decimal:2',
        'tax' => 'decimal:2',
        'vat_rate' => 'decimal:5,2',
        'vat_amount' => 'decimal:10,2',
        'total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
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
     * Recalculate invoice totals from items including VAT
     */
    public function recalculateTotals(): void
    {
        $subTotal = $this->items->sum('line_total');
        
        // Get merchant's VAT rate if set, otherwise use default
        $vatRate = 0;
        if ($this->merchant_id) {
            $merchantVat = VatRate::where('merchant_id', $this->merchant_id)
                ->where('is_active', true)
                ->first();
            $vatRate = $merchantVat ? $merchantVat->rate_percentage : 0;
        }
        
        $tax = $subTotal * 0.15; // 15% default tax (legacy)
        $vatAmount = $subTotal * ($vatRate / 100); // VAT on sub total
        $total = $subTotal + $tax + $vatAmount;

        $this->update([
            'sub_total' => $subTotal,
            'tax' => $tax,
            'vat_rate' => $vatRate,
            'vat_amount' => $vatAmount,
            'total' => $total,
        ]);
    }
}
