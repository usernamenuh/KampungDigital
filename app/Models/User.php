<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_activity',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_activity' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is currently online
     *
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->last_activity && $this->last_activity->gt(now()->subMinutes(5));
    }

    /**
     * Get online users
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function online()
    {
        return static::where('last_activity', '>=', now()->subMinutes(5));
    }

    /**
     * Get users by role
     *
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function byRole(string $role)
    {
        return static::where('role', $role);
    }

    /**
     * Get online users count by role
     *
     * @return array
     */
    public static function getOnlineCountByRole(): array
    {
        $onlineThreshold = now()->subMinutes(5);
        
        return [
            'admin' => static::where('role', 'admin')->where('last_activity', '>=', $onlineThreshold)->count(),
            'kades' => static::where('role', 'kades')->where('last_activity', '>=', $onlineThreshold)->count(),
            'rw' => static::where('role', 'rw')->where('last_activity', '>=', $onlineThreshold)->count(),
            'rt' => static::where('role', 'rt')->where('last_activity', '>=', $onlineThreshold)->count(),
            'masyarakat' => static::where('role', 'masyarakat')->where('last_activity', '>=', $onlineThreshold)->count(),
        ];
    }

    /**
     * Scope for active users
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('last_activity', '>=', now()->subMinutes(5));
    }
}
