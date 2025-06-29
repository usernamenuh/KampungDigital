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
<div x-init="$nextTick(() => reinitializeIcons())" 
     :class="{
        'fixed inset-y-0 left-0 w-64 transform': isMobile,
        'translate-x-0': isMobile && isMobileMenuOpen,
        '-translate-x-full': isMobile && !isMobileMenuOpen,
        'w-56': !isMobile
     }"
     class="bg-white shadow-lg transition-all duration-300 ease-in-out flex flex-col z-50 md:relative md:translate-x-0 border-r border-gray-200">
     
    <!-- Header -->
    <div class="p-3 border-b border-gray-100">
        <div class="flex items-center space-x-2">
            <div class="w-7 h-7 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-xs">K</span>
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-gray-800 text-xs">Kampung Digital</span>
                <span class="text-xs text-gray-500">Admin Panel</span>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="p-3 border-b border-gray-100">
        <div class="relative">
            <i data-lucide="search" class="w-3 h-3 text-gray-400 absolute left-2.5 top-1/2 transform -translate-y-1/2"></i>
            <input type="text" 
                   x-model="searchQuery"
                   placeholder="Cari menu..."
                   class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent bg-gray-50 focus:bg-white transition-all">
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-2 overflow-y-auto">
        <ul class="space-y-0.5">
            <template x-for="item in filteredMenuItems" :key="item.id">
                <li class="relative">
                    <!-- Active Indicator Line -->
                    <div x-show="isActiveMenuItem && isActiveMenuItem(item)"
                         x-transition:enter="transition-all duration-200 ease-out"
                         x-transition:enter-start="opacity-0 scale-y-50"
                         x-transition:enter-end="opacity-100 scale-y-100"
                         x-transition:leave="transition-all duration-200 ease-in"
                         x-transition:leave-start="opacity-100 scale-y-100"
                         x-transition:leave-end="opacity-0 scale-y-50"
                         class="absolute left-0 top-0 bottom-0 w-1 rounded-r-full z-10"
                         :style="'background: linear-gradient(135deg, ' + (activeColor || '#8B5CF6') + ', ' + lightenColor((activeColor || '#8B5CF6'), 20) + ')'">
                    </div>

                    <button @click="setActiveItem && setActiveItem(item.id)"
                            :class="{
                                'text-white font-semibold shadow-md': isActiveMenuItem && isActiveMenuItem(item),
                                'text-gray-600 hover:text-gray-800 hover:bg-gray-50': !isActiveMenuItem || !isActiveMenuItem(item)
                            }"
                            :style="(isActiveMenuItem && isActiveMenuItem(item)) ? 'background: linear-gradient(135deg, ' + (activeColor || '#8B5CF6') + ', ' + lightenColor((activeColor || '#8B5CF6'), 10) + ')' : ''"
                            class="w-full flex items-center justify-between px-3 py-2.5 ml-1 mr-1 rounded-lg text-left transition-all duration-200 ease-in-out group relative overflow-hidden hover:scale-[1.01] active:scale-[0.99]">
                    
                        <div class="flex items-center space-x-3">
                            <!-- Icon -->
                            <div :class="{ 'scale-110': isActiveMenuItem && isActiveMenuItem(item) }"
                                 class="flex-shrink-0 transition-all duration-200 ease-in-out">
                                <i :data-lucide="item.icon" 
                                   :class="{
                                       'text-gray-500 group-hover:text-gray-700': !isActiveMenuItem || !isActiveMenuItem(item),
                                       'text-white drop-shadow-sm': isActiveMenuItem && isActiveMenuItem(item)
                                   }"
                                   class="w-4 h-4 transition-all duration-200 ease-in-out"></i>
                            </div>

                            <!-- Label -->
                            <span :class="{ 'font-semibold': isActiveMenuItem && isActiveMenuItem(item) }"
                                  class="font-medium text-xs transition-all duration-200 ease-in-out"
                                  x-text="item.label">
                            </span>
                        </div>

                        <!-- Notification badge -->
                        <span x-show="item.count" 
                              x-text="item.count"
                              :class="{
                                  'bg-white text-purple-600 shadow-sm': isActiveMenuItem && isActiveMenuItem(item),
                                  'bg-red-500 text-white': !isActiveMenuItem || !isActiveMenuItem(item)
                              }"
                              class="text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center transition-all duration-200">
                        </span>
                    </button>
                </li>
            </template>
        </ul>
    </nav>

    <!-- Settings at Bottom -->
    <div class="p-2 border-t border-gray-100">
        <div class="relative">
            <!-- Active Indicator Line for Settings -->
            <div x-show="activeItem === 'settings'"
                 x-transition:enter="transition-all duration-200 ease-out"
                 x-transition:enter-start="opacity-0 scale-y-50"
                 x-transition:enter-end="opacity-100 scale-y-100"
                 x-transition:leave="transition-all duration-200 ease-in"
                 x-transition:leave-start="opacity-100 scale-y-100"
                 x-transition:leave-end="opacity-0 scale-y-50"
                 class="absolute left-0 top-0 bottom-0 w-1 rounded-r-full z-10"
                 :style="'background: linear-gradient(135deg, ' + (activeColor || '#8B5CF6') + ', ' + lightenColor((activeColor || '#8B5CF6'), 20) + ')'">
            </div>

            <button @click="openSettings && openSettings()"
                    :class="{
                        'text-white font-semibold shadow-md': activeItem === 'settings',
                        'text-gray-600 hover:text-gray-800 hover:bg-gray-50': activeItem !== 'settings'
                    }"
                    :style="activeItem === 'settings' ? 'background: linear-gradient(135deg, ' + (activeColor || '#8B5CF6') + ', ' + lightenColor((activeColor || '#8B5CF6'), 10) + ')' : ''"
                    class="w-full flex items-center space-x-3 px-3 py-2.5 ml-1 mr-1 rounded-lg text-left transition-all duration-200 ease-in-out group relative overflow-hidden hover:scale-[1.01] active:scale-[0.99]">
                
                <!-- Icon -->
                <div :class="{ 'scale-110': activeItem === 'settings' }"
                     class="flex-shrink-0 transition-all duration-200 ease-in-out">
                    <i data-lucide="settings" 
                       :class="{
                           'text-gray-500 group-hover:text-gray-700': activeItem !== 'settings',
                           'text-white drop-shadow-sm': activeItem === 'settings'
                       }"
                       class="w-4 h-4 transition-all duration-200 ease-in-out"></i>
                </div>

                <!-- Label -->
                <span :class="{ 'font-semibold': activeItem === 'settings' }"
                      class="font-medium text-xs transition-all duration-200 ease-in-out">
                    Pengaturan
                </span>
            </button>
        </div>
    </div>
