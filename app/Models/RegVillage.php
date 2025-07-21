<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegVillage extends Model
{
    use HasFactory;

    protected $table = 'reg_villages';

    protected $fillable = [
        'id',
        'district_id',
        'name'
    ];

    public $timestamps = false;

    // Relationship dengan District
    public function district()
    {
        return $this->belongsTo(RegDistrict::class, 'district_id', 'id');
    }

    // Relationship dengan Regency melalui District
    public function regency()
    {
        return $this->hasOneThrough(RegRegency::class, RegDistrict::class, 'id', 'id', 'district_id', 'regency_id');
    }

    // Relationship dengan Province melalui District dan Regency
    public function province()
    {
        return $this->hasOneThrough(RegProvince::class, RegRegency::class, 'id', 'id', 'district_id', 'province_id')
                    ->join('reg_districts', 'reg_districts.regency_id', '=', 'reg_regencies.id')
                    ->where('reg_districts.id', $this->district_id);
    }
}
