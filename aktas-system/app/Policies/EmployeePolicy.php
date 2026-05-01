<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Auth\Access\Response;

/**
 * EmployeePolicy - Authorization for employee management
 * 
 * Permissions:
 * - Admin: Full access
 * - Manager: Create, edit, manage commissions/deductions
 * - User: View only
 */
class EmployeePolicy extends BasePolicy
{
    /**
     * Create new employee (requires create-employee permission)
     */
    public function create(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'create-employee')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to create employees');
    }

    /**
     * Update employee (requires edit-employee permission)
     */
    public function update(User $user, Employee $employee): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'edit-employee')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to edit employees');
    }

    /**
     * Delete/terminate employee (requires delete-employee permission)
     */
    public function delete(User $user, Employee $employee): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'delete-employee')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to delete employees');
    }

    /**
     * Manage commissions (requires manage-commission permission)
     */
    public function manageCommission(User $user, Employee $employee): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'manage-commission')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to manage commissions');
    }

    /**
     * Approve commission (requires approve-commission permission)
     */
    public function approveCommission(User $user, Employee $employee): Response
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
     * Manage deductions (requires manage-deduction permission)
     */
    public function manageDeduction(User $user, Employee $employee): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'manage-deduction')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to manage deductions');
    }

    /**
     * Access salary information (requires view-salary permission)
     */
    public function viewSalary(User $user, Employee $employee): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'view-salary')) {
            return $this->allow();
        }

        // User can view their own salary
        if ($user->id === $employee->user_id) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to view this salary information');
    }

    /**
     * Restore soft-deleted employee
     */
    public function restore(User $user, Employee $employee): Response
    {
        return $this->delete($user, $employee);
    }

    /**
     * Force delete employee (admin only)
     */
    public function forceDelete(User $user, Employee $employee): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        return $this->deny('Only administrators can permanently delete employees');
    }
}
