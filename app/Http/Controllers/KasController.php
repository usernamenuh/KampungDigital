<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use App\Models\PengaturanKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Notifikasi; // Add this line

class KasController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
      $user = Auth::user();
      $query = Kas::with(['penduduk.user', 'rt.rw', 'confirmedBy']);

      // Apply role-based filters
      switch ($user->role) {
          case 'masyarakat':
              if ($user->penduduk) {
                  $query->where('penduduk_id', $user->penduduk->id);
              } else {
                  return redirect()->back()->with('error', 'Data penduduk tidak ditemukan.');
              }
              break;
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

      // Apply filters from request
      if ($request->filled('status')) {
          $statuses = explode(',', $request->status);
          $query->whereIn('status', $statuses);
      }
      if ($request->filled('rt_id')) {
          $query->where('rt_id', $request->rt_id);
      }
      if ($request->filled('tahun')) {
          $query->where('tahun', $request->tahun);
      }
      if ($request->filled('minggu_ke')) {
          $query->where('minggu_ke', $request->minggu_ke);
      }
      if ($request->filled('nama')) {
          $search = $request->nama;
          $query->whereHas('penduduk', function($q) use ($search) {
              $q->where('nama_lengkap', 'like', '%' . $search . '%')
                ->orWhere('nik', 'like', '%' . $search . '%');
          });
      }
      if ($request->filled('email')) {
          $searchEmail = $request->email;
          $query->whereHas('penduduk.user', function($q) use ($searchEmail) {
              $q->where('email', 'like', '%' . $searchEmail . '%');
          });
      }

      $kas = $query->orderBy('tahun', 'desc')
                   ->orderBy('minggu_ke', 'desc')
                   ->paginate(10);

      $rtList = [];
      if (in_array($user->role, ['admin', 'kades'])) {
          $rtList = Rt::with('rw')->get();
      } elseif ($user->role === 'rw' && $user->penduduk && $user->penduduk->rwKetua) {
          $rtList = $user->penduduk->rwKetua->rts()->with('rw')->get();
      } elseif ($user->role === 'rt' && $user->penduduk && $user->penduduk->rtKetua) {
          $rtList = collect([$user->penduduk->rtKetua->load('rw')]);
      }

      // Calculate statistics for the cards
      $stats = $this->getKasStats($user);

      return view('kas.index', compact('kas', 'rtList', 'stats'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
      $user = Auth::user();
      $rtList = [];
      if (in_array($user->role, ['admin', 'kades'])) {
          $rtList = Rt::with('rw')->get();
      } elseif ($user->role === 'rw' && $user->penduduk && $user->penduduk->rwKetua) {
          $rtList = $user->penduduk->rwKetua->rts()->with('rw')->get();
      } elseif ($user->role === 'rt' && $user->penduduk && $user->penduduk->rtKetua) {
          $rtList = collect([$user->penduduk->rtKetua->load('rw')]);
      } else {
          return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk membuat kas.');
      }
      
      // Fetch PengaturanKas to pass to the view for default values
      $pengaturanKas = PengaturanKas::getDefault();
      
      return view('kas.create', compact('rtList', 'pengaturanKas'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'penduduk_id' => 'required|exists:penduduk,id',
          'rt_id' => 'required|exists:rts,id',
          'minggu_ke' => 'required|integer|min:1|max:52',
          'tahun' => 'required|integer|min:2000|max:' . (date('Y') + 1),
          'jumlah' => 'required|numeric|min:0',
          'tanggal_jatuh_tempo' => 'required|date',
          'status' => 'required|in:belum_bayar,lunas,menunggu_konfirmasi,terlambat',
          'tanggal_bayar' => 'nullable|date',
          'metode_bayar' => 'nullable|string|max:50',
          'bukti_bayar_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
          'bukti_bayar_notes' => 'nullable|string|max:500',
          'confirmed_by' => 'nullable|exists:users,id',
          'confirmed_at' => 'nullable|date',
          'confirmation_notes' => 'nullable|string|max:500',
      ]);

      if ($validator->fails()) {
          return redirect()->back()->withErrors($validator)->withInput();
      }

      DB::beginTransaction();
      try {
          $kas = Kas::create($validator->validated());

          if ($request->hasFile('bukti_bayar_file')) {
              $path = $request->file('bukti_bayar_file')->store('public/bukti_bayar');
              $kas->bukti_bayar_file = str_replace('public/', 'storage/', $path);
              $kas->bukti_bayar_uploaded_at = now();
              $kas->save();
          }

          // Create notification if status is 'menunggu_konfirmasi' or 'lunas'
          if ($kas->status === 'menunggu_konfirmasi') {
              $this->createPaymentNotification($kas, 'pending');
          } elseif ($kas->status === 'lunas') {
              $this->createPaymentNotification($kas, 'approved');
          }

          DB::commit();
          return redirect()->route('kas.index')->with('success', 'Data kas berhasil ditambahkan.');
      } catch (\Exception $e) {
          DB::rollback();
          Log::error('Error creating kas: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
          return redirect()->back()->with('error', 'Gagal menambahkan data kas: ' . $e->getMessage())->withInput();
      }
  }

  /**
   * Display the specified resource.
   */
  public function show(Kas $kas)
  {
      // Authorization check
      if (!$this->canAccessKas($kas)) {
          return redirect()->back()->with('error', 'Anda tidak memiliki akses ke data kas ini.');
      }
      return view('kas.show', compact('kas'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Kas $kas)
  {
      // Authorization check
      if (!$this->canAccessKas($kas)) {
          return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit data kas ini.');
      }

      $user = Auth::user();
      $rtList = [];
      if (in_array($user->role, ['admin', 'kades'])) {
          $rtList = Rt::with('rw')->get();
      } elseif ($user->role === 'rw' && $user->penduduk && $user->penduduk->rwKetua) {
          $rtList = $user->penduduk->rwKetua->rts()->with('rw')->get();
      } elseif ($user->role === 'rt' && $user->penduduk && $user->penduduk->rtKetua) {
          $rtList = collect([$user->penduduk->rtKetua->load('rw')]);
      } else {
          return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit kas.');
      }
      return view('kas.edit', compact('kas', 'rtList'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Kas $kas)
  {
      // Authorization check
      if (!$this->canAccessKas($kas)) {
          return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk memperbarui data kas ini.');
      }

      $validator = Validator::make($request->all(), [
          'penduduk_id' => 'required|exists:penduduk,id',
          'rt_id' => 'required|exists:rts,id',
          'minggu_ke' => 'required|integer|min:1|max:52',
          'tahun' => 'required|integer|min:2000|max:' . (date('Y') + 1),
          'jumlah' => 'required|numeric|min:0',
          'tanggal_jatuh_tempo' => 'required|date',
          'status' => 'required|in:belum_bayar,lunas,menunggu_konfirmasi,terlambat',
          'tanggal_bayar' => 'nullable|date',
          'metode_bayar' => 'nullable|string|max:50',
          'bukti_bayar_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
          'bukti_bayar_notes' => 'nullable|string|max:500',
          'confirmed_by' => 'nullable|exists:users,id',
          'confirmed_at' => 'nullable|date',
          'confirmation_notes' => 'nullable|string|max:500',
      ]);

      if ($validator->fails()) {
          return redirect()->back()->withErrors($validator)->withInput();
      }

      DB::beginTransaction();
      try {
          $oldStatus = $kas->status;
          $kas->fill($validator->validated());

          if ($request->hasFile('bukti_bayar_file')) {
              // Delete old file if exists
              if ($kas->bukti_bayar_file) {
                  $oldPath = str_replace('storage/', 'public/', $kas->bukti_bayar_file);
                  if (file_exists(storage_path('app/' . $oldPath))) {
                      unlink(storage_path('app/' . $oldPath));
                  }
              }
              $path = $request->file('bukti_bayar_file')->store('public/bukti_bayar');
              $kas->bukti_bayar_file = str_replace('public/', 'storage/', $path);
              $kas->bukti_bayar_uploaded_at = now();
          }

          $kas->save();

          // Create notification if status changes to 'menunggu_konfirmasi' or 'lunas'
          if ($oldStatus !== 'menunggu_konfirmasi' && $kas->status === 'menunggu_konfirmasi') {
              $this->createPaymentNotification($kas, 'pending');
          } elseif ($oldStatus !== 'lunas' && $kas->status === 'lunas') {
              $this->createPaymentNotification($kas, 'approved');
          }

          DB::commit();
          return redirect()->route('kas.index')->with('success', 'Data kas berhasil diperbarui.');
      } catch (\Exception $e) {
          DB::rollback();
          Log::error('Error updating kas: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
          return redirect()->back()->with('error', 'Gagal memperbarui data kas: ' . $e->getMessage())->withInput();
      }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Kas $kas)
  {
      // Authorization check
      if (!$this->canAccessKas($kas)) {
          return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data kas ini.');
      }

      DB::beginTransaction();
      try {
          if ($kas->bukti_bayar_file) {
              $path = str_replace('storage/', 'public/', $kas->bukti_bayar_file);
              if (file_exists(storage_path('app/' . $path))) {
                  unlink(storage_path('app/' . $path));
              }
          }
          $kas->delete();
          DB::commit();
          return redirect()->route('kas.index')->with('success', 'Data kas berhasil dihapus.');
      } catch (\Exception $e) {
          DB::rollback();
          Log::error('Error deleting kas: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
          return redirect()->back()->with('error', 'Gagal menghapus data kas: ' . $e->getMessage());
      }
  }

  /**
   * Get resident info for kas creation (AJAX)
   */
  public function getResidentInfo(Request $request)
  {
      $user = Auth::user();
      $query = Penduduk::query();

      // Filter by role
      if ($user->role === 'rt' && $user->penduduk && $user->penduduk->rtKetua) {
          $query->where('rt_id', $user->penduduk->rtKetua->id);
      } elseif ($user->role === 'rw' && $user->penduduk && $user->penduduk->rwKetua) {
          $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
          $query->whereIn('rt_id', $rtIds);
      } elseif (!in_array($user->role, ['admin', 'kades'])) {
          return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
      }

      if ($request->filled('search')) {
          $search = $request->search;
          $query->where(function($q) use ($search) {
              $q->where('nama_lengkap', 'like', '%' . $search . '%')
                ->orWhere('nik', 'like', '%' . $search . '%');
          });
      }

      $penduduk = $query->limit(10)->get()->map(function($p) {
          return [
              'id' => $p->id,
              'text' => $p->nama_lengkap . ' (NIK: ' . $p->nik . ') - RT ' . ($p->rt->no_rt ?? 'N/A') . ' RW ' . ($p->rt->rw->no_rw ?? 'N/A'),
              'rt_id' => $p->rt_id,
          ];
      });

      return response()->json(['success' => true, 'data' => $penduduk]);
  }

  /**
   * Generate weekly kas for all residents in a specific RT.
   */
  public function generateWeekly(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'rt_id' => 'required|exists:rts,id',
          'minggu_mulai' => 'required|integer|min:1|max:52',
          'minggu_selesai' => 'required|integer|min:1|max:52|gte:minggu_mulai',
          'tahun' => 'required|integer|min:2000|max:' . (date('Y') + 1),
          'jumlah' => 'required|numeric|min:0',
      ]);

      if ($validator->fails()) {
          return redirect()->back()->withErrors($validator)->withInput();
      }

      $rt = Rt::find($request->rt_id);
      if (!$rt) {
          return redirect()->back()->with('error', 'RT tidak ditemukan.')->withInput();
      }

      // Authorization check
      $user = Auth::user();
      if ($user->role === 'rt' && $user->penduduk && $user->penduduk->rtKetua && $user->penduduk->rtKetua->id !== $rt->id) {
          return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk RT ini.')->withInput();
      } elseif ($user->role === 'rw' && $user->penduduk && $user->penduduk->rwKetua && !$user->penduduk->rwKetua->rts->contains('id', $rt->id)) {
          return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk RT ini.')->withInput();
      } elseif (!in_array($user->role, ['admin', 'kades', 'rt', 'rw'])) {
          return redirect()->back()->with('error', 'Akses ditolak.')->withInput();
      }

      $iuranMingguan = $request->jumlah; // Use provided amount, not from PengaturanKas

      if ($iuranMingguan <= 0) {
          return redirect()->back()->with('error', 'Jumlah iuran mingguan harus lebih dari nol.')->withInput();
      }

      $penduduks = Penduduk::whereHas('kk', function($query) use ($rt) {
          $query->where('rt_id', $rt->id);
      })->get();

      if ($penduduks->isEmpty()) {
          return redirect()->back()->with('warning', 'Tidak ada penduduk di RT ini untuk dibuatkan kas.')->withInput();
      }

      $createdCount = 0;
      $totalAmountGenerated = 0;
      DB::beginTransaction();
      try {
          for ($minggu = $request->minggu_mulai; $minggu <= $request->minggu_selesai; $minggu++) {
              // Calculate tanggal_jatuh_tempo for each week
              $tanggalJatuhTempo = Carbon::now()->setISODate($request->tahun, $minggu)->endOfWeek(Carbon::SUNDAY);

              foreach ($penduduks as $penduduk) {
                  // Check if kas for this week and year already exists for this resident
                  $existingKas = Kas::where('penduduk_id', $penduduk->id)
                                    ->where('minggu_ke', $minggu)
                                    ->where('tahun', $request->tahun)
                                    ->first();

                  if (!$existingKas) {
                      Kas::create([
                          'penduduk_id' => $penduduk->id,
                          'rt_id' => $rt->id,
                          'minggu_ke' => $minggu,
                          'tahun' => $request->tahun,
                          'jumlah' => $iuranMingguan,
                          'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                          'status' => 'belum_bayar',
                      ]);
                      $createdCount++;
                      $totalAmountGenerated += $iuranMingguan;
                  }
              }
          }
          DB::commit();
          return redirect()->back()->with('success', "Berhasil membuat $createdCount tagihan kas mingguan untuk RT {$rt->no_rt}.")
                           ->with('show_success_modal', true)
                           ->with('kas_created', $createdCount)
                           ->with('total_amount', $totalAmountGenerated);
      } catch (\Exception $e) {
          DB::rollback();
          Log::error('Error generating weekly kas: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
          return redirect()->back()->with('error', 'Gagal membuat tagihan kas mingguan: ' . $e->getMessage())->withInput();
      }
  }

  /**
   * Mark kas as paid by RT/RW/Admin.
   */
  public function bayar(Request $request, Kas $kas)
  {
      // Authorization check
      if (!$this->canAccessKas($kas)) {
          return back()->with('error', 'Anda tidak memiliki akses untuk kas ini.');
      }

      if (!in_array(Auth::user()->role, ['rt', 'rw', 'kades', 'admin'])) {
          return back()->with('error', 'Akses ditolak.');
      }

      if ($kas->status === 'lunas') {
          return back()->with('error', 'Kas ini sudah lunas.');
      }

      $validator = Validator::make($request->all(), [
          'metode_bayar' => 'required|string|max:50',
          'tanggal_bayar' => 'nullable|date',
          'bukti_bayar_notes' => 'nullable|string|max:500',
      ]);

      if ($validator->fails()) {
          return back()->withErrors($validator)->withInput();
      }

      DB::beginTransaction();
      try {
          $kas->update([
              'status' => 'lunas',
              'tanggal_bayar' => $request->tanggal_bayar ?? now(),
              'metode_bayar' => $request->metode_bayar,
              'bukti_bayar_notes' => $request->bukti_bayar_notes,
              'confirmed_by' => Auth::id(),
              'confirmed_at' => now(),
          ]);

          // Update RT saldo
          $rt = Rt::find($kas->rt_id);
          if ($rt) {
              $rt->saldo += $kas->jumlah;
              $rt->save();
          }

          // Create notification for masyarakat
          $this->createPaymentNotification($kas, 'approved');

          DB::commit();
          return back()->with('success', 'Kas berhasil ditandai lunas.'); // Changed to redirect back
      } catch (\Exception $e) {
          DB::rollback();
          Log::error('Error marking kas as paid: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
          return back()->with('error', 'Gagal menandai kas lunas: ' . $e->getMessage());
      }
  }

  /**
   * Bulk create kas records.
   */
  public function bulkCreate(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'kas_data' => 'required|array',
          'kas_data.*.penduduk_id' => 'required|exists:penduduk,id',
          'kas_data.*.rt_id' => 'required|exists:rts,id',
          'kas_data.*.minggu_ke' => 'required|integer|min:1|max:52',
          'kas_data.*.tahun' => 'required|integer|min:2000|max:' . (date('Y') + 1),
          'kas_data.*.jumlah' => 'required|numeric|min:0',
          'kas_data.*.tanggal_jatuh_tempo' => 'required|date',
          'kas_data.*.status' => 'required|in:belum_bayar,lunas,menunggu_konfirmasi,terlambat',
      ]);

      if ($validator->fails()) {
          return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
      }

      DB::beginTransaction();
      try {
          $createdCount = 0;
          foreach ($request->kas_data as $data) {
              // Check if kas for this week and year already exists for this resident
              $existingKas = Kas::where('penduduk_id', $data['penduduk_id'])
                                ->where('minggu_ke', $data['minggu_ke'])
                                ->where('tahun', $data['tahun'])
                                ->first();

              if (!$existingKas) {
                  Kas::create($data);
                  $createdCount++;
              }
          }
          DB::commit();
          return response()->json(['success' => true, 'message' => "$createdCount data kas berhasil ditambahkan."]);
      } catch (\Exception $e) {
          DB::rollback();
          Log::error('Error bulk creating kas: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
          return response()->json(['success' => false, 'message' => 'Gagal menambahkan data kas secara massal: ' . $e->getMessage()], 500);
      }
  }

  /**
   * Bulk update kas records.
   */
  public function bulkUpdate(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'kas_data' => 'required|array',
          'kas_data.*.id' => 'required|exists:kas,id',
          'kas_data.*.status' => 'required|in:belum_bayar,lunas,menunggu_konfirmasi,terlambat',
          'kas_data.*.tanggal_bayar' => 'nullable|date',
          'kas_data.*.metode_bayar' => 'nullable|string|max:50',
          'kas_data.*.confirmed_by' => 'nullable|exists:users,id',
          'kas_data.*.confirmed_at' => 'nullable|date',
          'kas_data.*.confirmation_notes' => 'nullable|string|max:500',
      ]);

      if ($validator->fails()) {
          return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
      }

      DB::beginTransaction();
      try {
          $updatedCount = 0;
          foreach ($request->kas_data as $data) {
              $kas = Kas::find($data['id']);
              if ($kas && $this->canAccessKas($kas)) {
                  $oldStatus = $kas->status;
                  $kas->fill($data);
                  $kas->save();

                  if ($oldStatus !== 'menunggu_konfirmasi' && $kas->status === 'menunggu_konfirmasi') {
                      $this->createPaymentNotification($kas, 'pending');
                  } elseif ($oldStatus !== 'lunas' && $kas->status === 'lunas') {
                      $this->createPaymentNotification($kas, 'approved');
                  }
                  $updatedCount++;
              }
          }
          DB::commit();
          return response()->json(['success' => true, 'message' => "$updatedCount data kas berhasil diperbarui."]);
      } catch (\Exception $e) {
          DB::rollback();
          Log::error('Error bulk updating kas: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
          return response()->json(['success' => false, 'message' => 'Gagal memperbarui data kas secara massal: ' . $e->getMessage()], 500);
      }
  }

  /**
   * Bulk delete kas records.
   */
  public function bulkDelete(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'kas_ids' => 'required|array',
          'kas_ids.*' => 'required|exists:kas,id',
      ]);

      if ($validator->fails()) {
          return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
      }

      DB::beginTransaction();
      try {
          $deletedCount = 0;
          foreach ($request->kas_ids as $kasId) {
              $kas = Kas::find($kasId);
              if ($kas && $this->canAccessKas($kas)) {
                  if ($kas->bukti_bayar_file) {
                      $path = str_replace('storage/', 'public/', $kas->bukti_bayar_file);
                      if (file_exists(storage_path('app/' . $path))) {
                          unlink(storage_path('app/' . $path));
                      }
                  }
                  $kas->delete();
                  $deletedCount++;
              }
          }
          DB::commit();
          return response()->json(['success' => true, 'message' => "$deletedCount data kas berhasil dihapus."]);
      } catch (\Exception $e) {
          DB::rollback();
          Log::error('Error bulk deleting kas: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
          return response()->json(['success' => false, 'message' => 'Gagal menghapus data kas secara massal: ' . $e->getMessage()], 500);
      }
  }

  /**
   * Helper function to check if user can access a specific kas record.
   */
  private function canAccessKas(Kas $kas)
  {
      $user = Auth::user();
      switch ($user->role) {
          case 'masyarakat':
              return $kas->penduduk_id === $user->penduduk->id;
          case 'rt':
              return $kas->rt_id === $user->penduduk->rtKetua->id;
          case 'rw':
              return $user->penduduk->rwKetua->rts->contains('id', $kas->rt_id);
          case 'admin':
          case 'kades':
              return true;
          default:
              return false;
      }
  }

  /**
   * Helper function to create payment notification
   */
  private function createPaymentNotification(Kas $payment, $status)
  {
      Notifikasi::create([
          'user_id' => $payment->penduduk->user->id ?? null,
          'judul' => 'Pembayaran Kas ' . ($status === 'approved' ? 'Disetujui' : ($status === 'pending' ? 'Menunggu Konfirmasi' : 'Ditolak')),
          'pesan' => 'Pembayaran kas minggu ke-' . $payment->minggu_ke . ' telah ' . ($status === 'approved' ? 'disetujui' : ($status === 'pending' ? 'menunggu konfirmasi' : 'ditolak')),
          'tipe' => $status === 'approved' ? 'success' : ($status === 'pending' ? 'warning' : 'error'),
          'kategori' => 'pembayaran',
          'data' => json_encode([
              'kas_id' => $payment->id,
              'minggu_ke' => $payment->minggu_ke,
              'jumlah' => $payment->jumlah,
          ]),
          'dibaca' => false,
      ]);
  }

  /**
   * Helper function to get kas statistics based on user role.
   */
  private function getKasStats($user)
  {
      $query = Kas::query();

      switch ($user->role) {
          case 'masyarakat':
              if ($user->penduduk) {
                  $query->where('penduduk_id', $user->penduduk->id);
              } else {
                  return $this->getDefaultKasStats();
              }
              break;
          case 'rt':
              if ($user->penduduk && $user->penduduk->rtKetua) {
                  $query->where('rt_id', $user->penduduk->rtKetua->id);
              } else {
                  return $this->getDefaultKasStats();
              }
              break;
          case 'rw':
              if ($user->penduduk && $user->penduduk->rwKetua) {
                  $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                  $query->whereIn('rt_id', $rtIds);
              } else {
                  return $this->getDefaultKasStats();
              }
              break;
          // admin dan kades bisa lihat semua
      }

      $totalNominalTertagih = (clone $query)->sum('jumlah');
      $lunas = (clone $query)->where('status', 'lunas')->count();
      $belumBayar = (clone $query)->where('status', 'belum_bayar')->count();
      $menungguKonfirmasi = (clone $query)->where('status', 'menunggu_konfirmasi')->count();
      $terlambat = (clone $query)->where('status', 'belum_bayar')
                                 ->where('tanggal_jatuh_tempo', '<', Carbon::now())
                                 ->count();
      $totalTerkumpul = (clone $query)->where('status', 'lunas')->sum('jumlah');
      $totalOutstanding = (clone $query)->whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->sum('jumlah');

      return [
          'total_nominal_tertagih' => $totalNominalTertagih,
          'lunas' => $lunas,
          'belum_bayar' => $belumBayar,
          'menunggu_konfirmasi' => $menungguKonfirmasi,
          'terlambat' => $terlambat,
          'total_terkumpul' => $totalTerkumpul,
          'total_outstanding' => $totalOutstanding,
      ];
  }

  /**
   * Helper function to get default kas statistics.
   */
  private function getDefaultKasStats()
  {
      return [
          'total_nominal_tertagih' => 0,
          'lunas' => 0,
          'belum_bayar' => 0,
          'menunggu_konfirmasi' => 0,
          'terlambat' => 0,
          'total_terkumpul' => 0,
          'total_outstanding' => 0,
      ];
  }
}
