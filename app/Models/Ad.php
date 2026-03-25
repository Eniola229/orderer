<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Ad extends Model
{
    use HasUuid;

    protected $fillable = [
        'seller_id', 'ad_category_id', 'ad_banner_slot_id',
        'promotable_type', 'promotable_id',
        'title', 'media_url', 'cloudinary_public_id',
        'media_type', 'click_url', 'budget', 'amount_spent',
        'cost_per_day', 'cost_per_click', 'status',
        'rejection_reason', 'start_date', 'end_date',
        'total_impressions', 'total_clicks', 'total_conversions',
        'approved_by',
    ];

    protected $casts = [
        'budget'       => 'decimal:2',
        'amount_spent' => 'decimal:2',
        'start_date'   => 'date',
        'end_date'     => 'date',
    ];

    public function seller()      { return $this->belongsTo(Seller::class); }
    public function adCategory()  { return $this->belongsTo(AdCategory::class); }
    public function bannerSlot()  { return $this->belongsTo(AdBannerSlot::class, 'ad_banner_slot_id'); }
    public function promotable()  { return $this->morphTo(); }
}