<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Kas;
use App\Models\Penduduk;
use App\Models\PaymentInfo;
use Carbon\Carbon;

class PaymentApiController extends Controller
{
    /**
     * Get payment list for authenticated user
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'masyarakat') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diizinkan'
                ], 403);
            }

            $penduduk = Penduduk::where('user_id', $user->id)->first();
            if (!$penduduk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penduduk tidak ditemukan'
                ], 404);
            }

            $query = Kas::where('penduduk_id', $penduduk->id)
                        ->orderBy('tahun', 'desc')
                        ->orderBy('minggu_ke', 'desc');

            // Filter by status if provided
            if ($request->filled('status')) {
                $statuses = explode(',', $request->status);
                $query->whereIn('status', $statuses);
            }

            $payments = $query->get()->map(function($kas) {
                return [
                    'id' => $kas->id,
                    'kas_id' => $kas->id,
                    'minggu_ke' => $kas->minggu_ke,
                    'tahun' => $kas->tahun,
                    'jumlah' => $kas->jumlah,
                    'denda' => $kas->denda,
                    'total_bayar' => $kas->jumlah + $kas->denda,
                    'tanggal_jatuh_tempo' => $kas->tanggal_jatuh_tempo,
                    'tanggal_jatuh_tempo_formatted' => $kas->tanggal_jatuh_tempo ? $kas->tanggal_jatuh_tempo->format('d M Y') : '-',
                    'tanggal_bayar' => $kas->tanggal_bayar,
                    'tanggal_bayar_formatted' => $kas->tanggal_bayar ? $kas->tanggal_bayar->format('d M Y H:i') : '-',
                    'status' => $kas->status,
                    'status_text' => $this->getStatusText($kas->status),
                    'is_overdue' => $kas->status === 'belum_bayar' && $kas->tanggal_jatuh_tempo && $kas->tanggal_jatuh_tempo->isPast(),
                    'metode_bayar' => $kas->metode_bayar,
                    'metode_bayar_formatted' => $this->getMetodeBayarText($kas->metode_bayar),
                    'bukti_bayar_file' => $kas->bukti_bayar_file,
                    'bukti_bayar_uploaded_at' => $kas->bukti_bayar_uploaded_at,
                    'bukti_bayar_uploaded_at_formatted' => $kas->bukti_bayar_uploaded_at ? $kas->bukti_bayar_uploaded_at->format('d M Y H:i') : '-',
                    'can_pay' => in_array($kas->status, ['belum_bayar', 'terlambat']),
                    'rt_id' => $kas->rt_id,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $payments,
                'total' => $payments->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting payment list', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar pembayaran',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get payment info for user's RT
     */
    public function getPaymentInfoForUserRt(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'masyarakat') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diizinkan'
                ], 403);
            }

            $penduduk = Penduduk::where('user_id', $user->id)->first();
            if (!$penduduk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penduduk tidak ditemukan'
                ], 404);
            }

            $paymentInfo = PaymentInfo::where('rt_id', $penduduk->rt_id)
                                     ->where('is_active', true)
                                     ->first();

            if (!$paymentInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informasi pembayaran belum tersedia untuk RT Anda'
                ], 404);
            }

            // Format payment info data
            $data = [
                'id' => $paymentInfo->id,
                'rt_id' => $paymentInfo->rt_id,
                'has_bank_transfer' => !empty($paymentInfo->bank_name) && !empty($paymentInfo->bank_account_number),
                'bank_name' => $paymentInfo->bank_name,
                'bank_account_number' => $paymentInfo->bank_account_number,
                'bank_account_name' => $paymentInfo->bank_account_name,
                'has_e_wallet' => !empty($paymentInfo->dana_number) || !empty($paymentInfo->gopay_number) || !empty($paymentInfo->ovo_number) || !empty($paymentInfo->shopeepay_number),
                'e_wallet_list' => array_filter([
                    'dana' => $paymentInfo->dana_number,
                    'gopay' => $paymentInfo->gopay_number,
                    'ovo' => $paymentInfo->ovo_number,
                    'shopeepay' => $paymentInfo->shopeepay_number,
                ]),
                'has_qr_code' => !empty($paymentInfo->qr_code_path),
                'qr_code_path' => $paymentInfo->qr_code_path ? asset('storage/' . $paymentInfo->qr_code_path) : null,
                'qr_code_description' => $paymentInfo->qr_code_description,
                'payment_notes' => $paymentInfo->payment_notes,
                'is_active' => $paymentInfo->is_active,
                'created_at' => $paymentInfo->created_at,
                'updated_at' => $paymentInfo->updated_at,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting payment info for user RT', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat informasi pembayaran',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get payment info by RT ID
     */
    public function getPaymentInfoByRt(Request $request, $rtId)
    {
        try {
            $paymentInfo = PaymentInfo::where('rt_id', $rtId)
                                     ->where('is_active', true)
                                     ->first();

            if (!$paymentInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informasi pembayaran tidak ditemukan untuk RT ini'
                ], 404);
            }

            $data = [
                'id' => $paymentInfo->id,
                'rt_id' => $paymentInfo->rt_id,
                'has_bank_transfer' => !empty($paymentInfo->bank_name) && !empty($paymentInfo->bank_account_number),
                'bank_name' => $paymentInfo->bank_name,
                'bank_account_number' => $paymentInfo->bank_account_number,
                'bank_account_name' => $paymentInfo->bank_account_name,
                'has_e_wallet' => !empty($paymentInfo->dana_number) || !empty($paymentInfo->gopay_number) || !empty($paymentInfo->ovo_number) || !empty($paymentInfo->shopeepay_number),
                'e_wallet_list' => array_filter([
                    'dana' => $paymentInfo->dana_number,
                    'gopay' => $paymentInfo->gopay_number,
                    'ovo' => $paymentInfo->ovo_number,
                    'shopeepay' => $paymentInfo->shopeepay_number,
                ]),
                'has_qr_code' => !empty($paymentInfo->qr_code_path),
                'qr_code_path' => $paymentInfo->qr_code_path ? asset('storage/' . $paymentInfo->qr_code_path) : null,
                'qr_code_description' => $paymentInfo->qr_code_description,
                'payment_notes' => $paymentInfo->payment_notes,
                'is_active' => $paymentInfo->is_active,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting payment info by RT', [
                'rt_id' => $rtId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat informasi pembayaran',
                'data' => null
            ], 500);
        }
    }

    /**
     * Confirm payment
     */
    public function confirmPayment(Request $request, $paymentId)
    {
        try {
            $user = Auth::user();
            
            if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diizinkan'
                ], 403);
            }

            $kas = Kas::findOrFail($paymentId);
            
            if ($kas->status !== 'menunggu_konfirmasi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak dalam status menunggu konfirmasi'
                ], 400);
            }

            $kas->update([
                'status' => 'lunas',
                'tanggal_bayar' => Carbon::now(),
                'confirmed_by' => $user->id,
                'confirmed_at' => Carbon::now(),
                'confirmation_notes' => $request->input('notes', 'Dikonfirmasi oleh ' . $user->name)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi',
                'data' => [
                    'kas_id' => $kas->id,
                    'status' => $kas->status,
                    'confirmed_by' => $user->name,
                    'confirmed_at' => $kas->confirmed_at->format('d M Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error confirming payment', [
                'payment_id' => $paymentId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengkonfirmasi pembayaran'
            ], 500);
        }
    }

    /**
     * Helper methods
     */
    private function getStatusText($status)
    {
        $statusTexts = [
            'belum_bayar' => 'Belum Bayar',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'lunas' => 'Lunas',
            'terlambat' => 'Terlambat',
            'ditolak' => 'Ditolak'
        ];

        return $statusTexts[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    private function getMetodeBayarText($metode)
    {
        $methods = [
            'tunai' => 'Tunai',
            'bank_transfer' => 'Transfer Bank',
            'e_wallet' => 'E-Wallet',
            'qr_code' => 'QR Code',
        ];
        
        return $metode ? ($methods[$metode] ?? ucfirst(str_replace('_', ' ', $metode))) : '-';
    }
}
