<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPurchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'branch_id',
        'purchase_date',
        'total_amount',
        'note',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(SupplierPurchaseItem::class);
    }
}
