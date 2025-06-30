<div x-data="navigationData()" x-init="initNavigation()" class="flex flex-col h-full">
    <!-- Mobile Overlay -->
    <div x-show="isMobileMenuOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeMobileMenu()"
         class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
         x-cloak>
    </div>

    <!-- Sidebar -->
    <div :class="{
            'fixed inset-y-0 left-0 w-64 transform': isMobile,
            'translate-x-0': isMobile && isMobileMenuOpen,
            '-translate-x-full': isMobile && !isMobileMenuOpen,
            'w-48': !isMobile
         }"
         class="bg-white dark:bg-gray-800 shadow-lg transition-all duration-300 ease-in-out flex flex-col z-50 md:relative md:translate-x-0 border-r border-gray-200 dark:border-gray-700">
         
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
            <!-- Categories -->
            <template x-for="category in filteredCategories" :key="category.name">
                <div class="mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 px-2" 
                        x-text="category.name"></h3>
                    
                    <ul class="space-y-1">
                        <template x-for="item in category.items" :key="item.id">
                            <li class="relative">
                                <!-- Kondisi untuk menu Desa - hanya tampil untuk admin -->
                                <div x-show="item.id !== 'desa' || userRole === 'admin'">
                                    <button @click="setActiveItem(item.id)"
                                            :class="{
                                                'text-gray-800 dark:text-white font-medium shadow-sm border-l-2': isActiveMenuItem(item),
                                                'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white border-l-2 border-transparent': !isActiveMenuItem(item)
                                            }"
                                            :style="getMenuItemStyle(item)"
                                            class="w-full flex items-center justify-between px-3 py-2 ml-1 mr-1 rounded-lg text-left transition-all duration-200 group text-sm"
                                            @mouseenter="applyHoverEffect($event, item)"
                                            @mouseleave="removeHoverEffect($event, item)">
                                    
                                        <div class="flex items-center space-x-2">
                                            <i x-show="showIcons" 
                                               :data-lucide="item.icon" 
                                               :style="getIconStyle(item)"
                                               class="w-4 h-4 transition-colors flex-shrink-0"></i>

                                            <span class="font-medium text-xs truncate" x-text="item.label"></span>
                                        </div>

                                        <!-- Badge -->
                                        <span x-show="item.count" 
                                              x-text="item.count"
                                              :class="{
                                                  'bg-white text-purple-600': isActiveMenuItem(item),
                                                  'bg-red-500 text-white': !isActiveMenuItem(item)
                                              }"
                                              class="text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[16px] text-center flex-shrink-0">
                                        </span>
                                    </button>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
            </template>
        </nav>

        <!-- Settings -->
        <div class="p-2 border-t border-gray-100 dark:border-gray-700">
            <button @click="openSettings()"
                    :class="{
                        'text-gray-800 dark:text-white font-medium shadow-sm border-l-2': activeItem === 'settings',
                        'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white border-l-2 border-transparent': activeItem !== 'settings'
                    }"
                    :style="getSettingsStyle()"
                    class="w-full flex items-center space-x-2 px-3 py-2 ml-1 mr-1 rounded-lg text-left transition-all duration-200 group text-sm"
                    @mouseenter="applyHoverEffect($event, { id: 'settings' })"
                    @mouseleave="removeHoverEffect($event, { id: 'settings' })">
                
                <i x-show="showIcons"
                   data-lucide="settings" 
                   :style="getSettingsIconStyle()"
                   class="w-4 h-4 transition-colors flex-shrink-0"></i>

                <span class="font-medium text-xs">Pengaturan</span>
            </button>
        </div>
    </div>
</div>

