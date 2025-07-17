<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'jumlah',
        'tanggal_jatuh_tempo',
        'status',
        'tanggal_bayar',
        'jumlah',
        'metode_bayar',
        'bukti_bayar_file',
        'bukti_bayar_notes',
        'bukti_bayar_uploaded_at',
        'confirmed_by',
        'confirmed_at',
        'confirmation_notes',
        'denda',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar' => 'datetime',
        'bukti_bayar_uploaded_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'jumlah' => 'decimal:2',
        'denda' => 'decimal:2',
        'jumlah_dibayar' => 'decimal:2',
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

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by', 'id');
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'belum_bayar':
                return 'Belum Bayar';
            case 'lunas':
                return 'Lunas';
            case 'menunggu_konfirmasi':
                return 'Menunggu Konfirmasi';
            case 'terlambat':
                return 'Terlambat';
            case 'ditolak':
                return 'Ditolak';
            default:
                return 'Tidak Diketahui';
        }
    }

    public function getTanggalJatuhTempoFormattedAttribute()
    {
        return $this->tanggal_jatuh_tempo ? Carbon::parse($this->tanggal_jatuh_tempo)->translatedFormat('d F Y') : '-';
    }

    public function getTanggalBayarFormattedAttribute()
    {
        return $this->tanggal_bayar ? Carbon::parse($this->tanggal_bayar)->translatedFormat('d F Y H:i') : '-';
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

    public function getCanPayAttribute()
    {
        return in_array($this->status, ['belum_bayar', 'terlambat']);
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'belum_bayar' && 
               $this->tanggal_jatuh_tempo && 
               $this->tanggal_jatuh_tempo->isPast();
    }

    public function getTotalBayarAttribute()
    {
        return $this->jumlah + ($this->denda ?? 0);
    }

    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->total_bayar, 0, ',', '.');
    }

    public function getBuktiBayarUploadedAtFormattedAttribute()
    {
        return $this->bukti_bayar_uploaded_at ? Carbon::parse($this->bukti_bayar_uploaded_at)->format('d M Y H:i') : '-';
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
}
