@extends('layouts.app')

@section('title', 'Dashboard Masyarakat')
@section('page-title', 'Dashboard Masyarakat')
@section('page-description', 'Selamat datang, ' . auth()->user()->name . '! Kelola tagihan kas Anda.')

@section('content')
<div x-data="masyarakatDashboardData()" x-init="initDashboard()" class="p-6 space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6 rounded-xl shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Dashboard Masyarakat</h2>
                <p class="text-blue-100" x-text="`Selamat datang, ${userName}! Kelola tagihan kas Anda.`">Selamat datang! Kelola tagihan kas Anda.</p>
                @if(auth()->user()->penduduk)
                <p class="text-blue-200 text-sm mt-1">NIK: {{ auth()->user()->penduduk->nik }} | {{ auth()->user()->penduduk->nama_lengkap }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-sm text-blue-100" x-text="currentTime">01.32.26</p>
                <p class="text-xs text-blue-200" x-text="currentDate">Kamis, 3 Juli 2025</p>
                <div class="flex items-center mt-2 justify-end">
                    <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                    <span class="text-xs text-blue-200">Online</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Check if user has penduduk data -->
    @if(!auth()->user()->penduduk)
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 shadow-sm dark:bg-red-900/20 dark:border-red-800">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-bold text-red-800 dark:text-red-200">Data Penduduk Tidak Ditemukan</h4>
                <p class="text-sm text-red-700 dark:text-red-300">Akun Anda belum terhubung dengan data penduduk. Silakan hubungi administrator untuk menghubungkan akun Anda dengan data kependudukan.</p>
            </div>
        </div>
    </div>
    @else

    <!-- Kas Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Kas Lunas -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Lunas</p>
                    <p class="text-3xl font-bold text-green-600 mt-2" x-text="kasLunas">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium">Tahun ini</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Kas Belum Bayar -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Belum Bayar</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2" x-text="kasBelumBayar">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-yellow-600 font-medium">Segera bayar</span>
                    </div>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-xl">
                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Kas Terlambat -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Terlambat</p>
                    <p class="text-3xl font-bold text-red-600 mt-2" x-text="kasTerlambat">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-red-600 font-medium">Perlu tindakan</span>
                    </div>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-xl">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Kas Anda -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Kas Anda</p>
                    <p class="text-2xl font-bold text-blue-600 mt-2" x-text="formatCurrency(totalKasAnda)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-blue-600 font-medium">Tahun ini</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Alert for Unpaid Kas -->
    <div x-show="kasBelumBayar > 0 && !isYearCompleted" x-cloak class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center animate-pulse">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <h4 class="text-sm font-medium text-green-800 dark:text-green-200">💚 Ada Kas yang Perlu Dibayar!</h4>
                <p class="text-sm text-green-700 dark:text-green-300" x-text="`Anda memiliki ${kasBelumBayar} kas yang belum dibayar. Klik tombol hijau untuk membayar sekarang.`"></p>
            </div>
            <div class="ml-4">
                <button @click="scrollToKasList()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                    Lihat Kas
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kas Status -->
        <div class="lg:col-span-2">
            <!-- Year Completion Alert -->
            <div x-show="isYearCompleted" x-cloak class="mb-6 bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 border border-green-200 dark:border-green-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">🎉 Selamat! Anda Warga Teladan!</h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Anda telah menyelesaikan semua pembayaran kas untuk tahun ini! Terima kasih atas kedisiplinan Anda.</p>
                    </div>
                </div>
            </div>

            <!-- Kas List -->
            <div id="kas-list-section" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Daftar Kas Anda</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-show="!isYearCompleted">Kas yang perlu dibayar akan muncul dengan tombol hijau</p>
                        <p class="text-sm text-green-600" x-show="isYearCompleted">Semua Kas Sudah Dibayar! 🎉</p>
                    </div>
                    <button @click="refreshKasList()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>

                <!-- Loading State -->
                <div x-show="isLoading" class="text-center py-12">
                    <svg class="w-8 h-8 animate-spin mx-auto mb-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <p class="text-gray-600">Memuat data kas...</p>
                </div>

                <!-- Success Message for Completed Year -->
                <div x-show="isYearCompleted && !isLoading" x-cloak class="text-center py-12">
                    <div class="w-24 h-24 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Semua Kas Sudah Dibayar!</h3>
                    <p class="text-gray-500 dark:text-gray-400">Terima kasih atas partisipasi Anda dalam kas RT.</p>
                </div>

                <!-- Kas List with Scrollable Container -->
                <div x-show="!isYearCompleted && !isLoading" class="space-y-4 max-h-96 overflow-y-auto pr-2" style="scrollbar-width: thin;">
                    <template x-for="kas in kasList" :key="kas.id">
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-medium text-gray-800 dark:text-white" x-text="`Minggu ke-${kas.minggu_ke}`"></h4>
                                        <span :class="{
                                            'bg-green-100 text-green-800': kas.status === 'lunas',
                                            'bg-yellow-100 text-yellow-800': kas.status === 'menunggu_konfirmasi',
                                            'bg-red-100 text-red-800': kas.is_overdue && kas.status === 'belum_bayar',
                                            'bg-gray-100 text-gray-800': kas.status === 'belum_bayar' && !kas.is_overdue
                                        }" class="px-2 py-1 rounded-full text-xs font-medium" x-text="kas.status_text"></span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Jumlah</p>
                                            <p class="font-medium text-green-600" x-text="formatCurrency(kas.jumlah)"></p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Jatuh Tempo</p>
                                            <p class="font-medium text-gray-800 dark:text-white" x-text="kas.tanggal_jatuh_tempo_formatted"></p>
                                        </div>
                                    </div>
                                    <div x-show="kas.status === 'lunas'" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p class="text-gray-500 dark:text-gray-400">Dibayar</p>
                                                <p class="font-medium text-gray-800 dark:text-white" x-text="kas.tanggal_bayar_formatted"></p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 dark:text-gray-400">Metode</p>
                                                <p class="font-medium text-gray-800 dark:text-white" x-text="kas.metode_bayar_formatted"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-show="kas.status === 'menunggu_konfirmasi'" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p class="text-gray-500 dark:text-gray-400">Metode</p>
                                                <p class="font-medium text-gray-800 dark:text-white" x-text="kas.metode_bayar_formatted"></p>
                                            </div>
                                            <div>
                                                <p class="text-500 dark:text-gray-400">Waktu Upload</p>
                                                <p class="font-medium text-gray-800 dark:text-white" x-text="kas.bukti_bayar_uploaded_at_formatted"></p>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Menunggu konfirmasi dari RT/RW Anda.</p>
                                    </div>
                                </div>
                                <div x-show="kas.status === 'belum_bayar'" class="ml-4">
                                    <button @click="openPaymentModal(kas)" 
                                            :class="{
                                                'bg-red-600 hover:bg-red-700': kas.is_overdue,
                                                'bg-green-600 hover:bg-green-700': !kas.is_overdue
                                            }" 
                                            class="text-white px-4 py-2 rounded-lg transition-colors flex items-center text-sm animate-pulse">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        <span x-text="kas.is_overdue ? 'Bayar Terlambat' : 'Bayar Sekarang'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Load More Button -->
                    <div x-show="hasMoreKas" class="text-center pt-4">
                        <button @click="loadMoreKas()" :disabled="isLoadingMore" class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors disabled:opacity-50">
                            <span x-show="!isLoadingMore">Muat Lebih Banyak</span>
                            <span x-show="isLoadingMore" class="flex items-center">
                                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Memuat...
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div x-show="kasList.length === 0 && !isYearCompleted && !isLoading" class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Tidak Ada Kas</h3>
                    <p class="text-gray-500 dark:text-gray-400">Belum ada tagihan kas untuk Anda.</p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Recent Payments -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Riwayat Pembayaran</h3>
                    <button @click="refreshRecentPayments()" class="text-blue-600 hover:text-blue-800 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-3 max-h-80 overflow-y-auto" style="scrollbar-width: thin;">
                    <template x-for="payment in recentPayments" :key="payment.id">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="`Minggu ke-${payment.minggu_ke}`"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatCurrency(payment.jumlah)"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="`Dibayar: ${payment.tanggal_bayar_formatted}`"></p>
                            </div>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Lunas</span>
                        </div>
                    </template>
                    
                    <!-- Empty state for recent payments -->
                    <div x-show="recentPayments.length === 0" class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-500">Belum ada riwayat pembayaran</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Aksi Cepat</h3>
                <div class="space-y-3">
                    <a href="{{ route('kas.index') }}" class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        <span class="text-sm font-medium text-blue-600">Lihat Semua Kas</span>
                    </a>
                    <a href="{{ route('notifikasi.index') }}" class="flex items-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                        <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 4.828A4 4 0 015.5 4H9v1a3 3 0 006 0V4h3.5c.5 0 .956.12 1.328.328l-8.656 8.656A4 4 0 0110.5 13H7v-1a3 3 0 00-6 0v1H.5a4 4 0 01.328-1.172l8.656-8.656z"></path>
                        </svg>
                        <span class="text-sm font-medium text-purple-600">Notifikasi</span>
                        <span x-show="unreadNotifications > 0" x-text="unreadNotifications" class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1"></span>
                    </a>
                    <button @click="downloadReceipt()" class="w-full flex items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-sm font-medium text-green-600">Download Riwayat</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Konfirmasi Pembayaran</h3>
                    <button @click="closePaymentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4" x-show="selectedKas.id">
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Minggu: <span x-text="`ke-${selectedKas.minggu_ke}`" class="font-medium"></span></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Jumlah: <span x-text="formatCurrency(selectedKas.jumlah)" class="font-medium text-green-600"></span></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status: 
                            <span :class="{
                                'text-red-600': selectedKas.is_overdue,
                                'text-yellow-600': !selectedKas.is_overdue
                            }" class="font-medium" x-text="selectedKas.status_text"></span>
                        </p>
                    </div>
                </div>

                <div x-show="paymentInfo" class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">Detail Pembayaran (<span x-text="paymentInfo.rt_info"></span>)</h4>
                    <template x-if="paymentInfo.bank_transfer && paymentInfo.bank_transfer.account_number">
                        <div class="mb-3">
                            <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Transfer Bank:</p>
                            <p class="text-sm text-gray-800 dark:text-white">Bank: <span x-text="paymentInfo.bank_transfer.bank_name"></span></p>
                            <p class="text-sm text-gray-800 dark:text-white">No. Rek: <span x-text="paymentInfo.bank_transfer.account_number"></span></p>
                            <p class="text-sm text-gray-800 dark:text-white">A.N.: <span x-text="paymentInfo.bank_transfer.account_name"></span></p>
                        </div>
                    </template>
                    <template x-if="paymentInfo.e_wallet && (paymentInfo.e_wallet.dana || paymentInfo.e_wallet.ovo || paymentInfo.e_wallet.gopay)">
                        <div class="mb-3">
                            <p class="text-sm font-medium text-blue-700 dark:text-blue-300">E-Wallet:</p>
                            <template x-if="paymentInfo.e_wallet.dana">
                                <p class="text-sm text-gray-800 dark:text-white">DANA: <span x-text="paymentInfo.e_wallet.dana"></span></p>
                            </template>
                            <template x-if="paymentInfo.e_wallet.ovo">
                                <p class="text-sm text-gray-800 dark:text-white">OVO: <span x-text="paymentInfo.e_wallet.ovo"></span></p>
                            </template>
                            <template x-if="paymentInfo.e_wallet.gopay">
                                <p class="text-sm text-gray-800 dark:text-white">GOPAY: <span x-text="paymentInfo.e_wallet.gopay"></span></p>
                            </template>
                        </div>
                    </template>
                    <template x-if="paymentInfo.qr_code && paymentInfo.qr_code.image_url">
                        <div class="mb-3 text-center">
                            <p class="text-sm font-medium text-blue-700 dark:text-blue-300">QR Code:</p>
                            <img :src="paymentInfo.qr_code.image_url" alt="QR Code Pembayaran" class="w-32 h-32 mx-auto border rounded-lg mt-2">
                            <p x-show="paymentInfo.qr_code.description" class="text-xs text-gray-600 dark:text-gray-400 mt-1" x-text="paymentInfo.qr_code.description"></p>
                        </div>
                    </template>
                    <template x-if="paymentInfo.payment_notes">
                        <div class="mt-3 pt-3 border-t border-blue-200 dark:border-blue-700">
                            <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Catatan:</p>
                            <p class="text-sm text-gray-800 dark:text-white" x-text="paymentInfo.payment_notes"></p>
                        </div>
                    </template>
                </div>
                <div x-show="!paymentInfo" class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-yellow-800 dark:text-yellow-200">
                    <p class="text-sm">Informasi pembayaran untuk RT Anda belum diatur. Silakan hubungi pengurus RT/RW.</p>
                </div>
                
                <form @submit.prevent="submitPayment()">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Metode Pembayaran *</label>
                        <select x-model="paymentMethod" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">Pilih Metode</option>
                            <option value="tunai">💵 Tunai</option>
                            <template x-if="paymentInfo && paymentInfo.available_methods">
                                <template x-for="method in paymentInfo.available_methods" :key="method">
                                    <option :value="method" x-text="formatMetodeBayar(method)"></option>
                                </template>
                            </template>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload Bukti Pembayaran *</label>
                        <input type="file" x-ref="buktiBayarFile" accept="image/*" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 5MB</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keterangan (Opsional)</label>
                        <textarea x-model="paymentNote" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Nomor referensi, keterangan, atau catatan lainnya..."></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="closePaymentModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="isProcessing || !paymentInfo" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50">
                            <span x-show="!isProcessing" class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                Konfirmasi Bayar
                            </span>
                            <span x-show="isProcessing" class="flex items-center">
                                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function masyarakatDashboardData() {
    return {
        userName: '{{ auth()->user()->name }}',
        currentTime: '',
        currentDate: '',
        kasLunas: 0,
        kasBelumBayar: 0,
        kasTerlambat: 0,
        totalKasAnda: 0,
        kasList: [],
        recentPayments: [],
        showPaymentModal: false,
        selectedKas: {},
        paymentMethod: '',
        paymentNote: '',
        isProcessing: false,
        isYearCompleted: false,
        hasMoreKas: false,
        isLoadingMore: false,
        isLoading: true,
        currentPage: 1,
        unreadNotifications: 0,
        paymentInfo: null, // New property for payment info

        async initDashboard() {
            console.log('🚀 Initializing Masyarakat Dashboard...');
            
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
            
            @if(auth()->user()->penduduk)
            await this.loadDashboardData();
            await this.loadUnreadNotifications();
            @else
            this.isLoading = false;
            @endif
            
            console.log('✅ Masyarakat Dashboard initialized successfully');
        },
        
        updateTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID');
            this.currentDate = now.toLocaleDateString('id-ID', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        },
        
        async loadDashboardData() {
            try {
                console.log('📊 Loading masyarakat dashboard data...');
                this.isLoading = true;
                
                // Load stats from API
                const statsResponse = await fetch('/api/kas/stats');
                if (statsResponse.ok) {
                    const statsData = await statsResponse.json();
                    if (statsData.success) {
                        this.kasLunas = statsData.data.kasLunas || 0;
                        this.kasBelumBayar = statsData.data.kasBelumBayar || 0;
                        this.kasTerlambat = statsData.data.kasTerlambat || 0;
                        this.totalKasAnda = statsData.data.totalKasAnda || 0;
                        
                        // Check if year is completed (52 weeks paid)
                        this.isYearCompleted = this.kasLunas >= 52;
                    }
                }
                
                // Load kas list
                await this.loadKasList();
                await this.loadRecentPayments();
                
                console.log('✅ Masyarakat data loaded successfully');
            } catch (error) {
                console.error('❌ Error loading masyarakat dashboard data:', error);
                this.showNotification('Gagal memuat data dashboard', 'error');
            } finally {
                this.isLoading = false;
            }
        },
        
        async loadKasList(page = 1) {
            try {
                const response = await fetch(`/api/kas?page=${page}&status=belum_bayar,menunggu_konfirmasi&limit=10`); // Include menunggu_konfirmasi
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        const newKas = data.data.map(kas => ({
                            ...kas,
                            is_overdue: kas.status === 'belum_bayar' && new Date(kas.tanggal_jatuh_tempo) < new Date(),
                            status_text: kas.status === 'lunas' ? 'Lunas' : 
                                        (kas.status === 'menunggu_konfirmasi' ? 'Menunggu Konfirmasi' :
                                        (new Date(kas.tanggal_jatuh_tempo) < new Date() ? 'Terlambat' : 'Belum Bayar')),
                            tanggal_jatuh_tempo_formatted: new Date(kas.tanggal_jatuh_tempo).toLocaleDateString('id-ID'),
                            tanggal_bayar_formatted: kas.tanggal_bayar ? new Date(kas.tanggal_bayar).toLocaleDateString('id-ID') : '-',
                            metode_bayar_formatted: kas.metode_bayar ? this.formatMetodeBayar(kas.metode_bayar) : '-',
                            bukti_bayar_uploaded_at_formatted: kas.bukti_bayar_uploaded_at ? new Date(kas.bukti_bayar_uploaded_at).toLocaleString('id-ID') : '-'
                        }));
                        
                        if (page === 1) {
                            this.kasList = newKas;
                        } else {
                            this.kasList = [...this.kasList, ...newKas];
                        }
                        
                        this.hasMoreKas = data.pagination.has_more || false;
                        this.currentPage = page;
                    }
                }
            } catch (error) {
                console.error('Error loading kas list:', error);
            }
        },
        
        async loadMoreKas() {
            if (this.isLoadingMore || !this.hasMoreKas) return;
            
            this.isLoadingMore = true;
            await this.loadKasList(this.currentPage + 1);
            this.isLoadingMore = false;
        },
        
        async loadRecentPayments() {
            try {
                const response = await fetch('/api/kas?status=lunas&limit=5&sort=tanggal_bayar_desc');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.recentPayments = data.data.map(payment => ({
                            ...payment,
                            tanggal_bayar_formatted: payment.tanggal_bayar ? new Date(payment.tanggal_bayar).toLocaleDateString('id-ID') : '-'
                        }));
                    }
                }
            } catch (error) {
                console.error('Error loading recent payments:', error);
            }
        },
        
        async loadUnreadNotifications() {
            try {
                const response = await fetch('/api/notifications/unread-count');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.unreadNotifications = data.count || 0;
                    }
                }
            } catch (error) {
                console.error('Error loading unread notifications:', error);
            }
        },
        
        async refreshKasList() {
            await this.loadDashboardData();
            this.showNotification('Data kas berhasil diperbarui', 'success');
        },
        
        async refreshRecentPayments() {
            await this.loadRecentPayments();
            this.showNotification('Riwayat pembayaran diperbarui', 'success');
        },
        
        scrollToKasList() {
            document.getElementById('kas-list-section').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        },
        
        async openPaymentModal(kas) {
            this.selectedKas = kas;
            this.paymentMethod = '';
            this.paymentNote = '';
            this.paymentInfo = null; // Reset payment info
            
            // Fetch payment info for the selected RT
            if (kas.rt_id) { // Assuming kas object has rt_id
                try {
                    const response = await fetch(`/api/payment-info?rt_id=${kas.rt_id}`);
                    const data = await response.json();
                    if (data.success) {
                        this.paymentInfo = data.data;
                    } else {
                        this.showNotification(data.message || 'Gagal memuat info pembayaran RT ini.', 'warning');
                    }
                } catch (error) {
                    console.error('Error fetching payment info:', error);
                    this.showNotification('Gagal memuat info pembayaran.', 'error');
                }
            } else {
                this.showNotification('RT ID tidak ditemukan untuk kas ini.', 'error');
            }
            this.showPaymentModal = true;
        },
        
        closePaymentModal() {
            this.showPaymentModal = false;
            this.selectedKas = {};
            this.paymentMethod = '';
            this.paymentNote = '';
            this.isProcessing = false;
            this.paymentInfo = null; // Clear payment info on close
            if (this.$refs.buktiBayarFile) {
                this.$refs.buktiBayarFile.value = ''; // Clear file input
            }
        },
        
        async submitPayment() {
            if (!this.selectedKas.id || !this.paymentMethod || this.isProcessing) return;
            
            this.isProcessing = true;
            
            try {
                const formData = new FormData();
                formData.append('metode_bayar', this.paymentMethod);
                formData.append('bukti_bayar_notes', this.paymentNote);
                
                const buktiBayarFile = this.$refs.buktiBayarFile.files[0];
                if (buktiBayarFile) {
                    formData.append('bukti_bayar', buktiBayarFile);
                } else {
                    this.showNotification('Mohon unggah bukti pembayaran.', 'error');
                    this.isProcessing = false;
                    return;
                }

                const response = await fetch(`/payments/kas/${this.selectedKas.id}/submit`, { // Use the new route
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showNotification(data.message, 'success');
                    this.closePaymentModal();
                    
                    // Reload data
                    await this.loadDashboardData();
                    
                    // Check if year is now completed
                    if (this.kasLunas >= 52 && !this.isYearCompleted) {
                        this.isYearCompleted = true;
                        this.showNotification('🏆 Selamat! Anda telah menyelesaikan semua kas tahun ini!', 'success');
                    }
                } else {
                    throw new Error(data.message || 'Payment failed');
                }
            } catch (error) {
                console.error('Payment error:', error);
                this.showNotification('Gagal memproses pembayaran: ' + error.message, 'error');
            } finally {
                this.isProcessing = false;
            }
        },
        
        formatMetodeBayar(metode) {
            const methods = {
                'tunai': '💵 Tunai',
                'bank_transfer': '🏦 Transfer Bank',
                'e_wallet': '💳 E-Wallet',
                'qr_code': '📸 QR Code'
            };
            return methods[metode] || metode;
        },
        
        downloadReceipt() {
            this.showNotification('Fitur download riwayat akan segera tersedia', 'info');
        },
        
        showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, 5000);
        },
        
        formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        }
    }
}
</script>
@endpush
@endsection
