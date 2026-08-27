<?php

namespace App\Models;

use App\Models\Concerns\HasBranches;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;
    use \App\Models\Concerns\HasBranches;

    protected $fillable = [
        'employee_code',
        'name',
        'name_ar',
        'email',
        'phone',
        'merchant_id',
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
     * Get sales commission transactions
     */
    public function commissionTransactions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    /**
     * Get employee advances
     */
    public function advances(): HasMany
    {
        return $this->hasMany(EmployeeAdvance::class);
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
     * Get sale participation rows for all employee sales they helped close.
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(EmployeeSaleDetail::class);
    }

    /**
     * Calculate total sales for a period
     */
    public function calculateSalesForPeriod($month, $year): float
    {
        $detailTotal = (float) $this->saleDetails()
            ->whereHas('sale', function ($query) use ($month, $year) {
                $query->whereYear('sale_date', $year)
                    ->whereMonth('sale_date', $month);
            })
            ->sum('amount');

        $legacyTotal = (float) $this->sales()
            ->whereYear('sale_date', $year)
            ->whereMonth('sale_date', $month)
            ->whereDoesntHave('employeeSaleDetails')
            ->sum('total_amount');

        return $detailTotal + $legacyTotal;
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
            $detailCount = $this->saleDetails()
                ->whereHas('sale', function ($query) use ($month, $year) {
                    $query->whereYear('sale_date', $year)
                        ->whereMonth('sale_date', $month);
                })
                ->count();

            $legacyCount = $this->sales()
                ->whereYear('sale_date', $year)
                ->whereMonth('sale_date', $month)
                ->whereDoesntHave('employeeSaleDetails')
                ->count();

            $salesCount = $detailCount + $legacyCount;
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
            $salesCount = $this->saleIdsForPeriod($month, $year)->count();
            $commissionEarned = $this->calculateCommission($month, $year);

            $commission = $this->commissions()->create([
                'month' => $month,
                'year' => $year,
                'sales_amount' => $salesAmount,
                'sales_count' => $salesCount,
                'commission_earned' => $commissionEarned,
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

    /**
     * Get the unique sale IDs associated with the employee for a period.
     */
    protected function saleIdsForPeriod($month, $year)
    {
        $primarySaleIds = $this->sales()
            ->whereYear('sale_date', $year)
            ->whereMonth('sale_date', $month)
            ->pluck('id');

        $participatingSaleIds = $this->saleDetails()
            ->whereHas('sale', function ($query) use ($month, $year) {
                $query->whereYear('sale_date', $year)
                    ->whereMonth('sale_date', $month);
            })
            ->pluck('employee_sale_id');

        return $primarySaleIds->merge($participatingSaleIds)->unique()->values();
    }
}
