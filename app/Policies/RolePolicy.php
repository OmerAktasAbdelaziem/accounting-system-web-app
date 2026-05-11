<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\Response;

/**
 * RolePolicy - Authorization for role management
 */
class RolePolicy extends BasePolicy
{
    /**
     * Create new role (admin only)
     */
    public function create(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create roles');
    }

    /**
     * Update role (admin only, cannot edit system roles)
     */
    public function update(User $user, Role $role): Response
    {
        if ($this->isAdmin($user)) {
            // Prevent editing system roles
            if (in_array($role->name, ['Admin', 'System'])) {
                return $this->deny('System roles cannot be edited');
            }
            return $this->allow();
        }

        return $this->deny('You do not have permission to edit roles');
    }

    /**
     * Delete role (admin only, cannot delete system roles or roles with users)
     */
    public function delete(User $user, Role $role): Response
    {
        if ($this->isAdmin($user)) {
            // Prevent deleting system roles
            if (in_array($role->name, ['Admin', 'System'])) {
                return $this->deny('System roles cannot be deleted');
            }

            // Prevent deleting roles with assigned users
            if ($role->users()->count() > 0) {
                return $this->deny('Cannot delete roles with assigned users');
            }

            return $this->allow();
        }

        return $this->deny('You do not have permission to delete roles');
    }
}
