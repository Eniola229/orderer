<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class BrandReview extends Model
{
    use HasUuid;

    protected $fillable = [
        'brand_id', 'user_id', 'rating', 'review', 'is_visible',
    ];

    protected $casts = ['is_visible' => 'boolean'];

    public function brand() { return $this->belongsTo(Brand::class); }
    public function user()  { return $this->belongsTo(User::class); }
}