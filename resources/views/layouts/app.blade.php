<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Kampung Digital') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .notification-toast {
            z-index: 10000 !important;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-online { background-color: #10B981; }
        .status-offline { background-color: #EF4444; }
        .status-loading { background-color: #F59E0B; animation: pulse 2s infinite; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50" x-data="appData()" x-init="initApp()">
    <div class="flex h-screen overflow-hidden">
        @include('components.navigation')
        
        <div class="flex-1 flex flex-col overflow-hidden">
            @include('components.header')
            
            <main class="flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Global Alpine.js Data -->
    <script>
        function appData() {
            return {
                // Global state
                isMobileMenuOpen: false,
                isMobile: window.innerWidth < 768,
                showUserDropdown: false,
                isOnline: navigator.onLine,
                connectionStatus: 'loading',
                
                // Theme customization
                activeColor: localStorage.getItem('activeColor') || '#8B5CF6',
                hoverColor: localStorage.getItem('hoverColor') || '#F3F4F6',
                chartTheme: localStorage.getItem('chartTheme') || 'default',
                
                // Auto refresh
                refreshInterval: null,
                lastRefresh: null,
                
                // Notification system
                notifications: [],
                
                // Initialize app
                async initApp() {
                    this.setupEventListeners();
                    this.applyTheme();
                    this.startAutoRefresh();
                    this.checkConnectionStatus();
                    
                    // Initialize icons after DOM is ready
                    setTimeout(() => {
                        this.initializeIcons();
                    }, 100);
                },
                
                // Navigation
                toggleMobileMenu() {
                    this.isMobileMenuOpen = !this.isMobileMenuOpen;
                },
                
                toggleUserDropdown() {
                    this.showUserDropdown = !this.showUserDropdown;
                },
                
                // Connection status
                async checkConnectionStatus() {
                    try {
                        const response = await axios.get('/api/debug', { timeout: 5000 });
                        this.connectionStatus = response.data.success ? 'online' : 'offline';
                        this.isOnline = response.data.success;
                    } catch (error) {
                        this.connectionStatus = 'offline';
                        this.isOnline = false;
                    }
                },
                
                // Theme management
                applyTheme() {
                    document.documentElement.style.setProperty('--color-primary', this.activeColor);
                    document.documentElement.style.setProperty('--color-hover', this.hoverColor);
                },
                
                updateActiveColor(color) {
                    this.activeColor = color;
                    localStorage.setItem('activeColor', color);
                    this.applyTheme();
                    this.showNotification('Warna aktif berhasil diubah', 'success');
                },
                
                updateHoverColor(color) {
                    this.hoverColor = color;
                    localStorage.setItem('hoverColor', color);
                    this.applyTheme();
                    this.showNotification('Warna hover berhasil diubah', 'success');
                },
                
                updateChartTheme(theme) {
                    this.chartTheme = theme;
                    localStorage.setItem('chartTheme', theme);
                    this.showNotification('Tema chart berhasil diubah', 'success');
                    // Trigger chart update event
                    window.dispatchEvent(new CustomEvent('chartThemeChanged', { detail: theme }));
                },
                
                // Auto refresh functionality
                startAutoRefresh() {
                    this.refreshInterval = setInterval(() => {
                        if (this.isOnline) {
                            this.refreshData();
                        }
                    }, 30000); // 30 seconds
                },
                
                async refreshData() {
                    try {
                        await this.checkConnectionStatus();
                        // Trigger refresh event for components
                        window.dispatchEvent(new CustomEvent('dataRefresh'));
                        this.lastRefresh = new Date().toLocaleTimeString('id-ID');
                    } catch (error) {
                        console.error('Error refreshing data:', error);
                    }
                },
                
                // Event listeners
                setupEventListeners() {
                    // Window resize
                    window.addEventListener('resize', () => {
                        this.isMobile = window.innerWidth < 768;
                        if (window.innerWidth >= 768) {
                            this.isMobileMenuOpen = false;
                        }
                    });
                    
                    // Online/offline status
                    window.addEventListener('online', () => {
                        this.isOnline = true;
                        this.connectionStatus = 'online';
                        this.showNotification('Koneksi internet tersambung kembali', 'success');
                        this.refreshData();
                    });
                    
                    window.addEventListener('offline', () => {
                        this.isOnline = false;
                        this.connectionStatus = 'offline';
                        this.showNotification('Koneksi internet terputus', 'warning');
                    });
                    
                    // Page visibility change
                    document.addEventListener('visibilitychange', () => {
                        if (document.visibilityState === 'visible') {
                            this.checkConnectionStatus();
                            this.refreshData();
                        }
                    });
                    
                    // Click outside to close dropdowns
                    document.addEventListener('click', (e) => {
                        if (!e.target.closest('.user-dropdown')) {
                            this.showUserDropdown = false;
                        }
                    });
                },
                
                // Notification system
                showNotification(message, type = 'info', duration = 5000) {
                    const id = Date.now();
                    const notification = {
                        id,
                        message,
                        type,
                        timestamp: new Date()
                    };
                    
                    this.notifications.push(notification);
                    this.renderNotification(notification);
                    
                    setTimeout(() => {
                        this.removeNotification(id);
                    }, duration);
                },
                
                renderNotification(notification) {
                    const container = document.getElementById('notification-container');
                    const bgColor = {
                        success: 'bg-green-500',
                        error: 'bg-red-500',
                        warning: 'bg-yellow-500',
                        info: 'bg-blue-500'
                    }[notification.type];
                    
                    const notificationEl = document.createElement('div');
                    notificationEl.id = `notification-${notification.id}`;
                    notificationEl.className = `notification-toast p-4 rounded-lg shadow-lg transition-all transform translate-x-full opacity-0 ${bgColor} text-white max-w-sm`;
                    notificationEl.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <span class="flex-1">${notification.message}</span>
                            <button onclick="removeNotification(${notification.id})" class="text-white hover:text-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                    
                    container.appendChild(notificationEl);
                    
                    setTimeout(() => {
                        notificationEl.style.transform = 'translateX(0)';
                        notificationEl.style.opacity = '1';
                    }, 10);
                },
                
                removeNotification(id) {
                    const notificationEl = document.getElementById(`notification-${id}`);
                    if (notificationEl) {
                        notificationEl.style.transform = 'translateX(100%)';
                        notificationEl.style.opacity = '0';
                        setTimeout(() => {
                            notificationEl.remove();
                        }, 300);
                    }
                    this.notifications = this.notifications.filter(n => n.id !== id);
                },
                
                // Icon initialization
                initializeIcons() {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            }
        }
        
        // Global function for notification removal
        function removeNotification(id) {
            const app = Alpine.store ? Alpine.store('app') : window.Alpine.data('appData')();
            if (app && app.removeNotification) {
                app.removeNotification(id);
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>
