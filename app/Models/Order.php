<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasUuid;

    protected $fillable = [
        'order_number', 'user_id', 'subtotal', 'shipping_fee',
        'commission_total', 'total', 'payment_method',
        'payment_status', 'status', 'shipping_name',
        'shipping_phone', 'shipping_address', 'shipping_city',
        'shipping_state', 'shipping_country', 'shipping_zip',
        'payment_reference', 'notes', 'delivered_at', 'completed_at',
    ];

    protected $casts = [
        'subtotal'          => 'decimal:2',
        'shipping_fee'      => 'decimal:2',
        'commission_total'  => 'decimal:2',
        'total'             => 'decimal:2',
        'delivered_at'      => 'datetime',
        'completed_at'      => 'datetime',
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

    public function user()       { return $this->belongsTo(User::class); }
    public function items()      { return $this->hasMany(OrderItem::class); }
    public function statusLogs() { return $this->hasMany(OrderStatusLog::class); }
    public function escrow()     { return $this->hasOne(EscrowHold::class); }
}