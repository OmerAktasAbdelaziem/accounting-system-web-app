<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StorageItem;
use Illuminate\Auth\Access\Response;

/**
 * StorageItemPolicy - Authorization for storage item management
 */
class StorageItemPolicy extends BasePolicy
{
    /**
     * Create new storage item (requires manage-storage-items permission)
     */
    public function create(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'manage-storage-items')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create storage items');
    }

    /**
     * Update storage item (requires manage-storage-items permission)
     */
    public function update(User $user, StorageItem $item): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'manage-storage-items')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to edit storage items');
    }

    /**
     * Delete storage item (requires manage-storage-items permission)
     */
    public function delete(User $user, StorageItem $item): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'manage-storage-items')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to delete storage items');
    }
}
