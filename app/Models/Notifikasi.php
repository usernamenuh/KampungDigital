<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasis';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe', // info, success, warning, error
        'kategori', // kas, user, system, payment, etc.
        'data', // JSON string for additional data
        'dibaca', // boolean
        'dibaca_pada', // datetime
    ];

    protected $casts = [
        'data' => 'array',
        'dibaca' => 'boolean',
        'dibaca_pada' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function kas()
    {
        return $this->belongsTo(Kas::class, 'kas_id', 'id');
    }

    // Accessors
    public function getTipeTextAttribute()
    {
        $tipeMap = [
            'info' => 'Informasi',
            'success' => 'Berhasil',
            'warning' => 'Peringatan',
            'error' => 'Error',
        ];
        return $tipeMap[$this->tipe] ?? 'Informasi';
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->translatedFormat('d F Y H:i');
    }

    // Static methods for easy creation
    public static function createKasNotification($userId, $kasData)
    {
        if (!$userId) {
            Log::warning('Attempted to create kas notification for null user_id', ['kas_data' => $kasData]);
            return null;
        }

        $judul = 'Pembaruan Status Kas';
        $pesan = "Status kas minggu ke-{$kasData['minggu_ke']} sebesar Rp " . number_format($kasData['jumlah'], 0, ',', '.') . " telah diperbarui menjadi " . (new Kas(['status' => $kasData['status']]))->status_text . ".";
        $tipe = 'info';

        if ($kasData['status'] === 'lunas') {
            $judul = 'Kas Telah Lunas';
            $pesan = "Pembayaran kas minggu ke-{$kasData['minggu_ke']} sebesar Rp " . number_format($kasData['jumlah'], 0, ',', '.') . " telah dikonfirmasi lunas.";
            $tipe = 'success';
        } elseif ($kasData['status'] === 'ditolak') {
            $judul = 'Pembayaran Kas Ditolak';
            $pesan = "Pembayaran kas minggu ke-{$kasData['minggu_ke']} sebesar Rp " . number_format($kasData['jumlah'], 0, ',', '.') . " ditolak. Silakan periksa kembali bukti pembayaran Anda.";
            $tipe = 'error';
        } elseif ($kasData['status'] === 'terlambat') {
            $judul = 'Tagihan Kas Terlambat';
            $pesan = "Tagihan kas minggu ke-{$kasData['minggu_ke']} sebesar Rp " . number_format($kasData['jumlah'], 0, ',', '.') . " sudah terlambat. Mohon segera lakukan pembayaran.";
            $tipe = 'warning';
        } elseif ($kasData['status'] === 'menunggu_konfirmasi') {
            $judul = 'Pembayaran Menunggu Konfirmasi';
            $pesan = "Pembayaran kas minggu ke-{$kasData['minggu_ke']} sebesar Rp " . number_format($kasData['jumlah'], 0, ',', '.') . " sedang menunggu konfirmasi.";
            $tipe = 'info';
        }

        return self::create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'kategori' => 'kas',
            'data' => json_encode($kasData),
            'dibaca' => false,
        ]);
    }

    public static function createSystemNotification($userId, $judul, $pesan, $tipe = 'info', $data = [])
    {
        return self::create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'kategori' => 'system',
            'data' => json_encode($data),
            'dibaca' => false,
        ]);
    }

    // Mark as read
    public function markAsRead()
    {
        if (!$this->dibaca) {
            $this->update([
                'dibaca' => true,
                'dibaca_pada' => now(),
            ]);
        }
    }
}
