<?php

namespace App\Http\Controllers;

use App\Models\Rw;
use App\Models\Desa;
use App\Models\Penduduk;
use Illuminate\Http\Request;

class RwController extends Controller
{
    public function index()
    {
        $rws = Rw::with(['desa', 'rts', 'ketua'])->get();
        return view('rw.index', compact('rws'));
    }

    public function show(Rw $rw)
    {
        $rw->load(['desa', 'rts', 'ketua']);
        return view('rw.show', compact('rw'));
    }

    public function create()
    {
        $desas = Desa::where('status', 'aktif')->get();
        $penduduks = $this->getAvailablePenduduk();
        return view('rw.create', compact('desas', 'penduduks'));
    }

    public function store(Request $request)
    {
        try {
            // Validasi dasar
            $request->validate([
                'desa_id' => 'required|exists:desas,id',
                'no_rw' => 'required|string|regex:/^\d{2,}$/',
                'nama_rw' => 'required|string|max:255',
                'alamat' => 'required|string|max:500',
                'ketua_rw_id' => 'nullable|exists:penduduks,id',
                'no_telpon' => 'nullable|string|max:20',
                'saldo' => 'nullable|numeric|min:0',
                'status' => 'required|in:aktif,tidak_aktif'
            ], [
                'desa_id.required' => 'Desa harus dipilih.',
                'desa_id.exists' => 'Desa yang dipilih tidak valid.',
                'no_rw.required' => 'Nomor RW harus diisi.',
                'no_rw.regex' => 'Nomor RW harus berupa angka minimal 2 digit.',
                'nama_rw.required' => 'Nama RW harus diisi.',
                'nama_rw.string' => 'Nama RW harus berupa teks.',
                'nama_rw.max' => 'Nama RW maksimal 255 karakter.',
                'alamat.required' => 'Alamat harus diisi.',
                'alamat.string' => 'Alamat harus berupa teks.',
                'alamat.max' => 'Alamat maksimal 500 karakter.',
                'ketua_rw_id.exists' => 'Ketua RW yang dipilih tidak valid.',
                'no_telpon.string' => 'Nomor telepon harus berupa teks.',
                'no_telpon.max' => 'Nomor telepon maksimal 20 karakter.',
                'saldo.numeric' => 'Saldo harus berupa angka.',
                'saldo.min' => 'Saldo tidak boleh kurang dari 0.',
                'status.required' => 'Status harus dipilih.',
                'status.in' => 'Status harus aktif atau tidak aktif.',
            ]);

            // Validasi tambahan: cek apakah penduduk sudah menjabat
            if ($request->ketua_rw_id) {
                $sudahMenjabat = $this->checkPendudukSudahMenjabat($request->ketua_rw_id);
                if ($sudahMenjabat) {
                    return back()->withErrors(['ketua_rw_id' => 'Penduduk yang dipilih sudah menjabat sebagai ' . $sudahMenjabat])->withInput();
                }
            }

            // Validasi nomor RW unik dalam Desa
            $existingRw = Rw::where('desa_id', $request->desa_id)
                           ->where('no_rw', $request->no_rw)
                           ->first();
            if ($existingRw) {
                return back()->withErrors(['no_rw' => 'Nomor RW sudah ada dalam desa ini.'])->withInput();
            }

            $rw = Rw::create($request->all());
            
            return redirect()->route('rt-rw.index')->with('success', 'RW berhasil ditambahkan');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating RW: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data RW. Silakan coba lagi.')->withInput();
        }
    }

    public function edit(Rw $rw)
    {
        $desas = Desa::where('status', 'aktif')->get();
        $penduduks = $this->getAvailablePenduduk($rw->ketua_rw_id);
        return view('rw.edit', compact('rw', 'desas', 'penduduks'));
    }

