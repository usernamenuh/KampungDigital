<?php

namespace App\Http\Controllers;

use App\Models\Penduduk;
use App\Models\Kk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PendudukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Penduduk::with(['kk.rt.rw', 'user']);

        // Filter berdasarkan KK
        if ($request->filled('kk_id')) {
            $query->where('kk_id', $request->kk_id);
        }

        // Filter berdasarkan jenis kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search berdasarkan NIK atau nama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $penduduks = $query->paginate(15);
        $kks = Kk::with('rt.rw')->get();

        return view('penduduk.index', compact('penduduks', 'kks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kks = Kk::with('rt.rw')->where('status', 'aktif')->get();
        $users = User::whereDoesntHave('penduduk')->get();
        
        return view('penduduk.create', compact('kks', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16|unique:penduduks,nik',
            'kk_id' => 'required|exists:kks,id',
            'user_id' => 'nullable|exists:users,id|unique:penduduks,user_id',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Khonghucu,Lainnya',
            'pendidikan' => 'nullable|string',
            'pekerjaan' => 'nullable|string|max:100',
            'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'hubungan_keluarga' => 'required|string',
            'kewarganegaraan' => 'required|string|max:50',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nik.required' => 'NIK harus diisi',
            'nik.size' => 'NIK harus 16 karakter',
            'nik.unique' => 'NIK sudah terdaftar',
            'kk_id.required' => 'Kartu Keluarga harus dipilih',
            'kk_id.exists' => 'Kartu Keluarga tidak valid',
            'user_id.exists' => 'User tidak valid',
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'jenis_kelamin.required' => 'Jenis kelamin harus dipilih',
            'tempat_lahir.required' => 'Tempat lahir harus diisi',
            'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
            'agama.required' => 'Agama harus dipilih',
            'status_perkawinan.required' => 'Status perkawinan harus dipilih',
            'hubungan_keluarga.required' => 'Hubungan keluarga harus diisi',
            'kewarganegaraan.required' => 'Kewarganegaraan harus diisi',
            'foto.image' => 'Foto harus berupa gambar',
            'foto.mimes' => 'Foto harus berformat jpeg, png, jpg',
            'foto.max' => 'Foto maksimal 2MB',
        ]);

        DB::transaction(function () use ($request) {
            $data = $request->all();

            // Handle foto upload
            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('penduduk', 'public');
            }

            $penduduk = Penduduk::create($data);

            // Auto-assign sebagai kepala keluarga jika hubungan keluarga adalah "Kepala Keluarga"
            if ($request->hubungan_keluarga === 'Kepala Keluarga') {
                $kk = Kk::find($request->kk_id);
                
                // Jika KK belum memiliki kepala keluarga, set penduduk ini sebagai kepala keluarga
                if (!$kk->kepala_keluarga_id) {
                    $kk->update(['kepala_keluarga_id' => $penduduk->id]);
                } else {
                    // Jika sudah ada kepala keluarga, update yang lama menjadi "Lainnya"
                    $kepalaLama = Penduduk::find($kk->kepala_keluarga_id);
                    if ($kepalaLama) {
                        $kepalaLama->update(['hubungan_keluarga' => 'Lainnya']);
                    }
                    $kk->update(['kepala_keluarga_id' => $penduduk->id]);
                }
            }
        });

        return redirect()->route('penduduk.index')
            ->with('success', 'Data penduduk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Penduduk $penduduk)
    {
        $penduduk->load(['kk.rt.rw', 'user', 'kkAsKepala']);
        return view('penduduk.show', compact('penduduk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penduduk $penduduk)
    {
        $kks = Kk::with('rt.rw')->where('status', 'aktif')->get();
        $users = User::whereDoesntHave('penduduk')
                    ->orWhere('id', $penduduk->user_id)
                    ->get();
        
        return view('penduduk.edit', compact('penduduk', 'kks', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penduduk $penduduk)
    {
        $request->validate([
            'nik' => 'required|string|size:16|unique:penduduks,nik,' . $penduduk->id,
            'kk_id' => 'required|exists:kks,id',
            'user_id' => 'nullable|exists:users,id|unique:penduduks,user_id,' . $penduduk->id,
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Khonghucu,Lainnya',
            'status' => 'required|in:aktif,tidak_aktif,meninggal,pindah',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::transaction(function () use ($request, $penduduk) {
            $data = $request->all();

            // Handle foto upload
            if ($request->hasFile('foto')) {
                // Delete old foto
                if ($penduduk->foto) {
                    Storage::disk('public')->delete($penduduk->foto);
                }
                $data['foto'] = $request->file('foto')->store('penduduk', 'public');
            }

            $penduduk->update($data);
        });

        return redirect()->route('penduduk.index')
            ->with('success', 'Data penduduk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penduduk $penduduk)
    {
        DB::transaction(function () use ($penduduk) {
            // Delete foto if exists
            if ($penduduk->foto) {
                Storage::disk('public')->delete($penduduk->foto);
            }

            // Update KK jika penduduk adalah kepala keluarga
            if ($penduduk->kkAsKepala) {
                $penduduk->kkAsKepala->update(['kepala_keluarga_id' => null]);
            }

            $penduduk->delete();
        });

        return redirect()->route('penduduk.index')
            ->with('success', 'Data penduduk berhasil dihapus.');
    }

    /**
     * Get statistics
     */
    public function statistics()
    {
        $stats = [
            'total_penduduk' => Penduduk::count(),
            'laki_laki' => Penduduk::where('jenis_kelamin', 'L')->count(),
            'perempuan' => Penduduk::where('jenis_kelamin', 'P')->count(),
            'anak_anak' => Penduduk::umur(null, 17)->count(),
            'dewasa' => Penduduk::umur(18, 59)->count(),
            'lansia' => Penduduk::umur(60)->count(),
            'belum_kawin' => Penduduk::where('status_perkawinan', 'Belum Kawin')->count(),
            'kawin' => Penduduk::where('status_perkawinan', 'Kawin')->count(),
            'total_kk' => Kk::count(),
        ];

        return view('penduduk.statistics', compact('stats'));
    }
}
