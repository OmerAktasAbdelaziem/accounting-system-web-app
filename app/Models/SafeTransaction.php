<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SafeTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'safe_id',
        'type',
        'amount',
        'reference_type',
        'reference_id',
        'user_id',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function safe()
    {
        return $this->belongsTo(Safe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
