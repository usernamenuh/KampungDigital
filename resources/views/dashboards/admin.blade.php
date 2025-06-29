@extends('layouts.app')

@section('title', 'Dashboard Administrator')
@section('page-title', 'Dashboard Administrator')
@section('page-description', 'Selamat datang, ' . auth()->user()->name . '! Kelola seluruh sistem kampung digital.')

@section('content')
<div x-data="adminDashboardData()" x-init="initDashboard()" class="p-6 space-y-6">
    <!-- Admin Info Header -->
    <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md bg-gradient-to-r from-purple-600 to-blue-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Administrator System</h2>
                <p class="text-purple-100">Sistem Manajemen Kampung Digital</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-purple-100">Total Saldo Sistem</p>
                <p class="text-3xl font-bold" x-text="formatCurrency(totalSaldoSistem)">Rp 0</p>
                <p class="text-xs text-purple-200">Desa + RW + RT</p>
                <button @click="refreshSaldo()" class="text-xs text-purple-200 hover:text-white mt-1">
                    <i data-lucide="refresh-cw" class="w-3 h-3 inline mr-1"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Saldo Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Saldo Desa -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Saldo Desa</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(totalSaldoDesa)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium" x-text="totalDesa + ' Desa'">0 Desa</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">terdaftar</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i data-lucide="landmark" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Saldo RW -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Saldo RW</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(totalSaldoRw)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-blue-600 font-medium" x-text="totalRw + ' RW'">0 RW</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">aktif</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i data-lucide="map-pin" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Saldo RT -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Saldo RT</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(totalSaldoRt)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-purple-600 font-medium" x-text="totalRt + ' RT'">0 RT</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">aktif</span>
                    </div>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-xl">
                    <i data-lucide="home" class="w-8 h-8 text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Pengguna</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatNumber(totalUsers)">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-indigo-600 font-medium">+2.5%</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">pengguna aktif</span>
                    </div>
                </div>
                <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-xl">
                    <i data-lucide="users" class="w-8 h-8 text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Kas Belum Bayar -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Belum Bayar</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(totalKasBelumBayar)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-red-600 font-medium" x-text="jumlahKasBelumBayar + ' Tagihan'">0 Tagihan</span>
                    </div>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-xl">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>

        <!-- Bantuan Pending -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bantuan Pending</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="bantuanPending">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-yellow-600 font-medium">Menunggu Approval</span>
                    </div>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-xl">
                    <i data-lucide="clock" class="w-8 h-8 text-yellow-600 dark:text-yellow-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Masyarakat -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Masyarakat</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatNumber(totalMasyarakat)">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-teal-600 font-medium">Warga Aktif</span>
                    </div>
                </div>
                <div class="p-3 bg-teal-100 dark:bg-teal-900 rounded-xl">
                    <i data-lucide="user-check" class="w-8 h-8 text-teal-600 dark:text-teal-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart Kas Bulanan -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Tren Kas Bulanan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pembayaran kas 6 bulan terakhir</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="refreshChart('kas')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="kasChart"></canvas>
            </div>
        </div>

        <!-- Recent Activities -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Aktivitas Terbaru</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Log aktivitas sistem</p>
                </div>
                <button @click="loadActivities()" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Lihat Semua
                </button>
            </div>
            
            <div class="space-y-4">
                <template x-for="activity in activities" :key="activity.id">
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div :class="{
                            'bg-green-100 text-green-600': activity.type === 'success',
                            'bg-blue-100 text-blue-600': activity.type === 'info',
                            'bg-yellow-100 text-yellow-600': activity.type === 'warning',
                            'bg-red-100 text-red-600': activity.type === 'error'
                        }" class="p-2 rounded-lg">
                            <i :data-lucide="activity.icon" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="activity.title"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.description"></p>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.time"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                <i data-lucide="users" class="w-8 h-8 text-blue-600 mb-2"></i>
                <span class="text-sm font-medium text-blue-600">Kelola Users</span>
            </button>
            <button class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <i data-lucide="map-pin" class="w-8 h-8 text-green-600 mb-2"></i>
                <span class="text-sm font-medium text-green-600">Kelola Wilayah</span>
            </button>
            <button class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                <i data-lucide="wallet" class="w-8 h-8 text-purple-600 mb-2"></i>
                <span class="text-sm font-medium text-purple-600">Kelola Kas</span>
            </button>
            <button class="flex flex-col items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                <i data-lucide="bar-chart-3" class="w-8 h-8 text-orange-600 mb-2"></i>
                <span class="text-sm font-medium text-orange-600">Laporan</span>
            </button>
        </div>
    </div>
</div>

