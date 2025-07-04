<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_activity',
        'is_online',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_activity' => 'datetime',
            'is_online' => 'boolean',
        ];
    }

    /**
     * Scope untuk user aktif - TAMBAHAN INI YANG MISSING
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if user is masyarakat - TAMBAHAN INI YANG MISSING
     */
    public function isMasyarakat()
    {
        return $this->role === 'masyarakat';
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
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
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
}
