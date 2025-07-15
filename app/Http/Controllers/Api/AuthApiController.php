<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use App\Models\Penduduk;
use Illuminate\Support\Facades\Log;

class AuthApiController extends Controller
{
    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['success' => false, 'message' => 'Kredensial tidak valid'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:masyarakat', // Only 'masyarakat' can self-register
            'nik' => 'required|string|digits:16|unique:penduduk,nik',
            'nama_lengkap' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string|max:255',
            'rt_id' => 'required|exists:rts,id',
            'kk_id' => 'nullable|exists:kk,id', // Optional, can be linked later
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => 'pending', // User needs to be activated by admin
            ]);

            // Create Penduduk
            $penduduk = Penduduk::create([
                'user_id' => $user->id,
                'nik' => $request->nik,
                'nama_lengkap' => $request->nama_lengkap,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'rt_id' => $request->rt_id,
                'kk_id' => $request->kk_id,
                'status' => 'aktif', // Penduduk status can be active by default
            ]);

            event(new Registered($user));

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil. Akun Anda menunggu aktivasi.',
                'user' => $user,
                'penduduk' => $penduduk,
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Registrasi gagal. Silakan coba lagi.'], 500);
        }
    }

    /**
     * Get authenticated user details.
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('penduduk.rt.rw'), // Load related data
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Logout berhasil']);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $user->update($request->only('name', 'email'));

        return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui', 'user' => $user]);
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['success' => true, 'message' => 'Password berhasil diperbarui']);
    }

    /**
     * Handle forgot password request.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
            ? response()->json(['success' => true, 'message' => 'Link reset password telah dikirim ke email Anda.'])
            : response()->json(['success' => false, 'message' => 'Gagal mengirim link reset password.'], 500);
    }

    /**
     * Handle reset password request.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60))->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? response()->json(['success' => true, 'message' => 'Password berhasil direset.'])
            : response()->json(['success' => false, 'message' => 'Gagal mereset password.'], 500);
    }

    /**
     * Search users (for admin/kades/rw/rt).
     */
    public function searchUsers(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $role = $request->input('role');
        $limit = $request->input('limit', 10);

        $query = User::query()->with('penduduk');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereHas('penduduk', function ($q2) use ($search) {
                      $q2->where('nama_lengkap', 'like', '%' . $search . '%')
                         ->orWhere('nik', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($role && in_array($role, ['admin', 'kades', 'rw', 'rt', 'masyarakat'])) {
            $query->where('role', $role);
        }

        // Role-based access control for searching users
        if ($user->role === 'rw' && $user->penduduk && $user->penduduk->rwKetua) {
            $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
            $query->whereHas('penduduk', function ($q) use ($rtIds) {
                $q->whereIn('rt_id', $rtIds);
            });
        } elseif ($user->role === 'rt' && $user->penduduk && $user->penduduk->rtKetua) {
            $rtId = $user->penduduk->rtKetua->id;
            $query->whereHas('penduduk', function ($q) use ($rtId) {
                $q->where('rt_id', $rtId);
            });
        } elseif (!in_array($user->role, ['admin', 'kades'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $users = $query->limit($limit)->get();

        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * Get all users (Admin only).
     */
    public function getAllUsers(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $users = User::with('penduduk.rt.rw')->paginate(10);

        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * Create a new user (Admin only).
     */
    public function createUser(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,kades,rw,rt,masyarakat',
            'nik' => 'nullable|string|digits:16|unique:penduduk,nik',
            'nama_lengkap' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'nullable|string|max:255',
            'rt_id' => 'nullable|exists:rts,id',
            'kk_id' => 'nullable|exists:kk,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => $request->role === 'masyarakat' ? 'pending' : 'aktif', // Masyarakat needs activation
            ]);

            if ($request->filled('nik')) {
                Penduduk::create([
                    'user_id' => $user->id,
                    'nik' => $request->nik,
                    'nama_lengkap' => $request->nama_lengkap,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'alamat' => $request->alamat,
                    'rt_id' => $request->rt_id,
                    'kk_id' => $request->kk_id,
                    'status' => 'aktif',
                ]);
            }

            return response()->json(['success' => true, 'message' => 'User berhasil dibuat', 'user' => $user], 201);
        } catch (\Exception $e) {
            Log::error('Create user failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Gagal membuat user.'], 500);
        }
    }

    /**
     * Update an existing user (Admin only).
     */
    public function updateUser(Request $request, User $targetUser)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $targetUser->id,
            'role' => 'required|in:admin,kades,rw,rt,masyarakat',
            'status' => 'required|in:aktif,nonaktif,pending',
            'nik' => 'nullable|string|digits:16|unique:penduduk,nik,' . ($targetUser->penduduk->id ?? 'NULL') . ',id',
            'nama_lengkap' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'nullable|string|max:255',
            'rt_id' => 'nullable|exists:rts,id',
            'kk_id' => 'nullable|exists:kk,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $targetUser->update($request->only('name', 'email', 'role', 'status'));

            if ($request->filled('nik')) {
                if ($targetUser->penduduk) {
                    $targetUser->penduduk->update($request->only([
                        'nik', 'nama_lengkap', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'rt_id', 'kk_id'
                    ]));
                } else {
                    Penduduk::create([
                        'user_id' => $targetUser->id,
                        'nik' => $request->nik,
                        'nama_lengkap' => $request->nama_lengkap,
                        'tanggal_lahir' => $request->tanggal_lahir,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'alamat' => $request->alamat,
                        'rt_id' => $request->rt_id,
                        'kk_id' => $request->kk_id,
                        'status' => 'aktif',
                    ]);
                }
            } elseif ($targetUser->penduduk) {
                // If NIK is removed, detach penduduk? Or just keep it as is.
                // For now, if NIK is empty, don't update penduduk fields.
            }

            return response()->json(['success' => true, 'message' => 'User berhasil diperbarui', 'user' => $targetUser->load('penduduk')], 200);
        } catch (\Exception $e) {
            Log::error('Update user failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui user.'], 500);
        }
    }

    /**
     * Delete a user (Admin only).
     */
    public function deleteUser(User $targetUser)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        if (Auth::id() === $targetUser->id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak dapat menghapus akun Anda sendiri.'], 403);
        }

        try {
            $targetUser->delete();
            return response()->json(['success' => true, 'message' => 'User berhasil dihapus'], 200);
        } catch (\Exception $e) {
            Log::error('Delete user failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Gagal menghapus user.'], 500);
        }
    }

    /**
     * Reset user password (Admin only).
     */
    public function resetUserPassword(Request $request, User $targetUser)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $targetUser->update(['password' => Hash::make($request->password)]);
            return response()->json(['success' => true, 'message' => 'Password user berhasil direset'], 200);
        } catch (\Exception $e) {
            Log::error('Reset user password failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Gagal mereset password user.'], 500);
        }
    }

    /**
     * Toggle user status (Admin only).
     */
    public function toggleUserStatus(User $targetUser)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        if (Auth::id() === $targetUser->id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak dapat mengubah status akun Anda sendiri.'], 403);
        }

        try {
            $newStatus = $targetUser->status === 'aktif' ? 'nonaktif' : 'aktif';
            $targetUser->update(['status' => $newStatus]);
            return response()->json(['success' => true, 'message' => 'Status user berhasil diubah menjadi ' . $newStatus, 'user' => $targetUser], 200);
        } catch (\Exception $e) {
            Log::error('Toggle user status failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Gagal mengubah status user.'], 500);
        }
    }
}
