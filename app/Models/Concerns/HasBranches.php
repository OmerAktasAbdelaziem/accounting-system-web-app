<?php

namespace App\Models\Concerns;

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

            $model = new static();
            $table = $model->getTable();
            $qualifiedBranchId = $table . '.branch_id';
            $hasBranchIdColumn = Schema::hasColumn($table, 'branch_id');

            $builder->where(function (Builder $query) use ($user, $qualifiedBranchId, $hasBranchIdColumn) {
                $query->whereHas('branches', function (Builder $branchQuery) use ($user) {
                    $branchQuery->where('branches.merchant_id', $user->merchant_id);
                });

                if ($hasBranchIdColumn) {
                    $query->orWhereIn($qualifiedBranchId, Branch::query()
                        ->select('id')
                        ->where('merchant_id', $user->merchant_id));
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
