<?php

namespace App\Http\Controllers;

use App\Models\Rt;
use App\Models\Rw;
use App\Models\Penduduk;
use Illuminate\Http\Request;

class RtController extends Controller
{
    public function index()
    {
        $rts = Rt::with(['rw.desa', 'ketua'])->get();
        return view('rt.index', compact('rts'));
    }

    public function show(Rt $rt)
    {
        $rt->load(['rw.desa', 'ketua']);
        return view('rt.show', compact('rt'));
    }

    public function create()
    {
        $rws = Rw::with('desa')->where('status', 'aktif')->get();
        $penduduks = $this->getAvailablePenduduk();
        return view('rt.create', compact('rws', 'penduduks'));
    }

    public function store(Request $request)
    {
        try {
            // Validasi dasar
            $request->validate([
                'rw_id' => 'required|exists:rws,id',
                'no_rt' => 'required|string|regex:/^\d{2,}$/',
                'nama_rt' => 'required|string|max:255',
                'alamat' => 'nullable|string|max:500',
                'ketua_rt_id' => 'nullable|exists:penduduks,id',
                'no_telpon' => 'nullable|string|max:20',
                'jumlah_kk' => 'nullable|integer|min:0',
                'saldo' => 'nullable|numeric|min:0',
                'status' => 'required|in:aktif,tidak_aktif'
            ], [
                'rw_id.required' => 'RW harus dipilih.',
                'rw_id.exists' => 'RW yang dipilih tidak valid.',
                'no_rt.required' => 'Nomor RT harus diisi.',
                'no_rt.regex' => 'Nomor RT harus berupa angka minimal 2 digit.',
                'nama_rt.required' => 'Nama RT harus diisi.',
                'nama_rt.string' => 'Nama RT harus berupa teks.',
                'nama_rt.max' => 'Nama RT maksimal 255 karakter.',
                'alamat.string' => 'Alamat harus berupa teks.',
                'alamat.max' => 'Alamat maksimal 500 karakter.',
                'ketua_rt_id.exists' => 'Ketua RT yang dipilih tidak valid.',
                'no_telpon.string' => 'Nomor telepon harus berupa teks.',
                'no_telpon.max' => 'Nomor telepon maksimal 20 karakter.',
                'jumlah_kk.integer' => 'Jumlah KK harus berupa angka bulat.',
                'jumlah_kk.min' => 'Jumlah KK tidak boleh kurang dari 0.',
                'saldo.numeric' => 'Saldo harus berupa angka.',
                'saldo.min' => 'Saldo tidak boleh kurang dari 0.',
                'status.required' => 'Status harus dipilih.',
                'status.in' => 'Status harus aktif atau tidak aktif.',
            ]);

            // Validasi tambahan: cek apakah penduduk sudah menjabat
            if ($request->ketua_rt_id) {
                $sudahMenjabat = $this->checkPendudukSudahMenjabat($request->ketua_rt_id);
                if ($sudahMenjabat) {
                    return back()->withErrors(['ketua_rt_id' => 'Penduduk yang dipilih sudah menjabat sebagai ' . $sudahMenjabat])->withInput();
                }
            }

            // Validasi nomor RT unik dalam RW
            $existingRt = Rt::where('rw_id', $request->rw_id)
                           ->where('no_rt', $request->no_rt)
                           ->first();
            if ($existingRt) {
                return back()->withErrors(['no_rt' => 'Nomor RT sudah ada dalam RW ini.'])->withInput();
            }

            $rt = Rt::create($request->all());
            
            return redirect()->route('rt-rw.index')->with('success', 'RT berhasil ditambahkan');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating RT: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data RT. Silakan coba lagi.')->withInput();
        }
    }

    public function edit(Rt $rt)
    {
        $rws = Rw::with('desa')->where('status', 'aktif')->get();
        $penduduks = $this->getAvailablePenduduk($rt->ketua_rt_id);
        return view('rt.edit', compact('rt', 'rws', 'penduduks'));
    }

