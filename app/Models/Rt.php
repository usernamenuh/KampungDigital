<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rt extends Model
{
    use HasFactory;

    protected $fillable = [
        'rw_id',
        'nama_rt',
        'alamat',
        'ketua_rt',
        'no_telpon',
        'jumlah_kk',
        'saldo',
        'status'
    ];

    protected $casts = [
        'jumlah_kk' => 'integer',
        'saldo' => 'decimal:2'
    ];

    public function rw(): BelongsTo
    {
        return $this->belongsTo(Rw::class);
    }

    public function desa()
    {
        return $this->hasOneThrough(Desa::class, Rw::class, 'id', 'id', 'rw_id', 'desa_id');
    }

    /**
     * Relasi dengan KK (One to Many)
     */
    public function kks()
    {
        return $this->hasMany(Kk::class);
    }

    /**
     * Update jumlah KK otomatis
     */
    public function updateJumlahKk()
    {
        $this->update(['jumlah_kk' => $this->kks()->count()]);
    }

    /**
     * Get total penduduk
     */
    public function getTotalPendudukAttribute()
    {
        return $this->kks()->withCount('penduduks')->get()->sum('penduduks_count');
    }
}
