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
        'title', 'media_url', 'cloudinary_public_id', 'media_type',
        'click_url', 'budget', 'cost_per_day', 'amount_spent',
        'start_date', 'end_date', 'status', 'approved_by',
        'rejection_reason', 'total_impressions', 'total_clicks',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'end_date'          => 'date',
        'budget'            => 'decimal:2',
        'cost_per_day'      => 'decimal:2',
        'amount_spent'      => 'decimal:2',
        'total_impressions' => 'integer',
        'total_clicks'      => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────
    public function seller()     { return $this->belongsTo(Seller::class); }
    public function adCategory() { return $this->belongsTo(AdCategory::class); }
    public function bannerSlot() { return $this->belongsTo(AdBannerSlot::class, 'ad_banner_slot_id'); }
    public function promotable() { return $this->morphTo(); }
    public function impressions(){ return $this->hasMany(AdImpression::class); }
    public function clicks()     { return $this->hasMany(AdClick::class); }

    // ── Helpers ───────────────────────────────────────────────
    public function isActive(): bool
    {
        return $this->status === 'active'
            && now()->between($this->start_date, $this->end_date->endOfDay());
    }

    public function getRemainingBudgetAttribute(): float
    {
        return max(0, $this->budget - $this->amount_spent);
    }

    public function getCtrAttribute(): float
    {
        if ($this->total_impressions === 0) return 0;
        return round(($this->total_clicks / $this->total_impressions) * 100, 2);
    }

    /**
     * Click tracking URL for use in blade templates.
     */
    public function clickTrackingUrl(): string
    {
        return route('ads.click', $this->id);
    }
}
