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
                   placeholder="Cari menu..."
                   class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent bg-gray-50 focus:bg-white transition-all">
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-2 overflow-y-auto">
        <ul class="space-y-0.5">
            <template x-for="item in filteredMenuItems" :key="item.id">
                <li>
                    <button @click="setActiveItem(item.id); navigateToRoute(item.route)"
                            :class="{
                                'custom-active custom-border-active': activeItem === item.id,
                                'text-gray-600 hover:text-gray-800 custom-hover': activeItem !== item.id
                            }"
                            class="w-full flex items-center justify-between px-2.5 py-2 rounded-lg text-left transition-all duration-200 ease-in-out group relative overflow-hidden hover:scale-[1.01] active:scale-[0.99]">
                    
                    <!-- Active indicator -->
                    <div :class="{
                            'opacity-100 scale-y-100': activeItem === item.id,
                            'opacity-0 scale-y-50': activeItem !== item.id
                         }"
                         class="absolute left-0 top-0 bottom-0 w-0.5 rounded-r-full transition-all duration-300 ease-out"
                         :style="'background-color: ' + activeColor">
                    </div>

                    <div class="flex items-center space-x-2.5">
                        <!-- Icon -->
                        <div :class="{ 'scale-110': activeItem === item.id }"
                             class="flex-shrink-0 transition-all duration-200 ease-in-out">
                            <i :data-lucide="item.icon" 
                               :class="{
                                   'text-gray-500 group-hover:text-gray-700': activeItem !== item.id
                               }"
                               :style="activeItem === item.id ? 'color: ' + activeColor : ''"
                               class="w-3.5 h-3.5 transition-all duration-200 ease-in-out"></i>
                        </div>

                        <!-- Label -->
                        <span :class="{ 'font-semibold': activeItem === item.id }"
                              class="font-medium text-xs transition-all duration-200 ease-in-out"
                              x-text="item.label">
                        </span>
                    </div>

                    <!-- Notification badge -->
                    <span x-show="item.count" 
                          x-text="item.count"
                          class="bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">
                    </span>
                </button>
            </li>
        </template>
    </ul>
</nav>

<!-- Settings at Bottom -->
<div class="p-2 border-t border-gray-100">
    <button @click="openSettings()"
            :class="{
                'custom-active custom-border-active': activeItem === 'settings',
                'text-gray-600 hover:text-gray-800 custom-hover': activeItem !== 'settings'
            }"
            class="w-full flex items-center space-x-2.5 px-2.5 py-2 rounded-lg text-left transition-all duration-200 ease-in-out group relative overflow-hidden hover:scale-[1.01] active:scale-[0.99]">
        
        <!-- Active indicator -->
        <div :class="{
                'opacity-100 scale-y-100': activeItem === 'settings',
                'opacity-0 scale-y-50': activeItem !== 'settings'
             }"
             class="absolute left-0 top-0 bottom-0 w-0.5 rounded-r-full transition-all duration-300 ease-out"
             :style="'background-color: ' + activeColor">
        </div>

        <!-- Icon -->
        <div :class="{ 'scale-110': activeItem === 'settings' }"
             class="flex-shrink-0 transition-all duration-200 ease-in-out">
            <i data-lucide="settings" 
               :class="{
                   'text-gray-500 group-hover:text-gray-700': activeItem !== 'settings'
               }"
               :style="activeItem === 'settings' ? 'color: ' + activeColor : ''"
               class="w-3.5 h-3.5 transition-all duration-200 ease-in-out"></i>
        </div>

        <!-- Label -->
        <span :class="{ 'font-semibold': activeItem === 'settings' }"
              class="font-medium text-xs transition-all duration-200 ease-in-out">
            Pengaturan
        </span>
    </button>
</div>
</div>