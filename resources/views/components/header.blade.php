<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-2 flex-shrink-0">
    <div class="flex items-center justify-between w-full">
        <div class="flex items-center gap-4">
            <!-- Mobile Sidebar Trigger -->
            <button @click="$store.app.toggleMobileMenu()" 
                    class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    title="Toggle Menu">
                <i data-lucide="menu" class="w-4 h-4 text-gray-600 dark:text-gray-300"></i>
            </button>

        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <!-- Current Time and Date -->
            <div class="hidden lg:flex flex-col items-end text-right mr-4">
                <div class="text-sm font-semibold text-gray-800 dark:text-white" x-text="$store.app.currentTime"></div>
                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="$store.app.currentDate"></div>
            </div>

            <!-- Refresh Button -->
            <button @click="$store.app.refreshAll()" 
                    :disabled="$store.app.isLoading"
                    class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors disabled:opacity-50 border border-gray-200 dark:border-gray-600"
                    title="Perbarui Data">
                <i data-lucide="refresh-cw" 
                   :class="{ 'loading-spinner': $store.app.isLoading }"
                   class="w-4 h-4 text-gray-600 dark:text-gray-300"></i>
            </button>

            <!-- Dark Mode Toggle -->
            <button @click="$store.app.toggleDarkMode()" 
                    class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors border border-gray-200 dark:border-gray-600"
                    title="Toggle Dark Mode">
                <i :data-lucide="$store.app.darkMode ? 'sun' : 'moon'" class="w-4 h-4 text-gray-600 dark:text-gray-300"></i>
            </button>

            <!-- Notifications Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" 
                        class="relative p-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors border border-gray-200 dark:border-gray-600"
                        title="Notifikasi">
                    <i data-lucide="bell" class="w-4 h-4 text-gray-600 dark:text-gray-300"></i>
                    <span x-show="$store.app.unreadCount > 0" 
                          x-text="$store.app.unreadCount > 99 ? '99+' : $store.app.unreadCount"
                          class="absolute -top-1 -right-1 min-w-[16px] h-[16px] bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold"></span>
                </button>

                <!-- Notification Dropdown -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50"
                     x-cloak>
                    
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Notifikasi</h3>
                        <button @click="$store.app.clearNotifications(); open = false" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            Hapus Semua
                        </button>
                    </div>
                    
                    <div class="max-h-80 overflow-y-auto">
                        <template x-for="notification in $store.app.notifications.slice(0, 10)" :key="notification.id">
                            <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <div class="flex items-start space-x-3">
                                    <div :class="{
                                        'bg-blue-500': notification.type === 'info',
                                        'bg-green-500': notification.type === 'success',
                                        'bg-yellow-500': notification.type === 'warning',
                                        'bg-red-500': notification.type === 'error'
                                    }" class="w-2 h-2 rounded-full mt-2 flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="notification.message"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="new Date(notification.timestamp).toLocaleString('id-ID')"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="$store.app.notifications.length === 0" class="px-4 py-8 text-center">
                            <i data-lucide="bell-off" class="w-8 h-8 text-gray-300 mx-auto mb-2"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" 
                        class="flex items-center transition-colors"
                        title="Menu Pengguna">
                   @php
            $user = Auth::user();
            $userName = $user->name ?? 'Guest';
            $userInitials = collect(explode(' ', $userName))
                                ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                ->take(2)
                                ->implode('');
            $userPhoto = optional($user->penduduk)->foto; // akses foto dari relasi penduduk
        @endphp
                    
                    @if($userPhoto)
            <img src="{{ asset('storage/' . $userPhoto) }}" 
                 alt="{{ $userName }}" 
                 class="h-8 w-8 rounded-full border-2 border-gray-300 dark:border-gray-600 hover:border-purple-500 transition-colors object-cover">
        @else
            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white text-sm font-semibold border-2 border-gray-300 dark:border-gray-600 hover:border-purple-500 transition-colors">
                {{ $userInitials }}
            </div>
        @endif
                </button>

                <!-- User Dropdown Menu -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50"
                     x-cloak>
                    
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $userName }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email ?? '' }}</p>
                    </div>
                    
                    <div class="py-1">
                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i data-lucide="user" class="w-4 h-4 mr-3 text-gray-400"></i>
                            Profil Saya
                        </a>
                        <button @click="$store.app.openSettings(); open = false" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i data-lucide="settings" class="w-4 h-4 mr-3 text-gray-400"></i>
                            Pengaturan
                        </button>
                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i data-lucide="help-circle" class="w-4 h-4 mr-3 text-gray-400"></i>
                            Bantuan
                        </a>
                        <hr class="my-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                <i data-lucide="log-out" class="w-4 h-4 mr-3"></i>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
