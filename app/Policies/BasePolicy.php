<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Base Authorization Policy
 * 
 * Provides common authorization logic for all resource policies.
 * Uses role-based access control (RBAC) with permission checking.
 */
abstract class BasePolicy
{
    /**
     * Check if user is admin (unrestricted access)
     */
    protected function isAdmin(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Check if user has specific permission
     */
    protected function can(User $user, string $permission): bool
    {
        return $user->hasPermission($permission);
    }

    /**
     * Deny action with message
     */
    protected function deny(string $message = 'Unauthorized'): Response
    {
        return Response::deny($message);
    }

    /**
     * Allow action
     */
    protected function allow(): Response
    {
        return Response::allow();
    }

    /**
     * View Listing
     * Everyone can view (but API filters by role)
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * View Single Item
     * Everyone can view (but API filters by role)
     */
    public function view(User $user, mixed $model): bool
    {
        return true;
    }
}
