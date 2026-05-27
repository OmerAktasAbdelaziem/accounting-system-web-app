<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_ar', 'description'];

    /**
     * Get all users with this role
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all permissions for this role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /**
     * Get all branch access rules for this role
     */
    public function branchAccesses(): HasMany
    {
        return $this->hasMany(BranchAccess::class);
    }

    /**
     * Get all branches allowed for this role
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'role_branch_accesses')
            ->withPivot(['merchant_id', 'is_enabled'])
            ->wherePivot('is_enabled', true);
    }

    /**
     * Check if role has permission
     */
    public function hasPermission($permissionName): bool
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }
}
