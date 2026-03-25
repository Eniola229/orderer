<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class OrderItem extends Model
{
    use HasUuid;

    protected $fillable = [
        'order_id', 'seller_id', 'orderable_type', 'orderable_id',
        'item_name', 'item_image', 'unit_price', 'quantity',
        'total_price', 'commission_rate', 'commission_amount',
        'seller_earnings', 'status',
    ];

    protected $casts = [
        'unit_price'         => 'decimal:2',
        'total_price'        => 'decimal:2',
        'commission_rate'    => 'decimal:2',
        'commission_amount'  => 'decimal:2',
        'seller_earnings'    => 'decimal:2',
    ];

    public function order()     { return $this->belongsTo(Order::class); }
    public function seller()    { return $this->belongsTo(Seller::class); }
    public function orderable() { return $this->morphTo(); }
}