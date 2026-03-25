<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class KorapayTransaction extends Model
{
    use HasUuid;

    protected $table = 'korapay_transactions';

    protected $fillable = [
        'reference', 'korapay_reference',
        'payable_type', 'payable_id',
        'amount', 'currency', 'type',
        'status', 'gateway_response',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'gateway_response' => 'array',
    ];

    public function payable() { return $this->morphTo(); }
}