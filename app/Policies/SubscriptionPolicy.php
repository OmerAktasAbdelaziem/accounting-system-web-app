<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;

class SubscriptionPolicy
{
    /**
     * Determine if user can view any subscriptions (super admin only)
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if user can view subscription
     */
    public function view(User $user, Subscription $subscription): bool
    {
        return $user->isSuperAdmin() || $user->merchant_id === $subscription->merchant_id;
    }

    /**
     * Determine if user can create subscription (super admin only)
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if user can update subscription (super admin only)
     */
    public function update(User $user, Subscription $subscription): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if user can delete subscription (super admin only)
     */
    public function delete(User $user, Subscription $subscription): bool
    {
        return $user->isSuperAdmin();
    }
}
