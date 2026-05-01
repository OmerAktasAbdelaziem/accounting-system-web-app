<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Auth\Access\Response;

/**
 * WarehousePolicy - Authorization for warehouse management
 */
class WarehousePolicy extends BasePolicy
{
    /**
     * Create new warehouse (requires create-warehouse permission)
     */
    public function create(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'create-warehouse')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create warehouses');
    }

    /**
     * Update warehouse (requires edit-warehouse permission)
     */
    public function update(User $user, Warehouse $warehouse): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'edit-warehouse')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to edit warehouses');
    }

    /**
     * Delete warehouse (requires delete-warehouse permission)
     */
    public function delete(User $user, Warehouse $warehouse): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'delete-warehouse')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to delete warehouses');
    }
}