@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')

@push('styles')
<style>
    .success-card {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
        border: 1px solid rgba(34, 197, 94, 0.1);
    }
    .dark .success-card {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%);
        border: 1px solid rgba(34, 197, 94, 0.2);
    }
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .icon-bounce {
        animation: bounce 1s infinite;
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
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
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 flex items-center justify-center animate-fade-in">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden success-card">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6 icon-bounce">
                    <i data-lucide="check-circle" class="w-10 h-10 text-green-600 dark:text-green-400"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Pembayaran Berhasil!</h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-6">
                    Terima kasih, pembayaran kas Anda untuk minggu ke-{{ $kas->minggu_ke }} tahun {{ $kas->tahun }} telah berhasil diajukan.
                </p>

                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-xl border border-gray-200 dark:border-gray-700 mb-8 text-left">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i data-lucide="receipt" class="w-5 h-5 mr-2 text-blue-500"></i>
                        Detail Pembayaran
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Nama Penduduk:</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $kas->penduduk->nama_lengkap ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">RT/RW:</p>
                            <p class="font-medium text-gray-900 dark:text-white">RT {{ $kas->rt->no_rt ?? 'N/A' }} / RW {{ $kas->rt->rw->no_rw ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Jumlah Dibayar:</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $kas->formatted_amount }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Metode Pembayaran:</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $kas->metode_bayar_formatted }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Tanggal Pembayaran:</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $kas->tanggal_bayar_formatted }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Status:</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                <span class="status-badge status-{{ $kas->status }}">
                                    {{ $kas->status_text }}
                                </span>
                            </p>
                        </div>
                        @if($kas->bukti_bayar_notes)
                        <div class="col-span-full">
                            <p class="text-gray-500 dark:text-gray-400">Catatan:</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $kas->bukti_bayar_notes }}</p>
                        </div>
                        @endif
                        @if($kas->bukti_bayar_file)
                        <div class="col-span-full mt-4">
                            <a href="{{ route('payments.proof', $kas->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg shadow-md transition-colors duration-200">
                                <i data-lucide="image" class="w-4 h-4 mr-2"></i>
                                Lihat Bukti Pembayaran
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('dashboard.masyarakat') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                    <a href="{{ route('kas.index') }}" 
                       class="inline-flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                        <i data-lucide="list-checks" class="w-4 h-4 mr-2"></i>
                        Lihat Daftar Kas
                    </a>
                </div>
            </div>
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
