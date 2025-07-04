<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasis';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'kategori',
        'data',
        'dibaca',
        'dibaca_pada',
    ];

    protected $casts = [
        'data' => 'array',
        'dibaca' => 'boolean',
        'dibaca_pada' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('dibaca', false);
    }

    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    // Accessors
    public function getTipeBadgeAttribute()
    {
        $badges = [
            'info' => 'badge-info',
            'warning' => 'badge-warning',
            'success' => 'badge-success',
            'error' => 'badge-danger',
        ];

        return $badges[$this->tipe] ?? 'badge-secondary';
    }

    public function getTipeIconAttribute()
    {
        $icons = [
            'info' => 'fas fa-info-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'success' => 'fas fa-check-circle',
            'error' => 'fas fa-times-circle',
        ];

        return $icons[$this->tipe] ?? 'fas fa-bell';
    }

    // Methods
    public function markAsRead()
    {
        $this->update([
            'dibaca' => true,
            'dibaca_pada' => now(),
        ]);

        return $this;
    }

    public static function createKasNotification($userId, $kasData)
    {
        return self::create([
            'user_id' => $userId,
            'judul' => 'Kas Baru - Minggu ke-' . $kasData['minggu_ke'],
            'pesan' => 'Kas RT sebesar ' . number_format($kasData['jumlah'], 0, ',', '.') . ' telah dibuat untuk minggu ke-' . $kasData['minggu_ke'],
            'tipe' => 'info',
            'kategori' => 'kas',
            'data' => $kasData,
        ]);
    }

    public static function createReminderNotification($userId, $kasData)
    {
        return self::create([
            'user_id' => $userId,
            'judul' => 'Pengingat Kas - Jatuh Tempo',
            'pesan' => 'Kas minggu ke-' . $kasData['minggu_ke'] . ' akan jatuh tempo pada ' . $kasData['tanggal_jatuh_tempo'],
            'tipe' => 'warning',
            'kategori' => 'reminder',
            'data' => $kasData,
        ]);
    }
}