    public function update(Request $request, Rw $rw)
    {
        try {
            \Log::info('RW Update Request:', $request->all());
            
            // Validasi dasar
            $request->validate([
                'desa_id' => 'required|exists:desas,id',
                'no_rw' => 'required|string|regex:/^\d{2,}$/',
                'nama_rw' => 'required|string|max:255',
                'alamat' => 'required|string|max:500',
                'ketua_rw_id' => 'nullable|exists:penduduks,id',
                'no_telpon' => 'nullable|string|max:20',
                'saldo' => 'nullable|numeric|min:0',
                'status' => 'required|in:aktif,tidak_aktif'
            ], [
                'desa_id.required' => 'Desa harus dipilih.',
                'desa_id.exists' => 'Desa yang dipilih tidak valid.',
                'no_rw.required' => 'Nomor RW harus diisi.',
                'no_rw.regex' => 'Nomor RW harus berupa angka minimal 2 digit.',
                'nama_rw.required' => 'Nama RW harus diisi.',
                'nama_rw.string' => 'Nama RW harus berupa teks.',
                'nama_rw.max' => 'Nama RW maksimal 255 karakter.',
                'alamat.required' => 'Alamat harus diisi.',
                'alamat.string' => 'Alamat harus berupa teks.',
                'alamat.max' => 'Alamat maksimal 500 karakter.',
                'ketua_rw_id.exists' => 'Ketua RW yang dipilih tidak valid.',
                'no_telpon.string' => 'Nomor telepon harus berupa teks.',
                'no_telpon.max' => 'Nomor telepon maksimal 20 karakter.',
                'saldo.numeric' => 'Saldo harus berupa angka.',
                'saldo.min' => 'Saldo tidak boleh kurang dari 0.',
                'status.required' => 'Status harus dipilih.',
                'status.in' => 'Status harus aktif atau tidak aktif.',
            ]);

            // Validasi tambahan: cek apakah penduduk sudah menjabat (kecuali yang sedang diedit)
            if ($request->ketua_rw_id && $request->ketua_rw_id != $rw->ketua_rw_id) {
                $sudahMenjabat = $this->checkPendudukSudahMenjabat($request->ketua_rw_id);
                if ($sudahMenjabat) {
                    return back()->withErrors(['ketua_rw_id' => 'Penduduk yang dipilih sudah menjabat sebagai ' . $sudahMenjabat])->withInput();
                }
            }

            // Validasi nomor RW unik dalam Desa (kecuali yang sedang diedit)
            $existingRw = Rw::where('desa_id', $request->desa_id)
                           ->where('no_rw', $request->no_rw)
                           ->where('id', '!=', $rw->id)
                           ->first();
            if ($existingRw) {
                return back()->withErrors(['no_rw' => 'Nomor RW sudah ada dalam desa ini.'])->withInput();
            }

            $rw->update($request->all());
            
            \Log::info('RW Updated successfully:', $rw->toArray());
            
            return redirect()->route('rt-rw.index')->with('success', 'RW berhasil diperbarui');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('RW Validation Error:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating RW: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data RW. Silakan coba lagi.')->withInput();
        }
    }

    public function destroy(Rw $rw)
    {
        try {
            $rw->delete();
            
            // Mengalihkan kembali ke halaman index dengan pesan sukses
            return redirect()->route('rt-rw.index')->with('success', 'RW berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Error deleting RW: ' . $e->getMessage());
            
            // Mengalihkan kembali dengan pesan error jika terjadi kesalahan
            return back()->with('error', 'Terjadi kesalahan saat menghapus data RW. Silakan coba lagi.');
        }
    }

    private function getAvailablePenduduk($excludeId = null)
    {
        $pendudukSudahMenjabat = collect();
        $pendudukSudahMenjabat = $pendudukSudahMenjabat
            ->merge(\App\Models\Desa::whereNotNull('kepala_desa_id')->pluck('kepala_desa_id'))
            ->merge(\App\Models\Rw::whereNotNull('ketua_rw_id')->pluck('ketua_rw_id'))
            ->merge(\App\Models\Rt::whereNotNull('ketua_rt_id')->pluck('ketua_rt_id'))
            ->filter()->unique();

        // Kecuali jika ada ID yang dikecualikan (untuk edit)
        if ($excludeId) {
            $pendudukSudahMenjabat = $pendudukSudahMenjabat->filter(fn($id) => $id != $excludeId);
        }

        return Penduduk::whereNotIn('id', $pendudukSudahMenjabat)->get();
    }

    private function checkPendudukSudahMenjabat($pendudukId)
    {
        // Cek apakah sudah menjadi kepala desa
        $kepalaDesa = \App\Models\Desa::where('kepala_desa_id', $pendudukId)->first();
        if ($kepalaDesa) {
            return 'Kepala Desa di ' . $kepalaDesa->alamat;
        }

        // Cek apakah sudah menjadi ketua RW
        $ketuaRw = \App\Models\Rw::where('ketua_rw_id', $pendudukId)->first();
        if ($ketuaRw) {
            return 'Ketua RW ' . $ketuaRw->nama_rw;
        }

        // Cek apakah sudah menjadi ketua RT
        $ketuaRt = \App\Models\Rt::where('ketua_rt_id', $pendudukId)->first();
        if ($ketuaRt) {
            return 'Ketua RT ' . $ketuaRt->nama_rt;
        }

        return false;
    }
}
