<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Kas;
use App\Models\Penduduk;
use App\Models\PaymentInfo;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Kk;
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
            $query = Kas::with(['penduduk', 'rt']);

            // Apply role-based filtering
            switch ($user->role) {
                case 'masyarakat':
                    $penduduk = Penduduk::where('user_id', $user->id)->first();
                    if (!$penduduk) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data penduduk tidak ditemukan'
                        ], 404);
                    }
                    $query->where('penduduk_id', $penduduk->id);
                    break;

                case 'rt':
                    $rt = $this->getUserRt($user);
                    if ($rt) {
                        $query->where('rt_id', $rt->id);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'RT tidak ditemukan untuk user ini'
                        ], 404);
                    }
                    break;

                case 'rw':
                    $rw = $this->getUserRw($user);
                    if ($rw) {
                        $rtIds = $rw->rts->pluck('id');
                        $query->whereIn('rt_id', $rtIds);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'RW tidak ditemukan untuk user ini'
                        ], 404);
                    }
                    break;

                case 'admin':
                case 'kades':
                    // Can see all payments
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses tidak diizinkan'
                    ], 403);
            }

            // Filter by status if provided
            if ($request->filled('status')) {
                $statuses = explode(',', $request->status);
                $query->whereIn('status', $statuses);
            }

            // Filter by year if provided
            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }

            $payments = $query->orderBy('tahun', 'desc')
                             ->orderBy('minggu_ke', 'desc')
                             ->get();

            // Transform the data
            $transformedPayments = $payments->map(function($kas) {
                return [
                    'id' => $kas->id,
                    'kas_id' => $kas->id,
                    'penduduk_id' => $kas->penduduk_id,
                    'penduduk_nama' => $kas->penduduk->nama_lengkap ?? 'N/A',
                    'rt_id' => $kas->rt_id,
                    'rt_nama' => $kas->rt->nama_rt ?? 'N/A',
                    'minggu_ke' => $kas->minggu_ke,
                    'tahun' => $kas->tahun,
                    'jumlah' => $kas->jumlah,
                    'denda' => $kas->denda ?? 0,
                    'total_bayar' => $kas->jumlah + ($kas->denda ?? 0),
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
                    'can_confirm' => $kas->status === 'menunggu_konfirmasi',
                    'confirmed_by' => $kas->confirmed_by,
                    'confirmed_at' => $kas->confirmed_at,
                    'confirmation_notes' => $kas->confirmation_notes,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedPayments,
                'total' => $transformedPayments->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting payment list', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar pembayaran'
            ], 500);
        }
    }

    /**
     * Get payment info for user's RT - FIXED
     */
    public function getPaymentInfoForUserRt(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role === 'masyarakat') {
                // Fixed: Use proper relationship chain through KK
                $penduduk = Penduduk::where('user_id', $user->id)
                                   ->with(['kk.rt'])
                                   ->first();
                
                if (!$penduduk) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data penduduk tidak ditemukan'
                    ], 404);
                }

                if (!$penduduk->kk || !$penduduk->kk->rt) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data RT tidak ditemukan untuk penduduk ini'
                    ], 404);
                }

                $rtId = $penduduk->kk->rt->id;
                
            } elseif ($user->role === 'rt') {
                $rt = $this->getUserRt($user);
                if (!$rt) {
                    return response()->json([
                        'success' => false,
                        'message' => 'RT tidak ditemukan untuk user ini'
                    ], 404);
                }
                $rtId = $rt->id;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diizinkan'
                ], 403);
            }

            return $this->getPaymentInfoByRt($request, $rtId);

        } catch (\Exception $e) {
            Log::error('Error getting payment info for user RT', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat informasi pembayaran'
            ], 500);
        }
    }

    /**
     * Get payment info by RT ID - ENHANCED
     */
    public function getPaymentInfoByRt(Request $request, $rtId)
    {
        try {
            $user = Auth::user();
            
            // Authorization check
            if ($user->role === 'rt') {
                $userRt = $this->getUserRt($user);
                if (!$userRt || $userRt->id != $rtId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses tidak diizinkan untuk RT ini'
                    ], 403);
                }
            } elseif ($user->role === 'rw') {
                $userRw = $this->getUserRw($user);
                if (!$userRw || !$userRw->rts->contains('id', $rtId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses tidak diizinkan untuk RT ini'
                    ], 403);
                }
            } elseif ($user->role === 'masyarakat') {
                // For masyarakat, verify they belong to this RT
                $penduduk = Penduduk::where('user_id', $user->id)->with('kk.rt')->first();
                if (!$penduduk || !$penduduk->kk || $penduduk->kk->rt->id != $rtId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses tidak diizinkan untuk RT ini'
                    ], 403);
                }
            } elseif (!in_array($user->role, ['admin', 'kades'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diizinkan'
                ], 403);
            }

            $paymentInfo = PaymentInfo::where('rt_id', $rtId)
                                     ->where('is_active', true)
                                     ->first();

            if (!$paymentInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informasi pembayaran tidak ditemukan untuk RT ini'
                ], 404);
            }

            // Use model accessors for better data consistency
            $data = [
                'id' => $paymentInfo->id,
                'rt_id' => $paymentInfo->rt_id,
                'has_bank_transfer' => $paymentInfo->has_bank_transfer,
                'bank_name' => $paymentInfo->bank_name,
                'bank_account_number' => $paymentInfo->bank_account_number,
                'bank_account_name' => $paymentInfo->bank_account_name,
                'has_e_wallet' => $paymentInfo->has_e_wallet,
                'e_wallet_list' => $paymentInfo->e_wallet_list,
                'has_qr_code' => $paymentInfo->has_qr_code,
                'qr_code_path' => $paymentInfo->qr_code_url, // Use accessor
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
            Log::error('Error getting payment info by RT', [
                'rt_id' => $rtId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat informasi pembayaran'
            ], 500);
        }
    }

    /**
     * Confirm payment - ENHANCED
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

            $kas = Kas::with(['penduduk', 'rt'])->findOrFail($paymentId);
            
            // Additional authorization check
            if ($user->role === 'rt') {
                $userRt = $this->getUserRt($user);
                if (!$userRt || $kas->rt_id !== $userRt->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses tidak diizinkan untuk konfirmasi pembayaran ini'
                    ], 403);
                }
            } elseif ($user->role === 'rw') {
                $userRw = $this->getUserRw($user);
                if (!$userRw || !$userRw->rts->contains('id', $kas->rt_id)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses tidak diizinkan untuk konfirmasi pembayaran ini'
                    ], 403);
                }
            }
            
            if ($kas->status !== 'menunggu_konfirmasi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak dalam status menunggu konfirmasi'
                ], 400);
            }

            // Use model method for consistency
            $kas->markAsLunas($user->id, $request->input('notes', 'Dikonfirmasi oleh ' . $user->name));

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
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengkonfirmasi pembayaran'
            ], 500);
        }
    }

    /**
     * Helper methods - ENHANCED
     */
    private function getUserRt($user)
    {
        try {
            // Method 1: Check if user has penduduk relationship and is RT ketua
            if ($user->penduduk && $user->penduduk->rtKetua) {
                return $user->penduduk->rtKetua;
            }
            
            // Method 2: Check if user is directly assigned as RT ketua
            $rt = Rt::where('ketua_rt_id', $user->penduduk->id ?? null)->first();
            if ($rt) {
                return $rt;
            }

            // Method 3: Check by user_id if there's a direct relationship
            $rt = Rt::whereHas('ketua', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->first();
            
            return $rt;
        } catch (\Exception $e) {
            Log::error('Error getting user RT', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return null;
        }
    }

    private function getUserRw($user)
    {
        try {
            // Method 1: Check if user has penduduk relationship and is RW ketua
            if ($user->penduduk && $user->penduduk->rwKetua) {
                return $user->penduduk->rwKetua;
            }
            
            // Method 2: Check if user is directly assigned as RW ketua
            $rw = Rw::where('ketua_rw_id', $user->penduduk->id ?? null)->first();
            if ($rw) {
                return $rw;
            }

            // Method 3: Check by user_id if there's a direct relationship
            $rw = Rw::whereHas('ketua', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->first();
            
            return $rw;
        } catch (\Exception $e) {
            Log::error('Error getting user RW', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return null;
        }
    }

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