<script>
function navigationData() {
    return {
        searchQuery: '',
        activeItem: 'dashboard',
        activeColor: localStorage.getItem('activeColor') || '#6366F1',
        hoverColor: localStorage.getItem('hoverColor') || 'rgba(99, 102, 241, 0.08)',
        showIcons: localStorage.getItem('showIcons') !== 'false',
        showIconColors: localStorage.getItem('showIconColors') === 'true',
        iconColor: localStorage.getItem('iconColor') || '#6366F1',
        isMobile: window.innerWidth < 768,
        isMobileMenuOpen: false,
        userRole: '{{ Auth::user()->role ?? "guest" }}', // Ambil role user dari Laravel
        
        menuCategories: [
            {
                name: 'Dashboard',
                items: [
                    { id: 'dashboard', label: 'Dashboard', icon: 'layout-dashboard', route: '/home' }
                ]
            },
            {
                name: 'Data Master',
                items: [
                    { id: 'penduduk', label: 'Penduduk', icon: 'users', count: 12, route: '/penduduk' },
                    { id: 'lokasi', label: 'Lokasi', icon: 'map-pin', route: '/lokasi' },
                    { id: 'desa', label: 'Desa', icon: 'building-2', route: '/desas' }
                ]
            },
            {
                name: 'Keuangan',
                items: [
                    { id: 'rt-rw', label: 'RT & RW', icon: 'home', route: '/rt-rw' },
                    { id: 'umkm', label: 'UMKM', icon: 'store', count: 3, route: '/umkm' }
                ]
            },
            {
                name: 'Konten',
                items: [
                    { id: 'wisata', label: 'Wisata', icon: 'camera', route: '/wisata' },
                    { id: 'berita', label: 'Berita', icon: 'newspaper', route: '/berita' },
                    { id: 'program', label: 'Program', icon: 'calendar', route: '/program' }
                ]
            },
            {
                name: 'Administrasi',
                items: [
                    { id: 'pembangunan', label: 'Pembangunan', icon: 'hammer', route: '/pembangunan' },
                    { id: 'keuangan', label: 'Keuangan', icon: 'banknote', route: '/keuangan' }
                ]
            }
        ],
        
        filteredCategories: [],
        
        initNavigation() {
            this.filteredCategories = this.menuCategories;
            this.activeItem = this.getCurrentRoute();
            
            // Listen for color changes from settings modal
            window.addEventListener('activeColorChanged', (e) => {
                this.activeColor = e.detail;
                this.updateStyles();
            });
            
            window.addEventListener('hoverColorChanged', (e) => {
                this.hoverColor = e.detail;
                this.updateStyles();
            });
            
            window.addEventListener('showIconsChanged', (e) => {
                this.showIcons = e.detail;
                this.reinitializeIcons();
            });
            
            window.addEventListener('showIconColorsChanged', (e) => {
                this.showIconColors = e.detail;
                this.updateStyles();
            });
            
            window.addEventListener('iconColorChanged', (e) => {
                this.iconColor = e.detail;
                this.updateStyles();
            });

            // Handle window resize
            window.addEventListener('resize', () => {
                this.isMobile = window.innerWidth < 768;
            });

            // Initialize icons and styles
            this.reinitializeIcons();
            this.updateStyles();
        },

        updateStyles() {
            // Force Alpine to re-evaluate styles
            this.$nextTick(() => {
                // Trigger reactivity
                this.activeColor = this.activeColor;
                this.hoverColor = this.hoverColor;
                this.iconColor = this.iconColor;
            });
        },

        getMenuItemStyle(item) {
            if (this.isActiveMenuItem(item)) {
                return `background-color: ${this.activeColor}15; border-left-color: ${this.activeColor};`;
            }
            return '';
        },

        getIconStyle(item) {
            if (this.isActiveMenuItem(item)) {
                return this.showIconColors ? `color: ${this.iconColor};` : `color: ${this.activeColor};`;
            }
            return '';
        },

        getSettingsStyle() {
            if (this.activeItem === 'settings') {
                return `background-color: ${this.activeColor}15; border-left-color: ${this.activeColor};`;
            }
            return '';
        },

        getSettingsIconStyle() {
            if (this.activeItem === 'settings') {
                return this.showIconColors ? `color: ${this.iconColor};` : `color: ${this.activeColor};`;
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
            return path.substring(1) || 'dashboard';
        },
        
        filterMenuItems() {
            if (!this.searchQuery.trim()) {
                this.filteredCategories = this.menuCategories;
                return;
            }
            
            const query = this.searchQuery.toLowerCase();
            this.filteredCategories = this.menuCategories.map(category => ({
                ...category,
                items: category.items.filter(item => {
                    // Filter berdasarkan pencarian dan role
                    const matchesSearch = item.label.toLowerCase().includes(query);
                    const hasAccess = item.id !== 'desa' || this.userRole === 'admin';
                    return matchesSearch && hasAccess;
                })
            })).filter(category => category.items.length > 0);
        },
        
        setActiveItem(itemId) {
            this.activeItem = itemId;
            const item = this.findMenuItem(itemId);
            if (item && item.route) {
                window.location.href = item.route;
            }
        },
        
        findMenuItem(itemId) {
            for (const category of this.menuCategories) {
                const item = category.items.find(item => item.id === itemId);
                if (item) return item;
            }
            return null;
        },
        
        isActiveMenuItem(item) {
            return this.activeItem === item.id;
        },
        
        applyHoverEffect(event, item) {
            if (!this.isActiveMenuItem(item)) {
                event.target.style.backgroundColor = this.hoverColor;
            }
        },
        
        removeHoverEffect(event, item) {
            if (!this.isActiveMenuItem(item)) {
                event.target.style.backgroundColor = '';
            }
        },

        closeMobileMenu() {
            this.isMobileMenuOpen = false;
        },
        
        openSettings() {
            window.dispatchEvent(new CustomEvent('openSettings'));
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
</style>
