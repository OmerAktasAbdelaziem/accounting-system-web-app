<?php

namespace App\Policies;

use App\Models\FeatureAccess;
use App\Models\User;

class FeatureAccessPolicy
{
    /**
     * Determine if user can view any feature access (super admin only)
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if user can view feature access
     */
    public function view(User $user, FeatureAccess $featureAccess): bool
    {
        return $user->isSuperAdmin() || $user->merchant_id === $featureAccess->merchant_id;
    }

    /**
     * Determine if user can update feature access (super admin only)
     */
    public function update(User $user, FeatureAccess $featureAccess = null): bool
    {
        return $user->isSuperAdmin();
    }
}
