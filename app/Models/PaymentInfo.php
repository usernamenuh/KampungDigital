<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Import Storage facade

class PaymentInfo extends Model
{
  use HasFactory;

  protected $table = 'payment_infos';
  
  protected $fillable = [
      'rt_id',
      'bank_name',
      'bank_account_number',
      'bank_account_name',
      'dana_number',
      'gopay_number',
      'ovo_number',
      'shopeepay_number',
      'qr_code_path',
      'qr_code_description',
      'payment_notes',
      'is_active',
  ];

  protected $casts = [
      'is_active' => 'boolean',
  ];

  /**
   * Relationship with RT
   */
  public function rt()
  {
      return $this->belongsTo(Rt::class, 'rt_id', 'id');
  }

  /**
   * Get active payment info for specific RT
   */
  public static function getActiveForRt($rtId)
  {
      return self::where('rt_id', $rtId)
                 ->where('is_active', true)
                 ->first();
  }

  /**
   * Check if has bank transfer option
   */
  public function getHasBankTransferAttribute()
  {
      return !empty($this->bank_name) && !empty($this->bank_account_number);
  }

  /**
   * Check if has e-wallet options
   */
  public function getHasEWalletAttribute()
  {
      return !empty($this->dana_number) || 
             !empty($this->gopay_number) || 
             !empty($this->ovo_number) || 
             !empty($this->shopeepay_number);
  }

  /**
   * Get e-wallet list as an associative array
   */
  public function getEWalletListAttribute()
  {
      return array_filter([
          'dana' => $this->dana_number,
          'gopay' => $this->gopay_number,
          'ovo' => $this->ovo_number,
          'shopeepay' => $this->shopeepay_number,
      ]);
  }

  /**
   * Check if has QR code
   */
  public function getHasQrCodeAttribute()
  {
      return !empty($this->qr_code_path);
  }

  /**
   * Get full QR code URL
   */
  public function getQrCodeUrlAttribute()
  {
      return $this->qr_code_path ? Storage::url($this->qr_code_path) : null;
  }
}
