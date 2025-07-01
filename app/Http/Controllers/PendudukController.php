<?php

namespace App\Http\Controllers;

use App\Models\Penduduk;
use App\Models\Kk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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
        $users = User::whereDoesntHave('penduduk')->where('status', 'active')->get();
        
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
                $data['status'] = 'aktif'; // Default status aktif

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

                // Log penduduk creation
                Log::info('New penduduk created', [
                    'penduduk_id' => $penduduk->id,
                    'nik' => $penduduk->nik,
                    'nama_lengkap' => $penduduk->nama_lengkap,
                    'user_id' => $penduduk->user_id,
                    'created_by' => auth()->id()
                ]);
            });

            return redirect()->route('penduduk.index')
                ->with('success', 'Data penduduk berhasil ditambahkan!');

        } catch (\Exception $e) {
            Log::error('Error creating penduduk', [
                'error' => $e->getMessage(),
                'request_data' => $request->except(['foto', 'password']),
                'user_id' => auth()->id()
            ]);

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
                    ->where('status', 'active')
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
                $oldStatus = $penduduk->status;
                $oldUserId = $penduduk->user_id;
                $data = $request->all();

                // Handle foto upload
                if ($request->hasFile('foto')) {
                    // Delete old foto
                    if ($penduduk->foto) {
                        Storage::disk('public')->delete($penduduk->foto);
                    }
                    $data['foto'] = $request->file('foto')->store('penduduk', 'public');
                }

                // Update penduduk data
                $penduduk->update($data);

                // Handle status change - sync with user status
                if ($penduduk->user) {
                    $newUserStatus = in_array($request->status, ['tidak_aktif', 'meninggal', 'pindah']) ? 'inactive' : 'active';
                    
                    if ($penduduk->user->status !== $newUserStatus) {
                        $penduduk->user->update(['status' => $newUserStatus]);
                        
                        Log::info('User status updated due to penduduk status change', [
                            'user_id' => $penduduk->user->id,
                            'penduduk_id' => $penduduk->id,
                            'penduduk_nik' => $penduduk->nik,
                            'old_penduduk_status' => $oldStatus,
                            'new_penduduk_status' => $request->status,
                            'new_user_status' => $newUserStatus,
                            'updated_by' => auth()->id()
                        ]);
                    }
                }

                // Handle user assignment change
                if ($oldUserId !== $request->user_id) {
                    // If old user exists, deactivate it
                    if ($oldUserId) {
                        $oldUser = User::find($oldUserId);
                        if ($oldUser && $oldUser->role === 'masyarakat') {
                            $oldUser->update(['status' => 'inactive']);
                            
                            Log::info('Old user deactivated due to penduduk reassignment', [
                                'old_user_id' => $oldUserId,
                                'penduduk_id' => $penduduk->id,
                                'penduduk_nik' => $penduduk->nik,
                                'updated_by' => auth()->id()
                            ]);
                        }
                    }

                    // If new user assigned, activate it if penduduk is active
                    if ($request->user_id && $request->status === 'aktif') {
                        $newUser = User::find($request->user_id);
                        if ($newUser) {
                            $newUser->update(['status' => 'active']);
                            
                            Log::info('New user activated due to penduduk assignment', [
                                'new_user_id' => $request->user_id,
                                'penduduk_id' => $penduduk->id,
                                'penduduk_nik' => $penduduk->nik,
                                'updated_by' => auth()->id()
                            ]);
                        }
                    }
                }

                Log::info('Penduduk updated', [
                    'penduduk_id' => $penduduk->id,
                    'nik' => $penduduk->nik,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'old_user_id' => $oldUserId,
                    'new_user_id' => $request->user_id,
                    'updated_by' => auth()->id()
                ]);
            });

            return redirect()->route('penduduk.index')
                ->with('success', 'Data penduduk berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Error updating penduduk', [
                'penduduk_id' => $penduduk->id,
                'error' => $e->getMessage(),
                'request_data' => $request->except(['foto', 'password']),
                'user_id' => auth()->id()
            ]);

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
                // Log before deletion
                Log::info('Penduduk deletion started', [
                    'penduduk_id' => $penduduk->id,
                    'nik' => $penduduk->nik,
                    'nama_lengkap' => $penduduk->nama_lengkap,
                    'user_id' => $penduduk->user_id,
                    'deleted_by' => auth()->id()
                ]);

                // Delete foto if exists
                if ($penduduk->foto) {
                    Storage::disk('public')->delete($penduduk->foto);
                }

                // Update KK jika penduduk adalah kepala keluarga
                if ($penduduk->kkAsKepala) {
                    $penduduk->kkAsKepala->update(['kepala_keluarga_id' => null]);
                    
                    Log::info('KK kepala keluarga updated due to penduduk deletion', [
                        'kk_id' => $penduduk->kkAsKepala->id,
                        'penduduk_id' => $penduduk->id,
                        'deleted_by' => auth()->id()
                    ]);
                }

                // Disable associated user account
                if ($penduduk->user) {
                    $penduduk->user->update([
                        'status' => 'inactive',
                        'email_verified_at' => null
                    ]);
                    
                    Log::info('User deactivated due to penduduk deletion', [
                        'user_id' => $penduduk->user->id,
                        'user_email' => $penduduk->user->email,
                        'penduduk_id' => $penduduk->id,
                        'penduduk_nik' => $penduduk->nik,
                        'deleted_by' => auth()->id()
                    ]);
                }

                $penduduk->delete();

                Log::info('Penduduk deleted successfully', [
                    'penduduk_id' => $penduduk->id,
                    'nik' => $penduduk->nik,
                    'deleted_by' => auth()->id()
                ]);
            });

            return redirect()->route('penduduk.index')
                ->with('success', 'Data penduduk berhasil dihapus! Akun pengguna terkait telah dinonaktifkan.');

        } catch (\Exception $e) {
            Log::error('Error deleting penduduk', [
                'penduduk_id' => $penduduk->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->route('penduduk.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status penduduk
     */
    public function toggleStatus(Penduduk $penduduk)
    {
        try {
            DB::transaction(function () use ($penduduk) {
                $oldStatus = $penduduk->status;
                $newStatus = $penduduk->status === 'aktif' ? 'tidak_aktif' : 'aktif';
                
                $penduduk->update(['status' => $newStatus]);

                // Update user status accordingly
                if ($penduduk->user) {
                    $newUserStatus = $newStatus === 'aktif' ? 'active' : 'inactive';
                    $penduduk->user->update(['status' => $newUserStatus]);
                    
                    Log::info('User status toggled with penduduk status', [
                        'user_id' => $penduduk->user->id,
                        'penduduk_id' => $penduduk->id,
                        'penduduk_nik' => $penduduk->nik,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'new_user_status' => $newUserStatus,
                        'toggled_by' => auth()->id()
                    ]);
                }

                Log::info('Penduduk status toggled', [
                    'penduduk_id' => $penduduk->id,
                    'nik' => $penduduk->nik,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'toggled_by' => auth()->id()
                ]);
            });

            $message = $penduduk->status === 'aktif' ? 'Penduduk berhasil diaktifkan.' : 'Penduduk berhasil dinonaktifkan.';
            
            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error toggling penduduk status', [
                'penduduk_id' => $penduduk->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengubah status penduduk.');
        }
    }

    /**
     * Get statistics
     */
    public function statistics()
    {
        $stats = [
            'total_penduduk' => Penduduk::count(),
            'penduduk_aktif' => Penduduk::where('status', 'aktif')->count(),
            'penduduk_tidak_aktif' => Penduduk::where('status', 'tidak_aktif')->count(),
            'laki_laki' => Penduduk::where('jenis_kelamin', 'L')->count(),
            'perempuan' => Penduduk::where('jenis_kelamin', 'P')->count(),
            'anak_anak' => Penduduk::umur(null, 17)->count(),
            'dewasa' => Penduduk::umur(18, 59)->count(),
            'lansia' => Penduduk::umur(60)->count(),
            'belum_kawin' => Penduduk::where('status_perkawinan', 'Belum Kawin')->count(),
            'kawin' => Penduduk::where('status_perkawinan', 'Kawin')->count(),
            'total_kk' => Kk::count(),
            'users_with_penduduk' => User::whereHas('penduduk')->count(),
            'users_without_penduduk' => User::whereDoesntHave('penduduk')->count(),
        ];

        return view('penduduk.statistics', compact('stats'));
    }
}
