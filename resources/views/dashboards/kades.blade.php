@extends('layouts.app')

@section('title', 'Dashboard Kepala Desa')
@section('page-title', 'Dashboard Kepala Desa')
@section('page-description', 'Selamat datang, ' . auth()->user()->name . '! Kelola desa Anda dengan bijak.')

@section('content')
<div x-data="kadesDashboardData()" x-init="initDashboard()" class="p-6 space-y-6">
    <!-- Kades Info Header -->
    <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md bg-gradient-to-r from-green-600 to-teal-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold" x-text="desaName">Desa Sukamaju</h2>
                <p class="text-green-100" x-text="wilayahLengkap">Kecamatan Sukamaju, Kabupaten Bogor</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-green-100">Saldo Kas Desa</p>
                <p class="text-3xl font-bold" x-text="formatCurrency(saldoDesa)">Rp 0</p>
                <button @click="refreshSaldo()" class="text-xs text-green-200 hover:text-white mt-1">
                    <i data-lucide="refresh-cw" class="w-3 h-3 inline mr-1"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Saldo Management Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Saldo Desa -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Saldo Kas Desa</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(saldoDesa)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium">+5.2%</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">dana operasional</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i data-lucide="landmark" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Bantuan Bulan Ini -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bantuan Bulan Ini</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(bantuanBulanIni)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-blue-600 font-medium">Sudah Dicairkan</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i data-lucide="hand-heart" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Saldo Tersedia -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Saldo Tersedia</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(saldoTersedia)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-purple-600 font-medium">Untuk Bantuan</span>
                    </div>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-xl">
                    <i data-lucide="wallet" class="w-8 h-8 text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total RW -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total RW</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatNumber(totalRw)">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-blue-600 font-medium">Rukun Warga</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i data-lucide="map-pin" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Total RT -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total RT</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatNumber(totalRt)">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium">Rukun Tetangga</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i data-lucide="home" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Penduduk -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Penduduk</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatNumber(totalPenduduk)">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-purple-600 font-medium">+1.8%</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">jiwa</span>
                    </div>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-xl">
                    <i data-lucide="users" class="w-8 h-8 text-purple-600 dark:text-purple-400"></i>
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
                        <span class="text-sm text-orange-600 font-medium">Menunggu Approval</span>
                    </div>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-xl">
                    <i data-lucide="clock" class="w-8 h-8 text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bantuan Pending Alert -->
    <div x-show="bantuanPending > 0" :class="getCardClass()" class="p-4 rounded-xl shadow-sm border border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/20">
        <div class="flex items-center">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3"></i>
            <div>
                <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Pengajuan Bantuan Menunggu</h4>
                <p class="text-sm text-yellow-700 dark:text-yellow-300" x-text="`Ada ${bantuanPending} pengajuan bantuan yang menunggu persetujuan Anda.`"></p>
            </div>
            <button class="ml-auto bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-700 transition-colors">
                Review Sekarang
            </button>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pengajuan Bantuan -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Pengajuan Bantuan Terbaru</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Daftar pengajuan yang perlu ditinjau</p>
                </div>
                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Lihat Semua
                </button>
            </div>
            
            <div class="space-y-4">
                <template x-for="bantuan in pengajuanBantuan" :key="bantuan.id">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <i data-lucide="hand-heart" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="bantuan.rw"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatCurrency(bantuan.jumlah)"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="bantuan.tanggal"></p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 transition-colors">
                                Setujui
                            </button>
                            <button class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700 transition-colors">
                                Tolak
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Chart Bantuan Bulanan -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Bantuan Bulanan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tren bantuan 6 bulan terakhir</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="refreshChart('bantuan')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="bantuanChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <i data-lucide="hand-heart" class="w-8 h-8 text-green-600 mb-2"></i>
                <span class="text-sm font-medium text-green-600">Review Bantuan</span>
            </button>
            <button class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                <i data-lucide="users" class="w-8 h-8 text-blue-600 mb-2"></i>
                <span class="text-sm font-medium text-blue-600">Data Penduduk</span>
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
function kadesDashboardData() {
    return {
        desaName: 'Desa Sukamaju',
        wilayahLengkap: 'Kecamatan Sukamaju, Kabupaten Bogor',
        saldoDesa: 0,
        bantuanBulanIni: 0,
        saldoTersedia: 0,
        totalRw: 0,
        totalRt: 0,
        totalPenduduk: 0,
        bantuanPending: 0,
        pengajuanBantuan: [],
        charts: {
            bantuan: null
        },
        
        // Settings
        cardStyle: localStorage.getItem('cardStyle') || 'default',
        chartTheme: localStorage.getItem('chartTheme') || 'default',
        isDarkMode: localStorage.getItem('darkMode') === 'true',
        
        async initDashboard() {
            console.log('🚀 Initializing Kades Dashboard...');
            
            this.setupEventListeners();
            await this.loadDashboardData();
            
            setTimeout(() => {
                this.initializeCharts();
                lucide.createIcons();
            }, 100);
            
            console.log('✅ Kades Dashboard initialized successfully');
        },
        
        setupEventListeners() {
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
            
            window.addEventListener('dataRefresh', () => {
                this.loadDashboardData();
            });
        },
        
        async loadDashboardData() {
            try {
                console.log('📊 Loading kades dashboard data...');
                
                // Mock data - replace with actual API calls
                this.saldoDesa = 25000000;
                this.bantuanBulanIni = 5000000;
                this.saldoTersedia = this.saldoDesa - this.bantuanBulanIni;
                this.totalRw = 5;
                this.totalRt = 25;
                this.totalPenduduk = 2500;
                this.bantuanPending = 3;
                
                this.pengajuanBantuan = [
                    {
                        id: 1,
                        rw: 'RW 01 - Sukamaju',
                        jumlah: 2000000,
                        tanggal: '15 Jan 2024'
                    },
                    {
                        id: 2,
                        rw: 'RW 03 - Makmur',
                        jumlah: 1500000,
                        tanggal: '14 Jan 2024'
                    },
                    {
                        id: 3,
                        rw: 'RW 05 - Sejahtera',
                        jumlah: 3000000,
                        tanggal: '13 Jan 2024'
                    }
                ];
                
                console.log('✅ Kades data loaded successfully');
            } catch (error) {
                console.error('❌ Error loading kades dashboard data:', error);
                if (window.showNotification) {
                    window.showNotification('Gagal memuat data dashboard', 'error');
                }
            }
        },
        
        async refreshSaldo() {
            try {
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
            this.initBantuanChart();
        },
        
        initBantuanChart() {
            const ctx = document.getElementById('bantuanChart');
            if (!ctx) return;
            
            if (this.charts.bantuan) {
                this.charts.bantuan.destroy();
            }
            
            this.charts.bantuan = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Bantuan (Rp)',
                        data: [3000000, 4500000, 2000000, 6000000, 3500000, 5000000],
                        backgroundColor: this.getChartColors().primary,
                        borderColor: this.getChartColors().primary,
                        borderWidth: 1,
                        borderRadius: 8
                    }]
                },
                options: this.getChartOptions()
            });
        },
        
        getChartColors() {
            const themes = {
                default: {
                    primary: '#10B981'
                },
                ocean: {
                    primary: '#0EA5E9'
                },
                forest: {
                    primary: '#10B981'
                },
                sunset: {
                    primary: '#F59E0B'
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
                this.initBantuanChart();
                
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
