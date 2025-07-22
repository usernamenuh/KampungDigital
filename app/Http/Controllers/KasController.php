<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Desa;
use App\Models\User;
use App\Models\Notifikasi;
use App\Models\Kk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\PaymentInfo;
use App\Jobs\SendKasEmailNotification;

class KasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Helper method to get IDs of all official residents (Kepala Desa, Ketua RW, Ketua RT)
     */
    private function getOfficialPendudukIds(): array
    {
        $officialPendudukIds = collect();
        
        // Get kepala desa IDs
        $officialPendudukIds = $officialPendudukIds->merge(
            Desa::whereNotNull('kepala_desa_id')->pluck('kepala_desa_id')
        );
        
        // Get ketua RW IDs
        $officialPendudukIds = $officialPendudukIds->merge(
            Rw::whereNotNull('ketua_rw_id')->pluck('ketua_rw_id')
        );
        
        // Get ketua RT IDs
        $officialPendudukIds = $officialPendudukIds->merge(
            Rt::whereNotNull('ketua_rt_id')->pluck('ketua_rt_id')
        );
        
        return $officialPendudukIds->filter()->unique()->toArray();
    }

    /**
     * Helper method to send kas email notification
     */
    private function sendKasEmailNotification(Kas $kas, string $type, array $additionalData = [])
    {
        if (!$kas->penduduk || !$kas->penduduk->user || !$kas->penduduk->user->email) {
            Log::warning('Cannot send kas email: user or email not found', ['kas_id' => $kas->id]);
            return false;
        }

        // Dispatch the job to send email
        SendKasEmailNotification::dispatch($kas, $type, $additionalData);

        return true;
    }

    /**
     * Tampilkan daftar kas
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Kas::with(['penduduk.user', 'penduduk.kk.rt.rw', 'rt.rw']);

        // Filter berdasarkan role
        switch ($user->role) {
            case 'admin':
            case 'kades':
                // Admin dan Kades bisa lihat semua kas
                break;
            case 'rw':
                // RW hanya bisa lihat kas di RW mereka
                if ($user->penduduk && $user->penduduk->kk && $user->penduduk->kk->rt) {
                    $rwId = $user->penduduk->kk->rt->rw_id;
                    $query->whereHas('rt', function($q) use ($rwId) {
                        $q->where('rw_id', $rwId);
                    });
                }
                break;
            case 'rt':
                // RT hanya bisa lihat kas di RT mereka
                if ($user->penduduk && $user->penduduk->kk) {
                    $rtId = $user->penduduk->kk->rt_id;
                    $query->where('rt_id', $rtId);
                }
                break;
            case 'masyarakat':
                // Masyarakat hanya bisa lihat kas mereka sendiri
                if ($user->penduduk) {
                    $query->where('penduduk_id', $user->penduduk->id);
                }
                break;
        }

        // Filter berdasarkan parameter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }

        if ($request->filled('minggu_ke')) {
            $query->where('minggu_ke', $request->minggu_ke);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Filter berdasarkan email (khusus admin)
        if ($request->filled('email') && in_array($user->role, ['admin', 'kades'])) {
            $query->whereHas('penduduk.user', function($q) use ($request) {
                $q->where('email', 'like', '%' . $request->email . '%');
            });
        }

        // Filter berdasarkan nama penduduk
        if ($request->filled('nama')) {
            $query->whereHas('penduduk', function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->nama . '%');
            });
        }

        // Urutkan berdasarkan terbaru
        $kas = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate statistics with same filtering - Create a fresh query for stats
        $statsQuery = Kas::query();
        
        // Apply same role-based filtering for stats
        switch ($user->role) {
            case 'admin':
            case 'kades':
                // Admin dan Kades bisa lihat semua kas
                break;
            case 'rw':
                if ($user->penduduk && $user->penduduk->kk && $user->penduduk->kk->rt) {
                    $rwId = $user->penduduk->kk->rt->rw_id;
                    $statsQuery->whereHas('rt', function($q) use ($rwId) {
                        $q->where('rw_id', $rwId);
                    });
                }
                break;
            case 'rt':
                if ($user->penduduk && $user->penduduk->kk) {
                    $rtId = $user->penduduk->kk->rt_id;
                    $statsQuery->where('rt_id', $rtId);
                }
                break;
            case 'masyarakat':
                if ($user->penduduk) {
                    $statsQuery->where('penduduk_id', $user->penduduk->id);
                }
                break;
        }

        // Apply same filters to stats
        if ($request->filled('status')) {
            $statsQuery->where('status', $request->status);
        }
        if ($request->filled('rt_id')) {
            $statsQuery->where('rt_id', $request->rt_id);
        }
        if ($request->filled('minggu_ke')) {
            $statsQuery->where('minggu_ke', $request->minggu_ke);
        }
        if ($request->filled('tahun')) {
            $statsQuery->where('tahun', $request->tahun);
        }
        if ($request->filled('email') && in_array($user->role, ['admin', 'kades'])) {
            $statsQuery->whereHas('penduduk.user', function($q) use ($request) {
                $q->where('email', 'like', '%' . $request->email . '%');
            });
        }
        if ($request->filled('nama')) {
            $statsQuery->whereHas('penduduk', function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->nama . '%');
            });
        }

        // Calculate statistics
        $totalKas = $statsQuery->count();
        $lunasCount = (clone $statsQuery)->where('status', 'lunas')->count();
        $belumBayarCount = (clone $statsQuery)->where('status', 'belum_bayar')->count();
        $terlambatCount = (clone $statsQuery)->where('status', 'terlambat')->count();
        $ditolakCount = (clone $statsQuery)->where('status', 'ditolak')->count(); // Added ditolak count
        $menungguKonfirmasiCount = (clone $statsQuery)->where('status', 'menunggu_konfirmasi')->count(); // Added menunggu_konfirmasi count
        
        // Calculate amounts
        $totalTerkumpul = (clone $statsQuery)->where('status', 'lunas')->sum('jumlah');
        $totalOutstanding = (clone $statsQuery)->whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->sum('jumlah');

        // Calculate total nominal tertagih (sum of all kas amounts, regardless of status)
        $totalNominalTertagihQuery = Kas::query();
        // Apply same role-based filtering for total nominal tertagih
        switch ($user->role) {
            case 'admin':
            case 'kades':
                break;
            case 'rw':
                if ($user->penduduk && $user->penduduk->kk && $user->penduduk->kk->rt) {
                    $rwId = $user->penduduk->kk->rt->rw_id;
                    $totalNominalTertagihQuery->whereHas('rt', function($q) use ($rwId) {
                        $q->where('rw_id', $rwId);
                    });
                }
                break;
            case 'rt':
                if ($user->penduduk && $user->penduduk->kk) {
                    $rtId = $user->penduduk->kk->rt_id;
                    $totalNominalTertagihQuery->where('rt_id', $rtId);
                }
                break;
            case 'masyarakat':
                if ($user->penduduk) {
                    $totalNominalTertagihQuery->where('penduduk_id', $user->penduduk->id);
                }
                break;
        }
        // Apply same request filters for total nominal tertagih
        if ($request->filled('status')) {
            $totalNominalTertagihQuery->where('status', $request->status);
        }
        if ($request->filled('rt_id')) {
            $totalNominalTertagihQuery->where('rt_id', $request->rt_id);
        }
        if ($request->filled('minggu_ke')) {
            $totalNominalTertagihQuery->where('minggu_ke', $request->minggu_ke);
        }
        if ($request->filled('tahun')) {
            $totalNominalTertagihQuery->where('tahun', $request->tahun);
        }
        if ($request->filled('email') && in_array($user->role, ['admin', 'kades'])) {
            $totalNominalTertagihQuery->whereHas('penduduk.user', function($q) use ($request) {
                $q->where('email', 'like', '%' . $request->email . '%');
            });
        }
        if ($request->filled('nama')) {
            $totalNominalTertagihQuery->whereHas('penduduk', function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->nama . '%');
            });
        }
        $totalNominalTertagih = $totalNominalTertagihQuery->sum('jumlah');

        $stats = [
            'total' => $totalKas,
            'lunas' => $lunasCount,
            'belum_bayar' => $belumBayarCount,
            'terlambat' => $terlambatCount,
            'ditolak' => $ditolakCount, // Add ditolak to stats
            'menunggu_konfirmasi' => $menungguKonfirmasiCount, // Add menunggu_konfirmasi to stats
            'total_terkumpul' => $totalTerkumpul ?: 0,
            'total_outstanding' => $totalOutstanding ?: 0,
            'total_nominal_tertagih' => $totalNominalTertagih ?: 0, // Add total nominal tertagih
        ];

        // Daftar RT untuk filter (berdasarkan role)
        $rtList = collect();
        if (in_array($user->role, ['admin', 'kades'])) {
            $rtList = Rt::with('rw')->orderBy('no_rt')->get();
        } elseif ($user->role === 'rw' && $user->penduduk && $user->penduduk->kk) {
            $rwId = $user->penduduk->kk->rt->rw_id;
            $rtList = Rt::where('rw_id', $rwId)->with('rw')->orderBy('no_rt')->get();
        } elseif ($user->role === 'rt' && $user->penduduk && $user->penduduk->kk) {
            $rtId = $user->penduduk->kk->rt_id;
            $rtList = Rt::where('id', $rtId)->with('rw')->get();
        }

        return view('kas.index', compact('kas', 'stats', 'rtList'));
    }

    /**
     * Form buat kas baru
     */
    public function create()
    {
        $user = Auth::user();
        
        // Hanya admin, kades, rw, rt yang bisa buat kas
        if (!in_array($user->role, ['admin', 'kades', 'rw', 'rt'])) {
            abort(403, 'Anda tidak memiliki akses untuk membuat kas');
        }

        // Daftar RT berdasarkan role
        $rtList = collect();
        if (in_array($user->role, ['admin', 'kades'])) {
            // Admin dan Kades bisa lihat semua RT
            $rtList = Rt::with(['rw.desa'])
                ->join('rws', 'rts.rw_id', '=', 'rws.id')
                ->orderBy('rws.no_rw')
                ->orderBy('rts.no_rt')
                ->select('rts.*')
                ->get();
        } elseif ($user->role === 'rw' && $user->penduduk && $user->penduduk->kk) {
            $rwId = $user->penduduk->kk->rt->rw_id;
            $rtList = Rt::where('rw_id', $rwId)->with('rw')->orderBy('no_rt')->get();
        } elseif ($user->role === 'rt' && $user->penduduk && $user->penduduk->kk) {
            $rtId = $user->penduduk->kk->rt_id;
            $rtList = Rt::where('id', $rtId)->with('rw')->get();
        }

        return view('kas.create', compact('rtList'));
    }

    /**
     * Get resident info by RT (AJAX) - Now filters out officials
     */
    public function getResidentInfo(Request $request)
    {
        try {
            $rtId = $request->rt_id;
            
            if (!$rtId) {
                return response()->json(['success' => false, 'message' => 'RT tidak dipilih']);
            }

            // Ambil detail RT
            $rt = Rt::with('rw')->find($rtId);
            
            if (!$rt) {
                return response()->json(['success' => false, 'message' => 'RT tidak ditemukan']);
            }

            // Get IDs of officials to exclude
            $officialPendudukIds = $this->getOfficialPendudukIds();

            // Ambil semua penduduk aktif di RT ini melalui KK, kecuali yang menjabat
            $residents = Penduduk::with(['user', 'kk'])
                ->whereHas('kk', function($query) use ($rtId) {
                    $query->where('rt_id', $rtId);
                })
                ->where('status', 'aktif')
                ->whereNotIn('id', $officialPendudukIds) // Exclude officials
                ->orderBy('nama_lengkap')
                ->get();

            // Hitung statistik
            $stats = [
                'total' => $residents->count(),
                'active' => $residents->where('status', 'aktif')->count(),
                'with_accounts' => $residents->filter(function($resident) {
                    return $resident->user !== null;
                })->count()
            ];

            return response()->json([
                'success' => true,
                'rt_info' => "RT {$rt->no_rt} / RW {$rt->rw->no_rw}",
                'stats' => $stats,
                'residents' => $residents->map(function($resident) {
                    return [
                        'id' => $resident->id,
                        'nama_lengkap' => $resident->nama_lengkap,
                        'nik' => $resident->nik,
                        'user' => $resident->user ? [
                            'email' => $resident->user->email,
                            'status' => $resident->user->status
                        ] : null
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getResidentInfo: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Simpan kas baru - Now only creates kas for masyarakat (non-officials) with email notifications
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validasi akses
        if (!in_array($user->role, ['admin', 'kades', 'rw', 'rt'])) {
            abort(403, 'Anda tidak memiliki akses untuk membuat kas');
        }

        $request->validate([
            'rt_id' => 'required|exists:rts,id',
            'jumlah' => 'required|numeric|min:1000',
            'minggu_ke' => 'required|integer|min:1|max:53',
            'tahun' => 'required|integer|min:2020|max:2030',
            'tanggal_jatuh_tempo' => 'required|date|after:today',
            'keterangan' => 'nullable|string|max:500',
            'send_email_notification' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $rt = Rt::findOrFail($request->rt_id);
        
            // Validasi akses RT berdasarkan role (admin bisa akses semua)
            if ($user->role === 'rw' && $user->penduduk && $user->penduduk->kk) {
                $userRwId = $user->penduduk->kk->rt->rw_id;
                if ($rt->rw_id !== $userRwId) {
                    throw new \Exception('Anda hanya bisa membuat kas untuk RT di RW Anda');
                }
            } elseif ($user->role === 'rt' && $user->penduduk && $user->penduduk->kk) {
                $userRtId = $user->penduduk->kk->rt_id;
                if ($rt->id !== $userRtId) {
                    throw new \Exception('Anda hanya bisa membuat kas untuk RT Anda');
                }
            }
            // Admin dan Kades tidak perlu validasi RT

            // Get IDs of officials to exclude
            $officialPendudukIds = $this->getOfficialPendudukIds();

            // Ambil semua penduduk aktif di RT ini melalui KK, kecuali yang menjabat
            $pendudukList = Penduduk::whereHas('kk', function($query) use ($request) {
                $query->where('rt_id', $request->rt_id)
                      ->where('status', 'aktif');
            })
            ->where('status', 'aktif')
            ->whereNotIn('id', $officialPendudukIds) // Exclude officials
            ->get();

            if ($pendudukList->isEmpty()) {
                throw new \Exception('Tidak ada penduduk masyarakat aktif di RT ini. Pastikan ada penduduk aktif (bukan pejabat) di RT yang dipilih.');
            }

            $createdCount = 0;
            $notificationCount = 0;
            $emailCount = 0;
            $duplicateCount = 0;
            $sendEmailNotification = $request->boolean('send_email_notification');

            foreach ($pendudukList as $penduduk) {
                // Cek apakah kas untuk periode ini sudah ada
                $existingKas = Kas::where('penduduk_id', $penduduk->id)
                    ->where('minggu_ke', $request->minggu_ke)
                    ->where('tahun', $request->tahun)
                    ->first();

                if (!$existingKas) {
                    // Buat kas baru
                    $kas = Kas::create([
                        'penduduk_id' => $penduduk->id,
                        'rt_id' => $request->rt_id,
                        'rw_id' => $rt->rw_id,
                        'minggu_ke' => $request->minggu_ke,
                        'tahun' => $request->tahun,
                        'jumlah' => $request->jumlah,
                        'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                        'status' => 'belum_bayar',
                        'keterangan' => $request->keterangan,
                    ]);

                    $createdCount++;

                    // Kirim notifikasi ke warga jika punya akun dan aktif
                    if ($penduduk->user && $penduduk->user->status === 'active') {
                        Notifikasi::create([
                            'user_id' => $penduduk->user->id,
                            'judul' => 'Tagihan Kas Baru',
                            'pesan' => "Tagihan kas minggu ke-{$request->minggu_ke} sebesar Rp " . number_format($request->jumlah, 0, ',', '.') . " telah dibuat. Jatuh tempo: " . Carbon::parse($request->tanggal_jatuh_tempo)->format('d/m/Y') . ($request->keterangan ? ". {$request->keterangan}" : ''),
                            'tipe' => 'info',
                            'kategori' => 'kas',
                            'data' => json_encode([
                                'kas_id' => $kas->id,
                                'jumlah' => $request->jumlah,
                                'minggu_ke' => $request->minggu_ke,
                                'tahun' => $request->tahun,
                                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                            ])
                        ]);

                        $notificationCount++;

                        // Send email notification if enabled and user has email
                        if ($sendEmailNotification && $penduduk->user->email) {
                            $daysUntilDue = now()->diffInDays($request->tanggal_jatuh_tempo, false);
                            
                            $this->sendKasEmailNotification($kas, 'kas_created', [
                                'days_until_due' => max(0, $daysUntilDue),
                                'is_new_kas' => true
                            ]);
                            
                            $emailCount++;
                        }
                    }
                } else {
                    $duplicateCount++;
                }
            }

            DB::commit();

            $message = "Berhasil membuat {$createdCount} tagihan kas untuk penduduk masyarakat, mengirim {$notificationCount} notifikasi sistem";
            if ($emailCount > 0) {
                $message .= " dan {$emailCount} email";
            }
            if ($duplicateCount > 0) {
                $message .= ". {$duplicateCount} kas sudah ada sebelumnya";
            }

            return redirect()->route('kas.index')->with([
                'success' => $message,
                'kas_created' => $createdCount,
                'notifications_sent' => $notificationCount,
                'emails_sent' => $emailCount,
                'total_amount' => $createdCount * $request->jumlah,
                'show_success_modal' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating kas: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Generate kas mingguan - Now only creates kas for masyarakat (non-officials) with email notifications
     */
    public function generateWeekly(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'kades', 'rw', 'rt'])) {
            abort(403, 'Anda tidak memiliki akses untuk generate kas mingguan');
        }

        $request->validate([
            'rt_id' => 'required|exists:rts,id',
            'jumlah' => 'required|numeric|min:1000',
            'tahun' => 'required|integer|min:2020|max:2030',
            'minggu_mulai' => 'required|integer|min:1|max:52',
            'minggu_selesai' => 'required|integer|min:1|max:52|gte:minggu_mulai',
            'tanggal_jatuh_tempo_awal' => 'required|date',
            'send_email_notification' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $rt = Rt::findOrFail($request->rt_id);
        
            // Validasi akses RT berdasarkan role (admin bisa akses semua)
            if ($user->role === 'rw' && $user->penduduk && $user->penduduk->kk) {
                $userRwId = $user->penduduk->kk->rt->rw_id;
                if ($rt->rw_id !== $userRwId) {
                    throw new \Exception('Anda hanya bisa generate kas untuk RT di RW Anda');
                }
            } elseif ($user->role === 'rt' && $user->penduduk && $user->penduduk->kk) {
                $userRtId = $user->penduduk->kk->rt_id;
                if ($rt->id !== $userRtId) {
                    throw new \Exception('Anda hanya bisa generate kas untuk RT Anda');
                }
            }
            // Admin dan Kades tidak perlu validasi RT
        
            $totalCreated = 0;
            $totalNotifications = 0;
            $totalEmails = 0;
            $totalWeeks = $request->minggu_selesai - $request->minggu_mulai + 1;
            $sendEmailNotification = $request->boolean('send_email_notification');

            // Get IDs of officials to exclude
            $officialPendudukIds = $this->getOfficialPendudukIds();

            // Ambil semua penduduk aktif di RT ini melalui KK, kecuali yang menjabat
            $pendudukList = Penduduk::whereHas('kk', function($query) use ($request) {
                $query->where('rt_id', $request->rt_id)
                      ->where('status', 'aktif');
            })
            ->where('status', 'aktif')
            ->whereNotIn('id', $officialPendudukIds) // Exclude officials
            ->get();

            if ($pendudukList->isEmpty()) {
                throw new \Exception('Tidak ada penduduk masyarakat aktif di RT ini. Pastikan ada penduduk aktif (bukan pejabat) di RT yang dipilih.');
            }

            // Calculate initial due date
            $initialDueDate = Carbon::parse($request->tanggal_jatuh_tempo_awal);

            for ($minggu = $request->minggu_mulai; $minggu <= $request->minggu_selesai; $minggu++) {
                // Calculate due date for each week based on the initial date
                $jatuhTempo = $initialDueDate->copy()->addWeeks($minggu - $request->minggu_mulai);

                foreach ($pendudukList as $penduduk) {
                    // Cek apakah kas untuk minggu ini sudah ada
                    $existingKas = Kas::where('penduduk_id', $penduduk->id)
                        ->where('minggu_ke', $minggu)
                        ->where('tahun', $request->tahun)
                        ->first();

                    if (!$existingKas) {
                        $kas = Kas::create([
                            'penduduk_id' => $penduduk->id,
                            'rt_id' => $request->rt_id,
                            'rw_id' => $rt->rw_id,
                            'minggu_ke' => $minggu,
                            'tahun' => $request->tahun,
                            'jumlah' => $request->jumlah,
                            'tanggal_jatuh_tempo' => $jatuhTempo,
                            'status' => 'belum_bayar',
                            'keterangan' => $request->keterangan ?? "Generate kas mingguan oleh {$user->name}",
                        ]);

                        $totalCreated++;

                        // Kirim notifikasi ke warga jika punya akun dan aktif
                        if ($penduduk->user && $penduduk->user->status === 'active') {
                            Notifikasi::create([
                                'user_id' => $penduduk->user->id,
                                'judul' => 'Tagihan Kas Mingguan',
                                'pesan' => "Tagihan kas minggu ke-{$minggu} tahun {$request->tahun} sebesar Rp " . number_format($request->jumlah, 0, ',', '.') . " telah dibuat. Jatuh tempo: " . $jatuhTempo->format('d/m/Y'),
                                'tipe' => 'info',
                                'kategori' => 'kas',
                                'data' => json_encode([
                                    'kas_id' => $kas->id,
                                    'jumlah' => $request->jumlah,
                                    'minggu_ke' => $minggu,
                                    'tahun' => $request->tahun,
                                    'tanggal_jatuh_tempo' => $jatuhTempo->toDateString(),
                                ])
                            ]);

                            $totalNotifications++;

                            // Send email notification if enabled and user has email
                            if ($sendEmailNotification && $penduduk->user->email) {
                                $daysUntilDue = now()->diffInDays($jatuhTempo, false);
                                
                                $this->sendKasEmailNotification($kas, 'kas_created', [
                                    'days_until_due' => max(0, $daysUntilDue),
                                    'is_new_kas' => true
                                ]);
                                
                                $totalEmails++;
                            }
                        }
                    }
                }
            }

            DB::commit();

            $message = "Berhasil generate {$totalCreated} tagihan kas mingguan untuk penduduk masyarakat, mengirim {$totalNotifications} notifikasi sistem";
            if ($totalEmails > 0) {
                $message .= " dan {$totalEmails} email";
            }

            return redirect()->route('kas.index')->with([
                'success' => $message,
                'total_weeks' => $totalWeeks,
                'kas_created' => $totalCreated,
                'notifications_sent' => $totalNotifications,
                'emails_sent' => $totalEmails,
                'total_amount' => $totalCreated * $request->jumlah,
                'show_success_modal' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating weekly kas: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Show kas details
     */
    public function show(Kas $kas)
    {
        $user = Auth::user();
        
        // Check access permission
        if (!$this->canAccessKas($kas, $user)) {
            abort(403, 'Anda tidak memiliki akses untuk melihat kas ini');
        }

        // Load relasi dengan null checks
        $kas->load(['penduduk.user', 'rt.rw']);
        
        return view('kas.show', compact('kas'));
    }

    /**
     * Show edit form
     */
    public function edit(Kas $kas)
    {
        $user = Auth::user();
        
        // Only admin, kades, rw, rt can edit
        if (!in_array($user->role, ['admin', 'kades', 'rw', 'rt'])) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit kas');
        }

        // Check access permission
        if (!$this->canAccessKas($kas, $user)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit kas ini');
        }

        // Load relasi dengan null checks
        $kas->load(['penduduk.user', 'rt.rw']);
        
        // Get all penduduk for dropdown
        $penduduk = Penduduk::with('kk.rt')->where('status', 'aktif')->get();
        
        // Get all RT for dropdown
        $rt = Rt::with('rw')->get();
        
        return view('kas.edit', compact('kas', 'penduduk', 'rt'));
    }

    /**
     * Update kas - FIXED to use markAsLunas() method with email notifications
     */
    public function update(Request $request, Kas $kas)
    {
        $user = Auth::user();
        
        // Only admin, kades, rw, rt can update
        if (!in_array($user->role, ['admin', 'kades', 'rw', 'rt'])) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate kas');
        }

        // Check access permission
        if (!$this->canAccessKas($kas, $user)) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate kas ini');
        }

        $request->validate([
            'penduduk_id' => 'required|exists:penduduks,id',
            'rt_id' => 'required|exists:rts,id',
            'jumlah' => 'required|numeric|min:1000',
            'status' => ['required', Rule::in(['belum_bayar', 'lunas', 'terlambat', 'ditolak', 'menunggu_konfirmasi'])],
            'minggu_ke' => 'required|integer|min:1|max:53',
            'tahun' => 'required|integer|min:2020|max:2030',
            'tanggal_jatuh_tempo' => 'required|date',
            'tanggal_bayar' => 'nullable|date',
            'metode_bayar' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:500',
            'bukti_bayar_file' => 'nullable|string|max:1000',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Get RT info for rw_id
            $rt = Rt::findOrFail($request->rt_id);
            $previousStatus = $kas->status;

            $updateData = [
                'penduduk_id' => $request->penduduk_id,
                'rt_id' => $request->rt_id,
                'rw_id' => $rt->rw_id,
                'jumlah' => $request->jumlah,
                'minggu_ke' => $request->minggu_ke,
                'tahun' => $request->tahun,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'keterangan' => $request->keterangan,
                'bukti_bayar_file' => $request->bukti_bayar_file,
                'rejection_reason' => $request->rejection_reason,
            ];

            // Handle status changes with proper saldo management
            if ($request->status === 'lunas' && $previousStatus !== 'lunas') {
                // Changing to lunas - use markAsLunas method
                $kas->update($updateData); // Update other fields first
                $kas->markAsLunas($user->id, "Status diubah menjadi lunas oleh {$user->name}");
                
                if ($request->metode_bayar) {
                    $kas->update(['metode_bayar' => $request->metode_bayar]);
                }
                
            } elseif ($previousStatus === 'lunas' && $request->status !== 'lunas') {
                // Changing from lunas to non-lunas - reverse payment
                $kas->reversePayment($user->id, "Status diubah dari lunas ke {$request->status} oleh {$user->name}");
                
                // Update other fields
                $updateData['status'] = $request->status;
                $updateData['tanggal_bayar'] = null;
                $updateData['metode_bayar'] = null;
                $updateData['jumlah_dibayar'] = null;
                $updateData['confirmed_by'] = null;
                $updateData['confirmed_at'] = null;
                $updateData['confirmation_notes'] = null;
                // Keep rejection_reason if status is ditolak, otherwise clear
                if ($request->status !== 'ditolak') {
                    $updateData['rejection_reason'] = null;
                }
                $kas->update($updateData);
                
            } else {
                // No status change affecting saldo, just update normally
                $updateData['status'] = $request->status;
                
                if ($request->status === 'lunas') {
                    $updateData['tanggal_bayar'] = $request->tanggal_bayar ?: now();
                    $updateData['metode_bayar'] = $request->metode_bayar;
                    $updateData['jumlah_dibayar'] = $kas->total_bayar;
                    $updateData['rejection_reason'] = null;
                } elseif ($request->status === 'ditolak') {
                    $updateData['tanggal_bayar'] = null;
                    $updateData['metode_bayar'] = null;
                    $updateData['jumlah_dibayar'] = null;
                } else {
                    $updateData['tanggal_bayar'] = null;
                    $updateData['metode_bayar'] = null;
                    $updateData['jumlah_dibayar'] = null;
                    $updateData['rejection_reason'] = null;
                }
                
                $kas->update($updateData);
            }

            // Send notification and email if status changed to lunas or ditolak
            if ($kas->penduduk && $kas->penduduk->user) {
                if ($request->status === 'lunas' && $previousStatus !== 'lunas') {
                    Notifikasi::create([
                        'user_id' => $kas->penduduk->user->id,
                        'judul' => 'Kas Telah Lunas',
                        'pesan' => "Kas minggu ke-{$kas->minggu_ke} tahun {$kas->tahun} sebesar Rp " . number_format($kas->jumlah, 0, ',', '.') . " telah dikonfirmasi lunas.",
                        'tipe' => 'success',
                        'kategori' => 'kas',
                        'data' => json_encode([
                            'kas_id' => $kas->id,
                            'jumlah' => $kas->jumlah,
                            'tanggal_bayar' => $kas->tanggal_bayar,
                        ])
                    ]);

                    // Send email notification
                    $this->sendKasEmailNotification($kas, 'kas_approved', [
                        'payment_method' => $kas->metode_bayar,
                        'payment_date' => $kas->tanggal_bayar,
                    ]);

                } elseif ($request->status === 'ditolak' && $previousStatus !== 'ditolak') {
                    $rejectionMessage = "Pembayaran kas minggu ke-{$kas->minggu_ke} tahun {$kas->tahun} sebesar Rp " . number_format($kas->jumlah, 0, ',', '.') . " ditolak.";
                    if ($request->rejection_reason) {
                        $rejectionMessage .= " Alasan: {$request->rejection_reason}.";
                    }
                    $rejectionMessage .= " Silakan upload ulang bukti pembayaran yang benar.";
                    
                    Notifikasi::create([
                        'user_id' => $kas->penduduk->user->id,
                        'judul' => 'Pembayaran Kas Ditolak',
                        'pesan' => $rejectionMessage,
                        'tipe' => 'error',
                        'kategori' => 'kas',
                        'data' => json_encode([
                            'kas_id' => $kas->id,
                            'jumlah' => $kas->jumlah,
                            'minggu_ke' => $kas->minggu_ke,
                            'tahun' => $kas->tahun,
                            'rejection_reason' => $request->rejection_reason,
                        ])
                    ]);

                    // Send email notification
                    $this->sendKasEmailNotification($kas, 'kas_rejected', [
                        'rejection_reason' => $request->rejection_reason,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('kas.show', $kas)->with('success', 'Kas berhasil diupdate dan saldo RT telah disesuaikan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating kas: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengupdate kas: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Delete kas - FIXED to handle saldo reversal
     */
    public function destroy(Kas $kas)
    {
        $user = Auth::user();
        
        // Only admin can delete
        if ($user->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat menghapus kas');
        }

        try {
            DB::beginTransaction();

            // If kas is lunas, reverse the payment first
            if ($kas->status === 'lunas') {
                $kas->reversePayment($user->id, "Kas dihapus oleh admin {$user->name}");
            }

            // Send notification to resident if they have an account
            if ($kas->penduduk && $kas->penduduk->user) {
                Notifikasi::create([
                    'user_id' => $kas->penduduk->user->id,
                    'judul' => 'Kas Dihapus',
                    'pesan' => "Kas minggu ke-{$kas->minggu_ke} tahun {$kas->tahun} sebesar Rp " . number_format($kas->jumlah, 0, ',', '.') . " telah dihapus oleh administrator.",
                    'tipe' => 'warning',
                    'kategori' => 'kas',
                    'data' => json_encode([
                        'kas_id' => $kas->id,
                        'jumlah' => $kas->jumlah,
                        'minggu_ke' => $kas->minggu_ke,
                        'tahun' => $kas->tahun,
                    ])
                ]);
            }

            $kas->delete();

            DB::commit();

            return redirect()->route('kas.index')->with([
                'success' => 'Kas berhasil dihapus dan saldo RT telah disesuaikan',
                'show_success_modal' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting kas: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus kas: ' . $e->getMessage()]);
        }
    }

    /**
     * Confirm payment (AJAX) - FIXED to use markAsLunas() with email notifications
     */
    public function bayar(Request $request, Kas $kas)
    {
        $user = Auth::user();
        
        // Only admin, kades, rw, rt can confirm payment
        if (!in_array($user->role, ['admin', 'kades', 'rw', 'rt'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk konfirmasi pembayaran'
            ], 403);
        }

        // Check access permission
        if (!$this->canAccessKas($kas, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk kas ini'
            ], 403);
        }

        $request->validate([
            'metode_pembayaran' => 'required|string|max:50',
            'bukti_pembayaran' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Use markAsLunas method to properly handle saldo update
            $success = $kas->markAsLunas($user->id, "Dikonfirmasi oleh {$user->name}");
            
            if (!$success) {
                throw new \Exception('Gagal mengkonfirmasi pembayaran');
            }

            // Update payment method and proof
            $kas->update([
                'metode_bayar' => $request->metode_pembayaran,
                'bukti_bayar_file' => $request->bukti_pembayaran,
            ]);

            // Send notification to resident
            if ($kas->penduduk && $kas->penduduk->user) {
                Notifikasi::create([
                    'user_id' => $kas->penduduk->user->id,
                    'judul' => 'Pembayaran Kas Dikonfirmasi',
                    'pesan' => "Pembayaran kas minggu ke-{$kas->minggu_ke} tahun {$kas->tahun} sebesar Rp " . number_format($kas->jumlah, 0, ',', '.') . " telah dikonfirmasi lunas via {$request->metode_pembayaran}.",
                    'tipe' => 'success',
                    'kategori' => 'kas',
                    'data' => json_encode([
                        'kas_id' => $kas->id,
                        'jumlah' => $kas->jumlah,
                        'metode_bayar' => $request->metode_pembayaran,
                        'tanggal_bayar' => now(),
                    ])
                ]);

                // Send email notification
                $this->sendKasEmailNotification($kas, 'kas_approved', [
                    'payment_method' => $request->metode_pembayaran,
                    'payment_date' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('kas.show', $kas)->with([
                'success' => 'Pembayaran kas berhasil dikonfirmasi dan saldo RT telah diperbarui',
                'show_success_modal' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirming payment: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat konfirmasi pembayaran: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject payment with reason and allow re-confirmation with email notifications
     */
    public function tolak(Request $request, Kas $kas)
    {
        $user = Auth::user();
        
        // Only admin, kades, rw, rt can reject payment
        if (!in_array($user->role, ['admin', 'kades', 'rw', 'rt'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menolak pembayaran'
            ], 403);
        }

        // Check access permission
        if (!$this->canAccessKas($kas, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk kas ini'
            ], 403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Update kas status to ditolak and store rejection reason
            $kas->update([
                'status' => 'ditolak',
                'rejection_reason' => $request->rejection_reason,
                'tanggal_bayar' => null,
                'metode_bayar' => null,
                'bukti_bayar_file' => null,
                'bukti_bayar_notes' => null,
                'jumlah_dibayar' => null,
                'confirmed_by' => null,
                'confirmed_at' => null,
                'confirmation_notes' => null,
            ]);

            // Send notification to resident
            if ($kas->penduduk && $kas->penduduk->user) {
                Notifikasi::create([
                    'user_id' => $kas->penduduk->user->id,
                    'judul' => 'Pembayaran Kas Ditolak',
                    'pesan' => "Pembayaran kas minggu ke-{$kas->minggu_ke} tahun {$kas->tahun} sebesar Rp " . number_format($kas->jumlah, 0, ',', '.') . " ditolak. Alasan: {$request->rejection_reason}. Silakan ajukan konfirmasi ulang dengan bukti pembayaran yang benar.",
                    'tipe' => 'error',
                    'kategori' => 'kas',
                    'data' => json_encode([
                        'kas_id' => $kas->id,
                        'jumlah' => $kas->jumlah,
                        'minggu_ke' => $kas->minggu_ke,
                        'tahun' => $kas->tahun,
                        'rejection_reason' => $request->rejection_reason,
                    ])
                ]);

                // Send email notification
                $this->sendKasEmailNotification($kas, 'kas_rejected', [
                    'rejection_reason' => $request->rejection_reason,
                ]);
            }

            DB::commit();

            return redirect()->route('kas.show', $kas)->with([
                'success' => 'Pembayaran kas berhasil ditolak. Warga dapat mengajukan konfirmasi ulang.',
                'show_success_modal' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting payment: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menolak pembayaran: ' . $e->getMessage()]);
        }
    }

    /**
     * Re-confirm payment for rejected kas - ENHANCED VERSION with email notifications
     */
    public function konfirmasiUlang(Request $request, Kas $kas)
    {
        $user = Auth::user();
        
        // Allow both RT/RW/Admin AND masyarakat to re-confirm
        if (!in_array($user->role, ['admin', 'kades', 'rw', 'rt', 'masyarakat'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk konfirmasi ulang pembayaran'
                ], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk konfirmasi ulang pembayaran');
        }

        // Check access permission
        if (!$this->canAccessKas($kas, $user)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk kas ini'
                ], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk kas ini');
        }

        // Only allow re-confirmation for rejected kas
        if ($kas->status !== 'ditolak') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kas ini tidak dalam status ditolak'
                ], 400);
            }
            return redirect()->back()->with('error', 'Kas ini tidak dalam status ditolak');
        }

        try {
            DB::beginTransaction();

            // If masyarakat is re-confirming, just change status back to menunggu_konfirmasi
            if ($user->role === 'masyarakat') {
                $kas->update([
                    'status' => 'menunggu_konfirmasi',
                    'keterangan' => ($kas->keterangan ? $kas->keterangan . " | " : "") . "Diajukan ulang oleh warga pada " . now()->format('d/m/Y H:i'),
                    'rejection_reason' => null,
                ]);

                // Send notification to RT - find RT ketua
                $rt = $kas->rt;
                if ($rt) {
                    $rtKetua = null;
                    
                    if ($rt->ketua_rt_id) {
                        $rtKetuaPenduduk = Penduduk::find($rt->ketua_rt_id);
                        if ($rtKetuaPenduduk && $rtKetuaPenduduk->user) {
                            $rtKetua = $rtKetuaPenduduk->user;
                        }
                    }
                    
                    if (!$rtKetua) {
                        $rtKetua = User::where('role', 'rt')
                            ->whereHas('penduduk.kk', function($query) use ($rt) {
                                $query->where('rt_id', $rt->id);
                            })
                            ->first();
                    }
                    
                    if ($rtKetua) {
                        Notifikasi::create([
                            'user_id' => $rtKetua->id,
                            'judul' => 'Konfirmasi Ulang Kas',
                            'pesan' => "Warga {$kas->penduduk->nama_lengkap} mengajukan konfirmasi ulang untuk kas minggu ke-{$kas->minggu_ke} tahun {$kas->tahun} yang sebelumnya ditolak.",
                            'tipe' => 'info',
                            'kategori' => 'kas',
                            'data' => json_encode([
                                'kas_id' => $kas->id,
                                'penduduk_nama' => $kas->penduduk->nama_lengkap,
                                'minggu_ke' => $kas->minggu_ke,
                                'tahun' => $kas->tahun,
                            ])
                        ]);
                    }
                }

                DB::commit();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Kas berhasil diajukan ulang untuk konfirmasi. Menunggu persetujuan RT.',
                        'status' => 'menunggu_konfirmasi'
                    ]);
                }
                
                return redirect()->back()->with('success', 'Kas berhasil diajukan ulang untuk konfirmasi. Menunggu persetujuan RT.');

            } else {
                // RT/RW/Admin directly confirming the rejected payment
                $request->validate([
                    'metode_pembayaran' => 'nullable|string|max:50',
                    'bukti_pembayaran' => 'nullable|string|max:1000',
                    'catatan_konfirmasi' => 'nullable|string|max:500',
                ]);

                // Use markAsLunas method to properly handle saldo update
                $success = $kas->markAsLunas($user->id, "Konfirmasi ulang oleh {$user->name}: " . ($request->catatan_konfirmasi ?? 'Tidak ada catatan'));
                
                if (!$success) {
                    throw new \Exception('Gagal mengkonfirmasi ulang pembayaran');
                }

                // Update payment method and proof if provided
                $updateData = [];
                if ($request->metode_pembayaran) {
                    $updateData['metode_bayar'] = $request->metode_pembayaran;
                }
                if ($request->bukti_pembayaran) {
                    $updateData['bukti_bayar_file'] = $request->bukti_pembayaran;
                }
                if (!empty($updateData)) {
                    $kas->update($updateData);
                }

                $kas->update([
                    'keterangan' => ($kas->keterangan ? $kas->keterangan . " | " : "") . "Dikonfirmasi ulang: " . ($request->catatan_konfirmasi ?? 'Tidak ada catatan'),
                    'rejection_reason' => null,
                ]);

                // Send notification to resident
                if ($kas->penduduk && $kas->penduduk->user) {
                    Notifikasi::create([
                        'user_id' => $kas->penduduk->user->id,
                        'judul' => 'Pembayaran Kas Dikonfirmasi Ulang',
                        'pesan' => "Pembayaran kas minggu ke-{$kas->minggu_ke} tahun {$kas->tahun} sebesar Rp " . number_format($kas->jumlah, 0, ',', '.') . " telah dikonfirmasi ulang dan dinyatakan lunas" . ($request->metode_pembayaran ? " via {$request->metode_pembayaran}" : "") . ".",
                        'tipe' => 'success',
                        'kategori' => 'kas',
                        'data' => json_encode([
                            'kas_id' => $kas->id,
                            'jumlah' => $kas->jumlah,
                            'metode_bayar' => $request->metode_pembayaran,
                            'tanggal_bayar' => now(),
                            'catatan' => $request->catatan_konfirmasi,
                        ])
                    ]);

                    // Send email notification
                    $this->sendKasEmailNotification($kas, 'kas_approved', [
                        'payment_method' => $request->metode_pembayaran,
                        'payment_date' => now(),
                    ]);
                }

                DB::commit();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Pembayaran kas berhasil dikonfirmasi ulang dan saldo RT telah diperbarui',
                        'status' => 'lunas'
                    ]);
                }
                
                return redirect()->back()->with('success', 'Pembayaran kas berhasil dikonfirmasi ulang dan saldo RT telah diperbarui');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error re-confirming payment: ' . $e->getMessage());
        
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat konfirmasi ulang pembayaran: ' . $e->getMessage()
                ], 500);
            }
        
            return redirect()->back()->with('error', 'Terjadi kesalahan saat konfirmasi ulang pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Method to display the payment form for a specific Kas.
     */
    public function paymentForm(Kas $kas)
    {
        // Ensure overdue status is updated and denda is calculated
        $kas->updateOverdueStatus();
        // Refresh the model to ensure the latest denda value is loaded from the database
        $kas->refresh();

        // Get payment info for the RT associated with this Kas
        $paymentInfo = PaymentInfo::where('rt_id', $kas->rt_id)
                                ->where('is_active', true)
                                ->first();

        return view('kas.payment-form', compact('kas', 'paymentInfo'));
    }

    /**
     * Check if user can access specific kas
     */
    private function canAccessKas(Kas $kas, User $user)
    {
        try {
            switch ($user->role) {
                case 'admin':
                case 'kades':
                    return true;
                case 'rw':
                    if ($user->penduduk && $user->penduduk->kk && $user->penduduk->kk->rt && $kas->rt) {
                        $userRwId = $user->penduduk->kk->rt->rw_id;
                        return $kas->rt->rw_id === $userRwId;
                    }
                    return false;
                case 'rt':
                    if ($user->penduduk && $user->penduduk->kk) {
                        $userRtId = $user->penduduk->kk->rt_id;
                        return $kas->rt_id === $userRtId;
                    }
                    return false;
                case 'masyarakat':
                    return $kas->penduduk_id === ($user->penduduk ? $user->penduduk->id : null);
                default:
                    return false;
            }
        } catch (\Exception $e) {
            Log::error('Error in canAccessKas: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper method untuk mendapatkan nama penduduk dengan null check
     */
    private function getPendudukName($kas)
    {
        if ($kas && $kas->penduduk && $kas->penduduk->nama_lengkap) {
            return $kas->penduduk->nama_lengkap;
        }
        return 'Data tidak tersedia';
    }

    /**
     * Helper method untuk mendapatkan NIK penduduk dengan null check
     */
    private function getPendudukNik($kas)
    {
        if ($kas && $kas->penduduk && $kas->penduduk->nik) {
            return $kas->penduduk->nik;
        }
        return '-';
    }

    /**
     * Helper method untuk mendapatkan RT/RW info dengan null check
     */
    private function getRtRwInfo($kas)
    {
        if ($kas && $kas->rt) {
            $rtInfo = "RT " . ($kas->rt->no_rt ?? '-');
            if ($kas->rt->rw) {
                $rtInfo .= " / RW " . ($kas->rt->rw->no_rw ?? '-');
            }
            return $rtInfo;
        }
        return 'Data tidak tersedia';
    }

    /**
     * Method untuk validasi data kas sebelum operasi
     */
    private function validateKasData(Kas $kas)
    {
        $errors = [];
        
        if (!$kas->penduduk) {
            $errors[] = 'Data penduduk tidak ditemukan untuk kas ini';
        }
        
        if (!$kas->rt) {
            $errors[] = 'Data RT tidak ditemukan untuk kas ini';
        }
        
        return $errors;
    }

    /**
     * Method untuk log error dengan context yang lebih baik
     */
    private function logKasError($message, $kas = null, $exception = null)
    {
        $context = [
            'kas_id' => $kas ? $kas->id : null,
            'penduduk_id' => $kas ? $kas->penduduk_id : null,
            'rt_id' => $kas ? $kas->rt_id : null,
        ];
        
        if ($exception) {
            $context['exception'] = $exception->getMessage();
            $context['trace'] = $exception->getTraceAsString();
        }
        
        Log::error($message, $context);
    }
}
