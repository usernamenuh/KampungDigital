<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanKas extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_kas';

    protected $fillable = [
        'jumlah_kas_mingguan',
        'persentase_denda',
        'batas_hari_pembayaran',
        'hari_peringatan',
        'auto_generate_weekly',
        'pesan_peringatan',
    ];

    protected $casts = [
        'jumlah_kas_mingguan' => 'decimal:2',
        'persentase_denda' => 'decimal:2',
        'auto_generate_weekly' => 'boolean',
    ];

    // Accessors
    public function getFormattedJumlahKasAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_kas_mingguan, 0, ',', '.');
    }

    public function getDefaultPesanPeringatanAttribute()
    {
        return $this->pesan_peringatan ?: 
            'Halo {nama}, kas RT minggu ke-{minggu} tahun {tahun} sebesar {jumlah} akan jatuh tempo pada {tanggal}. Mohon segera lakukan pembayaran.';
    }

    // Methods
    public static function getDefault()
    {
        return self::first() ?: self::create([
            'jumlah_kas_mingguan' => 10000,
            'persentase_denda' => 2.00,
            'batas_hari_pembayaran' => 7,
            'hari_peringatan' => 1,
            'auto_generate_weekly' => false,
        ]);
    }
}
