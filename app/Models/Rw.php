<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rw extends Model
{
    use HasFactory;

    protected $fillable = [
        'desa_id',
        'nama_rw',
        'alamat',
        'no_telpon',
        'saldo',
        'no_rw',
        'status',
        'ketua_rw'
    ];

    protected $casts = [
        'saldo' => 'decimal:2'
    ];

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function rts(): HasMany
    {
        return $this->hasMany(Rt::class);
    }

    public function getTotalSaldoAttribute()
    {
        return $this->saldo + $this->rts->sum('saldo');
    }
    /**
     * Get total RT count
     */
    public function getTotalRtAttribute()
    {
        return $this->rts()->count();
    }

    /**
     * Get total KK count
     */
    public function getTotalKkAttribute()
    {
        return $this->rts()->withCount('kks')->get()->sum('kks_count');
    }
}
