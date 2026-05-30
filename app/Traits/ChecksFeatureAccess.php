<?php

namespace App\Traits;

use App\Models\FeatureAccess;
use App\Models\FeatureAccessOverride;
use Illuminate\Support\Facades\Auth;

trait ChecksFeatureAccess
{
    /**
     * Map a feature key to one or more permission names that should also unlock it.
     */
    private static function permissionFallbacksForFeature(string $featureKey): array
    {
        $map = [
            'products' => ['view_product'],
            'products.create' => ['create_product'],
            'products.edit' => ['edit_product'],
            'products.delete' => ['delete_product'],

            'categories' => ['view_category'],
            'categories.create' => ['create_category'],
            'categories.edit' => ['edit_category'],
            'categories.delete' => ['delete_category'],

            'employees' => ['view_user'],
            'employees.create' => ['create_user'],
            'employees.edit' => ['edit_user'],
            'employees.delete' => ['delete_user'],

            'sales' => ['view_sales', 'view_reports'],
            'sales.view' => ['view_sales', 'view_reports'],
            'sales.create' => ['view_sales', 'view_reports'],
            'sales.edit' => ['view_sales', 'view_reports'],
            'sales.delete' => ['view_sales', 'view_reports'],
            'sales_report' => ['view_sales', 'view_reports'],

            'suppliers' => ['view_supplier'],
            'suppliers.create' => ['create_supplier'],
            'suppliers.edit' => ['edit_supplier'],
            'suppliers.delete' => ['delete_supplier'],

            'invoices' => ['view_invoice'],
            'invoices.create' => ['create_invoice'],
            'invoices.edit' => ['edit_invoice'],
            'invoices.delete' => ['delete_invoice'],

            'payroll' => ['view_payroll'],
            'payroll.create' => ['create_payroll'],
            'payroll.edit' => ['edit_payroll'],
            'payroll.delete' => ['delete_payroll'],

            'branches' => ['view_branch'],
            'branches.create' => ['create_branch'],
            'branches.edit' => ['edit_branch'],
            'branches.delete' => ['delete_branch'],

            'commissions' => ['view_reports'],
            'commissions.create' => ['view_reports'],
            'commissions.edit' => ['view_reports'],
            'commissions.delete' => ['view_reports'],

            'storages' => ['view_inventory'],
            'storages.create' => ['edit_inventory'],
            'storages.edit' => ['edit_inventory'],
            'storages.delete' => ['edit_inventory'],

            'safes' => ['view_safe'],
            'safes.create' => ['create_safe', 'deposit_safe', 'withdraw_safe'],
            'safes.edit' => ['edit_safe'],
            'safes.delete' => ['delete_safe'],

            'inventory_report' => ['view_inventory', 'view_reports'],
            'financial_report' => ['view_reports'],

            'dashboard' => ['view_reports', 'view_sales', 'view_inventory'],
        ];

        return $map[$featureKey] ?? [];
    }

    /**
     * Check if the current user's role has access to a feature in their merchant
     */
    public static function hasFeatureAccess(string $featureKey): bool
    {
        $user = Auth::user();
        
        if (!$user || !$user->merchant_id) {
            // In local development it's convenient to show features even without a merchant
            if (app()->isLocal()) {
                return true;
            }
            return false;
        }

        // Super admins have access to everything
        if ($user->user_type === 'super_admin') {
            return true;
        }

        $override = FeatureAccessOverride::hasAccess((int) $user->merchant_id, (int) $user->id, $featureKey);
        if ($override !== null) {
            return $override;
        }

        $fallbackPermissions = self::permissionFallbacksForFeature($featureKey);

        // Get user's role ID
        $roleId = $user->role_id ?? null;
        if (!$roleId) {
            return !empty($fallbackPermissions) && self::hasAnyPermissionAccess($fallbackPermissions);
        }

        $featureEnabled = FeatureAccess::where('merchant_id', $user->merchant_id)
            ->where('role_id', $roleId)
            ->where('feature_key', $featureKey)
            ->where('is_enabled', true)
            ->exists();

        if ($featureEnabled) {
            return true;
        }

        return !empty($fallbackPermissions) && self::hasAnyPermissionAccess($fallbackPermissions);
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

        $overrideFeatures = FeatureAccessOverride::enabledFeaturesForUser((int) $user->merchant_id, (int) $user->id);
        $deniedFeatures = FeatureAccessOverride::deniedFeaturesForUser((int) $user->merchant_id, (int) $user->id);

        $roleId = $user->role_id ?? null;
        if (!$roleId) {
            return array_values(array_diff($overrideFeatures, $deniedFeatures));
        }

        $roleFeatures = FeatureAccess::where('merchant_id', $user->merchant_id)
            ->where('role_id', $roleId)
            ->where('is_enabled', true)
            ->pluck('feature_key')
            ->toArray();

        return array_values(array_diff(array_unique(array_merge($overrideFeatures, $roleFeatures)), $deniedFeatures));
    }

    /**
     * Check if a feature is visible to the current user (for navigation)
     */
    public static function isFeatureVisible(string $featureKey): bool
    {
        return self::hasFeatureAccess($featureKey);
    }

    private static function hasAnyPermissionAccess(array $permissionNames): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        foreach ($permissionNames as $permissionName) {
            if ($user->hasPermission($permissionName)) {
                return true;
            }
        }

        return false;
    }
}
