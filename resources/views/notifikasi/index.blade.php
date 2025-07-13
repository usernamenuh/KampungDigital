@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('page-description', 'Kelola dan lihat semua notifikasi Anda.')

@section('content')
<div x-data="notifikasiData()" x-init="initNotifikasi()" class="p-6 space-y-6">
    <!-- Header with Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Notifikasi Anda</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola dan lihat semua notifikasi sistem</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Filter -->
                <select x-model="selectedFilter" @change="loadNotifikasi()" 
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Notifikasi</option>
                    <option value="unread">Belum Dibaca</option>
                    <option value="read">Sudah Dibaca</option>
                </select>

                <!-- Category Filter -->
                <select x-model="selectedCategory" @change="loadNotifikasi()" 
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Kategori</option>
                    <option value="pembayaran_kas">Pembayaran Kas</option>
                    <option value="sistem">Sistem</option>
                    <option value="pengumuman">Pengumuman</option>
                </select>

                <!-- Actions -->
                <button @click="markAllAsRead()" :disabled="unreadCount === 0" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Tandai Semua Dibaca
                </button>
                
                <button @click="deleteAll()" :disabled="notifikasi.length === 0" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Hapus Semua
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="mt-4 flex items-center space-x-6 text-sm">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                <span class="text-gray-600 dark:text-gray-400">Total: <span class="font-medium" x-text="totalNotifikasi"></span></span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                <span class="text-gray-600 dark:text-gray-400">Belum Dibaca: <span class="font-medium" x-text="unreadCount"></span></span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                <span class="text-gray-600 dark:text-gray-400">Sudah Dibaca: <span class="font-medium" x-text="readCount"></span></span>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <!-- Loading State -->
        <div x-show="isLoading" class="p-8 text-center">
            <svg class="w-8 h-8 animate-spin mx-auto mb-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <p class="text-gray-600 dark:text-gray-400">Memuat notifikasi...</p>
        </div>

        <!-- Empty State -->
        <div x-show="!isLoading && notifikasi.length === 0" class="p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 4.828A4 4 0 015.5 4H9v1a3 3 0 006 0V4h3.5c.5 0 .956.12 1.328.328l-8.656 8.656A4 4 0 0110.5 13H7v-1a3 3 0 00-6 0v1H.5a4 4 0 01.328-1.172l8.656-8.656z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Tidak Ada Notifikasi</h3>
            <p class="text-gray-500 dark:text-gray-400">Belum ada notifikasi untuk ditampilkan.</p>
        </div>

        <!-- Notifications -->
        <div x-show="!isLoading && notifikasi.length > 0" class="divide-y divide-gray-200 dark:divide-gray-700">
            <template x-for="item in notifikasi" :key="item.id">
                <div :class="!item.dibaca ? 'bg-blue-50 dark:bg-blue-900/20' : ''" 
                     class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-start space-x-4">
                        <!-- Icon -->
                        <div :class="{
                            'bg-green-100 text-green-600': item.tipe === 'success',
                            'bg-blue-100 text-blue-600': item.tipe === 'info',
                            'bg-yellow-100 text-yellow-600': item.tipe === 'warning',
                            'bg-red-100 text-red-600': item.tipe === 'error'
                        }" class="p-2 rounded-lg flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="item.tipe === 'success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path x-show="item.tipe === 'info'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path x-show="item.tipe === 'warning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                <path x-show="item.tipe === 'error'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white" x-text="item.judul"></h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="item.pesan"></p>
                                    
                                    <!-- Additional Data -->
                                    <div x-show="item.data && Object.keys(item.data).length > 0" class="mt-3 p-3 bg-gray-50 dark:bg-gray-600 rounded-lg">
                                        <template x-if="item.kategori === 'pembayaran_kas'">
                                            <div class="text-xs space-y-1">
                                                <p x-show="item.data.penduduk_nama"><span class="font-medium">Warga:</span> <span x-text="item.data.penduduk_nama"></span></p>
                                                <p x-show="item.data.minggu_ke"><span class="font-medium">Kas:</span> Minggu ke-<span x-text="item.data.minggu_ke"></span> Tahun <span x-text="item.data.tahun"></span></p>
                                                <p x-show="item.data.jumlah"><span class="font-medium">Jumlah:</span> <span x-text="formatCurrency(item.data.jumlah)"></span></p>
                                                <p x-show="item.data.metode_bayar"><span class="font-medium">Metode:</span> <span x-text="item.data.metode_bayar"></span></p>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2 ml-4">
                                    <span :class="{
                                        'bg-green-100 text-green-800': item.tipe === 'success',
                                        'bg-blue-100 text-blue-800': item.tipe === 'info',
                                        'bg-yellow-100 text-yellow-800': item.tipe === 'warning',
                                        'bg-red-100 text-red-800': item.tipe === 'error'
                                    }" class="px-2 py-1 rounded-full text-xs font-medium" x-text="item.kategori"></span>
                                    
                                    <button x-show="!item.dibaca" @click="markAsRead(item)" 
                                            class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                        Tandai Dibaca
                                    </button>
                                    
                                    <button @click="deleteNotifikasi(item)" 
                                            class="text-red-600 hover:text-red-800 text-xs font-medium">
                                        Hapus
                                    </button>
                                </div>
                            </div>

                            <!-- Timestamp and Status -->
                            <div class="flex items-center justify-between mt-3">
                                <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400">
                                    <span x-text="formatDateTime(item.created_at)"></span>
                                    <span x-show="!item.dibaca" class="flex items-center">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-1"></div>
                                        Belum dibaca
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Pagination -->
        <div x-show="!isLoading && notifikasi.length > 0" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <span x-text="((currentPage - 1) * perPage) + 1"></span> - 
                    <span x-text="Math.min(currentPage * perPage, totalNotifikasi)"></span> 
                    dari <span x-text="totalNotifikasi"></span> notifikasi
                </div>
                <div class="flex space-x-2">
                    <button @click="previousPage()" :disabled="currentPage <= 1" 
                            class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Sebelumnya
                    </button>
                    <button @click="nextPage()" :disabled="currentPage >= totalPages" 
                            class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function notifikasiData() {
    return {
        notifikasi: [],
        isLoading: true,
        selectedFilter: '',
        selectedCategory: '',
        currentPage: 1,
        perPage: 20,
        totalNotifikasi: 0,
        totalPages: 0,
        unreadCount: 0,
        readCount: 0,

        async initNotifikasi() {
            await this.loadNotifikasi();
        },

        async loadNotifikasi() {
            this.isLoading = true;
            try {
                const params = new URLSearchParams({
                    page: this.currentPage,
                    limit: this.perPage,
                    status: this.selectedFilter,
                    kategori: this.selectedCategory
                });

                const response = await fetch(`/notifikasi?${params}`);
                if (response.ok) {
                    const html = await response.text();
                    // This would need to be adapted to return JSON data
                    // For now, we'll simulate the data
                    this.notifikasi = [];
                    this.totalNotifikasi = 0;
                    this.unreadCount = 0;
                    this.readCount = 0;
                }
            } catch (error) {
                console.error('Error loading notifikasi:', error);
                this.showNotification('Gagal memuat notifikasi', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async markAsRead(item) {
            try {
                const response = await fetch(`/notifikasi/${item.id}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    item.dibaca = true;
                    this.unreadCount--;
                    this.readCount++;
                    this.showNotification('Notifikasi ditandai sudah dibaca', 'success');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error marking as read:', error);
                this.showNotification('Gagal menandai notifikasi', 'error');
            }
        },

        async markAllAsRead() {
            if (!confirm('Tandai semua notifikasi sebagai sudah dibaca?')) {
                return;
            }

            try {
                const response = await fetch('/notifikasi/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.notifikasi.forEach(item => item.dibaca = true);
                    this.readCount += this.unreadCount;
                    this.unreadCount = 0;
                    this.showNotification(data.message, 'success');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
                this.showNotification('Gagal menandai semua notifikasi', 'error');
            }
        },

        async deleteNotifikasi(item) {
            if (!confirm('Hapus notifikasi ini?')) {
                return;
            }

            try {
                const response = await fetch(`/notifikasi/${item.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    const index = this.notifikasi.findIndex(n => n.id === item.id);
                    if (index > -1) {
                        this.notifikasi.splice(index, 1);
                        this.totalNotifikasi--;
                        if (!item.dibaca) {
                            this.unreadCount--;
                        } else {
                            this.readCount--;
                        }
                    }
                    this.showNotification('Notifikasi berhasil dihapus', 'success');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error deleting notifikasi:', error);
                this.showNotification('Gagal menghapus notifikasi', 'error');
            }
        },

        async deleteAll() {
            if (!confirm('Hapus semua notifikasi? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }

            try {
                const response = await fetch('/notifikasi/delete-all', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.notifikasi = [];
                    this.totalNotifikasi = 0;
                    this.unreadCount = 0;
                    this.readCount = 0;
                    this.showNotification(data.message, 'success');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error deleting all notifikasi:', error);
                this.showNotification('Gagal menghapus semua notifikasi', 'error');
            }
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadNotifikasi();
            }
        },

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.loadNotifikasi();
            }
        },

        formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        },

        formatDateTime(dateTime) {
            if (!dateTime) return '-';
            return new Date(dateTime).toLocaleString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, 5000);
        }
    }
}
</script>
@endpush
@endsection
