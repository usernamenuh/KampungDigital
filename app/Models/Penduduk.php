<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Penduduk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'kk_id',
        'user_id',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'pendidikan',
        'pekerjaan',
        'status_perkawinan',
        'hubungan_keluarga',
        'kewarganegaraan',
        'no_paspor',
        'tanggal_expired_paspor',
        'nama_ayah',
        'nama_ibu',
        'status_penduduk',
        'tanggal_pindah',
        'alamat_sebelumnya',
        'status',
        'tanggal_meninggal',
        'keterangan',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_expired_paspor' => 'date',
        'tanggal_pindah' => 'date',
        'tanggal_meninggal' => 'date',
    ];

    /**
     * Relasi dengan KK (Many to One)
     */
    public function kk()
    {
        return $this->belongsTo(Kk::class);
    }

    /**
     * Relasi dengan User (One to One)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi sebagai Kepala Keluarga (One to One)
     */
    public function kkAsKepala()
    {
        return $this->hasOne(Kk::class, 'kepala_keluarga_id');
    }

    /**
     * Get umur berdasarkan tanggal lahir
     */
    public function getUmurAttribute()
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }

    /**
     * Get tempat tanggal lahir
     */
    public function getTempatTanggalLahirAttribute()
    {
        return $this->tempat_lahir . ', ' . $this->tanggal_lahir->format('d-m-Y');
    }

    /**
     * Check if penduduk is kepala keluarga
     */
    public function isKepalaKeluarga()
    {
        return $this->hubungan_keluarga === 'Kepala Keluarga';
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk filter berdasarkan jenis kelamin
     */
    public function scopeJenisKelamin($query, $jenisKelamin)
    {
        return $query->where('jenis_kelamin', $jenisKelamin);
    }

    /**
     * Scope untuk filter berdasarkan umur
     */
    public function scopeUmur($query, $minUmur = null, $maxUmur = null)
    {
        if ($minUmur) {
            $query->whereDate('tanggal_lahir', '<=', Carbon::now()->subYears($minUmur));
        }
        if ($maxUmur) {
            $query->whereDate('tanggal_lahir', '>=', Carbon::now()->subYears($maxUmur));
        }
        return $query;
    }
}
