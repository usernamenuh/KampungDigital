<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\Rw;
use App\Models\Rt;
use App\Models\Penduduk;
use Illuminate\Http\Request;

class RtRwController extends Controller
{
    public function index()
    {
        $desas = Desa::where('status', 'aktif')->get();
        $rws = Rw::with(['desa', 'rts', 'ketua'])->get();
        $rts = Rt::with(['rw.desa', 'ketua'])->get();
        
        // Ambil semua penduduk yang sudah menjabat kepala desa, ketua rw, atau ketua rt
        $pendudukSudahMenjabat = collect();
        $pendudukSudahMenjabat = $pendudukSudahMenjabat
            ->merge(Desa::whereNotNull('kepala_desa_id')->pluck('kepala_desa_id'))
            ->merge(Rw::whereNotNull('ketua_rw_id')->pluck('ketua_rw_id'))
            ->merge(Rt::whereNotNull('ketua_rt_id')->pluck('ketua_rt_id'))
            ->filter()->unique();
        
        // Untuk dropdown, sertakan penduduk yang belum menjabat
        $penduduks = Penduduk::whereNotIn('id', $pendudukSudahMenjabat)->get();
        
        return view('rt-rw.index', compact('desas', 'rws', 'rts', 'penduduks'));
    }

    public function getAvailablePenduduk($excludeId = null, $type = null, $currentId = null)
    {
        $pendudukSudahMenjabat = collect();
        $pendudukSudahMenjabat = $pendudukSudahMenjabat
            ->merge(Desa::whereNotNull('kepala_desa_id')->pluck('kepala_desa_id'))
            ->merge(Rw::whereNotNull('ketua_rw_id')->pluck('ketua_rw_id'))
            ->merge(Rt::whereNotNull('ketua_rt_id')->pluck('ketua_rt_id'))
            ->filter()->unique();

        // Kecuali jika ada ID yang dikecualikan (untuk edit)
        if ($excludeId) {
            $pendudukSudahMenjabat = $pendudukSudahMenjabat->filter(fn($id) => $id != $excludeId);
        }

        return Penduduk::whereNotIn('id', $pendudukSudahMenjabat)->get();
    }
}
