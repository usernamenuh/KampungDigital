<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Desa;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\RegProvince;
use App\Models\RegRegency;
use App\Models\RegDistrict;
use App\Models\RegVillage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DesaController extends Controller
{
    public function index()
    {
        // Ambil data desa dengan eager loading
        $desas = Desa::with(['kepala', 'province', 'regency', 'district', 'village'])->get();
        
        return view('desas.index', compact('desas'));
    }

    public function create()
    {
        $provinces = RegProvince::orderBy('name')->get();
        
        $rtKetuaIds = Rt::pluck('ketua_rt_id')->filter()->unique()->toArray();
        $rwKetuaIds = Rw::pluck('ketua_rw_id')->filter()->unique()->toArray();
        $desaKepalaIds = Desa::pluck('kepala_desa_id')->filter()->unique()->toArray();

        $occupiedPendudukIds = array_merge($rtKetuaIds, $rwKetuaIds, $desaKepalaIds);
        $occupiedPendudukIds = array_unique($occupiedPendudukIds);

        $penduduks = Penduduk::whereNotIn('id', $occupiedPendudukIds)->get();

        return view('desas.create', compact('provinces', 'penduduks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alamat' => 'required|string|max:255',
            'kode_pos' => 'required|numeric',
            'province_id' => 'required|exists:reg_provinces,id',
            'regency_id' => 'required|exists:reg_regencies,id',
            'district_id' => 'required|exists:reg_districts,id',
            'village_id' => 'required|exists:reg_villages,id|unique:desas,village_id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'saldo' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,tidak_aktif',
            'no_telpon' => 'nullable|string|max:20',
            'gmail'=> 'nullable|email',
            'kepala_desa_id' => [
                'nullable',
                'exists:penduduks,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $isRtKetua = Rt::where('ketua_rt_id', $value)->exists();
                        $isRwKetua = Rw::where('ketua_rw_id', $value)->exists();
                        $isDesaKepala = Desa::where('kepala_desa_id', $value)->exists();

                        if ($isRtKetua || $isRwKetua || $isDesaKepala) {
                            $fail('Penduduk yang dipilih sudah menjabat sebagai Ketua RT, Ketua RW, atau Kepala Desa di desa lain dan tidak bisa menjadi Kepala Desa.');
                        }
                    }
                },
            ],
        ], [
            'alamat.required' => 'Alamat harus diisi',
            'alamat.string' => 'Alamat harus berupa teks',
            'alamat.max' => 'Alamat maksimal 255 karakter',
            'kode_pos.required' => 'Kode pos harus diisi',
            'kode_pos.numeric' => 'Kode pos harus berupa angka',
            'province_id.required' => 'Provinsi harus dipilih',
            'province_id.exists' => 'Provinsi tidak valid',
            'regency_id.required' => 'Kabupaten/Kota harus dipilih',
            'regency_id.exists' => 'Kabupaten/Kota tidak valid',
            'district_id.required' => 'Kecamatan harus dipilih',
            'district_id.exists' => 'Kecamatan tidak valid',
            'village_id.required' => 'Desa harus dipilih',
            'village_id.exists' => 'Desa tidak valid',
            'village_id.unique' => 'Desa ini sudah terdaftar.',
            'foto.image' => 'Foto harus berupa gambar',
            'foto.mimes' => 'Foto harus berformat jpeg, png, jpg, atau gif',
            'foto.max' => 'Foto maksimal 2MB',
            'saldo.required' => 'Saldo harus diisi',
            'saldo.numeric' => 'Saldo harus berupa angka',
            'saldo.min' => 'Saldo minimal 0',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status harus berupa aktif atau tidak aktif',
            'no_telpon.string' => 'Nomor telepon harus berupa teks',
            'no_telpon.max' => 'Nomor telepon maksimal 20 karakter',
            'gmail.email' => 'Format email tidak valid',
            'kepala_desa_id.exists' => 'Kepala Desa yang dipilih tidak valid.',
        ]);

        $data = $request->only([
            'alamat', 'kode_pos',
            'province_id', 'regency_id', 'district_id', 'village_id', 
            'saldo', 'status', 'gmail', 'no_telpon',
            'kepala_desa_id'
        ]);

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('desa_foto', 'public');
            $data['foto'] = $foto;
        }

        Desa::create($data);

        return redirect()->route('desas.index')->with('success', 'Desa berhasil ditambahkan!');
    }

    public function show($id)
    {
        $desa = Desa::with(['kepala', 'province', 'regency', 'district', 'village'])->findOrFail($id);
        
        return view('desas.show', compact('desa'));
    }

    public function edit($id)
    {
        $desa = Desa::with(['kepala', 'province', 'regency', 'district', 'village'])->findOrFail($id);
        
        $provinces = RegProvince::orderBy('name')->get();

        $rtKetuaIds = Rt::pluck('ketua_rt_id')->filter()->unique()->toArray();
        $rwKetuaIds = Rw::pluck('ketua_rw_id')->filter()->unique()->toArray();
        $desaKepalaIds = Desa::where('id', '!=', $id)->pluck('kepala_desa_id')->filter()->unique()->toArray();

        $occupiedPendudukIds = array_merge($rtKetuaIds, $rwKetuaIds, $desaKepalaIds);
        $occupiedPendudukIds = array_unique($occupiedPendudukIds);

        $penduduks = Penduduk::whereNotIn('id', $occupiedPendudukIds)
                                ->when($desa->kepala_desa_id, function ($query) use ($desa) {
                                    $query->orWhere('id', $desa->kepala_desa_id);
                                })
                                ->get();

        return view('desas.edit', compact('desa', 'provinces', 'penduduks'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'alamat' => 'required|string|max:255',
            'kode_pos' => 'required|numeric',
            'province_id' => 'required|exists:reg_provinces,id',
            'regency_id' => 'required|exists:reg_regencies,id',
            'district_id' => 'required|exists:reg_districts,id',
            'village_id' => [
                'required',
                'exists:reg_villages,id',
                Rule::unique('desas', 'village_id')->ignore($id),
            ],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'saldo' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,tidak_aktif',
            'no_telpon' => 'nullable|string|max:20',
            'gmail' => 'nullable|email',
            'kepala_desa_id' => [
                'nullable',
                'exists:penduduks,id',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value) {
                        $currentDesa = Desa::find($id);
                        if ($currentDesa && $currentDesa->kepala_desa_id == $value) {
                            return;
                        }

                        $isRtKetua = Rt::where('ketua_rt_id', $value)->exists();
                        $isRwKetua = Rw::where('ketua_rw_id', $value)->exists();
                        $isDesaKepalaInOtherDesa = Desa::where('id', '!=', $id)->where('kepala_desa_id', $value)->exists();

                        if ($isRtKetua || $isRwKetua || $isDesaKepalaInOtherDesa) {
                            $fail('Penduduk yang dipilih sudah menjabat sebagai Ketua RT, Ketua RW, atau Kepala Desa di desa lain dan tidak bisa menjadi Kepala Desa.');
                        }
                    }
                },
            ],
        ],[
            'alamat.required' => 'Alamat harus diisi',
            'alamat.string' => 'Alamat harus berupa teks',
            'alamat.max' => 'Alamat maksimal 255 karakter',
            'kode_pos.required' => 'Kode pos harus diisi',
            'kode_pos.numeric' => 'Kode pos harus berupa angka',
            'province_id.required' => 'Provinsi harus dipilih',
            'province_id.exists' => 'Provinsi tidak valid',
            'regency_id.required' => 'Kabupaten/Kota harus dipilih',
            'regency_id.exists' => 'Kabupaten/Kota tidak valid',
            'district_id.required' => 'Kecamatan harus dipilih',
            'district_id.exists' => 'Kecamatan tidak valid',
            'village_id.required' => 'Desa harus dipilih',
            'village_id.exists' => 'Desa tidak valid',
            'village_id.unique' => 'Desa ini sudah terdaftar.',
            'foto.image' => 'Foto harus berupa gambar',
            'foto.mimes' => 'Foto harus berformat jpeg, png, jpg, atau gif',
            'foto.max' => 'Foto maksimal 2MB',
            'saldo.required' => 'Saldo harus diisi',
            'saldo.numeric' => 'Saldo harus berupa angka',
            'saldo.min' => 'Saldo minimal 0',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status harus berupa aktif atau tidak aktif',
            'no_telpon.string' => 'Nomor telepon harus berupa teks',
            'no_telpon.max' => 'Nomor telepon maksimal 20 karakter',
            'gmail.email' => 'Format email tidak valid',
            'kepala_desa_id.exists' => 'Kepala Desa yang dipilih tidak valid.',
        ]);

        $desa = Desa::findOrFail($id);

        $data = $request->only([
            'alamat', 'kode_pos',
            'province_id', 'regency_id', 'district_id', 'village_id',
            'saldo', 'status', 'gmail', 'no_telpon',
            'kepala_desa_id'
        ]);

        if ($request->hasFile('foto')) {
            if ($desa->foto && Storage::disk('public')->exists($desa->foto)) {
                Storage::disk('public')->delete($desa->foto);
            }
            $foto = $request->file('foto')->store('desa_foto', 'public');
            $data['foto'] = $foto;
        } else if ($request->input('clear_foto')) {
            if ($desa->foto && Storage::disk('public')->exists($desa->foto)) {
                Storage::disk('public')->delete($desa->foto);
            }
            $data['foto'] = null;
        }

        $desa->update($data);

        return redirect()->route('desas.index')->with('success', 'Desa berhasil diupdate!');
    }

    public function destroy($id)
    {
        $desa = Desa::findOrFail($id);
        if ($desa->foto && Storage::disk('public')->exists($desa->foto)) {
            Storage::disk('public')->delete($desa->foto);
        }
        $desa->delete();

        return redirect()->route('desas.index')->with('success', 'Desa berhasil dihapus!');
    }
}
