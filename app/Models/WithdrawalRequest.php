<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class WithdrawalRequest extends Model
{
    use HasUuid;

    protected $fillable = [
        'seller_id', 'amount', 'bank_name', 'account_number',
        'account_name', 'bank_country', 'dollar_capable',
        'swift_code', 'status', 'rejection_reason',
        'transaction_reference', 'processed_by', 'processed_at',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'dollar_capable' => 'boolean',
        'processed_at'   => 'datetime',
    ];

    public function seller() { return $this->belongsTo(Seller::class); }
}