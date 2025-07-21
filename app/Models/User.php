<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_activity',
        'is_online',
        'penduduk_id',
        'otp',
        'email_verified_at', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
         'email_verified_at' => 'datetime',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_activity' => 'datetime',
            'is_online' => 'boolean',
            'otp' => 'string',
            'email_verified_at' => 'datetime'

        ];
    }

    // Relationships
    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    /**
     * Relationship with Penduduk
     */
    public function penduduk()
    {
        return $this->hasOne(Penduduk::class);
    }

    /**
     * Relationship with notifications
     */
    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifikasis()->where('dibaca', false)->count();
    }

    /**
     * Check if user has specific role(s)
     *
     * @param string|array $roles
     * @return bool
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }

        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return false;
    }

    /**
     * Check if user is an admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is RT
     *
     * @return bool
     */
    public function isRt(): bool
    {
        return $this->hasRole('rt');
    }

    /**
     * Check if user is RW
     *
     * @return bool
     */
    public function isRw(): bool
    {
        return $this->hasRole('rw');
    }

    /**
     * Check if user is Kades
     *
     * @return bool
     */
    public function isKades(): bool
    {
        return $this->hasRole('kades');
    }

    /**
     * Check if user is Masyarakat
     */
    public function isMasyarakat(): bool
    {
        return $this->role === 'masyarakat';
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if user is online
     */
    public function isOnline(): bool
    {
        return $this->is_online && $this->last_activity && $this->last_activity->diffInMinutes(now()) <= 10;
    }

    /**
     * Get user's role display name
     */
    public function getRoleDisplayAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'kades' => 'Kepala Desa',
            'rw' => 'Ketua RW',
            'rt' => 'Ketua RT',
            'masyarakat' => 'Masyarakat',
            default => 'Unknown'
        };
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }
}
