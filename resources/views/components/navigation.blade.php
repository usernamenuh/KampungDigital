<!-- Mobile Overlay -->
<div x-show="isMobileMenuOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="isMobileMenuOpen = false"
     class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
     x-cloak>
</div>

<!-- Sidebar Navigation -->
<div :class="{
        'fixed inset-y-0 left-0 w-64 transform': isMobile,
        'translate-x-0': isMobile && isMobileMenuOpen,
        '-translate-x-full': isMobile && !isMobileMenuOpen,
        'w-56': !isMobile
     }"
     class="bg-white shadow-lg transition-all duration-300 ease-in-out flex flex-col z-50 md:relative md:translate-x-0 border-r border-gray-200">
     
    <!-- Header -->
    <div class="p-4 border-b border-gray-100">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">K</span>
            </div>
            <div>
                <h1 class="font-bold text-gray-800 text-sm">Kampung Digital</h1>
                <p class="text-xs text-gray-500">Admin Panel</p>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="p-4 border-b border-gray-100">
        <div class="relative">
            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
            <input type="text" 
                   placeholder="Cari menu..."
                   class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 focus:bg-white transition-all">
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 p-3 overflow-y-auto">
        <ul class="space-y-1">
            @php
                $currentRoute = Route::currentRouteName();
                $menuItems = [
                    ['icon' => 'layout-dashboard', 'label' => 'Dashboard', 'route' => 'home', 'count' => null],
                    ['icon' => 'users', 'label' => 'Penduduk', 'route' => 'penduduk.index', 'count' => 12],
                    ['icon' => 'map-pin', 'label' => 'Lokasi', 'route' => 'lokasi.index', 'count' => null],
                    ['icon' => 'building-2', 'label' => 'Desa', 'route' => 'desa.index', 'count' => null],
                    ['icon' => 'home', 'label' => 'RT & RW', 'route' => 'rt-rw.index', 'count' => null],
                    ['icon' => 'store', 'label' => 'UMKM', 'route' => 'umkm.index', 'count' => 3],
                    ['icon' => 'camera', 'label' => 'Wisata', 'route' => 'wisata.index', 'count' => null],
                    ['icon' => 'newspaper', 'label' => 'Berita', 'route' => 'berita.index', 'count' => null],
                    ['icon' => 'calendar', 'label' => 'Program', 'route' => 'program.index', 'count' => null],
                    ['icon' => 'hammer', 'label' => 'Pembangunan', 'route' => 'pembangunan.index', 'count' => null],
                    ['icon' => 'banknote', 'label' => 'Keuangan', 'route' => 'keuangan.index', 'count' => null],
                    ['icon' => 'file-text', 'label' => 'Laporan', 'route' => 'laporan.index', 'count' => null],
                    ['icon' => 'calendar-days', 'label' => 'Agenda', 'route' => 'agenda.index', 'count' => null],
                    ['icon' => 'video', 'label' => 'Media', 'route' => 'media.index', 'count' => 5],
                    ['icon' => 'file', 'label' => 'Dokumen', 'route' => 'dokumen.index', 'count' => null],
                    ['icon' => 'message-circle', 'label' => 'Pesan', 'route' => 'pesan.index', 'count' => 8],
                ];
            @endphp
            
            @foreach($menuItems as $item)
                @php
                    $isActive = $currentRoute === $item['route'];
                @endphp
                <li class="relative">
                    <!-- Active Indicator -->
                    @if($isActive)
                        <div class="absolute left-0 top-0 bottom-0 w-1 rounded-r-full z-10"
                             :style="'background-color: ' + activeColor">
                        </div>
                    @endif

                    <a href="{{ route($item['route']) }}"
                       class="w-full flex items-center justify-between px-4 py-3 ml-1 mr-1 rounded-lg text-left transition-all duration-200 group {{ $isActive ? 'text-white font-medium shadow-sm' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}"
                       @if($isActive) :style="'background-color: ' + activeColor" @endif>
        
                        <div class="flex items-center space-x-3">
                            <i data-lucide="{{ $item['icon'] }}" 
                               class="w-5 h-5 transition-colors {{ $isActive ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}"></i>

                            <span class="font-medium text-sm">{{ $item['label'] }}</span>
                        </div>

                        @if($item['count'])
                            <span class="text-xs font-bold px-2 py-1 rounded-full min-w-[20px] text-center {{ $isActive ? 'bg-white text-purple-600' : 'bg-red-500 text-white' }}">
                                {{ $item['count'] }}
                            </span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <!-- Settings -->
    <div class="p-3 border-t border-gray-100">
        <button @click="$dispatch('open-settings')"
                class="w-full flex items-center space-x-3 px-4 py-3 ml-1 mr-1 rounded-lg text-left transition-all duration-200 group text-gray-600 hover:text-gray-800 hover:bg-gray-50">
            
            <i data-lucide="settings" class="w-5 h-5 transition-colors text-gray-500 group-hover:text-gray-700"></i>
            <span class="font-medium text-sm">Pengaturan</span>
        </button>
    </div>
</div>

<!-- Settings Modal -->
<div x-data="{ showSettings: false }" 
     @open-settings.window="showSettings = true"
     x-show="showSettings" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
     x-cloak>
    
    <div x-show="showSettings"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="showSettings = false"
         class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        
        <!-- Settings Header -->
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Pengaturan Aplikasi</h3>
                <button @click="showSettings = false" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
                </button>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="p-6 space-y-6">
            <!-- Navigation Colors -->
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-4">Warna Navigasi</h4>
                
                <div class="space-y-4">
                    <!-- Active Color -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Aktif</label>
                        <div class="grid grid-cols-6 gap-3">
                            @foreach(['#8B5CF6', '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#EC4899'] as $color)
                                <button @click="updateActiveColor('{{ $color }}')"
                                        style="background-color: {{ $color }}"
                                        :class="{ 'ring-4 ring-offset-2 ring-gray-300': activeColor === '{{ $color }}' }"
                                        class="w-12 h-12 rounded-xl shadow-sm hover:scale-110 transition-all">
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Chart Themes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tema Chart</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['default' => 'Default', 'dark' => 'Dark', 'colorful' => 'Colorful', 'minimal' => 'Minimal'] as $theme => $label)
                                <button @click="updateChartTheme('{{ $theme }}')"
                                        :class="{ 'ring-2 ring-purple-500 bg-purple-50': chartTheme === '{{ $theme }}' }"
                                        class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-all text-left">
                                    <p class="font-medium text-gray-800">{{ $label }}</p>
                                    <p class="text-sm text-gray-500">Tema {{ strtolower($label) }} untuk chart</p>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auto Refresh Status -->
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-4">Status Sistem</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">Status Koneksi</p>
                            <p class="text-sm text-gray-500">Status koneksi internet real-time</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span :class="{
                                'status-online': connectionStatus === 'online',
                                'status-offline': connectionStatus === 'offline',
                                'status-loading': connectionStatus === 'loading'
                            }" class="status-indicator"></span>
                            <span class="text-sm font-medium" 
                                  :class="{
                                      'text-green-600': connectionStatus === 'online',
                                      'text-red-600': connectionStatus === 'offline',
                                      'text-yellow-600': connectionStatus === 'loading'
                                  }"
                                  x-text="connectionStatus === 'online' ? 'Online' : connectionStatus === 'offline' ? 'Offline' : 'Loading'"></span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">Auto Refresh</p>
                            <p class="text-sm text-gray-500">Data diperbarui setiap 30 detik</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Aktif</span>
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Footer -->
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
            <button @click="showSettings = false" 
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>
