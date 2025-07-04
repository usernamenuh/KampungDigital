@extends('layouts.app')

@section('title', 'Dashboard Administrator')
@section('page-title', 'Dashboard Administrator')
@section('page-description', 'Selamat datang, ' . auth()->user()->name . '! Kelola seluruh sistem kampung digital.')

@section('content')
<div x-data="adminDashboardData()" x-init="initDashboard()" class="p-6 space-y-6">
    <!-- Admin Info Header -->
    <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md bg-gradient-to-r from-purple-600 to-blue-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Administrator System</h2>
                <p class="text-purple-100">Sistem Manajemen Kampung Digital</p>
                <div class="flex items-center mt-2 space-x-4">
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                        <span class="text-sm text-purple-100">System Online</span>
                    </div>
                    <div class="flex items-center">
                        <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                        <span class="text-sm text-purple-100" x-text="usersOnline + ' Online'">0 Online</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-purple-100">Total Saldo Sistem</p>
                <p class="text-3xl font-bold" x-text="formatCurrency(totalSaldoSistem)">Rp 0</p>
                <p class="text-xs text-purple-200">Desa + RW + RT</p>
                <button @click="refreshSaldo()" class="text-xs text-purple-200 hover:text-white mt-1">
                    <i data-lucide="refresh-cw" class="w-3 h-3 inline mr-1"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- System Health Alert -->
    <div x-show="systemHealth.status !== 'healthy'" :class="getCardClass()" class="p-4 rounded-xl shadow-sm border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20">
        <div class="flex items-center">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-3"></i>
            <div>
                <h4 class="text-sm font-medium text-red-800 dark:text-red-200">Peringatan Sistem</h4>
                <p class="text-sm text-red-700 dark:text-red-300">Beberapa komponen sistem mengalami masalah. Periksa status sistem.</p>
            </div>
            <button @click="checkSystemHealth()" class="ml-auto bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">
                Periksa Sekarang
            </button>
        </div>
    </div>

    <!-- Saldo Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Saldo Desa -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Saldo Desa</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(totalSaldoDesa)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium" x-text="totalDesa + ' Desa'">0 Desa</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i data-lucide="landmark" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Saldo RW -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Saldo RW</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(totalSaldoRw)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-blue-600 font-medium" x-text="totalRw + ' RW'">0 RW</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i data-lucide="map-pin" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Saldo RT -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Saldo RT</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(totalSaldoRt)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-purple-600 font-medium" x-text="totalRt + ' RT'">0 RT</span>
                    </div>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-xl">
                    <i data-lucide="home" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">System Health</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2" x-text="systemHealth.status === 'healthy' ? '100%' : '85%'">100%</p>
                    <div class="flex items-center mt-2">
                        <div :class="systemHealth.status === 'healthy' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-1 rounded-full text-xs font-medium">
                            <span x-text="systemHealth.status === 'healthy' ? 'Healthy' : 'Warning'">Healthy</span>
                        </div>
                    </div>
                </div>
                <div class="p-3 bg-teal-100 dark:bg-teal-900 rounded-xl">
                    <i data-lucide="activity" class="w-6 h-6 text-teal-600 dark:text-teal-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Total Users -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Pengguna</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatNumber(totalUsers)">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-indigo-600 font-medium" x-text="usersOnline + ' Online'">0 Online</span>
                    </div>
                </div>
                <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-xl">
                    <i data-lucide="users" class="w-6 h-6 text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Penduduk -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Penduduk</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatNumber(totalPenduduk)">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium" x-text="pendudukAktif + ' Aktif'">0 Aktif</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i data-lucide="user-check" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Kas -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Terkumpul</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(totalKasTerkumpul)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-blue-600 font-medium" x-text="kasLunas + ' Lunas'">0 Lunas</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i data-lucide="wallet" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Kas Belum Bayar -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Belum Bayar</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(totalKasBelumBayar)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-red-600 font-medium" x-text="jumlahKasBelumBayar + ' Tagihan'">0 Tagihan</span>
                    </div>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-xl">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>

        <!-- Notifikasi -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Notifikasi</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatNumber(totalNotifikasi)">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-yellow-600 font-medium" x-text="notifikasiUnread + ' Unread'">0 Unread</span>
                    </div>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-xl">
                    <i data-lucide="bell" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart Kas Bulanan -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Tren Kas Bulanan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pembayaran kas 6 bulan terakhir</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="refreshChart('kas')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                    </button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="kasChart"></canvas>
            </div>
        </div>

        <!-- SEMUA Aktivitas Sistem -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Semua Aktivitas Sistem</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Real-time system activities</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="loadActivities()" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Refresh
                    </button>
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                </div>
            </div>
            
            <div class="space-y-3 max-h-96 overflow-y-auto">
                <template x-for="activity in activities" :key="activity.id">
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <div :class="{
                            'bg-green-100 text-green-600': activity.color === 'green',
                            'bg-blue-100 text-blue-600': activity.color === 'blue',
                            'bg-yellow-100 text-yellow-600': activity.color === 'yellow',
                            'bg-red-100 text-red-600': activity.color === 'red',
                            'bg-purple-100 text-purple-600': activity.color === 'purple'
                        }" class="p-2 rounded-lg flex-shrink-0">
                            <i :data-lucide="activity.icon" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-white truncate" x-text="activity.title"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="activity.description"></p>
                            <div class="flex items-center mt-1 space-x-2">
                                <span class="text-xs text-gray-400" x-text="activity.user"></span>
                                <span x-show="activity.location" class="text-xs text-gray-400" x-text="activity.location"></span>
                                <span x-show="activity.amount" class="text-xs font-medium text-green-600" x-text="formatCurrency(activity.amount)"></span>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400 flex-shrink-0" x-text="formatTime(activity.timestamp)"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- System Monitoring (Admin Only) -->
    <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">System Monitoring</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Real-time system performance</p>
            </div>
            <button @click="loadSystemMonitoring()" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Refresh
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">Server Load</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="systemMonitoring.serverLoad || '0.5'">0.5</p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">Memory Usage</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="(systemMonitoring.memoryUsage || 128) + ' MB'">128 MB</p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">Active Sessions</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="systemMonitoring.activeSessions || 25">25</p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">DB Connections</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white" x-text="systemMonitoring.dbConnections || 8">8</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Admin Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <a href="{{ route('users.index') }}" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                <i data-lucide="users" class="w-6 h-6 text-blue-600 mb-2"></i>
                <span class="text-sm font-medium text-blue-600">Kelola Users</span>
            </a>
            <a href="{{ route('penduduk.index') }}" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <i data-lucide="user-check" class="w-6 h-6 text-green-600 mb-2"></i>
                <span class="text-sm font-medium text-green-600">Data Penduduk</span>
            </a>
            <a href="{{ route('kas.index') }}" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                <i data-lucide="wallet" class="w-6 h-6 text-purple-600 mb-2"></i>
                <span class="text-sm font-medium text-purple-600">Kelola Kas</span>
            </a>
            <button @click="clearCache()" class="flex flex-col items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                <i data-lucide="trash-2" class="w-6 h-6 text-orange-600 mb-2"></i>
                <span class="text-sm font-medium text-orange-600">Clear Cache</span>
            </button>
            <button @click="exportData()" class="flex flex-col items-center p-4 bg-teal-50 dark:bg-teal-900/20 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-900/30 transition-colors">
                <i data-lucide="download" class="w-6 h-6 text-teal-600 mb-2"></i>
                <span class="text-sm font-medium text-teal-600">Export Data</span>
            </button>
            <button @click="showSystemLogs()" class="flex flex-col items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                <i data-lucide="file-text" class="w-6 h-6 text-red-600 mb-2"></i>
                <span class="text-sm font-medium text-red-600">System Logs</span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function adminDashboardData() {
    return {
        // Data properties
        totalSaldoDesa: 0,
        totalSaldoRw: 0,
        totalSaldoRt: 0,
        totalSaldoSistem: 0,
        totalUsers: 0,
        totalDesa: 0,
        totalRw: 0,
        totalRt: 0,
        totalPenduduk: 0,
        pendudukAktif: 0,
        totalKasTerkumpul: 0,
        totalKasBelumBayar: 0,
        jumlahKasBelumBayar: 0,
        kasLunas: 0,
        usersOnline: 0,
        totalNotifikasi: 0,
        notifikasiUnread: 0,
        activities: [],
        systemHealth: { status: 'healthy' },
        systemMonitoring: {},
        charts: { kas: null },
        
        // Settings
        cardStyle: localStorage.getItem('cardStyle') || 'default',
        chartTheme: localStorage.getItem('chartTheme') || 'default',
        isDarkMode: localStorage.getItem('darkMode') === 'true',
        
        async initDashboard() {
            console.log('🚀 Initializing Admin Dashboard...');
            
            this.setupEventListeners();
            await this.loadDashboardData();
            await this.loadActivities();
            await this.loadSystemMonitoring();
            
            setTimeout(() => {
                this.initializeCharts();
                lucide.createIcons();
            }, 100);
            
            // Auto refresh every 30 seconds
            setInterval(() => {
                this.loadActivities();
                this.loadSystemMonitoring();
            }, 30000);
            
            console.log('✅ Admin Dashboard initialized successfully');
        },
        
        setupEventListeners() {
            window.addEventListener('cardStyleChanged', (e) => {
                this.cardStyle = e.detail;
            });
            
            window.addEventListener('chartThemeChanged', (e) => {
                this.chartTheme = e.detail;
                this.updateAllCharts();
            });
            
            window.addEventListener('darkModeChanged', (e) => {
                this.isDarkMode = e.detail;
                this.updateAllCharts();
            });
        },
        
        async loadDashboardData() {
            try {
                console.log('📊 Loading admin dashboard data...');
                
                const response = await fetch('/api/dashboard/stats');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        Object.assign(this, data.data);
                    }
                }
                
                console.log('✅ Admin data loaded successfully');
            } catch (error) {
                console.error('❌ Error loading admin dashboard data:', error);
                if (window.showNotification) {
                    window.showNotification('Gagal memuat data dashboard', 'error');
                }
            }
        },
        
        async loadActivities() {
            try {
                const response = await fetch('/api/dashboard/activities?limit=20');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.activities = data.data;
                    }
                }
            } catch (error) {
                console.error('Error loading activities:', error);
            }
        },
        
        async loadSystemMonitoring() {
            try {
                const response = await fetch('/api/dashboard/system-monitoring');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.systemMonitoring = data.data;
                    }
                }
            } catch (error) {
                console.error('Error loading system monitoring:', error);
            }
        },
        
        async refreshSaldo() {
            try {
                await this.loadDashboardData();
                if (window.showNotification) {
                    window.showNotification('Saldo berhasil diperbarui', 'success');
                }
            } catch (error) {
                if (window.showNotification) {
                    window.showNotification('Gagal memperbarui saldo', 'error');
                }
            }
        },
        
        async clearCache() {
            try {
                const response = await fetch('/api/dashboard/clear-cache', { method: 'POST' });
                const data = await response.json();
                
                if (data.success) {
                    if (window.showNotification) {
                        window.showNotification('Cache berhasil dibersihkan', 'success');
                    }
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                if (window.showNotification) {
                    window.showNotification('Gagal membersihkan cache', 'error');
                }
            }
        },
        
        async checkSystemHealth() {
            try {
                const response = await fetch('/api/dashboard/system-health');
                const data = await response.json();
                
                if (data.success) {
                    this.systemHealth = data.data;
                    if (window.showNotification) {
                        window.showNotification('Status sistem berhasil diperiksa', 'success');
                    }
                }
            } catch (error) {
                if (window.showNotification) {
                    window.showNotification('Gagal memeriksa status sistem', 'error');
                }
            }
        },
        
        exportData() {
            if (window.showNotification) {
                window.showNotification('Fitur export data akan segera tersedia', 'info');
            }
        },
        
        showSystemLogs() {
            if (window.showNotification) {
                window.showNotification('Fitur system logs akan segera tersedia', 'info');
            }
        },
        
        getCardClass() {
            const baseClass = 'transition-all duration-300';
            
            switch (this.cardStyle) {
                case 'blur':
                    return `${baseClass} card-blur`;
                case 'gradient':
                    return `${baseClass} card-gradient`;
                case 'colored':
                    return `${baseClass} card-colored`;
                default:
                    return `${baseClass} card-default`;
            }
        },
        
        initializeCharts() {
            this.initKasChart();
        },
        
        initKasChart() {
            const ctx = document.getElementById('kasChart');
            if (!ctx) return;
            
            if (this.charts.kas) {
                this.charts.kas.destroy();
            }
            
            this.charts.kas = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Total Kas (Rp)',
                        data: [12000000, 15000000, 13000000, 18000000, 16000000, 20000000],
                        borderColor: this.getChartColors().primary,
                        backgroundColor: this.getChartColors().primaryAlpha,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: this.getChartOptions()
            });
        },
        
        getChartColors() {
            const themes = {
                default: {
                    primary: '#8B5CF6',
                    primaryAlpha: 'rgba(139, 92, 246, 0.1)'
                },
                ocean: {
                    primary: '#0EA5E9',
                    primaryAlpha: 'rgba(14, 165, 233, 0.1)'
                },
                forest: {
                    primary: '#10B981',
                    primaryAlpha: 'rgba(16, 185, 129, 0.1)'
                },
                sunset: {
                    primary: '#F59E0B',
                    primaryAlpha: 'rgba(245, 158, 11, 0.1)'
                }
            };
            
            return themes[this.chartTheme] || themes.default;
        },
        
        getChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000) + 'M';
                            },
                            color: this.isDarkMode ? '#9CA3AF' : '#6B7280'
                        },
                        grid: {
                            color: this.isDarkMode ? '#374151' : '#F3F4F6'
                        }
                    },
                    x: {
                        ticks: {
                            color: this.isDarkMode ? '#9CA3AF' : '#6B7280'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            };
        },
        
        updateAllCharts() {
            Object.keys(this.charts).forEach(key => {
                if (this.charts[key]) {
                    this.charts[key].destroy();
                }
            });
            
            setTimeout(() => {
                this.initializeCharts();
            }, 100);
        },
        
        async refreshChart(chartType) {
            try {
                await new Promise(resolve => setTimeout(resolve, 500));
                this.initKasChart();
                
                if (window.showNotification) {
                    window.showNotification('Chart berhasil diperbarui', 'success');
                }
            } catch (error) {
                if (window.showNotification) {
                    window.showNotification('Gagal memperbarui chart', 'error');
                }
            }
        },
        
        formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        },
        
        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num || 0);
        },
        
        formatTime(timestamp) {
            return new Date(timestamp).toLocaleString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                day: '2-digit',
                month: 'short'
            });
        }
    }
}
</script>
@endpush
@endsection
