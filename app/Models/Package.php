<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_days',
        'max_employees',
        'max_currencies',
        'max_languages',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Get all features included in this package
     */
    public function features(): HasMany
    {
        return $this->hasMany(PackageFeature::class);
    }

    /**
     * Get all subscriptions using this package
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Check if package includes a specific feature
     */
    public function hasFeature(string $featureKey): bool
    {
        return $this->features()
            ->where('feature_key', $featureKey)
            ->exists();
    }

    /**
     * Get active packages
     */
    public static function active()
    {
        return static::where('is_active', true);
    }
}
