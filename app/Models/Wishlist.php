<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Wishlist extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id',
        'wishlistable_type',
        'wishlistable_id',
        'price_at_save',
    ];

    protected $casts = [
        'price_at_save' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wishlistable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}