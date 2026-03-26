<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Rider extends Authenticatable
{
    use HasUuid, Notifiable, SoftDeletes;

    protected $table = 'riders';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'password',
        'avatar', 'vehicle_type', 'vehicle_plate',
        'city', 'state', 'country',
        'is_available', 'is_active', 'is_approved', 'rating',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_available' => 'boolean',
        'is_active'    => 'boolean',
        'is_approved'  => 'boolean',
        'rating'       => 'decimal:2',
        'password'     => 'hashed',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function bookings()
    {
        return $this->hasMany(DeliveryBooking::class);
    }
}
