<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'basic_salary',
        'commission',
        'allowances',
        'advances_deducted',
        'net_salary',
        'notes',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'commission' => 'decimal:2',
        'allowances' => 'decimal:2',
        'advances_deducted' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Calculate net salary based on basic salary, commission, and allowances
     */
    public function calculateNetSalary(): float
    {
        return (float) ($this->basic_salary + ($this->commission ?? 0) + ($this->allowances ?? 0) - ($this->advances_deducted ?? 0));
    }
}
