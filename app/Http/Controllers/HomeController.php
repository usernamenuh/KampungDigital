<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Desa;
use App\Models\Kk;
use App\Models\Kas;
use App\Models\Notifikasi;
use App\Models\PengaturanKas;
use App\Models\PaymentInfo;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard based on user role.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('dashboard.admin');
            case 'kades':
                return redirect()->route('dashboard.kades');
            case 'rw':
                return redirect()->route('dashboard.rw');
            case 'rt':
                return redirect()->route('dashboard.rt');
            case 'masyarakat':
                return redirect()->route('dashboard.masyarakat');
            default:
                Auth::logout();
                return redirect('/login')->with('error', 'Peran pengguna tidak dikenal.');
        }
    }

    public function masyarakatDashboard()
    {
        return view('dashboards.masyarakat');
    }

    public function rtDashboard()
    {
        return view('dashboards.rt');
    }

    public function rwDashboard()
    {
        return view('dashboards.rw');
    }

    public function kadesDashboard()
    {
        return view('dashboards.kades');
    }

    public function adminDashboard()
    {
        return view('dashboards.admin');
    }

    public function profile()
    {
        $user = Auth::user();
        $penduduk = $user->penduduk; // Assuming a one-to-one relationship
        return view('profile.index', compact('user', 'penduduk'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $penduduk = $user->penduduk;

        $userValidationRules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ];

        $pendudukValidationRules = [
            'nik' => ['required', 'string', 'max:20', Rule::unique('penduduk')->ignore($penduduk->id, 'id')], // Changed to 'id'
            'nama_lengkap' => 'required|string|max:255', // Changed to nama_lengkap
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string|max:255',
            'agama' => 'required|string|max:50',
            'pendidikan' => 'required|string|max:100',
            'pekerjaan' => 'required|string|max:100',
            'status_perkawinan' => 'required|string|max:50',
            'status_hubungan_keluarga' => 'required|string|max:50',
            'kewarganegaraan' => 'required|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'alamat_lengkap' => 'required|string|max:500',
        ];

        $request->validate(array_merge($userValidationRules, $pendudukValidationRules));

        DB::transaction(function () use ($request, $user, $penduduk) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($penduduk) {
                $penduduk->update($request->only(array_keys($pendudukValidationRules)));
            }
        });

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('profile.index')->with('success', 'Kata sandi berhasil diperbarui.');
    }

    // Admin specific views (if needed, otherwise API handles data)
    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    public function editUser(User $user)
    {
        $roles = ['admin', 'kades', 'rw', 'rt', 'masyarakat'];
        return view('admin.edit-user', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,kades,rw,rt,masyarakat',
            'status' => 'required|in:active,inactive',
        ]);

        $user->update($request->only('name', 'email', 'role', 'status'));

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function settings()
    {
        // Example: Fetching general settings or admin-specific settings
        $pengaturanKas = PengaturanKas::first(); // Assuming a single settings entry for now
        return view('admin.settings', compact('pengaturanKas'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'jumlah_kas_per_minggu' => 'required|numeric|min:0',
            'persentase_denda' => 'required|numeric|min:0|max:100',
            'jatuh_tempo_hari' => 'required|integer|min:1',
        ]);

        $pengaturan = PengaturanKas::firstOrNew([]);
        $pengaturan->jumlah_kas_per_minggu = $request->jumlah_kas_per_minggu;
        $pengaturan->persentase_denda = $request->persentase_denda;
        $pengaturan->jatuh_tempo_hari = $request->jatuh_tempo_hari;
        $pengaturan->save();

        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function reports()
    {
        return view('admin.reports.index');
    }

    public function kasReports(Request $request)
    {
        // Example: Basic kas report data
        $query = Kas::with(['penduduk', 'rt.rw']);

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kasReports = $query->paginate(10);

        return view('admin.reports.kas', compact('kasReports'));
    }

    public function paymentReports(Request $request)
    {
        // Example: Basic payment report data
        $query = Kas::with(['penduduk', 'rt.rw'])
                    ->whereNotNull('tanggal_bayar');

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $paymentReports = $query->paginate(10);

        return view('admin.reports.payments', compact('paymentReports'));
    }

    public function exportReports(Request $request)
    {
        // Example: Simple CSV export
        $type = $request->query('type');
        $tahun = $request->query('tahun');
        $status = $request->query('status');
        $metode_bayar = $request->query('metode_bayar');

        if ($type === 'kas') {
            $query = Kas::with(['penduduk', 'rt.rw']);
            if ($tahun) $query->where('tahun', $tahun);
            if ($status) $query->where('status', $status);

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="kas_report_' . Carbon::now()->format('Ymd_His') . '.csv"',
            ];

            $callback = function() use ($query) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Penduduk', 'NIK', 'RT/RW', 'Minggu Ke', 'Tahun', 'Jumlah', 'Denda', 'Total', 'Jatuh Tempo', 'Status', 'Tanggal Bayar', 'Metode Bayar']);

                foreach ($query->cursor() as $kas) {
                    fputcsv($file, [
                        $kas->id, // Changed to id
                        $kas->penduduk->nama_lengkap ?? 'N/A', // Changed to nama_lengkap
                        $kas->penduduk->nik ?? 'N/A',
                        'RT ' . ($kas->rt->no_rt ?? 'N/A') . '/RW ' . ($kas->rt->rw->no_rw ?? 'N/A'),
                        $kas->minggu_ke,
                        $kas->tahun,
                        $kas->jumlah,
                        $kas->denda,
                        $kas->total_bayar,
                        $kas->tanggal_jatuh_tempo_formatted,
                        $kas->status_text,
                        $kas->tanggal_bayar_formatted,
                        $kas->metode_bayar_formatted,
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } elseif ($type === 'payments') {
            $query = Kas::with(['penduduk', 'rt.rw'])->whereNotNull('tanggal_bayar');
            if ($tahun) $query->where('tahun', $tahun);
            if ($metode_bayar) $query->where('metode_bayar', $metode_bayar);

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="payments_report_' . Carbon::now()->format('Ymd_His') . '.csv"',
            ];

            $callback = function() use ($query) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Penduduk', 'NIK', 'RT/RW', 'Minggu Ke', 'Tahun', 'Jumlah Dibayar', 'Metode Bayar', 'Tanggal Bayar', 'Status']);

                foreach ($query->cursor() as $kas) {
                    fputcsv($file, [
                        $kas->id, // Changed to id
                        $kas->penduduk->nama_lengkap ?? 'N/A', // Changed to nama_lengkap
                        $kas->penduduk->nik ?? 'N/A',
                        'RT ' . ($kas->rt->no_rt ?? 'N/A') . '/RW ' . ($kas->rt->rw->no_rw ?? 'N/A'),
                        $kas->minggu_ke,
                        $kas->tahun,
                        $kas->jumlah_dibayar,
                        $kas->metode_bayar_formatted,
                        $kas->tanggal_bayar_formatted,
                        $kas->status_text,
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return back()->with('error', 'Tipe laporan tidak valid.');
    }
}
