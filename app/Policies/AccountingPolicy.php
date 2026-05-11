<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Auth\Access\Response;

/**
 * AccountingPolicy - Authorization for accounting/ledger operations
 * 
 * Permissions:
 * - Admin: Full access
 * - Manager: Full access
 * - Accountant: Create, edit, post journal entries
 * - User: View only
 */
class AccountingPolicy extends BasePolicy
{
    /**
     * Create chart of account (requires accounting access)
     */
    public function createAccount(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'manage-accounts')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create accounts');
    }

    /**
     * Edit chart of account (requires accounting access)
     */
    public function editAccount(User $user, ChartOfAccount $account): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'manage-accounts')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to edit accounts');
    }

    /**
     * Delete chart of account (admin only)
     */
    public function deleteAccount(User $user, ChartOfAccount $account): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        return $this->deny('Only administrators can delete accounts');
    }

    /**
     * Create journal entry (requires create-journal permission)
     */
    public function createJournal(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'create-journal')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create journal entries');
    }

    /**
     * Edit journal entry (requires edit-journal permission)
     */
    public function editJournal(User $user, JournalEntry $entry): Response
    {
        // Can only edit if not posted
        if ($entry->status === 'posted') {
            return $this->deny('Cannot edit posted journal entries');
        }

        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'edit-journal')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to edit journal entries');
    }

    /**
     * Post journal entry (requires post-journal permission)
     */
    public function postJournal(User $user, JournalEntry $entry): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'post-journal')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to post journal entries');
    }

    /**
     * Reverse journal entry (requires post-journal permission)
     */
    public function reverseJournal(User $user, JournalEntry $entry): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'post-journal')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to reverse journal entries');
    }

    /**
     * Delete journal entry (admin only)
     */
    public function deleteJournal(User $user, JournalEntry $entry): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        return $this->deny('Only administrators can delete journal entries');
    }

    /**
     * View financial reports (requires view-reports permission)
     */
    public function viewReports(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'view-reports')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to view financial reports');
    }
}
