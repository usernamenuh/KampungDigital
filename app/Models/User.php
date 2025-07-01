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

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if user has valid penduduk data (for masyarakat role)
     */
    public function hasValidPendudukData()
    {
        if ($this->role !== 'masyarakat') {
            return true; // Non-masyarakat users don't need penduduk data
        }

        return $this->penduduk && $this->penduduk->status === 'aktif';
    }

    /**
     * Scope untuk user aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope untuk user dengan data penduduk valid
     */
    public function scopeWithValidPenduduk($query)
    {
        return $query->whereHas('penduduk', function ($q) {
            $q->where('status', 'aktif');
        });
    }

    /**
     * Get user's full name from penduduk if available
     */
    public function getFullNameAttribute()
    {
        return $this->penduduk ? $this->penduduk->nama_lengkap : $this->name;
    }

    /**
     * Get user's NIK from penduduk if available
     */
    public function getNikAttribute()
    {
        return $this->penduduk ? $this->penduduk->nik : null;
    }
}
