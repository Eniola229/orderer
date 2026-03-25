<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class ProductImage extends Model
{
    use HasUuid;

    protected $fillable = [
        'product_id', 'image_url',
        'cloudinary_public_id', 'is_primary', 'sort_order',
    ];

    protected $casts = ['is_primary' => 'boolean'];

    public function product() { return $this->belongsTo(Product::class); }
}