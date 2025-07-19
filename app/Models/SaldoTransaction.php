<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoTransaction extends Model
{
    use HasFactory;

    protected $table = 'saldo_transactions';

    protected $fillable = [
        'rt_id',
        'rw_id',
        'desa_id',
        'kas_id',
        'transaction_type',
        'amount',
        'previous_saldo',
        'new_saldo',
        'description',
        'processed_by',
        'processed_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'previous_saldo' => 'decimal:0',
        'new_saldo' => 'decimal:0',
        'processed_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function rt()
    {
        return $this->belongsTo(Rt::class, 'rt_id', 'id');
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class, 'rw_id', 'id');
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class, 'desa_id', 'id');
    }

    public function kas()
    {
        return $this->belongsTo(Kas::class, 'kas_id', 'id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'id');
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->translatedFormat('d F Y H:i') : '-';
    }
}
