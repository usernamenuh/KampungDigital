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
  protected $primaryKey = 'id';
  public $incrementing = true;
  protected $keyType = 'int';

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
      'pekerjaan', // Corrected from 'jenis_pekerjaan'
      'golongan_darah',
      'status_perkawinan',
      'hubungan_keluarga', // Corrected from 'status_hubungan_keluarga'
      'kewarganegaraan',
      'no_paspor',
      'no_kitap',
      'nama_ayah',
      'nama_ibu',
      'status', // 'aktif', 'nonaktif', 'meninggal', 'pindah'
      'foto', // Added to fillable
      'keterangan', // Added to fillable
  ];

  protected $casts = [
      'tanggal_lahir' => 'date',
      'tanggal_expired_paspor' => 'date',
      'tanggal_pindah' => 'date',
      'tanggal_meninggal' => 'date',
      'tanggal_perkawinan' => 'date',
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
      return $this->belongsTo(Kk::class, 'kk_id', 'id');
  }

  /**
   * Relasi dengan RT (One to One) - Melalui KK
   */
  public function rt()
  {
      return $this->hasOneThrough(Rt::class, Kk::class, 'id', 'id', 'kk_id', 'rt_id');
  }

  /**
   * Relasi dengan RW (One to One) - Melalui KK dan RT
   */
  public function rw()
  {
      return $this->hasOneThrough(Rw::class, Kk::class, 'id', 'id', 'kk_id', 'rw_id');
  }

  /**
   * Relasi dengan User (One to One)
   */
  public function user()
  {
      return $this->belongsTo(User::class, 'user_id', 'id');
  }

  /**
   * Relasi dengan Kas (One to Many)
   */
  public function kas()
  {
      return $this->hasMany(Kas::class, 'penduduk_id', 'id');
  }

  /**
   * Relasi untuk mengecek apakah penduduk ini adalah kepala keluarga dari suatu KK
   */
  public function kkAsKepala()
  {
      return $this->hasOne(Kk::class, 'kepala_keluarga_id', 'id');
  }

  // Accessors
  public function getUmurAttribute()
  {
      return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
  }

  public function getTempatTanggalLahirAttribute()
  {
      return $this->tempat_lahir . ', ' . ($this->tanggal_lahir ? $this->tanggal_lahir->format('d-m-Y') : '-');
  }

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
   * Check if penduduk is kepala keluarga (using the correct field)
   */
  public function isKepalaKeluarga()
  {
      return $this->hubungan_keluarga === 'Kepala Keluarga';
  }

  // Scopes
  public function scopeAktif($query)
  {
      return $query->where('status', 'aktif');
  }

  public function scopeJenisKelamin($query, $jenisKelamin)
  {
      return $query->where('jenis_kelamin', $jenisKelamin);
  }

  public function scopeUmur($query, $minUmur = null, $maxUmur = null)
  {
      if ($minUmur !== null) {
          $maxDate = Carbon::now()->subYears($minUmur)->endOfDay();
          $query->where('tanggal_lahir', '<=', $maxDate);
      }
      
      if ($maxUmur !== null) {
          $minDate = Carbon::now()->subYears($maxUmur + 1)->startOfDay();
          $query->where('tanggal_lahir', '>=', $minDate);
      }
      
      return $query;
  }

  public function scopeByRt($query, $rtId)
  {
      return $query->whereHas('kk.rt', function ($q) use ($rtId) {
          $q->where('id', $rtId);
      });
  }

  public function scopeByRw($query, $rwId)
  {
      return $query->whereHas('kk.rw', function ($q) use ($rwId) {
          $q->where('id', $rwId);
      });
  }

  // Relationships for leadership roles (assuming these are direct relations from Penduduk to leadership models)
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

  // Helper methods with null safety
  public function getNamaLengkapSafeAttribute()
  {
      return $this->nama_lengkap ?? 'Data tidak tersedia';
  }

  public function getNikSafeAttribute()
  {
      return $this->nik ?? '-';
  }

  public function getRtRwInfoAttribute()
  {
      if ($this->kk && $this->kk->rt && $this->kk->rt->rw) {
          $info = "RT " . ($this->kk->rt->no_rt ?? '-') . " / RW " . ($this->kk->rt->rw->no_rw ?? '-');
          return $info;
      }
      return 'Data tidak tersedia';
  }

  public function getHasActiveUserAttribute()
  {
      return $this->user && $this->user->status === 'active';
  }

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

  // Static methods for dropdown options
  public static function getJenisKelaminOptions()
  {
      return [
          'L' => 'Laki-laki',
          'P' => 'Perempuan'
      ];
  }

  public static function getAgamaOptions()
  {
      return [
          'Islam' => 'Islam',
          'Kristen' => 'Kristen',
          'Katolik' => 'Katolik',
          'Hindu' => 'Hindu',
          'Buddha' => 'Buddha',
          'Khonghucu' => 'Khonghucu',
          'Lainnya' => 'Lainnya'
      ];
  }

  public static function getStatusPerkawinanOptions()
  {
      return [
          'Belum Kawin' => 'Belum Kawin',
          'Kawin' => 'Kawin',
          'Cerai Hidup' => 'Cerai Hidup',
          'Cerai Mati' => 'Cerai Mati'
      ];
  }

  public static function getHubunganKeluargaOptions()
  {
      return [
          'Kepala Keluarga' => 'Kepala Keluarga',
          'Istri' => 'Istri',
          'Anak' => 'Anak',
          'Menantu' => 'Menantu',
          'Cucu' => 'Cucu',
          'Orangtua' => 'Orangtua',
          'Mertua' => 'Mertua',
          'Famili Lain' => 'Famili Lain',
          'Pembantu' => 'Pembantu',
          'Lainnya' => 'Lainnya'
      ];
  }

  public static function getKewarganegaraanOptions()
  {
      return [
          'WNI' => 'Warga Negara Indonesia',
          'WNA' => 'Warga Negara Asing'
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

  // Accessors for formatted text
  public function getJenisKelaminTextAttribute()
  {
      return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
  }

  public function getStatusTextAttribute()
  {
      $statusMap = [
          'aktif' => 'Aktif',
          'tidak_aktif' => 'Tidak Aktif',
          'meninggal' => 'Meninggal',
          'pindah' => 'Pindah',
      ];
      return $statusMap[$this->status] ?? 'Tidak Diketahui';
  }

  public function getTanggalLahirFormattedAttribute()
  {
      return $this->tanggal_lahir ? $this->tanggal_lahir->translatedFormat('d F Y') : '-';
  }

  public function getTanggalPerkawinanFormattedAttribute()
  {
      return $this->tanggal_perkawinan ? $this->tanggal_perkawinan->translatedFormat('d F Y') : '-';
  }

  /**
   * Helper methods with null safety
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
      
      if ($this->kk && $this->kk->alamat) {
          $address[] = $this->kk->alamat;
      }
      
      if ($this->kk && $this->kk->rt && $this->kk->rt->rw) {
          $address[] = "RT " . $this->kk->rt->no_rt;
          $address[] = "RW " . $this->kk->rt->rw->no_rw;
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
