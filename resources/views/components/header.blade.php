<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-100 dark:border-gray-700 px-6 py-4">
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
            <!-- Date and Time -->
            <div class="hidden lg:flex flex-col items-end text-right">
                <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="currentTime"></p>
                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="currentDate"></p>
            </div>
            
            <!-- Connection Status -->
            <div class="hidden md:flex items-center space-x-2 px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <span :class="{
                    'status-online': connectionStatus === 'online',
                    'status-offline': connectionStatus === 'offline',
                    'status-loading': connectionStatus === 'loading'
                }" class="status-indicator"></span>
                <span class="text-sm text-gray-600 dark:text-gray-300" 
                      x-text="connectionStatus === 'online' ? 'Online' : connectionStatus === 'offline' ? 'Offline' : 'Loading'"></span>
                <span x-show="lastRefresh" class="text-sm text-gray-500 dark:text-gray-400">• <span x-text="lastRefresh"></span></span>
            </div>

            <!-- Manual Refresh -->
            <button @click="refreshData()" 
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <i data-lucide="refresh-cw" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
            </button>

            <!-- Dark Mode Toggle -->
            <button @click="toggleDarkMode()" 
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <i :data-lucide="darkMode ? 'moon' : 'sun'" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
            </button>

            <!-- Notifications -->
            <button class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <i data-lucide="bell" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
                <span x-show="notifications.length > 0" 
                      class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full text-xs text-white flex items-center justify-center"
                      x-text="notifications.length"></span>
            </button>

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
                    <button @click="$dispatch('open-settings')" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <i data-lucide="settings" class="w-4 h-4 mr-3 text-gray-400"></i>
                        Pengaturan
                    </button>
                    <hr class="my-2 border-gray-100 dark:border-gray-700">
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900">
                                <i data-lucide="log-out" class="w-4 h-4 mr-3"></i>
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i data-lucide="log-in" class="w-4 h-4 mr-3"></i>
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>
