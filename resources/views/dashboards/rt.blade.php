@extends('layouts.app')

@section('title', 'Dashboard RT')
@section('page-title', 'Dashboard RT')
@section('page-description', 'Selamat datang, ' . auth()->user()->name . '! Kelola data RT Anda.')

@section('content')
<div x-data="rtDashboardData()" x-init="initDashboard()" class="p-6 space-y-6">
    <!-- RT Info Header -->
    <div class="p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 transition-all hover:shadow-lg bg-gradient-to-r from-orange-500 to-red-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Selamat datang, RT</h2>
                <p class="text-orange-100">RT <span x-text="rtNumber"></span> - Kelola data di wilayah Anda</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-orange-100" x-text="currentDate"></p>
                <div class="flex items-center justify-end mt-2">
                    <div :class="isOnline ? 'bg-green-400 animate-pulse' : 'bg-red-400'" class="w-2 h-2 rounded-full mr-2"></div>
                    <span class="text-sm text-orange-100" x-text="isOnline ? 'Online' : 'Offline'">Online</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RT Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total KK in RT -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Kepala Keluarga</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="totalKk">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-blue-600 font-medium">Di RT Anda</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i data-lucide="home" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Penduduk in RT -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Penduduk</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="totalPenduduk">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium">Di RT Anda</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i data-lucide="users" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Kas Lunas in RT -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Lunas</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="kasLunas">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-teal-600 font-medium">Tahun ini</span>
                    </div>
                </div>
                <div class="p-3 bg-teal-100 dark:bg-teal-900 rounded-xl">
                    <i data-lucide="check-circle" class="w-8 h-8 text-teal-600 dark:text-teal-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Kas Belum Bayar in RT -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Belum Bayar</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="kasBelumBayar">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-red-600 font-medium">Perlu perhatian</span>
                    </div>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-xl">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informasi Rekening RT Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Informasi Rekening RT</h3>
                <div class="flex space-x-2">
                    <button @click="loadPaymentInfo()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                    {{-- Link to the payment info index page, where the modal will handle creation --}}
                    <a href="{{ route('payment-info.index') }}" class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Atur
                    </a>
                </div>
            </div>
            
            <div x-show="paymentInfo" class="space-y-4">
                <template x-if="paymentInfo && paymentInfo.has_bank_transfer">
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <i data-lucide="banknote" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">Transfer Bank</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="paymentInfo.bank_name + ' - ' + paymentInfo.bank_account_number"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'A/N: ' + paymentInfo.bank_account_name"></p>
                        </div>
                    </div>
                </template>
                <template x-if="paymentInfo && paymentInfo.has_e_wallet">
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <i data-lucide="wallet" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">E-Wallet</p>
                            <template x-for="(number, wallet) in (paymentInfo ? paymentInfo.e_wallet_list : {})" :key="wallet">
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="wallet.toUpperCase() + ': ' + number"></p>
                            </template>
                        </div>
                    </div>
                </template>
                <template x-if="paymentInfo && paymentInfo.has_qr_code">
                    <div class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm font-medium text-gray-800 dark:text-white mb-2">QR Code Pembayaran</p>
                        <img :src="paymentInfo.qr_code_url" alt="QR Code Pembayaran" class="w-32 h-32 object-contain mb-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center" x-text="paymentInfo.qr_code_description"></p>
                    </div>
                </template>
                <template x-if="paymentInfo && paymentInfo.payment_notes">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm font-medium text-gray-800 dark:text-white">Catatan Pembayaran</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="paymentInfo.payment_notes"></p>
                    </div>
                </template>
            </div>
            <div x-show="!paymentInfo" class="text-center text-gray-500 dark:text-gray-400 py-4">
                <p>Informasi rekening pembayaran belum diatur untuk RT Anda.</p>
            </div>
        </div>

        <!-- Ringkasan Bulan Ini Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Ringkasan Bulan Ini</h3>
                <button @click="loadMonthlySummary()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                </button>
            </div>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Pemasukan:</span>
                    <span class="font-medium text-green-600" x-text="formatRupiah(monthlySummary.income)">Rp 0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Pengeluaran:</span>
                    <span class="font-medium text-red-600" x-text="formatRupiah(monthlySummary.expenses)">Rp 0</span>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                    <div class="flex justify-between items-center font-bold">
                        <span class="text-gray-800 dark:text-white">Saldo Bersih:</span>
                        <span :class="monthlySummary.netBalance >= 0 ? 'text-green-700' : 'text-red-700'" x-text="formatRupiah(monthlySummary.netBalance)">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pembayaran Terbaru Card (replacing Recent Activities) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Pembayaran Terbaru</h3>
            <button @click="loadRecentPayments()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
            </button>
        </div>
        
        <div class="space-y-4 max-h-96 overflow-y-auto">
            <template x-for="payment in recentPayments" :key="payment.id">
                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div :class="{
                        'bg-green-100 text-green-600': payment.status === 'lunas',
                        'bg-yellow-100 text-yellow-600': payment.status === 'menunggu_konfirmasi',
                        'bg-red-100 text-red-600': payment.status === 'terlambat'
                    }" class="p-2 rounded-lg">
                        <i :data-lucide="payment.status === 'lunas' ? 'check-circle' : (payment.status === 'menunggu_konfirmasi' ? 'clock' : 'alert-triangle')" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="payment.description"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatTime(payment.timestamp)"></p>
                    </div>
                    <div class="text-sm font-semibold" :class="{
                        'text-green-600': payment.status === 'lunas',
                        'text-yellow-600': payment.status === 'menunggu_konfirmasi',
                        'text-red-600': payment.status === 'terlambat'
                    }" x-text="formatRupiah(payment.amount)"></div>
                </div>
            </template>
            <div x-show="recentPayments.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                <p>Belum ada pembayaran terbaru.</p>
            </div>
        </div>
    </div>

    <!-- Monthly Kas Data Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Data Kas Bulanan</h3>
        <canvas id="monthlyKasChart"></canvas>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('kk.index') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl hover:from-blue-100 hover:to-blue-200 dark:hover:from-blue-800/30 dark:hover:to-blue-700/30 transition-all duration-200 transform hover:scale-105 border border-blue-200 dark:border-blue-700">
                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mb-3">
                    <i data-lucide="home" class="w-5 h-5 text-white"></i>
                </div>
                <span class="text-xs font-semibold text-blue-700 dark:text-blue-300 text-center">Kelola KK</span>
            </a>
            <a href="{{ route('penduduk.index') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl hover:from-green-100 hover:to-green-200 dark:hover:from-green-800/30 dark:hover:to-green-700/30 transition-all duration-200 transform hover:scale-105 border border-green-200 dark:border-green-700">
                <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center mb-3">
                    <i data-lucide="users" class="w-5 h-5 text-white"></i>
                </div>
                <span class="text-xs font-semibold text-green-700 dark:text-green-300 text-center">Kelola Penduduk</span>
            </a>
            <a href="{{ route('kas.index') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl hover:from-purple-100 hover:to-purple-200 dark:hover:from-purple-800/30 dark:hover:to-purple-700/30 transition-all duration-200 transform hover:scale-105 border border-purple-200 dark:border-purple-700">
                <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center mb-3">
                    <i data-lucide="wallet" class="w-5 h-5 text-white"></i>
                </div>
                <span class="text-xs font-semibold text-purple-700 dark:text-purple-300 text-center">Kelola Kas</span>
            </a>
            <a href="{{ route('payments.list') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-xl hover:from-orange-100 hover:to-orange-200 dark:hover:from-orange-800/30 dark:hover:to-orange-700/30 transition-all duration-200 transform hover:scale-105 border border-orange-200 dark:border-orange-700">
                <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center mb-3">
                    <i data-lucide="receipt" class="w-5 h-5 text-white"></i>
                </div>
                <span class="text-xs font-semibold text-orange-700 dark:text-orange-300 text-center">Konfirmasi Pembayaran</span>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

