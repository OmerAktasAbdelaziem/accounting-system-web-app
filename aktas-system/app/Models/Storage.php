<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Storage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'branch_id',
        'location',
        'description',
        'capacity',
        'current_usage',
        'storage_type',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'decimal:2',
        'current_usage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(StorageItem::class);
    }
}
