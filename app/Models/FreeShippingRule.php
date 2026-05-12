<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Carbon\Carbon;

class FreeShippingRule extends Model
{
    use HasUuid;

    protected $fillable = [
        'name', 'description', 'applies_to', 'new_buyer_days',
        'product_scope', 'minimum_order_amount', 'max_discount_amount',
        'starts_at', 'ends_at', 'is_active', 'created_by',
    ];

    protected $casts = [
        'starts_at'             => 'datetime',
        'ends_at'               => 'datetime',
        'is_active'             => 'boolean',
        'minimum_order_amount'  => 'decimal:2',
        'max_discount_amount'   => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function buyers()
    {
        return $this->belongsToMany(User::class, 'free_shipping_rule_buyers', 'rule_id', 'user_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'free_shipping_rule_products', 'rule_id', 'product_id');
    }

    public function sellers()
    {
        return $this->belongsToMany(Seller::class, 'free_shipping_rule_sellers', 'rule_id', 'seller_id');
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->ends_at && $this->ends_at->isPast()) return false;
        return true;
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) return 'Disabled';
        if ($this->starts_at && $this->starts_at->isFuture()) return 'Scheduled';
        if ($this->ends_at && $this->ends_at->isPast()) return 'Expired';
        return 'Active';
    }

    public function getAppliesToLabelAttribute(): string
    {
        return match($this->applies_to) {
            'all_buyers'       => 'All Buyers',
            'new_buyers'       => 'New Buyers (last ' . ($this->new_buyer_days ?? 30) . ' days)',
            'buyers_no_orders' => 'Buyers With No Orders',
            'specific_buyers'  => 'Specific Buyers',
            default            => ucfirst($this->applies_to),
        };
    }

    public function getProductScopeLabelAttribute(): string
    {
        return match($this->product_scope) {
            'all'               => 'All Products',
            'specific_products' => 'Specific Products',
            'specific_sellers'  => 'Specific Sellers',
            default             => ucfirst($this->product_scope),
        };
    }

    /**
     * Check if a given user qualifies for this rule.
     */
    public function qualifiesForUser(User $user): bool
    {
        return match($this->applies_to) {
            'all_buyers'       => true,
            'new_buyers'       => $user->created_at->gte(
                                      now()->subDays($this->new_buyer_days ?? 30)
                                  ),
            'buyers_no_orders' => $user->orders()->doesntExist(),
            'specific_buyers'  => $this->buyers()->where('user_id', $user->id)->exists(),
            default            => false,
        };
    }

    /**
     * Check if any cart item (product/seller) matches the product scope.
     * $cartProductIds  — array of product IDs in the cart
     * $cartSellerIds   — array of seller IDs in the cart
     */
    public function qualifiesForCart(array $cartProductIds, array $cartSellerIds): bool
    {
        return match($this->product_scope) {
            'all'               => true,
            'specific_products' => $this->products()
                                        ->whereIn('products.id', $cartProductIds)
                                        ->exists(),
            'specific_sellers'  => $this->sellers()
                                        ->whereIn('sellers.id', $cartSellerIds)
                                        ->exists(),
            default             => false,
        };
    }
}