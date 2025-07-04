<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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
        'status',
        'no_rt'
    ];

    protected $casts = [
        'jumlah_kk' => 'integer',
        'saldo' => 'decimal:2'
    ];

    /**
     * Relationship with RW
     */
    public function rw(): BelongsTo
    {
        return $this->belongsTo(Rw::class);
    }

    /**
     * Relationship with Desa through RW
     */
    public function desa(): HasOneThrough
    {
        return $this->hasOneThrough(Desa::class, Rw::class, 'id', 'id', 'rw_id', 'desa_id');
    }

    /**
     * Relationship with KK (One to Many)
     */
    public function kks(): HasMany
    {
        return $this->hasMany(Kk::class);
    }

    /**
     * Relationship with Kas (One to Many)
     */
    public function kas(): HasMany
    {
        return $this->hasMany(Kas::class);
    }

    /**
     * Update jumlah KK otomatis
     */
    public function updateJumlahKk(): void
    {
        $this->update(['jumlah_kk' => $this->kks()->count()]);
    }

    /**
     * Get total penduduk
     */
    public function getTotalPendudukAttribute(): int
    {
        return $this->kks()->withCount('penduduks')->get()->sum('penduduks_count');
    }

    /**
     * Get total kas terkumpul
     */
    public function getTotalKasTerkumpulAttribute(): float
    {
        return $this->kas()->where('status', 'lunas')->sum('jumlah');
    }

    /**
     * Get total kas outstanding
     */
    public function getTotalKasOutstandingAttribute(): float
    {
        return $this->kas()->whereIn('status', ['belum_bayar', 'terlambat'])->sum('jumlah');
    }

    /**
     * Get kas statistics
     */
    public function getKasStatsAttribute(): array
    {
        return [
            'total' => $this->kas()->count(),
            'lunas' => $this->kas()->where('status', 'lunas')->count(),
            'belum_bayar' => $this->kas()->where('status', 'belum_bayar')->count(),
            'terlambat' => $this->kas()->where('status', 'terlambat')->count(),
            'total_terkumpul' => $this->total_kas_terkumpul,
            'total_outstanding' => $this->total_kas_outstanding,
        ];
    }
}
