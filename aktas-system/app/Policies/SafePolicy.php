<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Safe;
use Illuminate\Auth\Access\Response;

/**
 * SafePolicy - Authorization for safe management
 */
class SafePolicy extends BasePolicy
{
    /**
     * Create new safe (requires create-safe permission)
     */
    public function create(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'create-safe')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create safes');
    }

    /**
     * Update safe (requires edit-safe permission)
     */
    public function update(User $user, Safe $safe): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'edit-safe')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to edit safes');
    }

    /**
     * Delete safe (requires delete-safe permission)
     */
    public function delete(User $user, Safe $safe): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'delete-safe')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to delete safes');
    }

    /**
     * Record deposit to safe (requires deposit-safe permission)
     */
    public function deposit(User $user, Safe $safe): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'deposit-safe')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to deposit to safes');
    }

    /**
     * Record withdrawal from safe (requires withdraw-safe permission)
     */
    public function withdraw(User $user, Safe $safe): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'withdraw-safe')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to withdraw from safes');
    }
}
