<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOptionValue extends Model
{
    use HasUuids;

    protected $fillable = [
        'product_option_id',
        'value',
        'image_url',
        'cloudinary_public_id',
        'sort_order',
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }
}