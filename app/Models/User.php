<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'merchant_id',
        'user_type',
        'subscription_id',
        'role_id',
        'branch_access_mode',
        'branch_access_branch_ids',
        'is_active',
        'phone',
        'address',
        'notes',
        'profile_photo_path',
        'last_seen_at',
        'last_login',
        'api_token',
        'api_token_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'branch_access_branch_ids' => 'array',
            'last_seen_at' => 'datetime',
            'last_login' => 'datetime',
            'api_token_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the role associated with the user
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the merchant this user belongs to
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Get the subscription for this user
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->user_type === 'super_admin';
    }

    /**
     * Check if user is merchant admin
     */
    public function isMerchantAdmin(): bool
    {
        return $this->user_type === 'merchant_admin';
    }

    /**
     * Check if user is employee
     */
    public function isEmployee(): bool
    {
        return $this->user_type === 'employee';
    }

    /**
     * Get all audit logs for this user's actions
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get all inventory movements created by this user
     */
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'created_by');
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permissionName): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->hasPermission($permissionName);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->name === $roleName;
    }

    /**
     * Check whether the user can see a sidebar item controlled by feature access and/or permissions.
     */
    public function canViewMenuItem(?string $featureKey = null, ?string $permissionName = null): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $hasPermission = $permissionName ? $this->hasPermission($permissionName) : false;

        if ($featureKey) {
            $hasFeature = \App\Traits\ChecksFeatureAccess::hasFeatureAccess($featureKey);

            // Allow either a feature toggle or a role permission to grant visibility.
            if ($hasFeature || $hasPermission) {
                return true;
            }

            return false;
        }

        if ($permissionName && ! $hasPermission) {
            return false;
        }

        return true;
    }

    /**
     * Get branch IDs this user can access.
     * Return null when the user is unrestricted.
     */
    public function accessibleBranchIds(): ?array
    {
        if ($this->isSuperAdmin() || $this->isMerchantAdmin() || !$this->merchant_id) {
            return null;
        }

        if ($this->branch_access_mode === 'all') {
            return null;
        }

        if ($this->branch_access_mode === 'custom') {
            return array_values(array_unique(array_map(
                'intval',
                is_array($this->branch_access_branch_ids) ? $this->branch_access_branch_ids : []
            )));
        }

        if (!$this->role_id) {
            return null;
        }

        $branchIds = BranchAccess::accessibleBranchIdsFor((int) $this->merchant_id, (int) $this->role_id);

        return empty($branchIds) ? null : $branchIds;
    }

    /**
     * Check whether the current user can access a specific branch.
     */
    public function canAccessBranch(int $branchId): bool
    {
        $branchIds = $this->accessibleBranchIds();

        if ($branchIds === null) {
            return true;
        }

        return in_array($branchId, $branchIds, true);
    }

    public function branchAccessSummary(): array
    {
        $branchIds = $this->accessibleBranchIds();

        if ($branchIds === null) {
            return ['label' => 'All branches', 'tone' => 'success'];
        }

        $count = count($branchIds);

        return ['label' => $count . ' branch' . ($count === 1 ? '' : 'es'), 'tone' => $count > 0 ? 'warning' : 'danger'];
    }

    /**
     * Update last login timestamp
     */
    public function recordLogin(): void
    {
        $this->update(['last_login' => now()]);
    }
}
