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
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    /**
     * Handle user login - ENHANCED
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if user is active
            if ($user->status !== 'active') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['Akun Anda tidak aktif. Hubungi administrator.'],
                ]);
            }

            // Update last activity
            $user->update([
                'last_activity' => now(),
                'is_online' => true
            ]);

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'user' => $user->load('penduduk'),
                'token' => $token,
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Email atau password tidak valid.'],
        ]);
    }

    /**
     * Handle user registration - ENHANCED
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:masyarakat,rt,rw,kades,admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => 'active',
                'last_activity' => now(),
                'is_online' => true
            ]);

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'user' => $user,
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Get authenticated user details - ENHANCED
     */
    public function user(Request $request)
    {
        $user = $request->user()->load('penduduk');
        
        // Update last activity
        $user->update([
            'last_activity' => now(),
            'is_online' => true
        ]);

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    /**
     * Handle user logout - ENHANCED
     */
    public function logout(Request $request)
    {
        // Update user status
        $request->user()->update([
            'is_online' => false
        ]);

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * Update user profile - ENHANCED
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validasi gagal', 
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user->update($request->only('name', 'email'));

            return response()->json([
                'success' => true, 
                'message' => 'Profil berhasil diperbarui', 
                'user' => $user->load('penduduk')
            ]);

        } catch (\Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil'
            ], 500);
        }
    }

    /**
     * Update user password - ENHANCED
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validasi gagal', 
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user->update(['password' => Hash::make($request->password)]);

            return response()->json([
                'success' => true, 
                'message' => 'Password berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            Log::error('Password update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui password'
            ], 500);
        }
    }

    /**
     * Handle forgot password request
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validasi gagal', 
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
            ? response()->json(['success' => true, 'message' => 'Link reset password telah dikirim ke email Anda.'])
            : response()->json(['success' => false, 'message' => 'Gagal mengirim link reset password.'], 500);
    }

    /**
     * Handle reset password request
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validasi gagal', 
                'errors' => $validator->errors()
            ], 422);
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
     * Search users (for admin/kades/rw/rt) - ENHANCED
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
            $query->whereHas('penduduk.kk', function ($q) use ($rtIds) {
                $q->whereIn('rt_id', $rtIds);
            });
        } elseif ($user->role === 'rt' && $user->penduduk && $user->penduduk->rtKetua) {
            $rtId = $user->penduduk->rtKetua->id;
            $query->whereHas('penduduk.kk', function ($q) use ($rtId) {
                $q->where('rt_id', $rtId);
            });
        } elseif (!in_array($user->role, ['admin', 'kades'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $users = $query->limit($limit)->get();

        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * Get all users (Admin only) - ENHANCED
     */
    public function getAllUsers(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $users = User::with('penduduk.kk.rt.rw')->paginate(10);

        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * Create a new user (Admin only) - ENHANCED
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
            'kk_id' => 'nullable|exists:kks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validasi gagal', 
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => 'active',
            ]);

            if ($request->filled('nik')) {
                Penduduk::create([
                    'user_id' => $user->id,
                    'nik' => $request->nik,
                    'nama_lengkap' => $request->nama_lengkap,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'alamat' => $request->alamat,
                    'kk_id' => $request->kk_id,
                    'status' => 'aktif',
                ]);
            }

            return response()->json([
                'success' => true, 
                'message' => 'User berhasil dibuat', 
                'user' => $user->load('penduduk')
            ], 201);

        } catch (\Exception $e) {
            Log::error('Create user failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Gagal membuat user.'], 500);
        }
    }

    /**
     * Toggle user status (Admin only) - ENHANCED
     */
    public function toggleUserStatus(User $targetUser)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        if (Auth::id() === $targetUser->id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak dapat mengubah status akun sendiri.'], 403);
        }

        try {
            $newStatus = $targetUser->status === 'active' ? 'inactive' : 'active';
            $targetUser->update(['status' => $newStatus]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Status user berhasil diubah menjadi ' . $newStatus, 
                'user' => $targetUser
            ], 200);

        } catch (\Exception $e) {
            Log::error('Toggle user status failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Gagal mengubah status user.'], 500);
        }
    }
}
