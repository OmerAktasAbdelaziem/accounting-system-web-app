<?php

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
