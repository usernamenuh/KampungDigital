<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kas;
use App\Models\Penduduk;
use App\Models\PaymentInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Added for logging

class KasApiController extends Controller
{
    /**
     * Get kas statistics for the authenticated user.
     */
    public function getStats(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->penduduk) {
            Log::warning('KasApiController: User not linked to a resident for getStats.', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'User not linked to a resident.'
            ], 403);
        }

        $pendudukId = $user->penduduk->id;
        $currentYear = Carbon::now()->year;

        try {
            $query = Kas::where('penduduk_id', $pendudukId)
                        ->where('tahun', $currentYear);

            $kasLunas = (clone $query)->where('status', 'lunas')->count();
            $kasBelumBayar = (clone $query)->where('status', 'belum_bayar')->count();
            $kasMenungguKonfirmasi = (clone $query)->where('status', 'menunggu_konfirmasi')->count();
            
            $kasTerlambat = (clone $query)->where('status', 'belum_bayar')
                                        ->where('tanggal_jatuh_tempo', '<', Carbon::now())
                                        ->count();

            $totalKasAnda = (clone $query)->sum('jumlah');

            return response()->json([
                'success' => true,
                'data' => [
                    'kasLunas' => $kasLunas,
                    'kasBelumBayar' => $kasBelumBayar + $kasMenungguKonfirmasi,
                    'kasTerlambat' => $kasTerlambat,
                    'totalKasAnda' => $totalKasAnda,
                    'kasMenungguKonfirmasi' => $kasMenungguKonfirmasi, // Added this stat
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in KasApiController getStats', [
                'user_id' => $user->id,
                'penduduk_id' => $pendudukId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik kas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a list of kas for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->penduduk) {
            Log::warning('KasApiController: User not linked to a resident for index.', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'User not linked to a resident.'
            ], 403);
        }

        $pendudukId = $user->penduduk->id;
        $perPage = $request->get('limit', 10);
        $statusFilter = $request->get('status');
        $sortBy = $request->get('sort', 'minggu_ke_desc');

        try {
            $query = Kas::where('penduduk_id', $pendudukId);

            if ($statusFilter) {
                $statuses = explode(',', $statusFilter);
                $query->whereIn('status', $statuses);
            }

            if ($sortBy === 'tanggal_bayar_desc') {
                $query->orderBy('tanggal_bayar', 'desc');
            } else {
                $query->orderBy('tahun', 'desc')->orderBy('minggu_ke', 'desc');
            }

            $kas = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $kas->items(),
                'pagination' => [
                    'current_page' => $kas->currentPage(),
                    'per_page' => $kas->perPage(),
                    'total' => $kas->total(),
                    'last_page' => $kas->lastPage(),
                    'has_more' => $kas->hasMorePages(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in KasApiController index', [
                'user_id' => $user->id,
                'penduduk_id' => $pendudukId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar kas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent payments for the authenticated user.
     */
    public function getRecentPayments(Request $request)
    {
        $user = Auth::user();

        if (!$user->penduduk) {
            Log::warning('KasApiController: User not linked to a resident for getRecentPayments.', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'User not linked to a resident.'
            ], 403);
        }

        $pendudukId = $user->penduduk->id;
        $limit = $request->get('limit', 5);

        try {
            $recentPayments = Kas::where('penduduk_id', $pendudukId)
                                ->where('status', 'lunas')
                                ->orderBy('tanggal_bayar', 'desc')
                                ->limit($limit)
                                ->get();

            return response()->json([
                'success' => true,
                'data' => $recentPayments
            ]);
        } catch (\Exception $e) {
            Log::error('Error in KasApiController getRecentPayments', [
                'user_id' => $user->id,
                'penduduk_id' => $pendudukId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil pembayaran terbaru: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment info for RT
     */
    public function getPaymentInfo(Request $request)
    {
        try {
            $rtId = $request->get('rt_id');
            
            if (!$rtId) {
                Log::warning('KasApiController: RT ID is required for getPaymentInfo.');
                return response()->json([
                    'success' => false,
                    'message' => 'RT ID is required'
                ], 400);
            }

            $paymentInfo = PaymentInfo::with('rt.rw')
                ->where('rt_id', $rtId)
                ->where('is_active', true)
                ->first();

            if (!$paymentInfo) {
                Log::info('KasApiController: Payment info not found for RT ID ' . $rtId);
                return response()->json([
                    'success' => false,
                    'message' => 'Payment info not found for this RT'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $paymentInfo->id,
                    'rt_info' => "RT {$paymentInfo->rt->no_rt} / RW {$paymentInfo->rt->rw->no_rw}",
                    'bank_transfer' => $paymentInfo->bank_transfer,
                    'e_wallet' => $paymentInfo->e_wallet,
                    'qr_code' => $paymentInfo->qr_code,
                    'payment_notes' => $paymentInfo->payment_notes,
                    'available_methods' => $paymentInfo->available_methods
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in KasApiController getPaymentInfo', [
                'rt_id' => $request->get('rt_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error fetching payment info: ' . $e->getMessage()
            ], 500);
        }
    }
}
