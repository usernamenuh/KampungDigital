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
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 animate-fade-in" x-data="kasDetail()">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i data-lucide="receipt" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                            <a href="{{ route('kas.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Daftar Kas</a>
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            <span>Detail Kas</span>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Kas Minggu ke-{{ $kas->minggu_ke }}</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $kas->penduduk->nama_lengkap }} - {{ $kas->tahun }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @switch($kas->status)
                        @case('lunas')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                Lunas
                            </span>
                            @break
                        @case('belum_bayar')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-800">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                Belum Bayar
                            </span>
                            @break
                        @case('terlambat')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                                <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                Terlambat
                            </span>
                            @break
                        @case('menunggu_konfirmasi')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-200 border border-orange-200 dark:border-orange-800">
                                <div class="w-2 h-2 bg-orange-500 rounded-full mr-2"></div>
                                Menunggu Konfirmasi
                            </span>
                            @break
                        @case('ditolak')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-200 border border-purple-200 dark:border-purple-800">
                                <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                                Ditolak
                            </span>
                            @break
                    @endswitch
                    
                    @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
                    <a href="{{ route('kas.edit', $kas) }}" 
                       class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                        <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                        Edit
                    </a>
                    @endif
                    
                    <a href="{{ route('kas.index') }}" 
                       class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Kas Information -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                            <i data-lucide="info" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Kas</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Detail lengkap kas warga</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                        <i data-lucide="banknote" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Jumlah</span>
                                </div>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($kas->jumlah, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                        <i data-lucide="calendar-week" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Periode</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Minggu {{ $kas->minggu_ke }} / {{ $kas->tahun }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                                        <i data-lucide="clock" class="w-4 h-4 text-orange-600 dark:text-orange-400"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Jatuh Tempo</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($kas->tanggal_jatuh_tempo)->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">RT/RW</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">RT {{ $kas->rt->no_rt }} / RW {{ $kas->rt->rw->no_rw }}</span>
                            </div>
                            
                            @if($kas->tanggal_bayar)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                        <i data-lucide="calendar-check" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal Bayar</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($kas->tanggal_bayar)->format('d/m/Y H:i') }}</span>
                            </div>
                            @endif
                            
                            @if($kas->metode_bayar)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center">
                                        <i data-lucide="credit-card" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Metode Bayar</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $kas->metode_bayar }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($kas->keterangan)
                    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="file-text" class="w-3 h-3 text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-1">Keterangan</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-300">{{ $kas->keterangan }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Actions (for admin/rt/rw) -->
            @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']) && in_array($kas->status, ['belum_bayar', 'terlambat', 'menunggu_konfirmasi']))
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Konfirmasi Pembayaran</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Konfirmasi pembayaran kas warga</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('kas.bayar', $kas) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Metode Pembayaran <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="metode_pembayaran" id="metode_pembayaran" required
                                       class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-green-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                       placeholder="Contoh: Tunai, Transfer Bank, E-Wallet">
                            </div>
                            <div>
                                <label for="bukti_pembayaran" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Bukti Pembayaran (Opsional)
                                </label>
                                <input type="text" name="bukti_pembayaran" id="bukti_pembayaran"
                                       class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-green-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                       placeholder="Nomor referensi atau keterangan bukti">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                                <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                                Konfirmasi Lunas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Resident Information -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-6">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <i data-lucide="user" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Penduduk</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Detail warga</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center space-x-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr($kas->penduduk->nama_lengkap, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h6 class="font-semibold text-gray-900 dark:text-white truncate">{{ $kas->penduduk->nama_lengkap }}</h6>
                            <p class="text-sm text-gray-500 dark:text-gray-400">NIK: {{ $kas->penduduk->nik }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            @if($kas->penduduk->user)
                                <div class="w-3 h-3 bg-green-500 rounded-full" title="Punya Akun"></div>
                            @else
                                <div class="w-3 h-3 bg-gray-400 rounded-full" title="Tidak Punya Akun"></div>
                            @endif
                        </div>
                    </div>

                    @if($kas->penduduk->user && $kas->penduduk->user->email)
                    <div class="flex items-center space-x-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                            <i data-lucide="mail" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $kas->penduduk->user->email }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center space-x-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                            <i data-lucide="home" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">RT {{ $kas->rt->no_rt }} / RW {{ $kas->rt->rw->no_rw }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Alamat</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                        <i data-lucide="clock" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Timeline</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Riwayat kas</p>
                    </div>
                </div>

                <div class="payment-timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot bg-blue-500"></div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Kas Dibuat</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $kas->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($kas->tanggal_bayar)
                    <div class="timeline-item">
                        <div class="timeline-dot bg-green-500"></div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Pembayaran Dikonfirmasi</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ \Carbon\Carbon::parse($kas->tanggal_bayar)->format('d/m/Y H:i') }}</p>
                            @if($kas->metode_bayar)
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Via {{ $kas->metode_bayar }}</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($kas->updated_at != $kas->created_at && !$kas->tanggal_bayar)
                    <div class="timeline-item">
                        <div class="timeline-dot bg-yellow-500"></div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Kas Diperbarui</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $kas->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
function kasDetail() {
    return {
        init() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    }
}
</script>
@endpush
@endsection
