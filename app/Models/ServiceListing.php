<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
use Illuminate\Support\Str;

class ServiceListing extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'seller_id', 'category_id', 'title', 'slug',
        'description', 'pricing_type', 'price',
        'delivery_time', 'location', 'portfolio_images',
        'status', 'rejection_reason',
        'average_rating', 'total_reviews','approved_by'
    ];

    protected $casts = [
        'portfolio_images' => 'array',
        'price'            => 'decimal:2',
        'average_rating'   => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $slug = Str::slug($model->title);
                $count = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = Str::slug($model->title) . '-' . $count++;
                }
                $model->slug = $slug;
            }
        });
    }

    public function seller()   { return $this->belongsTo(Seller::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function reviews() { return $this->hasMany(BrandReview::class); }
}