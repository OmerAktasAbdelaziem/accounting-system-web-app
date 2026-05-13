<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureAccess extends Model
{
    use HasFactory;

    protected $table = 'feature_access';
    protected $fillable = ['merchant_id', 'role_id', 'role_name', 'feature_name', 'feature_key', 'is_enabled'];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * Get the merchant
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Get the role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if a role has access to a feature in a merchant
     */
    public static function hasAccess(int $merchantId, int $roleId, string $featureKey): bool
    {
        return static::where('merchant_id', $merchantId)
            ->where('role_id', $roleId)
            ->where('feature_key', $featureKey)
            ->where('is_enabled', true)
            ->exists();
    }

    /**
     * Get all enabled features for a role in a merchant
     */
    public static function getFeaturesForRole(int $merchantId, int $roleId): array
    {
        return static::where('merchant_id', $merchantId)
            ->where('role_id', $roleId)
            ->where('is_enabled', true)
            ->pluck('feature_key')
            ->toArray();
    }

    /**
     * Disable all features for a role in a merchant
     */
    public static function disableAllForRole(int $merchantId, int $roleId): void
    {
        static::where('merchant_id', $merchantId)
            ->where('role_id', $roleId)
            ->update(['is_enabled' => false]);
    }

    /**
     * Enable all features for a role in a merchant
     */
    public static function enableAllForRole(int $merchantId, int $roleId): void
    {
        static::where('merchant_id', $merchantId)
            ->where('role_id', $roleId)
            ->update(['is_enabled' => true]);
    }
}
