<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasUuid;
use Illuminate\Support\Str;

class Marketer extends Authenticatable
{
    use HasUuid, Notifiable;

    protected $guard = 'marketer';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'marketing_code',
        'is_active',
        'last_login_at',
        'notes',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'last_login_at' => 'datetime',
        'password'      => 'hashed',
        'is_active'     => 'boolean',
    ];

    // ── Accessors ─────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // ── Code Generation ───────────────────────────────────────

    /**
     * Generate a unique OR-MRT-XXXXXX marketing code.
     */
    public static function generateMarketingCode(): string
    {
        do {
            $code = 'OR-MRT-' . strtoupper(Str::random(6));
        } while (self::where('marketing_code', $code)->exists());

        return $code;
    }

    // ── Relationships ─────────────────────────────────────────

    /**
     * Sellers who registered using this marketer's code.
     */
    public function referredSellers()
    {
        return $this->hasMany(Seller::class, 'marketer_id');
    }
}