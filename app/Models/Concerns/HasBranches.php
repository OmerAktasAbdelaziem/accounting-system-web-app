<?php

namespace App\Models\Concerns;

use App\Models\BranchAccess;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait HasBranches
{
    public static function bootHasBranches(): void
    {
        static::addGlobalScope('merchant_branch_scope', function (Builder $builder) {
            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();
            if (!$user || $user->isSuperAdmin() || empty($user->merchant_id)) {
                return;
            }

            $allowedBranchIds = $user->accessibleBranchIds();
            if ($allowedBranchIds === null) {
                return;
            }

            if (empty($allowedBranchIds)) {
                $builder->whereRaw('1 = 0');
                return;
            }

            $model = new static();
            $table = $model->getTable();
            $qualifiedBranchId = $table . '.branch_id';
            $hasBranchIdColumn = Schema::hasColumn($table, 'branch_id');

            $builder->where(function (Builder $query) use ($allowedBranchIds, $qualifiedBranchId, $hasBranchIdColumn) {
                $query->whereHas('branches', function (Builder $branchQuery) use ($allowedBranchIds) {
                    $branchQuery->whereIn('branches.id', $allowedBranchIds);
                });

                if ($hasBranchIdColumn) {
                    $query->orWhereIn($qualifiedBranchId, $allowedBranchIds);
                }
            });
        });
    }

    public function branches(): MorphToMany
    {
        return $this->morphToMany(Branch::class, 'branchable');
    }

    public function syncBranches(array $branchIds): void
    {
        $this->branches()->sync($branchIds);
    }
}
