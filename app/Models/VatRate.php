<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VatRate extends Model
{
    use HasFactory;

    protected $fillable = ['merchant_id', 'rate', 'is_enabled', 'description'];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_enabled' => 'boolean',
    ];

    /**
     * Get the merchant this VAT rate belongs to
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Calculate VAT amount for a given amount
     */
    public function calculateVat(float $amount): float
    {
        if (!$this->is_enabled) {
            return 0;
        }
        
        return ($amount * $this->rate) / 100;
    }

    /**
     * Calculate total with VAT
     */
    public function calculateTotal(float $amount): float
    {
        return $amount + $this->calculateVat($amount);
    }

    /**
     * Get percentage display
     */
    public function getPercentageAttribute(): string
    {
        return $this->rate . '%';
    }

    /**
     * Get active VAT rate for a merchant
     */
    public static function getActive(int $merchantId): ?self
    {
        return static::where('merchant_id', $merchantId)
            ->where('is_enabled', true)
            ->first();
    }
}
