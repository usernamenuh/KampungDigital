@extends('layouts.app')

@section('title', 'Dashboard Masyarakat')
@section('page-title', 'Dashboard Masyarakat')
@section('page-description', 'Selamat datang, ' . auth()->user()->name . '! Kelola tagihan Kas Anda.')

@section('content')
<div x-data="masyarakatDashboardData()" x-init="initDashboard()" class="p-6 space-y-6">
    <!-- Payment Alerts Section -->
    <div x-show="paymentAlerts.length > 0" class="space-y-3">
        <template x-for="alert in paymentAlerts" :key="alert.id">
            <div :class="{
                'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800': alert.type === 'error',
                'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-800': alert.type === 'warning',
                'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800': alert.type === 'info'
            }" class="p-4 rounded-xl shadow-sm border transition-all">
                <div class="flex items-start">
                    <div :class="{
                        'text-red-600': alert.type === 'error',
                        'text-yellow-600': alert.type === 'warning',
                        'text-blue-600': alert.type === 'info'
                    }" class="flex-shrink-0 mr-3 mt-0.5">
                        <i :data-lucide="alert.is_overdue ? 'alert-circle' : 'clock'" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-1">
                        <h4 :class="{
                            'text-red-800 dark:text-red-200': alert.type === 'error',
                            'text-yellow-800 dark:text-yellow-200': alert.type === 'warning',
                            'text-blue-800 dark:text-blue-200': alert.type === 'info'
                        }" class="text-sm font-medium" x-text="alert.title"></h4>
                        <p :class="{
                            'text-red-700 dark:text-red-300': alert.type === 'error',
                            'text-yellow-700 dark:text-yellow-300': alert.type === 'warning',
                            'text-blue-700 dark:text-blue-300': alert.type === 'info'
                        }" class="text-sm mt-1" x-text="alert.message"></p>
                        <div class="mt-2 text-xs" :class="{
                            'text-red-600 dark:text-red-400': alert.type === 'error',
                            'text-yellow-600 dark:text-yellow-400': alert.type === 'warning',
                            'text-blue-600 dark:text-blue-400': alert.type === 'info'
                        }">
                            <span>Jumlah: </span><span x-text="formatCurrency(alert.total_bayar)"></span>
                            <span class="mx-2">•</span>
                            <span>Jatuh Tempo: </span><span x-text="alert.tanggal_jatuh_tempo"></span>
                        </div>
                    </div>
                    <a :href="alert.payment_url" :class="{
                        'bg-red-600 hover:bg-red-700': alert.type === 'error',
                        'bg-yellow-600 hover:bg-yellow-700': alert.type === 'warning',
                        'bg-blue-600 hover:bg-blue-700': alert.type === 'info'
                    }" class="ml-4 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Bayar Sekarang
                    </a>
                </div>
            </div>
        </template>
    </div>

    <!-- Masyarakat Info Header -->
    <div class="p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 transition-all hover:shadow-lg bg-gradient-to-r from-blue-500 to-purple-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Selamat datang, Masyarakat</h2>
                <p class="text-blue-100">NIK: <span x-text="userNik"></span></p>
                <p class="text-blue-100">Dashboard Keluarga Kas Anda</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-blue-100" x-text="currentDate"></p>
                <div class="flex items-center justify-end mt-2">
                    <div :class="isOnline ? 'bg-green-400 animate-pulse' : 'bg-red-400'" class="w-2 h-2 rounded-full mr-2"></div>
                    <span class="text-sm text-blue-100" x-text="isOnline ? 'Online' : 'Offline'">Online</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kas Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Kas Lunas -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Lunas</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="kasLunas">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium">Tahun ini</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i data-lucide="check-circle" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Belum Bayar -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Belum Bayar</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="kasBelumBayar">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-yellow-600 font-medium">Segera bayar</span>
                    </div>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-xl">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-yellow-600 dark:text-yellow-400"></i>
                </div>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Terlambat</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="kasTerlambat">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-red-600 font-medium">Perlu tindakan</span>
                    </div>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-xl">
                    <i data-lucide="clock" class="w-8 h-8 text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Kas Anda -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Kas Anda</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(totalKasAnda)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-purple-600 font-medium">Tahun ini</span>
                    </div>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-xl">
                    <i data-lucide="wallet" class="w-8 h-8 text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert for incomplete year -->
    <div x-show="!isYearCompleted" class="p-4 rounded-xl shadow-sm border border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/20">
        <div class="flex items-center">
            <i data-lucide="info" class="w-5 h-5 text-yellow-600 mr-3"></i>
            <div>
                <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Tahun Belum Selesai</h4>
                <p class="text-sm text-yellow-700 dark:text-yellow-300">Anda belum menyelesaikan pembayaran kas untuk semua minggu tahun ini.</p>
            </div>
            <a href="{{ route('kas.index') }}" class="ml-auto bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-700 transition-colors">
                Lihat Tagihan
            </a>
        </div>
    </div>

    <!-- Payment Information, Daftar Tagihan, and Aktivitas Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Informasi Pembayaran Kas -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Informasi Pembayaran Kas</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Metode pembayaran yang tersedia dari RT Anda.</p>
                </div>
                <button @click="loadPaymentInfo()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                </button>
            </div>
            
            <div x-show="paymentInfo" class="space-y-4">
                <template x-if="paymentInfo && paymentInfo.bank_transfer && paymentInfo.bank_transfer.account_number">
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <i data-lucide="banknote" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">Transfer Bank</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="paymentInfo.bank_transfer.bank_name + ' - ' + paymentInfo.bank_transfer.account_number"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'A/N: ' + paymentInfo.bank_transfer.account_name"></p>
                        </div>
                    </div>
                </template>
                <template x-if="paymentInfo && paymentInfo.e_wallet && (paymentInfo.e_wallet.dana || paymentInfo.e_wallet.ovo || paymentInfo.e_wallet.gopay)">
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <i data-lucide="wallet" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">E-Wallet</p>
                            <template x-if="paymentInfo.e_wallet.dana">
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'DANA: ' + paymentInfo.e_wallet.dana"></p>
                            </template>
                            <template x-if="paymentInfo.e_wallet.ovo">
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'OVO: ' + paymentInfo.e_wallet.ovo"></p>
                            </template>
                            <template x-if="paymentInfo.e_wallet.gopay">
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'GOPAY: ' + paymentInfo.e_wallet.gopay"></p>
                            </template>
                        </div>
                    </div>
                </template>
                <template x-if="paymentInfo && paymentInfo.qr_code && paymentInfo.qr_code.image_url">
                    <div class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm font-medium text-gray-800 dark:text-white mb-2">QR Code Pembayaran</p>
                        <img :src="paymentInfo.qr_code.image_url" alt="QR Code Pembayaran" class="w-32 h-32 object-contain mb-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center" x-text="paymentInfo.qr_code.description"></p>
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
                <p>Informasi pembayaran belum tersedia.</p>
            </div>
        </div>

        <!-- Daftar Tagihan Kas Anda - Enhanced with real-time updates -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Daftar Kas</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tagihan kas yang belum lunas.</p>
                </div>
                <button @click="loadUserKasBills()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                </button>
            </div>
            
            <div class="space-y-4 max-h-96 overflow-y-auto">
                <template x-for="bill in userKasBills" :key="bill.id">
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div :class="{
                            'bg-yellow-100 text-yellow-600': bill.status === 'belum_bayar' || bill.status === 'menunggu_konfirmasi',
                            'bg-red-100 text-red-600': bill.status === 'terlambat',
                            'bg-green-100 text-green-600': bill.status === 'lunas'
                        }" class="p-2 rounded-lg">
                            <i :data-lucide="bill.status === 'lunas' ? 'check-circle' : (bill.status === 'terlambat' ? 'clock' : 'alert-triangle')" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">
                                Tagihan Minggu ke-<span x-text="bill.minggu_ke"></span> Tahun <span x-text="bill.tahun"></span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Jumlah: <span x-text="formatCurrency(bill.jumlah)"></span>
                                <template x-if="bill.denda > 0">
                                    (Denda: <span x-text="formatCurrency(bill.denda)"></span>)
                                </template>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Status: <span x-text="bill.status_text"></span>
                                <template x-if="bill.tanggal_jatuh_tempo_formatted">
                                    (Jatuh Tempo: <span x-text="bill.tanggal_jatuh_tempo_formatted"></span>)
                                </template>
                            </p>
                        </div>
                        <a :href="'{{ route('kas.payment.form', ['kas' => 'PLACEHOLDER_KAS_ID']) }}'.replace('PLACEHOLDER_KAS_ID', bill.id)" x-show="bill.can_pay" class="bg-blue-600 text-white px-3 py-1 rounded-md text-xs hover:bg-blue-700 transition-colors">
                            Bayar
                        </a>
                    </div>
                </template>
                <div x-show="userKasBills.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                    <p>Tidak ada tagihan kas yang belum lunas.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('kas.index') }}" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                <i data-lucide="wallet" class="w-8 h-8 text-blue-600 mb-2"></i>
                <span class="text-sm font-medium text-blue-600">Lihat Tagihan Kas</span>
            </a>
            <a href="{{ route('notifikasi.index') }}" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <i data-lucide="bell" class="w-8 h-8 text-green-600 mb-2"></i>
                <span class="text-sm font-medium text-green-600">Notifikasi Anda</span>
            </a>
            <a href="{{ route('profile.index') }}" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                <i data-lucide="user" class="w-8 h-8 text-purple-600 mb-2"></i>
                <span class="text-sm font-medium text-purple-600">Profil Saya</span>
            </a>
            <button @click="refreshAllData()" class="flex flex-col items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                <i data-lucide="refresh-cw" class="w-8 h-8 text-orange-600 mb-2"></i>
                <span class="text-sm font-medium text-orange-600">Refresh Data</span>
            </button>
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

