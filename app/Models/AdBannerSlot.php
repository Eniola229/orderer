<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class AdBannerSlot extends Model
{
    use HasUuid;

    protected $fillable = [
        'name', 'slug', 'location',
        'price_per_day', 'max_ads', 'dimensions', 'is_active',
    ];

    protected $casts = [
        'price_per_day' => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    public function ads() { return $this->hasMany(Ad::class); }
}