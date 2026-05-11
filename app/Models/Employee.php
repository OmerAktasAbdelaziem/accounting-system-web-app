<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_code',
        'name',
        'name_ar',
        'email',
        'phone',
        'position',
        'position_ar',
        'address',
        'address_ar',
        'branch_id',
        'hire_date',
        'termination_date',
        'base_salary',
        'commission_rate',
        'commission_type',
        'department',
        'is_active',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'termination_date' => 'date',
        'base_salary' => 'decimal:2',
        'commission_rate' => 'decimal:2',
    ];

    /**
     * Get employee commissions
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(EmployeeCommission::class);
    }

    /**
     * Get employee deductions
     */
    public function deductions(): HasMany
    {
        return $this->hasMany(EmployeeDeduction::class);
    }

    /**
     * Get employee sales
     */
    public function sales(): HasMany
    {
        return $this->hasMany(EmployeeSale::class);
    }

    /**
     * Calculate total sales for a period
     */
    public function calculateSalesForPeriod($month, $year): float
    {
        return $this->sales()
            ->whereYear('sale_date', $year)
            ->whereMonth('sale_date', $month)
            ->sum('total_amount');
    }

    /**
     * Calculate commission for a specific month
     */
    public function calculateCommission($month, $year): float
    {
        $salesAmount = $this->calculateSalesForPeriod($month, $year);

        if ($this->commission_type === 'percentage') {
            return ($salesAmount * $this->commission_rate) / 100;
        } else {
            // Fixed amount per sale
            $salesCount = $this->sales()
                ->whereYear('sale_date', $year)
                ->whereMonth('sale_date', $month)
                ->count();
            return $this->commission_rate * $salesCount;
        }
    }

    /**
     * Get or create commission record for period
     */
    public function getOrCreateCommission($month, $year): EmployeeCommission
    {
        $commission = $this->commissions()
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if (!$commission) {
            $salesAmount = $this->calculateSalesForPeriod($month, $year);
            $salesCount = $this->sales()
                ->whereYear('sale_date', $year)
                ->whereMonth('sale_date', $month)
                ->count();
            $commissionEarned = $this->calculateCommission($month, $year);

            $commission = $this->commissions()->create([
                'month' => $month,
                'year' => $year,
                'sales_amount' => $salesAmount,
                'sales_count' => $salesCount,
                'commission_earned' => $commissionEarned,
                'status' => 'pending',
            ]);
        }

        return $commission;
    }

    /**
     * Calculate total deductions for a period
     */
    public function calculateDeductionsForPeriod($month, $year): float
    {
        return $this->deductions()
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');
    }

    /**
     * Calculate net salary for a period (commission - deductions)
     */
    public function calculateNetSalary($month, $year): float
    {
        $commission = $this->getOrCreateCommission($month, $year);
        $deductions = $this->calculateDeductionsForPeriod($month, $year);

        return ($this->base_salary + $commission->commission_earned + $commission->bonus) - $deductions;
    }

    /**
     * Get commission history for a date range
     */
    public function commissionHistory($startDate, $endDate): HasMany
    {
        return $this->commissions()
            ->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get active employees
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('termination_date');
    }

    /**
     * Scope to get employees by department
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Get full name (Arabic or English based on context)
     */
    public function getFullNameAttribute(): string
    {
        return $this->name_ar ?? $this->name;
    }

    /**
     * Check if employee is still employed
     */
    public function isEmployed(): bool
    {
        return $this->is_active && !$this->termination_date;
    }
}