function masyarakatDashboardData() {
    return {
        userNik: 'Loading...',
        rtRw: 'Loading...', // Added rtRw property
        currentDate: '',
        kasLunas: 0,
        kasBelumBayar: 0,
        kasTerlambat: 0,
        kasMenungguKonfirmasi: 0,
        totalKasAnda: 0,
        isYearCompleted: false,
        notifikasiUnread: 0,
        activities: [],
        paymentInfo: null,
        userKasBills: [],
        paymentAlerts: [],
        isOnline: true,
        connectionStatus: 'online',
        refreshInterval: null,

        async initDashboard() {
            console.log('🚀 Initializing Masyarakat Dashboard...');
            this.currentDate = new Date().toLocaleDateString('id-ID', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            this.setupEventListeners();
            await this.loadAllData();
            this.startAutoRefresh();

            setTimeout(() => {
                lucide.createIcons();
            }, 100);
            
            console.log('✅ Masyarakat Dashboard initialized successfully');
        },

        async loadAllData() {
            await Promise.all([
                this.loadDashboardData(),
                this.loadActivities(),
                this.loadPaymentInfo(),
                this.loadUserKasBills(),
                this.loadPaymentAlerts()
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

            // Listen for payment completion events
            window.addEventListener('paymentCompleted', (event) => {
                this.loadAllData();
                this.showSuccessMessage('Pembayaran berhasil diproses!');
            });
        },

        startAutoRefresh() {
            // Auto refresh every 30 seconds
            this.refreshInterval = setInterval(() => {
                if (this.isOnline) {
                    this.loadDashboardData();
                    this.loadPaymentAlerts();
                    this.loadUserKasBills();
                }
            }, 30000);
        },

        async refreshAllData() {
            console.log('🔄 Refreshing all dashboard data...');
            await this.loadAllData();
            this.showSuccessMessage('Data berhasil diperbarui!');
        },

        async loadDashboardData() {
            try {
                console.log('📊 Loading masyarakat dashboard data...');
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
                        Object.assign(this, data.data);
                        this.userNik = data.data.userNik || 'N/A';
                        this.rtRw = data.data.rtRw || 'N/A'; // Set rtRw from loaded data
                        this.isOnline = true;
                        this.connectionStatus = 'online';
                        console.log('✅ Masyarakat data loaded successfully:', data.data);
                    } else {
                        throw new Error(data.message || 'Gagal memuat data');
                    }
                } else {
                    throw new Error('Response tidak OK: ' + response.status);
                }
            } catch (error) {
                console.error('❌ Error loading masyarakat dashboard data:', error);
                this.isOnline = false;
                this.connectionStatus = 'offline';
                this.showConnectionError('Gagal memuat data dashboard');
            }
        },

        async loadActivities() {
            try {
                const response = await fetch('/api/dashboard/activities?limit=10', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.activities = data.data.map(activity => ({
                            ...activity,
                            icon: activity.icon || 'activity',
                            color: activity.color || 'blue'
                        }));
                    }
                }
            } catch (error) {
                console.error('Error loading activities:', error);
            }
        },

        async loadPaymentInfo() {
            try {
                console.log('💳 Loading payment info...');
                const response = await fetch('/api/payment-info/for-user-rt', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.paymentInfo = data.data;
                        console.log('✅ Payment info loaded successfully:', data.data);
                    } else {
                        this.paymentInfo = null;
                        console.warn('⚠️ No payment info found:', data.message);
                    }
                } else {
                    this.paymentInfo = null;
                    console.error('❌ Failed to load payment info: HTTP ' + response.status);
                }
            } catch (error) {
                this.paymentInfo = null;
                console.error('❌ Error loading payment info:', error);
            }
        },

        async loadUserKasBills() {
            try {
                console.log('🧾 Loading user kas bills...');
                const response = await fetch('/api/payment/index?status=belum_bayar,terlambat,menunggu_konfirmasi', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.userKasBills = data.data;
                        console.log('✅ User kas bills loaded successfully:', data.data);
                        
                        // Trigger event for other components
                        window.dispatchEvent(new CustomEvent('kasBillsUpdated', {
                            detail: { bills: this.userKasBills }
                        }));
                    } else {
                        this.userKasBills = [];
                        console.warn('⚠️ No user kas bills found:', data.message);
                    }
                } else {
                    this.userKasBills = [];
                    console.error('❌ Failed to load user kas bills: HTTP ' + response.status);
                }
            } catch (error) {
                this.userKasBills = [];
                console.error('❌ Error loading user kas bills:', error);
            }
        },

        async loadPaymentAlerts() {
            try {
                console.log('🚨 Loading payment alerts...');
                const response = await fetch('/api/dashboard/payment-alerts', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.paymentAlerts = data.data;
                        console.log('✅ Payment alerts loaded successfully:', data.data);
                        
                        // Show browser notification for overdue payments
                        if (data.has_overdue && 'Notification' in window) {
                            this.showBrowserNotification();
                        }
                    } else {
                        this.paymentAlerts = [];
                        console.warn('⚠️ No payment alerts found:', data.message);
                    }
                } else {
                    this.paymentAlerts = [];
                    console.error('❌ Failed to load payment alerts: HTTP ' + response.status);
                }
            } catch (error) {
                this.paymentAlerts = [];
                console.error('❌ Error loading payment alerts:', error);
            }
        },

        showBrowserNotification() {
            if (Notification.permission === 'granted') {
                new Notification('Pembayaran Kas Terlambat!', {
                    body: 'Anda memiliki pembayaran kas yang sudah terlambat. Segera lakukan pembayaran.',
                    icon: '/favicon.ico'
                });
            } else if (Notification.permission !== 'denied') {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        this.showBrowserNotification();
                    }
                });
            }
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

        showSuccessMessage(message) {
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            successDiv.innerHTML = `
                <div class="flex items-center">
                    <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(successDiv);
            setTimeout(() => {
                successDiv.remove();
            }, 3000);
        },

        hideConnectionError() {
            const errorDiv = document.getElementById('connection-error');
            if (errorDiv) {
                errorDiv.remove();
            }
        },
        
        formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        },
        
        formatTime(timestamp) {
            return new Date(timestamp).toLocaleString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                day: '2-digit',
                month: 'short'
            });
        },

        // Cleanup on component destroy
        destroy() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
            }
        }
    }
}
</script>
@endpush
@endsection
