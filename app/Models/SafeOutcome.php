<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SafeOutcome extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'safe_id',
        'amount',
        'description',
        'currency_id',
        'reference',
        'reference_type',
        'supplier_id',
        'created_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function safe()
    {
        return $this->belongsTo(Safe::class);
    }

    public function currency()
    {
        return $this->belongsTo(SafeCurrency::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
