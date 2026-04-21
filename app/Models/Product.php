<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'seller_id', 'category_id', 'subcategory_id', 'brand_id',
        'name', 'slug', 'description', 'price', 'sale_price',
        'stock', 'sku', 'condition', 'location', 'weight_kg',
        'status', 'rejection_reason', 'is_featured',
        'average_rating', 'total_reviews', 'total_sold', 'views', 'approved_by'
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'sale_price'     => 'decimal:2',
        'weight_kg'      => 'decimal:2',
        'is_featured'    => 'boolean',
        'average_rating' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $slug = Str::slug($model->name);
                $count = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = Str::slug($model->name) . '-' . $count++;
                }
                $model->slug = $slug;
            }
        });
    }

    public function seller()      { return $this->belongsTo(Seller::class); }
    public function category()    { return $this->belongsTo(Category::class); }
    public function subcategory() { return $this->belongsTo(Subcategory::class); }
    public function brand()       { return $this->belongsTo(Brand::class); }
    public function images()      { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function videos()      { return $this->hasMany(ProductVideo::class); }
    public function reviews()     { return $this->hasMany(ProductReview::class); }
    public function options(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(ProductOption::class, 'product_id')->orderBy('sort_order'); }
     

    public function getPrimaryImageAttribute()
    {
        return $this->images->where('is_primary', true)->first()
            ?? $this->images->first();
    }

    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function updateAverageRating()
    {
        $avg = $this->reviews()->where('is_visible', true)->avg('rating');
        $this->average_rating = round($avg, 1);
        $this->total_reviews = $this->reviews()->where('is_visible', true)->count();
        $this->save();
    }
}