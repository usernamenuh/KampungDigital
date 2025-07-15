@extends('layouts.app')

@section('title', 'Dashboard RT')
@section('page-title', 'Dashboard RT')
@section('page-description', 'Selamat datang, ' . auth()->user()->name . '! Kelola data RT Anda.')

@section('content')
<div x-data="rtDashboardData()" x-init="initDashboard()" class="p-6 space-y-6">
    <!-- RT Info Header -->
    <div class="p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 transition-all hover:shadow-lg bg-gradient-to-r from-orange-500 to-red-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Selamat datang, RT</h2>
                <p class="text-orange-100">RT <span x-text="rtNumber"></span> - Kelola data di wilayah Anda</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-orange-100" x-text="currentDate"></p>
                <div class="flex items-center justify-end mt-2">
                    <div :class="isOnline ? 'bg-green-400 animate-pulse' : 'bg-red-400'" class="w-2 h-2 rounded-full mr-2"></div>
                    <span class="text-sm text-orange-100" x-text="isOnline ? 'Online' : 'Offline'">Online</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RT Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total KK in RT -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Kepala Keluarga</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="totalKk">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-blue-600 font-medium">Di RT Anda</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i data-lucide="home" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Penduduk in RT -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Penduduk</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="totalPenduduk">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium">Di RT Anda</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i data-lucide="users" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Kas Lunas in RT -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Lunas</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="kasLunas">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-teal-600 font-medium">Tahun ini</span>
                    </div>
                </div>
                <div class="p-3 bg-teal-100 dark:bg-teal-900 rounded-xl">
                    <i data-lucide="check-circle" class="w-8 h-8 text-teal-600 dark:text-teal-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Kas Belum Bayar in RT -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Belum Bayar</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="kasBelumBayar">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-red-600 font-medium">Perlu perhatian</span>
                    </div>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-xl">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Kas Data Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Data Kas Bulanan</h3>
        <canvas id="monthlyKasChart"></canvas>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Aktivitas Terbaru</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Log aktivitas di wilayah RT Anda.</p>
            </div>
            <button @click="loadActivities()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Refresh
            </button>
        </div>
        
        <div class="space-y-4 max-h-96 overflow-y-auto">
            <template x-for="activity in activities" :key="activity.id">
                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div :class="{
                        'bg-green-100 text-green-600': activity.color === 'green',
                        'bg-blue-100 text-blue-600': activity.color === 'blue',
                        'bg-yellow-100 text-yellow-600': activity.color === 'yellow',
                        'bg-red-100 text-red-600': activity.color === 'red',
                        'bg-purple-100 text-purple-600': activity.color === 'purple'
                    }" class="p-2 rounded-lg">
                        <i :data-lucide="activity.icon" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="activity.title"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.description"></p>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400" x-text="formatTime(activity.timestamp)"></div>
                </div>
            </template>
            <div x-show="activities.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                <p>Belum ada aktivitas terbaru.</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('kk.index') }}" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                <i data-lucide="home" class="w-8 h-8 text-blue-600 mb-2"></i>
                <span class="text-sm font-medium text-blue-600">Kelola KK</span>
            </a>
            <a href="{{ route('penduduk.index') }}" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <i data-lucide="users" class="w-8 h-8 text-green-600 mb-2"></i>
                <span class="text-sm font-medium text-green-600">Kelola Penduduk</span>
            </a>
            <a href="{{ route('kas.index') }}" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                <i data-lucide="wallet" class="w-8 h-8 text-purple-600 mb-2"></i>
                <span class="text-sm font-medium text-purple-600">Kelola Kas</span>
            </a>
            <a href="{{ route('payments.list') }}" class="flex flex-col items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                <i data-lucide="receipt" class="w-8 h-8 text-orange-600 mb-2"></i>
                <span class="text-sm font-medium text-orange-600">Konfirmasi Pembayaran</span>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

