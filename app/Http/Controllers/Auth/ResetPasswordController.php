<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ResetPasswordController extends Controller
{
    /**
     * Tampilkan form verifikasi OTP
     */
    public function showOtpVerificationForm(Request $request)
    {
        $email = $request->session()->get('email') ?? $request->email;
        return view('auth.passwords.otp-verify', compact('email'));
    }

    /**
     * Reset password berdasarkan OTP
     */
 public function resetWithOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|string|digits:6',
        'password' => 'required|string|min:8|confirmed',
    ]);

    // Ambil OTP dari DB
    $otpRecord = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->where('token', $request->otp)
                    ->first();

    if (!$otpRecord) {
        throw ValidationException::withMessages([
            'otp' => ['Kode OTP tidak valid atau tidak cocok dengan email.'],
        ]);
    }

    // Cek kadaluarsa OTP
    $expired = now()->subMinutes(config('auth.passwords.users.expire'));
    if ($otpRecord->created_at < $expired) {
        throw ValidationException::withMessages([
            'otp' => ['Kode OTP telah kadaluarsa.'],
        ]);
    }

    // ✅ Ambil user dan cek null
    $user = User::where('email', $request->email)->first();

    // 🚨 Pastikan ini benar-benar menghentikan
    if (is_null($user)) {
        Log::error('User tidak ditemukan saat reset password.', ['email' => $request->email]);
        throw ValidationException::withMessages([
            'email' => ['Email tidak ditemukan di sistem.'],
        ]);
    }

    try {
        // ✅ Update password
        $user->forceFill([
            'password' => Hash::make($request->password),
        ]);

        $user->setRememberToken(Str::random(60));
        $user->save();

        // Hapus OTP
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Password berhasil direset.');
    } catch (\Exception $e) {
        Log::error('Gagal menyimpan password baru: ' . $e->getMessage());
        throw ValidationException::withMessages([
            'password' => ['Terjadi kesalahan saat menyimpan password baru.'],
        ]);
    }
}

}
