<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Storage extends Model
{
    use SoftDeletes;
    use \App\Models\Concerns\HasBranches;

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

    public function transfersFrom(): HasMany
    {
        return $this->hasMany(StorageTransfer::class, 'from_storage_id');
    }

    public function transfersTo(): HasMany
    {
        return $this->hasMany(StorageTransfer::class, 'to_storage_id');
    }
}
