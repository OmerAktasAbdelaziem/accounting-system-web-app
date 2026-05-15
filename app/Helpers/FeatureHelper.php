<?php

use App\Models\Currency;
use App\Models\Setting;
use App\Traits\ChecksFeatureAccess;

/**
 * Check if current user has access to a feature
 */
if (!function_exists('hasFeature')) {
    function hasFeature(string $featureKey): bool
    {
        return ChecksFeatureAccess::hasFeatureAccess($featureKey);
    }
}

/**
 * Check if current user has access to any of the specified features
 */
if (!function_exists('hasAnyFeature')) {
    function hasAnyFeature(array $featureKeys): bool
    {
        return ChecksFeatureAccess::hasAnyFeatureAccess($featureKeys);
    }
}

/**
 * Check if current user has access to all specified features
 */
if (!function_exists('hasAllFeatures')) {
    function hasAllFeatures(array $featureKeys): bool
    {
        return ChecksFeatureAccess::hasAllFeatureAccess($featureKeys);
    }
}

/**
 * Get all enabled features for current user
 */
if (!function_exists('getEnabledFeatures')) {
    function getEnabledFeatures(): array
    {
        return ChecksFeatureAccess::getEnabledFeatures();
    }
}

/**
 * Check if a feature is visible to the current user
 */
if (!function_exists('isFeatureVisible')) {
    function isFeatureVisible(string $featureKey): bool
    {
        return ChecksFeatureAccess::isFeatureVisible($featureKey);
    }
}

/**
 * Get the currently selected currency record.
 */
if (!function_exists('currentCurrency')) {
    function currentCurrency(): ?Currency
    {
        static $currency = null;

        if ($currency === null) {
            $currencyCode = (string) Setting::get('currency', 'AED');
            $currency = Currency::byCode($currencyCode);
        }

        return $currency;
    }
}

/**
 * Get the current currency symbol.
 */
if (!function_exists('currencySymbol')) {
    function currencySymbol(string $default = '$'): string
    {
        return currentCurrency()?->symbol ?? $default;
    }
}

/**
 * Format a numeric amount using the current currency settings.
 */
if (!function_exists('formatCurrency')) {
    function formatCurrency(float|int $amount, ?int $decimalPlaces = null): string
    {
        $currency = currentCurrency();
        $symbol = $currency?->symbol ?? '$';
        $places = $decimalPlaces ?? $currency?->decimal_places ?? 2;

        return $symbol . number_format((float) $amount, $places, '.', ',');
    }
}
