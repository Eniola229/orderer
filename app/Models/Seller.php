<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Seller extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasUuid;

    protected $table = 'sellers';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'business_name',
        'business_slug',
        'business_description',
        'business_address',
        'address_code',
        'avatar',
        'banner_image',
        'is_verified_business',
        'verification_status',   // pending | approved | rejected
        'approval_note',
        'is_active',
        'is_approved',
        'wallet_balance',
        'ads_balance',
        'referral_code',
        'referred_by',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'rejection_reason',
        'rejected_at',
        'rejected_by',
        'approved_by',
        'approved_at',
        'marketer_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'    => 'datetime',
        'last_login_at'        => 'datetime',
        'is_active'            => 'boolean',
        'is_approved'          => 'boolean',
        'is_verified_business' => 'boolean',
        'wallet_balance'       => 'decimal:2',
        'ads_balance'          => 'decimal:2',
        'password'             => 'hashed',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function documents()
    {
        return $this->hasMany(SellerDocument::class);
    }

    public function document()
    {
        return $this->belongsTo(SellerDocument::class, 'id', 'seller_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

        public function brand()
        {
            return $this->hasOne(Brand::class);  // One seller has one brand
        }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, OrderItem::class, 'seller_id', 'id', 'id', 'order_id');
    }

}