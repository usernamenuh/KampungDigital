<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Kas;
use App\Models\Penduduk;
use Carbon\Carbon;

class KasApiController extends Controller
{
    /**
     * Get Kas statistics for the authenticated user.
     */
    public function getStats(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user->penduduk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penduduk tidak ditemukan untuk pengguna ini.'
                ], 404);
            }

            $pendudukId = $user->penduduk->penduduk_id;
            $currentYear = Carbon::now()->year;

            $query = Kas::where('penduduk_id', $pendudukId)
                        ->where('tahun', $currentYear);

            $kasLunas = (clone $query)->where('status', 'lunas')->count();
            $kasBelumBayar = (clone $query)->where('status', 'belum_bayar')->count();
            $kasMenungguKonfirmasi = (clone $query)->where('status', 'menunggu_konfirmasi')->count();
            
            $kasTerlambat = (clone $query)->where('status', 'belum_bayar')
                                    ->where('tanggal_jatuh_tempo', '<', Carbon::now())
                                    ->count();

            $totalKasAnda = (clone $query)->where('status', 'lunas')->sum('jumlah');
            
            $totalWeeksInYear = 52;
            $totalPaidOrPending = $kasLunas + $kasMenungguKonfirmasi;
            $isYearCompleted = ($totalPaidOrPending >= $totalWeeksInYear);

            return response()->json([
                'success' => true,
                'data' => [
                    'kasLunas' => $kasLunas,
                    'kasBelumBayar' => $kasBelumBayar,
                    'kasTerlambat' => $kasTerlambat,
                    'kasMenungguKonfirmasi' => $kasMenungguKonfirmasi,
                    'totalKasAnda' => $totalKasAnda,
                    'isYearCompleted' => $isYearCompleted,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting Kas stats for user', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik kas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's Kas list with pagination - Fixed for masyarakat dashboard
     */
    public function getUserKas(Request $request, $userId)
    {
        try {
            $user = Auth::user();
            if ($user->id != $userId && $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diizinkan.'
                ], 403);
            }

            $penduduk = Penduduk::where('user_id', $userId)->first();
            if (!$penduduk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penduduk tidak ditemukan.'
                ], 404);
            }

            $query = Kas::where('penduduk_id', $penduduk->penduduk_id)
                        ->orderBy('tahun', 'desc')
                        ->orderBy('minggu_ke', 'desc');

            if ($request->filled('status')) {
                $statuses = explode(',', $request->status);
                $query->whereIn('status', $statuses);
            }

            $kasList = $query->paginate($request->get('limit', 10));

            $formattedKasList = $kasList->map(function($kas) {
                return [
                    'id' => $kas->kas_id,
                    'kas_id' => $kas->kas_id,
                    'minggu_ke' => $kas->minggu_ke,
                    'tahun' => $kas->tahun,
                    'jumlah' => $kas->jumlah,
                    'denda' => $kas->denda,
                    'total_bayar' => $kas->total_bayar,
                    'tanggal_jatuh_tempo' => $kas->tanggal_jatuh_tempo,
                    'tanggal_jatuh_tempo_formatted' => $kas->tanggal_jatuh_tempo ? $kas->tanggal_jatuh_tempo->format('d M Y') : '-',
                    'tanggal_bayar' => $kas->tanggal_bayar,
                    'tanggal_bayar_formatted' => $kas->tanggal_bayar ? $kas->tanggal_bayar->format('d M Y H:i') : '-',
                    'status' => $kas->status,
                    'status_text' => $kas->status_text,
                    'is_overdue' => $kas->is_overdue,
                    'metode_bayar' => $kas->metode_bayar,
                    'metode_bayar_formatted' => $kas->metode_bayar_formatted,
                    'bukti_bayar_file' => $kas->bukti_bayar_file,
                    'bukti_bayar_uploaded_at' => $kas->bukti_bayar_uploaded_at,
                    'bukti_bayar_uploaded_at_formatted' => $kas->bukti_bayar_uploaded_at ? $kas->bukti_bayar_uploaded_at->format('d M Y H:i') : '-',
                    'can_pay' => in_array($kas->status, ['belum_bayar', 'terlambat']),
                    'rt_id' => $kas->rt_id,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedKasList,
                'pagination' => [
                    'total' => $kasList->total(),
                    'per_page' => $kasList->perPage(),
                    'current_page' => $kasList->currentPage(),
                    'last_page' => $kasList->lastPage(),
                    'from' => $kasList->firstItem(),
                    'to' => $kasList->lastItem(),
                    'has_more' => $kasList->hasMorePages(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting user Kas list', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar kas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent payments for the authenticated user.
     */
    public function getRecentPayments(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user->penduduk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penduduk tidak ditemukan untuk pengguna ini.'
                ], 404);
            }

            $recentPayments = Kas::where('penduduk_id', $user->penduduk->penduduk_id)
                                ->where('status', 'lunas')
                                ->whereNotNull('tanggal_bayar')
                                ->orderBy('tanggal_bayar', 'desc')
                                ->limit($request->get('limit', 5))
                                ->get();

            $formattedPayments = $recentPayments->map(function($payment) {
                return [
                    'id' => $payment->kas_id,
                    'minggu_ke' => $payment->minggu_ke,
                    'jumlah' => $payment->jumlah,
                    'tanggal_bayar' => $payment->tanggal_bayar,
                    'tanggal_bayar_formatted' => $payment->tanggal_bayar ? $payment->tanggal_bayar->format('d M Y H:i') : '-',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedPayments
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recent payments', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat riwayat pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}
