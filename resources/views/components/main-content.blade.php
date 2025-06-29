<!-- Main Content -->
<div class="flex-1 flex flex-col overflow-hidden">
    <!-- Top Bar -->
    <header class="bg-white shadow-sm border-b border-gray-100 px-4 py-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <button x-show="isMobile" 
                        @click="toggleMobileMenu && toggleMobileMenu()"
                        class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                    <i data-lucide="menu" class="w-4 h-4 text-gray-600"></i>
                </button>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
                    <p class="text-sm text-gray-500">Selamat datang kembali, {{ auth()->user()->name ?? 'admin' }}!</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Connection Status -->
                <div class="hidden md:flex items-center space-x-2 px-3 py-2 bg-gray-50 rounded-lg">
                    <span :class="{
                        'status-online': connectionStatus === 'online',
                        'status-offline': connectionStatus === 'offline',
                        'status-loading': connectionStatus === 'loading'
                    }" class="status-indicator"></span>
                    <span class="text-xs text-gray-600" x-text="
                        connectionStatus === 'online' ? 'Online' :
                        connectionStatus === 'offline' ? 'Offline' : 'Memuat...'
                    "></span>
                    <span x-show="lastUpdated" class="text-xs text-gray-500">
                        • <span x-text="lastUpdated"></span>
                    </span>
                </div>

                <!-- Manual Refresh Button -->
                <button @click="refreshAll && refreshAll()" 
                        :disabled="isLoading"
                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors disabled:opacity-50">
                    <i data-lucide="refresh-cw" 
                       :class="{ 'loading-spinner': isLoading }"
                       class="w-4 h-4 text-gray-600"></i>
                </button>

                <!-- Light/Dark Mode Toggle -->
                <button @click="toggleDarkMode && toggleDarkMode()" 
                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i :data-lucide="isDarkMode ? 'moon' : 'sun'" class="w-4 h-4 text-gray-600"></i>
                </button>

                <!-- Notifications -->
                <button class="relative p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i data-lucide="bell" class="w-4 h-4 text-gray-600"></i>
                    <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                
                <!-- Date -->
                <div class="hidden lg:flex items-center space-x-2 px-3 py-2 bg-gray-50 rounded-lg">
                    <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                    <span class="text-sm text-gray-600">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                </div>

                <!-- User Dropdown -->
                <div class="relative user-dropdown">
                    <button @click="toggleUserDropdown && toggleUserDropdown()" 
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center shadow-sm">
                            <span class="text-white font-semibold text-xs">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name ?? 'admin' }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'admin@gmail.com' }}</p>
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
                         class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50"
                         x-cloak>
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name ?? 'admin' }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'admin@gmail.com' }}</p>
                        </div>
                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i data-lucide="user" class="w-4 h-4 mr-3 text-gray-400"></i>
                            Profile Saya
                        </a>
                        <button @click="openSettings && openSettings()" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i data-lucide="settings" class="w-4 h-4 mr-3 text-gray-400"></i>
                            Pengaturan
                        </button>
                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i data-lucide="help-circle" class="w-4 h-4 mr-3 text-gray-400"></i>
                            Bantuan
                        </a>
                        <hr class="my-2">
                        <button @click="logout && logout()" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i data-lucide="log-out" class="w-4 h-4 mr-3"></i>
                            Keluar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Dashboard Content -->
    <main class="flex-1 overflow-y-auto p-4">
        <!-- Loading Overlay -->
        <div x-show="isLoading && (!dashboardCards || dashboardCards.length === 0)" 
             class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-40"
             x-cloak>
            <div class="text-center">
                <div class="loading-spinner w-8 h-8 border-4 border-purple-500 border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="text-gray-600">Memuat data dashboard...</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <template x-for="(card, index) in (dashboardCards || [])" :key="index">
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group cursor-pointer border border-gray-200">
                    <!-- Card Header -->
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <div class="p-2 bg-gray-50 rounded-lg">
                                <i :data-lucide="card.icon" :class="card.iconColor" class="w-5 h-5"></i>
                            </div>
                            <div class="text-right">
                                <span :class="{
                                        'text-green-600 bg-green-50': card.changeType === 'positive',
                                        'text-gray-600 bg-gray-50': card.changeType === 'stable',
                                        'text-red-600 bg-red-50': card.changeType === 'negative'
                                      }"
                                      class="text-xs font-semibold px-2 py-1 rounded-full"
                                      x-text="card.change">
                                </span>
                            </div>
                        </div>

                        <!-- Title and Value -->
                        <div>
                            <h3 class="text-xs font-medium text-gray-600 mb-1" x-text="card.title"></h3>
                            <p class="text-xl font-bold text-gray-800 group-hover:scale-105 transition-transform duration-200" 
                               x-text="card.value">
                            </p>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4">
                        <p class="text-xs text-gray-500 mb-3" x-text="card.description"></p>
                        
                        <!-- Sub Cards -->
                        <div x-show="card.subCards" class="space-y-2">
                            <template x-for="subCard in (card.subCards || [])" :key="subCard.label">
                                <div class="flex justify-between items-center text-xs bg-gray-50 p-2 rounded-lg">
                                    <span class="text-gray-600" x-text="subCard.label"></span>
                                    <span class="font-semibold text-gray-800" x-text="subCard.value"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Charts Section Row 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Monthly Statistics -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Statistik Bulanan</h3>
                        <p class="text-sm text-gray-500">Tren data bulanan komprehensif</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="loadChartData && loadChartData('monthly')" 
                                class="p-1 rounded hover:bg-gray-100 transition-colors">
                            <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                        </button>
                        <select class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option>30 hari terakhir</option>
                            <option>7 hari terakhir</option>
                            <option>3 bulan terakhir</option>
                        </select>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <!-- Gender Distribution -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Distribusi Gender</h3>
                        <p class="text-sm text-gray-500">Perbandingan jumlah penduduk</p>
                    </div>
                    <button @click="loadGenderData && loadGenderData()" 
                            class="p-1 rounded hover:bg-gray-100 transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
                <div class="flex flex-col items-center">
                    <!-- Chart Container -->
                    <div class="relative w-40 h-40 mb-6">
                        <canvas id="genderChart"></canvas>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-800" x-text="(genderData && genderData.total) || 0"></p>
                                <p class="text-xs text-gray-500">Total</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Legend -->
                    <div class="w-full space-y-3">
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Laki-laki</span>
                            </div>
                            <span class="text-lg font-bold text-blue-600" x-text="(genderData && genderData.male) || 0"></span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-pink-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-pink-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Perempuan</span>
                            </div>
                            <span class="text-lg font-bold text-pink-600" x-text="(genderData && genderData.female) || 0"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section Row 2 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Revenue Bar Chart -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Pendapatan Bulanan</h3>
                        <p class="text-sm text-gray-500">Grafik batang pendapatan desa</p>
                    </div>
                    <button @click="loadChartData && loadChartData('revenue')" 
                            class="p-1 rounded hover:bg-gray-100 transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
                <div class="h-64">
                    <canvas id="revenueBarChart"></canvas>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Kategori UMKM</h3>
                        <p class="text-sm text-gray-500">Distribusi jenis usaha</p>
                    </div>
                    <button @click="loadChartData && loadChartData('category')" 
                            class="p-1 rounded hover:bg-gray-100 transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
                <div class="h-64">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Section Row 3 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Population Trend Area Chart -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Tren Populasi</h3>
                        <p class="text-sm text-gray-500">Pertumbuhan penduduk 6 tahun</p>
                    </div>
                    <button @click="loadChartData && loadChartData('population')" 
                            class="p-1 rounded hover:bg-gray-100 transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
                <div class="h-48">
                    <canvas id="populationAreaChart"></canvas>
                </div>
            </div>

            <!-- Age Distribution -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Distribusi Usia</h3>
                        <p class="text-sm text-gray-500">Kelompok usia berdasarkan gender</p>
                    </div>
                    <button @click="loadChartData && loadChartData('age')" 
                            class="p-1 rounded hover:bg-gray-100 transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
                <div class="h-48">
                    <canvas id="ageDistributionChart"></canvas>
                </div>
            </div>

            <!-- Village Ranking -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Ranking Desa</h3>
                        <p class="text-sm text-gray-500">Skor pembangunan desa</p>
                    </div>
                    <button @click="loadChartData && loadChartData('village')" 
                            class="p-1 rounded hover:bg-gray-100 transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
                <div class="h-48">
                    <canvas id="villageRankingChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
                    <p class="text-sm text-gray-500">Log aktivitas sistem terkini</p>
                </div>
                <button @click="loadActivities && loadActivities()" 
                        class="p-1 rounded hover:bg-gray-100 transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                </button>
            </div>
            <div class="space-y-3">
                <template x-for="(activity, index) in (activities || [])" :key="index">
                    <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-2 h-2 bg-purple-500 rounded-full flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate" x-text="activity.action"></p>
                            <p class="text-xs text-gray-500" x-text="activity.time"></p>
                        </div>
                    </div>
                </template>
                
                <!-- Empty State -->
                <div x-show="!activities || activities.length === 0" class="text-center py-8">
                    <i data-lucide="activity" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                    <p class="text-gray-500">Tidak ada aktivitas terbaru</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>
