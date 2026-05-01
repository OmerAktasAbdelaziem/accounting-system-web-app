<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeCommission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'sales_amount',
        'sales_count',
        'commission_earned',
        'bonus',
        'status',
        'notes',
        'notes_ar',
        'approved_at',
        'paid_at',
    ];

    protected $casts = [
        'sales_amount' => 'decimal:2',
        'commission_earned' => 'decimal:2',
        'bonus' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Approve the commission
     */
    public function approve(): void
    {
        $this->status = 'approved';
        $this->approved_at = now();
        $this->save();
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(): void
    {
        $this->status = 'paid';
        $this->paid_at = now();
        $this->save();
    }

    /**
     * Get total amount (commission + bonus)
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->commission_earned + $this->bonus;
    }

    /**
     * Scope to get pending commissions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved commissions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get paid commissions
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
