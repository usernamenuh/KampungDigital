<?php

namespace App\Http\Controllers;

use App\Models\Penduduk;
use App\Models\Kk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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

        Log::info('Penduduk index accessed', [
            'user_id' => Auth::id(),
            'filters' => $request->only(['kk_id', 'jenis_kelamin', 'status', 'min_umur', 'max_umur', 'search']),
            'total_results' => $penduduks->total()
        ]);

        return view('penduduk.index', compact('penduduks', 'kks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kks = Kk::with('rt.rw')->where('status', 'aktif')->get();
        // Load user roles for display in dropdown
        $users = User::active()->whereDoesntHave('penduduk')->get();
        
        Log::info('Penduduk create form accessed', [
            'user_id' => Auth::id(),
            'available_kks' => $kks->count(),
            'available_users' => $users->count()
        ]);
        
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
            'pendidikan' => 'nullable|string|max:100', // Added max length
            'pekerjaan' => 'nullable|string|max:100',
            'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'hubungan_keluarga' => [
                'required',
                'string',
                Rule::in(Penduduk::getHubunganKeluargaOptions()), // Validate against defined options
                function ($attribute, $value, $fail) use ($request) {
                    if ($value === 'Kepala Keluarga') {
                        $kk = Kk::find($request->kk_id);
                        if ($kk && $kk->kepala_keluarga_id) {
                            $fail('Kartu Keluarga ini sudah memiliki Kepala Keluarga.');
                        }
                    }
                }
            ],
            'kewarganegaraan' => 'required|string|max:50',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string|max:500', // Added max length
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
            'hubungan_keluarga.required' => 'Hubungan keluarga wajib diisi.',
            'hubungan_keluarga.in' => 'Hubungan keluarga tidak valid.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $data = $request->all();
                $data['status'] = 'aktif'; // Default status

                // Handle foto upload
                if ($request->hasFile('foto')) {
                    $data['foto'] = $request->file('foto')->store('penduduk', 'public');
                } else {
                    $data['foto'] = null; // Ensure foto is null if not uploaded
                }

                $penduduk = Penduduk::create($data);

                // Auto-assign sebagai kepala keluarga jika hubungan keluarga adalah "Kepala Keluarga"
                if ($request->hubungan_keluarga === 'Kepala Keluarga') {
                    $kk = Kk::find($request->kk_id);
                    if ($kk && !$kk->kepala_keluarga_id) {
                        $kk->update(['kepala_keluarga_id' => $penduduk->id]);
                        Log::info('KK kepala keluarga updated on create', [
                            'kk_id' => $kk->id,
                            'new_kepala_id' => $penduduk->id
                        ]);
                    }
                }

                // If user is assigned, ensure user is active
                if ($request->user_id) {
                    $user = User::find($request->user_id);
                    if ($user) {
                        $user->update(['status' => 'active']);
                        Log::info('Assigned user activated on penduduk create', [
                            'user_id' => $user->id,
                            'penduduk_id' => $penduduk->id
                        ]);
                    }
                }

                Log::info('Penduduk created successfully', [
                    'user_id' => Auth::id(),
                    'penduduk_id' => $penduduk->id,
                    'nik' => $penduduk->nik,
                    'nama' => $penduduk->nama_lengkap,
                    'assigned_user_id' => $request->user_id
                ]);
            });

            return redirect()->route('penduduk.index')
                ->with('success', 'Data penduduk berhasil ditambahkan!');

        } catch (\Exception $e) {
            Log::error('Error creating penduduk', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'data' => $request->except(['foto', 'password']),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Penduduk $penduduk)
    {
        // Load all potential leadership relationships
        $penduduk->load(['kk.rt.rw', 'user', 'kkAsKepala', 'rwKetua', 'rtKetua', 'kepalaDesa']);
        
        Log::info('Penduduk detail viewed', [
            'user_id' => Auth::id(),
            'penduduk_id' => $penduduk->id,
            'nik' => $penduduk->nik
        ]);
        
        return view('penduduk.show', compact('penduduk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penduduk $penduduk)
    {
        $kks = Kk::with('rt.rw')->where('status', 'aktif')->get();
        // Load user roles for display in dropdown
        $users = User::active()
                    ->whereDoesntHave('penduduk')
                    ->orWhere('id', $penduduk->user_id) // Include current user if already assigned
                    ->get();
        
        // Load all potential leadership relationships for the current penduduk
        $penduduk->load(['rwKetua', 'rtKetua', 'kepalaDesa']);

        Log::info('Penduduk edit form accessed', [
            'user_id' => Auth::id(),
            'penduduk_id' => $penduduk->id,
            'nik' => $penduduk->nik
        ]);
        
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
            'hubungan_keluarga' => [
                'required',
                'string',
                Rule::in(Penduduk::getHubunganKeluargaOptions()), // Validate against defined options
                function ($attribute, $value, $fail) use ($request, $penduduk) {
                    if ($value === 'Kepala Keluarga') {
                        $kk = Kk::find($request->kk_id);
                        // If KK already has a head AND it's not the current penduduk being edited
                        if ($kk && $kk->kepala_keluarga_id && $kk->kepala_keluarga_id !== $penduduk->id) {
                            $fail('Kartu Keluarga ini sudah memiliki Kepala Keluarga lain.');
                        }
                    }
                }
            ],
            'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pendidikan' => 'nullable|string|max:100',
            'pekerjaan' => 'nullable|string|max:100',
            'kewarganegaraan' => 'required|string|max:50',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:500',
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
            'hubungan_keluarga.required' => 'Hubungan keluarga wajib diisi.',
            'hubungan_keluarga.in' => 'Hubungan keluarga tidak valid.',
        ]);

        try {
            DB::transaction(function () use ($request, $penduduk) {
                $data = $request->all();
                $oldUserID = $penduduk->user_id;
                $oldStatus = $penduduk->status;
                $oldKkId = $penduduk->kk_id;
                $oldHubunganKeluarga = $penduduk->hubungan_keluarga;

                // Handle foto upload
                if ($request->hasFile('foto')) {
                    // Delete old foto
                    if ($penduduk->foto) {
                        Storage::disk('public')->delete($penduduk->foto);
                    }
                    $data['foto'] = $request->file('foto')->store('penduduk', 'public');
                } else if ($request->boolean('remove_foto')) { // Add a hidden field or checkbox for explicit removal
                    if ($penduduk->foto) {
                        Storage::disk('public')->delete($penduduk->foto);
                    }
                    $data['foto'] = null;
                } else {
                    // Keep existing foto if no new file and not explicitly removed
                    $data['foto'] = $penduduk->foto;
                }

                // Handle user assignment change
                if ($oldUserID != $request->user_id) {
                    // Deactivate old user if exists and was linked to this penduduk
                    if ($oldUserID) {
                        $oldUser = User::find($oldUserID);
                        if ($oldUser && $oldUser->penduduk_id === $penduduk->id) { // Ensure it's linked to this specific penduduk
                            $oldUser->update(['status' => 'inactive']);
                            Log::info('Old user deactivated due to penduduk user_id change', [
                                'old_user_id' => $oldUserID,
                                'penduduk_id' => $penduduk->id
                            ]);
                        }
                    }
                    
                    // Activate new user if assigned
                    if ($request->user_id) {
                        $newUser = User::find($request->user_id);
                        if ($newUser) {
                            $userStatus = ($request->status === 'aktif') ? 'active' : 'inactive';
                            $newUser->update(['status' => $userStatus]);
                            Log::info('New user activated due to penduduk user_id change', [
                                'new_user_id' => $request->user_id,
                                'penduduk_id' => $penduduk->id
                            ]);
                        }
                    }
                }

                // Handle status change - sync with user
                if ($penduduk->user && ($oldStatus != $request->status)) {
                    $userStatus = in_array($request->status, ['tidak_aktif', 'meninggal', 'pindah']) ? 'inactive' : 'active';
                    $penduduk->user->update(['status' => $userStatus]);
                    Log::info('User status synced with penduduk status change', [
                        'user_id' => $penduduk->user_id,
                        'penduduk_id' => $penduduk->id,
                        'old_penduduk_status' => $oldStatus,
                        'new_penduduk_status' => $request->status
                    ]);
                }

                $penduduk->update($data);

                // --- Logic for handling Kepala Keluarga assignment on KK model ---
                $newKkId = $request->kk_id;
                $newHubunganKeluarga = $request->hubungan_keluarga;

                // 1. If this penduduk was previously a 'Kepala Keluarga' for its old KK, and either:
                //    a) its relationship changed from 'Kepala Keluarga'
                //    b) its KK changed
                if ($oldHubunganKeluarga === 'Kepala Keluarga' && $oldKkId) {
                    $oldKk = Kk::find($oldKkId);
                    if ($oldKk && $oldKk->kepala_keluarga_id === $penduduk->id) {
                        // If relationship changed OR KK changed, nullify old KK's head
                        if ($newHubunganKeluarga !== 'Kepala Keluarga' || $newKkId !== $oldKkId) {
                            $oldKk->update(['kepala_keluarga_id' => null]);
                            Log::info('Old KK kepala keluarga nulled', [
                                'old_kk_id' => $oldKkId,
                                'penduduk_id' => $penduduk->id
                            ]);
                        }
                    }
                }

                // 2. If this penduduk is now 'Kepala Keluarga' for the new KK
                if ($newHubunganKeluarga === 'Kepala Keluarga') {
                    $newKk = Kk::find($newKkId);
                    // Only update if the new KK doesn't already have a head, or if the current head is this penduduk
                    if ($newKk && (!$newKk->kepala_keluarga_id || $newKk->kepala_keluarga_id === $penduduk->id)) {
                        $newKk->update(['kepala_keluarga_id' => $penduduk->id]);
                        Log::info('New KK kepala keluarga updated', [
                            'new_kk_id' => $newKkId,
                            'penduduk_id' => $penduduk->id
                        ]);
                    }
                }
                // --- End of Kepala Keluarga assignment logic ---

                Log::info('Penduduk updated successfully', [
                    'user_id' => Auth::id(),
                    'penduduk_id' => $penduduk->id,
                    'nik' => $penduduk->nik,
                    'changes' => $penduduk->getChanges(),
                    'old_user_id' => $oldUserID,
                    'new_user_id' => $request->user_id,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'old_kk_id' => $oldKkId,
                    'new_kk_id' => $newKkId,
                    'old_hubungan_keluarga' => $oldHubunganKeluarga,
                    'new_hubungan_keluarga' => $newHubunganKeluarga,
                ]);
            });

            return redirect()->route('penduduk.index')
                ->with('success', 'Data penduduk berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Error updating penduduk', [
                'user_id' => Auth::id(),
                'penduduk_id' => $penduduk->id,
                'error' => $e->getMessage(),
                'data' => $request->except(['foto', 'password']),
                'trace' => $e->getTraceAsString()
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
                // Delete foto if exists
                if ($penduduk->foto) {
                    Storage::disk('public')->delete($penduduk->foto);
                }

                // Update KK jika penduduk adalah kepala keluarga
                if ($penduduk->kkAsKepala) {
                    $penduduk->kkAsKepala->update(['kepala_keluarga_id' => null]);
                    Log::info('KK kepala keluarga nulled on penduduk delete', [
                        'kk_id' => $penduduk->kkAsKepala->id,
                        'penduduk_id' => $penduduk->id
                    ]);
                }

                // Disable/delete associated user account
                if ($penduduk->user) {
                    // Soft disable the user
                    $penduduk->user->update([
                        'status' => 'inactive',
                        'email_verified_at' => null
                    ]);
                    Log::info('Associated user deactivated on penduduk delete', [
                        'user_id' => $penduduk->user->id,
                        'penduduk_id' => $penduduk->id
                    ]);
                }

                Log::info('Penduduk deleted', [
                    'user_id' => Auth::id(),
                    'penduduk_id' => $penduduk->id,
                    'nik' => $penduduk->nik,
                    'nama' => $penduduk->nama_lengkap,
                    'associated_user_id' => $penduduk->user_id
                ]);

                $penduduk->delete();
            });

            return redirect()->route('penduduk.index')
                ->with('success', 'Data penduduk berhasil dihapus! Akun pengguna terkait telah dinonaktifkan.');

        } catch (\Exception $e) {
            Log::error('Error deleting penduduk', [
                'user_id' => Auth::id(),
                'penduduk_id' => $penduduk->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
            $newStatus = $penduduk->status === 'aktif' ? 'tidak_aktif' : 'aktif';
            
            DB::transaction(function () use ($penduduk, $newStatus) {
                $penduduk->update(['status' => $newStatus]);
                
                // Sync user status if exists
                if ($penduduk->user) {
                    $userStatus = $newStatus === 'aktif' ? 'active' : 'inactive';
                    $penduduk->user->update(['status' => $userStatus]);
                }
            });

            $message = $newStatus === 'aktif' ? 'Penduduk berhasil diaktifkan!' : 'Penduduk berhasil dinonaktifkan!';
            
            Log::info('Penduduk status toggled', [
                'user_id' => Auth::id(),
                'penduduk_id' => $penduduk->id,
                'old_status' => $penduduk->status,
                'new_status' => $newStatus,
                'user_id' => $penduduk->user_id
            ]);

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error toggling penduduk status', [
                'user_id' => Auth::id(),
                'penduduk_id' => $penduduk->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengubah status: ' . $e->getMessage());
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
            'laki_laki' => Penduduk::where('jenis_kelamin', 'L')->count(),
            'perempuan' => Penduduk::where('jenis_kelamin', 'P')->count(),
            'anak_anak' => Penduduk::umur(null, 17)->count(),
            'dewasa' => Penduduk::umur(18, 59)->count(),
            'lansia' => Penduduk::umur(60)->count(),
            'belum_kawin' => Penduduk::where('status_perkawinan', 'Belum Kawin')->count(),
            'kawin' => Penduduk::where('status_perkawinan', 'Kawin')->count(),
            'total_kk' => Kk::count(),
            'users_with_penduduk' => User::whereHas('penduduk')->count(),
            'active_users' => User::where('status', 'active')->count(),
        ];

        Log::info('Penduduk statistics accessed', [
            'user_id' => Auth::id(),
            'stats' => $stats
        ]);

        return view('penduduk.statistics', compact('stats'));
    }
}
