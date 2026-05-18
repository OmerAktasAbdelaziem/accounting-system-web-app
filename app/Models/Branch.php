<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'phone',
        'manager_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function employees(): MorphToMany
    {
        return $this->morphedByMany(Employee::class, 'branchable');
    }

    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'branchable');
    }

    public function categories(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'branchable');
    }

    public function suppliers(): MorphToMany
    {
        return $this->morphedByMany(Supplier::class, 'branchable');
    }

    public function customers(): MorphToMany
    {
        return $this->morphedByMany(Customer::class, 'branchable');
    }

    public function invoices(): MorphToMany
    {
        return $this->morphedByMany(Invoice::class, 'branchable');
    }

    public function storages(): MorphToMany
    {
        return $this->morphedByMany(Storage::class, 'branchable');
    }

    public function safes(): MorphToMany
    {
        return $this->morphedByMany(Safe::class, 'branchable');
    }

    public function commissions(): MorphToMany
    {
        return $this->morphedByMany(Commission::class, 'branchable');
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
}
