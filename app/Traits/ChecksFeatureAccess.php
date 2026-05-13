<?php

namespace App\Traits;

use App\Models\FeatureAccess;
use Illuminate\Support\Facades\Auth;

trait ChecksFeatureAccess
{
    /**
     * Check if the current user's role has access to a feature in their merchant
     */
    public static function hasFeatureAccess(string $featureKey): bool
    {
        $user = Auth::user();
        
        if (!$user || !$user->merchant_id) {
            return false;
        }

        // Super admins have access to everything
        if ($user->user_type === 'super_admin') {
            return true;
        }

        // Get user's role ID
        $roleId = $user->role_id ?? null;
        if (!$roleId) {
            return false;
        }

        return FeatureAccess::where('merchant_id', $user->merchant_id)
            ->where('role_id', $roleId)
            ->where('feature_key', $featureKey)
            ->where('is_enabled', true)
            ->exists();
    }

    /**
     * Check multiple features - returns true if user has access to at least one
     */
    public static function hasAnyFeatureAccess(array $featureKeys): bool
    {
        foreach ($featureKeys as $featureKey) {
            if (self::hasFeatureAccess($featureKey)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has access to all specified features
     */
    public static function hasAllFeatureAccess(array $featureKeys): bool
    {
        foreach ($featureKeys as $featureKey) {
            if (!self::hasFeatureAccess($featureKey)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all enabled features for the current user
     */
    public static function getEnabledFeatures(): array
    {
        $user = Auth::user();
        
        if (!$user || !$user->merchant_id) {
            return [];
        }

        // Super admins have all features
        if ($user->user_type === 'super_admin') {
            return array_keys(\App\Http\Controllers\SuperAdmin\FeatureAccessController::getAvailableFeatures());
        }

        $roleId = $user->role_id ?? null;
        if (!$roleId) {
            return [];
        }

        return FeatureAccess::where('merchant_id', $user->merchant_id)
            ->where('role_id', $roleId)
            ->where('is_enabled', true)
            ->pluck('feature_key')
            ->toArray();
    }

    /**
     * Check if a feature is visible to the current user (for navigation)
     */
    public static function isFeatureVisible(string $featureKey): bool
    {
        return self::hasFeatureAccess($featureKey);
    }
}
