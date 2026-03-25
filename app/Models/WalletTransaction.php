<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Support\Str;

class WalletTransaction extends Model
{
    use HasUuid;

    protected $fillable = [
        'wallet_id', 'type', 'amount',
        'balance_before', 'balance_after',
        'reference', 'description',
        'related_type', 'related_id', 'status',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after'  => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->reference)) {
                $model->reference = 'TXN-' . strtoupper(Str::random(12));
            }
        });
    }

    public function wallet() { return $this->belongsTo(Wallet::class); }
}