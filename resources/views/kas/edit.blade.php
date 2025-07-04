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
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.02) 0%, rgba(99, 102, 241, 0.02) 100%);
    border: 1px solid rgba(59, 130, 246, 0.08);
}

.dark .form-section {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.03) 0%, rgba(99, 102, 241, 0.03) 100%);
    border: 1px solid rgba(59, 130, 246, 0.12);
}

.payment-section {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.02) 0%, rgba(5, 150, 105, 0.02) 100%);
    border: 1px solid rgba(16, 185, 129, 0.08);
}

.dark .payment-section {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.03) 0%, rgba(5, 150, 105, 0.03) 100%);
    border: 1px solid rgba(16, 185, 129, 0.12);
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 animate-fade-in" x-data="kasEdit()" x-init="init()">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i data-lucide="edit" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                            <a href="{{ route('kas.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Daftar Kas</a>
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            <a href="{{ route('kas.show', $kas) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">Detail Kas</a>
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            <span>Edit</span>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Kas</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $kas->penduduk->nama_lengkap }} - Minggu {{ $kas->minggu_ke }}/{{ $kas->tahun }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('kas.show', $kas) }}" 
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
            <!-- Current Status Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 mb-8">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4 text-white"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Status Saat Ini</h2>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($kas->penduduk->nama_lengkap, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">{{ $kas->penduduk->nama_lengkap }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">RT {{ $kas->rt->no_rt }} / RW {{ $kas->rt->rw->no_rw }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-2">
                            Rp {{ number_format($kas->jumlah, 0, ',', '.') }}
                        </div>
                        @switch($kas->status)
                            @case('lunas')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                    Lunas
                                </span>
                                @break
                            @case('belum_bayar')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-800">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                    Belum Bayar
                                </span>
                                @break
                            @case('terlambat')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800">
                                    <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                    Terlambat
                                </span>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-amber-500 to-orange-500 rounded-lg flex items-center justify-center">
                            <i data-lucide="edit" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Form Edit Kas</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Ubah informasi kas sesuai kebutuhan</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <form action="{{ route('kas.update', $kas) }}" method="POST" @submit="handleSubmit">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-section p-6 rounded-xl space-y-6 mb-8">
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
                                               class="w-full pl-10 pr-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-amber-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                               value="{{ old('jumlah', $kas->jumlah) }}" min="1000" step="1000" required>
                                    </div>
                                    <small class="text-gray-500 dark:text-gray-400 mt-1 block">Minimal Rp 1.000</small>
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status" id="status" 
                                            class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-amber-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200" 
                                            required>
                                        <option value="belum_bayar" {{ old('status', $kas->status) == 'belum_bayar' ? 'selected' : '' }}>
                                            Belum Bayar
                                        </option>
                                        <option value="lunas" {{ old('status', $kas->status) == 'lunas' ? 'selected' : '' }}>
                                            Lunas
                                        </option>
                                        <option value="terlambat" {{ old('status', $kas->status) == 'terlambat' ? 'selected' : '' }}>
                                            Terlambat
                                        </option>
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
                                           class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-amber-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                           value="{{ old('minggu_ke', $kas->minggu_ke) }}" min="1" max="53" required>
                                </div>

                                <div>
                                    <label for="tahun" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i data-lucide="calendar" class="w-4 h-4 inline mr-2"></i>
                                        Tahun <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="tahun" id="tahun" 
                                           class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-amber-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                           value="{{ old('tahun', $kas->tahun) }}" min="2020" max="2030" required>
                                </div>

                                <div>
                                    <label for="tanggal_jatuh_tempo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i data-lucide="clock" class="w-4 h-4 inline mr-2"></i>
                                        Tanggal Jatuh Tempo <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" 
                                           class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-amber-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                           value="{{ old('tanggal_jatuh_tempo', $kas->tanggal_jatuh_tempo->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details Section -->
                        <div class="payment-section p-6 rounded-xl space-y-6 mb-8" 
                             x-show="showPaymentDetails" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             style="display: none;">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                                    <i data-lucide="credit-card" class="w-4 h-4 text-white"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Detail Pembayaran</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="tanggal_bayar" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i data-lucide="calendar-check" class="w-4 h-4 inline mr-2"></i>
                                        Tanggal Bayar
                                    </label>
                                    <input type="datetime-local" name="tanggal_bayar" id="tanggal_bayar" 
                                           class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-green-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                           value="{{ old('tanggal_bayar', $kas->tanggal_bayar ? $kas->tanggal_bayar->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                                </div>
                                <div>
                                    <label for="metode_bayar" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i data-lucide="credit-card" class="w-4 h-4 inline mr-2"></i>
                                        Metode Pembayaran
                                    </label>
                                    <select name="metode_bayar" id="metode_bayar" 
                                            class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-green-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200">
                                        <option value="">Pilih Metode</option>
                                        <option value="tunai" {{ old('metode_bayar', $kas->metode_bayar) == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                        <option value="transfer" {{ old('metode_bayar', $kas->metode_bayar) == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                        <option value="digital" {{ old('metode_bayar', $kas->metode_bayar) == 'digital' ? 'selected' : '' }}>Digital Payment</option>
                                        <option value="e_wallet" {{ old('metode_bayar', $kas->metode_bayar) == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label for="keterangan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                    <i data-lucide="file-text" class="w-4 h-4 inline mr-2"></i>
                                    Keterangan (Opsional)
                                </label>
                                <textarea name="keterangan" id="keterangan" rows="3"
                                          class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-amber-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 resize-none"
                                          placeholder="Keterangan tambahan untuk kas ini...">{{ old('keterangan', $kas->keterangan) }}</textarea>
                            </div>

                            <div>
                                <label for="bukti_bayar" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                    <i data-lucide="file-check" class="w-4 h-4 inline mr-2"></i>
                                    Bukti Pembayaran (Opsional)
                                </label>
                                <textarea name="bukti_bayar" id="bukti_bayar" rows="3"
                                          class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-amber-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 resize-none"
                                          placeholder="Nomor referensi, keterangan bukti pembayaran, atau catatan lainnya...">{{ old('bukti_bayar', $kas->bukti_bayar) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('kas.show', $kas) }}" 
                               class="inline-flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl"
                                    :disabled="isLoading">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                <span x-text="isLoading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                        <i data-lucide="info" class="w-4 h-4 text-white"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Penduduk</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">Nama</span>
                        <span class="text-gray-900 dark:text-white font-semibold">{{ $kas->penduduk->nama_lengkap }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">NIK</span>
                        <span class="text-gray-900 dark:text-white font-semibold">{{ $kas->penduduk->nik }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">RT/RW</span>
                        <span class="text-gray-900 dark:text-white font-semibold">RT {{ $kas->rt->no_rt }} / RW {{ $kas->rt->rw->no_rw }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">Periode</span>
                        <span class="text-gray-900 dark:text-white font-semibold">{{ $kas->minggu_ke }}/{{ $kas->tahun }}</span>
                    </div>
                </div>
            </div>

            @if($kas->status === 'lunas' && $kas->tanggal_bayar)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-4 h-4 text-white"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Riwayat Pembayaran</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">Tanggal Bayar</span>
                        <span class="text-green-600 dark:text-green-400 font-semibold">{{ $kas->tanggal_bayar->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($kas->metode_bayar)
                        <div class="flex items-center justify-between py-2">
                            <span class="text-gray-600 dark:text-gray-400 font-medium">Metode</span>
                            <span class="text-gray-900 dark:text-white font-semibold">{{ ucfirst($kas->metode_bayar) }}</span>
                        </div>
                    @endif
                </div>
            </div>
            @endif
            
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                <div class="flex items-start">
                    <i data-lucide="lightbulb" class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0"></i>
                    <div class="text-sm text-blue-800 dark:text-blue-200">
                        <strong>Tips:</strong> Pastikan data yang diubah sudah benar sebelum menyimpan perubahan. Perubahan status ke "Lunas" akan menampilkan form detail pembayaran.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
function kasEdit() {
    return {
        showPaymentDetails: false,
        isLoading: false,

        init() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Setup event listener for status change
            const statusSelect = document.getElementById('status');
            statusSelect.addEventListener('change', () => {
                this.togglePaymentDetails();
            });

            // Initialize payment details visibility
            this.togglePaymentDetails();
        },

        togglePaymentDetails() {
            const status = document.getElementById('status').value;
            this.showPaymentDetails = status === 'lunas';
        },

        handleSubmit(event) {
            this.isLoading = true;
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
