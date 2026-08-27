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
     * Scope to get by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
