<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'role_id',
        'branch_id',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function accessibleBranchIdsFor(int $merchantId, int $roleId): array
    {
        return static::query()
            ->where('merchant_id', $merchantId)
            ->where('role_id', $roleId)
            ->where('is_enabled', true)
            ->pluck('branch_id')
            ->map(fn ($branchId) => (int) $branchId)
            ->all();
    }

    public static function hasRestrictionFor(int $merchantId, int $roleId): bool
    {
        return static::query()
            ->where('merchant_id', $merchantId)
            ->where('role_id', $roleId)
            ->where('is_enabled', true)
            ->exists();
    }

    public static function syncForRole(Role $role, array $branchIds): void
    {
        static::query()->where('role_id', $role->id)->delete();

        $branchIds = array_values(array_unique(array_filter($branchIds)));
        if (empty($branchIds)) {
            return;
        }

        $branches = Branch::query()
            ->whereIn('id', $branchIds)
            ->get(['id', 'merchant_id']);

        foreach ($branches as $branch) {
            static::query()->create([
                'merchant_id' => $branch->merchant_id,
                'role_id' => $role->id,
                'branch_id' => $branch->id,
                'is_enabled' => true,
            ]);
        }
    }
}