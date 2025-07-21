<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegRegency;
use App\Models\RegDistrict;
use App\Models\RegVillage;

class WilayahApiController extends Controller
{
    /**
     * Get regencies (kabupaten/kota) by province ID.
     *
     * @param  string  $province_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegencies($province_id)
    {
        try {
            $regencies = RegRegency::where('province_id', $province_id)->orderBy('name')->get();
            return response()->json(['success' => true, 'data' => $regencies]);
        } catch (\Exception $e) {
            \Log::error("Error fetching regencies for province_id {$province_id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data kabupaten/kota'], 500);
        }
    }

    /**
     * Get districts (kecamatan) by province ID and regency ID.
     *
     * @param  string  $province_id
     * @param  string  $regency_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDistricts($province_id, $regency_id)
    {
        try {
            // Although province_id is passed in the route, filtering directly by regency_id is sufficient
            // if regency_id is unique across provinces. We keep province_id in the route for hierarchical consistency.
            $districts = RegDistrict::where('regency_id', $regency_id)->orderBy('name')->get();
            return response()->json(['success' => true, 'data' => $districts]);
        } catch (\Exception $e) {
            \Log::error("Error fetching districts for regency_id {$regency_id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data kecamatan'], 500);
        }
    }

    /**
     * Get villages (desa) by province ID, regency ID, and district ID.
     *
     * @param  string  $province_id
     * @param  string  $regency_id
     * @param  string  $district_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVillages($province_id, $regency_id, $district_id)
    {
        try {
            // Filtering directly by district_id is sufficient if district_id is unique across regencies.
            $villages = RegVillage::where('district_id', $district_id)->orderBy('name')->get();
            return response()->json(['success' => true, 'data' => $villages]);
        } catch (\Exception $e) {
            \Log::error("Error fetching villages for district_id {$district_id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data desa'], 500);
        }
    }
}
