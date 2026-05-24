<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureAccessOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'user_id',
        'feature_key',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function hasAccess(int $merchantId, int $userId, string $featureKey): ?bool
    {
        $override = static::where('merchant_id', $merchantId)
            ->where('user_id', $userId)
            ->where('feature_key', $featureKey)
            ->first();

        return $override?->is_enabled;
    }

    public static function enabledFeaturesForUser(int $merchantId, int $userId): array
    {
        return static::where('merchant_id', $merchantId)
            ->where('user_id', $userId)
            ->where('is_enabled', true)
            ->pluck('feature_key')
            ->toArray();
    }

    /**
     * Get features explicitly denied for a user.
     */
    public static function deniedFeaturesForUser(int $merchantId, int $userId): array
    {
        return static::where('merchant_id', $merchantId)
            ->where('user_id', $userId)
            ->where('is_enabled', false)
            ->pluck('feature_key')
            ->toArray();
    }
}