<script>
function adminDashboardData() {
    return {
        totalSaldoDesa: 0,
        totalSaldoRw: 0,
        totalSaldoRt: 0,
        totalSaldoSistem: 0,
        totalUsers: 0,
        totalDesa: 0,
        totalRw: 0,
        totalRt: 0,
        totalMasyarakat: 0,
        totalKasBelumBayar: 0,
        jumlahKasBelumBayar: 0,
        bantuanPending: 0,
        activities: [],
        charts: {
            kas: null
        },
        
        // Settings
        cardStyle: localStorage.getItem('cardStyle') || 'default',
        chartTheme: localStorage.getItem('chartTheme') || 'default',
        isDarkMode: localStorage.getItem('darkMode') === 'true',
        
        async initDashboard() {
            console.log('🚀 Initializing Admin Dashboard...');
            
            // Setup event listeners
            this.setupEventListeners();
            
            // Load data
            await this.loadDashboardData();
            
            // Initialize charts
            setTimeout(() => {
                this.initializeCharts();
                lucide.createIcons();
            }, 100);
            
            console.log('✅ Admin Dashboard initialized successfully');
        },
        
        setupEventListeners() {
            // Listen for theme changes
            window.addEventListener('cardStyleChanged', (e) => {
                this.cardStyle = e.detail;
            });
            
            window.addEventListener('chartThemeChanged', (e) => {
                this.chartTheme = e.detail;
                this.updateAllCharts();
            });
            
            window.addEventListener('darkModeChanged', (e) => {
                this.isDarkMode = e.detail;
                this.updateAllCharts();
            });
            
            // Listen for data refresh
            window.addEventListener('dataRefresh', () => {
                this.loadDashboardData();
                this.loadActivities();
            });
        },
        
        async loadDashboardData() {
            try {
                console.log('📊 Loading admin dashboard data...');
                
                // Mock data - replace with actual API calls
                this.totalSaldoDesa = 50000000;
                this.totalSaldoRw = 25000000;
                this.totalSaldoRt = 15000000;
                this.totalSaldoSistem = this.totalSaldoDesa + this.totalSaldoRw + this.totalSaldoRt;
                this.totalUsers = 1250;
                this.totalDesa = 5;
                this.totalRw = 25;
                this.totalRt = 125;
                this.totalMasyarakat = 1200;
                this.totalKasBelumBayar = 5500000;
                this.jumlahKasBelumBayar = 45;
                this.bantuanPending = 8;
                
                this.loadActivities();
                
                console.log('✅ Admin data loaded successfully');
            } catch (error) {
                console.error('❌ Error loading admin dashboard data:', error);
                if (window.showNotification) {
                    window.showNotification('Gagal memuat data dashboard', 'error');
                }
            }
        },
        
        async refreshSaldo() {
            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 1000));
                await this.loadDashboardData();
                
                if (window.showNotification) {
                    window.showNotification('Saldo berhasil diperbarui', 'success');
                }
            } catch (error) {
                if (window.showNotification) {
                    window.showNotification('Gagal memperbarui saldo', 'error');
                }
            }
        },
        
        loadActivities() {
            this.activities = [
                {
                    id: 1,
                    title: 'Kas Dibayar',
                    description: 'Ahmad membayar kas minggu ke-12',
                    type: 'success',
                    icon: 'check-circle',
                    time: '5 menit lalu'
                },
                {
                    id: 2,
                    title: 'Bantuan Diajukan',
                    description: 'RW 03 mengajukan bantuan Rp 2.000.000',
                    type: 'info',
                    icon: 'hand-heart',
                    time: '15 menit lalu'
                },
                {
                    id: 3,
                    title: 'User Baru',
                    description: 'Siti Aminah mendaftar sebagai warga',
                    type: 'info',
                    icon: 'user-plus',
                    time: '1 jam lalu'
                },
                {
                    id: 4,
                    title: 'Backup Data',
                    description: 'Backup otomatis database berhasil',
                    type: 'success',
                    icon: 'database',
                    time: '2 jam lalu'
                }
            ];
        },
        
        // Get card class based on style
        getCardClass() {
            const baseClass = 'transition-all duration-300';
            
            switch (this.cardStyle) {
                case 'blur':
                    return `${baseClass} card-blur`;
                case 'gradient':
                    return `${baseClass} card-gradient`;
                case 'colored':
                    return `${baseClass} card-colored`;
                default:
                    return `${baseClass} card-default`;
            }
        },
        
        initializeCharts() {
            this.initKasChart();
        },
        
        initKasChart() {
            const ctx = document.getElementById('kasChart');
            if (!ctx) return;
            
            if (this.charts.kas) {
                this.charts.kas.destroy();
            }
            
            this.charts.kas = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Total Kas (Rp)',
                        data: [12000000, 15000000, 13000000, 18000000, 16000000, 20000000],
                        borderColor: this.getChartColors().primary,
                        backgroundColor: this.getChartColors().primaryAlpha,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: this.getChartOptions()
            });
        },
        
        getChartColors() {
            const themes = {
                default: {
                    primary: '#8B5CF6',
                    primaryAlpha: 'rgba(139, 92, 246, 0.1)'
                },
                ocean: {
                    primary: '#0EA5E9',
                    primaryAlpha: 'rgba(14, 165, 233, 0.1)'
                },
                forest: {
                    primary: '#10B981',
                    primaryAlpha: 'rgba(16, 185, 129, 0.1)'
                },
                sunset: {
                    primary: '#F59E0B',
                    primaryAlpha: 'rgba(245, 158, 11, 0.1)'
                }
            };
            
            return themes[this.chartTheme] || themes.default;
        },
        
        getChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000) + 'M';
                            },
                            color: this.isDarkMode ? '#9CA3AF' : '#6B7280'
                        },
                        grid: {
                            color: this.isDarkMode ? '#374151' : '#F3F4F6'
                        }
                    },
                    x: {
                        ticks: {
                            color: this.isDarkMode ? '#9CA3AF' : '#6B7280'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            };
        },
        
        updateAllCharts() {
            Object.keys(this.charts).forEach(key => {
                if (this.charts[key]) {
                    this.charts[key].destroy();
                }
            });
            
            setTimeout(() => {
                this.initializeCharts();
            }, 100);
        },
        
        async refreshChart(chartType) {
            try {
                await new Promise(resolve => setTimeout(resolve, 500));
                this.initKasChart();
                
                if (window.showNotification) {
                    window.showNotification('Chart berhasil diperbarui', 'success');
                }
            } catch (error) {
                if (window.showNotification) {
                    window.showNotification('Gagal memperbarui chart', 'error');
                }
            }
        },
        
        formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        },
        
        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num || 0);
        }
    }
}
</script>
@endsection
