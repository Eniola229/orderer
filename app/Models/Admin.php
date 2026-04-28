<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasUuid;

class Admin extends Authenticatable
{
    use HasUuid, Notifiable; 

    protected $guard = 'admin';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
        'role', 'status', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'last_login_at' => 'datetime',
        'password'      => 'hashed',
    ];

    // ── Role constants ────────────────────────────────────────
    const ROLE_SUPER_ADMIN       = 'super_admin';
    const ROLE_FINANCE_ADMIN     = 'finance_admin';
    const ROLE_SUPPORT_ADMIN     = 'support_admin';
    const ROLE_CONTENT_MODERATOR = 'content_moderator';
    const ROLE_HR                = 'hr';

    public static function roles(): array
    {
        return [
            self::ROLE_SUPER_ADMIN       => 'Super Admin',
            self::ROLE_FINANCE_ADMIN     => 'Finance Admin',
            self::ROLE_SUPPORT_ADMIN     => 'Support Admin',
            self::ROLE_CONTENT_MODERATOR => 'Content Moderator',
            self::ROLE_HR                => 'HR',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    // ── Permission checks ─────────────────────────────────────

    /** Can view everything (read-only minimum) */
    public function canView(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_FINANCE_ADMIN,
            self::ROLE_SUPPORT_ADMIN,
            self::ROLE_CONTENT_MODERATOR,
            self::ROLE_HR,
        ]);
    }

    /** Approve / reject sellers and products */
    public function canModerateSellers(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_CONTENT_MODERATOR,
        ]);
    }

    public function canModerateBuyer(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_CONTENT_MODERATOR,
        ]);
    }

    /** Approve / reject / manage ads */
    public function canManageAds(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_CONTENT_MODERATOR,
        ]);
    }

    /** View and reply to support tickets */
    public function canHandleSupport(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_SUPPORT_ADMIN,
        ]);
    }

    /** Process withdrawals, view finance, manage wallets */
    public function canManageFinance(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_FINANCE_ADMIN,
        ]);
    }

    /** Edit / cancel orders */
    public function canEditOrders(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_FINANCE_ADMIN,
        ]);
    }

    /** Add, edit, suspend other admins */
    public function canManageAdmins(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_HR,
        ]);
    }

    /** View activity logs */
    public function canViewLogs(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /** Manage categories and brands */
    public function canManageCategories(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_CONTENT_MODERATOR,
        ]);
    }

    // ── Relationships ─────────────────────────────────────────
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /** Send newsletters to buyers and/or sellers */
    public function canManageNewsletter(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_SUPPORT_ADMIN, 
        ]);
    }
}