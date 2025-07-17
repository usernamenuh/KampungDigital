<?php

namespace App\Http\Controllers;

use App\Models\PaymentInfo;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Added for logging

class PaymentInfoController extends Controller
{
  /**
   * Display a listing of the payment info.
   * Returns JSON for API requests, or a view for traditional requests.
   */
  public function index(Request $request)
  {
      $user = Auth::user();
      $query = PaymentInfo::with('rt.rw');
      $rtsForSelection = collect(); // Initialize an empty collection for RTs dropdown

      // Apply role-based filtering for the list
      if ($user->hasRole('rt')) {
          // RT can only see their own payment info
          $userRtId = $user->penduduk->rtKetua->id ?? null;
          if ($userRtId) {
              $query->where('rt_id', $userRtId);
              $rtsForSelection = Rt::where('id', $userRtId)->with('rw')->get(); // Only their RT for selection
          } else {
              // If RT user has no associated RT Ketua, return empty
              $paymentInfos = collect();
          }
      } elseif ($user->hasRole('rw')) {
          // RW can see payment info for RTs within their RW
          $userRwId = $user->penduduk->rwKetua->id ?? null;
          if ($userRwId) {
              $rtsInRw = Rt::where('rw_id', $userRwId)->pluck('id');
              $query->whereIn('rt_id', $rtsInRw);
              $rtsForSelection = Rt::where('rw_id', $userRwId)->with('rw')->get(); // RTs within their RW for selection
          } else {
              // If RW user has no associated RW Ketua, return empty
              $paymentInfos = collect();
          }
      } elseif ($user->hasRole('admin') || $user->hasRole('kades')) {
          // Admin and Kades can see all, no additional filtering needed here
          $rtsForSelection = Rt::with('rw')->get(); // All RTs for selection
      }

      // Execute query if not already set to empty collection
      if (!isset($paymentInfos)) {
          $paymentInfos = $query->get();
      }
      
      // For API requests, always return JSON
      if ($request->ajax() || $request->wantsJson()) {
          return response()->json([
              'success' => true,
              'data' => $paymentInfos->map(function($info) {
                  // Map to include accessors for frontend
                  return [
                      'id' => $info->id,
                      'rt_id' => $info->rt_id,
                      'rt_no' => $info->rt_no, // Using accessor
                      'rw_no' => $info->rw_no, // Using accessor
                      'bank_name' => $info->bank_name,
                      'bank_account_number' => $info->bank_account_number,
                      'bank_account_name' => $info->bank_account_name,
                      'dana_number' => $info->dana_number,
                      'dana_account_name' => $info->dana_account_name,
                      'gopay_number' => $info->gopay_number,
                      'gopay_account_name' => $info->gopay_account_name,
                      'ovo_number' => $info->ovo_number,
                      'ovo_account_name' => $info->ovo_account_name,
                      'shopeepay_number' => $info->shopeepay_number,
                      'shopeepay_account_name' => $info->shopeepay_account_name,
                      'qr_code_path' => $info->qr_code_path,
                      'qr_code_description' => $info->qr_code_description,
                      'qr_code_account_name' => $info->qr_code_account_name,
                      'payment_notes' => $info->payment_notes,
                      'is_active' => $info->is_active,
                      'has_bank_transfer' => $info->has_bank_transfer,
                      'has_e_wallet' => $info->has_e_wallet,
                      'e_wallet_list' => $info->e_wallet_list, // This accessor now returns structured data
                      'has_qr_code' => $info->has_qr_code,
                      'qr_code_url' => $info->qr_code_url,
                      'created_at' => $info->created_at,
                      'updated_at' => $info->updated_at,
                  ];
              }),
              'rts_for_selection' => $rtsForSelection->map(function($rt) {
                  return [
                      'id' => $rt->id,
                      'no_rt' => $rt->no_rt,
                      'no_rw' => $rt->rw->no_rw ?? 'N/A',
                  ];
              }),
              'message' => 'Informasi pembayaran berhasil dimuat.'
          ]);
      }

      // This part is for traditional Blade view rendering, which might not be used if using Alpine.js for data fetching
      // This will only be reached if the route is accessed directly (not via AJAX/JSON)
      return view('payment-info.index', compact('paymentInfos', 'rtsForSelection'));
  }

