<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInfo extends Model
{
    use HasFactory;

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

    // Relationships
    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    // Accessors
    public function getHasBankTransferAttribute()
    {
        return !empty($this->bank_name) && !empty($this->bank_account_number);
    }

    public function getHasEWalletAttribute()
    {
        return !empty($this->dana_number) || !empty($this->gopay_number) || 
               !empty($this->ovo_number) || !empty($this->shopeepay_number);
    }

    public function getHasQrCodeAttribute()
    {
        return !empty($this->qr_code_path);
    }

    public function getAvailableMethodsAttribute()
    {
        $methods = [];
        
        if ($this->has_bank_transfer) {
            $methods[] = 'bank_transfer';
        }
        
        if ($this->has_e_wallet) {
            $methods[] = 'e_wallet';
        }
        
        if ($this->has_qr_code) {
            $methods[] = 'qr_code';
        }
        
        return $methods;
    }

    public function getEWalletListAttribute()
    {
        $wallets = [];
        
        if ($this->dana_number) {
            $wallets['dana'] = $this->dana_number;
        }
        
        if ($this->gopay_number) {
            $wallets['gopay'] = $this->gopay_number;
        }
        
        if ($this->ovo_number) {
            $wallets['ovo'] = $this->ovo_number;
        }
        
        if ($this->shopeepay_number) {
            $wallets['shopeepay'] = $this->shopeepay_number;
        }
        
        return $wallets;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForRt($query, $rtId)
    {
        return $query->where('rt_id', $rtId);
    }
}
