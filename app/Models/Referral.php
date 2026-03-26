<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Referral extends Model
{
    use HasUuid;

    protected $fillable = [
        'referrer_type', 'referrer_id',
        'referred_type', 'referred_id',
        'referral_code',
    ];

    public function referrer()  { return $this->morphTo('referrer'); }
    public function referred()  { return $this->morphTo('referred'); }
    public function earnings()  { return $this->hasMany(ReferralEarning::class); }
}
