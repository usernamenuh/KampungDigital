<?php

namespace App\Http\Controllers;

use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;

class RtController extends Controller
{
    public function index()
    {
        $rts = Rt::with(['rw.desa'])->get();
        return view('rt.index', compact('rts'));
    }

    public function show(Rt $rt)
    {
        $rt->load(['rw.desa']);
        return view('rt.show', compact('rt'));
    }

    public function create()
    {
        $rws = Rw::with('desa')->where('status', 'aktif')->get();
        return view('rt.create', compact('rws'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'rw_id' => 'required|exists:rws,id',
                'nama_rt' => 'required|string|max:255',
                'alamat' => 'nullable|string|max:500',
                'ketua_rt' => 'nullable|string|max:255',
                'no_telpon' => 'nullable|string|max:20',
                'jumlah_kk' => 'nullable|integer|min:0',
                'saldo' => 'nullable|numeric|min:0',
                'status' => 'required|in:aktif,tidak_aktif'
            ], [
                'rw_id.required' => 'RW harus dipilih.',
                'rw_id.exists' => 'RW yang dipilih tidak valid.',
                'nama_rt.required' => 'Nama RT harus diisi.',
                'nama_rt.string' => 'Nama RT harus berupa teks.',
                'nama_rt.max' => 'Nama RT maksimal 255 karakter.',
                'alamat.string' => 'Alamat harus berupa teks.',
                'alamat.max' => 'Alamat maksimal 500 karakter.',
                'ketua_rt.string' => 'Nama ketua RT harus berupa teks.',
                'ketua_rt.max' => 'Nama ketua RT maksimal 255 karakter.',
                'no_telpon.string' => 'Nomor telepon harus berupa teks.',
                'no_telpon.max' => 'Nomor telepon maksimal 20 karakter.',
                'jumlah_kk.integer' => 'Jumlah KK harus berupa angka bulat.',
                'jumlah_kk.min' => 'Jumlah KK tidak boleh kurang dari 0.',
                'saldo.numeric' => 'Saldo harus berupa angka.',
                'saldo.min' => 'Saldo tidak boleh kurang dari 0.',
                'status.required' => 'Status harus dipilih.',
                'status.in' => 'Status harus aktif atau tidak aktif.'
            ]);

            $rt = Rt::create($validated);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'RT berhasil ditambahkan',
                    'data' => $rt
                ]);
            }

            return redirect()->route('rt-rw.index')->with('success', 'RT berhasil ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang dimasukkan tidak valid',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating RT: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data RT. Silakan coba lagi.'
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data RT')->withInput();
        }
    }

    public function edit(Rt $rt)
    {
        $rws = Rw::with('desa')->where('status', 'aktif')->get();
        return view('rt.edit', compact('rt', 'rws'));
    }

    public function update(Request $request, Rt $rt)
    {
        try {
            $validated = $request->validate([
                'rw_id' => 'required|exists:rws,id',
                'nama_rt' => 'required|string|max:255',
                'alamat' => 'nullable|string|max:500',
                'ketua_rt' => 'nullable|string|max:255',
                'no_telpon' => 'nullable|string|max:20',
                'jumlah_kk' => 'nullable|integer|min:0',
                'saldo' => 'nullable|numeric|min:0',
                'status' => 'required|in:aktif,tidak_aktif'
            ], [
                'rw_id.required' => 'RW harus dipilih.',
                'rw_id.exists' => 'RW yang dipilih tidak valid.',
                'nama_rt.required' => 'Nama RT harus diisi.',
                'nama_rt.string' => 'Nama RT harus berupa teks.',
                'nama_rt.max' => 'Nama RT maksimal 255 karakter.',
                'alamat.string' => 'Alamat harus berupa teks.',
                'alamat.max' => 'Alamat maksimal 500 karakter.',
                'ketua_rt.string' => 'Nama ketua RT harus berupa teks.',
                'ketua_rt.max' => 'Nama ketua RT maksimal 255 karakter.',
                'no_telpon.string' => 'Nomor telepon harus berupa teks.',
                'no_telpon.max' => 'Nomor telepon maksimal 20 karakter.',
                'jumlah_kk.integer' => 'Jumlah KK harus berupa angka bulat.',
                'jumlah_kk.min' => 'Jumlah KK tidak boleh kurang dari 0.',
                'saldo.numeric' => 'Saldo harus berupa angka.',
                'saldo.min' => 'Saldo tidak boleh kurang dari 0.',
                'status.required' => 'Status harus dipilih.',
                'status.in' => 'Status harus aktif atau tidak aktif.'
            ]);

            $rt->update($validated);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'RT berhasil diperbarui',
                    'data' => $rt
                ]);
            }

            return redirect()->route('rt-rw.index')->with('success', 'RT berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang dimasukkan tidak valid',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating RT: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui data RT. Silakan coba lagi.'
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data RT')->withInput();
        }
    }

    public function destroy(Rt $rt)
    {
        try {
            $rt->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'RT berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting RT: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data RT'
            ], 500);
        }
    }
}
