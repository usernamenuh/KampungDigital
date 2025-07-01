<?php

namespace App\Http\Controllers;

use App\Models\Penduduk;
use App\Models\Kk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

        // Filter berdasarkan umur
        if ($request->filled('min_umur')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= ?', [$request->min_umur]);
        }

        if ($request->filled('max_umur')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) <= ?', [$request->max_umur]);
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
        // Validasi dasar
        $request->validate([
            'nik' => [
                'required',
                'string',
                'size:16',
                'unique:penduduks,nik',
                'regex:/^[0-9]{16}$/'
            ],
            'kk_id' => 'required|exists:kks,id',
            'user_id' => 'nullable|exists:users,id|unique:penduduks,user_id',
            'nama_lengkap' => 'required|string|max:255|min:2',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100|min:2',
            'tanggal_lahir' => 'required|date|before:today',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Khonghucu,Lainnya',
            'pendidikan' => 'nullable|string',
            'pekerjaan' => 'nullable|string|max:100',
            'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'hubungan_keluarga' => 'required|string',
            'kewarganegaraan' => 'required|string|max:50',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nik.required' => 'NIK wajib diisi',
            'nik.size' => 'NIK harus 16 digit',
            'nik.unique' => 'NIK sudah terdaftar',
            'nik.regex' => 'NIK harus berupa angka 16 digit',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nama_lengkap.min' => 'Nama lengkap minimal 2 karakter',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 2MB',
        ]);

        // Validasi khusus untuk kepala keluarga
        if ($request->hubungan_keluarga === 'Kepala Keluarga') {
            $kk = Kk::find($request->kk_id);
            if ($kk && $kk->kepala_keluarga_id) {
                return back()->withInput()->with('error', 'Kartu Keluarga ini sudah memiliki Kepala Keluarga. Silakan pilih hubungan keluarga yang lain.');
            }
        }

        try {
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
                    if ($kk && !$kk->kepala_keluarga_id) {
                        $kk->update(['kepala_keluarga_id' => $penduduk->id]);
                    }
                }
            });

            return redirect()->route('penduduk.index')
                ->with('success', 'Data penduduk berhasil ditambahkan!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
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
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]{16}$/',
                Rule::unique('penduduks')->ignore($penduduk->id)
            ],
            'kk_id' => 'required|exists:kks,id',
            'user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('penduduks')->ignore($penduduk->id)
            ],
            'nama_lengkap' => 'required|string|max:255|min:2',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100|min:2',
            'tanggal_lahir' => 'required|date|before:today',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Khonghucu,Lainnya',
            'status' => 'required|in:aktif,tidak_aktif,meninggal,pindah',
            'hubungan_keluarga' => 'required|string',
            'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nik.required' => 'NIK wajib diisi',
            'nik.size' => 'NIK harus 16 digit',
            'nik.unique' => 'NIK sudah terdaftar',
            'nik.regex' => 'NIK harus berupa angka 16 digit',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nama_lengkap.min' => 'Nama lengkap minimal 2 karakter',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 2MB',
        ]);

        try {
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
                ->with('success', 'Data penduduk berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penduduk $penduduk)
    {
        try {
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
                ->with('success', 'Data penduduk berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->route('penduduk.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
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
