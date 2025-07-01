<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('penduduk');

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search berdasarkan nama atau email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $penduduks = Penduduk::whereNull('user_id')->get();
        return view('users.create', compact('penduduks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,kades,rw,rt,masyarakat',
            'penduduk_id' => 'nullable|exists:penduduks,id|unique:users,id',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role wajib dipilih',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                    'status' => 'active',
                ]);

                // Link dengan penduduk jika dipilih
                if ($request->penduduk_id) {
                    $penduduk = Penduduk::find($request->penduduk_id);
                    $penduduk->update(['user_id' => $user->id]);
                }
            });

            return redirect()->route('users.index')
                ->with('success', 'Pengguna berhasil ditambahkan!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('penduduk');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $penduduks = Penduduk::whereNull('user_id')
                            ->orWhere('user_id', $user->id)
                            ->get();
        return view('users.edit', compact('user', 'penduduks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,kades,rw,rt,masyarakat',
            'status' => 'required|in:active,inactive',
            'penduduk_id' => 'nullable|exists:penduduks,id',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role wajib dipilih',
            'status.required' => 'Status wajib dipilih',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                $data = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'role' => $request->role,
                    'status' => $request->status,
                ];

                if ($request->filled('password')) {
                    $data['password'] = Hash::make($request->password);
                }

                $user->update($data);

                // Handle penduduk relationship
                if ($user->penduduk && $request->penduduk_id != $user->penduduk->id) {
                    // Remove old relationship
                    $user->penduduk->update(['user_id' => null]);
                }

                if ($request->penduduk_id) {
                    $penduduk = Penduduk::find($request->penduduk_id);
                    $penduduk->update(['user_id' => $user->id]);
                }
            });

            return redirect()->route('users.index')
                ->with('success', 'Data pengguna berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            DB::transaction(function () use ($user) {
                // Remove relationship with penduduk
                if ($user->penduduk) {
                    $user->penduduk->update(['user_id' => null]);
                }

                $user->delete();
            });

            return redirect()->route('users.index')
                ->with('success', 'Pengguna berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        try {
            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            $user->update(['status' => $newStatus]);

            $message = $newStatus === 'active' ? 'Pengguna berhasil diaktifkan!' : 'Pengguna berhasil dinonaktifkan!';

            return redirect()->route('users.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Terjadi kesalahan saat mengubah status pengguna.');
        }
    }

    /**
     * Change user role
     */
    public function changeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,kades,rw,rt,masyarakat'
        ]);

        try {
            $user->update(['role' => $request->role]);

            return redirect()->route('users.index')
                ->with('success', 'Role pengguna berhasil diubah!');

        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Terjadi kesalahan saat mengubah role pengguna.');
        }
    }
}
