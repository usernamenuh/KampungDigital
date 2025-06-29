@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Selamat datang kembali, ' . (auth()->user()->name ?? 'Admin') . '!')

@section('content')
<div x-data="dashboardData()" x-init="initDashboard()" class="p-6 space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Desa Card -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Desa</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="stats.totalDesa"></p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium">+2.5%</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">Desa terdaftar</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i data-lucide="building-2" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</div>
                <div class="flex space-x-2 text-xs">
                    <span class="text-green-600">Aktif: <span x-text="stats.desaAktif"></span></span>
                    <span class="text-red-600">Tidak Aktif: <span x-text="stats.desaTidakAktif"></span></span>
                </div>
            </div>
        </div>

        <!-- Total Penduduk Card -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Penduduk</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatNumber(stats.totalPenduduk)"></p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium">+1.2%</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">Jiwa terdaftar</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i data-lucide="users" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Distribusi</div>
                <div class="flex space-x-2 text-xs">
                    <span class="text-blue-600">Laki-laki: <span x-text="formatNumber(stats.lakiLaki)"></span></span>
                    <span class="text-pink-600">Perempuan: <span x-text="formatNumber(stats.perempuan)"></span></span>
                </div>
            </div>
        </div>

        <!-- Current Date Card -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal Hari Ini</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-white mt-2" x-text="currentDate"></p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-blue-600 font-medium" x-text="currentTime"></span>
                    </div>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-xl">
                    <i data-lucide="calendar" class="w-8 h-8 text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status Sistem</div>
                <div class="flex items-center space-x-2">
                    <span :class="{
                        'status-online': connectionStatus === 'online',
                        'status-offline': connectionStatus === 'offline',
                        'status-loading': connectionStatus === 'loading'
                    }" class="status-indicator"></span>
                    <span class="text-xs text-gray-600 dark:text-gray-300" 
                          x-text="connectionStatus === 'online' ? 'Online' : connectionStatus === 'offline' ? 'Offline' : 'Loading'"></span>
                </div>
            </div>
        </div>

        <!-- Activities Card -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Aktivitas Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="stats.aktivitasHariIni"></p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-orange-600 font-medium">+5 baru</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">aktivitas</span>
                    </div>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-xl">
                    <i data-lucide="activity" class="w-8 h-8 text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Jenis Aktivitas</div>
                <div class="flex space-x-2 text-xs">
                    <span class="text-green-600">Selesai: 8</span>
                    <span class="text-yellow-600">Proses: 4</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Revenue Chart -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Pendapatan Bulanan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Grafik batang pendapatan desa</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="refreshChart('monthly')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Gender Distribution Chart -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Kategori UMKM</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Distribusi jenis usaha</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="refreshChart('gender')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="genderChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Trend Chart -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Tren Pendapatan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Grafik garis tren 6 bulan terakhir</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="refreshChart('revenue')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Category Distribution Chart -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Distribusi Kategori</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pie chart kategori data</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="refreshChart('category')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Aktivitas Terbaru</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Daftar aktivitas sistem terbaru</p>
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

