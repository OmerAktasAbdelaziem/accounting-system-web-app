<?php

namespace App\Policies;

use App\Models\Merchant;
use App\Models\User;

class MerchantPolicy
{
    /**
     * Determine if user can view any merchants (super admin only)
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if user can view merchant
     */
    public function view(User $user, Merchant $merchant): bool
    {
        return $user->isSuperAdmin() || ($user->merchant_id === $merchant->id && $user->isMerchantAdmin());
    }

    /**
     * Determine if user can create merchant (super admin only)
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if user can update merchant
     */
    public function update(User $user, Merchant $merchant): bool
    {
        return $user->isSuperAdmin() || ($user->merchant_id === $merchant->id && $user->isMerchantAdmin());
    }

    /**
     * Determine if user can delete merchant (super admin only)
     */
    public function delete(User $user, Merchant $merchant): bool
    {
        return $user->isSuperAdmin();
    }
}
