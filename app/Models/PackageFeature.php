<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageFeature extends Model
{
    use HasFactory;

    protected $table = 'package_features';
    protected $fillable = ['package_id', 'feature_key', 'feature_name', 'description'];

    /**
     * Get the package this feature belongs to
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
