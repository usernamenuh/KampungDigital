<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link (now OTP).
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a password reset OTP to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendOtp(Request $request)
{
    $request->validate(['email' => 'required|email'], [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        throw ValidationException::withMessages([
            'email' => ['Kami tidak dapat menemukan pengguna dengan alamat email tersebut.'],
        ]);
    }

    // Generate OTP 6 digit
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    try {
        // Hapus OTP lama
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Simpan OTP baru
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $otp,
            'created_at' => now(),
        ]);

        // Kirim OTP ke email user
        Mail::to($user->email)->send(new PasswordResetOtpMail($otp));

        session(['email' => $request->email]); // Simpan email di session

        return redirect()->route('password.verify-otp')
            ->with('status', 'Kode OTP telah dikirim ke email Anda.');
    } catch (\Exception $e) {
        Log::error('Gagal mengirim OTP: ' . $e->getMessage());

        throw ValidationException::withMessages([
            'email' => ['Terjadi kesalahan saat mengirim OTP. Silakan coba lagi.'],
        ]);
    }
}
}