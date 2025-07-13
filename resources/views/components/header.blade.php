<header x-data="headerData()" x-init="initHeader()" class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-100 dark:border-gray-700 px-6 py-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <button x-show="isMobile" 
                    @click="toggleMobileMenu()"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <i data-lucide="menu" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
            </button>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">@yield('page-title', 'Dashboard')</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">@yield('page-description', 'Selamat datang kembali, ' . (auth()->user()->name ?? 'Admin') . '!')</p>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Current Time and Date -->
            <div class="hidden md:flex flex-col items-end text-right">
                <div class="text-lg font-bold text-gray-800 dark:text-white" x-text="currentTime"></div>
                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="currentDate"></div>
            </div>

            <!-- Connection Status -->
            <div class="hidden md:flex items-center space-x-2 px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <span :class="{
                    'status-online': connectionStatus === 'online',
                    'status-offline': connectionStatus === 'offline',
                    'status-loading': connectionStatus === 'loading'
                }" class="status-indicator"></span>
                <span class="text-sm text-gray-600 dark:text-gray-300" x-text="connectionStatusText"></span>
                <span x-show="onlineUsers > 0" class="text-sm text-gray-500 dark:text-gray-400">• <span x-text="onlineUsers + ' online'"></span></span>
            </div>

            <!-- Debug Info (Development Only) -->
            <div x-show="showDebugInfo" class="hidden md:flex items-center space-x-2 px-2 py-1 bg-yellow-50 dark:bg-yellow-900 rounded text-xs">
                <span x-text="'Last Check: ' + lastCheckTime"></span>
            </div>

            <!-- Refresh Button -->
            <button @click="refreshAll()" 
                    :disabled="isLoading"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors disabled:opacity-50">
                <i data-lucide="refresh-cw" 
                   :class="{ 'loading-spinner': isLoading }"
                   class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
            </button>

            <!-- Dark Mode Toggle -->
            <button @click="toggleDarkMode()" 
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <i :data-lucide="darkMode ? 'moon' : 'sun'" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
            </button>

            <!-- Notifications -->
            <div class="relative notification-dropdown">
                <button @click="toggleNotificationDropdown()" 
                        class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i data-lucide="bell" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
                    <span x-show="unreadCount > 0" 
                          x-text="unreadCount > 99 ? '99+' : unreadCount"
                          class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold"></span>
                </button>

                <!-- Notification Dropdown -->
                <div x-show="showNotificationDropdown" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50 notification-dropdown"
                     x-cloak>
                    
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Notifikasi</h3>
                        <button @click="clearNotifications()" class="text-xs text-blue-600 hover:text-blue-800">
                            Hapus Semua
                        </button>
                    </div>
                    
                    <div class="max-h-80 overflow-y-auto">
                        <template x-for="notification in notifications.slice(0, 10)" :key="notification.id">
                            <div :class="{ 'bg-blue-50 dark:bg-blue-900': !notification.read }" 
                                 class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <div class="flex items-start space-x-3">
                                    <div :class="{
                                        'bg-blue-500': notification.type === 'info',
                                        'bg-green-500': notification.type === 'success',
                                        'bg-yellow-500': notification.type === 'warning',
                                        'bg-red-500': notification.type === 'error'
                                    }" class="w-2 h-2 rounded-full mt-2 flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="notification.title"></p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1" x-text="notification.message"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1" x-text="new Date(notification.timestamp).toLocaleString('id-ID')"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="notifications.length === 0" class="px-4 py-8 text-center">
                            <i data-lucide="bell-off" class="w-8 h-8 text-gray-300 mx-auto mb-2"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="relative user-dropdown">
                <button @click="toggleUserDropdown()" 
                        class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-600">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold text-sm">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="showUserDropdown" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50"
                     x-cloak>
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
                    </div>
                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <i data-lucide="user" class="w-4 h-4 mr-3 text-gray-400"></i>
                        Profile Saya
                    </a>
                    <button @click="openSettings()" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <i data-lucide="settings" class="w-4 h-4 mr-3 text-gray-400"></i>
                        Pengaturan
                    </button>
                    <hr class="my-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900">
                            <i data-lucide="log-out" class="w-4 h-4 mr-3"></i>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
