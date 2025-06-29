@extends('layouts.app')


@section('content')
<div class="container">
    <h3>Daftar Desa</h3>
    <a href="{{ route('desas.create') }}" class="btn btn-primary mb-3">Tambah Desa</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Provinsi</th>
                <th>Kabupaten/Kota</th>
                <th>Kecamatan</th>
                <th>Wilayah (Desa)</th>
                <th>Alamat</th>
                <th>Kode Pos</th>
                <th>Saldo</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($desas as $desa)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if($desa->foto)
                        <img src="{{ asset('storage/' . $desa->foto) }}" alt="Foto Desa" style="max-width: 70px; max-height: 70px; object-fit:cover; border-radius:8px;">
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $desa->province->province_name ?? '-' }}</td>
                <td>{{ $desa->regency->regency_name ?? '-' }}</td>
                <td>{{ $desa->district->district_name ?? '-' }}</td>
                <td>{{ $desa->village->village_name ?? '-' }}</td>
                <td>{{ $desa->alamat }}</td>
                <td>{{ $desa->kode_pos }}</td>
                <td>{{ $desa->saldo }}</td>
                <td>{{ $desa->status }}</td>
                <td>
                    <a href="{{ route('desas.edit', $desa->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('desas.destroy', $desa->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection