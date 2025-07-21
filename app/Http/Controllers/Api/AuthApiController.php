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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetOtpMail;
use App\Mail\RegisterOtpMail; // Import the new mail class

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
                  'email' => ['Akun Anda tidak aktif atau belum diverifikasi. Silakan verifikasi email Anda atau hubungi administrator.'],
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
          'email' => [trans('auth.failed')],
      ]);
  }

  /**
   * Handle user registration - ENHANCED (with OTP)
   */
  public function register(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'name' => 'required|string|max:255',
          'email' => 'required|string|email|max:255|unique:users',
          'password' => 'required|string|min:8|confirmed',
          'role' => 'required|string|in:masyarakat,rt,rw,kades,admin',
          'nik' => 'nullable|string|digits:16|unique:penduduk,nik', // Added nik validation for API registration
      ]);

      if ($validator->fails()) {
          return response()->json([
              'success' => false,
              'message' => 'Validasi gagal',
              'errors' => $validator->errors()
          ], 422);
      }

      try {
          // Generate a 6-digit OTP
          $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

          $user = User::create([
              'name' => $request->name,
              'email' => $request->email,
              'password' => Hash::make($request->password),
              'role' => $request->role,
              'status' => 'pending_verification', // Set status to pending
              'last_activity' => now(),
              'is_online' => false, // User is not online until verified
              'otp' => $otp, // Store OTP
          ]);

          if ($request->filled('nik')) {
              Penduduk::create([
                  'user_id' => $user->id,
                  'nik' => $request->nik,
                  'nama_lengkap' => $request->name, // Assuming name from request is full name
                  'status' => 'aktif',
                  // Add other penduduk fields if available in API request
              ]);
          }

          // Send OTP via email
          try {
              Mail::to($user->email)->send(new RegisterOtpMail($otp));
          } catch (\Exception $e) {
              Log::error('Failed to send registration OTP email via API: ' . $e->getMessage());
              // Optionally, delete the user if email sending fails critically
              // $user->delete();
              return response()->json([
                  'success' => false,
                  'message' => 'Registrasi berhasil, tetapi gagal mengirim kode OTP. Silakan coba lagi atau hubungi administrator.'
              ], 500);
          }

          return response()->json([
              'success' => true,
              'message' => 'Registrasi berhasil! Kode OTP telah dikirim ke email Anda. Silakan verifikasi akun Anda.',
              'user_email' => $user->email, // Return email for OTP verification step
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
   * Handle OTP verification for registration (API)
   */
  public function verifyRegistrationOtp(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'email' => 'required|email|exists:users,email',
          'otp' => 'required|string|digits:6',
      ]);

      if ($validator->fails()) {
          return response()->json([
              'success' => false, 
              'message' => 'Validasi gagal', 
              'errors' => $validator->errors()
          ], 422);
      }

      $user = User::where('email', $request->email)
                  ->where('otp', $request->otp)
                  ->where('status', 'pending_verification')
                  ->first();

      if (!$user) {
          return response()->json([
              'success' => false,
              'message' => 'Kode OTP tidak valid atau akun sudah diverifikasi/tidak ditemukan.'
          ], 400);
      }

      // Optional: Add OTP expiration check here if you store otp_created_at in users table
      /*
      if ($user->otp_created_at->addMinutes(10)->isPast()) {
          $user->update(['otp' => null, 'status' => 'inactive']);
          return response()->json(['success' => false, 'message' => 'Kode OTP telah kadaluarsa. Silakan minta OTP baru.'], 400);
      }
      */

      $user->update([
          'status' => 'active',
          'otp' => null, // Clear OTP after successful verification
          'email_verified_at' => now(), // Mark email as verified
          'is_online' => true, // User is now online
      ]);

      // Optionally, log the user in automatically after verification
      // Auth::login($user);
      // $token = $user->createToken('authToken')->plainTextToken;

      return response()->json([
          'success' => true, 
          'message' => 'Akun Anda berhasil diverifikasi!',
          // 'token' => $token, // If auto-login
          // 'user' => $user->load('penduduk'), // If auto-login
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
   * Handle forgot password request (API - Send OTP)
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

      $user = User::where('email', $request->email)->first();

      if (!$user) {
          return response()->json([
              'success' => false,
              'message' => 'Kami tidak dapat menemukan pengguna dengan alamat email tersebut.'
          ], 404);
      }

      // Generate a 6-digit OTP
      $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

      // Delete any existing OTPs for this email from password_reset_tokens table
      DB::table('password_reset_tokens')->where('email', $request->email)->delete();

      // Store the new OTP in the password_reset_tokens table
      DB::table('password_reset_tokens')->insert([
          'email' => $request->email,
          'token' => $otp, // 'token' field will store the OTP
          'created_at' => now(),
      ]);

      // Send the OTP via email
      try {
          Mail::to($user->email)->send(new PasswordResetOtpMail($otp));
          return response()->json(['success' => true, 'message' => 'Kode OTP telah dikirim ke email Anda.']);
      } catch (\Exception $e) {
          Log::error('Failed to send OTP email: ' . $e->getMessage());
          return response()->json(['success' => false, 'message' => 'Gagal mengirim kode OTP. Silakan coba lagi.'], 500);
      }
  }

  /**
   * Handle reset password request (API - Verify OTP and Reset)
   */
  public function resetPassword(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'email' => 'required|email',
          'otp' => 'required|string|digits:6',
          'password' => 'required|string|min:8|confirmed',
      ]);

      if ($validator->fails()) {
          return response()->json([
              'success' => false, 
              'message' => 'Validasi gagal', 
              'errors' => $validator->errors()
          ], 422);
      }

      // Find the OTP record in password_reset_tokens table
      $otpRecord = DB::table('password_reset_tokens')
                      ->where('email', $request->email)
                      ->where('token', $request->otp)
                      ->first();

      if (!$otpRecord) {
          return response()->json([
              'success' => false,
              'message' => 'Kode OTP tidak valid atau email tidak cocok.'
          ], 400);
      }

      // Check if OTP has expired
      $expirationTime = now()->subMinutes(config('auth.passwords.users.expire'));
      if (empty($otpRecord->created_at) || $otpRecord->created_at < $expirationTime) {
          return response()->json([
              'success' => false,
              'message' => 'Kode OTP telah kadaluarsa. Silakan minta yang baru.'
          ], 400);
      }

      // Find the user
      $user = User::where('email', $request->email)->first();

      if (!$user) {
          return response()->json([
              'success' => false,
              'message' => 'Pengguna tidak ditemukan.'
          ], 404);
      }

      // Update password
      $user->forceFill([
          'password' => Hash::make($request->password),
      ])->setRememberToken(Str::random(60))->save();

      // Delete the used OTP from password_reset_tokens table
      DB::table('password_reset_tokens')->where('email', $request->email)->delete();

      return response()->json(['success' => true, 'message' => 'Password berhasil direset.']);
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
