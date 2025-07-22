@extends('layouts.app')

@section('title', 'Buat Kas Baru')

@push('styles')
<style>
    .form-section {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.02) 0%, rgba(99, 102, 241, 0.02) 100%);
        border: 1px solid rgba(59, 130, 246, 0.08);
    }

    .dark .form-section {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.03) 0%, rgba(99, 102, 241, 0.03) 100%);
        border: 1px solid rgba(59, 130, 246, 0.12);
    }

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

    .resident-item {
        transition: all 0.2s ease;
    }

    .resident-item:hover {
        transform: translateX(4px);
        background-color: rgba(59, 130, 246, 0.05);
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

    .notification-section {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.02) 0%, rgba(5, 150, 105, 0.02) 100%);
        border: 1px solid rgba(16, 185, 129, 0.08);
    }

    .dark .notification-section {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.03) 0%, rgba(5, 150, 105, 0.03) 100%);
        border: 1px solid rgba(16, 185, 129, 0.12);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 animate-fade-in" x-data="kasCreate()" x-init="init()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-lucide="plus-circle" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                                <a href="{{ route('kas.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Daftar Kas</a>
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                <span>Buat Kas Baru</span>
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Kas Baru</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Buat tagihan kas untuk seluruh penduduk di RT yang dipilih</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('kas.index') }}" 
                           class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 text-red-800 dark:text-red-200 px-6 py-4 rounded-r-xl shadow-sm">
                <div class="flex items-center mb-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 mr-2"></i>
                    <span class="font-medium">Terdapat kesalahan pada form:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Section -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden relative">
                    <!-- Loading Overlay -->
                    <div x-show="isLoading"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-90 backdrop-blur-sm flex items-center justify-center z-20"
                         style="display: none;">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                            <span class="text-gray-600 dark:text-gray-400 font-medium">Memproses data<span class="loading-dots"></span></span>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-700">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                                <i data-lucide="edit" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Kas</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Lengkapi detail kas yang akan dibuat</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <form action="{{ route('kas.store') }}" method="POST" id="kasForm" @submit="handleSubmit">
                            @csrf
                            
                            <div class="form-section p-6 rounded-xl space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="rt_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="map-pin" class="w-4 h-4 inline mr-2"></i>
                                            RT/RW <span class="text-red-500">*</span>
                                        </label>
                                        <select name="rt_id" id="rt_id" 
                                                class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-blue-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200" 
                                                required>
                                            <option value="">Pilih RT</option>
                                            @foreach($rtList as $rt)
                                                <option value="{{ $rt->id }}" {{ old('rt_id') == $rt->id ? 'selected' : '' }}>
                                                    RT {{ $rt->no_rt }} / RW {{ $rt->rw->no_rw }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-gray-500 dark:text-gray-400 mt-1 block">Pilih RT untuk melihat informasi penduduk</small>
                                    </div>
                                    
                                    <div>
                                        <label for="jumlah" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="banknote" class="w-4 h-4 inline mr-2"></i>
                                            Jumlah Kas <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 text-sm">Rp</span>
                                            </div>
                                            <input type="number" name="jumlah" id="jumlah" 
                                                   class="w-full pl-10 pr-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-blue-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                                   value="{{ old('jumlah', 10000) }}" min="1000" step="1000" required>
                                        </div>
                                        <small class="text-gray-500 dark:text-gray-400 mt-1 block">Minimal Rp 1.000</small>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="minggu_ke" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="calendar-week" class="w-4 h-4 inline mr-2"></i>
                                            Minggu Ke <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="minggu_ke" id="minggu_ke" 
                                               class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-blue-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                               value="{{ old('minggu_ke', now()->weekOfYear) }}" min="1" max="53" required>
                                        <small class="text-gray-500 dark:text-gray-400 mt-1 block">Minggu ke-{{ now()->weekOfYear }} (saat ini)</small>
                                    </div>
                                    
                                    <div>
                                        <label for="tahun" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="calendar" class="w-4 h-4 inline mr-2"></i>
                                            Tahun <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="tahun" id="tahun" 
                                               class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-blue-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                               value="{{ old('tahun', date('Y')) }}" min="2020" max="2030" required>
                                    </div>
                                    
                                    <div>
                                        <label for="tanggal_jatuh_tempo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="clock" class="w-4 h-4 inline mr-2"></i>
                                            Tanggal Jatuh Tempo <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" 
                                               class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-blue-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                               value="{{ old('tanggal_jatuh_tempo', now()->addDays(7)->format('Y-m-d')) }}" 
                                               min="{{ now()->addDay()->format('Y-m-d') }}" required>
                                        <small class="text-gray-500 dark:text-gray-400 mt-1 block">Minimal besok</small>
                                    </div>
                                </div>

                                <div>
                                    <label for="keterangan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i data-lucide="file-text" class="w-4 h-4 inline mr-2"></i>
                                        Keterangan (Opsional)
                                    </label>
                                    <textarea name="keterangan" id="keterangan" rows="3"
                                              class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-blue-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 resize-none"
                                              placeholder="Tambahan keterangan untuk kas ini...">{{ old('keterangan') }}</textarea>
                                    <small class="text-gray-500 dark:text-gray-400 mt-1 block">Maksimal 500 karakter</small>
                                </div>
                            </div>

                            <!-- Notification Settings Section -->
                            <div class="notification-section p-6 rounded-xl space-y-4 mt-6">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                                        <i data-lucide="bell" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pengaturan Notifikasi</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Pilih jenis notifikasi yang akan dikirim</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <!-- System Notification -->
                                    <div class="flex items-start space-x-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <input type="checkbox" name="send_notification" id="send_notification" 
                                               value="1" {{ old('send_notification', true) ? 'checked' : '' }} 
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 mt-1">
                                        <div class="flex-1">
                                            <label for="send_notification" class="text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                                <i data-lucide="smartphone" class="w-4 h-4 inline mr-2"></i>
                                                Notifikasi Sistem
                                            </label>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                Kirim notifikasi dalam aplikasi ke warga yang memiliki akun
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Email Notification -->
                                    <div class="flex items-start space-x-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                        <input type="checkbox" name="send_email_notification" id="send_email_notification" 
                                               value="1" {{ old('send_email_notification', true) ? 'checked' : '' }} 
                                               class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 mt-1">
                                        <div class="flex-1">
                                            <label for="send_email_notification" class="text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                                <i data-lucide="mail" class="w-4 h-4 inline mr-2"></i>
                                                Notifikasi Email
                                            </label>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                Kirim email ke alamat email warga yang terdaftar
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email Count Info -->
                                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4" id="emailCountInfo" style="display: none;">
                                    <div class="flex items-start">
                                        <i data-lucide="info" class="w-5 h-5 text-amber-600 dark:text-amber-400 mr-2 mt-0.5 flex-shrink-0"></i>
                                        <div class="text-sm text-amber-800 dark:text-amber-200">
                                            <strong>Info Email:</strong> Email akan dikirim ke <span id="emailCount" class="font-bold">0</span> warga yang memiliki alamat email terdaftar.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end space-x-3">
                                <a href="{{ route('kas.index') }}" 
                                   class="inline-flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                    Batal
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl"
                                        :disabled="isLoading" id="submitBtn">
                                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                    <span x-text="isLoading ? 'Memproses...' : 'Buat Kas'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Resident Info Panel -->
            <div class="lg:col-span-1">
                <!-- Default State -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 sticky top-6" 
                     id="defaultPanel">
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="users" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Pilih RT/RW</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Pilih RT/RW terlebih dahulu untuk melihat informasi penduduk</p>
                    </div>
                </div>

                <!-- Loading Panel -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 sticky top-6" 
                     id="loadingPanel" style="display: none;">
                    <div class="flex flex-col items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
                        <span class="text-gray-600 dark:text-gray-400 font-medium">Memuat informasi penduduk...</span>
                    </div>
                </div>

                <!-- Resident Info Panel -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 sticky top-6" 
                     id="residentInfoPanel" style="display: none;">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                            <i data-lucide="users" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Penduduk</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400" id="rtInfo">-</p>
                        </div>
                    </div>

                    <!-- Statistics Grid -->
                    <div class="grid grid-cols-3 gap-4 mb-6" id="statsGrid">
                        <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-800">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="totalResidents">0</div>
                            <div class="text-xs text-blue-600 dark:text-blue-400 font-medium uppercase tracking-wide mt-1">Total</div>
                        </div>
                        <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl border border-green-200 dark:border-green-800">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="activeResidents">0</div>
                            <div class="text-xs text-green-600 dark:text-green-400 font-medium uppercase tracking-wide mt-1">Aktif</div>
                        </div>
                        <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl border border-purple-200 dark:border-purple-800">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="withAccounts">0</div>
                            <div class="text-xs text-purple-600 dark:text-purple-400 font-medium uppercase tracking-wide mt-1">Akun</div>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6">
                        <div class="flex items-start">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0"></i>
                            <div class="text-sm text-blue-800 dark:text-blue-200">
                                <strong>Info:</strong> Kas akan dibuat untuk semua penduduk aktif di RT ini. Total kas yang akan dibuat: <span id="totalKasAmount" class="font-bold">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Resident List -->
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            <i data-lucide="list" class="w-4 h-4 mr-2"></i>
                            Daftar Penduduk Aktif
                        </h4>
                        <div class="max-h-64 overflow-y-auto space-y-2" id="residentList">
                            <!-- Resident items will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Error Panel -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 sticky top-6" 
                     id="errorPanel" style="display: none;">
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Terjadi Kesalahan</h3>
                        <p class="text-red-600 dark:text-red-400 text-sm mb-4" id="errorMessage">Gagal memuat data penduduk</p>
                        <button @click="loadResidentInfo()" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                            Coba Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function kasCreate() {
    return {
        isLoading: false,

        init() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Setup event listener for RT selection
            $('#rt_id').on('change', () => {
                this.loadResidentInfo();
            });

            // Setup event listener for amount change to update total
            $('#jumlah').on('input', () => {
                this.updateTotalKasAmount();
            });

            // Load resident info if RT is already selected
            const selectedRt = $('#rt_id').val();
            if (selectedRt) {
                this.loadResidentInfo();
            }
        },

        loadResidentInfo() {
            const rtId = $('#rt_id').val();
            
            if (!rtId) {
                this.showDefaultPanel();
                return;
            }

            // Show loading panel
            this.showLoadingPanel();

            // Make AJAX request - using the new route
            $.ajax({
                url: '{{ route("kas.ajax.get-resident-info") }}',
                method: 'GET',
                data: { rt_id: rtId },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    if (response.success) {
                        this.displayResidentInfo(response);
                    } else {
                        this.showError(response.message || 'Gagal memuat informasi penduduk');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                    
                    let errorMessage = 'Terjadi kesalahan saat memuat data penduduk';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 404) {
                        errorMessage = 'Route tidak ditemukan. Pastikan route kas.ajax.get-resident-info sudah didefinisikan.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Terjadi kesalahan server. Periksa log aplikasi.';
                    }
                    
                    this.showError(errorMessage);
                }
            });
        },

        displayResidentInfo(data) {
            // Update RT info
            $('#rtInfo').text(data.rt_info);
            
            // Update statistics
            $('#totalResidents').text(data.stats.total);
            $('#activeResidents').text(data.stats.active);
            $('#withAccounts').text(data.stats.with_accounts);
            
            // Update total kas amount
            this.updateTotalKasAmount(data.stats.active);
            
            // Update email count info
            this.updateEmailCountInfo(data.residents);
            
            // Update resident list
            const residentList = $('#residentList');
            residentList.empty();
            
            if (data.residents && data.residents.length > 0) {
                data.residents.forEach(resident => {
                    const hasAccount = resident.user !== null;
                    const hasEmail = hasAccount && resident.user.email;
                    const avatar = resident.nama_lengkap.charAt(0).toUpperCase();
                    
                    const residentItem = $(`
                        <div class="resident-item flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                ${avatar}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h6 class="font-medium text-gray-900 dark:text-white text-sm truncate">${resident.nama_lengkap}</h6>
                                <small class="text-gray-500 dark:text-gray-400 text-xs">NIK: ${resident.nik}</small>
                            </div>
                            <div class="flex items-center space-x-1">
                                ${hasAccount ? 
                                    '<div class="w-2 h-2 bg-green-500 rounded-full" title="Punya Akun"></div>' : 
                                    '<div class="w-2 h-2 bg-gray-400 rounded-full" title="Tidak Punya Akun"></div>'
                                }
                                ${hasEmail ? 
                                    '<i data-lucide="mail" class="w-3 h-3 text-green-500" title="Punya Email"></i>' : 
                                    '<i data-lucide="mail-x" class="w-3 h-3 text-gray-400" title="Tidak Punya Email"></i>'
                                }
                            </div>
                        </div>
                    `);
                    residentList.append(residentItem);
                });
            } else {
                residentList.html(`
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        <i data-lucide="user-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                        <p class="text-sm font-medium">Tidak ada penduduk aktif</p>
                        <p class="text-xs">Pastikan ada penduduk aktif dengan status 'aktif' di RT ini</p>
                    </div>
                `);
            }
            
            // Show resident info panel
            this.showResidentPanel();
            
            // Re-initialize icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        },

        updateTotalKasAmount(activeCount = null) {
            if (activeCount === null) {
                activeCount = parseInt($('#activeResidents').text()) || 0;
            }
            const amount = parseInt($('#jumlah').val()) || 0;
            const total = activeCount * amount;
            $('#totalKasAmount').text('Rp ' + total.toLocaleString('id-ID'));
        },

        updateEmailCountInfo(residents) {
            if (!residents) return;
            
            const emailCount = residents.filter(resident => 
                resident.user && resident.user.email
            ).length;
            
            $('#emailCount').text(emailCount);
            
            if (emailCount > 0) {
                $('#emailCountInfo').show();
            } else {
                $('#emailCountInfo').hide();
            }
        },

        showDefaultPanel() {
            $('#defaultPanel').show();
            $('#loadingPanel').hide();
            $('#residentInfoPanel').hide();
            $('#errorPanel').hide();
            $('#emailCountInfo').hide();
        },

        showLoadingPanel() {
            $('#defaultPanel').hide();
            $('#loadingPanel').show();
            $('#residentInfoPanel').hide();
            $('#errorPanel').hide();
            $('#emailCountInfo').hide();
        },

        showResidentPanel() {
            $('#defaultPanel').hide();
            $('#loadingPanel').hide();
            $('#residentInfoPanel').show();
            $('#errorPanel').hide();
        },

        showError(message) {
            $('#errorMessage').text(message);
            $('#defaultPanel').hide();
            $('#loadingPanel').hide();
            $('#residentInfoPanel').hide();
            $('#errorPanel').show();
            $('#emailCountInfo').hide();
        },

        handleSubmit(event) {
            const rtId = $('#rt_id').val();
            const jumlah = $('#jumlah').val();
            const mingguKe = $('#minggu_ke').val();
            const tahun = $('#tahun').val();
            const tanggalJatuhTempo = $('#tanggal_jatuh_tempo').val();

            if (!rtId) {
                event.preventDefault();
                alert('Silakan pilih RT terlebih dahulu');
                return false;
            }

            if (!jumlah || jumlah < 1000) {
                event.preventDefault();
                alert('Jumlah kas minimal Rp 1.000');
                return false;
            }

            if (!mingguKe || mingguKe < 1 || mingguKe > 53) {
                event.preventDefault();
                alert('Minggu harus antara 1-53');
                return false;
            }

            if (!tahun || tahun < 2020 || tahun > 2030) {
                event.preventDefault();
                alert('Tahun harus antara 2020-2030');
                return false;
            }

            if (!tanggalJatuhTempo) {
                event.preventDefault();
                alert('Silakan pilih tanggal jatuh tempo');
                return false;
            }

            // Show loading state
            this.isLoading = true;
        }
    }
}
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
