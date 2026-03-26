<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class ReferralEarning extends Model
{
    use HasUuid;

    protected $fillable = [
        'referral_id', 'amount', 'currency',
        'triggered_by', 'status', 'credited_at',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'credited_at' => 'datetime',
    ];

    public function referral() { return $this->belongsTo(Referral::class); }
}