  /**
   * Store a newly created payment info in storage.
   */
  public function store(Request $request)
  {
      $user = Auth::user();
      $targetRtId = $request->input('rt_id'); // Get rt_id from request

      // Authorization logic
      if ($user->hasRole('rt')) {
          // RT can only add for their own RT
          $userRtId = $user->penduduk->rtKetua->id ?? null;
          if (!$userRtId || $targetRtId != $userRtId) {
              return response()->json(['success' => false, 'message' => 'Anda tidak diizinkan menambahkan informasi pembayaran untuk RT lain.'], 403);
          }
          $rt = Rt::find($userRtId);
      } elseif ($user->hasRole('admin') || $user->hasRole('kades') || $user->hasRole('rw')) {
          // Admin/Kades/RW can add for any RT, but rt_id must be provided and exist
          $request->validate(['rt_id' => 'required|exists:rts,id']);
          $rt = Rt::find($targetRtId);
      } else {
          return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk mengatur informasi pembayaran.'], 403);
      }

      if (!$rt) {
          return response()->json(['success' => false, 'message' => 'RT target tidak ditemukan.'], 404);
      }

      $validatedData = $request->validate([
          'bank_name' => 'nullable|string|max:255',
          'bank_account_number' => 'nullable|string|max:255',
          'bank_account_name' => 'nullable|string|max:255',
          'dana_number' => 'nullable|string|max:255',
          'dana_account_name' => 'nullable|string|max:255',
          'gopay_number' => 'nullable|string|max:255',
          'gopay_account_name' => 'nullable|string|max:255',
          'ovo_number' => 'nullable|string|max:255',
          'ovo_account_name' => 'nullable|string|max:255',
          'shopeepay_number' => 'nullable|string|max:255',
          'shopeepay_account_name' => 'nullable|string|max:255',
          'qr_code_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          'qr_code_description' => 'nullable|string|max:500',
          'qr_code_account_name' => 'nullable|string|max:255',
          'payment_notes' => 'nullable|string',
          'is_active' => 'boolean',
      ]);

      DB::beginTransaction();
      try {
          // If is_active is true, deactivate any existing active payment info for this RT
          if (isset($validatedData['is_active']) && $validatedData['is_active']) {
              PaymentInfo::where('rt_id', $rt->id)->update(['is_active' => false]);
          }

          $paymentInfo = new PaymentInfo();
          $paymentInfo->rt_id = $rt->id; // Assign the target RT ID
          $paymentInfo->fill($validatedData);
          $paymentInfo->is_active = $validatedData['is_active'] ?? true; // Default to active if not provided

          if ($request->hasFile('qr_code_file')) {
              $path = $request->file('qr_code_file')->store('qrcodes', 'public');
              $paymentInfo->qr_code_path = $path;
          }

          $paymentInfo->save();

          DB::commit();
          return response()->json(['success' => true, 'message' => 'Informasi pembayaran berhasil ditambahkan.', 'data' => $paymentInfo->load('rt.rw')->append(['qr_code_url', 'e_wallet_list', 'has_bank_transfer', 'has_e_wallet', 'has_qr_code'])]);
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
      
      // Authorization check: Ensure user has permission to update THIS paymentInfo
      if ($user->hasRole('rt')) {
          $userRtId = $user->penduduk->rtKetua->id ?? null;
          if (!$userRtId || $paymentInfo->rt_id !== $userRtId) {
              return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk mengubah informasi pembayaran ini.'], 403);
          }
      } elseif ($user->hasRole('rw')) {
          $userRw = $user->penduduk->rwKetua ?? null;
          if (!$userRw || !$userRw->rts->contains('id', $paymentInfo->rt_id)) {
              return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk mengubah informasi pembayaran ini.'], 403);
          }
      } elseif (!($user->hasRole('admin') || $user->hasRole('kades'))) {
          return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk mengubah informasi pembayaran ini.'], 403);
      }

      $validatedData = $request->validate([
          'bank_name' => 'nullable|string|max:255',
          'bank_account_number' => 'nullable|string|max:255',
          'bank_account_name' => 'nullable|string|max:255',
          'dana_number' => 'nullable|string|max:255',
          'dana_account_name' => 'nullable|string|max:255',
          'gopay_number' => 'nullable|string|max:255',
          'gopay_account_name' => 'nullable|string|max:255',
          'ovo_number' => 'nullable|string|max:255',
          'ovo_account_name' => 'nullable|string|max:255',
          'shopeepay_number' => 'nullable|string|max:255',
          'shopeepay_account_name' => 'nullable|string|max:255',
          'qr_code_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          'qr_code_description' => 'nullable|string|max:500',
          'qr_code_account_name' => 'nullable|string|max:255',
          'payment_notes' => 'nullable|string',
          'is_active' => 'boolean',
          'clear_qr_code' => 'nullable|boolean',
      ]);

      DB::beginTransaction();
      try {
          // If this payment info is being set to active, deactivate others for this RT
          if (isset($validatedData['is_active']) && $validatedData['is_active']) {
              PaymentInfo::where('rt_id', $paymentInfo->rt_id)
                  ->where('id', '!=', $paymentInfo->id)
                  ->update(['is_active' => false]);
          }

          // Handle QR code file upload
          if ($request->hasFile('qr_code_file')) {
              // Delete old QR code if exists
              if ($paymentInfo->qr_code_path) {
                  Storage::delete($paymentInfo->qr_code_path);
              }
              // Store directly in 'qrcodes' subdirectory of the 'public' disk
              $path = $request->file('qr_code_file')->store('qrcodes', 'public');
              $paymentInfo->qr_code_path = $path;
          } elseif ($request->input('clear_qr_code')) { // Allow clearing QR code
              if ($paymentInfo->qr_code_path) {
                  Storage::delete($paymentInfo->qr_code_path);
              }
              $paymentInfo->qr_code_path = null;
              $paymentInfo->qr_code_description = null; // Clear description and account name too
              $paymentInfo->qr_code_account_name = null;
          }

          $paymentInfo->fill($validatedData);
          $paymentInfo->save();

          DB::commit();
          return response()->json(['success' => true, 'message' => 'Informasi pembayaran berhasil diperbarui.', 'data' => $paymentInfo->load('rt.rw')->append(['qr_code_url', 'e_wallet_list', 'has_bank_transfer', 'has_e_wallet', 'has_qr_code'])]);
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

      // Authorization check: Ensure user has permission to delete THIS paymentInfo
      if ($user->hasRole('rt')) {
          $userRtId = $user->penduduk->rtKetua->id ?? null;
          if (!$userRtId || $paymentInfo->rt_id !== $userRtId) {
              return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk menghapus informasi pembayaran ini.'], 403);
          }
      } elseif ($user->hasRole('rw')) {
          $userRw = $user->penduduk->rwKetua ?? null;
          if (!$userRw || !$userRw->rts->contains('id', $paymentInfo->rt_id)) {
              return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk menghapus informasi pembayaran ini.'], 403);
          }
      } elseif (!($user->hasRole('admin') || $user->hasRole('kades'))) {
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
   * Test QR code URL generation and file existence.
   */
  public function testQrCodeUrl(Request $request)
  {
      $path = $request->query('path'); // Get the path from query parameter, e.g., ?path=qrcodes/your-image.png

      if (!$path) {
          return response()->json([
              'success' => false,
              'message' => 'Parameter "path" diperlukan. Contoh: /test-qr-url?path=qrcodes/namafile.png'
          ], 400);
      }

      $exists = Storage::disk('public')->exists($path);
      $url = Storage::disk('public')->url($path);

      return response()->json([
          'success' => true,
          'requested_path' => $path,
          'file_exists_in_storage_disk' => $exists,
          'generated_public_url' => $url,
          'message' => $exists ? 'File ditemukan dan URL berhasil dibuat.' : 'File TIDAK ditemukan di direktori storage/app/public.'
      ]);
  }

  /**
   * API endpoint to get payment info for the authenticated RT user.
   */
  public function getPaymentInfoForUserRt()
  {
      $user = Auth::user();
      if (!$user->hasRole('rt')) {
          return response()->json([
              'success' => false,
              'message' => 'Akses ditolak. Hanya Ketua RT yang dapat melihat informasi pembayaran mereka.',
          ], 403);
      }

      $rt = $user->penduduk->rtKetua;
      if (!$rt) {
          return response()->json([
              'success' => false,
              'message' => 'Anda bukan Ketua RT atau data RT tidak ditemukan.',
          ], 404);
      }

      $paymentInfo = PaymentInfo::with('rt.rw')->where('rt_id', $rt->id)->first();

      if (!$paymentInfo) {
          return response()->json([
              'success' => false,
              'message' => 'Informasi pembayaran untuk RT Anda belum diatur.',
              'data' => null,
          ], 404);
      }

      // Append accessors for API response
      $paymentInfo->append(['qr_code_url', 'e_wallet_list', 'has_bank_transfer', 'has_e_wallet', 'has_qr_code']);

      return response()->json([
          'success' => true,
          'message' => 'Informasi pembayaran RT berhasil diambil.',
          'data' => $paymentInfo,
      ]);
  }
}
