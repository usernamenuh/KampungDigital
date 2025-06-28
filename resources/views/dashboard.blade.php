<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Kampung Digital</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <!-- Axios for API calls -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50">
    <div x-data="dashboardData()" x-init="initDashboard()" class="min-h-screen">
        <!-- Simple Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">K</span>
                    </div>
                    <h1 class="text-xl font-bold text-gray-800">Dashboard Kampung Digital</h1>
                </div>
                
                <div class="flex items-center space-x-3">
                    <span :class="{
                        'text-green-600': connectionStatus === 'online',
                        'text-red-600': connectionStatus === 'offline',
                        'text-yellow-600': connectionStatus === 'loading'
                    }" class="text-sm font-medium" x-text="
                        connectionStatus === 'online' ? 'Online' :
                        connectionStatus === 'offline' ? 'Offline' : 'Loading...'
                    "></span>
                    
                    <button @click="loadData()" 
                            :disabled="isLoading"
                            class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 disabled:opacity-50">
                        <span x-show="!isLoading">Refresh</span>
                        <span x-show="isLoading">Loading...</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <template x-for="(card, index) in dashboardCards" :key="index">
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-gray-100 rounded-lg">
                                <i :data-lucide="card.icon" class="w-5 h-5 text-gray-600"></i>
                            </div>
                            <span class="text-sm font-medium text-green-600" x-text="card.change"></span>
                        </div>
                        <h3 class="text-sm font-medium text-gray-600 mb-1" x-text="card.title"></h3>
                        <p class="text-2xl font-bold text-gray-800" x-text="card.value"></p>
                        <p class="text-xs text-gray-500 mt-1" x-text="card.description"></p>
                    </div>
                </template>
            </div>

            <!-- Activities -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h3>
                <div class="space-y-3">
                    <template x-for="(activity, index) in activities" :key="index">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800" x-text="activity.action"></p>
                                <p class="text-xs text-gray-500" x-text="activity.time"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </main>
    </div>

    <script>
        function dashboardData() {
            return {
                dashboardCards: [],
                activities: [],
                connectionStatus: 'loading',
                isLoading: false,

                async loadData() {
                    this.isLoading = true;
                    this.connectionStatus = 'loading';

                    try {
                        // Test API connection first
                        const testResponse = await axios.get('/api/test');
                        console.log('API Test:', testResponse.data);

                        // Load stats
                        const statsResponse = await axios.get('/api/dashboard/stats');
                        this.dashboardCards = statsResponse.data.data;

                        // Load activities
                        const activitiesResponse = await axios.get('/api/dashboard/activities');
                        this.activities = activitiesResponse.data.data;

                        this.connectionStatus = 'online';
                        console.log('Data loaded successfully');
                    } catch (error) {
                        console.error('Error loading data:', error);
                        this.connectionStatus = 'offline';
                        
                        // Show fallback data
                        this.dashboardCards = [
                            {
                                title: 'Total Saldo',
                                value: 'Rp 15.000.000',
                                change: '+12.5%',
                                icon: 'wallet',
                                description: 'Dana bantuan'
                            },
                            {
                                title: 'Total Penduduk',
                                value: '2,430',
                                change: '+2.1%',
                                icon: 'users',
                                description: 'Jiwa terdaftar'
                            }
                        ];
                        
                        this.activities = [
                            { action: 'Data offline - periksa koneksi', time: 'Sekarang' }
                        ];
                    } finally {
                        this.isLoading = false;
                    }
                },

                async initDashboard() {
                    console.log('Initializing dashboard...');
                    
                    // Initialize Lucide icons
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                    
                    // Load initial data
                    await this.loadData();
                    
                    // Auto refresh every 30 seconds
                    setInterval(() => {
                        this.loadData();
                    }, 30000);
                }
            }
        }
    </script>
</body>
</html>
