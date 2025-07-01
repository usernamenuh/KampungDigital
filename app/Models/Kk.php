<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kk extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_kk',
        'rt_id',
        'alamat',
        'status',
        'tanggal_dibuat',
        'keterangan',
        'kepala_keluarga_id',
    ];

    protected $casts = [
        'tanggal_dibuat' => 'date',
    ];

    /**
     * Relasi dengan RT (Many to One)
     */
    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    /**
     * Relasi dengan Penduduk (One to Many)
     */
    public function penduduks()
    {
        return $this->hasMany(Penduduk::class);
    }

    /**
     * Relasi dengan Kepala Keluarga (One to One)
     */
    public function kepalaKeluarga()
    {
        return $this->belongsTo(Penduduk::class, 'kepala_keluarga_id');
    }

    /**
     * Get jumlah anggota keluarga
     */
    public function getJumlahAnggotaAttribute()
    {
        return $this->penduduks()->count();
    }

    /**
     * Get alamat lengkap
     */
    public function getAlamatLengkapAttribute()
    {
        return $this->alamat . ', RT ' . $this->rt->nama_rt . ' RW ' . $this->rt->rw->nama_rw;
    }

    /**
     * Boot method untuk update jumlah KK di RT
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($kk) {
            $kk->rt->updateJumlahKk();
        });

        static::deleted(function ($kk) {
            $kk->rt->updateJumlahKk();
        });
    }
}
