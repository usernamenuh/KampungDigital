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
        ];
    }

    /**
     * Relasi dengan Penduduk (One to One)
     */
    public function penduduk()
    {
        return $this->hasOne(Penduduk::class);
    }

    /**
     * Check user roles
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isKades()
    {
        return $this->role === 'kades';
    }

    public function isRW()
    {
        return $this->role === 'rw';
    }

    public function isRT()
    {
        return $this->role === 'rt';
    }

    public function isMasyarakat()
    {
        return $this->role === 'masyarakat';
    }
}
