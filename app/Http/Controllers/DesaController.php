<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Desa;
use Vermaysha\Territory\Models\Province;
use Vermaysha\Territory\Models\Regency;
use Vermaysha\Territory\Models\District;
use Vermaysha\Territory\Models\Village;

class DesaController extends Controller
{
    // Tampilkan daftar desa
    public function index()
    {
        $desas = Desa::with(['province', 'regency', 'district', 'village'])->get();
        return view('desas.index', compact('desas'));
    }

    // Form tambah desa
    public function create()
    {
        $provinces = Province::all();
        $regencies = Regency::all();
        $districts = District::all();
        $villages = Village::all();
        return view('desas.create', compact('provinces', 'regencies', 'districts', 'villages'));
    }

    // Simpan desa baru
    public function store(Request $request)
    {
        $request->validate([
            'alamat' => 'required|string|max:255',
            'kode_pos' => 'required|numeric',
            'province_code' => 'required|exists:id_provinces,province_code',
            'regency_code' => 'required|exists:id_regencies,regency_code',
            'district_code' => 'required|exists:id_districts,district_code',
            'village_code' => 'required|exists:id_villages,village_code',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'saldo' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,tidak_aktif',
            'no_telpon' => 'nullable|numeric',
            'gmail'=> 'nullable|email',
        ], [
            'alamat.required' => 'Alamat harus diisi',
            'alamat.string' => 'Alamat harus berupa teks',
            'alamat.max' => 'Alamat maksimal 255 karakter',
            'kode_pos.required' => 'Kode pos harus diisi',
            'kode_pos.numeric' => 'Kode pos harus berupa angka',
            'province_code.required' => 'Provinsi harus dipilih',
            'province_code.exists' => 'Provinsi tidak valid',
            'regency_code.required' => 'Kabupaten/Kota harus dipilih',
            'regency_code.exists' => 'Kabupaten/Kota tidak valid',
            'district_code.required' => 'Kecamatan harus dipilih',
            'district_code.exists' => 'Kecamatan tidak valid',
            'village_code.required' => 'Desa harus dipilih',
            'village_code.exists' => 'Desa tidak valid',
            'foto.image' => 'Foto harus berupa gambar',
            'foto.mimes' => 'Foto harus berformat jpeg, png, jpg, atau gif',
            'foto.max' => 'Foto maksimal 2MB',
            'saldo.required' => 'Saldo harus diisi',
            'saldo.numeric' => 'Saldo harus berupa angka',
            'saldo.min' => 'Saldo minimal 0',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status harus berupa aktif atau tidak aktif',
        ]);

        $data = $request->only([
            'alamat', 'kode_pos',
            'province_code', 'regency_code', 'district_code', 'village_code', 'saldo', 'status', 'gmail', 'no_telpon'
        ]);

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('desa_foto', 'public');
            $data['foto'] = $foto;
        }

        Desa::create($data);

        return redirect()->route('desas.index')->with('success', 'Desa berhasil ditambahkan!');
    }

    // Form edit desa
    public function edit($id)
    {
        $desa = Desa::findOrFail($id);
        $provinces = Province::all();
        return view('desas.edit', compact('desa', 'provinces'));
    }

    // Update desa
    public function update(Request $request, $id)
    {
        $request->validate([
            'alamat' => 'required|string|max:255',
            'kode_pos' => 'required|numeric',
            'province_code' => 'required|exists:id_provinces,province_code',
            'regency_code' => 'required|exists:id_regencies,regency_code',
            'district_code' => 'required|exists:id_districts,district_code',
            'village_code' => 'required|exists:id_villages,village_code',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'saldo' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,tidak_aktif',
            'no_telpon' => 'nullable|numeric',
            'gmail' => 'nullable|email',
        ],[
            'alamat.required' => 'Alamat harus diisi',
            'alamat.string' => 'Alamat harus berupa teks',
            'alamat.max' => 'Alamat maksimal 255 karakter',
            'kode_pos.required' => 'Kode pos harus diisi',
            'kode_pos.numeric' => 'Kode pos harus berupa angka',
            'province_code.required' => 'Provinsi harus dipilih',
            'province_code.exists' => 'Provinsi tidak valid',
            'regency_code.required' => 'Kabupaten/Kota harus dipilih',
            'regency_code.exists' => 'Kabupaten/Kota tidak valid',
            'district_code.required' => 'Kecamatan harus dipilih',
            'district_code.exists' => 'Kecamatan tidak valid',
            'village_code.required' => 'Desa harus dipilih',
            'village_code.exists' => 'Desa tidak valid',
            'foto.image' => 'Foto harus berupa gambar',
            'foto.mimes' => 'Foto harus berformat jpeg, png, jpg, atau gif',
            'foto.max' => 'Foto maksimal 2MB',
            'saldo.required' => 'Saldo harus diisi',
            'saldo.numeric' => 'Saldo harus berupa angka',
            'saldo.min' => 'Saldo minimal 0',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status harus berupa aktif atau tidak aktif',
            'no_telpon.numeric' => 'Nomor telepon harus berupa angka',
            'gmail.string'=> 'gmail harus di isi',
        ]);

        $desa = Desa::findOrFail($id);

        $data = $request->only([
            'alamat', 'kode_pos',
            'province_code', 'regency_code', 'district_code', 'village_code', 'saldo', 'status', 'gmail', 'no_telpon'
        ]);

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('desa_foto', 'public');
            $data['foto'] = $foto;
        }

        $desa->update($data);

        return redirect()->route('desas.index')->with('success', 'Desa berhasil diupdate!');
    }

    // Hapus desa
    public function destroy($id)
    {
        $desa = Desa::findOrFail($id);
        $desa->delete();

        return redirect()->route('desas.index')->with('success', 'Desa berhasil dihapus!');
    }

    public function show($id)
    {
        $desa = Desa::findOrFail($id);
        return view('desas.show', compact('desa'));
    }
}
