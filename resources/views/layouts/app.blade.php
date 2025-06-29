<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Kampung Digital') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
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

        /* Card Styles */
        .card-default {
            background: white;
        }
        
        .dark .card-default {
            background: #1f2937;
        }
        
        .card-blur {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .dark .card-blur {
            background: rgba(31, 41, 55, 0.8);
            border: 1px solid rgba(75, 85, 99, 0.2);
        }
        
        .card-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .card-gradient .text-gray-800 {
            color: white !important;
        }
        
        .card-gradient .text-gray-600 {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        
        .card-gradient .text-gray-500 {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        .card-colored {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            color: #1f2937;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .dark ::-webkit-scrollbar-track {
            background: #374151;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #6b7280;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Dark mode transitions */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        /* Notification dropdown */
        .notification-dropdown {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300" x-data="appData()" x-init="initApp()">
    <div class="flex h-screen overflow-hidden">
        @include('components.navigation')
        
        <div class="flex-1 flex flex-col overflow-hidden">
            @include('components.header')
            
            <main class="flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Settings Modal -->
    <div x-data="settingsModalData()" x-init="
        // Listen for settings open event
        window.addEventListener('openSettings', () => openSettings());
        
        // Listen for color changes from navigation
        window.addEventListener('activeColorChanged', (e) => {
            activeColor = e.detail;
        });
        
        window.addEventListener('hoverColorChanged', (e) => {
            hoverColor = e.detail;
        });
    ">
        @include('components.settings-modal')
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
                showNotificationDropdown: false,
                isOnline: navigator.onLine,
                connectionStatus: 'loading',
                
                // Theme customization
                activeColor: localStorage.getItem('activeColor') || '#8B5CF6',
                hoverColor: localStorage.getItem('hoverColor') || 'rgba(139, 92, 246, 0.1)',
                chartTheme: localStorage.getItem('chartTheme') || 'default',
                cardStyle: localStorage.getItem('cardStyle') || 'default',
                darkMode: localStorage.getItem('darkMode') === 'true',
                showIcons: localStorage.getItem('showIcons') !== 'false',
                
                // Time and date
                currentTime: '',
                currentDate: '',
                
                // Auto refresh
                refreshInterval: null,
                timeInterval: null,
                lastRefresh: null,
                isLoading: false,
                
                // Notification system
                notifications: [],
                unreadCount: 0,
                
                // Initialize app
                async initApp() {
                    this.setupEventListeners();
                    this.applyTheme();
                    this.startTimeUpdate();
                    this.startAutoRefresh();
                    this.checkConnectionStatus();
                    this.loadNotifications();
                    
                    // Initialize icons after DOM is ready
                    setTimeout(() => {
                        this.initializeIcons();
                    }, 100);
                    
                    // Make showNotification globally available
                    window.showNotification = this.showNotification.bind(this);
                },
                
                // Time management
                startTimeUpdate() {
                    this.updateTime();
                    this.timeInterval = setInterval(() => {
                        this.updateTime();
                    }, 1000);
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
                
                // Navigation
                toggleMobileMenu() {
                    this.isMobileMenuOpen = !this.isMobileMenuOpen;
                },
                
                toggleUserDropdown() {
                    this.showUserDropdown = !this.showUserDropdown;
                    this.showNotificationDropdown = false;
                },
                
                toggleNotificationDropdown() {
                    this.showNotificationDropdown = !this.showNotificationDropdown;
                    this.showUserDropdown = false;
                    if (this.showNotificationDropdown) {
                        this.markNotificationsAsRead();
                    }
                },
                
                // Dark mode
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                    document.documentElement.classList.toggle('dark', this.darkMode);
                    this.showNotification(
                        this.darkMode ? 'Mode gelap diaktifkan' : 'Mode terang diaktifkan', 
                        'success'
                    );
                    // Update charts for dark mode
                    window.dispatchEvent(new CustomEvent('darkModeChanged', { detail: this.darkMode }));
                },
                
                // Connection status
                async checkConnectionStatus() {
                    try {
                        const response = await axios.get('/api/dashboard/test', { timeout: 5000 });
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
                    document.documentElement.classList.toggle('dark', this.darkMode);
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
                        this.lastRefresh = this.currentTime;
                    } catch (error) {
                        console.error('Error refreshing data:', error);
                    }
                },
                
                async refreshAll() {
                    this.isLoading = true;
                    try {
                        await this.refreshData();
                        this.showNotification('Data berhasil diperbarui', 'success');
                    } catch (error) {
                        this.showNotification('Gagal memperbarui data', 'error');
                    } finally {
                        this.isLoading = false;
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
                        if (!e.target.closest('.notification-dropdown')) {
                            this.showNotificationDropdown = false;
                        }
                    });
                },
                
                // Settings
                openSettings() {
                    window.dispatchEvent(new CustomEvent('openSettings'));
                },
                
                logout() {
                    if (confirm('Apakah Anda yakin ingin keluar?')) {
                        window.location.href = '/logout';
                    }
                },
                
                // Notification system
                loadNotifications() {
                    // Load from localStorage or API
                    const stored = localStorage.getItem('notifications');
                    if (stored) {
                        this.notifications = JSON.parse(stored);
                        this.updateUnreadCount();
                    }
                },
                
                addNotification(title, message, type = 'info') {
                    const notification = {
                        id: Date.now(),
                        title,
                        message,
                        type,
                        timestamp: new Date(),
                        read: false
                    };
                    
                    this.notifications.unshift(notification);
                    this.updateUnreadCount();
                    this.saveNotifications();
                    
                    // Show toast
                    this.showNotification(message, type);
                },
                
                markNotificationsAsRead() {
                    this.notifications.forEach(n => n.read = true);
                    this.updateUnreadCount();
                    this.saveNotifications();
                },
                
                updateUnreadCount() {
                    this.unreadCount = this.notifications.filter(n => !n.read).length;
                },
                
                saveNotifications() {
                    localStorage.setItem('notifications', JSON.stringify(this.notifications));
                },
                
                clearNotifications() {
                    this.notifications = [];
                    this.unreadCount = 0;
                    this.saveNotifications();
                },
                
                showNotification(message, type = 'info', duration = 5000) {
                    const id = Date.now();
                    const notification = {
                        id,
                        message,
                        type,
                        timestamp: new Date()
                    };
                    
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
                },
                
                // Icon initialization
                initializeIcons() {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                },
                
                // Cleanup
                destroy() {
                    if (this.refreshInterval) clearInterval(this.refreshInterval);
                    if (this.timeInterval) clearInterval(this.timeInterval);
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
