<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // Added for logging

class AuthApiController extends Controller
{
    /**
     * Login user
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                Log::warning('AuthApiController: Login validation failed.', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                Log::info('AuthApiController: Login failed for email: ' . $request->email);
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah'
                ], 401);
            }

            $user = Auth::user();

            // Check user status
            if ($user->status !== 'aktif') {
                Auth::logout();
                Log::warning('AuthApiController: Inactive user tried to login.', ['user_id' => $user->id, 'status' => $user->status]);
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'
                ], 403);
            }

            // Update last activity
            $user->update(['last_activity' => now()]);
            Log::info('AuthApiController: User logged in successfully.', ['user_id' => $user->id]);

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $user->load('penduduk'),
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error during login.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register user
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'nik' => 'required|string|size:16|unique:penduduk',
                'nama_lengkap' => 'required|string|max:255',
                'tempat_lahir' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:L,P',
                'alamat' => 'required|string',
                'rt_id' => 'required|exists:rts,id',
                'no_hp' => 'nullable|string|max:15',
            ]);

            if ($validator->fails()) {
                Log::warning('AuthApiController: Registration validation failed.', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create penduduk first
            $penduduk = Penduduk::create([
                'nik' => $request->nik,
                'nama_lengkap' => $request->nama_lengkap,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'rt_id' => $request->rt_id,
                'no_hp' => $request->no_hp,
                'status' => 'aktif'
            ]);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'masyarakat',
                'penduduk_id' => $penduduk->id,
                'status' => 'aktif',
                'last_activity' => now()
            ]);
            Log::info('AuthApiController: User registered successfully.', ['user_id' => $user->id, 'penduduk_id' => $penduduk->id]);

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => $user->load('penduduk'),
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error during registration.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal registrasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            Log::info('AuthApiController: User logged out successfully.', ['user_id' => $request->user()->id]);

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ]);

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error during logout.', ['user_id' => $request->user()->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        try {
            $user = $request->user()->load('penduduk');
            Log::debug('AuthApiController: Fetched authenticated user data.', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error fetching authenticated user data.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'nama_lengkap' => 'required|string|max:255',
                'tempat_lahir' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:L,P',
                'alamat' => 'required|string',
                'no_hp' => 'nullable|string|max:15',
            ]);

            if ($validator->fails()) {
                Log::warning('AuthApiController: Profile update validation failed.', ['user_id' => $user->id, 'errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update user
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update penduduk if exists
            if ($user->penduduk) {
                $user->penduduk->update([
                    'nama_lengkap' => $request->nama_lengkap,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'alamat' => $request->alamat,
                    'no_hp' => $request->no_hp,
                ]);
                Log::info('AuthApiController: User profile and resident data updated successfully.', ['user_id' => $user->id, 'penduduk_id' => $user->penduduk->id]);
            } else {
                Log::warning('AuthApiController: User profile updated, but no resident data found to update.', ['user_id' => $user->id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diperbarui',
                'data' => $user->fresh(['penduduk'])
            ]);

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error updating user profile.', ['user_id' => $request->user()->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                Log::warning('AuthApiController: Password update validation failed.', ['user_id' => $request->user()->id, 'errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                Log::warning('AuthApiController: Incorrect current password during update.', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Password saat ini salah'
                ], 400);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->password)
            ]);
            Log::info('AuthApiController: User password updated successfully.', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error updating user password.', ['user_id' => $request->user()->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                Log::warning('AuthApiController: Forgot password validation failed.', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                Log::info('AuthApiController: Password reset link sent.', ['email' => $request->email]);
                return response()->json([
                    'success' => true,
                    'message' => 'Link reset password telah dikirim ke email Anda'
                ]);
            } else {
                Log::error('AuthApiController: Failed to send password reset link.', ['email' => $request->email, 'status' => $status]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim link reset password'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error during forgot password process.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses forgot password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                Log::warning('AuthApiController: Reset password validation failed.', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                Log::info('AuthApiController: Password reset successfully.', ['email' => $request->email]);
                return response()->json([
                    'success' => true,
                    'message' => 'Password berhasil direset'
                ]);
            } else {
                Log::error('AuthApiController: Failed to reset password.', ['email' => $request->email, 'status' => $status]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal reset password'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error during reset password process.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search users (for RT/RW/Kades/Admin)
     */
    public function searchUsers(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
                Log::warning('AuthApiController: Unauthorized access to searchUsers.', ['user_id' => $user->id, 'role' => $user->role]);
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $query = $request->get('q');
            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $usersQuery = User::with('penduduk');

            // Apply role-based filters
            switch ($user->role) {
                case 'rt':
                    if ($user->penduduk && $user->penduduk->rtKetua) {
                        $usersQuery->whereHas('penduduk', function($q) use ($user) {
                            $q->where('rt_id', $user->penduduk->rtKetua->id);
                        });
                    } else {
                        Log::warning('AuthApiController: RT data not found for user during searchUsers.', ['user_id' => $user->id]);
                        return response()->json(['error' => 'Data RT tidak ditemukan'], 404);
                    }
                    break;
                case 'rw':
                    if ($user->penduduk && $user->penduduk->rwKetua) {
                        $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                        $usersQuery->whereHas('penduduk', function($q) use ($rtIds) {
                            $q->whereIn('rt_id', $rtIds);
                        });
                    } else {
                        Log::warning('AuthApiController: RW data not found for user during searchUsers.', ['user_id' => $user->id]);
                        return response()->json(['error' => 'Data RW tidak ditemukan'], 404);
                    }
                    break;
            }

            // Search in multiple fields
            $usersQuery->where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%')
                  ->orWhereHas('penduduk', function($subQ) use ($query) {
                      $subQ->where('nama_lengkap', 'like', '%' . $query . '%')
                           ->orWhere('nik', 'like', '%' . $query . '%');
                  });
            });

            $results = $usersQuery->limit(20)->get();
            Log::debug('AuthApiController: User search completed.', ['user_id' => $user->id, 'query' => $query, 'results_count' => $results->count()]);

            return response()->json([
                'success' => true,
                'data' => $results,
                'query' => $query
            ]);

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error during user search.', ['user_id' => Auth::id(), 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users (Admin only)
     */
    public function getAllUsers(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin') {
                Log::warning('AuthApiController: Unauthorized access to getAllUsers.', ['user_id' => $user->id, 'role' => $user->role]);
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $query = User::with('penduduk');

            // Apply filters
            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhereHas('penduduk', function($subQ) use ($search) {
                          $subQ->where('nama_lengkap', 'like', '%' . $search . '%')
                               ->orWhere('nik', 'like', '%' . $search . '%');
                      });
                });
            }

            $users = $query->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 20));
            Log::debug('AuthApiController: Fetched all users for admin.', ['user_id' => $user->id, 'total_users' => $users->total()]);

            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'has_more' => $users->hasMorePages()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error fetching all users.', ['user_id' => Auth::id(), 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create user (Admin only)
     */
    public function createUser(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin') {
                Log::warning('AuthApiController: Unauthorized access to createUser.', ['user_id' => $user->id, 'role' => $user->role]);
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'role' => 'required|in:masyarakat,rt,rw,kades,admin',
                'status' => 'required|in:aktif,nonaktif',
                'penduduk_id' => 'nullable|exists:penduduk,id',
            ]);

            if ($validator->fails()) {
                Log::warning('AuthApiController: Create user validation failed.', ['user_id' => $user->id, 'errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => $request->status,
                'penduduk_id' => $request->penduduk_id,
                'last_activity' => now()
            ]);
            Log::info('AuthApiController: New user created successfully.', ['admin_id' => $user->id, 'new_user_id' => $newUser->id]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dibuat',
                'data' => $newUser->load('penduduk')
            ], 201);

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error creating user.', ['admin_id' => Auth::id(), 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user (Admin only)
     */
    public function updateUser(Request $request, User $targetUser)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin') {
                Log::warning('AuthApiController: Unauthorized access to updateUser.', ['user_id' => $user->id, 'role' => $user->role]);
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $targetUser->id,
                'role' => 'required|in:masyarakat,rt,rw,kades,admin',
                'status' => 'required|in:aktif,nonaktif',
                'penduduk_id' => 'nullable|exists:penduduk,id',
            ]);

            if ($validator->fails()) {
                Log::warning('AuthApiController: Update user validation failed.', ['admin_id' => $user->id, 'target_user_id' => $targetUser->id, 'errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $targetUser->update($request->only(['name', 'email', 'role', 'status', 'penduduk_id']));
            Log::info('AuthApiController: User updated successfully.', ['admin_id' => $user->id, 'target_user_id' => $targetUser->id]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui',
                'data' => $targetUser->fresh(['penduduk'])
            ]);

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error updating user.', ['admin_id' => Auth::id(), 'target_user_id' => $targetUser->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user (Admin only)
     */
    public function deleteUser(User $targetUser)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin') {
                Log::warning('AuthApiController: Unauthorized access to deleteUser.', ['user_id' => $user->id, 'role' => $user->role]);
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            // Prevent admin from deleting themselves
            if ($user->id === $targetUser->id) {
                Log::warning('AuthApiController: Admin tried to delete their own account.', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun sendiri'
                ], 400);
            }

            $targetUser->delete();
            Log::info('AuthApiController: User deleted successfully.', ['admin_id' => $user->id, 'deleted_user_id' => $targetUser->id]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error deleting user.', ['admin_id' => Auth::id(), 'target_user_id' => $targetUser->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user password (Admin only)
     */
    public function resetUserPassword(Request $request, User $targetUser)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin') {
                Log::warning('AuthApiController: Unauthorized access to resetUserPassword.', ['user_id' => $user->id, 'role' => $user->role]);
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                Log::warning('AuthApiController: Reset user password validation failed.', ['admin_id' => $user->id, 'target_user_id' => $targetUser->id, 'errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $targetUser->update([
                'password' => Hash::make($request->password)
            ]);
            Log::info('AuthApiController: User password reset by admin successfully.', ['admin_id' => $user->id, 'target_user_id' => $targetUser->id]);

            return response()->json([
                'success' => true,
                'message' => 'Password user berhasil direset'
            ]);

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error resetting user password.', ['admin_id' => Auth::id(), 'target_user_id' => $targetUser->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal reset password user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user status (Admin only)
     */
    public function toggleUserStatus(User $targetUser)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin') {
                Log::warning('AuthApiController: Unauthorized access to toggleUserStatus.', ['user_id' => $user->id, 'role' => $user->role]);
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            // Prevent admin from deactivating themselves
            if ($user->id === $targetUser->id) {
                Log::warning('AuthApiController: Admin tried to toggle their own status.', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengubah status akun sendiri'
                ], 400);
            }

            $newStatus = $targetUser->status === 'aktif' ? 'nonaktif' : 'aktif';
            $targetUser->update(['status' => $newStatus]);
            Log::info('AuthApiController: User status toggled successfully.', ['admin_id' => $user->id, 'target_user_id' => $targetUser->id, 'new_status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => "Status user berhasil diubah menjadi {$newStatus}",
                'data' => $targetUser
            ]);

        } catch (\Exception $e) {
            Log::error('AuthApiController: Error toggling user status.', ['admin_id' => Auth::id(), 'target_user_id' => $targetUser->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
