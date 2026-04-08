<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class EscrowHold extends Model
{
    use HasUuid;

    protected $with = ['buyer', 'seller'];

    protected $fillable = [
        'order_id', 'order_item_id', 'seller_id', 'buyer_id',
        'amount', 'commission_amount', 'seller_amount',
        'status', 'release_at', 'released_at',
    ];

    protected $casts = [
        'amount'            => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'seller_amount'     => 'decimal:2',
        'release_at'        => 'datetime',
        'released_at'       => 'datetime',
    ];

    public function order()     { return $this->belongsTo(Order::class); }
    public function orderItem() { return $this->belongsTo(OrderItem::class); }
    public function seller()    { return $this->belongsTo(Seller::class); }
    public function buyer()     { return $this->belongsTo(User::class, 'buyer_id'); }
    
}