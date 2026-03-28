<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class ActivityLog extends Model
{
    use HasUuid;
    
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

    // Get the actual user object based on guard type
    public function getUser()
    {
        if (!$this->guard_id) return null;
        
        switch ($this->guard_type) {
            case 'admin':
                return Admin::find($this->guard_id);
            case 'seller':
                return Seller::find($this->guard_id);
            case 'buyer':
                return User::find($this->guard_id);
            default:
                return null;
        }
    }
    
    // Accessor for user name
    public function getUserNameAttribute()
    {
        $user = $this->getUser();
        
        if (!$user) return 'Guest';
        
        if ($this->guard_type === 'admin') {
            return $user->first_name . ' ' . $user->last_name;
        }
        
        if ($this->guard_type === 'seller') {
            return $user->business_name ?? ($user->first_name . ' ' . $user->last_name);
        }
        
        if ($this->guard_type === 'buyer') {
            return $user->first_name . ' ' . $user->last_name;
        }
        
        return 'Guest';
    }
    
    // Accessor for user email
    public function getUserEmailAttribute()
    {
        $user = $this->getUser();
        
        if (!$user) return '—';
        
        return $user->email ?? '—';
    }
    
    // Accessor for user avatar
    public function getUserAvatarAttribute()
    {
        $user = $this->getUser();
        
        if (!$user) return null;
        
        return $user->avatar ?? null;
    }
    
    // Accessor for guard type label
    public function getGuardTypeLabelAttribute()
    {
        $types = [
            'admin' => 'Admin',
            'seller' => 'Seller',
            'buyer' => 'Buyer',
        ];
        
        return $types[$this->guard_type] ?? 'Guest';
    }
}