</div>

<script>
// Enhanced function untuk mendeteksi active menu berdasarkan URL
function isActiveMenuItem(item) {
    if (!item || !item.id) return false;
    
    const currentPath = window.currentPath || window.location.pathname.replace('/', '');
    
    // Mapping URL ke menu ID
    const urlToMenuMap = {
        'dashboard': 'dashboard',
        'home': 'dashboard',
        'desas': 'desa',
        'penduduk': 'penduduk',
        'lokasi': 'lokasi',
        'rt-rw': 'rt-rw ',
        'umkm': 'umkm',
        'wisata': 'wisata',
        'berita': 'berita',
        'program': 'program',
        'pembangunan': 'pembangunan',
        'keuangan': 'keuangan',
        'laporan': 'laporan',
        'agenda': 'agenda',
        'media': 'media',
        'dokumen': 'dokumen',
        'pesan': 'pesan'
    };
    
    // Cek apakah current path mengandung route item
    for (const [path, menuId] of Object.entries(urlToMenuMap)) {
        if (currentPath.includes(path) && item.id === menuId) {
            return true;
        }
    }
    
    // Default untuk dashboard jika di root
    if ((currentPath === '' || currentPath === 'dashboard' || currentPath === 'home') && item.id === 'dashboard') {
        return true;
    }
    
    return false;
}

// Function untuk lighten color
function lightenColor(color, percent) {
    if (!color) return '#8B5CF6';
    
    try {
        const num = parseInt(color.replace("#", ""), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) + amt;
        const G = (num >> 8 & 0x00FF) + amt;
        const B = (num & 0x0000FF) + amt;
        return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
            (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
            (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
    } catch (error) {
        console.warn('Error lightening color:', error);
        return color || '#8B5CF6';
    }
}

// Tambahkan ke window agar bisa diakses dari Alpine.js
window.isActiveMenuItem = isActiveMenuItem;
window.lightenColor = lightenColor;
</script>
