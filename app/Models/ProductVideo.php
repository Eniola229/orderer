<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class ProductVideo extends Model
{
    use HasUuid;

    protected $fillable = [
        'product_id', 'video_url',
        'cloudinary_public_id', 'thumbnail_url',
    ];

    public function product() { return $this->belongsTo(Product::class); }
}