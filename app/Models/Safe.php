<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Safe extends Model
{
    use SoftDeletes;
    use \App\Models\Concerns\HasBranches;

    protected $fillable = [
        'name',
        'branch_id',
        'location',
        'balance',
        'max_balance',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'max_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(SafeTransaction::class);
    }

    public function incomes()
    {
        return $this->hasMany(SafeIncome::class);
    }

    public function outcomes()
    {
        return $this->hasMany(SafeOutcome::class);
    }

    public function currencies()
    {
        return $this->hasMany(SafeCurrency::class);
    }
}