function rtDashboardData() {
    return {
        currentDate: '',
        rtNumber: 'Loading...',
        rtId: null, // Initialize rtId
        totalKk: 0,
        totalPenduduk: 0,
        kasLunas: 0,
        kasBelumBayar: 0,
        monthlyKasChart: null,
        paymentInfo: null, // For RT's bank account details
        monthlySummary: { // New: For monthly financial summary
            income: 0,
            expenses: 0,
            netBalance: 0
        },
        recentPayments: [], // New: For recent payment transactions
        isOnline: true,
        connectionStatus: 'online',
        refreshInterval: null,

        async initDashboard() {
            console.log('🚀 Initializing RT Dashboard...');
            this.currentDate = new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            this.setupEventListeners();
            // Ensure loadDashboardData completes first to get rtId
            await this.loadDashboardData(); 
            await Promise.all([
                this.loadPaymentInfo(), // Now rtId should be available
                this.loadMonthlySummary(),
                this.loadRecentPayments(),
                this.loadMonthlyKasData()
            ]);
            this.startAutoRefresh();

            setTimeout(() => {
                lucide.createIcons();
            }, 100);
            
            console.log('✅ RT Dashboard initialized successfully');
        },

        async loadAllData() {
            // This function is now redundant as initDashboard handles the sequence
            // but keeping it for consistency if other parts of the app call it.
            await this.loadDashboardData(); 
            await Promise.all([
                this.loadPaymentInfo(),
                this.loadMonthlySummary(),
                this.loadRecentPayments(),
                this.loadMonthlyKasData()
            ]);
        },

        setupEventListeners() {
            window.addEventListener('dataRefresh', () => {
                this.loadAllData();
            });

            window.addEventListener('online', () => {
                this.isOnline = true;
                this.connectionStatus = 'online';
                this.hideConnectionError();
                this.loadAllData();
            });

            window.addEventListener('offline', () => {
                this.isOnline = false;
                this.connectionStatus = 'offline';
                this.showConnectionError('Koneksi internet terputus');
            });
        },

        startAutoRefresh() {
            this.refreshInterval = setInterval(() => {
                if (this.isOnline) {
                    this.loadDashboardData();
                    this.loadPaymentInfo();
                    this.loadMonthlySummary();
                    this.loadRecentPayments();
                    this.loadMonthlyKasData();
                }
            }, 30000); // Refresh every 30 seconds
        },

        async loadDashboardData() {
            try {
                console.log('📊 Loading RT dashboard data...');
                const response = await fetch('/api/dashboard/stats', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.rtNumber = data.data.rtNumber || 'N/A';
                        this.rtId = data.data.rtId; // Capture the actual RT ID
                        this.totalKk = data.data.totalKk || 0;
                        this.totalPenduduk = data.data.totalPenduduk || 0;
                        this.kasLunas = data.data.kasLunas || 0;
                        this.kasBelumBayar = data.data.kasBelumBayar || 0;
                        this.isOnline = true;
                        this.connectionStatus = 'online';
                        console.log('✅ RT data loaded successfully:', data.data);
                    } else {
                        throw new Error(data.message || 'Gagal memuat data');
                    }
                } else {
                    throw new Error('Response tidak OK: ' + response.status);
                }
            } catch (error) {
                console.error('❌ Error loading RT dashboard data:', error);
                this.isOnline = false;
                this.connectionStatus = 'offline';
                this.showConnectionError('Gagal memuat data dashboard');
            }
        },

        async loadPaymentInfo() {
            try {
                console.log('💳 Loading RT payment info...');
                // Changed the API endpoint to the correct one for user's RT payment info
                const paymentInfoResponse = await fetch('/api/payment-info/for-user-rt', { 
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (paymentInfoResponse.ok) {
                    const paymentInfoData = await paymentInfoResponse.json();
                    if (paymentInfoData.success) {
                        this.paymentInfo = paymentInfoData.data;
                        console.log('✅ RT Payment info loaded successfully:', paymentInfoData.data);
                    } else {
                        this.paymentInfo = null;
                        console.warn('⚠️ No RT payment info found:', paymentInfoData.message);
                    }
                } else {
                    this.paymentInfo = null;
                    console.error('❌ Failed to load RT payment info: HTTP ' + paymentInfoResponse.status);
                }
            } catch (error) {
                this.paymentInfo = null;
                console.error('❌ Error loading RT payment info:', error);
            }
        },

        async loadMonthlySummary() {
            try {
                console.log('💰 Loading monthly summary...');
                const response = await fetch('/api/dashboard/monthly-summary', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.monthlySummary.income = data.data.income || 0;
                        this.monthlySummary.expenses = data.data.expenses || 0;
                        this.monthlySummary.netBalance = data.data.netBalance || 0;
                        console.log('✅ Monthly summary loaded successfully:', data.data);
                    } else {
                        throw new Error(data.message || 'Gagal memuat ringkasan bulanan');
                    }
                } else {
                    throw new Error('Response tidak OK: ' + response.status);
                }
            } catch (error) {
                console.error('❌ Error loading monthly summary:', error);
                this.showConnectionError('Gagal memuat ringkasan bulanan');
            }
        },

        async loadRecentPayments() {
            try {
                console.log('💸 Loading recent payments...');
                const response = await fetch('/api/dashboard/recent-payments?limit=5', { // Limit to 5 recent payments
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.recentPayments = data.data.map(payment => ({
                            ...payment,
                            description: payment.description || `Pembayaran Kas Minggu ke-${payment.minggu_ke} Tahun ${payment.tahun}`,
                            timestamp: payment.timestamp || new Date().toISOString(), // Fallback if timestamp is missing
                            amount: payment.amount || 0,
                            status: payment.status || 'unknown'
                        }));
                        console.log('✅ Recent payments loaded successfully:', data.data);
                    } else {
                        throw new Error(data.message || 'Gagal memuat pembayaran terbaru');
                    }
                } else {
                    throw new Error('Response tidak OK: ' + response.status);
                }
            } catch (error) {
                console.error('❌ Error loading recent payments:', error);
                this.showConnectionError('Gagal memuat pembayaran terbaru');
            }
        },

        async loadMonthlyKasData() {
            try {
                console.log('📈 Loading monthly kas data...');
                const response = await fetch('/api/dashboard/monthly-kas', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.renderMonthlyKasChart(data.data);
                        console.log('✅ Monthly kas data loaded successfully:', data.data);
                    } else {
                        throw new Error(data.message || 'Gagal memuat data kas bulanan');
                    }
                } else {
                    throw new Error('Response tidak OK: ' + response.status);
                }
            } catch (error) {
                console.error('❌ Error loading monthly kas data:', error);
                this.showConnectionError('Gagal memuat data kas bulanan');
            }
        },

        renderMonthlyKasChart(chartData) {
            const ctx = document.getElementById('monthlyKasChart').getContext('2d');
            if (this.monthlyKasChart) {
                this.monthlyKasChart.destroy();
            }
            this.monthlyKasChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Jumlah Kas Terkumpul (Rp)',
                        data: chartData.values,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        },

        showConnectionError(message) {
            this.hideConnectionError();
            const errorDiv = document.createElement('div');
            errorDiv.id = 'connection-error';
            errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            errorDiv.innerHTML = `
                <div class="flex items-center">
                    <i data-lucide="wifi-off" class="w-4 h-4 mr-2"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(errorDiv);
            setTimeout(() => {
                this.hideConnectionError();
            }, 5000);
        },

        hideConnectionError() {
            const errorDiv = document.getElementById('connection-error');
            if (errorDiv) {
                errorDiv.remove();
            }
        },
        
        formatTime(timestamp) {
            return new Date(timestamp).toLocaleString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                day: '2-digit',
                month: 'short'
            });
        },

        formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        },

        // Cleanup on component destroy
        destroy() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
            }
            if (this.monthlyKasChart) {
                this.monthlyKasChart.destroy();
            }
        }
    }
}
</script>
@endpush
@endsection
