@extends('layouts.app')

@section('title', 'Dashboard RW')
@section('page-title', 'Dashboard RW')
@section('page-description', 'Selamat datang, ' . auth()->user()->name . '! Kelola data RW Anda.')

@section('content')
<div x-data="rwDashboardData()" x-init="initDashboard()" class="p-6 space-y-6">
    <!-- RW Info Header -->
    <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold" x-text="rwName">RW 01</h2>
                <p class="text-blue-100" x-text="villageName">Desa Sukamaju</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-blue-100">Saldo RW</p>
                <p class="text-3xl font-bold" x-text="formatCurrency(balance)">Rp 0</p>
                <button @click="refreshBalance()" class="text-xs text-blue-200 hover:text-white mt-1">
                    <i data-lucide="refresh-cw" class="w-3 h-3 inline mr-1"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Saldo Management Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Saldo RW -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Saldo Kas RW</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(balance)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-blue-600 font-medium">+3.2%</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">dana operasional</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i data-lucide="wallet" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Kas Masuk Bulan Ini -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Masuk Bulan Ini</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(kasMasukBulanIni)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium">Dari RT</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i data-lucide="trending-up" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Bantuan Diterima -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bantuan Diterima</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(bantuanDiterima)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-purple-600 font-medium">Total</span>
                    </div>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-xl">
                    <i data-lucide="hand-heart" class="w-8 h-8 text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total RT -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total RT</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatNumber(totalRts)">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-blue-600 font-medium">Aktif</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i data-lucide="map-pin" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Total KK -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total KK</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatNumber(totalKks)">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium">Keluarga</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i data-lucide="home" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Population -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Penduduk</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatNumber(totalPopulation)">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-purple-600 font-medium">+1.5%</span>
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
                        <span class="text-sm text-orange-600 font-medium">Menunggu</span>
                    </div>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-xl">
                    <i data-lucide="clock" class="w-8 h-8 text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Population Chart -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Distribusi Penduduk per RT</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Jumlah penduduk di setiap RT</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="refreshChart('population')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="populationChart"></canvas>
            </div>
        </div>

        <!-- Recent Activities -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Aktivitas Terbaru</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Log aktivitas RW</p>
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
                <i data-lucide="hand-heart" class="w-8 h-8 text-blue-600 mb-2"></i>
                <span class="text-sm font-medium text-blue-600">Ajukan Bantuan</span>
            </button>
            <a href="{{ route('penduduk.index') }}" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <i data-lucide="users" class="w-8 h-8 text-green-600 mb-2"></i>
                <span class="text-sm font-medium text-green-600">Data Warga</span>
            </a>
            <a href="{{ route('rt-rw.index') }}" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                <i data-lucide="map-pin" class="w-8 h-8 text-purple-600 mb-2"></i>
                <span class="text-sm font-medium text-purple-600">Kelola RT</span>
            </a>
            <button class="flex flex-col items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                <i data-lucide="bar-chart-3" class="w-8 h-8 text-orange-600 mb-2"></i>
                <span class="text-sm font-medium text-orange-600">Laporan</span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function rwDashboardData() {
    return {
        rwName: 'RW 01',
        villageName: 'Desa Sukamaju',
        balance: 0,
        kasMasukBulanIni: 0,
        bantuanDiterima: 0,
        totalRts: 0,
        totalKks: 0,
        totalPopulation: 0,
        bantuanPending: 0,
        activities: [],
        rtData: [],
        charts: {
            population: null
        },
        
        // Settings
        cardStyle: localStorage.getItem('cardStyle') || 'default',
        chartTheme: localStorage.getItem('chartTheme') || 'default',
        isDarkMode: localStorage.getItem('darkMode') === 'true',
        
        async initDashboard() {
            console.log('🚀 Initializing RW Dashboard...');
            
            this.setupEventListeners();
            await this.loadDashboardData();
            
            setTimeout(() => {
                this.initializeCharts();
                lucide.createIcons();
            }, 100);
            
            console.log('✅ RW Dashboard initialized successfully');
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
                this.loadActivities();
            });
        },
        
        async loadDashboardData() {
            try {
                console.log('📊 Loading RW dashboard data...');
                
                // LOAD REAL DATA FROM API
                const response = await fetch('/api/dashboard/stats', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        // Assign real data
                        Object.assign(this, data.data);
                        this.loadActivities();
                        console.log('✅ RW data loaded successfully:', data.data);
                    } else {
                        throw new Error(data.message || 'Failed to load data');
                    }
                } else {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
            } catch (error) {
                console.error('❌ Error loading RW dashboard data:', error);
                if (window.showNotification) {
                    window.showNotification('Gagal memuat data dashboard: ' + error.message, 'error');
                }
            }
        },
        
        async refreshBalance() {
            try {
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
                    title: 'Kas Diterima',
                    description: 'Kas dari RT 03 sebesar Rp 500.000',
                    type: 'success',
                    icon: 'trending-up',
                    time: '10 menit lalu'
                },
                {
                    id: 2,
                    title: 'Bantuan Disetujui',
                    description: 'Bantuan Rp 2.000.000 telah disetujui',
                    type: 'success',
                    icon: 'check-circle',
                    time: '2 jam lalu'
                },
                {
                    id: 3,
                    title: 'Data RT Diperbarui',
                    description: 'RT 02 memperbarui data warga',
                    type: 'info',
                    icon: 'edit',
                    time: '1 hari lalu'
                },
                {
                    id: 4,
                    title: 'Pengajuan Bantuan',
                    description: 'Pengajuan bantuan baru dari RT 04',
                    type: 'info',
                    icon: 'hand-heart',
                    time: '2 hari lalu'
                }
            ];
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
            this.initPopulationChart();
        },
        
        initPopulationChart() {
            const ctx = document.getElementById('populationChart');
            if (!ctx) return;
            
            if (this.charts.population) {
                this.charts.population.destroy();
            }
            
            // Use real data if available
            const labels = this.rtData && this.rtData.length > 0 
                ? this.rtData.map(rt => rt.nama) 
                : ['RT 001', 'RT 002', 'RT 003', 'RT 004', 'RT 005'];
            
            const data = this.rtData && this.rtData.length > 0 
                ? this.rtData.map(rt => rt.total_penduduk) 
                : [85, 92, 78, 105, 90];
            
            this.charts.population = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Penduduk',
                        data: data,
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
                    primary: '#8B5CF6'
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
                            stepSize: 20,
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
                this.initPopulationChart();
                
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
@endpush
@endsection
