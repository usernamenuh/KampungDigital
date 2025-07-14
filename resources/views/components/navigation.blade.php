<div x-data="navigationData($store.app)" x-init="initNavigation()" class="flex flex-col h-full min-h-screen">
    <!-- Mobile Overlay -->
    <div x-show="$store.app.isMobileMenuOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="$store.app.toggleMobileMenu()"
         class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
         x-cloak>
    </div>

    <!-- Sidebar -->
    <div :class="{
            'fixed inset-y-0 left-0 w-64 transform': $store.app.isMobile,
            'translate-x-0': $store.app.isMobile && $store.app.isMobileMenuOpen,
            '-translate-x-full': $store.app.isMobile && !$store.app.isMobileMenuOpen,
            'w-48': !$store.app.isMobile
         }"
         class="bg-white dark:bg-gray-800 shadow-lg transition-all duration-300 ease-in-out flex flex-col z-50 md:relative md:translate-x-0 border-r border-gray-200 dark:border-gray-700 min-h-screen"
         x-cloak>
         
        <!-- Header -->
        <div class="p-3 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xs">K</span>
                </div>
                <div>
                    <h1 class="font-bold text-gray-800 dark:text-white text-xs">Kampung Digital</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Admin Panel</p>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="p-3 border-b border-gray-100 dark:border-gray-700">
            <div class="relative">
                <i data-lucide="search" class="w-3 h-3 text-gray-400 absolute left-2 top-1/2 transform -translate-y-1/2"></i>
                <input type="text" 
                       x-model="searchQuery"
                       @input="filterMenuItems()"
                       placeholder="Cari menu..."
                       class="w-full pl-7 pr-3 py-1.5 text-xs border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 dark:bg-gray-700 focus:bg-white dark:focus:bg-gray-600 transition-all text-gray-800 dark:text-white">
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-2 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600">
            <ul class="space-y-1">
                <template x-for="item in filteredMenuItems" :key="item.id">
                    <li class="relative">
                        <!-- Kondisi untuk menu berdasarkan role -->
                        <div x-show="hasMenuAccess(item.id)">
                            <button @click="setActiveItem(item.id)"
                                    :class="{
                                        'text-gray-800 dark:text-white font-medium border-l-2': isActiveMenuItem(item),
                                        'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white': !isActiveMenuItem(item)
                                    }"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-left transition-all duration-200 group text-sm border-l-2 border-transparent"
                                    :style="getMenuItemStyle(item)"
                                    @mouseenter="applyHoverEffect($event, item)"
                                    @mouseleave="removeHoverEffect($event, item)">
                            
                                <div class="flex items-center space-x-2">
                                    <i x-show="$store.app.showIcons" 
                                       :data-lucide="item.icon" 
                                       :style="getIconStyle(item)"
                                       class="w-4 h-4 transition-colors flex-shrink-0"></i>

                                    <span class="font-medium text-sm truncate" x-text="item.label"></span>
                                </div>

                                <!-- Badge (removed) -->
                            </button>
                        </div>
                    </li>
                </template>
            </ul>
        </nav>

        <!-- Settings -->
        <div class="p-2 border-t border-gray-100 dark:border-gray-700 mt-auto">
            <button @click="openSettings()"
                    :class="{
                        'text-gray-800 dark:text-white font-medium border-l-2': activeItem === 'settings',
                        'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white border-l-2 border-transparent': activeItem !== 'settings'
                    }"
                    :style="getSettingsStyle()"
                    class="w-full flex items-center space-x-2 px-3 py-2 ml-1 mr-1 rounded-lg text-left transition-all duration-200 group text-sm"
                    @mouseenter="applyHoverEffect($event, { id: 'settings' })"
                    @mouseleave="removeHoverEffect($event, { id: 'settings' })">
                
                <i x-show="$store.app.showIcons"
                   data-lucide="settings" 
                   :style="getSettingsIconStyle()"
                   class="w-4 h-4 transition-colors flex-shrink-0"></i>

                <span class="font-medium text-sm">Pengaturan</span>
            </button>
        </div>
    </div>
