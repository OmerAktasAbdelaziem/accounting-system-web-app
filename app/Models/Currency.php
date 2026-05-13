<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'symbol', 'decimal_places'];

    /**
     * Get the merchants that use this currency
     */
    public function merchants(): BelongsToMany
    {
        return $this->belongsToMany(Merchant::class, 'merchant_currencies')
            ->withTimestamps();
    }

    /**
     * Format amount with currency symbol
     */
    public function format(float $amount): string
    {
        return $this->symbol . number_format($amount, $this->decimal_places, '.', ',');
    }

    /**
     * Get a currency by code
     */
    public static function byCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }
}
