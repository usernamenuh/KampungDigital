@extends('layouts.app')

@section('title', 'Edit Kas')

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

    .form-section {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.02) 0%, rgba(251, 146, 60, 0.02) 100%);
        border: 1px solid rgba(245, 158, 11, 0.08);
    }

    .dark .form-section {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.03) 0%, rgba(251, 146, 60, 0.03) 100%);
        border: 1px solid rgba(245, 158, 11, 0.12);
    }

    .info-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.9) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .dark .info-card {
        background: linear-gradient(135deg, rgba(31, 41, 55, 0.9) 0%, rgba(17, 24, 39, 0.9) 100%);
        border: 1px solid rgba(75, 85, 99, 0.2);
    }

    .status-card {
        transition: all 0.3s ease;
    }

    .status-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .mobile-text-sm { font-size: 0.75rem; }
        .mobile-text-xs { font-size: 0.6875rem; }
        .mobile-p-3 { padding: 0.75rem; }
        .mobile-p-4 { padding: 1rem; }
        .mobile-gap-3 { gap: 0.75rem; }
        .mobile-space-y-3 > * + * { margin-top: 0.75rem; }
    }

    .loading-dots::after {
        content: '';
        animation: dots 1.5s steps(5, end) infinite;
    }

    @keyframes dots {
        0%, 20% { content: ''; }
        40% { content: '.'; }
        60% { content: '..'; }
        80%, 100% { content: '...'; }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4 md:py-6 animate-fade-in">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <!-- Header -->
        <div class="mb-6 md:mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 md:gap-6">
                    <div class="flex items-center space-x-3 md:space-x-4">
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg md:rounded-xl flex items-center justify-center shadow-lg">
                            <i data-lucide="edit" class="w-5 h-5 md:w-6 md:h-6 text-white"></i>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2 text-xs md:text-sm text-gray-500 dark:text-gray-400 mb-1">
                                <a href="{{ route('kas.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Daftar Kas</a>
                                <i data-lucide="chevron-right" class="w-3 h-3 md:w-4 md:h-4"></i>
                                <span>Edit Kas</span>
                            </div>
                            <h1 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white">Edit Kas Minggu ke-{{ $kas->minggu_ke }}</h1>
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
                        <a href="{{ route('kas.show', $kas) }}" 
                           class="inline-flex items-center px-3 md:px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg md:rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md text-xs md:text-sm">
                            <i data-lucide="eye" class="w-3 h-3 md:w-4 md:h-4 mr-1 md:mr-2"></i>
                            Lihat Detail
                        </a>
                        <a href="{{ route('kas.index') }}" 
                           class="inline-flex items-center px-3 md:px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg md:rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md text-xs md:text-sm">
                            <i data-lucide="arrow-left" class="w-3 h-3 md:w-4 md:h-4 mr-1 md:mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="mb-4 md:mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 text-red-800 dark:text-red-200 px-4 md:px-6 py-3 md:py-4 rounded-r-lg md:rounded-r-xl shadow-sm">
                <div class="flex items-center mb-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 md:w-5 md:h-5 mr-2"></i>
                    <span class="font-medium text-sm md:text-base">Terdapat kesalahan pada form:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs md:text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
            <!-- Form Section -->
            <div class="xl:col-span-2">
                <div class="info-card rounded-xl md:rounded-2xl shadow-xl border overflow-hidden relative status-card">
                    <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-700">
                        <div class="flex items-center space-x-2 md:space-x-3">
                            <div class="w-6 h-6 md:w-8 md:h-8 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-md md:rounded-lg flex items-center justify-center">
                                <i data-lucide="info" class="w-3 h-3 md:w-4 md:h-4 text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-base md:text-lg font-bold text-gray-900 dark:text-white">Informasi Kas</h2>
                                <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400">Perbarui detail kas</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 md:p-6 lg:p-8">
                        <form action="{{ route('kas.update', $kas) }}" method="POST" id="kasForm">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-section p-4 md:p-6 rounded-lg md:rounded-xl space-y-4 md:space-y-6">
                                <!-- Penduduk Selection -->
                                <div>
                                    <label for="penduduk_id" class="block text-sm md:text-base font-semibold text-gray-700 dark:text-gray-300 mb-2 md:mb-3">
                                        <i data-lucide="user" class="w-3 h-3 md:w-4 md:h-4 inline mr-2"></i>
                                        Penduduk <span class="text-red-500">*</span>
                                    </label>
                                    <select class="w-full px-3 md:px-4 py-2 md:py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-lg md:rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 text-sm md:text-base @error('penduduk_id') ring-red-500 @enderror" 
                                            id="penduduk_id" name="penduduk_id" required>
                                        <option value="">Pilih Penduduk</option>
                                        @if(isset($penduduk) && $penduduk->count() > 0)
                                            @foreach($penduduk as $p)
                                                <option value="{{ $p->id }}" 
                                                        data-nik="{{ $p->nik }}"
                                                        data-rt="{{ $p->kk && $p->kk->rt ? $p->kk->rt->id : '' }}"
                                                        {{ old('penduduk_id', $kas->penduduk_id) == $p->id ? 'selected' : '' }}>
                                                    {{ $p->nama_lengkap }} ({{ $p->nik }})
                                                    @if($p->kk && $p->kk->rt)
                                                        - RT {{ $p->kk->rt->no_rt }}
                                                        @if($p->kk->rt->rw)
                                                            / RW {{ $p->kk->rt->rw->no_rw }}
                                                        @endif
                                                    @endif
                                                </option>
                                            @endforeach
                                        @else
                                            <!-- Fallback: show current penduduk if no penduduk list available -->
                                            @if($kas->penduduk)
                                                <option value="{{ $kas->penduduk->id }}" selected>
                                                    {{ $kas->penduduk->nama_lengkap }} ({{ $kas->penduduk->nik }})
                                                </option>
                                            @endif
                                        @endif
                                    </select>
                                    @error('penduduk_id')
                                        <p class="mt-1 text-xs md:text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <div class="form-text text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-1">Silakan pilih penduduk yang valid atau hubungi administrator.</div>
                                </div>

                                <!-- RT Selection -->
                                <div>
                                    <label for="rt_id" class="block text-sm md:text-base font-semibold text-gray-700 dark:text-gray-300 mb-2 md:mb-3">
                                        <i data-lucide="map-pin" class="w-3 h-3 md:w-4 md:h-4 inline mr-2"></i>
                                        RT <span class="text-red-500">*</span>
                                    </label>
                                    <select class="w-full px-3 md:px-4 py-2 md:py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-lg md:rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 text-sm md:text-base @error('rt_id') ring-red-500 @enderror" 
                                            id="rt_id" name="rt_id" required>
                                        <option value="">Pilih RT</option>
                                        @if(isset($rt) && $rt->count() > 0)
                                            @foreach($rt as $r)
                                                <option value="{{ $r->id }}" 
                                                        {{ old('rt_id', $kas->rt_id) == $r->id ? 'selected' : '' }}>
                                                    RT {{ $r->no_rt }}
                                                    @if($r->rw)
                                                        / RW {{ $r->rw->no_rw }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        @else
                                            <!-- Fallback: show current RT if no RT list available -->
                                            @if($kas->rt)
                                                <option value="{{ $kas->rt->id }}" selected>
                                                    RT {{ $kas->rt->no_rt }}
                                                    @if($kas->rt->rw)
                                                        / RW {{ $kas->rt->rw->no_rw }}
                                                    @endif
                                                </option>
                                            @endif
                                        @endif
                                    </select>
                                    @error('rt_id')
                                        <p class="mt-1 text-xs md:text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                    <div>
                                        <label for="jumlah" class="block text-sm md:text-base font-semibold text-gray-700 dark:text-gray-300 mb-2 md:mb-3">
                                            <i data-lucide="banknote" class="w-3 h-3 md:w-4 md:h-4 inline mr-2"></i>
                                            Jumlah <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 text-xs md:text-sm">Rp</span>
                                            </div>
                                            <input type="number" name="jumlah" id="jumlah" 
                                                   class="w-full pl-8 md:pl-10 pr-3 md:pr-4 py-2 md:py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-lg md:rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 text-sm md:text-base @error('jumlah') ring-red-500 @enderror"
                                                   value="{{ old('jumlah', (int)$kas->jumlah) }}" min="1000" step="1000" required>
                                        </div>
                                        @error('jumlah')
                                            <p class="mt-1 text-xs md:text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="status" class="block text-sm md:text-base font-semibold text-gray-700 dark:text-gray-300 mb-2 md:mb-3">
                                            <i data-lucide="flag" class="w-3 h-3 md:w-4 md:h-4 inline mr-2"></i>
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <select class="w-full px-3 md:px-4 py-2 md:py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-lg md:rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 text-sm md:text-base @error('status') ring-red-500 @enderror" 
                                                id="status" name="status" required>
                                            <option value="belum_bayar" {{ old('status', $kas->status) === 'belum_bayar' ? 'selected' : '' }}>
                                                Belum Bayar
                                            </option>
                                            <option value="lunas" {{ old('status', $kas->status) === 'lunas' ? 'selected' : '' }}>
                                                Lunas
                                            </option>
                                            <option value="terlambat" {{ old('status', $kas->status) === 'terlambat' ? 'selected' : '' }}>
                                                Terlambat
                                            </option>
                                            <option value="ditolak" {{ old('status', $kas->status) === 'ditolak' ? 'selected' : '' }}>
                                                Ditolak
                                            </option>
                                            <option value="menunggu_konfirmasi" {{ old('status', $kas->status) === 'menunggu_konfirmasi' ? 'selected' : '' }}>
                                                Menunggu Konfirmasi
                                            </option>
                                        </select>
                                        @error('status')
                                            <p class="mt-1 text-xs md:text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                                    <div>
                                        <label for="minggu_ke" class="block text-sm md:text-base font-semibold text-gray-700 dark:text-gray-300 mb-2 md:mb-3">
                                            <i data-lucide="calendar-week" class="w-3 h-3 md:w-4 md:h-4 inline mr-2"></i>
                                            Minggu Ke <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="minggu_ke" id="minggu_ke" 
                                               class="w-full px-3 md:px-4 py-2 md:py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-lg md:rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 text-sm md:text-base @error('minggu_ke') ring-red-500 @enderror"
                                               value="{{ old('minggu_ke', $kas->minggu_ke) }}" min="1" max="53" required>
                                        @error('minggu_ke')
                                            <p class="mt-1 text-xs md:text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="tahun" class="block text-sm md:text-base font-semibold text-gray-700 dark:text-gray-300 mb-2 md:mb-3">
                                            <i data-lucide="calendar" class="w-3 h-3 md:w-4 md:h-4 inline mr-2"></i>
                                            Tahun <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="tahun" id="tahun" 
                                               class="w-full px-3 md:px-4 py-2 md:py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-lg md:rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 text-sm md:text-base @error('tahun') ring-red-500 @enderror"
                                               value="{{ old('tahun', $kas->tahun) }}" min="2020" max="2030" required>
                                        @error('tahun')
                                            <p class="mt-1 text-xs md:text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="tanggal_jatuh_tempo" class="block text-sm md:text-base font-semibold text-gray-700 dark:text-gray-300 mb-2 md:mb-3">
                                            <i data-lucide="calendar-times" class="w-3 h-3 md:w-4 md:h-4 inline mr-2"></i>
                                            Jatuh Tempo <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" 
                                               class="w-full px-3 md:px-4 py-2 md:py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-lg md:rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 text-sm md:text-base @error('tanggal_jatuh_tempo') ring-red-500 @enderror"
                                               value="{{ old('tanggal_jatuh_tempo', $kas->tanggal_jatuh_tempo ? $kas->tanggal_jatuh_tempo->format('Y-m-d') : '') }}" required>
                                        @error('tanggal_jatuh_tempo')
                                            <p class="mt-1 text-xs md:text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Payment Details (Show when status is lunas) -->
                                <div id="paymentDetails" style="display: {{ old('status', $kas->status) === 'lunas' ? 'block' : 'none' }};">
                                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg md:rounded-xl p-4 md:p-6">
                                        <h6 class="mb-3 md:mb-4 text-sm md:text-base font-semibold text-blue-800 dark:text-blue-200">
                                            <i data-lucide="credit-card" class="w-3 h-3 md:w-4 md:h-4 inline mr-2"></i>
                                            Detail Pembayaran
                                        </h6>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                            <div>
                                                <label for="tanggal_bayar" class="block text-sm md:text-base font-semibold text-gray-700 dark:text-gray-300 mb-2 md:mb-3">
                                                    <i data-lucide="calendar-check" class="w-3 h-3 md:w-4 md:h-4 inline mr-2"></i>
                                                    Tanggal Bayar
                                                </label>
                                                <input type="datetime-local" name="tanggal_bayar" id="tanggal_bayar" 
                                                       class="w-full px-3 md:px-4 py-2 md:py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-lg md:rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 text-sm md:text-base @error('tanggal_bayar') ring-red-500 @enderror"
                                                       value="{{ old('tanggal_bayar', $kas->tanggal_bayar ? $kas->tanggal_bayar->format('Y-m-d\TH:i') : '') }}">
                                                @error('tanggal_bayar')
                                                    <p class="mt-1 text-xs md:text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="metode_bayar" class="block text-sm md:text-base font-semibold text-gray-700 dark:text-gray-300 mb-2 md:mb-3">
                                                    <i data-lucide="credit-card" class="w-3 h-3 md:w-4 md:h-4 inline mr-2"></i>
                                                    Metode Bayar
                                                </label>
                                                <select class="w-full px-3 md:px-4 py-2 md:py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-lg md:rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 text-sm md:text-base @error('metode_bayar') ring-red-500 @enderror" 
                                                        id="metode_bayar" name="metode_bayar">
                                                    <option value="">Pilih Metode Bayar</option>
                                                    
                                                    <!-- Cash -->
                                                    <option value="tunai" {{ old('metode_bayar', $kas->metode_bayar) === 'tunai' ? 'selected' : '' }}>Tunai</option>
                                                    
                                                    <!-- E-Wallets -->
                                                    <optgroup label="E-Wallet">
                                                        <option value="dana" {{ old('metode_bayar', $kas->metode_bayar) === 'dana' ? 'selected' : '' }}>DANA</option>
                                                        <option value="ovo" {{ old('metode_bayar', $kas->metode_bayar) === 'ovo' ? 'selected' : '' }}>OVO</option>
                                                        <option value="gopay" {{ old('metode_bayar', $kas->metode_bayar) === 'gopay' ? 'selected' : '' }}>GoPay</option>
                                                        <option value="shopeepay" {{ old('metode_bayar', $kas->metode_bayar) === 'shopeepay' ? 'selected' : '' }}>ShopeePay</option>
                                                    </optgroup>
                                                    
                                                    <!-- Banks -->
                                                    <optgroup label="Bank Transfer">
                                                        <option value="bca" {{ old('metode_bayar', $kas->metode_bayar) === 'bca' ? 'selected' : '' }}>BCA</option>
                                                        <option value="bni" {{ old('metode_bayar', $kas->metode_bayar) === 'bni' ? 'selected' : '' }}>BNI</option>
                                                        <option value="bri" {{ old('metode_bayar', $kas->metode_bayar) === 'bri' ? 'selected' : '' }}>BRI</option>
                                                        <option value="mandiri" {{ old('metode_bayar', $kas->metode_bayar) === 'mandiri' ? 'selected' : '' }}>Mandiri</option>
                                                        <option value="bsi" {{ old('metode_bayar', $kas->metode_bayar) === 'bsi' ? 'selected' : '' }}>BSI</option>
                                                        <option value="cimb" {{ old('metode_bayar', $kas->metode_bayar) === 'cimb' ? 'selected' : '' }}>CIMB Niaga</option>
                                                        <option value="danamon" {{ old('metode_bayar', $kas->metode_bayar) === 'danamon' ? 'selected' : '' }}>Danamon</option>
                                                        <option value="permata" {{ old('metode_bayar', $kas->metode_bayar) === 'permata' ? 'selected' : '' }}>Permata</option>
                                                        <option value="mega" {{ old('metode_bayar', $kas->metode_bayar) === 'mega' ? 'selected' : '' }}>Mega</option>
                                                        <option value="btn" {{ old('metode_bayar', $kas->metode_bayar) === 'btn' ? 'selected' : '' }}>BTN</option>
                                                        <option value="panin" {{ old('metode_bayar', $kas->metode_bayar) === 'panin' ? 'selected' : '' }}>Panin</option>
                                                        <option value="maybank" {{ old('metode_bayar', $kas->metode_bayar) === 'maybank' ? 'selected' : '' }}>Maybank</option>
                                                        <option value="btpn" {{ old('metode_bayar', $kas->metode_bayar) === 'btpn' ? 'selected' : '' }}>BTPN</option>
                                                        <option value="commonwealth" {{ old('metode_bayar', $kas->metode_bayar) === 'commonwealth' ? 'selected' : '' }}>Commonwealth</option>
                                                        <option value="uob" {{ old('metode_bayar', $kas->metode_bayar) === 'uob' ? 'selected' : '' }}>UOB</option>
                                                        <option value="sinarmas" {{ old('metode_bayar', $kas->metode_bayar) === 'sinarmas' ? 'selected' : '' }}>Sinarmas</option>
                                                        <option value="bukopin" {{ old('metode_bayar', $kas->metode_bayar) === 'bukopin' ? 'selected' : '' }}>Bukopin</option>
                                                    </optgroup>
                                                    
                                                    <!-- Digital Banks -->
                                                    <optgroup label="Digital Bank">
                                                        <option value="jago" {{ old('metode_bayar', $kas->metode_bayar) === 'jago' ? 'selected' : '' }}>Jago</option>
                                                        <option value="seabank" {{ old('metode_bayar', $kas->metode_bayar) === 'seabank' ? 'selected' : '' }}>SeaBank</option>
                                                        <option value="neo_commerce" {{ old('metode_bayar', $kas->metode_bayar) === 'neo_commerce' ? 'selected' : '' }}>Neo Commerce</option>
                                                        <option value="allo_bank" {{ old('metode_bayar', $kas->metode_bayar) === 'allo_bank' ? 'selected' : '' }}>Allo Bank</option>
                                                    </optgroup>
                                                    
                                                    <!-- QR Code -->
                                                    <option value="qr_code" {{ old('metode_bayar', $kas->metode_bayar) === 'qr_code' ? 'selected' : '' }}>QR Code</option>
                                                </select>
                                                @error('metode_bayar')
                                                    <p class="mt-1 text-xs md:text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bukti Bayar -->
                                <div>
                                    <label for="bukti_bayar" class="block text-sm md:text-base font-semibold text-gray-700 dark:text-gray-300 mb-2 md:mb-3">
                                        <i data-lucide="receipt" class="w-3 h-3 md:w-4 md:h-4 inline mr-2"></i>
                                        Bukti Bayar / Catatan
                                    </label>
                                    <textarea class="w-full px-3 md:px-4 py-2 md:py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-lg md:rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 resize-none text-sm md:text-base @error('bukti_bayar') ring-red-500 @enderror" 
                                              id="bukti_bayar" name="bukti_bayar" rows="3" 
                                              placeholder="Masukkan URL bukti bayar atau catatan tambahan">{{ old('bukti_bayar', $kas->bukti_bayar) }}</textarea>
                                    @error('bukti_bayar')
                                        <p class="mt-1 text-xs md:text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-1">Opsional: URL gambar bukti bayar atau catatan tambahan</p>
                                </div>

                                <!-- Keterangan -->
                                <div>
                                    <label for="keterangan" class="block text-sm md:text-base font-semibold text-gray-700 dark:text-gray-300 mb-2 md:mb-3">
                                        <i data-lucide="sticky-note" class="w-3 h-3 md:w-4 md:h-4 inline mr-2"></i>
                                        Keterangan
                                    </label>
                                    <textarea class="w-full px-3 md:px-4 py-2 md:py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-lg md:rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 resize-none text-sm md:text-base @error('keterangan') ring-red-500 @enderror" 
                                              id="keterangan" name="keterangan" rows="3" 
                                              placeholder="Masukkan keterangan tambahan (opsional)">{{ old('keterangan', $kas->keterangan) }}</textarea>
                                    @error('keterangan')
                                        <p class="mt-1 text-xs md:text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-6 md:mt-8 flex flex-col sm:flex-row justify-between gap-3 md:gap-4">
                                <div>
                                    <a href="{{ route('kas.index') }}" class="inline-flex items-center px-4 md:px-6 py-2 md:py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg md:rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md text-sm md:text-base w-full sm:w-auto justify-center">
                                        <i data-lucide="x" class="w-3 h-3 md:w-4 md:h-4 mr-1 md:mr-2"></i>
                                        Batal
                                    </a>
                                </div>
                                <div>
                                    <button type="submit" class="inline-flex items-center px-4 md:px-6 py-2 md:py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-medium rounded-lg md:rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl text-sm md:text-base w-full sm:w-auto justify-center">
                                        <i data-lucide="save" class="w-3 h-3 md:w-4 md:h-4 mr-1 md:mr-2"></i>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="xl:col-span-1">
                <!-- Current Kas Info -->
                <div class="info-card rounded-xl md:rounded-2xl shadow-xl border p-4 md:p-6 mb-4 md:mb-6 status-card">
                    <div class="flex items-center space-x-2 md:space-x-3 mb-3 md:mb-4">
                        <div class="w-6 h-6 md:w-8 md:h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-md md:rounded-lg flex items-center justify-center">
                            <i data-lucide="info" class="w-3 h-3 md:w-4 md:h-4 text-white"></i>
                        </div>
                        <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white">Informasi Saat Ini</h3>
                    </div>
                    
                    <div class="space-y-3 md:space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 md:p-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center text-white font-semibold text-sm md:text-base">
                                    @if($kas->penduduk)
                                        {{ substr($kas->penduduk->nama_lengkap, 0, 1) }}
                                    @else
                                        ?
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h6 class="font-medium text-gray-900 dark:text-white text-sm md:text-base truncate">
                                        @if($kas->penduduk)
                                            {{ $kas->penduduk->nama_lengkap }}
                                        @else
                                            <span class="text-red-500">Data tidak tersedia</span>
                                        @endif
                                    </h6>
                                    <small class="text-gray-500 dark:text-gray-400 text-xs md:text-sm">
                                        NIK: 
                                        @if($kas->penduduk)
                                            {{ $kas->penduduk->nik }}
                                        @else
                                            <span class="text-red-500">-</span>
                                        @endif
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($kas->penduduk && $kas->penduduk->user)
                                        <div class="w-2 h-2 bg-green-500 rounded-full" title="Punya Akun"></div>
                                    @else
                                        <div class="w-2 h-2 bg-gray-400 rounded-full" title="Tidak Punya Akun"></div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 md:gap-4">
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                                <div class="text-xs md:text-sm text-yellow-700 dark:text-yellow-300 font-medium">Periode</div>
                                <div class="text-sm md:text-base font-bold text-yellow-800 dark:text-yellow-200">
                                    Minggu {{ $kas->minggu_ke }}
                                </div>
                                <div class="text-xs text-yellow-600 dark:text-yellow-400">{{ $kas->tahun }}</div>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                                <div class="text-xs md:text-sm text-green-700 dark:text-green-300 font-medium">Jumlah</div>
                                <div class="text-sm md:text-base font-bold text-green-800 dark:text-green-200">
                                    Rp {{ number_format((int)$kas->jumlah, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 md:p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs md:text-sm text-gray-600 dark:text-gray-400 font-medium">Status Saat Ini</span>
                            </div>
                            @switch($kas->status)
                                @case('lunas')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs md:text-sm font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                        Lunas
                                    </span>
                                    @break
                                @case('belum_bayar')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs md:text-sm font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-800">
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                        Belum Bayar
                                    </span>
                                    @break
                                @case('terlambat')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs md:text-sm font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                                        <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                        Terlambat
                                    </span>
                                    @break
                                @case('menunggu_konfirmasi')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs md:text-sm font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 border border-blue-200 dark:border-blue-800">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                        Menunggu Konfirmasi
                                    </span>
                                    @break
                                @case('ditolak')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs md:text-sm font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                                        <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                        Ditolak
                                    </span>
                                    @break
                            @endswitch
                        </div>

                        @if($kas->rt)
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                <div class="text-xs md:text-sm text-blue-700 dark:text-blue-300 font-medium">RT/RW</div>
                                <div class="text-sm md:text-base font-bold text-blue-800 dark:text-blue-200">
                                    RT {{ $kas->rt->no_rt ?? '-' }}
                                    @if($kas->rt->rw)
                                        / RW {{ $kas->rt->rw->no_rw ?? '-' }}
                                    @endif
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
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    const statusSelect = document.getElementById('status');
    const paymentDetails = document.getElementById('paymentDetails');
    const pendudukSelect = document.getElementById('penduduk_id');
    const rtSelect = document.getElementById('rt_id');

    // Toggle payment details based on status
    function togglePaymentDetails() {
        if (statusSelect.value === 'lunas') {
            paymentDetails.style.display = 'block';
            // Set current datetime if tanggal_bayar is empty
            const tanggalBayar = document.getElementById('tanggal_bayar');
            if (!tanggalBayar.value) {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                tanggalBayar.value = `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        } else {
            paymentDetails.style.display = 'none';
        }
    }

    // Auto-select RT when penduduk is selected
    function autoSelectRt() {
        const selectedOption = pendudukSelect.options[pendudukSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.rt) {
            rtSelect.value = selectedOption.dataset.rt;
        }
    }

    // Event listeners
    statusSelect.addEventListener('change', togglePaymentDetails);
    pendudukSelect.addEventListener('change', autoSelectRt);

    // Initialize on page load
    togglePaymentDetails();

    // Form validation
    document.getElementById('kasForm').addEventListener('submit', function(e) {
        const status = statusSelect.value;
        const tanggalBayar = document.getElementById('tanggal_bayar').value;
        const metodeBayar = document.getElementById('metode_bayar').value;

        if (status === 'lunas' && (!tanggalBayar || !metodeBayar)) {
            e.preventDefault();
            alert('Untuk status lunas, tanggal bayar dan metode bayar harus diisi!');
            return false;
        }
    });

    // Format currency input
    const jumlahInput = document.getElementById('jumlah');
    jumlahInput.addEventListener('input', function() {
        // Remove non-numeric characters except for the decimal point
        let value = this.value.replace(/[^\d]/g, '');
        
        // Ensure minimum value
        if (value && parseInt(value) < 1000) {
            value = '1000';
        }
        
        this.value = value;
    });
});
</script>
@endpush
@endsection
