@extends('layouts.app')

@section('title', 'Edit Kas - ' . $kas->penduduk->nama_lengkap)

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
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 animate-fade-in" x-data="kasEdit()" x-init="init()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-lucide="edit" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                                <a href="{{ route('kas.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Daftar Kas</a>
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                <span>Edit Kas</span>
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Kas Minggu ke-{{ $kas->minggu_ke }}</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $kas->penduduk->nama_lengkap }} - {{ $kas->tahun }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('kas.show', $kas) }}" 
                           class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                            Lihat Detail
                        </a>
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
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-yellow-500"></div>
                            <span class="text-gray-600 dark:text-gray-400 font-medium">Memproses perubahan<span class="loading-dots"></span></span>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-700">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                                <i data-lucide="info" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Kas</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Perbarui detail kas</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <form action="{{ route('kas.update', $kas) }}" method="POST" id="kasForm" @submit="handleSubmit">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-section p-6 rounded-xl space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                                   class="w-full pl-10 pr-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                                   value="{{ old('jumlah', $kas->jumlah) }}" min="0" step="1000" required>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <select name="status" id="status" 
                                                class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200" 
                                                required>
                                            <option value="belum_bayar" {{ old('status', $kas->status) == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                            <option value="menunggu_konfirmasi" {{ old('status', $kas->status) == 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                            <option value="lunas" {{ old('status', $kas->status) == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                            <option value="terlambat" {{ old('status', $kas->status) == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                            <option value="ditolak" {{ old('status', $kas->status) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="minggu_ke" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="calendar-week" class="w-4 h-4 inline mr-2"></i>
                                            Minggu Ke <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="minggu_ke" id="minggu_ke" 
                                               class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                               value="{{ old('minggu_ke', $kas->minggu_ke) }}" min="1" max="53" required>
                                    </div>
                                    
                                    <div>
                                        <label for="tahun" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="calendar" class="w-4 h-4 inline mr-2"></i>
                                            Tahun <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="tahun" id="tahun" 
                                               class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                               value="{{ old('tahun', $kas->tahun) }}" min="2020" max="2030" required>
                                    </div>
                                    
                                    <div>
                                        <label for="tanggal_jatuh_tempo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="clock" class="w-4 h-4 inline mr-2"></i>
                                            Tanggal Jatuh Tempo <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" 
                                               class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                               value="{{ old('tanggal_jatuh_tempo', $kas->tanggal_jatuh_tempo->format('Y-m-d')) }}" required>
                                    </div>
                                </div>

                                <!-- Payment Details (shown when status is lunas) -->
                                <div id="paymentDetails" style="display: {{ old('status', $kas->status) === 'lunas' ? 'block' : 'none' }};">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="tanggal_bayar" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                                <i data-lucide="calendar-check" class="w-4 h-4 inline mr-2"></i>
                                                Tanggal Bayar
                                            </label>
                                            <input type="datetime-local" name="tanggal_bayar" id="tanggal_bayar" 
                                                   class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                                   value="{{ old('tanggal_bayar', $kas->tanggal_bayar ? $kas->tanggal_bayar->format('Y-m-d\TH:i') : '') }}">
                                        </div>
                                        
                                        <div>
                                            <label for="metode_bayar" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                                <i data-lucide="credit-card" class="w-4 h-4 inline mr-2"></i>
                                                Metode Bayar
                                            </label>
                                            <input type="text" name="metode_bayar" id="metode_bayar" 
                                                   class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                                   value="{{ old('metode_bayar', $kas->metode_bayar) }}" placeholder="Contoh: Tunai, Transfer Bank, E-Wallet">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="keterangan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i data-lucide="file-text" class="w-4 h-4 inline mr-2"></i>
                                        Keterangan (Opsional)
                                    </label>
                                    <textarea name="keterangan" id="keterangan" rows="3"
                                              class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 resize-none"
                                              placeholder="Tambahan keterangan untuk kas ini...">{{ old('keterangan', $kas->keterangan) }}</textarea>
                                    <small class="text-gray-500 dark:text-gray-400 mt-1 block">Maksimal 500 karakter</small>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end space-x-3">
                                <a href="{{ route('kas.show', $kas) }}" 
                                   class="inline-flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                    Batal
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl"
                                        :disabled="isLoading" id="submitBtn">
                                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                    <span x-text="isLoading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Resident Info Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 sticky top-6" 
                     id="residentInfoPanel">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                            <i data-lucide="user" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Penduduk</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400" id="rtInfo">
                                RT {{ $kas->rt->no_rt ?? '-' }} / RW {{ $kas->rt->rw->no_rw ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                {{ substr($kas->penduduk->nama_lengkap, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h6 class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ $kas->penduduk->nama_lengkap }}</h6>
                                <small class="text-gray-500 dark:text-gray-400 text-xs">NIK: {{ $kas->penduduk->nik }}</small>
                            </div>
                            <div class="flex-shrink-0">
                                @if($kas->penduduk->user)
                                    <div class="w-2 h-2 bg-green-500 rounded-full" title="Punya Akun"></div>
                                @else
                                    <div class="w-2 h-2 bg-gray-400 rounded-full" title="Tidak Punya Akun"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function kasEdit() {
    return {
        isLoading: false,

        init() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            // Show/hide payment details based on status
            const statusSelect = document.getElementById('status');
            const paymentDetails = document.getElementById('paymentDetails');
            
            statusSelect.addEventListener('change', function() {
                if (this.value === 'lunas') {
                    paymentDetails.style.display = 'block';
                } else {
                    paymentDetails.style.display = 'none';
                }
            });
        },

        handleSubmit(event) {
            // Basic client-side validation before submission
            const jumlah = $('#jumlah').val();
            const mingguKe = $('#minggu_ke').val();
            const tahun = $('#tahun').val();
            const tanggalJatuhTempo = $('#tanggal_jatuh_tempo').val();
            const status = $('#status').val();

            if (jumlah === null || jumlah < 0) {
                event.preventDefault();
                alert('Jumlah kas tidak boleh negatif');
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

            if (!status) {
                event.preventDefault();
                alert('Silakan pilih status kas');
                return false;
            }

            // Show loading state
            this.isLoading = true;
        }
    }
}
</script>
@endpush
@endsection