    public function update(Request $request, Rt $rt)
    {
        try {
            \Log::info('RT Update Request:', $request->all());
            
            // Validasi dasar
            $request->validate([
                'rw_id' => 'required|exists:rws,id',
                'no_rt' => 'required|string|regex:/^\d{2,}$/',
                'nama_rt' => 'required|string|max:255',
                'alamat' => 'nullable|string|max:500',
                'ketua_rt_id' => 'nullable|exists:penduduks,id',
                'no_telpon' => 'nullable|string|max:20',
                'jumlah_kk' => 'nullable|integer|min:0',
                'saldo' => 'nullable|numeric|min:0',
                'status' => 'required|in:aktif,tidak_aktif'
            ], [
                'rw_id.required' => 'RW harus dipilih.',
                'rw_id.exists' => 'RW yang dipilih tidak valid.',
                'no_rt.required' => 'Nomor RT harus diisi.',
                'no_rt.regex' => 'Nomor RT harus berupa angka minimal 2 digit.',
                'nama_rt.required' => 'Nama RT harus diisi.',
                'nama_rt.string' => 'Nama RT harus berupa teks.',
                'nama_rt.max' => 'Nama RT maksimal 255 karakter.',
                'alamat.string' => 'Alamat harus berupa teks.',
                'alamat.max' => 'Alamat maksimal 500 karakter.',
                'ketua_rt_id.exists' => 'Ketua RT yang dipilih tidak valid.',
                'no_telpon.string' => 'Nomor telepon harus berupa teks.',
                'no_telpon.max' => 'Nomor telepon maksimal 20 karakter.',
                'jumlah_kk.integer' => 'Jumlah KK harus berupa angka bulat.',
                'jumlah_kk.min' => 'Jumlah KK tidak boleh kurang dari 0.',
                'saldo.numeric' => 'Saldo harus berupa angka.',
                'saldo.min' => 'Saldo tidak boleh kurang dari 0.',
                'status.required' => 'Status harus dipilih.',
                'status.in' => 'Status harus aktif atau tidak aktif.',
            ]);

            // Validasi tambahan: cek apakah penduduk sudah menjabat (kecuali yang sedang diedit)
            if ($request->ketua_rt_id && $request->ketua_rt_id != $rt->ketua_rt_id) {
                $sudahMenjabat = $this->checkPendudukSudahMenjabat($request->ketua_rt_id);
                if ($sudahMenjabat) {
                    return back()->withErrors(['ketua_rt_id' => 'Penduduk yang dipilih sudah menjabat sebagai ' . $sudahMenjabat])->withInput();
                }
            }

            // Validasi nomor RT unik dalam RW (kecuali yang sedang diedit)
            $existingRt = Rt::where('rw_id', $request->rw_id)
                           ->where('no_rt', $request->no_rt)
                           ->where('id', '!=', $rt->id)
                           ->first();
            if ($existingRt) {
                return back()->withErrors(['no_rt' => 'Nomor RT sudah ada dalam RW ini.'])->withInput();
            }

            $rt->update($request->all());
            
            \Log::info('RT Updated successfully:', $rt->toArray());
            
            return redirect()->route('rt-rw.index')->with('success', 'RT berhasil diperbarui');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('RT Validation Error:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating RT: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data RT. Silakan coba lagi.')->withInput();
        }
    }

    public function destroy(Rt $rt)
    {
        try {
            $rt->delete();
            
            // Mengalihkan kembali ke halaman index dengan pesan sukses
            return redirect()->route('rt-rw.index')->with('success', 'RT berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Error deleting RT: ' . $e->getMessage());
            
            // Mengalihkan kembali dengan pesan error jika terjadi kesalahan
            return back()->with('error', 'Terjadi kesalahan saat menghapus data RT. Silakan coba lagi.');
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
