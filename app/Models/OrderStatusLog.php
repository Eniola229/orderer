<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class OrderStatusLog extends Model
{
    use HasUuid;

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'from_status', 'to_status',
        'changed_by_type', 'changed_by_id', 'note', 'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(fn($m) => $m->created_at = now());
    }

    public function order() { return $this->belongsTo(Order::class); }
}