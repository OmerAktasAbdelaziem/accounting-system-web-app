<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDeduction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'type',
        'type_ar',
        'amount',
        'description',
        'description_ar',
        'status',
        'deducted_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deducted_at' => 'datetime',
    ];

    /**
     * Get the employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Mark as deducted
     */
    public function markAsDeducted(): void
    {
        $this->status = 'deducted';
        $this->deducted_at = now();
        $this->save();
    }

    /**
     * Scope to get pending deductions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved deductions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get deducted deductions
     */
    public function scopeDeducted($query)
    {
        return $query->where('status', 'deducted');
    }

    /**
     * Scope to get by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
