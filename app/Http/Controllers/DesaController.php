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
        ]);

        $data = $request->only([
            'alamat', 'kode_pos',
            'province_code', 'regency_code', 'district_code', 'village_code'
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
        ]);

        $desa = Desa::findOrFail($id);

        $data = $request->only([
            'alamat', 'kode_pos',
            'province_code', 'regency_code', 'district_code', 'village_code'
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
}
