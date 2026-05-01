<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Commission;
use Illuminate\Auth\Access\Response;

/**
 * CommissionPolicy - Authorization for commission management
 * 
 * Permissions:
 * - Admin: Full access
 * - Manager: Create, edit, view
 * - User: View only
 */
class CommissionPolicy extends BasePolicy
{
    /**
     * Create new commission (requires create-commission permission)
     */
    public function create(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'create-commission')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create commissions');
    }

    /**
     * Update commission (requires edit-commission permission)
     */
    public function update(User $user, Commission $commission): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'edit-commission')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to edit commissions');
    }

    /**
     * Delete commission (requires delete-commission permission)
     */
    public function delete(User $user, Commission $commission): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'delete-commission')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to delete commissions');
    }

    /**
     * Approve commission (requires approve-commission permission)
     */
    public function approve(User $user, Commission $commission): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'approve-commission')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to approve commissions');
    }

    /**
     * Pay commission (requires pay-commission permission)
     */
    public function pay(User $user, Commission $commission): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'pay-commission')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to pay commissions');
    }
}
