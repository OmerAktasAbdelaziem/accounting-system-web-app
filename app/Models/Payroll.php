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
        'net_salary',
        'status',
        'notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'commission' => 'decimal:2',
        'allowances' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Calculate net salary based on basic salary, commission, and allowances
     */
    public function calculateNetSalary(): float
    {
        return (float) ($this->basic_salary + ($this->commission ?? 0) + ($this->allowances ?? 0));
    }
}
