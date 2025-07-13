<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\PengaturanKas;
use App\Models\PaymentInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of kas
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Kas::with(['penduduk', 'rt.rw']);

        // Filter berdasarkan role
        switch ($user->role) {
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    $query->where('rt_id', $user->penduduk->rtKetua->id);
                } else {
                    return redirect()->back()->with('error', 'Anda tidak memiliki akses RT.');
                }
                break;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                    $query->whereIn('rt_id', $rtIds);
                } else {
                    return redirect()->back()->with('error', 'Anda tidak memiliki akses RW.');
                }
                break;
            case 'masyarakat':
                if ($user->penduduk) {
                    $query->where('penduduk_id', $user->penduduk->id);
                } else {
                    return redirect()->back()->with('error', 'Data penduduk tidak ditemukan.');
                }
                break;
            // admin dan kades bisa lihat semua
        }

        // Filter berdasarkan request
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('search')) {
            $query->whereHas('penduduk', function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%');
            });
        }

        $kas = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('kas.index', compact('kas'));
    }

    /**
     * Show the form for creating a new kas
     */
    public function create()
    {
        $user = Auth::user();
        
        // Hanya RT, RW, Kades, Admin yang bisa create
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk membuat kas.');
        }

        $pengaturanKas = PengaturanKas::first();
        
        // Get penduduk berdasarkan role
        $penduduk = collect();
        $rts = collect();

        switch ($user->role) {
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    $rt = $user->penduduk->rtKetua;
                    $penduduk = Penduduk::whereHas('kk', function($q) use ($rt) {
                        $q->where('rt_id', $rt->id);
                    })->where('status', 'aktif')->get();
                    $rts = collect([$rt]);
                }
                break;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    $rw = $user->penduduk->rwKetua;
                    $rts = $rw->rts;
                    $rtIds = $rts->pluck('id');
                    $penduduk = Penduduk::whereHas('kk', function($q) use ($rtIds) {
                        $q->whereIn('rt_id', $rtIds);
                    })->where('status', 'aktif')->get();
                }
                break;
            case 'kades':
            case 'admin':
                $penduduk = Penduduk::where('status', 'aktif')->get();
                $rts = Rt::all();
                break;
        }

        return view('kas.create', compact('penduduk', 'rts', 'pengaturanKas'));
    }

    /**
     * Store a newly created kas
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        $request->validate([
            'penduduk_id' => 'required|exists:penduduks,id',
            'minggu_ke' => 'required|integer|min:1|max:53',
            'tahun' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'jumlah' => 'required|numeric|min:0',
            'tanggal_jatuh_tempo' => 'required|date',
        ]);

        // Cek duplikasi
        $existing = Kas::where('penduduk_id', $request->penduduk_id)
                       ->where('minggu_ke', $request->minggu_ke)
                       ->where('tahun', $request->tahun)
                       ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Kas untuk penduduk ini pada minggu dan tahun tersebut sudah ada.');
        }

        // Get RT dari penduduk
        $penduduk = Penduduk::with('kk.rt')->findOrFail($request->penduduk_id);
        $rt = $penduduk->kk->rt;

        // Validasi akses berdasarkan role
        switch ($user->role) {
            case 'rt':
                if (!$user->penduduk || !$user->penduduk->rtKetua || $user->penduduk->rtKetua->id !== $rt->id) {
                    return redirect()->back()->with('error', 'Anda hanya bisa menambah kas untuk RT Anda.');
                }
                break;
            case 'rw':
                if (!$user->penduduk || !$user->penduduk->rwKetua || $user->penduduk->rwKetua->id !== $rt->rw_id) {
                    return redirect()->back()->with('error', 'Anda hanya bisa menambah kas untuk RW Anda.');
                }
                break;
        }

        Kas::create([
            'penduduk_id' => $request->penduduk_id,
            'rt_id' => $rt->id,
            'minggu_ke' => $request->minggu_ke,
            'tahun' => $request->tahun,
            'jumlah' => $request->jumlah,
            'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
            'status' => 'belum_bayar',
            'created_by' => $user->id,
        ]);

        return redirect()->route('kas.index')->with('success', 'Kas berhasil dibuat.');
    }

    /**
     * Display the specified kas
     */
    public function show(Kas $kas)
    {
        $kas->load(['penduduk', 'rt.rw', 'createdBy', 'confirmedBy']);
        
        // Check access
        if (!$this->canAccessKas($kas)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat kas ini.');
        }

        return view('kas.show', compact('kas'));
    }

    /**
     * Show the form for editing kas
     */
    public function edit(Kas $kas)
    {
        if (!$this->canAccessKas($kas)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit kas ini.');
        }

        $user = Auth::user();
        $penduduk = collect();

        switch ($user->role) {
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    $rt = $user->penduduk->rtKetua;
                    $penduduk = Penduduk::whereHas('kk', function($q) use ($rt) {
                        $q->where('rt_id', $rt->id);
                    })->where('status', 'aktif')->get();
                }
                break;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    $rw = $user->penduduk->rwKetua;
                    $rtIds = $rw->rts->pluck('id');
                    $penduduk = Penduduk::whereHas('kk', function($q) use ($rtIds) {
                        $q->whereIn('rt_id', $rtIds);
                    })->where('status', 'aktif')->get();
                }
                break;
            case 'kades':
            case 'admin':
                $penduduk = Penduduk::where('status', 'aktif')->get();
                break;
        }

        return view('kas.edit', compact('kas', 'penduduk'));
    }

    /**
     * Update the specified kas
     */
    public function update(Request $request, Kas $kas)
    {
        if (!$this->canAccessKas($kas)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit kas ini.');
        }

        $request->validate([
            'penduduk_id' => 'required|exists:penduduks,id',
            'minggu_ke' => 'required|integer|min:1|max:53',
            'tahun' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'jumlah' => 'required|numeric|min:0',
            'tanggal_jatuh_tempo' => 'required|date',
            'status' => 'required|in:belum_bayar,menunggu_konfirmasi,lunas',
        ]);

        // Cek duplikasi (kecuali untuk kas yang sedang diedit)
        $existing = Kas::where('penduduk_id', $request->penduduk_id)
                       ->where('minggu_ke', $request->minggu_ke)
                       ->where('tahun', $request->tahun)
                       ->where('id', '!=', $kas->id)
                       ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Kas untuk penduduk ini pada minggu dan tahun tersebut sudah ada.');
        }

        $kas->update($request->only([
            'penduduk_id', 'minggu_ke', 'tahun', 'jumlah', 
            'tanggal_jatuh_tempo', 'status'
        ]));

        return redirect()->route('kas.index')->with('success', 'Kas berhasil diperbarui.');
    }

    /**
     * Remove the specified kas
     */
    public function destroy(Kas $kas)
    {
        if (!$this->canAccessKas($kas)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus kas ini.');
        }

        // Hapus file bukti pembayaran jika ada
        if ($kas->bukti_bayar_file) {
            Storage::delete($kas->bukti_bayar_file);
        }

        $kas->delete();

        return redirect()->route('kas.index')->with('success', 'Kas berhasil dihapus.');
    }

    /**
     * Generate kas mingguan untuk semua penduduk
     */
    public function generateWeekly(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $request->validate([
            'minggu_ke' => 'required|integer|min:1|max:53',
            'tahun' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'rt_id' => 'nullable|exists:rts,id',
            'rw_id' => 'nullable|exists:rws,id',
        ]);

        $pengaturanKas = PengaturanKas::first();
        if (!$pengaturanKas) {
            return response()->json(['success' => false, 'message' => 'Pengaturan kas belum dikonfigurasi.']);
        }

        // Hitung tanggal jatuh tempo
        $tanggalJatuhTempo = Carbon::now()->addDays($pengaturanKas->batas_hari_pembayaran);

        $query = Penduduk::where('status', 'aktif')->whereHas('kk');

        // Filter berdasarkan role dan request
        switch ($user->role) {
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    $query->whereHas('kk', function($q) use ($user) {
                        $q->where('rt_id', $user->penduduk->rtKetua->id);
                    });
                } else {
                    return response()->json(['success' => false, 'message' => 'Data RT tidak ditemukan.']);
                }
                break;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                    $query->whereHas('kk', function($q) use ($rtIds) {
                        $q->whereIn('rt_id', $rtIds);
                    });
                } else {
                    return response()->json(['success' => false, 'message' => 'Data RW tidak ditemukan.']);
                }
                break;
        }

        // Filter tambahan berdasarkan request
        if ($request->filled('rt_id')) {
            $query->whereHas('kk', function($q) use ($request) {
                $q->where('rt_id', $request->rt_id);
            });
        }

        if ($request->filled('rw_id')) {
            $query->whereHas('kk.rt', function($q) use ($request) {
                $q->where('rw_id', $request->rw_id);
            });
        }

        $pendudukList = $query->with('kk.rt')->get();
        $created = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($pendudukList as $penduduk) {
                // Cek apakah kas sudah ada
                $existing = Kas::where('penduduk_id', $penduduk->id)
                               ->where('minggu_ke', $request->minggu_ke)
                               ->where('tahun', $request->tahun)
                               ->first();

                if (!$existing) {
                    Kas::create([
                        'penduduk_id' => $penduduk->id,
                        'rt_id' => $penduduk->kk->rt_id,
                        'minggu_ke' => $request->minggu_ke,
                        'tahun' => $request->tahun,
                        'jumlah' => $pengaturanKas->jumlah_kas_mingguan,
                        'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                        'status' => 'belum_bayar',
                        'created_by' => $user->id,
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => "Berhasil generate {$created} kas baru. {$skipped} kas sudah ada sebelumnya."
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal generate kas: ' . $e->getMessage()]);
        }
    }

    /**
     * Bayar kas (untuk RT/RW/Kades/Admin yang bayar langsung)
     */
    public function bayar(Request $request, Kas $kas)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.']);
        }

        if (!$this->canAccessKas($kas)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk kas ini.']);
        }

        $request->validate([
            'tanggal_bayar' => 'required|date',
        ]);

        if ($kas->status === 'lunas') {
            return response()->json(['success' => false, 'message' => 'Kas sudah lunas.']);
        }

        $kas->update([
            'status' => 'lunas',
            'tanggal_bayar' => $request->tanggal_bayar,
            'metode_bayar' => 'tunai',
            'confirmed_by' => $user->id,
            'confirmed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Kas berhasil ditandai lunas.']);
    }

    /**
     * Show payment form for masyarakat
     */
    public function showPaymentForm(Kas $kas)
    {
        $user = Auth::user();
        
        if ($user->role !== 'masyarakat') {
            return redirect()->back()->with('error', 'Halaman ini khusus untuk masyarakat.');
        }

        if (!$user->penduduk || $kas->penduduk_id !== $user->penduduk->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk kas ini.');
        }

        if ($kas->status === 'lunas') {
            return redirect()->back()->with('info', 'Kas sudah lunas.');
        }

        // Get payment info untuk RT ini
        $paymentInfo = PaymentInfo::where('rt_id', $kas->rt_id)
                                  ->where('is_active', true)
                                  ->first();

        return view('kas.payment-form', compact('kas', 'paymentInfo'));
    }

    /**
     * Check if user can access specific kas
     */
    private function canAccessKas(Kas $kas)
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
            case 'kades':
                return true;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    return $kas->rt->rw_id === $user->penduduk->rwKetua->id;
                }
                return false;
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    return $kas->rt_id === $user->penduduk->rtKetua->id;
                }
                return false;
            case 'masyarakat':
                if ($user->penduduk) {
                    return $kas->penduduk_id === $user->penduduk->id;
                }
                return false;
            default:
                return false;
        }
    }
}
