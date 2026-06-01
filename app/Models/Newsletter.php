<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Newsletter extends Model
{
    use HasUuid;

    protected $fillable = [
        'subject',
        'body',
        'audience',        // buyers | sellers | both
        'status',          // draft | queued | sending | sent | failed
        'total_recipients',
        'sent_count',
        'failed_count',
        'sent_at',
        'created_by',
        'send_sms',
        'sms_message',
        'sms_audience',
        'sms_extra_numbers',

    ];

    protected $casts = [
        'sent_at'          => 'datetime',
        'total_recipients' => 'integer',
        'sent_count'       => 'integer',
        'failed_count'     => 'integer',
        'send_sms'          => 'boolean',
        'sms_extra_numbers' => 'array',
    ];

    // ── Audience constants ────────────────────────────────────
    const AUDIENCE_BUYERS              = 'buyers';
    const AUDIENCE_SELLERS             = 'sellers';
    const AUDIENCE_BOTH                = 'both';
    const AUDIENCE_GUESTS              = 'guests';
    const AUDIENCE_NEW_BUYERS          = 'new_buyers';
    const AUDIENCE_BUYERS_NO_ORDERS    = 'buyers_no_orders';
    const AUDIENCE_BUYERS_WITH_ORDERS  = 'buyers_with_orders';
    const AUDIENCE_SELLERS_NO_LISTINGS = 'sellers_no_listings'; 

    // ── Status constants ──────────────────────────────────────
    const STATUS_DRAFT   = 'draft';
    const STATUS_QUEUED  = 'queued';
    const STATUS_SENDING = 'sending';
    const STATUS_SENT    = 'sent';
    const STATUS_FAILED  = 'failed';

    // ── Relationships ─────────────────────────────────────────
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    // ── Helpers ───────────────────────────────────────────────
    public function getProgressPercentAttribute(): int
    {
        if ($this->total_recipients === 0) {
            return 0;
        }

        return (int) round(
            (($this->sent_count + $this->failed_count) / $this->total_recipients) * 100
        );
    }

    public function getAudienceLabelAttribute(): string
    {
        return match ($this->audience) {
            self::AUDIENCE_BUYERS              => 'Buyers only',
            self::AUDIENCE_SELLERS             => 'Sellers only',
            self::AUDIENCE_GUESTS              => 'Subscribers only',
            self::AUDIENCE_NEW_BUYERS          => 'New Buyers (last 30 days)',
            self::AUDIENCE_BUYERS_NO_ORDERS    => 'Buyers – No Orders Yet',
            self::AUDIENCE_BUYERS_WITH_ORDERS  => 'Buyers – Have Ordered',
            self::AUDIENCE_SELLERS_NO_LISTINGS => 'Sellers – No Listings Yet',
            default                            => 'Buyers & Sellers',
        };
    }
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }
}