<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StorageTransfer;
use Illuminate\Auth\Access\Response;

/**
 * StorageTransferPolicy - Authorization for storage transfer management
 */
class StorageTransferPolicy extends BasePolicy
{
    /**
     * Create new storage transfer (requires create-storage-transfer permission)
     */
    public function create(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'create-storage-transfer')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create storage transfers');
    }

    /**
     * Approve storage transfer (requires approve-storage-transfer permission)
     */
    public function approve(User $user, StorageTransfer $transfer): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'approve-storage-transfer')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to approve storage transfers');
    }

    /**
     * Cancel storage transfer (requires delete-storage-transfer permission)
     */
    public function cancel(User $user, StorageTransfer $transfer): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'delete-storage-transfer')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to cancel storage transfers');
    }
}
