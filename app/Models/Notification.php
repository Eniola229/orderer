<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Notification extends Model
{
    use HasUuid;

    protected $fillable = [
        'notifiable_type', 'notifiable_id',
        'type', 'title', 'body',
        'data', 'action_url', 'read_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    public function notifiable() { return $this->morphTo(); }

    public function isRead(): bool { return !is_null($this->read_at); }
}