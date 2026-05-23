<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_sale_id',
        'employee_id',
        'description',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(EmployeeSale::class, 'employee_sale_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }
}