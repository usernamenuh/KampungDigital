@extends('layouts.app')

@section('title', 'Detail Desa - ' . $desa->village_name)

@push('styles')
<style>
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .info-card {
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .dark .info-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
</style>
@endpush

@section('content')
<div class="p-6 animate-fade-in" x-data="desaShow()" x-init="init()">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="space-y-2">
                <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                    <a href="{{ route('desas.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-200">Data Desa</a>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    <span>Detail Desa</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center mr-4">
                        <i data-lucide="eye" class="w-5 h-5 text-white"></i>
                    </div>
                    {{ $desa->village->village_name ?? $desa->village_name ?? 'Nama Desa' }}
                </h1>
            </div>
            
            <div class="flex space-x-3">
                <a href="{{ route('desas.edit', $desa->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('desas.index') }}" 
                   class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <!-- Village Photo and Basic Info -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-8">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:space-x-8 space-y-6 lg:space-y-0">
                        <!-- Photo -->
                        <div class="flex-shrink-0">
                            @if($desa->foto)
                                <img src="{{ asset('storage/' . $desa->foto) }}" 
                                     alt="{{ $desa->village->village_name ?? 'Foto Desa' }}"
                                     class="w-48 h-48 rounded-2xl object-cover border-4 border-gray-200 dark:border-gray-600 shadow-lg"
                                     onerror="this.onerror=null; this.src='{{ asset('images/placeholder-village.jpg') }}'; this.alt='Foto tidak tersedia';">
                            @else
                                <div class="w-48 h-48 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg">
                                    <i data-lucide="home" class="w-24 h-24 text-white"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Basic Info -->
                        <div class="flex-1 space-y-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $desa->village->village_name ?? 'Nama Desa' }}</h2>
                                <p class="text-lg text-gray-600 dark:text-gray-400">Kode Desa: {{ $desa->village_code ?? '-' }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                            <i data-lucide="map-pin" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kecamatan</p>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $desa->district->district_name ?? '-' }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                            <i data-lucide="building" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kabupaten/Kota</p>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $desa->regency->regency_name ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                                            <i data-lucide="flag" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Provinsi</p>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $desa->province->province_name ?? '-' }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                                            <i data-lucide="mail" class="w-5 h-5 text-yellow-600 dark:text-yellow-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kode Pos</p>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $desa->kode_pos ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Address Information -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 info-card">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                            <i data-lucide="map-pin" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Informasi Alamat</h3>
                            <p class="text-gray-600 dark:text-gray-400">Detail lokasi desa</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Alamat Lengkap</p>
                            <p class="text-gray-900 dark:text-white leading-relaxed">{{ $desa->alamat ?? '-' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl text-center">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Kode Pos</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $desa->kode_pos ?? '-' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl text-center">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Kode Desa</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $desa->village_code ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 info-card">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                            <i data-lucide="dollar-sign" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Informasi Keuangan</h3>
                            <p class="text-gray-600 dark:text-gray-400">Saldo dan status desa</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/10 dark:to-emerald-900/10 rounded-xl border border-green-200 dark:border-green-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-600 dark:text-green-400 mb-1">Total Saldo</p>
                                    <p class="text-3xl font-bold text-green-700 dark:text-green-300">
                                        Rp {{ number_format($desa->saldo ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                    <i data-lucide="wallet" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Status Desa</p>
                                    @if($desa->status === 'aktif')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200">
                                            <div class="w-2 h-2 bg-red-400 rounded-full mr-2"></div>
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </div>
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                    @if($desa->status === 'aktif')
                                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                                    @else
                                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Administrative Information -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Informasi Administratif</h3>
                        <p class="text-gray-600 dark:text-gray-400">Data administratif dan metadata</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl text-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="calendar-plus" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Tanggal Dibuat</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $desa->created_at ? $desa->created_at->format('d M Y') : '-' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $desa->created_at ? $desa->created_at->format('H:i') . ' WIB' : '' }}
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl text-center">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="calendar-check" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Terakhir Diupdate</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $desa->updated_at ? $desa->updated_at->format('d M Y') : '-' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $desa->updated_at ? $desa->updated_at->format('H:i') . ' WIB' : '' }}
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl text-center">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="hash" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">ID Desa</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            #{{ str_pad($desa->id, 4, '0', STR_PAD_LEFT) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Database ID
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                <a href="{{ route('desas.edit', $desa->id) }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i data-lucide="edit" class="w-5 h-5 mr-2"></i>
                    Edit Data Desa
                </a>
                <a href="{{ route('desas.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i data-lucide="list" class="w-5 h-5 mr-2"></i>
                    Lihat Semua Desa
                </a>
                <button @click="printPage()" 
                        class="inline-flex items-center justify-center px-6 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i data-lucide="printer" class="w-5 h-5 mr-2"></i>
                    Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function desaShow() {
    return {
        init() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        },

        printPage() {
            window.print();
        }
    }
}
</script>

@endsection
