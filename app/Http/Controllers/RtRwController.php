<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\Rw;
use App\Models\Rt;
use Illuminate\Http\Request;

class RtRwController extends Controller
{
    public function index()
    {
        $desas = Desa::where('status', 'aktif')->get();
        $rws = Rw::with(['desa', 'rts'])->get();
        $rts = Rt::with(['rw.desa'])->get();
        
        return view('rt-rw.index', compact('desas', 'rws', 'rts'));
    }
}
