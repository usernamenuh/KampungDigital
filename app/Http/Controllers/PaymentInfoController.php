<?php

namespace App\Http\Controllers;

use App\Models\PaymentInfo;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class PaymentInfoController extends Controller
{
    /**
     * Display a listing of the payment info.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $rt = null;

        // Determine the RT for the current user
        if ($user->hasRole('rt') && $user->penduduk && $user->penduduk->rtKetua) {
            $rt = $user->penduduk->rtKetua;
        } elseif ($user->hasRole('admin') || $user->hasRole('kades') || $user->hasRole('rw')) {
            // Admins, Kades, RWs might view payment info for a specific RT
            // For now, we'll just show their own if they are also an RT ketua,
            // or show nothing if not explicitly selected.
            // A more robust solution would involve a dropdown to select RT for these roles.
            if ($request->has('rt_id')) {
                $rt = Rt::find($request->input('rt_id'));
            }
        }

        if (!$rt) {
            // If no RT is found or selected, return an empty response or error for AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'RT tidak ditemukan atau tidak dipilih.'
                ], 404);
            }
            // For non-AJAX requests, redirect or show a message
            return view('payment-info.index', ['paymentInfo' => null, 'rt' => null]);
        }

        $paymentInfo = PaymentInfo::where('rt_id', $rt->id)->first();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $paymentInfo,
                'message' => $paymentInfo ? 'Informasi pembayaran berhasil dimuat.' : 'Informasi pembayaran belum diatur untuk RT ini.'
            ]);
        }

        return view('payment-info.index', compact('paymentInfo', 'rt'));
    }

    /**
     * Store a newly created payment info in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $rt = $this->getUserRt($user);

        if (!$rt) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk mengatur informasi pembayaran RT.'], 403);
        }

        $validatedData = $request->validate([
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'dana_number' => 'nullable|string|max:255',
            'gopay_number' => 'nullable|string|max:255',
            'ovo_number' => 'nullable|string|max:255',
            'shopeepay_number' => 'nullable|string|max:255',
            'qr_code_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'qr_code_description' => 'nullable|string|max:500',
            'payment_notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Deactivate any existing active payment info for this RT
            PaymentInfo::where('rt_id', $rt->id)->update(['is_active' => false]);

            $paymentInfo = new PaymentInfo();
            $paymentInfo->rt_id = $rt->id;
            $paymentInfo->fill($validatedData);
            $paymentInfo->is_active = $validatedData['is_active'] ?? true; // Default to active if not provided

            if ($request->hasFile('qr_code_file')) {
                $path = $request->file('qr_code_file')->store('public/qrcodes');
                $paymentInfo->qr_code_path = $path;
            }

            $paymentInfo->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Informasi pembayaran berhasil ditambahkan.', 'data' => $paymentInfo]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing payment info: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan informasi pembayaran: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified payment info in storage.
     */
    public function update(Request $request, PaymentInfo $paymentInfo)
    {
        $user = Auth::user();
        $rt = $this->getUserRt($user);

        if (!$rt || $paymentInfo->rt_id !== $rt->id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk mengubah informasi pembayaran ini.'], 403);
        }

        $validatedData = $request->validate([
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'dana_number' => 'nullable|string|max:255',
            'gopay_number' => 'nullable|string|max:255',
            'ovo_number' => 'nullable|string|max:255',
            'shopeepay_number' => 'nullable|string|max:255',
            'qr_code_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'qr_code_description' => 'nullable|string|max:500',
            'payment_notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // If this payment info is being set to active, deactivate others for this RT
            if (isset($validatedData['is_active']) && $validatedData['is_active']) {
                PaymentInfo::where('rt_id', $rt->id)
                           ->where('id', '!=', $paymentInfo->id)
                           ->update(['is_active' => false]);
            }

            // Handle QR code file upload
            if ($request->hasFile('qr_code_file')) {
                // Delete old QR code if exists
                if ($paymentInfo->qr_code_path) {
                    Storage::delete($paymentInfo->qr_code_path);
                }
                $path = $request->file('qr_code_file')->store('public/qrcodes');
                $paymentInfo->qr_code_path = $path;
            } elseif ($request->input('clear_qr_code')) { // Allow clearing QR code
                if ($paymentInfo->qr_code_path) {
                    Storage::delete($paymentInfo->qr_code_path);
                }
                $paymentInfo->qr_code_path = null;
            }

            $paymentInfo->fill($validatedData);
            $paymentInfo->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Informasi pembayaran berhasil diperbarui.', 'data' => $paymentInfo]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating payment info: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui informasi pembayaran: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified payment info from storage.
     */
    public function destroy(PaymentInfo $paymentInfo)
    {
        $user = Auth::user();
        $rt = $this->getUserRt($user);

        if (!$rt || $paymentInfo->rt_id !== $rt->id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk menghapus informasi pembayaran ini.'], 403);
        }

        DB::beginTransaction();
        try {
            if ($paymentInfo->qr_code_path) {
                Storage::delete($paymentInfo->qr_code_path);
            }
            $paymentInfo->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Informasi pembayaran berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting payment info: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Gagal menghapus informasi pembayaran: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper to get the RT object for the authenticated user.
     */
    private function getUserRt($user)
    {
        if ($user->penduduk && $user->penduduk->rtKetua) {
            return $user->penduduk->rtKetua;
        }
        return null;
    }
}
