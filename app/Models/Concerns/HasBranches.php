<?php

namespace App\Models\Concerns;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasBranches
{
    public function branches(): MorphToMany
    {
        return $this->morphToMany(Branch::class, 'branchable');
    }

    public function syncBranches(array $branchIds): void
    {
        $this->branches()->sync($branchIds);
    }
}
