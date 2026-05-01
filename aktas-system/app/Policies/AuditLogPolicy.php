<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Auth\Access\Response;

/**
 * AuditLogPolicy - Authorization for audit log access
 * 
 * Audit logs are system records and should only be viewable by authorized users.
 */
class AuditLogPolicy extends BasePolicy
{
    /**
     * View audit logs (admin only)
     */
    public function viewAny(User $user): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        // Allow if user has view-audit-logs permission
        return $user->hasPermission('view-audit-logs');
    }

    /**
     * View single audit log entry (admin only)
     */
    public function view(User $user, AuditLog $auditLog): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        // Allow if user has view-audit-logs permission
        return $user->hasPermission('view-audit-logs');
    }

    /**
     * Delete audit log (admin only - for maintenance)
     */
    public function delete(User $user, AuditLog $auditLog): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to delete audit logs');
    }

    /**
     * Export audit logs (admin only)
     */
    public function export(User $user): Response
    {
        if ($this->isAdmin($user)) {
            return $this->allow();
        }

        if ($this->can($user, 'export-audit-logs')) {
            return $this->allow();
        }

        return $this->deny('You do not have permission to export audit logs');
    }
}
