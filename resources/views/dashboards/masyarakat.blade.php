@extends('layouts.app')

@section('title', 'Dashboard Masyarakat')
@section('page-title', 'Dashboard Masyarakat')
@section('page-description', 'Selamat datang, ' . auth()->user()->name . '! Kelola tagihan kas Anda.')

@section('content')
<div x-data="masyarakatDashboardData()" x-init="initDashboard()" class="p-6 space-y-6">
    <!-- User Info Header -->
    <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold" x-text="userName">Nama Pengguna</h2>
                <p class="text-indigo-100" x-text="userAddress">Alamat Lengkap</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-indigo-100">Total Kas Belum Bayar</p>
                <p class="text-3xl font-bold" x-text="formatCurrency(totalKasBelumBayar)">Rp 0</p>
                <p class="text-xs text-indigo-200" x-text="kasBelumBayarCount + ' Tagihan'">0 Tagihan</p>
            </div>
        </div>
    </div>

    <!-- Alert Kas Belum Bayar -->
    <div x-show="kasBelumBayarCount > 0" :class="getCardClass()" class="p-4 rounded-xl shadow-sm border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20">
        <div class="flex items-center">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-3"></i>
            <div>
                <h4 class="text-sm font-medium text-red-800 dark:text-red-200">Kas Belum Dibayar!</h4>
                <p class="text-sm text-red-700 dark:text-red-300" x-text="`Anda memiliki ${kasBelumBayarCount} tagihan kas yang belum dibayar.`"></p>
            </div>
            <button class="ml-auto bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">
                Bayar Sekarang
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Kas Belum Bayar -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Belum Bayar</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="kasBelumBayarCount">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-red-600 font-medium" x-text="formatCurrency(totalKasBelumBayar)">Rp 0</span>
                    </div>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-xl">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>

        <!-- Kas Terlambat -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Terlambat</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="kasTerlambat">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-orange-600 font-medium">Perlu Segera</span>
                    </div>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-xl">
                    <i data-lucide="clock" class="w-8 h-8 text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
        </div>

        <!-- Kas Dibayar Tahun Ini -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Dibayar Tahun Ini</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(kasTahunIni)">Rp 0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-600 font-medium" x-text="kasLunasCount + ' Pembayaran'">0 Pembayaran</span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i data-lucide="check-circle" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Notifikasi -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Notifikasi Baru</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="notifikasiBaru">0</p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-blue-600 font-medium">Belum Dibaca</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i data-lucide="bell" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tagihan Kas -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Tagihan Kas Terbaru</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Daftar kas yang perlu dibayar</p>
                </div>
                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Lihat Semua
                </button>
            </div>
            
            <div class="space-y-4">
                <template x-for="kas in tagihanKas" :key="kas.id">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div :class="{
                                'bg-green-100 text-green-600': kas.status === 'lunas',
                                'bg-yellow-100 text-yellow-600': kas.status === 'belum_bayar',
                                'bg-red-100 text-red-600': kas.status === 'terlambat'
                            }" class="p-2 rounded-lg">
                                <i :data-lucide="kas.icon" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="`Minggu ke-${kas.minggu}`"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatCurrency(kas.jumlah)"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="`Jatuh tempo: ${kas.jatuhTempo}`"></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span :class="{
                                'bg-green-100 text-green-800': kas.status === 'lunas',
                                'bg-yellow-100 text-yellow-800': kas.status === 'belum_bayar',
                                'bg-red-100 text-red-800': kas.status === 'terlambat'
                            }" class="px-2 py-1 rounded-full text-xs font-medium" x-text="kas.statusText"></span>
                            <button x-show="kas.status !== 'lunas'" class="block mt-2 bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition-colors">
                                Bayar
                            </button>
                        </div>
                    </div>
                </template>
                
                <div x-show="tagihanKas.length === 0" class="text-center py-8">
                    <i data-lucide="check-circle" class="w-12 h-12 text-green-500 mx-auto mb-3"></i>
                    <h5 class="text-lg font-semibold text-gray-800 dark:text-white">Semua Kas Sudah Dibayar!</h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Terima kasih atas partisipasi Anda dalam kas RT.</p>
                </div>
            </div>
        </div>

        <!-- Riwayat Pembayaran -->
        <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Riwayat Pembayaran</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">5 pembayaran terakhir</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <template x-for="riwayat in riwayatPembayaran" :key="riwayat.id">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                                <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="`Minggu ke-${riwayat.minggu}`"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatCurrency(riwayat.jumlah)"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="`Dibayar: ${riwayat.tanggalBayar}`"></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Lunas</span>
                        </div>
                    </div>
                </template>
                
                <div x-show="riwayatPembayaran.length === 0" class="text-center py-8">
                    <i data-lucide="history" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat pembayaran</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifikasi Terbaru -->
    <div x-show="notifikasiTerbaru.length > 0" :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Notifikasi Terbaru</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Informasi penting untuk Anda</p>
            </div>
            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Lihat Semua
            </button>
        </div>
        
        <div class="space-y-4">
            <template x-for="notif in notifikasiTerbaru.slice(0, 5)" :key="notif.id">
                <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div :class="{
                        'bg-blue-100 text-blue-600': notif.type === 'info',
                        'bg-yellow-100 text-yellow-600': notif.type === 'warning',
                        'bg-green-100 text-green-600': notif.type === 'success',
                        'bg-red-100 text-red-600': notif.type === 'error'
                    }" class="p-2 rounded-lg">
                        <i :data-lucide="notif.icon" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="notif.judul"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="notif.pesan"></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2" x-text="notif.waktu"></p>
                    </div>
                    <button x-show="!notif.dibaca" @click="tandaiDibaca(notif.id)" class="text-blue-600 hover:text-blue-800 text-xs">
                        Tandai Dibaca
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Quick Actions -->
    <div :class="getCardClass()" class="p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                <i data-lucide="credit-card" class="w-8 h-8 text-blue-600 mb-2"></i>
                <span class="text-sm font-medium text-blue-600">Bayar Kas</span>
            </button>
            <button class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <i data-lucide="history" class="w-8 h-8 text-green-600 mb-2"></i>
                <span class="text-sm font-medium text-green-600">Riwayat Kas</span>
            </button>
            <button class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                <i data-lucide="bell" class="w-8 h-8 text-purple-600 mb-2"></i>
                <span class="text-sm font-medium text-purple-600">Notifikasi</span>
            </button>
            <button class="flex flex-col items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                <i data-lucide="user" class="w-8 h-8 text-orange-600 mb-2"></i>
                <span class="text-sm font-medium text-orange-600">Profil</span>
            </button>
        </div>
    </div>
