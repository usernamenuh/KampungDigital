<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Kampung Digital') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Axios for API calls -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Dark mode styles */
        .dark {
            color-scheme: dark;
        }
        
        .dark body {
            background-color: #0f172a;
            color: #f1f5f9;
        }
        
        .dark .bg-white {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
        }
        
        .dark .bg-gray-50 {
            background-color: #0f172a !important;
        }
        
        .dark .text-gray-800 {
            color: #f1f5f9 !important;
        }
        
        .dark .text-gray-600 {
            color: #cbd5e1 !important;
        }
        
        .dark .text-gray-500 {
            color: #94a3b8 !important;
        }
        
        .dark .border-gray-100,
        .dark .border-gray-200 {
            border-color: #334155 !important;
        }
        
        .dark .hover\:bg-gray-50:hover {
            background-color: #334155 !important;
        }
        
        .dark .hover\:bg-gray-100:hover {
            background-color: #334155 !important;
        }
        
        /* Loading animation */
        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Connection status indicator */
        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .status-online { background-color: #10B981; }
        .status-offline { background-color: #EF4444; }
        .status-loading { background-color: #F59E0B; animation: pulse 2s infinite; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="bg-gray-50 transition-all duration-300" :class="{ 'dark': isDarkMode }">
    <div x-data="dashboardData()" x-init="initDashboard()" class="flex h-screen overflow-hidden">
        @include('components.sidebar')
        @include('components.main-content')
        @include('components.settings-modal')
    </div>

    <script>
        window.userRole = "{{ auth()->user()->role ?? '' }}";
    </script>

    <script>
        function dashboardData() {
            return {
                // UI State
                activeItem: 'dashboard',
                isMobileMenuOpen: false,
                isMobile: window.innerWidth < 768,
                showUserDropdown: false,
                showSettingsModal: false,
                isDarkMode: localStorage.getItem('darkMode') === 'true' || false,
                
                // Data State
                dashboardCards: [],
                activities: [],
                genderData: { male: 0, female: 0, total: 0 },
                
                // Connection State
                isOnline: true,
                isLoading: false,
                lastUpdated: null,
                connectionStatus: 'loading',

                // Theme customization
                activeColor: localStorage.getItem('activeColor') || '#8B5CF6',
                hoverColor: localStorage.getItem('hoverColor') || '#F3F4F6',
                
                // Auto-refresh intervals
                refreshIntervals: {
                    stats: null,
                    charts: null,
                    activities: null
                },
                
                // Chart instances
                chartInstances: {},
                

                  menuItems: [
                    { icon: 'layout-dashboard', label: 'Dashboard', id: 'dashboard', count: null, route: 'dashboard' },
                    { icon: 'users', label: 'Penduduk', id: 'penduduk', count: 12, route: 'penduduk.index' },
                    { icon: 'map-pin', label: 'Lokasi', id: 'lokasi', count: null, route: 'lokasi.index' },
                    { icon: 'building-2', label: 'Desa', id: 'desa', count: null, route: 'desas', roles: ['admin','kades','rw','rt'] },
                    { icon: 'home', label: 'RT & RW', id: 'rt-rw', count: null, route: 'rt-rw.index' },
                    { icon: 'store', label: 'UMKM', id: 'umkm', count: 3, route: 'umkm.index' },
                    { icon: 'camera', label: 'Wisata', id: 'wisata', count: null, route: 'wisata.index' },
                    { icon: 'newspaper', label: 'Berita', id: 'berita', count: null, route: 'berita.index' },
                    { icon: 'calendar', label: 'Program', id: 'program', count: null, route: 'program.index' },
                    { icon: 'hammer', label: 'Pembangunan', id: 'pembangunan', count: null, route: 'pembangunan.index' },
                    { icon: 'banknote', label: 'Keuangan', id: 'keuangan', count: null, route: 'keuangan.index' },
                    { icon: 'file-text', label: 'Laporan', id: 'laporan', count: null, route: 'laporan.index' },
                    { icon: 'calendar-days', label: 'Agenda', id: 'agenda', count: null, route: 'agenda.index' },
                    { icon: 'video', label: 'Media', id: 'media', count: 5, route: 'media.index' },
                    { icon: 'file', label: 'Dokumen', id: 'dokumen', count: null, route: 'dokumen.index' },
                    { icon: 'message-circle', label: 'Pesan', id: 'messages', count: 8, route: 'messages.index' }
                ],
                get filteredMenuItems() {
                    return this.menuItems.filter(item => !item.roles || item.roles.includes(window.userRole));
                },
                // API Methods with better error handling
                async fetchData(endpoint, showLoading = true) {
                    if (showLoading) this.isLoading = true;
                    this.connectionStatus = 'loading';
                    
                    try {
                        console.log(`Fetching: /api/dashboard/${endpoint}`);
                        
                        const response = await axios.get(`/api/dashboard/${endpoint}`, {
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            timeout: 10000 // 10 second timeout
                        });
                        
                        console.log(`Response for ${endpoint}:`, response.data);
                        
                        this.connectionStatus = 'online';
                        this.isOnline = true;
                        this.lastUpdated = new Date().toLocaleTimeString('id-ID');
                        
                        return response.data;
                    } catch (error) {
                        console.error(`Error fetching ${endpoint}:`, error);
                        
                        this.connectionStatus = 'offline';
                        this.isOnline = false;
                        
                        // More detailed error handling
                        if (error.code === 'ECONNABORTED') {
                            this.showNotification('Koneksi timeout. Periksa jaringan Anda.', 'error');
                        } else if (error.response) {
                            this.showNotification(`Server error: ${error.response.status}`, 'error');
                        } else if (error.request) {
                            this.showNotification('Tidak dapat terhubung ke server', 'error');
                        } else {
                            this.showNotification('Terjadi kesalahan tidak terduga', 'error');
                        }
                        
                        return null;
                    } finally {
                        this.isLoading = false;
                    }
                },

                async loadStats() {
                    const data = await this.fetchData('stats');
                    if (data && data.success) {
                        this.dashboardCards = data.data;
                        console.log('Stats loaded:', this.dashboardCards);
                    }
                },

                async loadGenderData() {
                    const data = await this.fetchData('gender-data', false);
                    if (data && data.success) {
                        this.genderData = data.data;
                        this.updateGenderChart();
                    }
                },

                async loadActivities() {
                    const data = await this.fetchData('activities', false);
                    if (data && data.success) {
                        this.activities = data.data;
                    }
                },

                async loadChartData(chartType) {
                    const endpoints = {
                        monthly: 'monthly-data',
                        revenue: 'revenue-data',
                        category: 'category-data',
                        population: 'population-trend',
                        age: 'age-distribution',
                        village: 'village-ranking'
                    };

                    const data = await this.fetchData(endpoints[chartType], false);
                    if (data && data.success) {
                        this.updateChart(chartType, data.data);
                    }
                },

                // Test API connection
                async testConnection() {
                    try {
                        const response = await axios.get('/api/test');
                        console.log('API Test Response:', response.data);
                        this.showNotification('Koneksi API berhasil!', 'success');
                        return true;
                    } catch (error) {
                        console.error('API Test Failed:', error);
                        this.showNotification('Koneksi API gagal!', 'error');
                        return false;
                    }
                },

                // Chart Management (same as before)
                updateChart(chartType, data) {
                    const chartMethods = {
                        monthly: () => this.createMonthlyChart(data),
                        revenue: () => this.createRevenueBarChart(data),
                        category: () => this.createCategoryChart(data),
                        population: () => this.createPopulationAreaChart(data),
                        age: () => this.createAgeDistributionChart(data),
                        village: () => this.createVillageRankingChart(data)
                    };

                    if (chartMethods[chartType]) {
                        chartMethods[chartType]();
                    }
                },

                updateGenderChart() {
                    const ctx = document.getElementById('genderChart');
                    if (ctx) {
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
                    }
                },

                createMonthlyChart(data) {
                    const ctx = document.getElementById('monthlyChart');
                    if (ctx) {
                        if (this.chartInstances.monthly) {
                            this.chartInstances.monthly.destroy();
                        }
                        
                        const textColor = this.isDarkMode ? '#f1f5f9' : '#374151';
                        const gridColor = this.isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                        
                        this.chartInstances.monthly = new Chart(ctx, {
                            type: 'line',
                            data: data,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: { 
                                            usePointStyle: true, 
                                            padding: 20,
                                            color: textColor
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: gridColor },
                                        ticks: { color: textColor }
                                    },
                                    x: { 
                                        grid: { display: false },
                                        ticks: { color: textColor }
                                    }
                                }
                            }
                        });
                    }
                },

                createRevenueBarChart(data) {
                    const ctx = document.getElementById('revenueBarChart');
                    if (ctx) {
                        if (this.chartInstances.revenue) {
                            this.chartInstances.revenue.destroy();
                        }
                        
                        const textColor = this.isDarkMode ? '#f1f5f9' : '#374151';
                        const gridColor = this.isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                        
                        this.chartInstances.revenue = new Chart(ctx, {
                            type: 'bar',
                            data: data,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: gridColor },
                                        ticks: { 
                                            color: textColor,
                                            callback: function(value) {
                                                return 'Rp ' + (value / 1000000) + 'M';
                                            }
                                        }
                                    },
                                    x: { 
                                        grid: { display: false },
                                        ticks: { color: textColor }
                                    }
                                }
                            }
                        });
                    }
                },

                createCategoryChart(data) {
                    const ctx = document.getElementById('categoryChart');
                    if (ctx) {
                        if (this.chartInstances.category) {
                            this.chartInstances.category.destroy();
                        }
                        
                        const textColor = this.isDarkMode ? '#f1f5f9' : '#374151';
                        
                        this.chartInstances.category = new Chart(ctx, {
                            type: 'doughnut',
                            data: data,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            usePointStyle: true,
                                            padding: 15,
                                            font: { size: 11 },
                                            color: textColor
                                        }
                                    }
                                }
                            }
                        });
                    }
                },

                createPopulationAreaChart(data) {
                    const ctx = document.getElementById('populationAreaChart');
                    if (ctx) {
                        if (this.chartInstances.population) {
                            this.chartInstances.population.destroy();
                        }
                        
                        const textColor = this.isDarkMode ? '#f1f5f9' : '#374151';
                        const gridColor = this.isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                        
                        this.chartInstances.population = new Chart(ctx, {
                            type: 'line',
                            data: data,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: gridColor },
                                        ticks: { color: textColor }
                                    },
                                    x: { 
                                        grid: { display: false },
                                        ticks: { color: textColor }
                                    }
                                },
                                elements: {
                                    point: {
                                        radius: 6,
                                        hoverRadius: 8
                                    }
                                }
                            }
                        });
                    }
                },

                createAgeDistributionChart(data) {
                    const ctx = document.getElementById('ageDistributionChart');
                    if (ctx) {
                        if (this.chartInstances.age) {
                            this.chartInstances.age.destroy();
                        }
                        
                        const textColor = this.isDarkMode ? '#f1f5f9' : '#374151';
                        const gridColor = this.isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                        
                        this.chartInstances.age = new Chart(ctx, {
                            type: 'bar',
                            data: data,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: {
                                            usePointStyle: true,
                                            padding: 20,
                                            color: textColor
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: gridColor },
                                        ticks: { color: textColor }
                                    },
                                    x: { 
                                        grid: { display: false },
                                        ticks: { color: textColor }
                                    }
                                }
                            }
                        });
                    }
                },

                createVillageRankingChart(data) {
                    const ctx = document.getElementById('villageRankingChart');
                    if (ctx) {
                        if (this.chartInstances.village) {
                            this.chartInstances.village.destroy();
                        }
                        
                        const textColor = this.isDarkMode ? '#f1f5f9' : '#374151';
                        const gridColor = this.isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                        
                        this.chartInstances.village = new Chart(ctx, {
                            type: 'bar',
                            data: data,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                indexAxis: 'y',
                                plugins: {
                                    legend: { display: false }
                                },
                                scales: {
                                    y: {
                                        grid: { display: false },
                                        ticks: { color: textColor }
                                    },
                                    x: { 
                                        beginAtZero: true,
                                        grid: { color: gridColor },
                                        ticks: { color: textColor }
                                    }
                                }
                            }
                        });
                    }
                },

                // Auto-refresh Management
                startAutoRefresh() {
                    // Refresh stats every 30 seconds
                    this.refreshIntervals.stats = setInterval(() => {
                        this.loadStats();
                        this.loadGenderData();
                    }, 30000);

                    // Refresh charts every 60 seconds
                    this.refreshIntervals.charts = setInterval(() => {
                        this.loadChartData('monthly');
                        this.loadChartData('revenue');
                        this.loadChartData('category');
                        this.loadChartData('population');
                        this.loadChartData('age');
                        this.loadChartData('village');
                    }, 60000);

                    // Refresh activities every 15 seconds
                    this.refreshIntervals.activities = setInterval(() => {
                        this.loadActivities();
                    }, 15000);
                },

                stopAutoRefresh() {
                    Object.values(this.refreshIntervals).forEach(interval => {
                        if (interval) clearInterval(interval);
                    });
                },

                // Manual refresh
                async refreshAll() {
                    this.isLoading = true;
                    this.connectionStatus = 'loading';
                    
                    try {
                        await Promise.all([
                            this.loadStats(),
                            this.loadGenderData(),
                            this.loadActivities(),
                            this.loadChartData('monthly'),
                            this.loadChartData('revenue'),
                            this.loadChartData('category'),
                            this.loadChartData('population'),
                            this.loadChartData('age'),
                            this.loadChartData('village')
                        ]);
                        
                        this.showNotification('Data berhasil diperbarui!', 'success');
                    } catch (error) {
                        this.showNotification('Gagal memperbarui data', 'error');
                    }
                },

                // Utility Methods
                showNotification(message, type = 'info') {
                    // Simple notification system
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                        type === 'success' ? 'bg-green-500 text-white' :
                        type === 'error' ? 'bg-red-500 text-white' :
                        'bg-blue-500 text-white'
                    }`;
                    notification.textContent = message;
                    
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                },

                // UI Methods
                setActiveItem(itemId) {
                    this.activeItem = itemId;
                    if (this.isMobile) {
                        this.isMobileMenuOpen = false;
                    }
                    
                    if (itemId === 'settings') {
                        this.showSettingsModal = true;
                    }
                },
                
                navigateToRoute(route) {
                    window.location.href = route;
                },
                
                toggleMobileMenu() {
                    this.isMobileMenuOpen = !this.isMobileMenuOpen;
                },
                
                closeMobileMenu() {
                    this.isMobileMenuOpen = false;
                },
                
                toggleUserDropdown() {
                    this.showUserDropdown = !this.showUserDropdown;
                },
                
                toggleDarkMode() {
                    this.isDarkMode = !this.isDarkMode;
                    localStorage.setItem('darkMode', this.isDarkMode);
                    document.documentElement.classList.toggle('dark', this.isDarkMode);
                    
                    // Recreate all charts with new theme
                    this.$nextTick(() => {
                        Object.keys(this.chartInstances).forEach(chartType => {
                            if (chartType === 'gender') {
                                this.updateGenderChart();
                            } else {
                                this.loadChartData(chartType);
                            }
                        });
                    });
                },
                
                openSettings() {
                    this.showSettingsModal = true;
                },
                
                closeSettings() {
                    this.showSettingsModal = false;
                },

                // Theme Methods
                updateActiveColor(color) {
                    this.activeColor = color;
                    localStorage.setItem('activeColor', color);
                    this.applyThemeColors();
                },

                updateHoverColor(color) {
                    this.hoverColor = color;
                    localStorage.setItem('hoverColor', color);
                    this.applyThemeColors();
                },

                applyThemeColors() {
                    document.documentElement.style.setProperty('--color-primary', this.activeColor);
                    document.documentElement.style.setProperty('--color-hover', this.hoverColor);
                    
                    // Update CSS custom properties
                    const style = document.createElement('style');
                    style.textContent = `
                        :root {
                            --color-primary: ${this.activeColor};
                            --color-hover: ${this.hoverColor};
                        }
                        .custom-active {
                            background-color: ${this.activeColor}20 !important;
                            color: ${this.activeColor} !important;
                        }
                        .custom-hover:hover {
                            background-color: ${this.hoverColor} !important;
                        }
                        .custom-border-active {
                            border-color: ${this.activeColor} !important;
                        }
                    `;
                    document.head.appendChild(style);
                },
                
                logout() {
                    document.getElementById('logout-form').submit();
                },
                
                checkMobile() {
                    this.isMobile = window.innerWidth < 768;
                    if (window.innerWidth >= 768) {
                        this.isMobileMenuOpen = false;
                    }
                },
                
                initIcons() {
                    lucide.createIcons();
                },
                
                // Main initialization
                async initDashboard() {
                    console.log('Initializing dashboard...');
                    
                    // Set initial dark mode
                    document.documentElement.classList.toggle('dark', this.isDarkMode);
                    
                    // Initialize icons
                    this.initIcons();
                    
                    // Test API connection first
                    const apiWorking = await this.testConnection();
                    
                    if (apiWorking) {
                        // Load initial data
                        await this.refreshAll();
                        
                        // Start auto-refresh
                        this.startAutoRefresh();
                    } else {
                        this.connectionStatus = 'offline';
                        this.showNotification('Tidak dapat terhubung ke API. Periksa server Laravel.', 'error');
                    }
                    
                    // Setup event listeners
                    window.addEventListener('resize', () => {
                        this.checkMobile();
                    });
                    
                    document.addEventListener('click', (e) => {
                        if (!e.target.closest('.user-dropdown')) {
                            this.showUserDropdown = false;
                        }
                    });

                    // Handle page visibility change
                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            this.stopAutoRefresh();
                        } else {
                            this.startAutoRefresh();
                            this.refreshAll();
                        }
                    });

                    // Handle online/offline events
                    window.addEventListener('online', () => {
                        this.isOnline = true;
                        this.connectionStatus = 'online';
                        this.refreshAll();
                        this.showNotification('Koneksi kembali tersambung!', 'success');
                    });

                    window.addEventListener('offline', () => {
                        this.isOnline = false;
                        this.connectionStatus = 'offline';
                        this.stopAutoRefresh();
                        this.showNotification('Koneksi terputus', 'error');
                    });
                },

                // Cleanup on destroy
                destroy() {
                    this.stopAutoRefresh();
                    Object.values(this.chartInstances).forEach(chart => {
                        if (chart) chart.destroy();
                    });
                }
            }
        }
    </script>
</body>
</html>
