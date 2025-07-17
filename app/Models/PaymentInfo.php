<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'rt_id',
        'bank_name', // Single bank name
        'bank_account_number', // Single bank account number
        'bank_account_name', // Single bank account name
        'dana_number',
        'dana_account_name',
        'gopay_number',
        'gopay_account_name',
        'ovo_number',
        'ovo_account_name',
        'shopeepay_number',
        'shopeepay_account_name',
        'qr_code_path',
        'qr_code_description',
        'qr_code_account_name',
        'payment_notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Define relationship to RT
    public function rt()
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    // Accessor for qr_code_url
    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code_path ? Storage::url($this->qr_code_path) : null;
    }

    // Accessor for e_wallet_list (to maintain existing display logic)
    public function getEWalletListAttribute()
    {
        $list = [];
        if ($this->dana_number) {
            $list['dana'] = ['number' => $this->dana_number, 'name' => $this->dana_account_name];
        }
        if ($this->gopay_number) {
            $list['gopay'] = ['number' => $this->gopay_number, 'name' => $this->gopay_account_name];
        }
        if ($this->ovo_number) {
            $list['ovo'] = ['number' => $this->ovo_number, 'name' => $this->ovo_account_name];
        }
        if ($this->shopeepay_number) {
            $list['shopeepay'] = ['number' => $this->shopeepay_number, 'name' => $this->shopeepay_account_name];
        }
        return $list;
    }

    // Accessor for has_bank_transfer
    public function getHasBankTransferAttribute()
    {
        return !empty($this->bank_name) && !empty($this->bank_account_number) && !empty($this->bank_account_name);
    }

    // Accessor for has_e_wallet
    public function getHasEWalletAttribute()
    {
        return $this->dana_number || $this->gopay_number || $this->ovo_number || $this->shopeepay_number;
    }

    // Accessor for has_qr_code
    public function getHasQrCodeAttribute()
    {
        return !empty($this->qr_code_path);
    }


    // Accessor for rt_no
    public function getRtNoAttribute()
    {
        return $this->rt ? $this->rt->no_rt : 'N/A';
    }

    // Accessor for rw_no
    public function getRwNoAttribute()
    {
        return $this->rt && $this->rt->rw ? $this->rt->rw->no_rw : 'N/A';
    }
}
