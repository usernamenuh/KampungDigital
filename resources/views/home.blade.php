@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Selamat datang kembali, ' . (auth()->user()->name ?? 'Admin') . '!')

@section('content')
<div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-full" x-data="dashboardData()" x-init="initDashboard()">
    <!-- Loading Overlay -->
    <div x-show="isLoading" 
         class="fixed inset-0 bg-white dark:bg-gray-900 bg-opacity-75 flex items-center justify-center z-40"
         x-cloak>
        <div class="text-center">
            <div class="loading-spinner w-8 h-8 border-4 border-purple-500 border-t-transparent rounded-full mx-auto mb-4"></div>
            <p class="text-gray-600 dark:text-gray-300">Memuat data dashboard...</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6 mb-8">
        <template x-for="(card, index) in dashboardCards" :key="index">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 fade-in">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i :data-lucide="card.icon" :class="card.iconColor" class="w-6 h-6"></i>
                        </div>
                        <span :class="{
                                'text-green-600 bg-green-50 dark:bg-green-900 dark:text-green-400': card.changeType === 'positive',
                                'text-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-gray-400': card.changeType === 'stable',
                                'text-red-600 bg-red-50 dark:bg-red-900 dark:text-red-400': card.changeType === 'negative'
                              }"
                              class="text-xs font-semibold px-2 py-1 rounded-full"
                              x-text="card.change">
                        </span>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2" x-text="card.title"></h3>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white mb-1" x-text="card.value"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="card.description"></p>
                    </div>

                    <!-- Sub Cards -->
                    <div x-show="card.subCards" class="mt-4 space-y-2">
                        <template x-for="subCard in (card.subCards || [])" :key="subCard.label">
                            <div class="flex justify-between items-center text-xs bg-gray-50 dark:bg-gray-700 p-2 rounded-lg">
                                <span class="text-gray-600 dark:text-gray-400" x-text="subCard.label"></span>
                                <span class="font-semibold text-gray-800 dark:text-white" x-text="subCard.value"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Charts Section Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Monthly Statistics -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Statistik Bulanan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tren data bulanan komprehensif</p>
                </div>
                <select class="text-sm border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                    <option>30 hari terakhir</option>
                    <option>7 hari terakhir</option>
                    <option>3 bulan terakhir</option>
                </select>
            </div>
            <div class="chart-container">
                <canvas id="monthlyChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Gender Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Distribusi Gender</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Perbandingan jumlah penduduk</p>
                </div>
            </div>
            <div class="flex flex-col items-center">
                <div class="relative w-40 h-40 mb-6">
                    <canvas id="genderChart" width="160" height="160"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="genderData.total"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                        </div>
                    </div>
                </div>
                
                <div class="w-full space-y-3">
                    <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Laki-laki</span>
                        </div>
                        <span class="text-lg font-bold text-blue-600 dark:text-blue-400" x-text="genderData.male"></span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-pink-50 dark:bg-pink-900 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-pink-500 rounded-full mr-3"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Perempuan</span>
                        </div>
                        <span class="text-lg font-bold text-pink-600 dark:text-pink-400" x-text="genderData.female"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Pendapatan Bulanan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Grafik batang pendapatan desa</p>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Category Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Kategori UMKM</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Distribusi jenis usaha</p>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="categoryChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Aktivitas Terbaru</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Log aktivitas sistem terkini</p>
            </div>
        </div>
        <div class="space-y-4">
            <template x-for="(activity, index) in activities" :key="index">
                <div class="flex items-center space-x-4 p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors fade-in">
                    <div class="w-2 h-2 bg-purple-500 rounded-full flex-shrink-0"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="activity.action"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.time"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
