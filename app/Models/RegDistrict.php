<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegDistrict extends Model
{
    use HasFactory;

    protected $table = 'reg_districts';

    protected $fillable = [
        'id',
        'regency_id',
        'name'
    ];

    public $timestamps = false;

    // Relationship dengan Regency
    public function regency()
    {
        return $this->belongsTo(RegRegency::class, 'regency_id', 'id');
    }

    // Relationship dengan Province melalui Regency
    public function province()
    {
        return $this->hasOneThrough(RegProvince::class, RegRegency::class, 'id', 'id', 'regency_id', 'province_id');
    }

    // Relationship dengan Villages
    public function villages()
    {
        return $this->hasMany(RegVillage::class, 'district_id', 'id');
    }
}
