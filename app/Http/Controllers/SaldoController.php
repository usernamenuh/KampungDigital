<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Desa;
use App\Models\Kas;
use App\Models\SaldoTransaction;

class SaldoController extends Controller
{
    /**
     * Transfer collected kas to RT saldo.
     */
    public function transferKasToSaldo(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        if ($user->role !== 'rt') {
            return response()->json(['success' => false, 'message' => 'Akses tidak diizinkan.'], 403);
        }

        try {
            DB::beginTransaction();

            $rt = $user->penduduk->rtKetua ?? Rt::where('ketua_rt_id', $user->penduduk->id)->first();

            if (!$rt) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Data RT tidak ditemukan untuk pengguna ini.'], 404);
            }

            $amount = $request->amount;

            // Calculate kas terkumpul (total collected kas including denda)
            $kasTerkumpul = Kas::where('rt_id', $rt->id)
                ->where('status', 'lunas')
                ->sum(DB::raw('jumlah + COALESCE(denda, 0)')) ?? 0;

            // Calculate already transferred amount
            $alreadyTransferred = SaldoTransaction::where('rt_id', $rt->id)
                ->where('transaction_type', 'kas_transfer')
                ->sum('amount') ?? 0;
            
            $kasAvailableForTransfer = $kasTerkumpul - $alreadyTransferred;

            if ($amount > $kasAvailableForTransfer) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Jumlah transfer melebihi kas yang tersedia untuk ditransfer.'], 400);
            }

            $previousSaldo = $rt->saldo;
            $newSaldo = $previousSaldo + $amount;

            // Update RT saldo
            $rt->saldo = $newSaldo;
            $rt->save();

            // Record transaction
            SaldoTransaction::create([
                'rt_id' => $rt->id,
                'transaction_type' => 'kas_transfer',
                'amount' => $amount,
                'previous_saldo' => $previousSaldo,
                'new_saldo' => $newSaldo,
                'description' => $request->description ?? 'Transfer kas terkumpul ke saldo RT',
                'processed_by' => $user->id,
                'processed_at' => now(),
                'metadata' => [
                    'source' => 'kas_collection',
                    'rt_no' => $rt->no_rt,
                ],
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Kas berhasil ditransfer ke saldo RT.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error transferring kas to RT saldo', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat transfer kas: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Add income to RT saldo.
     */
    public function addIncome(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        if ($user->role !== 'rt') {
            return response()->json(['success' => false, 'message' => 'Akses tidak diizinkan.'], 403);
        }

        try {
            DB::beginTransaction();

            $rt = $user->penduduk->rtKetua ?? Rt::where('ketua_rt_id', $user->penduduk->id)->first();

            if (!$rt) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Data RT tidak ditemukan untuk pengguna ini.'], 404);
            }

            $amount = $request->amount;
            $previousSaldo = $rt->saldo;
            $newSaldo = $previousSaldo + $amount;

            // Update RT saldo
            $rt->saldo = $newSaldo;
            $rt->save();

            // Record transaction
            SaldoTransaction::create([
                'rt_id' => $rt->id,
                'transaction_type' => 'income',
                'amount' => $amount,
                'previous_saldo' => $previousSaldo,
                'new_saldo' => $newSaldo,
                'description' => $request->description,
                'processed_by' => $user->id,
                'processed_at' => now(),
                'metadata' => [
                    'rt_no' => $rt->no_rt,
                ],
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pemasukan berhasil ditambahkan ke saldo RT.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding income to RT saldo', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambah pemasukan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Add expense from RT saldo.
     */
    public function addExpense(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        if ($user->role !== 'rt') {
            return response()->json(['success' => false, 'message' => 'Akses tidak diizinkan.'], 403);
        }

        try {
            DB::beginTransaction();

            $rt = $user->penduduk->rtKetua ?? Rt::where('ketua_rt_id', $user->penduduk->id)->first();

            if (!$rt) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Data RT tidak ditemukan untuk pengguna ini.'], 404);
            }

            $amount = $request->amount;
            $previousSaldo = $rt->saldo;

            if ($amount > $previousSaldo) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Jumlah pengeluaran melebihi saldo RT yang tersedia.'], 400);
            }

            $newSaldo = $previousSaldo - $amount;

            // Update RT saldo
            $rt->saldo = $newSaldo;
            $rt->save();

            // Record transaction
            SaldoTransaction::create([
                'rt_id' => $rt->id,
                'transaction_type' => 'expense',
                'amount' => -$amount, // Store as negative for expense
                'previous_saldo' => $previousSaldo,
                'new_saldo' => $newSaldo,
                'description' => $request->description,
                'processed_by' => $user->id,
                'processed_at' => now(),
                'metadata' => [
                    'rt_no' => $rt->no_rt,
                ],
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pengeluaran berhasil dicatat dari saldo RT.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding expense from RT saldo', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mencatat pengeluaran: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get saldo history for the authenticated user's RT.
     */
    public function getSaldoHistory(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'rt') {
            return response()->json(['success' => false, 'message' => 'Akses tidak diizinkan.'], 403);
        }

        try {
            $rt = $user->penduduk->rtKetua ?? Rt::where('ketua_rt_id', $user->penduduk->id)->first();

            if (!$rt) {
                return response()->json(['success' => false, 'message' => 'Data RT tidak ditemukan untuk pengguna ini.'], 404);
            }

            $transactions = SaldoTransaction::where('rt_id', $rt->id)
                                            ->orderBy('created_at', 'desc')
                                            ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'transactions' => $transactions,
                ],
                'message' => 'Riwayat saldo berhasil dimuat.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting RT saldo history', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal memuat riwayat saldo: ' . $e->getMessage()], 500);
        }
    }
}
