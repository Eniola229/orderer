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
        'delivery_type', 'pickup_address', 'pickup_city', 'pickup_country',
        'delivery_address', 'delivery_city', 'delivery_country',
        'item_description', 'weight_kg', 'fee',
        'payment_status', 'status',
        'shiprocket_order_id', 'tracking_number',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
        'fee'       => 'decimal:2',
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
}
