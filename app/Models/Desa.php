<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    use HasFactory;

    protected $fillable = [
        'alamat',
        'kode_pos',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'foto',
        'saldo',
        'status',
        'no_telpon',
        'gmail',
        'kepala_desa_id'
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship dengan Penduduk untuk Kepala Desa
    public function kepala()
    {
        return $this->belongsTo(Penduduk::class, 'kepala_desa_id');
    }

    // Territory relationships using reg_wilayah models
    public function province()
    {
        return $this->belongsTo(RegProvince::class, 'province_id');
    }

    public function regency()
    {
        return $this->belongsTo(RegRegency::class, 'regency_id');
    }

    public function district()
    {
        return $this->belongsTo(RegDistrict::class, 'district_id');
    }

    public function village()
    {
        return $this->belongsTo(RegVillage::class, 'village_id');
    }
}
