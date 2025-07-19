<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kas extends Model
{
    use HasFactory;

    protected $table = 'kas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'penduduk_id',
        'rt_id',
        'rw_id',
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
        'keterangan',
        'confirmed_by',
        'confirmed_at',
        'confirmation_notes',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar' => 'datetime',
        'bukti_bayar_uploaded_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'jumlah' => 'decimal:0',
        'denda' => 'decimal:0',
    ];

    // Relationships
    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'penduduk_id', 'id');
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class, 'rt_id', 'id');
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class, 'rw_id', 'id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by', 'id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by', 'id');
    }

    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class, 'kas_id');
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        $statusMap = [
            'belum_bayar' => 'Belum Bayar',
            'lunas' => 'Lunas',
            'terlambat' => 'Terlambat',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'ditolak' => 'Ditolak',
        ];

        return $statusMap[$this->status] ?? 'Status Tidak Dikenal';
    }

    public function getIsOverdueAttribute()
    {
        if ($this->status === 'lunas') {
            return false;
        }

        return $this->tanggal_jatuh_tempo && $this->tanggal_jatuh_tempo->isPast();
    }

    public function getTanggalJatuhTempoFormattedAttribute()
    {
        return $this->tanggal_jatuh_tempo ? $this->tanggal_jatuh_tempo->translatedFormat('d F Y') : '-';
    }

    public function getTanggalBayarFormattedAttribute()
    {
        return $this->tanggal_bayar ? $this->tanggal_bayar->translatedFormat('d F Y H:i') : '-';
    }

    public function getTotalBayarAttribute()
    {
        return $this->jumlah + ($this->denda ?? 0);
    }

    public function getCanPayAttribute()
    {
        return in_array($this->status, ['belum_bayar', 'terlambat', 'ditolak']);
    }

    public function getStatusColorAttribute()
    {
        $colorMap = [
            'belum_bayar' => 'yellow',
            'lunas' => 'green',
            'terlambat' => 'red',
            'menunggu_konfirmasi' => 'blue',
            'ditolak' => 'red',
        ];

        return $colorMap[$this->status] ?? 'gray';
    }

    public function getMetodeBayarFormattedAttribute()
    {
        $methodMap = [
            'tunai' => 'Tunai',
            'bank_transfer' => 'Transfer Bank',
            'e_wallet' => 'E-Wallet',
            'qr_code' => 'QR Code',
            'dana' => 'DANA',
            'ovo' => 'OVO',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'bca' => 'Bank BCA',
            'bni' => 'Bank BNI',
            'bri' => 'Bank BRI',
            'mandiri' => 'Bank Mandiri',
            'bsi' => 'Bank BSI',
            'cimb' => 'CIMB Niaga',
            'danamon' => 'Bank Danamon',
            'permata' => 'PermataBank',
            'mega' => 'Bank Mega',
            'btn' => 'Bank BTN',
            'panin' => 'Bank Panin',
            'maybank' => 'Maybank Indonesia',
            'btpn' => 'Bank BTPN',
            'commonwealth' => 'Bank Commonwealth',
            'uob' => 'Bank UOB Indonesia',
            'sinarmas' => 'Bank Sinarmas',
            'bukopin' => 'Bank Bukopin',
            'jago' => 'Bank Jago',
            'seabank' => 'SeaBank',
            'neo_commerce' => 'Bank Neo Commerce',
            'allo_bank' => 'Allo Bank',
        ];

        return $methodMap[$this->metode_bayar] ?? ucfirst(str_replace('_', ' ', $this->metode_bayar));
    }

    public function getBuktiBayarUploadedAtFormattedAttribute()
    {
        return $this->bukti_bayar_uploaded_at ? $this->bukti_bayar_uploaded_at->format('d M Y H:i') : '-';
    }

    // Scopes
    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    public function scopeBelumBayar($query)
    {
        return $query->where('status', 'belum_bayar');
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', 'terlambat');
    }

    public function scopeMenungguKonfirmasi($query)
    {
        return $query->where('status', 'menunggu_konfirmasi');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('tahun', $year);
    }

    public function scopeByWeek($query, $week)
    {
        return $query->where('minggu_ke', $week);
    }

    public function scopeByRt($query, $rtId)
    {
        return $query->where('rt_id', $rtId);
    }

    public function scopeByRw($query, $rwId)
    {
        return $query->where('rw_id', $rwId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('tanggal_jatuh_tempo', '<', now())
                     ->whereNotIn('status', ['lunas']);
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
    /**
     * Mark kas as paid (lunas) - UPDATED: NO LONGER AUTOMATICALLY UPDATES RT SALDO
     */
    public function markAsLunas($confirmedBy = null, $notes = null)
    {
        try {
            DB::beginTransaction();

            Log::info('Starting markAsLunas process', [
                'kas_id' => $this->id,
                'current_status' => $this->status,
                'amount' => $this->total_bayar,
                'rt_id' => $this->rt_id,
                'confirmed_by' => $confirmedBy
            ]);

            // Check if already lunas
            if ($this->status === 'lunas') {
                Log::warning('Kas already lunas', ['kas_id' => $this->id]);
                DB::rollBack();
                return false;
            }

            // Update kas status only - NO SALDO UPDATE
            $this->update([
                'status' => 'lunas',
                'tanggal_bayar' => now(),
                'confirmed_by' => $confirmedBy,
                'confirmed_at' => now(),
                'confirmation_notes' => $notes,
            ]);

            // Create notification
            Notifikasi::createKasNotification(
                $this->penduduk->user_id ?? null,
                [
                    'kas_id' => $this->id,
                    'minggu_ke' => $this->minggu_ke,
                    'jumlah' => $this->total_bayar,
                    'status' => 'lunas'
                ]
            );

            Log::info('Kas status updated to lunas (no saldo update)', ['kas_id' => $this->id]);

            DB::commit();
            Log::info('markAsLunas completed successfully', ['kas_id' => $this->id]);
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in markAsLunas', [
                'kas_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Reverse payment (from lunas to other status) - UPDATED: NO SALDO REVERSAL
     */
    public function reversePayment($reversedBy = null, $notes = null)
    {
        try {
            DB::beginTransaction();

            Log::info('Starting reversePayment process', [
                'kas_id' => $this->id,
                'current_status' => $this->status,
                'amount' => $this->total_bayar,
                'rt_id' => $this->rt_id,
                'reversed_by' => $reversedBy
            ]);

            if ($this->status !== 'lunas') {
                Log::warning('Kas not lunas, cannot reverse', ['kas_id' => $this->id, 'status' => $this->status]);
                DB::rollBack();
                return false;
            }

            // Update kas status only - NO SALDO REVERSAL
            $this->update([
                'status' => 'belum_bayar',
                'tanggal_bayar' => null,
                'confirmed_by' => null,
                'confirmed_at' => null,
                'confirmation_notes' => $notes,
            ]);

            DB::commit();
            Log::info('reversePayment completed successfully (no saldo reversal)', ['kas_id' => $this->id]);
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in reversePayment', [
                'kas_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Calculate and apply late fee (denda)
     */
    public function calculateLateFee()
    {
        if ($this->status === 'lunas' || !$this->is_overdue) {
            return 0;
        }

        $pengaturan = PengaturanKas::first();
        $persentaseDenda = $pengaturan ? $pengaturan->persentase_denda : 2.00;
        
        $calculatedFee = ($this->jumlah * $persentaseDenda) / 100;
        $this->update(['denda' => $calculatedFee]);

        return $calculatedFee;
    }

    /**
     * Calculate denda based on days late
     */
    public function hitungDenda()
    {
        if ($this->is_overdue && $this->status === 'belum_bayar') {
            $pengaturan = PengaturanKas::first();
            $persentaseDenda = $pengaturan ? $pengaturan->persentase_denda : 2.00;
            $this->denda = ($this->jumlah * $persentaseDenda) / 100;
            $this->save();
        }
    }

    /**
     * Update status to terlambat if overdue
     */
    public function updateOverdueStatus()
    {
        if ($this->status === 'belum_bayar' && $this->is_overdue) {
            $this->update(['status' => 'terlambat']);
            $this->calculateLateFee();
        }
    }

    /**
     * Get payment history for this kas
     */
    public function getPaymentHistory()
    {
        return SaldoTransaction::where('kas_id', $this->id)
                              ->orderBy('created_at', 'desc')
                              ->get();
    }

    /**
     * Check if kas can be edited
     */
    public function canBeEdited()
    {
        return !in_array($this->status, ['lunas']);
    }

    /**
     * Check if kas can be deleted
     */
    public function canBeDeleted()
    {
        return true; // Admin can delete any kas, but with proper saldo reversal
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    /**
     * Get formatted total payment with currency
     */
    public function getFormattedTotalBayarAttribute()
    {
        return 'Rp ' . number_format($this->total_bayar, 0, ',', '.');
    }

    /**
     * Get formatted late fee with currency
     */
    public function getFormattedDendaAttribute()
    {
        return 'Rp ' . number_format($this->denda ?? 0, 0, ',', '.');
    }

    /**
     * Helper method to normalize payment method input to enum value
     */
    public static function normalizePaymentMethod($input)
    {
        $input = strtolower(trim($input));
        
        // Direct mapping for common variations
        $methodMap = [
            'cash' => 'tunai',
            'tunai' => 'tunai',
            'tunai (bayar langsung)' => 'tunai',
            'bank transfer' => 'bank_transfer',
            'transfer bank' => 'bank_transfer',
            'e-wallet' => 'e_wallet',
            'e_wallet' => 'e_wallet',
            'qr code' => 'qr_code',
            'qr_code' => 'qr_code',
            'qris' => 'qr_code',
            
            // E-wallets
            'dana' => 'dana',
            'ovo' => 'ovo',
            'gopay' => 'gopay',
            'go-pay' => 'gopay',
            'shopeepay' => 'shopeepay',
            'shopee pay' => 'shopeepay',
            
            // Banks - map bank names to enum values
            'bank central asia (bca)' => 'bca',
            'bca' => 'bca',
            'bank mandiri' => 'mandiri',
            'mandiri' => 'mandiri',
            'bank rakyat indonesia (bri)' => 'bri',
            'bri' => 'bri',
            'bank negara indonesia (bni)' => 'bni',
            'bni' => 'bni',
            'bank syariah indonesia (bsi)' => 'bsi',
            'bsi' => 'bsi',
            'cimb niaga' => 'cimb',
            'cimb' => 'cimb',
            'bank danamon' => 'danamon',
            'danamon' => 'danamon',
            'permatabank' => 'permata',
            'permata' => 'permata',
            'bank mega' => 'mega',
            'mega' => 'mega',
            'bank btn' => 'btn',
            'btn' => 'btn',
            'bank panin' => 'panin',
            'panin' => 'panin',
            'bank maybank indonesia' => 'maybank',
            'maybank' => 'maybank',
            'bank tabungan pensiunan nasional (btpn)' => 'btpn',
            'btpn' => 'btpn',
            'bank commonwealth' => 'commonwealth',
            'commonwealth' => 'commonwealth',
            'bank uob indonesia' => 'uob',
            'uob' => 'uob',
            'bank sinarmas' => 'sinarmas',
            'sinarmas' => 'sinarmas',
            'bank bukopin' => 'bukopin',
            'bukopin' => 'bukopin',
            'bank jago' => 'jago',
            'jago' => 'jago',
            'seabank' => 'seabank',
            'bank neo commerce (bnc)' => 'neo_commerce',
            'neo_commerce' => 'neo_commerce',
            'allo bank' => 'allo_bank',
            'allo' => 'allo_bank',
        ];

        // Check if input contains bank name and map accordingly
        foreach ($methodMap as $key => $value) {
            if (strpos($input, $key) !== false) {
                return $value;
            }
        }

        // Default fallback
        return 'bank_transfer';
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // When kas is being deleted, reverse payment if it was lunas
        static::deleting(function ($kas) {
            if ($kas->status === 'lunas') {
                $kas->reversePayment(auth()->id(), 'Kas dihapus');
            }
        });

        // Update overdue status when kas is retrieved
        static::retrieved(function ($kas) {
            if ($kas->status === 'belum_bayar' && $kas->is_overdue) {
                $kas->updateOverdueStatus();
            }
        });
    }
}
