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

    // Method untuk mendapatkan total saldo RW + RT
    public function getTotalSaldoAttribute()
    {
        return $this->saldo + $this->rts->sum('saldo');
    }

    // Method untuk mendapatkan total KK
    public function getTotalKkAttribute()
    {
        return $this->rts->sum('jumlah_kk');
    }
}
