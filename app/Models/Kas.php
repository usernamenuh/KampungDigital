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
        'minggu_ke',
        'tahun',
        'jumlah',
        'denda',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'status',
        'metode_bayar',
        'bukti_bayar_file',
        'bukti_bayar_notes',
        'bukti_bayar_uploaded_at',
        'confirmed_by',
        'confirmed_at',
        'confirmation_notes',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
        'bukti_bayar_uploaded_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'jumlah' => 'decimal:2',
        'denda' => 'decimal:2',
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

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        $statusTexts = [
            'belum_bayar' => 'Belum Bayar',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'lunas' => 'Lunas',
            'terlambat' => 'Terlambat'
        ];

        return $statusTexts[$this->status] ?? $this->status;
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'belum_bayar' && 
               $this->tanggal_jatuh_tempo && 
               $this->tanggal_jatuh_tempo->isPast();
    }

    public function getTotalBayarAttribute()
    {
        return $this->jumlah + $this->denda;
    }

    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->total_bayar, 0, ',', '.');
    }

    public function getTanggalJatuhTempoFormattedAttribute()
    {
        return $this->tanggal_jatuh_tempo ? $this->tanggal_jatuh_tempo->format('d M Y') : '-';
    }

    public function getTanggalBayarFormattedAttribute()
    {
        return $this->tanggal_bayar ? $this->tanggal_bayar->format('d M Y') : '-';
    }

    public function getMetodeBayarFormattedAttribute()
    {
        $methods = [
            'tunai' => 'Tunai',
            'bank_transfer' => 'Transfer Bank',
            'e_wallet' => 'E-Wallet',
            'qr_code' => 'QR Code',
        ];
        return $this->metode_bayar ? ($methods[$this->metode_bayar] ?? ucfirst(str_replace('_', ' ', $this->metode_bayar))) : '-';
    }

    // Scopes
    public function scopeBelumBayar($query)
    {
        return $query->where('status', 'belum_bayar');
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', 'belum_bayar')
                    ->where('tanggal_jatuh_tempo', '<', Carbon::now());
    }

    public function scopeMenungguKonfirmasi($query)
    {
        return $query->where('status', 'menunggu_konfirmasi');
    }

    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    public function scopeForRt($query, $rtId)
    {
        return $query->where('rt_id', $rtId);
    }

    public function scopeForPenduduk($query, $pendudukId)
    {
        return $query->where('penduduk_id', $pendudukId);
    }

    // Methods
    public function hitungDenda()
    {
        if ($this->is_overdue && $this->status === 'belum_bayar') {
            $pengaturan = PengaturanKas::first();
            $persentaseDenda = $pengaturan ? $pengaturan->persentase_denda : 2.00;
            $this->denda = ($this->jumlah * $persentaseDenda) / 100;
            $this->status = 'terlambat';
            $this->save();
        }
    }

    public function markAsLunas($confirmedBy, $notes = null)
    {
        $this->update([
            'status' => 'lunas',
            'tanggal_bayar' => Carbon::now(),
            'confirmed_by' => $confirmedBy,
            'confirmed_at' => Carbon::now(),
            'confirmation_notes' => $notes,
        ]);
    }
}