</div>

<script>
function masyarakatDashboardData() {
    return {
        userName: 'Ahmad Suryadi',
        userAddress: 'RT 001, RW 01, Desa Sukamaju',
        totalKasBelumBayar: 0,
        kasBelumBayarCount: 0,
        kasTerlambat: 0,
        kasTahunIni: 0,
        kasLunasCount: 0,
        notifikasiBaru: 0,
        tagihanKas: [],
        riwayatPembayaran: [],
        notifikasiTerbaru: [],
        
        // Settings
        cardStyle: localStorage.getItem('cardStyle') || 'default',
        
        async initDashboard() {
            console.log('🚀 Initializing Masyarakat Dashboard...');
            
            this.setupEventListeners();
            await this.loadDashboardData();
            
            setTimeout(() => {
                lucide.createIcons();
            }, 100);
            
            console.log('✅ Masyarakat Dashboard initialized successfully');
        },
        
        setupEventListeners() {
            window.addEventListener('cardStyleChanged', (e) => {
                this.cardStyle = e.detail;
            });
            
            window.addEventListener('dataRefresh', () => {
                this.loadDashboardData();
            });
        },
        
        async loadDashboardData() {
            try {
                console.log('📊 Loading masyarakat dashboard data...');
                
                // Mock data - replace with actual API calls
                this.totalKasBelumBayar = 30000;
                this.kasBelumBayarCount = 3;
                this.kasTerlambat = 1;
                this.kasTahunIni = 480000;
                this.kasLunasCount = 48;
                this.notifikasiBaru = 2;
                
                this.tagihanKas = [
                    {
                        id: 1,
                        minggu: 50,
                        jumlah: 10000,
                        jatuhTempo: '15 Jan 2024',
                        status: 'terlambat',
                        statusText: 'Terlambat',
                        icon: 'alert-triangle'
                    },
                    {
                        id: 2,
                        minggu: 51,
                        jumlah: 10000,
                        jatuhTempo: '22 Jan 2024',
                        status: 'belum_bayar',
                        statusText: 'Belum Bayar',
                        icon: 'clock'
                    },
                    {
                        id: 3,
                        minggu: 52,
                        jumlah: 10000,
                        jatuhTempo: '29 Jan 2024',
                        status: 'belum_bayar',
                        statusText: 'Belum Bayar',
                        icon: 'clock'
                    }
                ];
                
                this.riwayatPembayaran = [
                    {
                        id: 1,
                        minggu: 49,
                        jumlah: 10000,
                        tanggalBayar: '10 Jan 2024'
                    },
                    {
                        id: 2,
                        minggu: 48,
                        jumlah: 10000,
                        tanggalBayar: '3 Jan 2024'
                    },
                    {
                        id: 3,
                        minggu: 47,
                        jumlah: 10000,
                        tanggalBayar: '27 Des 2023'
                    },
                    {
                        id: 4,
                        minggu: 46,
                        jumlah: 10000,
                        tanggalBayar: '20 Des 2023'
                    },
                    {
                        id: 5,
                        minggu: 45,
                        jumlah: 10000,
                        tanggalBayar: '13 Des 2023'
                    }
                ];
                
                this.notifikasiTerbaru = [
                    {
                        id: 1,
                        judul: 'Pengingat Kas Minggu ke-50',
                        pesan: 'Jatuh tempo pembayaran kas minggu ke-50 adalah hari ini.',
                        type: 'warning',
                        icon: 'alert-triangle',
                        waktu: '2 jam lalu',
                        dibaca: false
                    },
                    {
                        id: 2,
                        judul: 'Kas Minggu ke-49 Diterima',
                        pesan: 'Terima kasih, pembayaran kas minggu ke-49 telah diterima.',
                        type: 'success',
                        icon: 'check-circle',
                        waktu: '1 minggu lalu',
                        dibaca: false
                    }
                ];
                
                console.log('✅ Masyarakat data loaded successfully');
            } catch (error) {
                console.error('❌ Error loading masyarakat dashboard data:', error);
                if (window.showNotification) {
                    window.showNotification('Gagal memuat data dashboard', 'error');
                }
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
        
        tandaiDibaca(notifId) {
            const notif = this.notifikasiTerbaru.find(n => n.id === notifId);
            if (notif) {
                notif.dibaca = true;
                this.notifikasiBaru = Math.max(0, this.notifikasiBaru - 1);
                
                if (window.showNotification) {
                    window.showNotification('Notifikasi ditandai sebagai dibaca', 'success');
                }
            }
        },
        
        formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        },
        
        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num || 0);
        }
    }
}
</script>
@endsection
