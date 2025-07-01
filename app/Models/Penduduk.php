<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
     * Get status badge attribute
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'aktif' => '<span class="badge bg-success">Aktif</span>',
            'tidak_aktif' => '<span class="badge bg-warning">Tidak Aktif</span>',
            'meninggal' => '<span class="badge bg-dark">Meninggal</span>',
            'pindah' => '<span class="badge bg-info">Pindah</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
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
        if ($minUmur !== null && $maxUmur !== null) {
            return $query->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN ? AND ?', [$minUmur, $maxUmur]);
        } elseif ($minUmur !== null) {
            return $query->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= ?', [$minUmur]);
        } elseif ($maxUmur !== null) {
            return $query->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) <= ?', [$maxUmur]);
        }

        return $query;
    }

    /**
     * Boot method untuk handle events
     */
    protected static function boot()
    {
        parent::boot();

        // Event ketika penduduk akan dihapus
        static::deleting(function ($penduduk) {
            // Jika ada user terkait, nonaktifkan
            if ($penduduk->user) {
                $penduduk->user->update(['status' => 'inactive']);
                
                Log::info('User deactivated due to penduduk deletion', [
                    'user_id' => $penduduk->user->id,
                    'penduduk_id' => $penduduk->id,
                    'penduduk_nik' => $penduduk->nik
                ]);
            }
        });

        // Event ketika penduduk diupdate
        static::updated(function ($penduduk) {
            // Jika status berubah dan ada user terkait
            if ($penduduk->isDirty('status') && $penduduk->user) {
                $newUserStatus = in_array($penduduk->status, ['tidak_aktif', 'meninggal', 'pindah']) ? 'inactive' : 'active';
                $penduduk->user->update(['status' => $newUserStatus]);
                
                Log::info('User status updated due to penduduk status change', [
                    'user_id' => $penduduk->user->id,
                    'penduduk_id' => $penduduk->id,
                    'penduduk_nik' => $penduduk->nik,
                    'new_status' => $newUserStatus
                ]);
            }
        });
    }
}
