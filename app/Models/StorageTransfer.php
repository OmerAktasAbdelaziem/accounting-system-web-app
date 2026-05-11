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
        'product_id',
        'quantity',
        'description',
        'transfer_date',
        'transferred_by',
    ];

    protected $casts = [
        'transfer_date' => 'datetime',
    ];

    public function fromStorage()
    {
        return $this->belongsTo(Storage::class, 'from_storage_id');
    }

    public function toStorage()
    {
        return $this->belongsTo(Storage::class, 'to_storage_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }
}
