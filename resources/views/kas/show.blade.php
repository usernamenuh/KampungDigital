@extends('layouts.app')

@section('title', 'Detail Kas')

@push('styles')
<style>
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px; /* full rounded */
        font-size: 0.75rem; /* text-xs */
        font-weight: 600; /* font-semibold */
        display: inline-block;
    }
    .status-belum_bayar {
        background-color: #fef3c7; /* yellow-100 */
        color: #b45309; /* yellow-800 */
    }
    .dark .status-belum_bayar {
        background-color: #422006; /* yellow-900 */
        color: #fcd34d; /* yellow-300 */
    }
    .status-lunas {
        background-color: #d1fae5; /* green-100 */
        color: #065f46; /* green-800 */
    }
    .dark .status-lunas {
        background-color: #064e3b; /* green-900 */
        color: #a7f3d0; /* green-300 */
    }
    .status-menunggu_konfirmasi {
        background-color: #bfdbfe; /* blue-200 */
        color: #1e40af; /* blue-800 */
    }
    .dark .status-menunggu_konfirmasi {
        background-color: #1e3a8a; /* blue-900 */
        color: #93c5fd; /* blue-300 */
    }
    .status-terlambat {
        background-color: #fee2e2; /* red-100 */
        color: #991b1b; /* red-800 */
    }
    .dark .status-terlambat {
        background-color: #7f1d1d; /* red-900 */
        color: #fca5a5; /* red-300 */
    }
    .status-ditolak {
        background-color: #fecaca; /* red-200 */
        color: #b91c1c; /* red-800 */
    }
    .dark .status-ditolak {
        background-color: #991b1b; /* red-900 */
        color: #f87171; /* red-400 */
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 animate-fade-in" x-data="{ openConfirmModal: false, openRejectModal: false, kasId: {{ $kas->id }} }" x-init="if (typeof lucide !== 'undefined') { lucide.createIcons(); }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-lucide="clipboard-list" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Kas</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Informasi lengkap mengenai tagihan kas.</p>
                        </div>
                    </div>
                    <a href="{{ route('kas.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-xl transition-all duration-200 shadow-md dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Kas Details Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Informasi Kas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 dark:text-gray-300">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Penduduk:</p>
                    <p class="text-base font-semibold">{{ $kas->penduduk->nama_lengkap ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">RT/RW:</p>
                    <p class="text-base font-semibold">RT {{ $kas->rt->no_rt ?? 'N/A' }} / RW {{ $kas->rt->rw->no_rw ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Minggu/Tahun:</p>
                    <p class="text-base font-semibold">Minggu ke-{{ $kas->minggu_ke }} / {{ $kas->tahun }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Tagihan:</p>
                    <p class="text-base font-semibold text-blue-600 dark:text-blue-400">{{ $kas->formatted_amount }}</p>
                </div>
                @if($kas->denda > 0)
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Denda:</p>
                    <p class="text-base font-semibold text-red-600 dark:text-red-400">{{ $kas->formatted_denda }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pembayaran:</p>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ $kas->formatted_total_bayar }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jatuh Tempo:</p>
                    <p class="text-base font-semibold">{{ $kas->tanggal_jatuh_tempo_formatted }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</p>
                    <p class="text-base font-semibold">
                        <span class="status-badge status-{{ $kas->status }}">
                            {{ $kas->status_text }}
                        </span>
                    </p>
                </div>
                @if($kas->status === 'ditolak' && $kas->rejection_reason)
                <div class="md:col-span-2 mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                    <p class="text-sm font-medium text-red-700 dark:text-red-300 flex items-center">
                        <i data-lucide="info" class="w-4 h-4 mr-2"></i>
                        Alasan Penolakan:
                    </p>
                    <p class="text-base text-red-800 dark:text-red-200 mt-1">{{ $kas->rejection_reason }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Details Card (if paid/pending) -->
        @if($kas->status !== 'belum_bayar' && $kas->status !== 'terlambat')
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Detail Pembayaran</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 dark:text-gray-300">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Bayar:</p>
                    <p class="text-base font-semibold">{{ $kas->tanggal_bayar_formatted ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Metode Bayar:</p>
                    <p class="text-base font-semibold">{{ ucfirst(str_replace('_', ' ', $kas->metode_bayar ?? 'N/A')) }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Dibayar:</p>
                    <p class="text-base font-semibold">{{ $kas->formatted_paid_amount ?? 'N/A' }}</p>
                </div>
                @if($kas->bukti_bayar_file)
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Bukti Pembayaran:</p>
                    <a href="{{ route('kas.payments.proof', $kas->id) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors duration-200">
                        <i data-lucide="image" class="w-4 h-4 mr-1"></i>
                        Lihat Bukti
                    </a>
                    <a href="{{ route('kas.payments.download.proof', $kas->id) }}" class="inline-flex items-center ml-4 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 font-medium transition-colors duration-200">
                        <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                        Download Bukti
                    </a>
                </div>
                @endif
                @if($kas->bukti_bayar_notes)
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Catatan Pembayaran:</p>
                    <p class="text-base font-semibold">{{ $kas->bukti_bayar_notes }}</p>
                </div>
                @endif
                @if($kas->confirmed_by)
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Dikonfirmasi Oleh:</p>
                    <p class="text-base font-semibold">{{ $kas->confirmedBy->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Konfirmasi:</p>
                    <p class="text-base font-semibold">{{ optional($kas->confirmed_at)->format('d M Y H:i') ?? '-' }}</p>
                </div>
                @endif
                @if($kas->confirmation_notes)
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Catatan Konfirmasi:</p>
                    <p class="text-base">{{ $kas->confirmation_notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Actions Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Aksi</h2>
            <div class="flex flex-wrap gap-4">
                @if(Auth::user()->hasRole(['rt', 'rw', 'kades', 'admin']))
                    @if($kas->status === 'menunggu_konfirmasi')
                    <button @click="openConfirmModal = true" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-md transition-all duration-200">
                        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                        Konfirmasi Pembayaran
                    </button>
                    <button @click="openRejectModal = true" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-md transition-all duration-200">
                        <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                        Tolak Pembayaran
                    </button>
                    @elseif($kas->status === 'ditolak')
                    <button @click="document.getElementById('reconfirm-form-{{ $kas->id }}').submit()" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl shadow-md transition-all duration-200">
                        <i data-lucide="rotate-ccw" class="w-5 h-5 mr-2"></i>
                        Ajukan Konfirmasi Ulang
                    </button>
                    <form id="reconfirm-form-{{ $kas->id }}" action="{{ route('kas.konfirmasi-ulang', $kas->id) }}" method="POST">
                        @csrf
                    </form>
                    @endif
                @endif

                @if(Auth::user()->hasRole('masyarakat'))
                    @if($kas->status === 'belum_bayar' || $kas->status === 'terlambat' || $kas->status === 'ditolak')
                    <a href="{{ route('kas.payment.form', $kas->id) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md transition-all duration-200">
                        <i data-lucide="credit-card" class="w-5 h-5 mr-2"></i>
                        Bayar Sekarang
                    </a>
                    @elseif($kas->status === 'menunggu_konfirmasi')
                    <span class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl shadow-md">
                        <i data-lucide="hourglass" class="w-5 h-5 mr-2"></i>
                        Menunggu Konfirmasi
                    </span>
                    @endif
                @endif

                @if(Auth::user()->hasRole(['rt', 'rw', 'kades', 'admin']))
                    <a href="{{ route('kas.edit', $kas->id) }}" class="inline-flex items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-xl shadow-md transition-all duration-200">
                        <i data-lucide="edit" class="w-5 h-5 mr-2"></i>
                        Edit Kas
                    </a>
                    @if(Auth::user()->hasRole('admin'))
                    <form action="{{ route('kas.destroy', $kas->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kas ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl shadow-md transition-all duration-200">
                            <i data-lucide="trash-2" class="w-5 h-5 mr-2"></i>
                            Hapus Kas
                        </button>
                    </form>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Confirm Payment Modal -->
    <div x-show="openConfirmModal" class="fixed inset-0 bg-gray-900 bg-opacity-80 flex items-center justify-center z-50 p-4" x-cloak>
        <div @click.away="openConfirmModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md transform transition-all duration-300 scale-95"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Konfirmasi Pembayaran</h3>
            <p class="text-gray-700 dark:text-gray-300 mb-6">Apakah Anda yakin ingin mengkonfirmasi pembayaran kas ini sebagai Lunas?</p>
            <div class="flex justify-end space-x-3">
                <button @click="openConfirmModal = false" type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200">
                    Batal
                </button>
                <form action="{{ route('kas.payments.confirm', $kas->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                        Konfirmasi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Payment Modal -->
    <div x-show="openRejectModal" class="fixed inset-0 bg-gray-900 bg-opacity-80 flex items-center justify-center z-50 p-4" x-cloak>
        <div @click.away="openRejectModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md transform transition-all duration-300 scale-95"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Tolak Pembayaran</h3>
            <form action="{{ route('kas.tolak', $kas->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Masukkan alasan penolakan pembayaran..."></textarea>
                    @error('rejection_reason')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end space-x-3">
                    <button @click="openRejectModal = false" type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
