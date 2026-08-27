<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageTransfer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'from_storage_id',
        'to_storage_id',
        'product_name',
        'quantity',
        'weight',
        'unit_price',
        'total_price',
        'transfer_date',
        'transferred_by',
    ];

    protected $casts = [
        'transfer_date' => 'datetime',
        'quantity' => 'decimal:2',
        'weight' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function fromStorage()
    {
        return $this->belongsTo(Storage::class, 'from_storage_id');
    }

    public function toStorage()
    {
        return $this->belongsTo(Storage::class, 'to_storage_id');
    }

    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }
}
