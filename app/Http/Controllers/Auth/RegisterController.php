<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Penduduk;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterOtpMail;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Check if NIK exists in database
     */
    public function checkNik(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16|regex:/^[0-9]{16}$/'
        ]);

        try {
            $penduduk = Penduduk::where('nik', $request->nik)->first();
            
            if (!$penduduk) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIK tidak ditemukan dalam database penduduk.'
                ]);
            }

            if ($penduduk->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIK sudah terdaftar sebagai pengguna.'
                ]);
            }

            if ($penduduk->status !== 'aktif') {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penduduk dengan NIK ini tidak aktif.'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'NIK valid dan dapat digunakan untuk registrasi.',
                'data' => [
                    'nama_lengkap' => $penduduk->nama_lengkap,
                    'tempat_lahir' => $penduduk->tempat_lahir,
                    'tanggal_lahir' => $penduduk->tanggal_lahir
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error validating NIK in RegisterController@checkNik: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memvalidasi NIK.'
            ], 500);
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]{16}$/',
                function ($attribute, $value, $fail) {
                    $penduduk = Penduduk::where('nik', $value)->first();
                    if (!$penduduk) {
                        $fail('NIK tidak ditemukan dalam database penduduk.');
                    } elseif ($penduduk->user_id) {
                        $fail('NIK sudah terdaftar sebagai pengguna.');
                    } elseif ($penduduk->status !== 'aktif') {
                        $fail('Data penduduk dengan NIK ini tidak aktif.');
                    }
                }
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nik.required' => 'NIK wajib diisi',
            'nik.size' => 'NIK harus 16 digit',
            'nik.regex' => 'NIK harus berupa angka 16 digit',
            'name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Generate a 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Create user with pending_verification status and OTP
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'masyarakat', // Default role
                'status' => 'pending_verification', // Set status to pending
                'otp' => $otp, // Store OTP
            ]);

            // Link with penduduk
            $penduduk = Penduduk::where('nik', $data['nik'])->first();
            if ($penduduk) {
                $penduduk->update(['user_id' => $user->id]);
            }

            // Send OTP via email
            try {
                Mail::to($user->email)->send(new RegisterOtpMail($otp));
            } catch (\Exception $e) {
                Log::error('Failed to send registration OTP email: ' . $e->getMessage());
                // Optionally, delete the user if email sending fails critically
                // $user->delete();
                // throw new \Exception('Gagal mengirim kode OTP. Silakan coba lagi.');
            }

            return $user;
        });
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // Instead of logging in, redirect to OTP verification page
        return redirect()->route('register.otp.form')->with([
            'email' => $user->email,
            'success' => 'Registrasi berhasil! Kode OTP telah dikirim ke email Anda. Silakan verifikasi akun Anda.'
        ]);
    }

    /**
     * Show the OTP verification form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showOtpForm(Request $request)
    {
        // Ensure email is passed from the registration process or session
        if (!$request->session()->has('email')) {
            return redirect()->route('register'); // Redirect back to registration if no email in session
        }
        return view('auth.register-otp-verify', ['email' => $request->session()->get('email')]);
    }

    /**
     * Handle OTP verification for registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|digits:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits' => 'Kode OTP harus 6 digit angka.',
            'email.exists' => 'Email tidak terdaftar.',
        ]);

        $user = User::where('email', $request->email)
                    ->where('otp', $request->otp)
                    ->where('status', 'pending_verification')
                    ->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluarsa.'])->withInput();
        }

        // Check OTP expiration (e.g., 10 minutes)
        // You might want to add a 'otp_created_at' column to users table for this
        // For now, we'll assume the OTP is valid if it matches and status is pending.
        // If you add 'otp_created_at', uncomment and adjust the logic below:
        /*
        if ($user->otp_created_at->addMinutes(10)->isPast()) {
            $user->update(['otp' => null, 'status' => 'inactive']); // Invalidate OTP and set status to inactive
            return back()->withErrors(['otp' => 'Kode OTP telah kadaluarsa. Silakan daftar ulang atau minta OTP baru.'])->withInput();
        }
        */

        $user->update([
            'status' => 'active',
            'otp' => null, // Clear OTP after successful verification
            'email_verified_at' => now(), // Mark email as verified
        ]);

        return redirect()->route('login')->with('success', 'Akun Anda berhasil diverifikasi! Silakan login.');
    }

    /**
     * Resend OTP for registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)
                    ->where('status', 'pending_verification')
                    ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Pengguna tidak ditemukan atau sudah diverifikasi.'], 404);
        }

        // Generate new OTP
        $newOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update(['otp' => $newOtp]); // Update OTP

        try {
            Mail::to($user->email)->send(new RegisterOtpMail($newOtp));
            return response()->json(['success' => true, 'message' => 'Kode OTP baru telah dikirim ke email Anda.']);
        } catch (\Exception $e) {
            Log::error('Failed to resend registration OTP email: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengirim ulang kode OTP. Silakan coba lagi.'], 500);
        }
    }
}
