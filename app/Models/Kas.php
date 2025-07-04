<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kas extends Model
{
    use HasFactory;

    protected $table = 'kas';

    protected $fillable = [
        'penduduk_id',
        'rt_id',
        'rw_id',
        'minggu_ke',
        'tahun',
        'jumlah',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'status',
        'metode_bayar',
        'bukti_bayar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar' => 'datetime',
        'jumlah' => 'decimal:2',
    ];

    // Relationships
    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    // Get user through penduduk relationship
    public function user()
    {
        return $this->hasOneThrough(User::class, Penduduk::class, 'id', 'id', 'penduduk_id', 'user_id');
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('penduduk', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeBelumBayar($query)
    {
        return $query->where('status', 'belum_bayar');
    }

    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', 'belum_bayar')
                    ->where('tanggal_jatuh_tempo', '<', now());
    }

    public function scopeCurrentYear($query)
    {
        return $query->where('tahun', now()->year);
    }

    // Accessors
    public function getIsOverdueAttribute()
    {
        return $this->status === 'belum_bayar' && 
               $this->tanggal_jatuh_tempo < now();
    }

    public function getStatusTextAttribute()
    {
        if ($this->status === 'lunas') {
            return 'Lunas';
        }
        
        if ($this->is_overdue) {
            return 'Terlambat';
        }
        
        return 'Belum Bayar';
    }

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'lunas':
                return 'green';
            case 'belum_bayar':
                return $this->is_overdue ? 'red' : 'yellow';
            default:
                return 'gray';
        }
    }
}
