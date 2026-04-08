<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasUuid;

    protected $fillable = [
        // Core
        'order_number', 'user_id',
        // Amounts
        'subtotal', 'shipping_fee', 'commission_total', 'total',
        // Payment
        'payment_method', 'payment_status', 'payment_reference',
        // Order status
        'status',
        // Delivery address
        'shipping_name', 'shipping_phone', 'shipping_address',
        'shipping_city', 'shipping_state', 'shipping_country', 'shipping_zip',
        // Shipping carrier & service (kept for display/reference)
        'shipping_carrier', 'shipping_service_code', 'shipping_service_name',
        // Package info
        'declared_value', 'package_weight',
        // Raw rate data from Shipbubble
        'shipping_rate_data',
        // Flags
        'is_multi_seller',
        // Misc
        'notes', 'delivered_at', 'completed_at',
    ];

    protected $casts = [
        'subtotal'           => 'decimal:2',
        'shipping_fee'       => 'decimal:2',
        'commission_total'   => 'decimal:2',
        'total'              => 'decimal:2',
        'declared_value'     => 'decimal:2',
        'shipping_rate_data' => 'array',
        'is_multi_seller'    => 'boolean',
        'delivered_at'       => 'datetime',
        'completed_at'       => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->order_number)) {
                $model->order_number = 'ORD-' . strtoupper(Str::random(10));
            }
        });
    }

    // ── Relationships ────────────────────────────────────────
    public function user()       { return $this->belongsTo(User::class); }
    public function items()      { return $this->hasMany(OrderItem::class); }
    public function statusLogs() { return $this->hasMany(OrderStatusLog::class); }
    public function escrow()     { return $this->hasOne(EscrowHold::class); }
    public function escrowHolds() { return $this->hasMany(EscrowHold::class); }

    // ── Helpers ──────────────────────────────────────────────
    public function allItemsDelivered(): bool
    {
        return $this->items()->whereNotIn('status', ['delivered', 'completed'])->doesntExist();
    }

    public function getShippingLabelAttribute(): string
    {
        if ($this->shipping_carrier && $this->shipping_service_name) {
            return "{$this->shipping_carrier} — {$this->shipping_service_name}";
        }
        return 'Standard Shipping';
    }
}