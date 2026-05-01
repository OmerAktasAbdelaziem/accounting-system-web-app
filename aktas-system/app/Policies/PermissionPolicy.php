<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Auth\Access\Response;

/**
 * PermissionPolicy - Authorization for permission management
 */
class PermissionPolicy extends BasePolicy
{
    /**
     * Create new permission (admin only)
     */
    public function create(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create permissions');
    }

    /**
     * Update permission (admin only)
     */
    public function update(User $user, Permission $permission): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to edit permissions');
    }

    /**
     * Delete permission (admin only, cannot delete permissions assigned to roles)
     */
    public function delete(User $user, Permission $permission): Response
    {
        if ($this->isAdmin($user)) {
            // Prevent deleting permissions assigned to roles
            if ($permission->roles()->count() > 0) {
                return $this->deny('Cannot delete permissions assigned to roles');
            }

            return $this->allow();
        }

        return $this->deny('You do not have permission to delete permissions');
    }
}