function headerData() {
    return {
        currentTime: '',
        currentDate: '',
        connectionStatus: 'online', // Default to online
        connectionStatusText: 'Online',
        onlineUsers: 1, // Default to 1 (current user)
        isLoading: false,
        darkMode: localStorage.getItem('darkMode') === 'true',
        isMobile: window.innerWidth < 768,
        lastCheckTime: '',
        showDebugInfo: false,
        
        // Notifications
        showNotificationDropdown: false,
        notifications: [],
        unreadCount: 0,
        
        // User dropdown
        showUserDropdown: false,
        
        initHeader() {
            console.log('🚀 Initializing Header...');
            
            this.updateTime();
            this.checkConnectionStatus();
            this.loadNotifications();
            this.startPeriodicUpdates();
            this.setupEventListeners();
            
            // Initialize icons
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
            
            console.log('✅ Header initialized successfully');
        },
        
        setupEventListeners() {
            // Update time every second
            setInterval(() => {
                this.updateTime();
            }, 1000);
            
            // Handle window resize
            window.addEventListener('resize', () => {
                this.isMobile = window.innerWidth < 768;
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', (event) => {
                if (!event.target.closest('.notification-dropdown')) {
                    this.showNotificationDropdown = false;
                }
                if (!event.target.closest('.user-dropdown')) {
                    this.showUserDropdown = false;
                }
            });
            
            // Listen for dark mode changes
            window.addEventListener('darkModeChanged', (e) => {
                this.darkMode = e.detail;
            });

            // Listen for data refresh events
            window.addEventListener('dataRefresh', () => {
                this.checkConnectionStatus();
                this.loadNotifications();
            });
        },
        
        updateTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            this.currentDate = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        
        async checkConnectionStatus() {
            try {
                this.connectionStatus = 'loading';
                this.connectionStatusText = 'Checking...';
                
                console.log('🔍 Checking connection status...');
                
                // Use web-based AJAX endpoint instead of API
                const response = await fetch('/ajax/dashboard/stats', {
                    method: 'HEAD', // Just check if endpoint is accessible
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });
                
                console.log('📡 Response status:', response.status);
                
                if (response.ok) {
                    this.connectionStatus = 'online';
                    this.connectionStatusText = 'Online';
                    this.onlineUsers = 1; // At least current user is online
                    this.lastCheckTime = new Date().toLocaleTimeString('id-ID');
                    
                    console.log('✅ Connection status: Online');
                    
                    // Update user activity
                    this.updateUserActivity();
                } else {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
            } catch (error) {
                console.error('❌ Connection check failed:', error);
                this.connectionStatus = 'offline';
                this.connectionStatusText = 'Offline';
                this.onlineUsers = 0;
                this.lastCheckTime = new Date().toLocaleTimeString('id-ID');
            }
        },
        
        async updateUserActivity() {
            try {
                // Use web-based AJAX endpoint
                const response = await fetch('/ajax/dashboard/update-activity', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        timestamp: new Date().toISOString()
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('✅ User activity updated:', data);
                } else {
                    console.warn('⚠️ Failed to update user activity:', response.status);
                }
            } catch (error) {
                console.error('❌ Failed to update user activity:', error);
            }
        },
        
        async loadNotifications() {
            try {
                // Use web-based notification endpoint
                const response = await fetch('/notifikasi/recent', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.notifications = data.data || [];
                        this.unreadCount = this.notifications.filter(n => !n.read).length;
                    }
                } else {
                    // If endpoint doesn't exist, just set empty notifications
                    this.notifications = [];
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('❌ Failed to load notifications:', error);
                this.notifications = [];
                this.unreadCount = 0;
            }
        },
        
        startPeriodicUpdates() {
            // Check connection status every 30 seconds
            setInterval(() => {
                this.checkConnectionStatus();
            }, 30000);
            
            // Load notifications every 60 seconds
            setInterval(() => {
                this.loadNotifications();
            }, 60000);
        },
        
        async refreshAll() {
            if (this.isLoading) return;
            
            this.isLoading = true;
            
            try {
                await Promise.all([
                    this.checkConnectionStatus(),
                    this.loadNotifications()
                ]);
                
                // Dispatch refresh event for other components
                window.dispatchEvent(new CustomEvent('dataRefresh'));
                
                if (window.showNotification) {
                    window.showNotification('Data berhasil diperbarui', 'success');
                }
            } catch (error) {
                console.error('❌ Refresh failed:', error);
                if (window.showNotification) {
                    window.showNotification('Gagal memperbarui data', 'error');
                }
            } finally {
                this.isLoading = false;
            }
        },
        
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            // Dispatch event for other components
            window.dispatchEvent(new CustomEvent('darkModeChanged', { detail: this.darkMode }));
            
            // Reinitialize icons after theme change
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
        },
        
        toggleMobileMenu() {
            window.dispatchEvent(new CustomEvent('toggleMobileMenu'));
        },
        
        toggleNotificationDropdown() {
            this.showNotificationDropdown = !this.showNotificationDropdown;
            this.showUserDropdown = false;
        },
        
        toggleUserDropdown() {
            this.showUserDropdown = !this.showUserDropdown;
            this.showNotificationDropdown = false;
        },
        
        async clearNotifications() {
            try {
                // Use web-based notification endpoint
                const response = await fetch('/notifikasi/delete-all', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    this.notifications = [];
                    this.unreadCount = 0;
                    this.showNotificationDropdown = false;
                    
                    if (window.showNotification) {
                        window.showNotification('Semua notifikasi telah dihapus', 'success');
                    }
                }
            } catch (error) {
                console.error('❌ Failed to clear notifications:', error);
                if (window.showNotification) {
                    window.showNotification('Gagal menghapus notifikasi', 'error');
                }
            }
        },
        
        openSettings() {
            window.dispatchEvent(new CustomEvent('openSettings'));
            this.showUserDropdown = false;
        }
    }
}
</script>

<style>
.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.status-online {
    background-color: #10B981;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
}

.status-offline {
    background-color: #EF4444;
    box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.3);
}

.status-loading {
    background-color: #F59E0B;
    box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3);
    animation: pulse 2s infinite;
}

.loading-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.notification-dropdown {
    max-height: 400px;
}

.user-dropdown {
    min-width: 200px;
}
</style>
