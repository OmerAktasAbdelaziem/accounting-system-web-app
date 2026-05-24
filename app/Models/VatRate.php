<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VatRate extends Model
{
    use HasFactory;

    protected $fillable = ['merchant_id', 'rate_percentage', 'is_active', 'applies_to', 'rate', 'is_enabled', 'description'];

    protected $casts = [
        'rate_percentage' => 'decimal:2',
        'is_active' => 'boolean',
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
        if (!$this->is_active && !$this->is_enabled) {
            return 0;
        }
        
        return ($amount * (float) $this->percentage_value) / 100;
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
        return $this->percentage_value . '%';
    }

    /**
     * Get the active percentage from whichever column is present.
     */
    public function getPercentageValueAttribute(): float
    {
        if ($this->getAttribute('rate_percentage') !== null) {
            return (float) $this->getAttribute('rate_percentage');
        }

        if ($this->getAttribute('rate') !== null) {
            return (float) $this->getAttribute('rate');
        }

        return 0.0;
    }

    /**
     * Keep compatibility with legacy code referencing `rate`.
     */
    public function getRateAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }

        return $this->getAttribute('rate_percentage');
    }

    /**
     * Keep compatibility with legacy code referencing `is_enabled`.
     */
    public function getIsEnabledAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }

        return $this->getAttribute('is_active');
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
