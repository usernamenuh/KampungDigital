<?php

namespace App\Http\Controllers;

use App\Models\PaymentInfo;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentInfoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of payment infos
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        $query = PaymentInfo::with('rt.rw');

        // Filter berdasarkan role
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

        $paymentInfos = $query->paginate(10);

        return view('payment-info.index', compact('paymentInfos'));
    }

    /**
     * Show the form for creating a new payment info
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        $rts = collect();

        switch ($user->role) {
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    $rts = collect([$user->penduduk->rtKetua]);
                }
                break;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    $rts = $user->penduduk->rwKetua->rts;
                }
                break;
            case 'kades':
            case 'admin':
                $rts = Rt::with('rw')->get();
                break;
        }

        return view('payment-info.form', compact('rts'));
    }

    /**
     * Store a newly created payment info
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        $request->validate([
            'rt_id' => 'required|exists:rts,id',
            'bank_transfer_bank_name' => 'nullable|string|max:100',
            'bank_transfer_account_number' => 'nullable|string|max:50',
            'bank_transfer_account_name' => 'nullable|string|max:100',
            'e_wallet_dana' => 'nullable|string|max:20',
            'e_wallet_ovo' => 'nullable|string|max:20',
            'e_wallet_gopay' => 'nullable|string|max:20',
            'qr_code_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'qr_code_description' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Validasi akses RT
        $rt = Rt::findOrFail($request->rt_id);
        if (!$this->canAccessRt($rt)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk RT ini.');
        }

        // Cek apakah sudah ada payment info untuk RT ini
        $existing = PaymentInfo::where('rt_id', $request->rt_id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Informasi pembayaran untuk RT ini sudah ada. Silakan edit yang sudah ada.');
        }

        $data = [
            'rt_id' => $request->rt_id,
            'is_active' => $request->boolean('is_active', true),
            'payment_notes' => $request->payment_notes,
        ];

        // Bank Transfer
        if ($request->filled('bank_transfer_account_number')) {
            $data['bank_transfer'] = [
                'bank_name' => $request->bank_transfer_bank_name,
                'account_number' => $request->bank_transfer_account_number,
                'account_name' => $request->bank_transfer_account_name,
            ];
        }

        // E-Wallet
        $eWallet = [];
        if ($request->filled('e_wallet_dana')) $eWallet['dana'] = $request->e_wallet_dana;
        if ($request->filled('e_wallet_ovo')) $eWallet['ovo'] = $request->e_wallet_ovo;
        if ($request->filled('e_wallet_gopay')) $eWallet['gopay'] = $request->e_wallet_gopay;
        
        if (!empty($eWallet)) {
            $data['e_wallet'] = $eWallet;
        }

        // QR Code
        $qrCode = [];
        if ($request->hasFile('qr_code_image')) {
            $file = $request->file('qr_code_image');
            $path = $file->store('payment-qr-codes', 'public');
            $qrCode['image_url'] = Storage::url($path);
        }
        if ($request->filled('qr_code_description')) {
            $qrCode['description'] = $request->qr_code_description;
        }
        
        if (!empty($qrCode)) {
            $data['qr_code'] = $qrCode;
        }

        PaymentInfo::create($data);

        return redirect()->route('payment-info.index')->with('success', 'Informasi pembayaran berhasil dibuat.');
    }

    /**
     * Show the form for editing payment info
     */
    public function edit(PaymentInfo $paymentInfo)
    {
        if (!$this->canAccessRt($paymentInfo->rt)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit informasi pembayaran ini.');
        }

        $user = Auth::user();
        $rts = collect();

        switch ($user->role) {
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    $rts = collect([$user->penduduk->rtKetua]);
                }
                break;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    $rts = $user->penduduk->rwKetua->rts;
                }
                break;
            case 'kades':
            case 'admin':
                $rts = Rt::with('rw')->get();
                break;
        }

        return view('payment-info.form', compact('paymentInfo', 'rts'));
    }

    /**
     * Update the specified payment info
     */
    public function update(Request $request, PaymentInfo $paymentInfo)
    {
        if (!$this->canAccessRt($paymentInfo->rt)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit informasi pembayaran ini.');
        }

        $request->validate([
            'rt_id' => 'required|exists:rts,id',
            'bank_transfer_bank_name' => 'nullable|string|max:100',
            'bank_transfer_account_number' => 'nullable|string|max:50',
            'bank_transfer_account_name' => 'nullable|string|max:100',
            'e_wallet_dana' => 'nullable|string|max:20',
            'e_wallet_ovo' => 'nullable|string|max:20',
            'e_wallet_gopay' => 'nullable|string|max:20',
            'qr_code_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'qr_code_description' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $data = [
            'rt_id' => $request->rt_id,
            'is_active' => $request->boolean('is_active', true),
            'payment_notes' => $request->payment_notes,
        ];

        // Bank Transfer
        if ($request->filled('bank_transfer_account_number')) {
            $data['bank_transfer'] = [
                'bank_name' => $request->bank_transfer_bank_name,
                'account_number' => $request->bank_transfer_account_number,
                'account_name' => $request->bank_transfer_account_name,
            ];
        } else {
            $data['bank_transfer'] = null;
        }

        // E-Wallet
        $eWallet = [];
        if ($request->filled('e_wallet_dana')) $eWallet['dana'] = $request->e_wallet_dana;
        if ($request->filled('e_wallet_ovo')) $eWallet['ovo'] = $request->e_wallet_ovo;
        if ($request->filled('e_wallet_gopay')) $eWallet['gopay'] = $request->e_wallet_gopay;
        
        $data['e_wallet'] = !empty($eWallet) ? $eWallet : null;

        // QR Code
        $qrCode = $paymentInfo->qr_code ?? [];
        
        if ($request->hasFile('qr_code_image')) {
            // Hapus file lama jika ada
            if (isset($qrCode['image_url'])) {
                $oldPath = str_replace('/storage/', '', $qrCode['image_url']);
                Storage::disk('public')->delete($oldPath);
            }
            
            $file = $request->file('qr_code_image');
            $path = $file->store('payment-qr-codes', 'public');
            $qrCode['image_url'] = Storage::url($path);
        }
        
        if ($request->filled('qr_code_description')) {
            $qrCode['description'] = $request->qr_code_description;
        } elseif ($request->has('qr_code_description')) {
            unset($qrCode['description']);
        }
        
        $data['qr_code'] = !empty($qrCode) ? $qrCode : null;

        $paymentInfo->update($data);

        return redirect()->route('payment-info.index')->with('success', 'Informasi pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified payment info
     */
    public function destroy(PaymentInfo $paymentInfo)
    {
        if (!$this->canAccessRt($paymentInfo->rt)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus informasi pembayaran ini.');
        }

        // Hapus file QR code jika ada
        if ($paymentInfo->qr_code && isset($paymentInfo->qr_code['image_url'])) {
            $path = str_replace('/storage/', '', $paymentInfo->qr_code['image_url']);
            Storage::disk('public')->delete($path);
        }

        $paymentInfo->delete();

        return redirect()->route('payment-info.index')->with('success', 'Informasi pembayaran berhasil dihapus.');
    }

    /**
     * Check if user can access specific RT
     */
    private function canAccessRt(Rt $rt)
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
            case 'kades':
                return true;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    return $rt->rw_id === $user->penduduk->rwKetua->id;
                }
                return false;
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    return $rt->id === $user->penduduk->rtKetua->id;
                }
                return false;
            default:
                return false;
        }
    }
}
