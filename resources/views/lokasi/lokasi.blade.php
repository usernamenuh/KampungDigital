@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Data Lokasi</h1>
        <p class="text-gray-600">Halaman untuk mengelola data lokasi dan wilayah.</p>
        
        <div class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-cyan-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-cyan-800">Total RT</h3>
                    <p class="text-2xl font-bold text-cyan-600">18</p>
                </div>
                <div class="bg-indigo-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-indigo-800">Total RW</h3>
                    <p class="text-2xl font-bold text-indigo-600">12</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
