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
            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'masyarakat', // Default role
                'status' => 'active',
            ]);

            // Link with penduduk
            $penduduk = Penduduk::where('nik', $data['nik'])->first();
            if ($penduduk) {
                $penduduk->update(['user_id' => $user->id]);
            }

            return $user;
        });
    }
}
