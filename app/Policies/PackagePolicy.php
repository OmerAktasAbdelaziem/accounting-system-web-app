<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\User;

class PackagePolicy
{
    /**
     * Determine if user can view any packages (super admin only)
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if user can view package
     */
    public function view(User $user, Package $package): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if user can create package (super admin only)
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if user can update package (super admin only)
     */
    public function update(User $user, Package $package): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if user can delete package (super admin only)
     */
    public function delete(User $user, Package $package): bool
    {
        return $user->isSuperAdmin();
    }
}
