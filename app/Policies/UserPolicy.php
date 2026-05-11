<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * UserPolicy - Authorization for user management
 */
class UserPolicy extends BasePolicy
{
    /**
     * Create new user (admin only)
     */
    public function create(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create users');
    }

    /**
     * Update user (admin only, or user editing their own profile)
     */
    public function update(User $user, User $targetUser): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        // Users can edit their own profile
        if ($user->id === $targetUser->id) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to edit this user');
    }

    /**
     * Delete user (admin only, cannot delete own account)
     */
    public function delete(User $user, User $targetUser): Response
    {
        if ($this->isAdmin($user)) {
            // Prevent deleting own account
            if ($user->id === $targetUser->id) {
                return $this->deny('You cannot delete your own account');
            }

            return $this->allow();
        }

        return $this->deny('You do not have permission to delete users');
    }

    /**
     * Toggle user status (admin only, cannot toggle own status)
     */
    public function toggleStatus(User $user, User $targetUser): Response
    {
        if ($this->isAdmin($user)) {
            // Prevent toggling own status
            if ($user->id === $targetUser->id) {
                return $this->deny('You cannot toggle your own status');
            }

            return $this->allow();
        }

        return $this->deny('You do not have permission to toggle user status');
    }

    /**
     * Reset user password (admin only)
     */
    public function resetPassword(User $user, User $targetUser): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to reset user password');
    }

    /**
     * Change own password (everyone can)
     */
    public function changePassword(User $user, User $targetUser): Response
    {
        if ($user->id === $targetUser->id) {
            return $this->allow();
        }

        return $this->deny('You can only change your own password');
    }
}
