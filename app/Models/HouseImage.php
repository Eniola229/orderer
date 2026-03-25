<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class HouseImage extends Model
{
    use HasUuid;

    protected $fillable = [
        'house_listing_id', 'image_url',
        'cloudinary_public_id', 'is_primary', 'sort_order',
    ];

    protected $casts = ['is_primary' => 'boolean'];
}