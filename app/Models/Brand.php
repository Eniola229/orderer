<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasUuid;

    protected $fillable = [
        'seller_id', 'name', 'slug', 'description',
        'logo', 'banner', 'website', 'is_active',
        'average_rating', 'total_reviews',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'average_rating' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $slug  = Str::slug($model->name);
                $count = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = Str::slug($model->name) . '-' . $count++;
                }
                $model->slug = $slug;
            }
        });
    }

    public function seller()  { return $this->belongsTo(Seller::class); }
    public function reviews() { return $this->hasMany(BrandReview::class); }
    public function products(){ return $this->hasMany(Product::class); }
    
}