function dashboardData() {
    return {
        // Data
        dashboardCards: [],
        genderData: { male: 0, female: 0, total: 0 },
        activities: [],
        
        // Chart instances
        chartInstances: {},
        
        // Loading state
        isLoading: true,
        isInitialized: false,
        
        // Initialize dashboard
        async initDashboard() {
            // Prevent multiple initializations
            if (this.isInitialized) {
                console.log('🔄 Dashboard already initialized, skipping...');
                return;
            }
            
            console.log('🚀 Initializing Dashboard...');
            this.isInitialized = true;
            
            try {
                // Destroy existing charts first
                this.destroyAllCharts();
                
                await this.loadAllData();
                
                // Wait for DOM to be ready
                await this.$nextTick();
                
                // Initialize icons
                this.initializeIcons();
                
                // Setup charts with proper delay
                setTimeout(() => {
                    this.setupCharts();
                }, 1000);
                
                this.setupEventListeners();
                
            } catch (error) {
                console.error('❌ Error initializing dashboard:', error);
                this.showNotification('Error loading dashboard', 'error');
                // Load fallback data on error
                this.loadFallbackData();
            } finally {
                this.isLoading = false;
            }
        },
        
        // Destroy all existing charts
        destroyAllCharts() {
            console.log('🗑️ Destroying existing charts...');
            Object.keys(this.chartInstances).forEach(key => {
                if (this.chartInstances[key]) {
                    try {
                        this.chartInstances[key].destroy();
                        console.log(`✅ Destroyed ${key} chart`);
                    } catch (error) {
                        console.warn(`⚠️ Error destroying ${key} chart:`, error);
                    }
                }
            });
            this.chartInstances = {};
        },
        
        // Load all dashboard data with better error handling
        async loadAllData() {
            console.log('📊 Loading dashboard data...');
            
            // Load stats
            try {
                const statsResponse = await this.fetchData('/api/dashboard/stats');
                if (statsResponse && statsResponse.success) {
                    this.dashboardCards = statsResponse.data;
                    console.log('✅ Stats loaded:', this.dashboardCards.length, 'cards');
                } else {
                    throw new Error('Stats API failed');
                }
            } catch (error) {
                console.warn('⚠️ Stats API failed, using fallback data');
                this.loadFallbackStats();
            }
            
            // Load gender data
            try {
                const genderResponse = await this.fetchData('/api/dashboard/gender-data');
                if (genderResponse && genderResponse.success) {
                    this.genderData = genderResponse.data;
                    console.log('✅ Gender data loaded:', this.genderData);
                } else {
                    throw new Error('Gender API failed');
                }
            } catch (error) {
                console.warn('⚠️ Gender API failed, using fallback data');
                this.genderData = { male: 1256, female: 1181, total: 2437 };
            }
            
            // Load activities
            try {
                const activitiesResponse = await this.fetchData('/api/dashboard/activities');
                if (activitiesResponse && activitiesResponse.success) {
                    this.activities = activitiesResponse.data;
                    console.log('✅ Activities loaded:', this.activities.length, 'items');
                } else {
                    throw new Error('Activities API failed');
                }
            } catch (error) {
                console.warn('⚠️ Activities API failed, using fallback data');
                this.loadFallbackActivities();
            }
        },
        
        // Load fallback data
        loadFallbackData() {
            this.loadFallbackStats();
            this.loadFallbackActivities();
            this.genderData = { male: 1256, female: 1181, total: 2437 };
        },
        
        loadFallbackStats() {
            this.dashboardCards = [
                {
                    title: 'Total Desa',
                    value: '12',
                    change: '+2.5%',
                    changeType: 'positive',
                    icon: 'building-2',
                    iconColor: 'text-blue-600',
                    description: 'Desa terdaftar',
                    subCards: [
                        { label: 'Aktif', value: '12' },
                        { label: 'Tidak Aktif', value: '0' }
                    ]
                },
                {
                    title: 'Total Penduduk',
                    value: '2,437',
                    change: '+1.2%',
                    changeType: 'positive',
                    icon: 'users',
                    iconColor: 'text-green-600',
                    description: 'Jiwa terdaftar',
                    subCards: [
                        { label: 'Laki-laki', value: '1,256' },
                        { label: 'Perempuan', value: '1,181' }
                    ]
                },
                {
                    title: 'UMKM Aktif',
                    value: '44',
                    change: '+5.1%',
                    changeType: 'positive',
                    icon: 'store',
                    iconColor: 'text-purple-600',
                    description: 'Usaha terdaftar',
                    subCards: [
                        { label: 'Kuliner', value: '18' },
                        { label: 'Kerajinan', value: '26' }
                    ]
                },
                {
                    title: 'Saldo Kas',
                    value: 'Rp 125.750.000',
                    change: '+8.3%',
                    changeType: 'positive',
                    icon: 'banknote',
                    iconColor: 'text-emerald-600',
                    description: 'Dana tersedia',
                    subCards: [
                        { label: 'Pemasukan', value: 'Rp 45.2M' },
                        { label: 'Pengeluaran', value: 'Rp 32.8M' }
                    ]
                },
                {
                    title: 'Program Aktif',
                    value: '13',
                    change: '0%',
                    changeType: 'stable',
                    icon: 'calendar',
                    iconColor: 'text-orange-600',
                    description: 'Program berjalan',
                    subCards: [
                        { label: 'Pembangunan', value: '8' },
                        { label: 'Sosial', value: '5' }
                    ]
                },
                {
                    title: 'Berita',
                    value: '28',
                    change: '+8.1%',
                    changeType: 'positive',
                    icon: 'newspaper',
                    iconColor: 'text-indigo-600',
                    description: 'Berita terpublikasi',
                    subCards: [
                        { label: 'Bulan ini', value: '12' },
                        { label: 'Minggu ini', value: '4' }
                    ]
                },
                {
                    title: 'Wisata',
                    value: '8',
                    change: '+12.5%',
                    changeType: 'positive',
                    icon: 'camera',
                    iconColor: 'text-pink-600',
                    description: 'Destinasi wisata',
                    subCards: [
                        { label: 'Alam', value: '5' },
                        { label: 'Budaya', value: '3' }
                    ]
                },
                {
                    title: 'Dokumen',
                    value: '156',
                    change: '+3.2%',
                    changeType: 'positive',
                    icon: 'file-text',
                    iconColor: 'text-cyan-600',
                    description: 'Dokumen tersimpan',
                    subCards: [
                        { label: 'Surat', value: '89' },
                        { label: 'Laporan', value: '67' }
                    ]
                },
                {
                    title: 'Pesan',
                    value: '24',
                    change: '+15.2%',
                    changeType: 'positive',
                    icon: 'message-circle',
                    iconColor: 'text-yellow-600',
                    description: 'Pesan masuk',
                    subCards: [
                        { label: 'Belum dibaca', value: '8' },
                        { label: 'Sudah dibaca', value: '16' }
                    ]
                }
            ];
        },
        
        loadFallbackActivities() {
            this.activities = [
                { action: 'Data desa baru ditambahkan', time: '2 menit yang lalu' },
                { action: 'Saldo kas diperbarui', time: '10 menit yang lalu' },
                { action: 'Laporan bulanan diperbarui', time: '15 menit yang lalu' },
                { action: 'UMKM baru terdaftar', time: '1 jam yang lalu' },
                { action: 'Program pembangunan dimulai', time: '2 jam yang lalu' },
                { action: 'Berita baru dipublikasi', time: '3 jam yang lalu' },
                { action: 'Dokumen surat masuk', time: '4 jam yang lalu' },
                { action: 'Pesan dari warga diterima', time: '5 jam yang lalu' }
            ];
        },
        
        // Fetch data from API with timeout
        async fetchData(url) {
            console.log('🌐 Fetching:', url);
            try {
                const response = await axios.get(url, {
                    timeout: 5000,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                return response.data;
            } catch (error) {
                console.error('❌ API Error:', error.message);
                throw error;
            }
        },
        
        // Setup all charts with better error handling
        setupCharts() {
            console.log('📈 Setting up charts...');
            
            // Ensure Chart.js is loaded
            if (typeof Chart === 'undefined') {
                console.error('❌ Chart.js not loaded');
                setTimeout(() => this.setupCharts(), 1000);
                return;
            }
            
            // Apply chart theme
            this.applyChartTheme();
            
            // Setup charts one by one with error handling
            this.safeSetupChart('gender', () => this.setupGenderChart());
            this.safeSetupChart('monthly', () => this.setupMonthlyChart());
            this.safeSetupChart('revenue', () => this.setupRevenueChart());
            this.safeSetupChart('category', () => this.setupCategoryChart());
        },
        
        // Apply chart theme
        applyChartTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            const theme = this.chartTheme;
            
            Chart.defaults.color = isDark ? '#E5E7EB' : '#374151';
            Chart.defaults.borderColor = isDark ? '#374151' : '#E5E7EB';
            Chart.defaults.backgroundColor = isDark ? '#1F2937' : '#FFFFFF';
            
            if (theme === 'dark') {
                Chart.defaults.color = '#E5E7EB';
                Chart.defaults.borderColor = '#374151';
            } else if (theme === 'colorful') {
                Chart.defaults.color = isDark ? '#F3F4F6' : '#111827';
            } else if (theme === 'minimal') {
                Chart.defaults.color = isDark ? '#D1D5DB' : '#6B7280';
                Chart.defaults.borderColor = isDark ? '#4B5563' : '#D1D5DB';
            }
        },
        
        // Safe chart setup wrapper
        safeSetupChart(name, setupFunction) {
            try {
                setupFunction();
            } catch (error) {
                console.error(`❌ Error setting up ${name} chart:`, error);
                this.createFallbackChart(`${name}Chart`, name === 'gender' || name === 'category' ? 'doughnut' : name === 'revenue' ? 'bar' : 'line');
            }
        },
        
        // Setup individual charts
        setupGenderChart() {
            const ctx = document.getElementById('genderChart');
            if (!ctx) {
                console.warn('⚠️ Gender chart canvas not found');
                return;
            }
            
            if (this.chartInstances.gender) {
                this.chartInstances.gender.destroy();
            }
            
            this.chartInstances.gender = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        data: [this.genderData.male, this.genderData.female],
                        backgroundColor: ['#3B82F6', '#EC4899'],
                        borderWidth: 0,
                        cutout: '70%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
            console.log('✅ Gender chart created');
        },
        
        async setupMonthlyChart() {
            try {
                const response = await this.fetchData('/api/dashboard/monthly-data');
                if (response && response.success) {
                    const ctx = document.getElementById('monthlyChart');
                    if (!ctx) return;
                    
                    if (this.chartInstances.monthly) {
                        this.chartInstances.monthly.destroy();
                    }
                    
                    this.chartInstances.monthly = new Chart(ctx, {
                        type: 'line',
                        data: response.data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' }
                            },
                            scales: {
                                y: { 
                                    beginAtZero: true,
                                    grid: {
                                        color: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB'
                                    }
                                },
                                x: {
                                    grid: {
                                        color: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB'
                                    }
                                }
                            }
                        }
                    });
                    console.log('✅ Monthly chart created');
                }
            } catch (error) {
                console.error('❌ Error setting up monthly chart:', error);
                this.createFallbackChart('monthlyChart', 'line');
            }
        },
        
        async setupRevenueChart() {
            try {
                const response = await this.fetchData('/api/dashboard/revenue-data');
                if (response && response.success) {
                    const ctx = document.getElementById('revenueChart');
                    if (!ctx) return;
                    
                    if (this.chartInstances.revenue) {
                        this.chartInstances.revenue.destroy();
                    }
                    
                    this.chartInstances.revenue = new Chart(ctx, {
                        type: 'bar',
                        data: response.data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: { 
                                    beginAtZero: true,
                                    grid: {
                                        color: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB'
                                    }
                                },
                                x: {
                                    grid: {
                                        color: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB'
                                    }
                                }
                            }
                        }
                    });
                    console.log('✅ Revenue chart created');
                }
            } catch (error) {
                console.error('❌ Error setting up revenue chart:', error);
                this.createFallbackChart('revenueChart', 'bar');
            }
        },
        
        async setupCategoryChart() {
            try {
                const response = await this.fetchData('/api/dashboard/category-data');
                if (response && response.success) {
                    const ctx = document.getElementById('categoryChart');
                    if (!ctx) return;
                    
                    if (this.chartInstances.category) {
                        this.chartInstances.category.destroy();
                    }
                    
                    this.chartInstances.category = new Chart(ctx, {
                        type: 'doughnut',
                        data: response.data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { 
                                    position: 'bottom',
                                    labels: {
                                        color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151'
                                    }
                                }
                            }
                        }
                    });
                    console.log('✅ Category chart created');
                }
            } catch (error) {
                console.error('❌ Error setting up category chart:', error);
                this.createFallbackChart('categoryChart', 'doughnut');
            }
        },
        
        // Create fallback chart when API fails
        createFallbackChart(canvasId, type) {
            const ctx = document.getElementById(canvasId);
            if (!ctx) return;
            
            const fallbackData = {
                labels: ['Data', 'Tidak', 'Tersedia'],
                datasets: [{
                    label: 'Fallback Data',
                    data: [10, 20, 30],
                    backgroundColor: ['#E5E7EB', '#D1D5DB', '#9CA3AF']
                }]
            };
            
            const chartName = canvasId.replace('Chart', '');
            if (this.chartInstances[chartName]) {
                this.chartInstances[chartName].destroy();
            }
            
            this.chartInstances[chartName] = new Chart(ctx, {
                type: type,
                data: fallbackData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
            console.log(`✅ Fallback ${chartName} chart created`);
        },
        
        // Setup event listeners
        setupEventListeners() {
            // Listen for data refresh events
            window.addEventListener('dataRefresh', () => {
                this.refreshDashboard();
            });
            
            // Listen for chart theme changes
            window.addEventListener('chartThemeChanged', (event) => {
                this.updateChartThemes(event.detail);
            });
            
            // Listen for dark mode changes
            window.addEventListener('darkModeChanged', (event) => {
                this.updateChartsForDarkMode(event.detail);
            });
            
            // Handle page visibility change
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible' && this.isInitialized) {
                    // Refresh when page becomes visible again
                    setTimeout(() => this.refreshDashboard(), 1000);
                }
            });
        },
        
        // Refresh dashboard
        async refreshDashboard() {
            console.log('🔄 Refreshing dashboard...');
            this.isLoading = true;
            
            try {
                await this.loadAllData();
                await this.$nextTick();
                this.setupCharts();
            } catch (error) {
                console.error('❌ Error refreshing dashboard:', error);
            } finally {
                this.isLoading = false;
            }
        },
        
        // Update chart themes
        updateChartThemes(theme) {
            console.log('🎨 Updating chart theme to:', theme);
            this.chartTheme = theme;
            this.setupCharts();
        },
        
        // Update charts for dark mode
        updateChartsForDarkMode(isDark) {
            console.log('🌙 Updating charts for dark mode:', isDark);
            this.setupCharts();
        },
        
        // Initialize icons
        initializeIcons() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
                console.log('✅ Icons initialized');
            } else {
                console.warn('⚠️ Lucide icons not loaded');
                // Retry after delay
                setTimeout(() => this.initializeIcons(), 500);
            }
        },
        
        // Cleanup on destroy
        destroy() {
            console.log('🧹 Cleaning up dashboard...');
            this.destroyAllCharts();
            this.isInitialized = false;
        }
    }
}
</script>
@endpush
@endsection


