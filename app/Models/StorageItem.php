<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'storage_id',
        'product_id',
        'quantity',
        'location_code',
        'entry_date',
        'expiry_date',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
