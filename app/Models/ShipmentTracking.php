<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class ShipmentTracking extends Model
{
    use HasUuid;

    protected $fillable = [
        'delivery_booking_id', 'tracking_number',
        'carrier', 'status', 'description',
        'location', 'event_at',
    ];

    protected $casts = [
        'event_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(DeliveryBooking::class, 'delivery_booking_id');
    }
}