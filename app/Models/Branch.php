<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Branch extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Branch $branch) {
            $user = Auth::user();
            if ($user && !$user->isSuperAdmin() && empty($branch->merchant_id)) {
                $branch->merchant_id = $user->merchant_id;
            }
        });

        static::addGlobalScope('merchant_scope', function (Builder $builder) {
            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();
            if (!$user || $user->isSuperAdmin() || empty($user->merchant_id)) {
                return;
            }

            $builder->where('merchant_id', $user->merchant_id);
        });

        static::addGlobalScope('role_branch_scope', function (Builder $builder) {
            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();
            if (!$user || $user->isSuperAdmin() || $user->isMerchantAdmin() || empty($user->merchant_id)) {
                return;
            }

            $allowedBranchIds = $user->accessibleBranchIds();
            if ($allowedBranchIds === null) {
                return;
            }

            if (empty($allowedBranchIds)) {
                $builder->whereRaw('1 = 0');
                return;
            }

            $builder->whereIn('branches.id', $allowedBranchIds);
        });
    }

    protected $fillable = [
        'merchant_id',
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

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

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
