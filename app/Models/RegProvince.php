<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RegRegency;

class RegProvince extends Model
{
    use HasFactory;

    protected $table = 'reg_provinces';

    protected $fillable = [
        'id',
        'name'
    ];

    public $timestamps = false;

    // Relationship dengan Regencies
    public function regencies()
    {
        return $this->hasMany(RegRegency::class, 'province_id', 'id');
    }

    // Relationship dengan Districts melalui Regencies
    public function districts()
    {
        return $this->hasManyThrough(RegDistrict::class, RegRegency::class, 'province_id', 'regency_id', 'id', 'id');
    }

    // Relationship dengan Villages melalui Districts
    public function villages()
    {
        return $this->hasManyThrough(RegVillage::class, RegDistrict::class, 'regency_id', 'district_id', 'id', 'id')
                    ->join('reg_regencies', 'reg_regencies.id', '=', 'reg_districts.regency_id')
                    ->where('reg_regencies.province_id', $this->id);
    }
}
