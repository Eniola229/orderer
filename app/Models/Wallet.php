<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Wallet extends Model
{
    use HasUuid;

    protected $fillable = [
        'walletable_type', 'walletable_id',
        'balance', 'escrow_balance', 'ads_balance', 'currency',
    ];

    protected $casts = [
        'balance'         => 'decimal:2',
        'escrow_balance'  => 'decimal:2',
        'ads_balance'     => 'decimal:2',
    ];

    public function walletable() { return $this->morphTo(); }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }
}