<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class TicketMessage extends Model
{
    use HasUuid;

    protected $fillable = [
        'support_ticket_id', 'sender_type', 'sender_id',
        'message', 'attachments', 'is_internal', 'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean',
        'read_at'     => 'datetime',
    ];

    public function ticket() { return $this->belongsTo(SupportTicket::class); }
    public function sender() { return $this->morphTo(); }
}