<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kas;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\PaymentInfo; // Added PaymentInfo model

class KasPaymentController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
      $this->middleware('role:masyarakat')->only(['showPaymentForm', 'submitPayment', 'paymentSuccess']); // Added paymentSuccess
      $this->middleware('role:rt,rw,kades,admin')->only(['paymentsList', 'confirmPayment', 'showProof', 'downloadProof']); // Added confirmPayment
  }

  public function showPaymentForm(Kas $kas)
  {
      $user = Auth::user();
      // Ensure the user is 'masyarakat' and the kas belongs to their resident profile
      if ($user->role !== 'masyarakat' || ($user->penduduk && $kas->penduduk_id !== $user->penduduk->id)) {
          abort(403, 'Unauthorized action.');
      }

      if ($kas->status === 'lunas' || $kas->status === 'menunggu_konfirmasi') {
          return redirect()->route('dashboard.masyarakat')->with('error', 'Tagihan ini sudah lunas atau sedang menunggu konfirmasi.'); // Redirect to dashboard
      }

      // Fetch payment info for the user's RT
      $paymentInfo = null;
      if ($user->penduduk && $user->penduduk->rt_id) {
          $paymentInfo = PaymentInfo::where('rt_id', $user->penduduk->rt_id)->first();
      }

      return view('kas.payment-form', compact('kas', 'paymentInfo'));
  }

  public function submitPayment(Request $request, Kas $kas)
  {
      $user = Auth::user();
      // Ensure the user is 'masyarakat' and the kas belongs to their resident profile
      if ($user->role !== 'masyarakat' || ($user->penduduk && $kas->penduduk_id !== $user->penduduk->id)) {
          abort(403, 'Unauthorized action.');
      }

      if ($kas->status === 'lunas' || $kas->status === 'menunggu_konfirmasi') {
          return redirect()->route('dashboard.masyarakat')->with('error', 'Tagihan ini sudah lunas atau sedang menunggu konfirmasi.'); // Redirect to dashboard
      }

      $request->validate([
          'metode_bayar' => 'required|in:bank_transfer,e_wallet,qr_code,tunai', // Added tunai
          'jumlah_dibayar' => 'required|numeric|min:0',
          'bukti_bayar_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
          'bukti_bayar_notes' => 'nullable|string|max:500',
      ]);

      DB::beginTransaction();
      try {
          $filePath = $request->file('bukti_bayar_file')->store('public/bukti_pembayaran');
          $filePath = str_replace('public/', 'storage/', $filePath); // Adjust path for public access

          $kas->update([
              'status' => 'menunggu_konfirmasi',
              'tanggal_bayar' => Carbon::now(),
              'jumlah_dibayar' => $request->jumlah_dibayar,
              'metode_bayar' => $request->metode_bayar,
              'bukti_bayar_file' => $filePath,
              'bukti_bayar_notes' => $request->bukti_bayar_notes,
              'bukti_bayar_uploaded_at' => Carbon::now(),
          ]);

          // Create notification for RT/RW/Admin to confirm
          $rtUser = $kas->rt->ketuaRt->user ?? null;
          $rwUser = $kas->rt->rw->ketuaRw->user ?? null;
          $adminUsers = \App\Models\User::where('role', 'admin')->get();

          $notificationMessage = 'Pembayaran kas dari ' . $kas->penduduk->nama_lengkap . ' untuk minggu ke-' . $kas->minggu_ke . ' tahun ' . $kas->tahun . ' menunggu konfirmasi.';

          if ($rtUser) {
              Notifikasi::create([
                  'user_id' => $rtUser->id,
                  'judul' => 'Konfirmasi Pembayaran Kas',
                  'pesan' => $notificationMessage,
                  'link' => route('payments.proof', $kas->id), // Link to kas detail for confirmation
                  'is_read' => false,
              ]);
          }
          if ($rwUser) {
              Notifikasi::create([
                  'user_id' => $rwUser->id,
                  'judul' => 'Konfirmasi Pembayaran Kas',
                  'pesan' => $notificationMessage,
                  'link' => route('payments.proof', $kas->id),
                  'is_read' => false,
              ]);
          }
          foreach ($adminUsers as $adminUser) {
              Notifikasi::create([
                  'user_id' => $adminUser->id,
                  'judul' => 'Konfirmasi Pembayaran Kas',
                  'pesan' => $notificationMessage,
                  'link' => route('payments.proof', $kas->id),
                  'is_read' => false,
              ]);
          }

          DB::commit();
          return redirect()->route('kas.payment.success', $kas->id)->with('success', 'Pembayaran berhasil diajukan. Menunggu konfirmasi.');
      } catch (\Exception $e) {
          DB::rollBack();
          Log::error('Error submitting payment: ' . $e->getMessage());
          return back()->with('error', 'Gagal mengajukan pembayaran: ' . $e->getMessage());
      }
  }

  public function paymentSuccess(Kas $kas)
  {
      $user = Auth::user();
      if ($user->role !== 'masyarakat' || ($user->penduduk && $kas->penduduk_id !== $user->penduduk->id)) {
          abort(403, 'Unauthorized action.');
      }
      return view('kas.payment-success', compact('kas'));
  }

  public function paymentsList(Request $request)
  {
      $user = Auth::user();
      $query = Kas::with(['penduduk.user', 'rt.rw']);

      // Apply role-based filters
      switch ($user->role) {
          case 'rt':
              if ($user->penduduk && $user->penduduk->rtKetua) {
                  $query->where('rt_id', $user->penduduk->rtKetua->id);
              } else {
                  return redirect()->back()->with('error', 'Data RT tidak ditemukan.');
              }
              break;
          case 'rw':
              if ($user->penduduk && $user->penduduk->rwKetua) {
                  $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                  $query->whereIn('rt_id', $rtIds);
              } else {
                  return redirect()->back()->with('error', 'Data RW tidak ditemukan.');
              }
              break;
          // admin dan kades bisa lihat semua
      }

      // Apply status filter
      if ($request->filled('status')) {
          $query->where('status', $request->status);
      } else {
          // Default to showing 'menunggu_konfirmasi' if no status is specified
          $query->whereIn('status', ['menunggu_konfirmasi', 'lunas', 'ditolak']);
      }

      $payments = $query->orderBy('bukti_bayar_uploaded_at', 'desc')->paginate(10);

      return view('payments.list', compact('payments'));
  }

  public function showProof(Kas $kas)
  {
      $user = Auth::user();
      // Authorization: Only RT, RW, Kades, Admin, or the owning Masyarakat can view proof
      if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
          if ($user->role === 'masyarakat' && ($user->penduduk && $kas->penduduk_id !== $user->penduduk->id)) {
              abort(403, 'Unauthorized action.');
          } elseif ($user->role !== 'masyarakat') {
              abort(403, 'Unauthorized action.');
          }
      }

      // Further authorization based on RT/RW scope for RT/RW roles
      if ($user->role === 'rt' && $kas->rt_id !== ($user->penduduk->rtKetua->id ?? null)) {
          abort(403, 'Unauthorized access.');
      } elseif ($user->role === 'rw') {
          $rtIdsInRw = ($user->penduduk->rwKetua->rts->pluck('id')->toArray() ?? []);
          if (!in_array($kas->rt_id, $rtIdsInRw)) {
              abort(403, 'Unauthorized access.');
          }
      }

      return view('payments.proof', compact('kas'));
  }

  public function downloadProof(Kas $kas)
  {
      $user = Auth::user();
      // Authorization: Same as showProof
      if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
          if ($user->role === 'masyarakat' && ($user->penduduk && $kas->penduduk_id !== $user->penduduk->id)) {
              abort(403, 'Unauthorized action.');
          } elseif ($user->role !== 'masyarakat') {
              abort(403, 'Unauthorized action.');
          }
      }

      // Further authorization based on RT/RW scope for RT/RW roles
      if ($user->role === 'rt' && $kas->rt_id !== ($user->penduduk->rtKetua->id ?? null)) {
          abort(403, 'Unauthorized access.');
      } elseif ($user->role === 'rw') {
          $rtIdsInRw = ($user->penduduk->rwKetua->rts->pluck('id')->toArray() ?? []);
          if (!in_array($kas->rt_id, $rtIdsInRw)) {
              abort(403, 'Unauthorized access.');
          }
      }

      if (!$kas->bukti_bayar_file) {
          return back()->with('error', 'Bukti pembayaran tidak ditemukan.');
      }

      $filePath = str_replace('storage/', 'public/', $kas->bukti_bayar_file);
      if (!\Illuminate\Support\Facades\Storage::exists($filePath)) {
          return back()->with('error', 'File bukti pembayaran tidak ditemukan di server.');
      }

      return \Illuminate\Support\Facades\Storage::download($filePath, 'bukti_pembayaran_kas_' . $kas->id . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
  }

  public function confirmPayment(Request $request, Kas $kas)
  {
      $user = Auth::user();
      // Authorization: Only RT, RW, Kades, Admin can confirm
      if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
          abort(403, 'Unauthorized action.');
      }

      // Further authorization based on RT/RW scope
      if ($user->role === 'rt' && $kas->rt_id !== ($user->penduduk->rtKetua->id ?? null)) {
          abort(403, 'Unauthorized access.');
      } elseif ($user->role === 'rw') {
          $rtIdsInRw = ($user->penduduk->rwKetua->rts->pluck('id')->toArray() ?? []);
          if (!in_array($kas->rt_id, $rtIdsInRw)) {
              abort(403, 'Unauthorized access.');
          }
      }

      if ($kas->status !== 'menunggu_konfirmasi') {
          return response()->json(['success' => false, 'message' => 'Pembayaran tidak dalam status menunggu konfirmasi.'], 400);
      }

      $request->validate([
          'action' => 'required|in:approve,reject',
          'catatan_konfirmasi' => 'nullable|string|max:500',
      ]);

      DB::beginTransaction();
      try {
          if ($request->action === 'approve') {
              $kas->update([
                  'status' => 'lunas',
                  'tanggal_bayar' => $kas->tanggal_bayar ?? Carbon::now(), // Use existing tanggal_bayar if set by user, else now
                  'confirmed_by' => $user->id,
                  'confirmed_at' => Carbon::now(),
                  'confirmation_notes' => $request->catatan_konfirmasi ?? 'Pembayaran dikonfirmasi.',
              ]);
              // Update RT saldo only if it was not already updated by the user's payment submission
              $rt = $kas->rt;
              if ($rt && $kas->jumlah_dibayar > 0) { // Only add to saldo if there was an amount paid
                  $rt->saldo += $kas->jumlah_dibayar; // Use jumlah_dibayar
                  $rt->save();
              }
              $message = 'Pembayaran kas Anda untuk minggu ke-' . $kas->minggu_ke . ' tahun ' . $kas->tahun . ' telah dikonfirmasi lunas.';
              $notificationType = 'success';
          } else { // reject
              $kas->update([
                  'status' => 'ditolak',
                  'confirmed_by' => $user->id,
                  'confirmed_at' => Carbon::now(),
                  'confirmation_notes' => $request->catatan_konfirmasi ?? 'Pembayaran ditolak.',
                  // Optionally reset payment details if rejected
                  // 'tanggal_bayar' => null,
                  // 'jumlah_dibayar' => null,
                  // 'metode_bayar' => null,
                  // 'bukti_bayar_file' => null,
                  // 'bukti_bayar_notes' => null,
                  // 'bukti_bayar_uploaded_at' => null,
              ]);
              $message = 'Pembayaran kas Anda untuk minggu ke-' . $kas->minggu_ke . ' tahun ' . $kas->tahun . ' ditolak. Silakan periksa catatan konfirmasi.';
              $notificationType = 'error';
          }

          // Notify the masyarakat user
          if ($kas->penduduk->user) {
              Notifikasi::create([
                  'user_id' => $kas->penduduk->user->id,
                  'judul' => 'Status Pembayaran Kas',
                  'pesan' => $message,
                  'link' => route('kas.show', $kas->id),
                  'is_read' => false,
              ]);
          }

          DB::commit();
          return response()->json(['success' => true, 'message' => 'Konfirmasi pembayaran berhasil.']);
      } catch (\Exception $e) {
          DB::rollBack();
          Log::error('Error confirming payment: ' . $e->getMessage());
          return response()->json(['success' => false, 'message' => 'Gagal mengkonfirmasi pembayaran: ' . $e->getMessage()], 500);
      }
  }
}
