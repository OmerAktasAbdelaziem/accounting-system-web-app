<?php

namespace App\Models;

use App\Models\Concerns\HasBranches;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    use \App\Models\Concerns\HasBranches;

    protected $fillable = [
        'name',
        'name_ar',
        'barcode',
        'category_id',
        'branch_id',
        'description',
        'unit',
        'unit_ar',
        'purchase_price',
        'selling_price',
        'wholesale_price',
        'profit_margin',
        'current_stock',
        'is_active',
        'track_inventory',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'profit_margin' => 'decimal:2',
    ];

    /**
     * Get the category this product belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all inventory movements for this product
     */
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Scope to get only active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if product is low on stock
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= 0;
    }

    /**
     * Calculate profit on product
     */
    public function calculateProfit(): float
    {
        if ($this->purchase_price == 0) {
            return 0;
        }
        return ($this->selling_price - $this->purchase_price) / $this->purchase_price * 100;
    }

    /**
     * Get total value of stock
     */
    public function getStockValueAttribute(): float
    {
        return $this->current_stock * $this->purchase_price;
    }

    /**
     * Update stock quantity
     */
    public function updateStock($quantity, $movementType = 'adjustment', $reference = null, $notes = null, $userId = null): InventoryMovement
    {
        $stockBefore = $this->current_stock;
        
        if (in_array($movementType, ['incoming', 'transfer_in', 'return'])) {
            $this->current_stock += $quantity;
        } elseif (in_array($movementType, ['outgoing', 'waste', 'transfer_out'])) {
            $this->current_stock -= $quantity;
        }

        $this->save();

        return InventoryMovement::create([
            'product_id' => $this->id,
            'movement_type' => $movementType,
            'quantity' => $quantity,
            'unit_price' => $this->purchase_price,
            'reference_type' => $reference['type'] ?? null,
            'reference_id' => $reference['id'] ?? null,
            'notes' => $notes,
            'created_by' => $userId ?? auth()->id(),
            'stock_before' => $stockBefore,
            'stock_after' => $this->current_stock,
        ]);
    }
}
