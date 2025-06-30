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
}
