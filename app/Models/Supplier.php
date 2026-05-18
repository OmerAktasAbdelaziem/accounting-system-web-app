<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    use \App\Models\Concerns\HasBranches;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'opening_balance',
        'branch_id',
    ];

    public function purchases()
    {
        return $this->hasMany(SupplierPurchase::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function branchableBranches()
    {
        return $this->branches();
    }

    public function getTotalPurchasedAttribute(): float
    {
        return (float) $this->purchases()->sum('total_amount');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getOutstandingBalanceAttribute(): float
    {
        return ((float) $this->opening_balance + $this->total_purchased) - $this->total_paid;
    }
}
