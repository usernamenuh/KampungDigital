<?php
// filepath: app/Models/Desa.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vermaysha\Territory\Models\Village;
use Vermaysha\Territory\Models\District;
use Vermaysha\Territory\Models\Regency;
use Vermaysha\Territory\Models\Province;

class Desa extends Model
{
    protected $table = 'desas';
   protected $fillable = [
    'alamat', 'kode_pos', 'saldo', 'status', 'no_telpon', 'gmail',
    'province_code', 'regency_code', 'district_code', 'village_code', 'foto'
];

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_code', 'village_code');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_code', 'district_code');
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'regency_code', 'regency_code');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }
    public function rws()
    {
        return $this->hasMany(Rw::class, 'desa_id', 'id');
    }
}