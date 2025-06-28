@extends('layouts.auth')

@section('content')
<div class="container" style="max-width: 600px; margin: 0 auto;">
    <h2 class="mb-4">Tambah Data Desa</h2>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <form action="{{ route('desas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="province" class="form-label">Provinsi</label>
            <select id="province" name="province_code" class="form-select" required>
                <option value="">-- Pilih Provinsi --</option>
                @foreach(DB::table('id_provinces')->get() as $prov)
                    <option value="{{ $prov->province_code }}">{{ $prov->province_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="regency" class="form-label">Kabupaten/Kota</label>
            <select id="regency" name="regency_code" class="form-select" required>
                <option value="">-- Pilih Kabupaten/Kota --</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="district" class="form-label">Kecamatan</label>
            <select id="district" name="district_code" class="form-select" required>
                <option value="">-- Pilih Kecamatan --</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="village" class="form-label">Desa</label>
            <select id="village" name="village_code" class="form-select" required>
                <option value="">-- Pilih Desa --</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" name="alamat" id="alamat" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="kode_pos" class="form-label">Kode Pos</label>
            <input type="number" name="kode_pos" id="kode_pos" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="foto" class="form-label">Foto Desa (opsional)</label>
            <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let selectedProvince = '';
let selectedRegency = '';

$('#province').on('change', function () {
    selectedProvince = $(this).val();

    // Reset dropdown bawah
    $('#regency').html('<option>Loading...</option>');
    $('#district').html('<option value="">-- Pilih Kecamatan --</option>');
    $('#village').html('<option value="">-- Pilih Desa --</option>');

    if (selectedProvince) {
        $.get(`/api/regencies/${selectedProvince}`, function (response) {
            let options = '<option value="">-- Pilih Kabupaten/Kota --</option>';

            if (response.success && response.data.length > 0) {
                response.data.forEach(item => {
                    options += `<option value="${item.regency_code}">${item.regency_name}</option>`;
                });
            } else {
                options += '<option value="">Data tidak ditemukan</option>';
            }

            $('#regency').html(options);
        }).fail(function () {
            alert('Gagal mengambil data kabupaten/kota');
        });
    }
});

$('#regency').on('change', function () {
    selectedRegency = $(this).val();

    $('#district').html('<option>Loading...</option>');
    $('#village').html('<option value="">-- Pilih Desa --</option>');

    if (selectedProvince && selectedRegency) {
        $.get(`/api/districts/${selectedProvince}/${selectedRegency}`, function (response) {
            let options = '<option value="">-- Pilih Kecamatan --</option>';

            if (response.success && response.data.length > 0) {
                response.data.forEach(item => {
                    options += `<option value="${item.district_code}">${item.district_name}</option>`;
                });
            } else {
                options += '<option value="">Data tidak ditemukan</option>';
            }

            $('#district').html(options);
        }).fail(function () {
            alert('Gagal mengambil data kecamatan');
        });
    }
});

$('#district').on('change', function () {
    const selectedDistrict = $(this).val();
    $('#village').html('<option>Loading...</option>');

    if (selectedProvince && selectedRegency && selectedDistrict) {
        $.get(`/api/villages/${selectedProvince}/${selectedRegency}/${selectedDistrict}`, function (response) {
            let options = '<option value="">-- Pilih Desa --</option>';

            if (response.success && response.data.length > 0) {
                response.data.forEach(item => {
                    options += `<option value="${item.village_code}">${item.village_name}</option>`;
                });
            } else {
                options += '<option value="">Data tidak ditemukan</option>';
            }

            $('#village').html(options);
        }).fail(function () {
            alert('Gagal mengambil data desa');
        });
    }
});
</script>

@endsection