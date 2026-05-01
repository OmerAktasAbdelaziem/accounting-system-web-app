<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SafeTransaction;
use Illuminate\Auth\Access\Response;

/**
 * SafeTransactionPolicy - Authorization for safe transaction management
 */
class SafeTransactionPolicy extends BasePolicy
{
    /**
     * View safe transaction (everyone can view)
     */
    public function view(User $user, SafeTransaction $transaction): bool
    {
        return true;
    }

    /**
     * Delete safe transaction (admin and financial staff only)
     */
    public function delete(User $user, SafeTransaction $transaction): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'delete-safe-transaction')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to delete safe transactions');
    }

    /**
     * Reverse safe transaction (admin and financial staff only)
     */
    public function reverse(User $user, SafeTransaction $transaction): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'reverse-safe-transaction')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to reverse safe transactions');
    }
}
