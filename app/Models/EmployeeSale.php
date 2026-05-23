<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_amount',
        'spent_amount',
        'sale_date',
        'sale_reference',
        'notes',
        'notes_ar',
        'employee_assignments',
        'branch_id',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'employee_assignments' => 'array',
    ];

    /**
     * Get the employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Net income after store spend.
     */
    public function getNetIncomeAttribute(): float
    {
        return (float) ($this->total_amount - ($this->spent_amount ?? 0));
    }

    /**
     * Scope to get sales for a date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get sales for a specific employee
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope to get sales for a specific product
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
