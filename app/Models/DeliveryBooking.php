<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Support\Str;

class DeliveryBooking extends Model
{
    use HasUuid;

    protected $fillable = [
        'booking_number', 'user_id', 'rider_id', 'order_id',
        'delivery_type', 'payment_reference',
        // Pickup
        'pickup_address', 'pickup_city', 'pickup_country',
        // Delivery
        'delivery_address', 'delivery_city', 'delivery_country',
        // Package
        'item_description', 'weight_kg', 'declared_value',
        // Pricing
        'fee', 'payment_status',
        // Status
        'status',
        // Shipbubble
        'shipbubble_order_id', 'shipbubble_shipment_id',
        'carrier', 'courier_id', 'service_code', 'service_name',
        'tracking_number', 'tracking_url',
        'estimated_delivery_date',
        'rate_data',
    ];

    protected $casts = [
        'weight_kg'      => 'decimal:2',
        'fee'            => 'decimal:2',
        'declared_value' => 'decimal:2',
        'rate_data'      => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->booking_number)) {
                $model->booking_number = 'BKG-' . strtoupper(Str::random(8));
            }
        });
    }

    public function user()  { return $this->belongsTo(User::class); }
    public function rider() { return $this->belongsTo(Rider::class); }
    public function order() { return $this->belongsTo(Order::class); }

    public function tracking()
    {
        return $this->hasMany(ShipmentTracking::class, 'delivery_booking_id', 'id');
    }
}