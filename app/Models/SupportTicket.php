<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    use HasUuid;

    protected $fillable = [
        'ticket_number', 'subject',
        'requester_type', 'requester_id',
        'assigned_to', 'priority', 'status', 'category',
        'resolved_at',
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->ticket_number)) {
                $model->ticket_number = 'TKT-' . strtoupper(Str::random(8));
            }
        });
    }

    public function requester() { return $this->morphTo(); }
    public function messages()  { return $this->hasMany(TicketMessage::class); }
    public function admin()     { return $this->belongsTo(Admin::class, 'assigned_to'); }
}