<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use SoftDeletes;
    use \App\Models\Concerns\HasBranches;

    protected $fillable = [
        'employee_id',
        'commission_rate',
        'sale_amount',
        'commission_amount',
        'commission_date',
        'reference_type',
        'reference_id',
        'notes',
    ];

    protected $casts = [
        'commission_date' => 'date',
        'commission_rate' => 'decimal:2',
        'sale_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
