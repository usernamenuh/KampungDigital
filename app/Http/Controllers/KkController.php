<?php

namespace App\Http\Controllers;

use App\Models\Kk;
use App\Models\Rt;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            'no_kk' => [
                'required',
                'string',
                'size:16',
                'unique:kks,no_kk',
                'regex:/^[0-9]{16}$/'
            ],
            'rt_id' => 'required|exists:rts,id',
            'alamat' => 'required|string|max:255|min:5',
            'tanggal_dibuat' => 'required|date|before_or_equal:today',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'no_kk.required' => 'Nomor KK wajib diisi',
            'no_kk.size' => 'Nomor KK harus 16 digit',
            'no_kk.unique' => 'Nomor KK sudah terdaftar',
            'no_kk.regex' => 'Nomor KK harus berupa angka 16 digit',
            'rt_id.required' => 'RT/RW wajib dipilih',
            'rt_id.exists' => 'RT/RW tidak valid',
            'alamat.required' => 'Alamat wajib diisi',
            'alamat.min' => 'Alamat minimal 5 karakter',
            'tanggal_dibuat.required' => 'Tanggal dibuat wajib diisi',
            'tanggal_dibuat.before_or_equal' => 'Tanggal dibuat tidak boleh lebih dari hari ini',
        ]);

        try {
            DB::transaction(function () use ($request) {
                Kk::create($request->all());
            });

            return redirect()->route('kk.index')
                ->with('success', 'Kartu Keluarga berhasil ditambahkan!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
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
            'no_kk' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]{16}$/',
                Rule::unique('kks')->ignore($kk->id)
            ],
            'rt_id' => 'required|exists:rts,id',
            'alamat' => 'required|string|max:255|min:5',
            'kepala_keluarga_id' => 'nullable|exists:penduduks,id',
            'status' => 'required|in:aktif,tidak_aktif',
            'tanggal_dibuat' => 'required|date|before_or_equal:today',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'no_kk.required' => 'Nomor KK wajib diisi',
            'no_kk.size' => 'Nomor KK harus 16 digit',
            'no_kk.unique' => 'Nomor KK sudah terdaftar',
            'no_kk.regex' => 'Nomor KK harus berupa angka 16 digit',
            'rt_id.required' => 'RT/RW wajib dipilih',
            'alamat.required' => 'Alamat wajib diisi',
            'alamat.min' => 'Alamat minimal 5 karakter',
            'tanggal_dibuat.before_or_equal' => 'Tanggal dibuat tidak boleh lebih dari hari ini',
        ]);

        // Validasi kepala keluarga harus anggota KK ini
        if ($request->kepala_keluarga_id) {
            $penduduk = Penduduk::find($request->kepala_keluarga_id);
            if (!$penduduk || $penduduk->kk_id !== $kk->id) {
                return back()->withInput()->with('error', 'Kepala keluarga harus merupakan anggota KK ini.');
            }
        }

        try {
            DB::transaction(function () use ($request, $kk) {
                $kk->update($request->all());
            });

            return redirect()->route('kk.index')
                ->with('success', 'Kartu Keluarga berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kk $kk)
    {
        try {
            // Check if KK has members
            $memberCount = $kk->penduduks()->count();
            if ($memberCount > 0) {
                return redirect()->route('kk.index')
                    ->with('error', "Tidak dapat menghapus Kartu Keluarga yang masih memiliki {$memberCount} anggota keluarga. Silakan hapus atau pindahkan semua anggota keluarga terlebih dahulu.");
            }

            DB::transaction(function () use ($kk) {
                $kk->delete();
            });

            return redirect()->route('kk.index')
                ->with('success', "Kartu Keluarga No. {$kk->no_kk} berhasil dihapus!");

        } catch (\Exception $e) {
            return redirect()->route('kk.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Set kepala keluarga
     */
    public function setKepalaKeluarga(Request $request, Kk $kk)
    {
        $request->validate([
            'kepala_keluarga_id' => 'required|exists:penduduks,id'
        ], [
            'kepala_keluarga_id.required' => 'Kepala keluarga wajib dipilih',
            'kepala_keluarga_id.exists' => 'Penduduk tidak ditemukan',
        ]);

        $penduduk = Penduduk::findOrFail($request->kepala_keluarga_id);
        
        // Pastikan penduduk adalah anggota KK ini
        if ($penduduk->kk_id !== $kk->id) {
            return back()->with('error', 'Penduduk bukan anggota KK ini.');
        }

        try {
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

            return back()->with('success', 'Kepala keluarga berhasil ditetapkan!');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menetapkan kepala keluarga: ' . $e->getMessage());
        }
    }
}
