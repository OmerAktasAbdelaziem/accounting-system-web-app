<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPurchaseItem extends Model
{
    protected $fillable = [
        'supplier_purchase_id',
        'product_name',
        'weight',
        'unit_price',
        'line_total',
    ];

    protected $casts = [
        'weight' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(SupplierPurchase::class, 'supplier_purchase_id');
    }
}
