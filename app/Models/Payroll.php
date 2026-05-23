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
        'deductions',
        'net_salary',
        'status',
        'safe_id',
        'processed_by',
        'processed_at',
        'notes',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'commission' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function safe(): BelongsTo
    {
        return $this->belongsTo(Safe::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', '!=', 'paid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function markAsPaid(): bool
    {
        $this->status = 'paid';

        return $this->save();
    }

    /**
     * Calculate net salary based on basic salary, commission, and allowances
     */
    public function calculateNetSalary(): float
    {
        return (float) ($this->basic_salary + ($this->commission ?? 0) + ($this->allowances ?? 0) - ($this->deductions ?? 0));
    }
}
