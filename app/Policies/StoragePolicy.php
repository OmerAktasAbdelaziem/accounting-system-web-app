<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Storage;
use Illuminate\Auth\Access\Response;

/**
 * StoragePolicy - Authorization for storage/warehouse management
 */
class StoragePolicy extends BasePolicy
{
    /**
     * Create new storage (requires create-storage permission)
     */
    public function create(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'create-storage')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create storages');
    }

    /**
     * Update storage (requires edit-storage permission)
     */
    public function update(User $user, Storage $storage): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'edit-storage')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to edit storages');
    }

    /**
     * Delete storage (requires delete-storage permission)
     */
    public function delete(User $user, Storage $storage): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'delete-storage')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to delete storages');
    }
}