function rtDashboardData() {
    return {
        currentDate: '',
        rtNumber: 'Loading...',
        totalKk: 0,
        totalPenduduk: 0,
        kasLunas: 0,
        kasBelumBayar: 0,
        monthlyKasChart: null,
        activities: [],
        isOnline: true,
        connectionStatus: 'online',
        refreshInterval: null,

        async initDashboard() {
            console.log('🚀 Initializing RT Dashboard...');
            this.currentDate = new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            this.setupEventListeners();
            await this.loadAllData();
            this.startAutoRefresh();

            setTimeout(() => {
                lucide.createIcons();
            }, 100);
            
            console.log('✅ RT Dashboard initialized successfully');
        },

        async loadAllData() {
            await Promise.all([
                this.loadDashboardData(),
                this.loadMonthlyKasData(),
                this.loadActivities()
            ]);
        },

        setupEventListeners() {
            window.addEventListener('dataRefresh', () => {
                this.loadAllData();
            });

            window.addEventListener('online', () => {
                this.isOnline = true;
                this.connectionStatus = 'online';
                this.hideConnectionError();
                this.loadAllData();
            });

            window.addEventListener('offline', () => {
                this.isOnline = false;
                this.connectionStatus = 'offline';
                this.showConnectionError('Koneksi internet terputus');
            });
        },

        startAutoRefresh() {
            this.refreshInterval = setInterval(() => {
                if (this.isOnline) {
                    this.loadDashboardData();
                    this.loadMonthlyKasData();
                    this.loadActivities();
                }
            }, 30000); // Refresh every 30 seconds
        },

        async loadDashboardData() {
            try {
                console.log('📊 Loading RT dashboard data...');
                const response = await fetch('/api/dashboard/stats', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.rtNumber = data.data.rtNumber || 'N/A';
                        this.totalKk = data.data.totalKk || 0;
                        this.totalPenduduk = data.data.totalPenduduk || 0;
                        this.kasLunas = data.data.kasLunas || 0;
                        this.kasBelumBayar = data.data.kasBelumBayar || 0;
                        this.isOnline = true;
                        this.connectionStatus = 'online';
                        console.log('✅ RT data loaded successfully:', data.data);
                    } else {
                        throw new Error(data.message || 'Gagal memuat data');
                    }
                } else {
                    throw new Error('Response tidak OK: ' + response.status);
                }
            } catch (error) {
                console.error('❌ Error loading RT dashboard data:', error);
                this.isOnline = false;
                this.connectionStatus = 'offline';
                this.showConnectionError('Gagal memuat data dashboard');
            }
        },

        async loadMonthlyKasData() {
            try {
                console.log('📈 Loading monthly kas data...');
                const response = await fetch('/api/dashboard/monthly-kas', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.renderMonthlyKasChart(data.data);
                        console.log('✅ Monthly kas data loaded successfully:', data.data);
                    } else {
                        throw new Error(data.message || 'Gagal memuat data kas bulanan');
                    }
                } else {
                    throw new Error('Response tidak OK: ' + response.status);
                }
            } catch (error) {
                console.error('❌ Error loading monthly kas data:', error);
                this.showConnectionError('Gagal memuat data kas bulanan');
            }
        },

        renderMonthlyKasChart(chartData) {
            const ctx = document.getElementById('monthlyKasChart').getContext('2d');
            if (this.monthlyKasChart) {
                this.monthlyKasChart.destroy();
            }
            this.monthlyKasChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Jumlah Kas Terkumpul (Rp)',
                        data: chartData.values,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        },

        async loadActivities() {
            try {
                const response = await fetch('/api/dashboard/activities?limit=10', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.activities = data.data.map(activity => ({
                            ...activity,
                            icon: activity.icon || 'activity',
                            color: activity.color || 'blue'
                        }));
                    }
                }
            } catch (error) {
                console.error('Error loading activities:', error);
            }
        },

        showConnectionError(message) {
            this.hideConnectionError();
            const errorDiv = document.createElement('div');
            errorDiv.id = 'connection-error';
            errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            errorDiv.innerHTML = `
                <div class="flex items-center">
                    <i data-lucide="wifi-off" class="w-4 h-4 mr-2"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(errorDiv);
            setTimeout(() => {
                this.hideConnectionError();
            }, 5000);
        },

        hideConnectionError() {
            const errorDiv = document.getElementById('connection-error');
            if (errorDiv) {
                errorDiv.remove();
            }
        },
        
        formatTime(timestamp) {
            return new Date(timestamp).toLocaleString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                day: '2-digit',
                month: 'short'
            });
        },

        // Cleanup on component destroy
        destroy() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
            }
            if (this.monthlyKasChart) {
                this.monthlyKasChart.destroy();
            }
        }
    }
}
</script>
@endpush
@endsection
