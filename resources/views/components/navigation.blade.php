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
        <nav class="flex-1 p-2 overflow-y-auto">
            <!-- Categories -->
            <template x-for="category in filteredCategories" :key="category.name">
                <div class="mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 px-2" 
                        x-text="category.name"></h3>
                    
                    <ul class="space-y-1">
                        <template x-for="item in category.items" :key="item.id">
                            <li class="relative">
                                <!-- Active Indicator with Glow Effect -->
                                <div x-show="isActiveMenuItem(item)"
                                     class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-5 rounded-r-full z-10 transition-all duration-300"
                                     :style="'background-color: ' + activeColor + '; box-shadow: 0 0 8px ' + activeColor + '40, 0 0 16px ' + activeColor + '20;'">
                                </div>

                                <button @click="setActiveItem(item.id)"
                                        :class="{
                                            'text-white font-medium shadow-sm': isActiveMenuItem(item),
                                            'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white': !isActiveMenuItem(item)
                                        }"
                                        :style="isActiveMenuItem(item) ? 'background-color: ' + activeColor + '; box-shadow: 0 2px 8px ' + activeColor + '30;' : ''"
                                        class="w-full flex items-center justify-between px-3 py-2 ml-1 mr-1 rounded-lg text-left transition-all duration-200 group text-sm"
                                        @mouseenter="applyHoverEffect($event)"
                                        @mouseleave="removeHoverEffect($event)">
                                
                                    <div class="flex items-center space-x-2">
                                        <i x-show="showIcons" 
                                           :data-lucide="item.icon" 
                                           :class="{
                                               'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-200': !isActiveMenuItem(item),
                                               'text-white': isActiveMenuItem(item)
                                           }"
                                           :style="showIconColors && isActiveMenuItem(item) ? 'color: ' + iconColor + ';' : ''"
                                           class="w-4 h-4 transition-colors"></i>

                                        <span class="font-medium text-xs" x-text="item.label"></span>
                                    </div>

                                    <!-- Badge -->
                                    <span x-show="item.count" 
                                          x-text="item.count"
                                          :class="{
                                              'bg-white text-purple-600': isActiveMenuItem(item),
                                              'bg-red-500 text-white': !isActiveMenuItem(item)
                                          }"
                                          class="text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[16px] text-center">
                                    </span>
                                </button>
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
                        'text-white font-medium shadow-sm': activeItem === 'settings',
                        'text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white': activeItem !== 'settings'
                    }"
                    :style="activeItem === 'settings' ? 'background-color: ' + activeColor + '; box-shadow: 0 2px 8px ' + activeColor + '30;' : ''"
                    class="w-full flex items-center space-x-2 px-3 py-2 ml-1 mr-1 rounded-lg text-left transition-all duration-200 group text-sm"
                    @mouseenter="applyHoverEffect($event)"
                    @mouseleave="removeHoverEffect($event)">
                
                <i x-show="showIcons"
                   data-lucide="settings" 
                   :class="{
                       'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-200': activeItem !== 'settings',
                       'text-white': activeItem === 'settings'
                   }"
                   :style="showIconColors && activeItem === 'settings' ? 'color: ' + iconColor + ';' : ''"
                   class="w-4 h-4 transition-colors"></i>

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
        activeColor: localStorage.getItem('activeColor') || '#8B5CF6',
        hoverColor: localStorage.getItem('hoverColor') || 'rgba(139, 92, 246, 0.08)',
        showIcons: localStorage.getItem('showIcons') !== 'false',
        showIconColors: localStorage.getItem('showIconColors') === 'true',
        iconColor: localStorage.getItem('iconColor') || '#FFFFFF',
        
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
            
            // Listen for color changes
            window.addEventListener('activeColorChanged', (e) => {
                this.activeColor = e.detail;
            });
            
            window.addEventListener('hoverColorChanged', (e) => {
                this.hoverColor = e.detail;
            });
            
            window.addEventListener('showIconsChanged', (e) => {
                this.showIcons = e.detail;
            });
            
            window.addEventListener('showIconColorsChanged', (e) => {
                this.showIconColors = e.detail;
            });
            
            window.addEventListener('iconColorChanged', (e) => {
                this.iconColor = e.detail;
            });
        },
        
        getCurrentRoute() {
            const path = window.location.pathname;
            if (path === '/home' || path === '/') return 'dashboard';
            if (path === '/desas') return 'desa';
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
                items: category.items.filter(item => 
                    item.label.toLowerCase().includes(query)
                )
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
        
        applyHoverEffect(event) {
            if (!this.isActiveMenuItem({ id: this.activeItem })) {
                event.target.style.backgroundColor = this.hoverColor;
            }
        },
        
        removeHoverEffect(event) {
            if (!this.isActiveMenuItem({ id: this.activeItem })) {
                event.target.style.backgroundColor = '';
            }
        },
        
        openSettings() {
            window.dispatchEvent(new CustomEvent('openSettings'));
        }
    }
}
</script>
