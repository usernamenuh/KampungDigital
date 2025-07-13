<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kas;
use App\Models\PaymentInfo;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PaymentApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Get payments list with filters
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Kas::with(['penduduk', 'rt.rw', 'confirmedBy']);

            // Apply role-based filters
            switch ($user->role) {
                case 'masyarakat':
                    if ($user->penduduk) {
                        $query->where('penduduk_id', $user->penduduk->id);
                    } else {
                        return response()->json(['error' => 'Data penduduk tidak ditemukan'], 404);
                    }
                    break;
                case 'rt':
                    if ($user->penduduk && $user->penduduk->rtKetua) {
                        $query->where('rt_id', $user->penduduk->rtKetua->id);
                    } else {
                        return response()->json(['error' => 'Data RT tidak ditemukan'], 404);
                    }
                    break;
                case 'rw':
                    if ($user->penduduk && $user->penduduk->rwKetua) {
                        $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                        $query->whereIn('rt_id', $rtIds);
                    } else {
                        return response()->json(['error' => 'Data RW tidak ditemukan'], 404);
                    }
                    break;
                // admin dan kades bisa lihat semua
            }

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('rt_id')) {
                $query->where('rt_id', $request->rt_id);
            }

            if ($request->filled('metode_bayar')) {
                $query->where('metode_bayar', $request->metode_bayar);
            }

            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }

            if ($request->filled('minggu_ke')) {
                $query->where('minggu_ke', $request->minggu_ke);
            }

            if ($request->filled('search')) {
                $query->whereHas('penduduk', function($q) use ($request) {
                    $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                      ->orWhere('nik', 'like', '%' . $request->search . '%');
                });
            }

            // Date range filter
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 20);
            $payments = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $payments->items(),
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                    'has_more' => $payments->hasMorePages()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific payment details
     */
    public function show(Kas $payment)
    {
        try {
            if (!$this->canAccessPayment($payment)) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $payment->load(['penduduk', 'rt.rw', 'confirmedBy']);

            return response()->json([
                'success' => true,
                'data' => $payment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending payments for confirmation
     */
    public function getPendingPayments(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $query = Kas::with(['penduduk', 'rt.rw'])
                        ->where('status', 'menunggu_konfirmasi');

            // Apply role-based filters
            switch ($user->role) {
                case 'rt':
                    if ($user->penduduk && $user->penduduk->rtKetua) {
                        $query->where('rt_id', $user->penduduk->rtKetua->id);
                    } else {
                        return response()->json(['error' => 'Data RT tidak ditemukan'], 404);
                    }
                    break;
                case 'rw':
                    if ($user->penduduk && $user->penduduk->rwKetua) {
                        $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                        $query->whereIn('rt_id', $rtIds);
                    } else {
                        return response()->json(['error' => 'Data RW tidak ditemukan'], 404);
                    }
                    break;
            }

            // Apply additional filters
            if ($request->filled('rt_id')) {
                $query->where('rt_id', $request->rt_id);
            }

            if ($request->filled('metode_bayar')) {
                $query->where('metode_bayar', $request->metode_bayar);
            }

            $payments = $query->orderBy('bukti_bayar_uploaded_at', 'asc')
                             ->paginate($request->get('per_page', 20));

            return response()->json([
                'success' => true,
                'data' => $payments->items(),
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                    'has_more' => $payments->hasMorePages()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat pembayaran pending',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm payment
     */
    public function confirmPayment(Request $request, Kas $payment)
    {
        try {
            $user = Auth::user();
            
            if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            if (!$this->canAccessPayment($payment)) {
                return response()->json(['error' => 'Anda tidak memiliki akses untuk kas ini'], 403);
            }

            if ($payment->status !== 'menunggu_konfirmasi') {
                return response()->json(['error' => 'Kas ini tidak dalam status menunggu konfirmasi'], 400);
            }

            $validator = Validator::make($request->all(), [
                'confirmation_notes' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $payment->update([
                'status' => 'lunas',
                'tanggal_bayar' => now(),
                'confirmed_by' => $user->id,
                'confirmed_at' => now(),
                'confirmation_notes' => $request->confirmation_notes,
            ]);

            // Create notification for masyarakat
            $this->createPaymentNotification($payment, 'approved');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi',
                'data' => $payment->fresh(['penduduk', 'rt.rw', 'confirmedBy'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengkonfirmasi pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject payment
     */
    public function rejectPayment(Request $request, Kas $payment)
    {
        try {
            $user = Auth::user();
            
            if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            if (!$this->canAccessPayment($payment)) {
                return response()->json(['error' => 'Anda tidak memiliki akses untuk kas ini'], 403);
            }

            if ($payment->status !== 'menunggu_konfirmasi') {
                return response()->json(['error' => 'Kas ini tidak dalam status menunggu konfirmasi'], 400);
            }

            $validator = Validator::make($request->all(), [
                'confirmation_notes' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $oldBuktiFile = $payment->bukti_bayar_file;
            
            $payment->update([
                'status' => 'belum_bayar',
                'metode_bayar' => null,
                'bukti_bayar_file' => null,
                'bukti_bayar_notes' => null,
                'bukti_bayar_uploaded_at' => null,
                'e_wallet_type' => null,
                'confirmed_by' => $user->id,
                'confirmed_at' => now(),
                'confirmation_notes' => $request->confirmation_notes,
            ]);

            // Delete old proof file
            if ($oldBuktiFile && Storage::disk('public')->exists($oldBuktiFile)) {
                Storage::disk('public')->delete($oldBuktiFile);
            }

            // Create notification for masyarakat
            $this->createPaymentNotification($payment, 'rejected');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil ditolak',
                'data' => $payment->fresh(['penduduk', 'rt.rw', 'confirmedBy'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment proof
     */
    public function getProof(Kas $payment)
    {
        try {
            if (!$this->canAccessPayment($payment)) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            if (!$payment->bukti_bayar_file) {
                return response()->json(['error' => 'Bukti pembayaran tidak ditemukan'], 404);
            }

            $proofUrl = Storage::disk('public')->url($payment->bukti_bayar_file);

            return response()->json([
                'success' => true,
                'data' => [
                    'payment' => $payment->load(['penduduk', 'rt.rw', 'confirmedBy']),
                    'proof_url' => $proofUrl,
                    'proof_exists' => Storage::disk('public')->exists($payment->bukti_bayar_file)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat bukti pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history
     */
    public function getPaymentHistory(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Kas::with(['penduduk', 'rt.rw', 'confirmedBy']);

            // Apply role-based filters
            switch ($user->role) {
                case 'masyarakat':
                    if ($user->penduduk) {
                        $query->where('penduduk_id', $user->penduduk->id);
                    } else {
                        return response()->json(['error' => 'Data penduduk tidak ditemukan'], 404);
                    }
                    break;
                case 'rt':
                    if ($user->penduduk && $user->penduduk->rtKetua) {
                        $query->where('rt_id', $user->penduduk->rtKetua->id);
                    } else {
                        return response()->json(['error' => 'Data RT tidak ditemukan'], 404);
                    }
                    break;
                case 'rw':
                    if ($user->penduduk && $user->penduduk->rwKetua) {
                        $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                        $query->whereIn('rt_id', $rtIds);
                    } else {
                        return response()->json(['error' => 'Data RW tidak ditemukan'], 404);
                    }
                    break;
            }

            // Only show completed payments
            $query->whereIn('status', ['lunas']);

            // Apply filters
            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('tanggal_bayar', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('tanggal_bayar', '<=', $request->end_date);
            }

            $payments = $query->orderBy('tanggal_bayar', 'desc')
                             ->paginate($request->get('per_page', 20));

            return response()->json([
                'success' => true,
                'data' => $payments->items(),
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                    'has_more' => $payments->hasMorePages()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat riwayat pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Kas::query();

            // Apply role-based filters
            switch ($user->role) {
                case 'masyarakat':
                    if ($user->penduduk) {
                        $query->where('penduduk_id', $user->penduduk->id);
                    } else {
                        return response()->json(['error' => 'Data penduduk tidak ditemukan'], 404);
                    }
                    break;
                case 'rt':
                    if ($user->penduduk && $user->penduduk->rtKetua) {
                        $query->where('rt_id', $user->penduduk->rtKetua->id);
                    } else {
                        return response()->json(['error' => 'Data RT tidak ditemukan'], 404);
                    }
                    break;
                case 'rw':
                    if ($user->penduduk && $user->penduduk->rwKetua) {
                        $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                        $query->whereIn('rt_id', $rtIds);
                    } else {
                        return response()->json(['error' => 'Data RW tidak ditemukan'], 404);
                    }
                    break;
            }

            // Apply date filter
            $year = $request->get('tahun', date('Y'));
            $query->where('tahun', $year);

            $stats = [
                'total_kas' => $query->count(),
                'lunas' => $query->clone()->where('status', 'lunas')->count(),
                'belum_bayar' => $query->clone()->where('status', 'belum_bayar')->count(),
                'menunggu_konfirmasi' => $query->clone()->where('status', 'menunggu_konfirmasi')->count(),
                'terlambat' => $query->clone()->where('status', 'terlambat')->count(),
                'total_terkumpul' => $query->clone()->where('status', 'lunas')->sum('jumlah'),
                'total_denda' => $query->clone()->where('status', 'lunas')->sum('denda'),
                'total_outstanding' => $query->clone()->whereIn('status', ['belum_bayar', 'terlambat'])->sum('jumlah'),
            ];

            // Monthly breakdown
            $monthlyStats = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthlyQuery = $query->clone()->whereMonth('created_at', $month);
                $monthlyStats[] = [
                    'month' => $month,
                    'month_name' => Carbon::create()->month($month)->format('F'),
                    'total' => $monthlyQuery->count(),
                    'lunas' => $monthlyQuery->clone()->where('status', 'lunas')->count(),
                    'belum_bayar' => $monthlyQuery->clone()->where('status', 'belum_bayar')->count(),
                    'amount_collected' => $monthlyQuery->clone()->where('status', 'lunas')->sum('jumlah'),
                ];
            }

            // Payment method breakdown
            $paymentMethods = $query->clone()
                                   ->where('status', 'lunas')
                                   ->whereNotNull('metode_bayar')
                                   ->groupBy('metode_bayar')
                                   ->selectRaw('metode_bayar, COUNT(*) as count, SUM(jumlah) as total_amount')
                                   ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => $stats,
                    'monthly' => $monthlyStats,
                    'payment_methods' => $paymentMethods,
                    'year' => $year
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search payments
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q');
            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $user = Auth::user();
            $kasQuery = Kas::with(['penduduk', 'rt.rw']);

            // Apply role-based filters
            switch ($user->role) {
                case 'masyarakat':
                    if ($user->penduduk) {
                        $kasQuery->where('penduduk_id', $user->penduduk->id);
                    } else {
                        return response()->json(['error' => 'Data penduduk tidak ditemukan'], 404);
                    }
                    break;
                case 'rt':
                    if ($user->penduduk && $user->penduduk->rtKetua) {
                        $kasQuery->where('rt_id', $user->penduduk->rtKetua->id);
                    } else {
                        return response()->json(['error' => 'Data RT tidak ditemukan'], 404);
                    }
                    break;
                case 'rw':
                    if ($user->penduduk && $user->penduduk->rwKetua) {
                        $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                        $kasQuery->whereIn('rt_id', $rtIds);
                    } else {
                        return response()->json(['error' => 'Data RW tidak ditemukan'], 404);
                    }
                    break;
            }

            // Search in multiple fields
            $kasQuery->where(function($q) use ($query) {
                $q->whereHas('penduduk', function($subQ) use ($query) {
                    $subQ->where('nama_lengkap', 'like', '%' . $query . '%')
                         ->orWhere('nik', 'like', '%' . $query . '%');
                })
                ->orWhere('minggu_ke', 'like', '%' . $query . '%')
                ->orWhere('tahun', 'like', '%' . $query . '%')
                ->orWhere('status', 'like', '%' . $query . '%')
                ->orWhere('metode_bayar', 'like', '%' . $query . '%');
            });

            $results = $kasQuery->limit(20)->get();

            return response()->json([
                'success' => true,
                'data' => $results,
                'query' => $query
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload file
     */
    public function uploadFile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'type' => 'required|in:bukti_bayar,qr_code'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('file');
            $type = $request->type;
            
            $path = $file->store($type === 'qr_code' ? 'payment-qr-codes' : 'bukti-bayar', 'public');
            $url = Storage::disk('public')->url($path);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'data' => [
                    'path' => $path,
                    'url' => $url,
                    'filename' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download file
     */
    public function downloadFile($file)
    {
        try {
            if (!Storage::disk('public')->exists($file)) {
                return response()->json(['error' => 'File tidak ditemukan'], 404);
            }

            $filePath = Storage::disk('public')->path($file);
            $fileName = basename($file);

            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete file
     */
    public function deleteFile($file)
    {
        try {
            if (!Storage::disk('public')->exists($file)) {
                return response()->json(['error' => 'File tidak ditemukan'], 404);
            }

            Storage::disk('public')->delete($file);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment infos
     */
    public function getPaymentInfos(Request $request)
    {
        try {
            $user = Auth::user();
            $query = PaymentInfo::with('rt.rw');

            // Apply role-based filters
            switch ($user->role) {
                case 'rt':
                    if ($user->penduduk && $user->penduduk->rtKetua) {
                        $query->where('rt_id', $user->penduduk->rtKetua->id);
                    } else {
                        return response()->json(['error' => 'Data RT tidak ditemukan'], 404);
                    }
                    break;
                case 'rw':
                    if ($user->penduduk && $user->penduduk->rwKetua) {
                        $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                        $query->whereIn('rt_id', $rtIds);
                    } else {
                        return response()->json(['error' => 'Data RW tidak ditemukan'], 404);
                    }
                    break;
            }

            if ($request->filled('rt_id')) {
                $query->where('rt_id', $request->rt_id);
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            $paymentInfos = $query->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $paymentInfos->items(),
                'pagination' => [
                    'current_page' => $paymentInfos->currentPage(),
                    'last_page' => $paymentInfos->lastPage(),
                    'per_page' => $paymentInfos->perPage(),
                    'total' => $paymentInfos->total(),
                    'has_more' => $paymentInfos->hasMorePages()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat informasi pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store payment info
     */
    public function storePaymentInfo(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $validator = Validator::make($request->all(), [
                'rt_id' => 'required|exists:rts,id',
                'bank_transfer' => 'nullable|array',
                'bank_transfer.bank_name' => 'nullable|string|max:100',
                'bank_transfer.account_number' => 'nullable|string|max:50',
                'bank_transfer.account_name' => 'nullable|string|max:100',
                'e_wallet' => 'nullable|array',
                'e_wallet.dana' => 'nullable|string|max:20',
                'e_wallet.ovo' => 'nullable|string|max:20',
                'e_wallet.gopay' => 'nullable|string|max:20',
                'qr_code' => 'nullable|array',
                'qr_code.image_url' => 'nullable|string',
                'qr_code.description' => 'nullable|string|max:255',
                'payment_notes' => 'nullable|string|max:1000',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if payment info already exists for this RT
            $existing = PaymentInfo::where('rt_id', $request->rt_id)->first();
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informasi pembayaran untuk RT ini sudah ada'
                ], 400);
            }

            $paymentInfo = PaymentInfo::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Informasi pembayaran berhasil dibuat',
                'data' => $paymentInfo->load('rt.rw')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat informasi pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific payment info
     */
    public function getPaymentInfo(PaymentInfo $paymentInfo)
    {
        try {
            if (!$this->canAccessPaymentInfo($paymentInfo)) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $paymentInfo->load('rt.rw')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat informasi pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment info
     */
    public function updatePaymentInfo(Request $request, PaymentInfo $paymentInfo)
    {
        try {
            if (!$this->canAccessPaymentInfo($paymentInfo)) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $validator = Validator::make($request->all(), [
                'bank_transfer' => 'nullable|array',
                'bank_transfer.bank_name' => 'nullable|string|max:100',
                'bank_transfer.account_number' => 'nullable|string|max:50',
                'bank_transfer.account_name' => 'nullable|string|max:100',
                'e_wallet' => 'nullable|array',
                'e_wallet.dana' => 'nullable|string|max:20',
                'e_wallet.ovo' => 'nullable|string|max:20',
                'e_wallet.gopay' => 'nullable|string|max:20',
                'qr_code' => 'nullable|array',
                'qr_code.image_url' => 'nullable|string',
                'qr_code.description' => 'nullable|string|max:255',
                'payment_notes' => 'nullable|string|max:1000',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $paymentInfo->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Informasi pembayaran berhasil diperbarui',
                'data' => $paymentInfo->fresh(['rt.rw'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui informasi pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete payment info
     */
    public function deletePaymentInfo(PaymentInfo $paymentInfo)
    {
        try {
            if (!$this->canAccessPaymentInfo($paymentInfo)) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            // Delete QR code file if exists
            if ($paymentInfo->qr_code && isset($paymentInfo->qr_code['image_url'])) {
                $path = str_replace('/storage/', '', $paymentInfo->qr_code['image_url']);
                Storage::disk('public')->delete($path);
            }

            $paymentInfo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Informasi pembayaran berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus informasi pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get RT payment info
     */
    public function getRtPaymentInfo($rtId)
    {
        try {
            $paymentInfo = PaymentInfo::where('rt_id', $rtId)
                                     ->where('is_active', true)
                                     ->with('rt.rw')
                                     ->first();

            if (!$paymentInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informasi pembayaran tidak ditemukan untuk RT ini'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $paymentInfo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat informasi pembayaran RT',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment report
     */
    public function getPaymentReport(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $query = Kas::with(['penduduk', 'rt.rw']);

            // Apply role-based filters
            switch ($user->role) {
                case 'rt':
                    if ($user->penduduk && $user->penduduk->rtKetua) {
                        $query->where('rt_id', $user->penduduk->rtKetua->id);
                    } else {
                        return response()->json(['error' => 'Data RT tidak ditemukan'], 404);
                    }
                    break;
                case 'rw':
                    if ($user->penduduk && $user->penduduk->rwKetua) {
                        $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                        $query->whereIn('rt_id', $rtIds);
                    } else {
                        return response()->json(['error' => 'Data RW tidak ditemukan'], 404);
                    }
                    break;
            }

            // Apply filters
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('rt_id')) {
                $query->where('rt_id', $request->rt_id);
            }

            $payments = $query->orderBy('created_at', 'desc')->get();

            // Calculate summary
            $summary = [
                'total_payments' => $payments->count(),
                'total_amount' => $payments->where('status', 'lunas')->sum('jumlah'),
                'total_denda' => $payments->where('status', 'lunas')->sum('denda'),
                'pending_amount' => $payments->whereIn('status', ['belum_bayar', 'menunggu_konfirmasi'])->sum('jumlah'),
                'by_status' => [
                    'lunas' => $payments->where('status', 'lunas')->count(),
                    'belum_bayar' => $payments->where('status', 'belum_bayar')->count(),
                    'menunggu_konfirmasi' => $payments->where('status', 'menunggu_konfirmasi')->count(),
                    'terlambat' => $payments->where('status', 'terlambat')->count(),
                ],
                'by_method' => $payments->where('status', 'lunas')
                                       ->groupBy('metode_bayar')
                                       ->map(function($group) {
                                           return [
                                               'count' => $group->count(),
                                               'amount' => $group->sum('jumlah')
                                           ];
                                       })
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'payments' => $payments,
                    'summary' => $summary,
                    'filters' => $request->only(['start_date', 'end_date', 'status', 'rt_id'])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can access payment
     */
    private function canAccessPayment(Kas $payment)
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
            case 'kades':
                return true;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    return $payment->rt->rw_id === $user->penduduk->rwKetua->id;
                }
                return false;
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    return $payment->rt_id === $user->penduduk->rtKetua->id;
                }
                return false;
            case 'masyarakat':
                if ($user->penduduk) {
                    return $payment->penduduk_id === $user->penduduk->id;
                }
                return false;
            default:
                return false;
        }
    }

    /**
     * Check if user can access payment info
     */
    private function canAccessPaymentInfo(PaymentInfo $paymentInfo)
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
            case 'kades':
                return true;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    return $paymentInfo->rt->rw_id === $user->penduduk->rwKetua->id;
                }
                return false;
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    return $paymentInfo->rt_id === $user->penduduk->rtKetua->id;
                }
                return false;
            default:
                return false;
        }
    }

    /**
     * Create notification for payment events
     */
    private function createPaymentNotification(Kas $kas, $type)
    {
        $kas->load(['penduduk', 'rt.rw']);
        
        switch ($type) {
            case 'submitted':
                // Notification for RT
                $rtUser = \App\Models\User::whereHas('penduduk.rtKetua', function($q) use ($kas) {
                    $q->where('id', $kas->rt_id);
                })->first();

                if ($rtUser) {
                    Notifikasi::create([
                        'user_id' => $rtUser->id,
                        'judul' => 'Bukti Pembayaran Kas Baru',
                        'pesan' => "Bukti pembayaran kas dari {$kas->penduduk->nama_lengkap} untuk minggu ke-{$kas->minggu_ke} tahun {$kas->tahun} telah dikirim dan menunggu konfirmasi.",
                        'tipe' => 'info',
                        'kategori' => 'pembayaran_kas',
                        'data' => [
                            'kas_id' => $kas->id,
                            'penduduk_nama' => $kas->penduduk->nama_lengkap,
                            'minggu_ke' => $kas->minggu_ke,
                            'tahun' => $kas->tahun,
                            'jumlah' => $kas->total_bayar,
                            'metode_bayar' => $kas->metode_bayar_formatted,
                        ],
                    ]);
                }
                break;

            case 'approved':
                // Notification for masyarakat
                $masyarakatUser = \App\Models\User::where('penduduk_id', $kas->penduduk_id)->first();
                
                if ($masyarakatUser) {
                    Notifikasi::create([
                        'user_id' => $masyarakatUser->id,
                        'judul' => 'Pembayaran Kas Dikonfirmasi',
                        'pesan' => "Pembayaran kas Anda untuk minggu ke-{$kas->minggu_ke} tahun {$kas->tahun} telah dikonfirmasi dan diterima.",
                        'tipe' => 'success',
                        'kategori' => 'pembayaran_kas',
                        'data' => [
                            'kas_id' => $kas->id,
                            'minggu_ke' => $kas->minggu_ke,
                            'tahun' => $kas->tahun,
                            'jumlah' => $kas->total_bayar,
                            'tanggal_bayar' => $kas->tanggal_bayar_formatted,
                        ],
                    ]);
                }
                break;

            case 'rejected':
                // Notification for masyarakat
                $masyarakatUser = \App\Models\User::where('penduduk_id', $kas->penduduk_id)->first();
                
                if ($masyarakatUser) {
                    Notifikasi::create([
                        'user_id' => $masyarakatUser->id,
                        'judul' => 'Pembayaran Kas Ditolak',
                        'pesan' => "Pembayaran kas Anda untuk minggu ke-{$kas->minggu_ke} tahun {$kas->tahun} ditolak. Silakan kirim ulang bukti pembayaran yang valid.",
                        'tipe' => 'warning',
                        'kategori' => 'pembayaran_kas',
                        'data' => [
                            'kas_id' => $kas->id,
                            'minggu_ke' => $kas->minggu_ke,
                            'tahun' => $kas->tahun,
                            'jumlah' => $kas->total_bayar,
                            'alasan_penolakan' => $kas->confirmation_notes,
                        ],
                    ]);
                }
                break;
        }
    }
}
