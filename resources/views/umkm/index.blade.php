@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Data UMKM</h1>
        <p class="text-gray-600">Halaman untuk mengelola data Usaha Mikro, Kecil, dan Menengah.</p>
        
        <div class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-purple-800">UMKM Aktif</h3>
                    <p class="text-2xl font-bold text-purple-600">45</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-yellow-800">Kategori</h3>
                    <p class="text-2xl font-bold text-yellow-600">6</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-red-800">Menunggu Verifikasi</h3>
                    <p class="text-2xl font-bold text-red-600">3</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
