<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'location',
        'location_ar',
        'description',
        'capacity',
        'is_active',
    ];

    /**
     * Get warehouse inventory
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(WarehouseInventory::class);
    }

    /**
     * Get transfers from this warehouse
     */
    public function transfersFrom(): HasMany
    {
        return $this->hasMany(WarehouseTransfer::class, 'from_warehouse_id');
    }

    /**
     * Get transfers to this warehouse
     */
    public function transfersTo(): HasMany
    {
        return $this->hasMany(WarehouseTransfer::class, 'to_warehouse_id');
    }

    /**
     * Get product quantity in this warehouse
     */
    public function getProductQuantity($productId): int
    {
        return $this->inventory()
            ->where('product_id', $productId)
            ->first()?->quantity ?? 0;
    }

    /**
     * Update product quantity in this warehouse
     */
    public function updateProductQuantity($productId, $quantity, $reserved = 0): WarehouseInventory
    {
        return $this->inventory()->updateOrCreate(
            ['product_id' => $productId],
            [
                'quantity' => $quantity,
                'reserved_quantity' => $reserved,
            ]
        );
    }

    /**
     * Scope to get only active warehouses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
