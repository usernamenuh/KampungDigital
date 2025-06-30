<?php

namespace App\Http\Controllers;

use App\Models\Rw;
use App\Models\Desa;
use Illuminate\Http\Request;

class RwController extends Controller
{
    public function index()
    {
        $rws = Rw::with(['desa', 'rts'])->get();
        return view('rw.index', compact('rws'));
    }

    public function show(Rw $rw)
    {
        $rw->load(['desa', 'rts']);
        return view('rw.show', compact('rw'));
    }

    public function create()
    {
        $desas = Desa::where('status', 'aktif')->get();
        return view('rw.create', compact('desas'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'desa_id' => 'required|exists:desas,id',
                'nama_rw' => 'required|string|max:255',
                'alamat' => 'required|string|max:500',
                'no_telpon' => 'nullable|string|max:20',
                'saldo' => 'nullable|numeric|min:0',
                'status' => 'required|in:aktif,tidak_aktif',
                'ketua_rw' => 'nullable|string|max:255'
            ], [
                'desa_id.required' => 'Desa harus dipilih.',
                'desa_id.exists' => 'Desa yang dipilih tidak valid.',
                'nama_rw.required' => 'Nama RW harus diisi.',
                'nama_rw.string' => 'Nama RW harus berupa teks.',
                'nama_rw.max' => 'Nama RW maksimal 255 karakter.',
                'alamat.required' => 'Alamat harus diisi.',
                'alamat.string' => 'Alamat harus berupa teks.',
                'alamat.max' => 'Alamat maksimal 500 karakter.',
                'no_telpon.string' => 'Nomor telepon harus berupa teks.',
                'no_telpon.max' => 'Nomor telepon maksimal 20 karakter.',
                'saldo.numeric' => 'Saldo harus berupa angka.',
                'saldo.min' => 'Saldo tidak boleh kurang dari 0.',
                'status.required' => 'Status harus dipilih.',
                'status.in' => 'Status harus aktif atau tidak aktif.',
                'ketua_rw.string' => 'Nama ketua RW harus berupa teks.',
                'ketua_rw.max' => 'Nama ketua RW maksimal 255 karakter.'
            ]);

            $rw = Rw::create($validated);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'RW berhasil ditambahkan',
                    'data' => $rw
                ]);
            }

            return redirect()->route('rt-rw.index')->with('success', 'RW berhasil ditambahkan');
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
            \Log::error('Error creating RW: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data RW. Silakan coba lagi.'
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data RW')->withInput();
        }
    }

    public function edit(Rw $rw)
    {
        $desas = Desa::where('status', 'aktif')->get();
        return view('rw.edit', compact('rw', 'desas'));
    }

    public function update(Request $request, Rw $rw)
    {
        try {
            $validated = $request->validate([
                'desa_id' => 'required|exists:desas,id',
                'nama_rw' => 'required|string|max:255',
                'alamat' => 'required|string|max:500',
                'no_telpon' => 'nullable|string|max:20',
                'saldo' => 'nullable|numeric|min:0',
                'status' => 'required|in:aktif,tidak_aktif',
                'ketua_rw' => 'nullable|string|max:255'
            ], [
                'desa_id.required' => 'Desa harus dipilih.',
                'desa_id.exists' => 'Desa yang dipilih tidak valid.',
                'nama_rw.required' => 'Nama RW harus diisi.',
                'nama_rw.string' => 'Nama RW harus berupa teks.',
                'nama_rw.max' => 'Nama RW maksimal 255 karakter.',
                'alamat.required' => 'Alamat harus diisi.',
                'alamat.string' => 'Alamat harus berupa teks.',
                'alamat.max' => 'Alamat maksimal 500 karakter.',
                'no_telpon.string' => 'Nomor telepon harus berupa teks.',
                'no_telpon.max' => 'Nomor telepon maksimal 20 karakter.',
                'saldo.numeric' => 'Saldo harus berupa angka.',
                'saldo.min' => 'Saldo tidak boleh kurang dari 0.',
                'status.required' => 'Status harus dipilih.',
                'status.in' => 'Status harus aktif atau tidak aktif.',
                'ketua_rw.string' => 'Nama ketua RW harus berupa teks.',
                'ketua_rw.max' => 'Nama ketua RW maksimal 255 karakter.'
            ]);

            $rw->update($validated);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'RW berhasil diperbarui',
                    'data' => $rw
                ]);
            }

            return redirect()->route('rt-rw.index')->with('success', 'RW berhasil diperbarui');
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
            \Log::error('Error updating RW: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui data RW. Silakan coba lagi.'
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data RW')->withInput();
        }
    }

    public function destroy(Rw $rw)
    {
        try {
            $rw->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'RW berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting RW: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data RW'
            ], 500);
        }
    }
}
