<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegRegency extends Model
{
    use HasFactory;

    protected $table = 'reg_regencies';

    protected $fillable = [
        'id',
        'province_id',
        'name'
    ];

    public $timestamps = false;

    // Relationship dengan Province
    public function province()
    {
        return $this->belongsTo(RegProvince::class, 'province_id', 'id');
    }

    // Relationship dengan Districts
    public function districts()
    {
        return $this->hasMany(RegDistrict::class, 'regency_id', 'id');
    }

    // Relationship dengan Villages melalui Districts
    public function villages()
    {
        return $this->hasManyThrough(RegVillage::class, RegDistrict::class, 'regency_id', 'district_id', 'id', 'id');
    }
}
