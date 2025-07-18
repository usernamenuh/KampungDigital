@extends('layouts.app')

@section('title', 'Detail Kas')

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

.payment-timeline {
    position: relative;
}

.payment-timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #3b82f6, #10b981);
}

.timeline-item {
    position: relative;
    padding-left: 3rem;
    margin-bottom: 1.5rem;
}

.timeline-dot {
    position: absolute;
    left: 0.5rem;
    top: 0.5rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #3b82f6;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .mobile-text-sm { font-size: 0.75rem; }
    .mobile-text-xs { font-size: 0.6875rem; }
    .mobile-p-3 { padding: 0.75rem; }
    .mobile-p-4 { padding: 1rem; }
    .mobile-gap-3 { gap: 0.75rem; }
    .mobile-space-y-3 > * + * { margin-top: 0.75rem; }
    
    .timeline-item {
        padding-left: 2rem;
        margin-bottom: 1rem;
    }
    
    .payment-timeline::before {
        left: 0.5rem;
    }
    
    .timeline-dot {
        left: 0rem;
        width: 0.75rem;
        height: 0.75rem;
    }
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4 md:py-6 animate-fade-in" x-data="kasDetail()">
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
    <!-- Header Section -->
    <div class="mb-6 md:mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 md:gap-6">
                <div class="flex items-center space-x-3 md:space-x-4">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg md:rounded-xl flex items-center justify-center shadow-lg">
                        <i data-lucide="receipt" class="w-5 h-5 md:w-6 md:h-6 text-white"></i>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2 text-xs md:text-sm text-gray-500 dark:text-gray-400 mb-1">
                            <a href="{{ route('kas.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Daftar Kas</a>
                            <i data-lucide="chevron-right" class="w-3 h-3 md:w-4 md:h-4"></i>
                            <span>Detail Kas</span>
                        </div>
                        <h1 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white">Detail Kas Minggu ke-{{ $kas->minggu_ke }}</h1>
                        <p class="text-sm md:text-base text-gray-600 dark:text-gray-400 mt-1">
                            @if($kas->penduduk)
                                {{ $kas->penduduk->nama_lengkap }} - {{ $kas->tahun }}
                            @else
                                <span class="text-red-500">Data penduduk tidak tersedia - {{ $kas->tahun }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2 md:space-x-3 w-full lg:w-auto">
                    @switch($kas->status)
                        @case('lunas')
                            <span class="inline-flex items-center px-3 md:px-4 py-1 md:py-2 rounded-full text-xs md:text-sm font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                Lunas
                            </span>
                            @break
                        @case('belum_bayar')
                            <span class="inline-flex items-center px-3 md:px-4 py-1 md:py-2 rounded-full text-xs md:text-sm font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-800">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                Belum Bayar
                            </span>
                            @break
                        @case('terlambat')
                            <span class="inline-flex items-center px-3 md:px-4 py-1 md:py-2 rounded-full text-xs md:text-sm font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                                <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                Terlambat
                            </span>
                            @break
                        @case('menunggu_konfirmasi')
                            <span class="inline-flex items-center px-3 md:px-4 py-1 md:py-2 rounded-full text-xs md:text-sm font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 border border-blue-200 dark:border-blue-800">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                Menunggu Konfirmasi
                            </span>
                            @break
                        @case('ditolak')
                            <span class="inline-flex items-center px-3 md:px-4 py-1 md:py-2 rounded-full text-xs md:text-sm font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                                <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                Ditolak
                            </span>
                            @break
                    @endswitch
                    <a href="{{ route('kas.index') }}" 
                       class="inline-flex items-center px-3 md:px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg md:rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md text-xs md:text-sm">
                        <i data-lucide="arrow-left" class="w-3 h-3 md:w-4 md:h-4 mr-1 md:mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6 md:space-y-8">
            <!-- Kas Details Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-700">
                    <div class="flex items-center space-x-2 md:space-x-3">
                        <div class="w-6 h-6 md:w-8 md:h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-md md:rounded-lg flex items-center justify-center">
                            <i data-lucide="user" class="w-3 h-3 md:w-4 md:h-4 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-base md:text-lg font-bold text-gray-900 dark:text-white">Informasi Warga</h2>
                            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400">Detail penduduk dan kas</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 md:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                        <!-- Left Column -->
                        <div class="space-y-4 md:space-y-6">
                            <div class="flex items-center space-x-3 md:space-x-4">
                                <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold text-lg md:text-xl">
                                    @if($kas->penduduk)
                                        {{ substr($kas->penduduk->nama_lengkap, 0, 1) }}
                                    @else
                                        ?
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">
                                        @if($kas->penduduk)
                                            {{ $kas->penduduk->nama_lengkap }}
                                        @else
                                            <span class="text-red-500">Data tidak tersedia</span>
                                        @endif
                                    </h3>
                                    <p class="text-sm md:text-base text-gray-600 dark:text-gray-400">
                                        NIK: 
                                        @if($kas->penduduk)
                                            {{ $kas->penduduk->nik }}
                                        @else
                                            <span class="text-red-500">-</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="space-y-3 md:space-y-4">
                                <div class="flex items-center justify-between py-2 md:py-3 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-sm md:text-base text-gray-600 dark:text-gray-400 font-medium">RT/RW</span>
                                    <span class="text-sm md:text-base text-gray-900 dark:text-white font-semibold">
                                        @if($kas->rt)
                                            RT {{ $kas->rt->no_rt ?? $kas->rt->nama_rt }} / RW {{ $kas->rt->rw->no_rw ?? $kas->rt->rw->nama_rw }}
                                        @else
                                            <span class="text-red-500">Data tidak tersedia</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center justify-between py-2 md:py-3 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-sm md:text-base text-gray-600 dark:text-gray-400 font-medium">Periode</span>
                                    <span class="text-sm md:text-base text-gray-900 dark:text-white font-semibold">
                                        Minggu ke-{{ $kas->minggu_ke }} Tahun {{ $kas->tahun }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4 md:space-y-6">
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg md:rounded-xl p-4 md:p-6 border border-green-200 dark:border-green-800">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm md:text-base text-green-700 dark:text-green-300 font-medium">Jumlah Kas</span>
                                    <i data-lucide="banknote" class="w-4 h-4 md:w-5 md:h-5 text-green-600"></i>
                                </div>
                                <div class="text-2xl md:text-3xl font-bold text-green-600 dark:text-green-400">
                                    Rp {{ number_format((int)$kas->jumlah, 0, ',', '.') }}
                                </div>
                            </div>
                            
                            <div class="space-y-3 md:space-y-4">
                                <div class="flex items-center justify-between py-2 md:py-3 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-sm md:text-base text-gray-600 dark:text-gray-400 font-medium">Jatuh Tempo</span>
                                    <span class="text-sm md:text-base text-gray-900 dark:text-white font-semibold {{ $kas->tanggal_jatuh_tempo && $kas->tanggal_jatuh_tempo->isPast() && $kas->status !== 'lunas' ? 'text-red-600' : '' }}">
                                        {{ $kas->tanggal_jatuh_tempo ? $kas->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}
                                        @if($kas->tanggal_jatuh_tempo && $kas->tanggal_jatuh_tempo->isPast() && $kas->status !== 'lunas')
                                            <small class="text-red-500 ml-1">(Terlambat)</small>
                                        @endif
                                    </span>
                                </div>
                                
                                @if($kas->status === 'lunas')
                                <div class="flex items-center justify-between py-2 md:py-3 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-sm md:text-base text-gray-600 dark:text-gray-400 font-medium">Tanggal Bayar</span>
                                    <span class="text-sm md:text-base text-green-600 dark:text-green-400 font-semibold">
                                        {{ $kas->tanggal_bayar ? $kas->tanggal_bayar->format('d/m/Y H:i') : '-' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between py-2 md:py-3">
                                    <span class="text-sm md:text-base text-gray-600 dark:text-gray-400 font-medium">Metode Bayar</span>
                                    <span class="text-sm md:text-base text-gray-900 dark:text-white font-semibold">
                                        {{ $kas->metode_bayar ?? '-' }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($kas->keterangan)
                    <div class="mt-6 md:mt-8 p-3 md:p-4 bg-gray-50 dark:bg-gray-700 rounded-lg md:rounded-xl">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2 flex items-center text-sm md:text-base">
                            <i data-lucide="file-text" class="w-3 h-3 md:w-4 md:h-4 mr-2"></i>
                            Keterangan
                        </h4>
                        <p class="text-sm md:text-base text-gray-600 dark:text-gray-400">{{ $kas->keterangan }}</p>
                    </div>
                    @endif

                    @if($kas->bukti_bayar)
                    <div class="mt-6 md:mt-8 p-3 md:p-4 bg-gray-50 dark:bg-gray-700 rounded-lg md:rounded-xl">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2 flex items-center text-sm md:text-base">
                            <i data-lucide="image" class="w-3 h-3 md:w-4 md:h-4 mr-2"></i>
                            Bukti Pembayaran
                        </h4>
                        <p class="text-sm md:text-base text-gray-600 dark:text-gray-400">{{ $kas->bukti_bayar }}</p>
                    </div>
                    @endif
                </div>
                
                <!-- Action Footer -->
                <div class="px-4 md:px-6 py-3 md:py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 md:gap-4">
                        <div class="text-xs md:text-sm text-gray-600 dark:text-gray-400">
                            <i data-lucide="clock" class="w-3 h-3 md:w-4 md:h-4 inline mr-1"></i>
                            Dibuat: {{ $kas->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="flex items-center space-x-2 md:space-x-3">
                            @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
                                <a href="{{ route('kas.edit', $kas) }}"
                                   class="inline-flex items-center px-3 md:px-4 py-2 text-amber-600 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/20 dark:hover:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-lg transition-all duration-200 text-xs md:text-sm">
                                    <i data-lucide="edit" class="w-3 h-3 md:w-4 md:h-4 mr-1 md:mr-2"></i>
                                    Edit
                                </a>
                            @endif
                            
                            @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']) && $kas->status !== 'lunas')
                                <button @click="showPaymentModal = true"
                                        class="inline-flex items-center px-3 md:px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl text-xs md:text-sm">
                                    <i data-lucide="credit-card" class="w-3 h-3 md:w-4 md:h-4 mr-1 md:mr-2"></i>
                                    Konfirmasi Bayar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Status Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-4 md:p-6 mb-4 md:mb-6">
                <div class="flex items-center space-x-2 md:space-x-3 mb-3 md:mb-4">
                    <div class="w-6 h-6 md:w-8 md:h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-md md:rounded-lg flex items-center justify-center">
                        <i data-lucide="info" class="w-3 h-3 md:w-4 md:h-4 text-white"></i>
                    </div>
                    <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white">Status Pembayaran</h3>
                </div>
                
                @if($kas->status === 'lunas')
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg md:rounded-xl p-3 md:p-4">
                        <div class="flex items-center">
                            <i data-lucide="check-circle" class="w-5 h-5 md:w-6 md:h-6 text-green-600 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-green-800 dark:text-green-200 text-sm md:text-base">Kas Sudah Lunas</h4>
                                <p class="text-xs md:text-sm text-green-600 dark:text-green-300 mt-1">
                                    Dibayar pada {{ $kas->tanggal_bayar ? $kas->tanggal_bayar->format('d/m/Y H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($kas->tanggal_jatuh_tempo && $kas->tanggal_jatuh_tempo->isPast() && $kas->status !== 'lunas')
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg md:rounded-xl p-3 md:p-4">
                        <div class="flex items-center">
                            <i data-lucide="alert-triangle" class="w-5 h-5 md:w-6 md:h-6 text-red-600 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-red-800 dark:text-red-200 text-sm md:text-base">Kas Terlambat</h4>
                                <p class="text-xs md:text-sm text-red-600 dark:text-red-300 mt-1">
                                    Jatuh tempo: {{ $kas->tanggal_jatuh_tempo->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($kas->status === 'menunggu_konfirmasi')
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg md:rounded-xl p-3 md:p-4">
                        <div class="flex items-center">
                            <i data-lucide="clock" class="w-5 h-5 md:w-6 md:h-6 text-blue-600 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-blue-800 dark:text-blue-200 text-sm md:text-base">Menunggu Konfirmasi</h4>
                                <p class="text-xs md:text-sm text-blue-600 dark:text-blue-300 mt-1">
                                    Menunggu verifikasi pembayaran.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($kas->status === 'ditolak')
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg md:rounded-xl p-3 md:p-4">
                        <div class="flex items-center">
                            <i data-lucide="x-circle" class="w-5 h-5 md:w-6 md:h-6 text-red-600 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-red-800 dark:text-red-200 text-sm md:text-base">Pembayaran Ditolak</h4>
                                <p class="text-xs md:text-sm text-red-600 dark:text-red-300 mt-1">
                                    Bukti pembayaran tidak valid atau tidak sesuai.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg md:rounded-xl p-3 md:p-4">
                        <div class="flex items-center">
                            <i data-lucide="clock" class="w-5 h-5 md:w-6 md:h-6 text-yellow-600 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 text-sm md:text-base">Belum Dibayar</h4>
                                <p class="text-xs md:text-sm text-yellow-600 dark:text-yellow-300 mt-1">
                                    Jatuh tempo: {{ $kas->tanggal_jatuh_tempo ? $kas->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Payment History -->
            <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                <div class="flex items-center space-x-2 md:space-x-3 mb-3 md:mb-4">
                    <div class="w-6 h-6 md:w-8 md:h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-md md:rounded-lg flex items-center justify-center">
                        <i data-lucide="history" class="w-3 h-3 md:w-4 md:h-4 text-white"></i>
                    </div>
                    <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white">Riwayat Pembayaran</h3>
                </div>
                
                <div class="payment-timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot bg-blue-500"></div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2 md:p-3">
                            <h4 class="font-semibold text-gray-900 dark:text-white text-xs md:text-sm">Kas Dibuat</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                {{ $kas->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                    
                    @if($kas->status === 'menunggu_konfirmasi')
                    <div class="timeline-item">
                        <div class="timeline-dot bg-blue-500"></div>
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-2 md:p-3">
                            <h4 class="font-semibold text-blue-800 dark:text-blue-200 text-xs md:text-sm">Menunggu Konfirmasi</h4>
                            <p class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                                Bukti pembayaran diunggah pada {{ $kas->tanggal_bayar ? $kas->tanggal_bayar->format('d/m/Y H:i') : '-' }}
                            </p>
                            @if($kas->metode_bayar)
                                <p class="text-xs text-blue-600 dark:text-blue-300">
                                    Via {{ ucfirst(str_replace('_', ' ', $kas->metode_bayar)) }}
                                </p>
                            @endif
                            @if($kas->bukti_bayar)
                                <p class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                                    Bukti: {{ $kas->bukti_bayar }}
                                </p>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($kas->status === 'ditolak')
                    <div class="timeline-item">
                        <div class="timeline-dot bg-red-500"></div>
                        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-2 md:p-3">
                            <h4 class="font-semibold text-red-800 dark:text-red-200 text-xs md:text-sm">Pembayaran Ditolak</h4>
                            <p class="text-xs text-red-600 dark:text-red-300 mt-1">
                                Ditolak pada {{ $kas->updated_at->format('d/m/Y H:i') }}
                            </p>
                            @if($kas->keterangan)
                                <p class="text-xs text-red-600 dark:text-red-300 mt-1">
                                    Alasan: {{ $kas->keterangan }}
                                </p>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($kas->status === 'lunas')
                    <div class="timeline-item">
                        <div class="timeline-dot bg-green-500"></div>
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-2 md:p-3">
                            <h4 class="font-semibold text-green-800 dark:text-green-200 text-xs md:text-sm">Pembayaran Lunas</h4>
                            <p class="text-xs text-green-600 dark:text-green-300 mt-1">
                                {{ $kas->tanggal_bayar ? $kas->tanggal_bayar->format('d/m/Y H:i') : '-' }}
                            </p>
                            @if($kas->metode_bayar)
                                <p class="text-xs text-green-600 dark:text-green-300">
                                    Via {{ ucfirst(str_replace('_', ' ', $kas->metode_bayar)) }}
                                </p>
                            @endif
                            @if($kas->bukti_bayar)
                                <p class="text-xs text-green-600 dark:text-green-300 mt-1">
                                    Bukti: {{ $kas->bukti_bayar }}
                                </p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Confirmation Modal -->
    @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']) && $kas->status !== 'lunas')
    <div x-show="showPaymentModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75 backdrop-blur-sm"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showPaymentModal = false"></div>

            <div class="inline-block w-full max-w-md p-4 md:p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-2xl rounded-xl md:rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4 md:mb-6">
                    <div class="flex items-center space-x-2 md:space-x-3">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg md:rounded-xl flex items-center justify-center">
                            <i data-lucide="credit-card" class="w-4 h-4 md:w-5 md:h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">Konfirmasi Pembayaran</h3>
                            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400">Rp {{ number_format((int)$kas->jumlah, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <button @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <i data-lucide="x" class="w-5 h-5 md:w-6 md:h-6"></i>
                    </button>
                </div>

                <form action="{{ route('kas.bayar', $kas) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="space-y-3 md:space-y-4">
                        <div>
                            <label for="metode_bayar" class="block text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Metode Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <select name="metode_bayar" id="metode_bayar" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-transparent text-xs md:text-sm" 
                                    required>
                                <option value="">Pilih Metode</option>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="digital">Digital Payment</option>
                                <option value="e_wallet">E-Wallet</option>
                            </select>
                        </div>
                        <div>
                            <label for="bukti_bayar" class="block text-xs md:text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Bukti/Keterangan
                            </label>
                            <textarea name="bukti_bayar" id="bukti_bayar" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none text-xs md:text-sm"
                                      placeholder="Nomor referensi, keterangan, atau bukti pembayaran..."></textarea>
                        </div>
                    </div>

                    <div class="mt-4 md:mt-6 flex gap-2 md:gap-3">
                        <button type="button" @click="showPaymentModal = false" 
                                class="flex-1 px-3 md:px-4 py-2 md:py-3 text-xs md:text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg md:rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                            <i data-lucide="x" class="w-3 h-3 md:w-4 md:h-4 inline mr-1 md:mr-2"></i>
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 px-3 md:px-4 py-2 md:py-3 text-xs md:text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 rounded-lg md:rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i data-lucide="check" class="w-3 h-3 md:w-4 md:h-4 inline mr-1 md:mr-2"></i>
                            Konfirmasi Bayar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
</div>

<script>
function kasDetail() {
    return {
        showPaymentModal: false,

        init() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    }
}
</script>

@push('scripts')
<script>
// Initialize icons after page load
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endpush
@endsection
