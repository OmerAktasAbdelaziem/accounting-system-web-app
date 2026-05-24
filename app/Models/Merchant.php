<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'admin_email',
        'super_admin_id',
        'business_name',
        'slug',
        'default_currency_id',
        'max_currencies',
        'max_languages',
        'default_language',
        'max_employees',
        'description',
        'is_active',
        'subscription_expires_at'
    ];

    protected $casts = [
        'subscription_expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the super admin who created this merchant
     */
    public function superAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'super_admin_id');
    }

    /**
     * Get the default currency
     */
    public function defaultCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get all currencies available to this merchant
     */
    public function currencies(): BelongsToMany
    {
        return $this->belongsToMany(Currency::class, 'merchant_currencies')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    /**
     * Get all users belonging to this merchant
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all branches belonging to this merchant
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get all employees
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get merchant's VAT rates
     */
    public function vatRates(): HasMany
    {
        return $this->hasMany(VatRate::class);
    }

    /**
     * Get active subscription
     */
    public function subscription(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Check if subscription is active and not expired
     */
    public function isSubscriptionActive(): bool
    {
        if ($this->subscription_expires_at === null) {
            return true; // No expiry set
        }
        
        return now()->isBefore($this->subscription_expires_at);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->slug) {
                $model->slug = Str::slug($model->business_name) . '-' . Str::random(6);
            }
        });
    }
}
