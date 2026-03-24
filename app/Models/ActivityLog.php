<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $table = 'activity_logs';

    protected $fillable = [
        'guard_type',
        'guard_id',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'status_code',
        'payload',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'payload'    => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }

    // Relationships
    public function buyer()
    {
        return $this->belongsTo(User::class, 'guard_id')
                    ->where('guard_type', 'buyer');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'guard_id')
                    ->where('guard_type', 'seller');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'guard_id')
                    ->where('guard_type', 'admin');
    }
}