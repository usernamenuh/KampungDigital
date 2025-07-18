<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Penduduk extends Model
{
    use HasFactory;

    protected $table = 'penduduks';

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
        'status_hidup',
        'alamat',
        'rt_id',
        'rw_id',
        'status', // 'aktif', 'nonaktif', 'meninggal', 'pindah'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_expired_paspor' => 'date',
        'tanggal_pindah' => 'date',
        'tanggal_meninggal' => 'date',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-sync user status when penduduk status changes
        static::updated(function ($penduduk) {
            if ($penduduk->user && $penduduk->isDirty('status')) {
                $userStatus = in_array($penduduk->status, ['tidak_aktif', 'meninggal', 'pindah']) ? 'inactive' : 'active';
                
                $penduduk->user->update(['status' => $userStatus]);
                
                Log::info('User status synced with penduduk', [
                    'penduduk_id' => $penduduk->id,
                    'user_id' => $penduduk->user_id,
                    'penduduk_status' => $penduduk->status,
                    'user_status' => $userStatus
                ]);
            }
        });

        // Auto-deactivate user when penduduk is deleted
        static::deleting(function ($penduduk) {
            if ($penduduk->user) {
                $penduduk->user->update(['status' => 'inactive']);
                
                Log::info('User deactivated due to penduduk deletion', [
                    'penduduk_id' => $penduduk->id,
                    'user_id' => $penduduk->user_id
                ]);
            }
        });
    }

    /**
     * Relasi dengan KK (Many to One)
     */
    public function kk()
    {
        return $this->belongsTo(Kk::class, 'kk_id');
    }

    /**
     * Relasi dengan RT (One to One)
     */
    public function rt()
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    /**
     * Relasi dengan RW (One to One)
     */
    public function rw()
    {
        return $this->belongsTo(Rw::class, 'rw_id');
    }

    /**
     * Relasi dengan User (One to One)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi dengan Kas (One to Many) - DITAMBAHKAN
     */
    public function kas()
    {
        return $this->hasMany(Kas::class, 'penduduk_id');
    }

    /**
     * Get umur berdasarkan tanggal lahir
     */
    public function getUmurAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }

    /**
     * Get tempat tanggal lahir
     */
    public function getTempatTanggalLahirAttribute()
    {
        return $this->tempat_lahir . ', ' . ($this->tanggal_lahir ? $this->tanggal_lahir->format('d-m-Y') : '-');
    }

    /**
     * Get status badge for UI
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
     * Check if penduduk is kepala keluarga
     */
    public function isKepalaKeluarga()
    {
        return $this->status_hidup === 'Kepala Keluarga';
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
        if ($minUmur !== null) {
            $maxDate = Carbon::now()->subYears($minUmur)->endOfYear();
            $query->where('tanggal_lahir', '<=', $maxDate);
        }
        
        if ($maxUmur !== null) {
            $minDate = Carbon::now()->subYears($maxUmur + 1)->startOfYear();
            $query->where('tanggal_lahir', '>=', $minDate);
        }
        
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan RT
     */
    public function scopeByRt($query, $rtId)
    {
        return $query->where('rt_id', $rtId);
    }

    /**
     * Scope untuk filter berdasarkan RW
     */
    public function scopeByRw($query, $rwId)
    {
        return $query->where('rw_id', $rwId);
    }

    // Relationships for leadership roles
    public function rwKetua()
    {
        return $this->hasOne(Rw::class, 'ketua_rw_id');
    }

    public function rtKetua()
    {
        return $this->hasOne(Rt::class, 'ketua_rt_id');
    }

    public function kepalaDesa()
    {
        return $this->hasOne(Desa::class, 'kepala_desa_id');
    }

    /**
     * Helper method untuk mendapatkan nama lengkap dengan null check - DITAMBAHKAN
     */
    public function getNamaLengkapSafeAttribute()
    {
        return $this->nama_lengkap ?? 'Data tidak tersedia';
    }

    /**
     * Helper method untuk mendapatkan NIK dengan null check - DITAMBAHKAN
     */
    public function getNikSafeAttribute()
    {
        return $this->nik ?? '-';
    }

    /**
     * Helper method untuk mendapatkan info RT/RW dengan null check - DITAMBAHKAN
     */
    public function getRtRwInfoAttribute()
    {
        if ($this->rt && $this->rw) {
            $info = "RT " . ($this->rt->no_rt ?? '-') . " / RW " . ($this->rw->no_rw ?? '-');
            return $info;
        }
        return 'Data tidak tersedia';
    }

    /**
     * Check if penduduk has active user account - DITAMBAHKAN
     */
    public function getHasActiveUserAttribute()
    {
        return $this->user && $this->user->status === 'active';
    }

    /**
     * Get kas statistics for this penduduk - DITAMBAHKAN
     */
    public function getKasStatsAttribute()
    {
        if (!$this->relationLoaded('kas')) {
            $this->load('kas');
        }

        return [
            'total' => $this->kas->count(),
            'lunas' => $this->kas->where('status', 'lunas')->count(),
            'belum_bayar' => $this->kas->where('status', 'belum_bayar')->count(),
            'terlambat' => $this->kas->where('status', 'terlambat')->count(),
            'ditolak' => $this->kas->where('status', 'ditolak')->count(),
            'menunggu_konfirmasi' => $this->kas->where('status', 'menunggu_konfirmasi')->count(),
        ];
    }

    /**
     * Static methods untuk dropdown options
     */
    public static function getJenisKelaminOptions()
    {
        return [
            'laki-laki' => 'Laki-laki',
            'perempuan' => 'Perempuan'
        ];
    }

    public static function getAgamaOptions()
    {
        return [
            'islam' => 'Islam',
            'kristen' => 'Kristen',
            'katolik' => 'Katolik',
            'hindu' => 'Hindu',
            'buddha' => 'Buddha',
            'konghucu' => 'Konghucu'
        ];
    }

    public static function getStatusPerkawinanOptions()
    {
        return [
            'belum_kawin' => 'Belum Kawin',
            'kawin' => 'Kawin',
            'cerai_hidup' => 'Cerai Hidup',
            'cerai_mati' => 'Cerai Mati'
        ];
    }

    public static function getHubunganKeluargaOptions()
    {
        return [
            'kepala_keluarga' => 'Kepala Keluarga',
            'istri' => 'Istri',
            'anak' => 'Anak',
            'menantu' => 'Menantu',
            'cucu' => 'Cucu',
            'orangtua' => 'Orangtua',
            'mertua' => 'Mertua',
            'famili_lain' => 'Famili Lain',
            'pembantu' => 'Pembantu',
            'lainnya' => 'Lainnya'
        ];
    }

    public static function getStatusOptions()
    {
        return [
            'aktif' => 'Aktif',
            'tidak_aktif' => 'Tidak Aktif',
            'pindah' => 'Pindah',
            'meninggal' => 'Meninggal'
        ];
    }

    public static function getDropdownOptions()
    {
        return self::with(['kk.rt', 'kk.rt.rw'])
            ->aktif()
            ->orderBy('nama_lengkap')
            ->get();
    }

    /**
     * Helper methods dengan null safety
     */
    public function getUmurSafe()
    {
        if (!$this->tanggal_lahir) {
            return null;
        }
        return $this->tanggal_lahir->age;
    }

    public function getFullAddress()
    {
        $address = [];
        
        if ($this->alamat) {
            $address[] = $this->alamat;
        }
        
        if ($this->rt && $this->rw) {
            $address[] = "RT " . $this->rt->no_rt;
            $address[] = "RW " . $this->rw->no_rw;
        }
        
        return implode(', ', array_filter($address));
    }

    public function canHaveUser()
    {
        // Hanya yang berusia 17+ yang bisa punya akun
        $umur = $this->getUmurSafe();
        return $umur && $umur >= 17;
    }
}