</div>

<script>
function navigationData(appStore) {
    return {
        appStore: appStore, // Reference to the global app store
        searchQuery: '',
        activeItem: 'dashboard',
        userRole: '{{ Auth::user()->role ?? "guest" }}', // Ambil role user dari Laravel
        
        // Flattened menu items
        menuItems: [
            { id: 'dashboard', label: 'Dashboard', icon: 'gauge', route: '/home' },
            { id: 'penduduk', label: 'Penduduk', icon: 'users', route: '/penduduk' },
            { id: 'kk', label: 'Kartu Keluarga', icon: 'users', route: '/kk' },
            { id: 'desa', label: 'Desa', icon: 'building-2', route: '/desas' },
            { id: 'kas', label: 'Kas RT/RW', icon: 'wallet', route: '/kas' },
            { id: 'pengaturan-kas', label: 'Pengaturan Kas', icon: 'settings', route: '/pengaturan-kas' },
            { id: 'umkm', label: 'UMKM', icon: 'store', route: '/umkm' },
            { id: 'users', label: 'Kelola Pengguna', icon: 'user-cog', route: '/users' },
            { id: 'rt-rw', label: 'RT & RW', icon: 'home', route: '/rt-rw' },
            { id: 'wisata', label: 'Wisata', icon: 'camera', route: '/wisata' },
            { id: 'berita', label: 'Berita', icon: 'newspaper', route: '/berita' },
            { id: 'program', label: 'Program', icon: 'calendar', route: '/program' },
            { id: 'pendidikan', label: 'Pendidikan', icon: 'graduation-cap', route: '/pendidikan' },
            { id: 'pembangunan', label: 'Pembangunan', icon: 'hammer', route: '/pembangunan' },
            { id: 'keuangan', label: 'Keuangan', icon: 'banknote', route: '/keuangan' }
        ],
        
        filteredMenuItems: [], // Will hold the filtered and role-accessed items
        
        // Fungsi untuk mengecek akses menu berdasarkan role
        hasMenuAccess(menuId) {
            switch(menuId) {
                case 'users':
                    // Menu Kelola Pengguna hanya untuk admin
                    return this.userRole === 'admin';
                    
                case 'desa':
                    // Menu Desa hanya untuk admin
                    return this.userRole === 'admin';
                    
                case 'rt-rw':
                    // Menu RT & RW hanya untuk kades dan admin
                    return this.userRole === 'admin' || this.userRole === 'kades';
                    
                case 'kas':
                case 'pengaturan-kas':
                    // Menu Kas hanya untuk admin, kades, rw, rt
                    return ['admin', 'kades', 'rw', 'rt'].includes(this.userRole);
                    
                case 'wisata':
                case 'berita':
                case 'program':
                case 'pendidikan':
                    // Menu ini bisa diakses oleh admin, kades, dan masyarakat
                    return ['admin', 'kades', 'masyarakat'].includes(this.userRole);
                    
                case 'pembangunan':
                case 'keuangan':
                    // Menu ini hanya untuk admin dan kades
                    return this.userRole === 'admin' || this.userRole === 'kades';
                    
                case 'penduduk':
                case 'kk':
                    // Menu Kartu Keluarga bisa diakses oleh admin, kades, rw, rt
                    return ['admin', 'kades', 'rw', 'rt'].includes(this.userRole);
                    
                case 'umkm':
                    // Menu UMKM bisa diakses oleh admin, kades, rw, rt, dan masyarakat
                    return ['admin', 'kades', 'rw', 'rt', 'masyarakat'].includes(this.userRole);
                    
                default:
                    // Menu lainnya (dashboard, dll) bisa diakses semua role yang login
                    return this.userRole !== 'guest';
            }
        },
        
        initNavigation() {
            this.filterMenuItems(); // Filter menu items berdasarkan role
            this.activeItem = this.getCurrentRoute();
            
            // Initialize icons and styles
            this.reinitializeIcons();
        },
        
        getMenuItemStyle(item) {
            if (this.isActiveMenuItem(item)) {
                return `background-color: ${this.appStore.activeColor}15; border-left-color: ${this.appStore.activeColor};`;
            }
            // Untuk item tidak aktif, border-left-color akan tetap transparan dari kelas dasar
            return '';
        },

        getIconStyle(item) {
            if (this.isActiveMenuItem(item)) {
                return this.appStore.showIconColors ? `color: ${this.appStore.iconColor};` : `color: ${this.appStore.activeColor};`;
            }
            return '';
        },

        getSettingsStyle() {
            if (this.activeItem === 'settings') {
                return `background-color: ${this.appStore.activeColor}15; border-left-color: ${this.appStore.activeColor};`;
            }
            return '';
        },

        getSettingsIconStyle() {
            if (this.activeItem === 'settings') {
                return this.appStore.showIconColors ? `color: ${this.appStore.iconColor};` : `color: ${this.appStore.activeColor};`;
            }
            return '';
        },

        reinitializeIcons() {
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        },
        
        getCurrentRoute() {
            const path = window.location.pathname;
            if (path === '/home' || path === '/') return 'dashboard';
            if (path === '/desas' || path.includes('/desas')) return 'desa';
            if (path === '/rt-rw' || path.includes('/rt-rw')) return 'rt-rw';
            if (path === '/kk' || path.includes('/kk')) return 'kk';
            if (path === '/kas' || path.includes('/kas')) return 'kas';
            if (path === '/pengaturan-kas' || path.includes('/pengaturan-kas')) return 'pengaturan-kas';
            if (path === '/users' || path.includes('/users')) return 'users';
            if (path === '/wisata' || path.includes('/wisata')) return 'wisata';
            if (path === '/berita' || path.includes('/berita')) return 'berita';
            if (path === '/program' || path.includes('/program')) return 'program';
            if (path === '/pendidikan' || path.includes('/pendidikan')) return 'pendidikan';
            if (path === '/pembangunan' || path.includes('/pembangunan')) return 'pembangunan';
            if (path === '/keuangan' || path.includes('/keuangan')) return 'keuangan';
            return path.substring(1) || 'dashboard';
        },
        
        filterMenuItems() {
            const query = this.searchQuery.toLowerCase().trim();
            this.filteredMenuItems = this.menuItems.filter(item => {
                const matchesSearch = item.label.toLowerCase().includes(query);
                const hasAccess = this.hasMenuAccess(item.id);
                return matchesSearch && hasAccess;
            });
            this.$nextTick(() => { // Reinitialize icons after filtering
                this.reinitializeIcons();
            });
        },
        
        setActiveItem(itemId) {
            this.activeItem = itemId;
            const item = this.findMenuItem(itemId);
            if (item && item.route) {
                window.location.href = item.route;
            }
        },
        
        findMenuItem(itemId) {
            return this.menuItems.find(item => item.id === itemId);
        },
        
        isActiveMenuItem(item) {
            return this.activeItem === item.id;
        },
        
        applyHoverEffect(event, item) {
            if (!this.isActiveMenuItem(item)) {
                event.target.style.backgroundColor = this.appStore.hoverColor;
            }
        },
        
        removeHoverEffect(event, item) {
            if (!this.isActiveMenuItem(item)) {
                event.target.style.backgroundColor = '';
            }
        },
        
        openSettings() {
            this.appStore.openSettings(); // Call the global openSettings method
        }
    }
}
</script>

<style>
/* Custom scrollbar for navigation */
.scrollbar-thin {
    scrollbar-width: thin;
}

.scrollbar-thumb-gray-300::-webkit-scrollbar {
    width: 4px;
}

.scrollbar-thumb-gray-300::-webkit-scrollbar-track {
    background: transparent;
}

.scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
    background-color: #d1d5db;
    border-radius: 2px;
}

.dark .scrollbar-thumb-gray-600::-webkit-scrollbar-thumb {
    background-color: #4b5563;
}

/* Ensure icons don't scroll */
[data-lucide] {
    flex-shrink: 0;
}

/* Ensure sidebar takes full height */
.min-h-screen {
    min-height: 100vh;
}
</style>
