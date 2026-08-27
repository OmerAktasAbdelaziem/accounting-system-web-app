<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SafeCurrency extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'safe_id',
        'code',
        'name',
        'balance',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function safe()
    {
        return $this->belongsTo(Safe::class);
    }

    public function incomes()
    {
        return $this->hasMany(SafeIncome::class, 'currency_id');
    }

    public function outcomes()
    {
        return $this->hasMany(SafeOutcome::class, 'currency_id');
    }
}
