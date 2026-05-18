<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'package_id',
        'start_date',
        'expires_at',
        'is_active',
        'payment_method'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the merchant this subscription belongs to
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Get the package included in this subscription
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active && now()->isBefore($this->expires_at);
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    /**
     * Get days remaining in subscription
     */
    public function daysRemaining(): int
    {
        return now()->diffInDays($this->expires_at);
    }

    /**
     * Activate subscription
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Expire subscription
     */
    public function expire(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Cancel subscription
     */
    public function cancel(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Get active subscriptions
     */
    public static function active()
    {
        return static::where('is_active', true)
            ->where('expires_at', '>', now());
    }
}
