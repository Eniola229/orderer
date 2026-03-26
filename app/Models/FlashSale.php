<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class FlashSale extends Model
{
    use HasUuid;

    protected $fillable = [
        'title', 'product_id', 'sale_price', 'original_price',
        'quantity_limit', 'quantity_sold',
        'starts_at', 'ends_at', 'is_active', 'created_by',
    ];

    protected $casts = [
        'sale_price'     => 'decimal:2',
        'original_price' => 'decimal:2',
        'starts_at'      => 'datetime',
        'ends_at'        => 'datetime',
        'is_active'      => 'boolean',
    ];

    public function product() { return $this->belongsTo(Product::class); }

    public function isActive(): bool
    {
        return $this->is_active
            && now()->between($this->starts_at, $this->ends_at)
            && ($this->quantity_limit === null || $this->quantity_sold < $this->quantity_limit);
    }
}
