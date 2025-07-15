<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kas extends Model
{
  use HasFactory;

  protected $table = 'kas';
  protected $primaryKey = 'id'; // Corrected: Changed from 'kas_id' to 'id'
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
      'jumlah_dibayar',
      'metode_bayar',
      'bukti_bayar_file',
      'bukti_bayar_notes',
      'bukti_bayar_uploaded_at',
      'confirmed_by',
      'confirmed_at',
      'confirmation_notes',
      // Removed redundant fields: 'konfirmasi_oleh_user_id', 'tanggal_konfirmasi', 'catatan_konfirmasi',
      'denda',
  ];

  protected $casts = [
      'tanggal_jatuh_tempo' => 'date',
      'tanggal_bayar' => 'datetime',
      'bukti_bayar_uploaded_at' => 'datetime',
      'confirmed_at' => 'datetime',
      // Removed redundant field: 'tanggal_konfirmasi',
      'jumlah' => 'decimal:2', // Added cast for numerical fields
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
      switch ($this->metode_bayar) {
          case 'bank_transfer':
              return 'Transfer Bank';
          case 'e_wallet':
              return 'E-Wallet';
          case 'qr_code':
              return 'QR Code';
          case 'tunai':
              return 'Tunai';
          default:
              return '-';
      }
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
      return $this->jumlah + $this->denda;
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
          $pengaturan = PengaturanKas::first(); // Assuming global or first setting
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
}
