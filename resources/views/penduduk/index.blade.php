@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Data Penduduk</h1>
        <p class="text-gray-600">Halaman untuk mengelola data penduduk.</p>
        
        <div class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-blue-800">Total Penduduk</h3>
                    <p class="text-2xl font-bold text-blue-600">2,430</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-green-800">Laki-laki</h3>
                    <p class="text-2xl font-bold text-green-600">1,250</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-purple-800">Perempuan</h3>
                    <p class="text-2xl font-bold text-purple-600">1,180</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