<script>
function dashboardData() {
    return {
        // Data state
        stats: {
            totalDesa: 0,
            desaAktif: 0,
            desaTidakAktif: 0,
            totalPenduduk: 0,
            lakiLaki: 0,
            perempuan: 0,
            aktivitasHariIni: 0
        },
        
        activities: [],
        
        // Chart instances
        charts: {
            monthly: null,
            gender: null,
            revenue: null,
            category: null
        },
        
        // Settings
        cardStyle: localStorage.getItem('cardStyle') || 'default',
        chartTheme: localStorage.getItem('chartTheme') || 'default',
        isDarkMode: localStorage.getItem('darkMode') === 'true',
        
        // Initialize dashboard
        async initDashboard() {
            console.log('🚀 Initializing Dashboard...');
            
            // Setup event listeners
            this.setupEventListeners();
            
            // Load initial data
            await this.loadDashboardData();
            
            // Initialize charts
            setTimeout(() => {
                this.initializeCharts();
            }, 100);
            
            // Load activities
            this.loadActivities();
            
            console.log('✅ Dashboard initialized successfully');
        },
        
        // Setup event listeners
        setupEventListeners() {
            // Listen for theme changes
            window.addEventListener('cardStyleChanged', (e) => {
                this.cardStyle = e.detail;
                console.log('Card style changed to:', e.detail);
            });
            
            window.addEventListener('chartThemeChanged', (e) => {
                this.chartTheme = e.detail;
                this.updateAllCharts();
                console.log('Chart theme changed to:', e.detail);
            });
            
            window.addEventListener('darkModeChanged', (e) => {
                this.isDarkMode = e.detail;
                this.updateAllCharts();
                console.log('Dark mode changed to:', e.detail);
            });
            
            // Listen for data refresh
            window.addEventListener('dataRefresh', () => {
                this.loadDashboardData();
                this.loadActivities();
            });
        },
        
        // Load dashboard data
        async loadDashboardData() {
            try {
                console.log('📊 Loading dashboard data...');
                
                const response = await axios.get('/api/dashboard/stats');
                
                if (response.data.success) {
                    this.stats = response.data.data;
                    console.log('✅ Stats loaded:', this.stats);
                } else {
                    console.error('❌ Failed to load stats:', response.data.message);
                }
            } catch (error) {
                console.error('❌ Error loading dashboard data:', error);
                
                // Use fallback data
                this.stats = {
                    totalDesa: 13,
                    desaAktif: 13,
                    desaTidakAktif: 0,
                    totalPenduduk: 2437,
                    lakiLaki: 1256,
                    perempuan: 1181,
                    aktivitasHariIni: 12
                };
            }
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
        
        // Initialize all charts
        initializeCharts() {
            console.log('📈 Setting up charts...');
            
            this.initMonthlyChart();
            this.initGenderChart();
            this.initRevenueChart();
            this.initCategoryChart();
            
            console.log('✅ All charts initialized');
        },
        
        // Initialize monthly chart
        initMonthlyChart() {
            const ctx = document.getElementById('monthlyChart');
            if (!ctx) return;
            
            console.log('📊 Creating monthly chart...');
            
            if (this.charts.monthly) {
                this.charts.monthly.destroy();
            }
            
            this.charts.monthly = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Pendapatan (Juta)',
                        data: [15, 18, 22, 25, 28, 32],
                        backgroundColor: this.getChartColors().primary,
                        borderColor: this.getChartColors().primaryBorder,
                        borderWidth: 1,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: this.getChartOptions('bar')
            });
            
            console.log('✅ Monthly chart created');
        },
        
        // Initialize gender chart
        initGenderChart() {
            const ctx = document.getElementById('genderChart');
            if (!ctx) return;
            
            console.log('📊 Creating gender chart...');
            
            if (this.charts.gender) {
                this.charts.gender.destroy();
            }
            
            this.charts.gender = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Kuliner', 'Kerajinan', 'Pertanian', 'Jasa', 'Lainnya'],
                    datasets: [{
                        data: [35, 25, 20, 15, 5],
                        backgroundColor: this.getChartColors().palette,
                        borderWidth: 0,
                        cutout: '60%'
                    }]
                },
                options: this.getChartOptions('doughnut')
            });
            
            console.log('✅ Gender chart created');
        },
        
        // Initialize revenue chart
        initRevenueChart() {
            const ctx = document.getElementById('revenueChart');
            if (!ctx) return;
            
            console.log('📊 Creating revenue chart...');
            
            if (this.charts.revenue) {
                this.charts.revenue.destroy();
            }
            
            this.charts.revenue = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Tren Pendapatan',
                        data: [50, 45, 60, 55, 70, 65],
                        borderColor: this.getChartColors().primary,
                        backgroundColor: this.getChartColors().primaryAlpha,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: this.getChartColors().primary,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: this.getChartOptions('line')
            });
            
            console.log('✅ Revenue chart created');
        },
        
        // Initialize category chart
        initCategoryChart() {
            const ctx = document.getElementById('categoryChart');
            if (!ctx) return;
            
            console.log('📊 Creating category chart...');
            
            if (this.charts.category) {
                this.charts.category.destroy();
            }
            
            this.charts.category = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Administrasi', 'Keuangan', 'Pembangunan', 'Sosial', 'Kesehatan'],
                    datasets: [{
                        data: [30, 25, 20, 15, 10],
                        backgroundColor: this.getChartColors().palette,
                        borderWidth: 2,
                        borderColor: this.isDarkMode ? '#374151' : '#ffffff'
                    }]
                },
                options: this.getChartOptions('pie')
            });
            
            console.log('✅ Category chart created');
        },
        
        // Get chart colors based on theme
        getChartColors() {
            const themes = {
                default: {
                    primary: '#8B5CF6',
                    primaryBorder: '#7C3AED',
                    primaryAlpha: 'rgba(139, 92, 246, 0.1)',
                    palette: ['#8B5CF6', '#06B6D4', '#10B981', '#F59E0B', '#EF4444']
                },
                ocean: {
                    primary: '#0EA5E9',
                    primaryBorder: '#0284C7',
                    primaryAlpha: 'rgba(14, 165, 233, 0.1)',
                    palette: ['#0EA5E9', '#06B6D4', '#0891B2', '#0E7490', '#155E75']
                },
                forest: {
                    primary: '#10B981',
                    primaryBorder: '#059669',
                    primaryAlpha: 'rgba(16, 185, 129, 0.1)',
                    palette: ['#10B981', '#34D399', '#6EE7B7', '#A7F3D0', '#D1FAE5']
                },
                sunset: {
                    primary: '#F59E0B',
                    primaryBorder: '#D97706',
                    primaryAlpha: 'rgba(245, 158, 11, 0.1)',
                    palette: ['#F59E0B', '#F97316', '#EF4444', '#EC4899', '#8B5CF6']
                }
            };
            
            return themes[this.chartTheme] || themes.default;
        },
        
        // Get chart options
        getChartOptions(type) {
            const baseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: type !== 'bar',
                        position: 'bottom',
                        labels: {
                            color: this.isDarkMode ? '#D1D5DB' : '#374151',
                            font: {
                                size: 12
                            },
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: this.isDarkMode ? '#1F2937' : '#FFFFFF',
                        titleColor: this.isDarkMode ? '#F9FAFB' : '#111827',
                        bodyColor: this.isDarkMode ? '#D1D5DB' : '#374151',
                        borderColor: this.isDarkMode ? '#374151' : '#E5E7EB',
                        borderWidth: 1,
                        cornerRadius: 8,
                        padding: 12
                    }
                }
            };
            
            if (type === 'bar' || type === 'line') {
                baseOptions.scales = {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: this.isDarkMode ? '#9CA3AF' : '#6B7280',
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: this.isDarkMode ? '#374151' : '#F3F4F6'
                        },
                        ticks: {
                            color: this.isDarkMode ? '#9CA3AF' : '#6B7280',
                            font: {
                                size: 11
                            }
                        }
                    }
                };
            }
            
            return baseOptions;
        },
        
        // Update all charts
        updateAllCharts() {
            console.log('🔄 Updating all charts...');
            
            Object.keys(this.charts).forEach(key => {
                if (this.charts[key]) {
                    this.charts[key].destroy();
                }
            });
            
            setTimeout(() => {
                this.initializeCharts();
            }, 100);
        },
        
        // Refresh specific chart
        async refreshChart(chartType) {
            console.log(`🔄 Refreshing ${chartType} chart...`);
            
            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 500));
                
                switch (chartType) {
                    case 'monthly':
                        this.initMonthlyChart();
                        break;
                    case 'gender':
                        this.initGenderChart();
                        break;
                    case 'revenue':
                        this.initRevenueChart();
                        break;
                    case 'category':
                        this.initCategoryChart();
                        break;
                }
                
                if (window.showNotification) {
                    window.showNotification(`Chart ${chartType} berhasil diperbarui`, 'success');
                }
            } catch (error) {
                console.error(`❌ Error refreshing ${chartType} chart:`, error);
                if (window.showNotification) {
                    window.showNotification(`Gagal memperbarui chart ${chartType}`, 'error');
                }
            }
        },
        
        // Load activities
        loadActivities() {
            this.activities = [
                {
                    id: 1,
                    title: 'Desa Baru Ditambahkan',
                    description: 'Desa Sukamaju berhasil didaftarkan ke sistem',
                    type: 'success',
                    icon: 'plus-circle',
                    time: '2 menit lalu'
                },
                {
                    id: 2,
                    title: 'Data Penduduk Diperbarui',
                    description: 'Update data penduduk Desa Makmur',
                    type: 'info',
                    icon: 'users',
                    time: '5 menit lalu'
                },
                {
                    id: 3,
                    title: 'Backup Data Berhasil',
                    description: 'Backup otomatis database telah selesai',
                    type: 'success',
                    icon: 'database',
                    time: '10 menit lalu'
                },
                {
                    id: 4,
                    title: 'Peringatan Sistem',
                    description: 'Penggunaan storage mencapai 80%',
                    type: 'warning',
                    icon: 'alert-triangle',
                    time: '15 menit lalu'
                }
            ];
        },
        
        // Utility functions
        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }
    }
}
</script>
@endsection
