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
        // Shipping carrier & service
        'shipping_carrier', 'shipping_service_code', 'shipping_service_name',
        // Shipbubble tracking
        'shipbubble_shipment_id', 'courier_id', 'shipping_status',
        'tracking_number', 'tracking_url', 'estimated_delivery_date',
        // Package
        'declared_value', 'package_weight',
        // Raw rate data from Shipbubble stored for reference
        'shipping_rate_data',
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
    public function tracking()   { return $this->hasMany(ShipmentTracking::class, 'tracking_number', 'tracking_number'); }

    // ── Helpers ──────────────────────────────────────────────
    public function hasTracking(): bool
    {
        return !empty($this->tracking_number);
    }

    public function getShippingLabelAttribute(): string
    {
        if ($this->shipping_carrier && $this->shipping_service_name) {
            return "{$this->shipping_carrier} — {$this->shipping_service_name}";
        }
        return 'Standard Shipping';
    }
}