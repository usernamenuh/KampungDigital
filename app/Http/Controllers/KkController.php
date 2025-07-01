<?php

namespace App\Http\Controllers;

use App\Models\Kk;
use App\Models\Rt;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kk::with(['rt.rw', 'kepalaKeluarga', 'penduduks']);

        // Filter berdasarkan RT
        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search berdasarkan nomor KK atau alamat
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_kk', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $kks = $query->paginate(15);
        $rts = Rt::with('rw')->get();

        return view('kk.index', compact('kks', 'rts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rts = Rt::with('rw')->where('status', 'aktif')->get();
        return view('kk.create', compact('rts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_kk' => 'required|string|size:16|unique:kks,no_kk',
            'rt_id' => 'required|exists:rts,id',
            'alamat' => 'required|string|max:255',
            'tanggal_dibuat' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            Kk::create($request->all());
        });

        return redirect()->route('kk.index')
            ->with('success', 'Kartu Keluarga berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kk $kk)
    {
        $kk->load(['rt.rw', 'kepalaKeluarga', 'penduduks']);
        return view('kk.show', compact('kk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kk $kk)
    {
        $rts = Rt::with('rw')->where('status', 'aktif')->get();
        $penduduks = Penduduk::where('kk_id', $kk->id)->get();
        
        return view('kk.edit', compact('kk', 'rts', 'penduduks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kk $kk)
    {
        $request->validate([
            'no_kk' => 'required|string|size:16|unique:kks,no_kk,' . $kk->id,
            'rt_id' => 'required|exists:rts,id',
            'alamat' => 'required|string|max:255',
            'kepala_keluarga_id' => 'nullable|exists:penduduks,id',
            'status' => 'required|in:aktif,tidak_aktif',
            'tanggal_dibuat' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $kk) {
            $kk->update($request->all());
        });

        return redirect()->route('kk.index')
            ->with('success', 'Kartu Keluarga berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kk $kk)
    {
        DB::transaction(function () use ($kk) {
            $kk->delete();
        });

        return redirect()->route('kk.index')
            ->with('success', 'Kartu Keluarga berhasil dihapus.');
    }

    /**
     * Set kepala keluarga
     */
    public function setKepalaKeluarga(Request $request, Kk $kk)
    {
        $request->validate([
            'kepala_keluarga_id' => 'required|exists:penduduks,id'
        ]);

        $penduduk = Penduduk::findOrFail($request->kepala_keluarga_id);
        
        // Pastikan penduduk adalah anggota KK ini
        if ($penduduk->kk_id !== $kk->id) {
            return back()->with('error', 'Penduduk bukan anggota KK ini.');
        }

        DB::transaction(function () use ($kk, $penduduk) {
            // Update hubungan keluarga penduduk lama jika ada
            if ($kk->kepala_keluarga_id) {
                $kepalaLama = Penduduk::find($kk->kepala_keluarga_id);
                if ($kepalaLama && $kepalaLama->hubungan_keluarga === 'Kepala Keluarga') {
                    $kepalaLama->update(['hubungan_keluarga' => 'Lainnya']);
                }
            }

            // Set kepala keluarga baru
            $kk->update(['kepala_keluarga_id' => $penduduk->id]);
            $penduduk->update(['hubungan_keluarga' => 'Kepala Keluarga']);
        });

        return back()->with('success', 'Kepala keluarga berhasil ditetapkan.');
    }
}
