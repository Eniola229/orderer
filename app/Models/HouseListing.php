<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
use Illuminate\Support\Str;

class HouseListing extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'seller_id', 'title', 'slug', 'description',
        'property_type', 'listing_type', 'price',
        'location', 'address', 'city', 'state', 'country',
        'bedrooms', 'bathrooms', 'toilets', 'size_sqm',
        'features', 'video_tour_url', 'status', 'rejection_reason',
    ];

    protected $casts = [
        'features' => 'array',
        'price'    => 'decimal:2',
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

    public function seller() { return $this->belongsTo(Seller::class); }
    public function images() { return $this->hasMany(HouseImage::class)->orderBy('sort_order'); }
}