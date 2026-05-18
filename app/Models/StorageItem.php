<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'storage_id',
        'product_name',
        'quantity',
        'weight',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'weight' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }
}
