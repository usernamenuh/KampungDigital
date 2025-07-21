<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\SaldoTransaction;
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
        'minggu_ke',
        'tahun',
        'jumlah', // Standardized to 'jumlah' for the main amount
        'denda',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'metode_bayar',
        'bukti_bayar_file',
        'bukti_bayar_notes',
        'bukti_bayar_uploaded_at',
        'jumlah_dibayar',
        'status',
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
        'jumlah' => 'decimal:0', // Cast 'jumlah' as decimal
        'denda' => 'decimal:0',
        'jumlah_dibayar' => 'decimal:0',
    ];

    protected $appends = ['formatted_amount', 'formatted_denda', 'total_bayar', 'formatted_total_bayar'];

    // Define constants for status
    public const STATUS_BELUM_BAYAR = 'belum_bayar';
    public const STATUS_LUNAS = 'lunas';
    public const STATUS_MENUNGGU_KONFIRMASI = 'menunggu_konfirmasi';
    public const STATUS_TERLAMBAT = 'terlambat';
    public const STATUS_DITOLAK = 'ditolak';

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
            self::STATUS_BELUM_BAYAR => 'Belum Bayar',
            self::STATUS_LUNAS => 'Lunas',
            self::STATUS_TERLAMBAT => 'Terlambat',
            self::STATUS_MENUNGGU_KONFIRMASI => 'Menunggu Konfirmasi',
            self::STATUS_DITOLAK => 'Ditolak',
        ];

        return $statusMap[$this->status] ?? 'Status Tidak Dikenal';
    }

    public function getIsOverdueAttribute()
    {
        if ($this->status === self::STATUS_LUNAS) {
            return false;
        }

        return $this->tanggal_jatuh_tempo && $this->tanggal_jatuh_tempo->isPast();
    }

    public function getTanggalJatuhTempoFormattedAttribute()
    {
        return optional($this->tanggal_jatuh_tempo)->translatedFormat('d F Y') ?? '-';
    }

    public function getTanggalBayarFormattedAttribute()
    {
        return optional($this->tanggal_bayar)->translatedFormat('d F Y H:i') ?? '-';
    }

    /**
     * Accessor for total_bayar (jumlah + denda)
     */
    public function getTotalBayarAttribute()
    {
        return $this->jumlah + ($this->denda ?? 0);
    }

    public function getCanPayAttribute()
    {
        return in_array($this->status, [self::STATUS_BELUM_BAYAR, self::STATUS_TERLAMBAT, self::STATUS_DITOLAK]);
    }

    public function getStatusColorAttribute()
    {
        $colorMap = [
            self::STATUS_BELUM_BAYAR => 'yellow',
            self::STATUS_LUNAS => 'green',
            self::STATUS_TERLAMBAT => 'red',
            self::STATUS_MENUNGGU_KONFIRMASI => 'blue',
            self::STATUS_DITOLAK => 'red',
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
            'go-pay' => 'gopay',
            'shopeepay' => 'ShopeePay',
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
            'allo_bank' => 'allo_bank',
            'allo' => 'allo_bank',
        ];

        // Check if input contains bank name and map accordingly
        foreach ($methodMap as $key => $value) {
            if (strpos($this->metode_bayar, $key) !== false) {
                return $value;
            }
        }

        // Default fallback
        return 'bank_transfer';
    }

    public function getBuktiBayarUploadedAtFormattedAttribute()
    {
        return $this->bukti_bayar_uploaded_at ? $this->bukti_bayar_uploaded_at->format('d M Y H:i') : '-';
    }

    // Scopes
    public function scopeLunas($query)
    {
        return $query->where('status', self::STATUS_LUNAS);
    }

    public function scopeBelumBayar($query)
    {
        return $query->where('status', self::STATUS_BELUM_BAYAR);
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', self::STATUS_TERLAMBAT);
    }

    public function scopeMenungguKonfirmasi($query)
    {
        return $query->where('status', self::STATUS_MENUNGGU_KONFIRMASI);
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', self::STATUS_DITOLAK);
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
        return $query->where('tanggal_jatuh_tempo', '<', Carbon::now()->toDateString())
                     ->whereIn('status', [self::STATUS_BELUM_BAYAR, self::STATUS_MENUNGGU_KONFIRMASI, self::STATUS_DITOLAK]);
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

            // Ensure overdue status is updated before marking as paid
            $this->updateOverdueStatus();
            $this->refresh(); // Refresh to get the latest denda value

            // Only proceed if not already lunas
            if ($this->status !== self::STATUS_LUNAS) {
                $this->status = self::STATUS_LUNAS;
                $this->tanggal_bayar = Carbon::now();
                $this->jumlah_dibayar = $this->total_bayar; // Store total_bayar including denda
                $this->confirmed_by = $confirmedBy;
                $this->confirmed_at = Carbon::now();
                $this->confirmation_notes = $notes;
                $this->rejection_reason = null; // Clear rejection reason if confirmed
                $this->save();

                // Add saldo to RT
                if ($this->rt) {
                    $this->rt->addSaldo($this->jumlah_dibayar, 'Pemasukan Kas', $this->id);
                }
            }

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

            if ($this->status === self::STATUS_LUNAS) {
                // Remove saldo from RT
                if ($this->rt) {
                    $this->rt->removeSaldo($this->jumlah_dibayar, 'Pembatalan Kas', $this->id);
                }

                $this->status = self::STATUS_BELUM_BAYAR; // Revert to default status
                $this->tanggal_bayar = null;
                $this->metode_bayar = null;
                $this->bukti_bayar_file = null;
                $this->bukti_bayar_notes = null;
                $this->jumlah_dibayar = null;
                $this->confirmed_by = null;
                $this->confirmed_at = null;
                $this->confirmation_notes = null;
                $this->denda = 0; // Reset denda
                $this->rejection_reason = null; // Clear rejection reason
                $this->save();
            }

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
        if ($this->status === self::STATUS_LUNAS || !$this->is_overdue) {
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
        if ($this->is_overdue && $this->status === self::STATUS_BELUM_BAYAR) {
            $pengaturanKas = PengaturanKas::first();
            $dendaPerHari = $pengaturanKas ? $pengaturanKas->denda_per_hari : 0;
            $maxDenda = $pengaturanKas ? $pengaturanKas->maksimal_denda : 0;

            $daysLate = Carbon::now()->diffInDays($this->tanggal_jatuh_tempo);
            $calculatedDenda = $daysLate * $dendaPerHari;

            // Apply maximum denda if set
            if ($maxDenda > 0 && $calculatedDenda > $maxDenda) {
                $calculatedDenda = $maxDenda;
            }

            $this->denda = $calculatedDenda;
            $this->save();
        }
    }

    /**
     * Update status to terlambat if overdue
     */
    public function updateOverdueStatus()
    {
        if ($this->status === self::STATUS_BELUM_BAYAR || $this->status === self::STATUS_TERLAMBAT) {
            $today = Carbon::now()->startOfDay();
            $dueDate = Carbon::parse($this->tanggal_jatuh_tempo)->startOfDay();

            if ($today->greaterThan($dueDate)) {
                $pengaturanKas = PengaturanKas::first();
                $dendaPerHari = $pengaturanKas ? $pengaturanKas->denda_per_hari : 0;
                $maxDenda = $pengaturanKas ? $pengaturanKas->maksimal_denda : 0;

                $daysLate = $today->diffInDays($dueDate);
                $calculatedDenda = $daysLate * $dendaPerHari;

                // Apply maximum denda if set
                if ($maxDenda > 0 && $calculatedDenda > $maxDenda) {
                    $calculatedDenda = $maxDenda;
                }

                $this->denda = $calculatedDenda;
                $this->status = self::STATUS_TERLAMBAT;
                $this->save();
            } else {
                // If it's no longer overdue (e.g., due date changed or denda removed manually)
                if ($this->denda > 0 || $this->status === self::STATUS_TERLAMBAT) {
                    $this->denda = 0;
                    $this->status = self::STATUS_BELUM_BAYAR;
                    $this->save();
                }
            }
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
        return !in_array($this->status, [self::STATUS_LUNAS]);
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
            'allo_bank' => 'allo_bank',
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
            if ($kas->status === self::STATUS_LUNAS) {
                $kas->reversePayment(auth()->id(), 'Kas dihapus');
            }
        });

        // Update overdue status when kas is retrieved
        static::retrieved(function ($kas) {
            if ($kas->status === self::STATUS_BELUM_BAYAR && $kas->is_overdue) {
                $kas->updateOverdueStatus();
            }
        });
    }
}
