@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('kk.index') }}" class="p-2 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Kartu Keluarga</h1>
                    <p class="text-sm text-gray-600">{{ $kk->no_kk }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- KK Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kartu Keluarga</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">No. Kartu Keluarga</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $kk->no_kk }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Alamat</label>
                            <p class="text-sm text-gray-900">{{ $kk->alamat }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">RT/RW</label>
                            <p class="text-sm text-gray-900">RT {{ $kk->rt->no_rt }} / RW {{ $kk->rt->rw->no_rw }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Tanggal Dibuat</label>
                            <p class="text-sm text-gray-900">{{ $kk->tanggal_dibuat->format('d F Y') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $kk->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($kk->status) }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Kepala Keluarga</label>
                            @if($kk->kepalaKeluarga)
                                <div class="flex items-center space-x-3 mt-2">
                                    @if($kk->kepalaKeluarga->foto)
                                        <img src="{{ asset('storage/' . $kk->kepalaKeluarga->foto) }}" alt="Foto" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $kk->kepalaKeluarga->nama_lengkap }}</p>
                                        <p class="text-xs text-gray-500">{{ $kk->kepalaKeluarga->nik }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Belum ditetapkan
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('kk.edit', $kk) }}" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 text-center">
                            Edit KK
                        </a>
                    </div>
                </div>
            </div>

            <!-- Anggota Keluarga -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Anggota Keluarga ({{ $kk->penduduks->count() }} orang)</h3>
                        <a href="{{ route('penduduk.create') }}?kk_id={{ $kk->id }}" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Anggota
                        </a>
                    </div>
                    
                    <div class="p-6">
                        @if($kk->penduduks->count() > 0)
                            <div class="space-y-4">
                                @foreach($kk->penduduks as $penduduk)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-center space-x-4">
                                        @if($penduduk->foto)
                                            <img src="{{ asset('storage/' . $penduduk->foto) }}" alt="Foto" class="w-12 h-12 rounded-full object-cover">
                                        @else
                                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <h4 class="text-sm font-medium text-gray-900">{{ $penduduk->nama_lengkap }}</h4>
                                                @if($penduduk->id === $kk->kepala_keluarga_id)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        Kepala Keluarga
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500">{{ $penduduk->nik }}</p>
                                            <div class="flex items-center space-x-4 mt-1">
                                                <span class="text-xs text-gray-600">{{ $penduduk->hubungan_keluarga }}</span>
                                                <span class="text-xs text-gray-600">{{ $penduduk->umur }} tahun</span>
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $penduduk->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                                    {{ $penduduk->jenis_kelamin == 'L' ? 'L' : 'P' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        @if($penduduk->id !== $kk->kepala_keluarga_id && $penduduk->hubungan_keluarga !== 'Kepala Keluarga')
                                            <form action="{{ route('kk.set-kepala-keluarga', $kk) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="kepala_keluarga_id" value="{{ $penduduk->id }}">
                                                <button type="submit" class="text-xs px-2 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded transition-colors duration-200" onclick="return confirm('Tetapkan {{ $penduduk->nama_lengkap }} sebagai kepala keluarga?')">
                                                    Jadikan Kepala
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <a href="{{ route('penduduk.show', $penduduk) }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        
                                        <a href="{{ route('penduduk.edit', $penduduk) }}" class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada anggota keluarga</h3>
                                <p class="text-gray-500 mb-4">Tambahkan anggota keluarga untuk kartu keluarga ini</p>
                                <a href="{{ route('penduduk.create') }}?kk_id={{ $kk->id }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Anggota Pertama
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
