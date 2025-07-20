@extends('layouts.app')

@section('title', 'Dashboard Masyarakat')
@section('page-title', 'Dashboard Masyarakat')
@section('page-description', 'Selamat datang, ' . auth()->user()->name . '! Kelola tagihan Kas Anda.')

@section('content')
<div x-data="masyarakatDashboardData()" x-init="initDashboard()" class="p-4 sm:p-6 space-y-6">
  <!-- Masyarakat Info Header -->
  <div class="p-3 md:p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 transition-all hover:shadow-lg bg-gradient-to-r from-blue-600 to-purple-700 text-white">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
          <div class="mb-3 sm:mb-0">
              <h2 class="text-base md:text-2xl font-bold">Selamat datang, Masyarakat</h2>
          </div>
          <div class="text-left sm:text-right w-full sm:w-auto flex items-center justify-between sm:justify-end space-x-2">
              <p class="text-xs md:text-base text-blue-200" x-text="currentDate"></p>
              <div class="flex items-center">
                  <div :class="isOnline ? 'bg-green-400 animate-pulse' : 'bg-red-400'" class="w-2 h-2 rounded-full mr-1"></div>
                  <span class="text-xs md:text-base text-blue-200" x-text="isOnline ? 'Online' : 'Offline'">Online</span>
              </div>
          </div>
      </div>
  </div>

  <!-- Payment Alert Section -->
  <div x-show="userKasBills.length > 0" 
       @click="document.getElementById('tagihan-mendatang-section').scrollIntoView({ behavior: 'smooth' })"
       class="w-full cursor-pointer">
      <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-3 flex items-center shadow-sm">
          <div class="p-2 bg-green-100 dark:bg-green-900 rounded-full mr-3 flex-shrink-0">
              <i data-lucide="wallet" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
          </div>
          <div class="flex-1">
              <p class="text-sm font-semibold text-green-800 dark:text-green-200 animate-pulse">💚 Ada Kas yang Perlu Dibayar!</p>
          </div>
      </div>
  </div>

  <!-- Kas Status Cards -->
  <div class="space-y-4 sm:space-y-6">
    <!-- First row: 4 status cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
      <!-- Kas Lunas -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4 lg:p-6 transition-all hover:shadow-md">
          <div class="flex flex-col items-start justify-between h-full">
              <div class="flex items-start justify-between w-full mb-auto">
                  <div class="flex-1">
                      <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Kas Lunas</p>
                      <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 dark:text-white mt-1" x-text="kasLunas">0</p>
                      <div class="flex items-center mt-1">
                          <span class="text-xs text-green-600 font-medium">Tahun ini</span>
                      </div>
                  </div>
                  <div class="p-1.5 sm:p-2 lg:p-3 bg-green-100 dark:bg-green-900 rounded-lg lg:rounded-xl ml-2">
                      <i data-lucide="check-circle" class="w-4 h-4 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-green-600 dark:text-green-400"></i>
                  </div>
              </div>
          </div>
      </div>

      <!-- Belum Bayar -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4 lg:p-6 transition-all hover:shadow-md">
          <div class="flex flex-col items-start justify-between h-full">
              <div class="flex items-start justify-between w-full mb-auto">
                  <div class="flex-1">
                      <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Belum Bayar</p>
                      <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 dark:text-white mt-1" x-text="kasBelumBayar">0</p>
                      <div class="flex items-center mt-1">
                          <span class="text-xs text-yellow-600 font-medium">Segera bayar</span>
                      </div>
                  </div>
                  <div class="p-1.5 sm:p-2 lg:p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg lg:rounded-xl ml-2">
                      <i data-lucide="alert-triangle" class="w-4 h-4 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-yellow-600 dark:text-yellow-400"></i>
                  </div>
              </div>
          </div>
      </div>

      <!-- Terlambat -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4 lg:p-6 transition-all hover:shadow-md">
          <div class="flex flex-col items-start justify-between h-full">
              <div class="flex items-start justify-between w-full mb-auto">
                  <div class="flex-1">
                      <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Terlambat</p>
                      <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 dark:text-white mt-1" x-text="kasTerlambat">0</p>
                      <div class="flex items-center mt-1">
                          <span class="text-xs text-red-600 font-medium">Perlu tindakan</span>
                      </div>
                  </div>
                  <div class="p-1.5 sm:p-2 lg:p-3 bg-red-100 dark:bg-red-900 rounded-lg lg:rounded-xl ml-2">
                      <i data-lucide="clock" class="w-4 h-4 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-red-600 dark:text-red-400"></i>
                  </div>
              </div>
          </div>
      </div>

      <!-- Kas Ditolak -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4 lg:p-6 transition-all hover:shadow-md">
          <div class="flex flex-col items-start justify-between h-full">
              <div class="flex items-start justify-between w-full mb-auto">
                  <div class="flex-1">
                      <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Kas Ditolak</p>
                      <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 dark:text-white mt-1" x-text="kasDitolak">0</p>
                      <div class="flex items-center mt-1">
                          <span class="text-xs text-red-600 font-medium">Perlu diperbaiki</span>
                      </div>
                  </div>
                  <div class="p-1.5 sm:p-2 lg:p-3 bg-red-100 dark:bg-red-900 rounded-lg lg:rounded-xl ml-2">
                      <i data-lucide="x-circle" class="w-4 h-4 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-red-600 dark:text-red-400"></i>
                  </div>
              </div>
          </div>
      </div>
    </div>
    
    <!-- Second row: Total Kas Anda card -->
    <div class="flex justify-center">
        <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 transition-all hover:shadow-md">
            <div class="flex flex-col items-start justify-between h-full">
                <div class="flex items-start justify-between w-full mb-auto">
                    <div class="flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Total Kas Anda</p>
                        <p class="text-3xl sm:text-4xl font-bold text-gray-800 dark:text-white mt-1 sm:mt-2" x-text="formatCurrency(totalKasAnda)">Rp 0</p>
                        <div class="flex items-center mt-1 sm:mt-2">
                            <span class="text-xs sm:text-sm text-purple-600 font-medium">Tahun ini</span>
                        </div>
                    </div>
                    <div class="p-2 sm:p-3 bg-purple-100 dark:bg-purple-900 rounded-xl">
                        <i data-lucide="wallet" class="w-8 h-8 sm:w-10 sm:h-10 text-purple-600 dark:text-purple-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- Tagihan Mendatang -->
  <div id="tagihan-mendatang-section" class="flex justify-center w-full">
      <div class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between mb-6">
              <div class="text-center flex-1">
                  <h3 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-white mb-2">📋 Tagihan Mendatang</h3>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Daftar tagihan kas yang perlu segera dibayar</p>
              </div>
              <button @click="loadUserKasBills()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                  <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
              </button>
          </div>
          
          <div class="space-y-4">
              <template x-for="bill in userKasBills" :key="bill.id">
                  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-700 dark:to-blue-900/20 rounded-xl border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all">
                      <!-- Left Section: Title and Details -->
                      <div class="flex flex-col flex-grow">
                          <h4 class="text-sm font-semibold text-gray-800 dark:text-white">
                              Minggu ke-<span x-text="bill.minggu_ke"></span>
                          </h4>
                          <!-- Status below Minggu ke- -->
                          <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                              Status: 
                              <span x-text="bill.status === 'belum_bayar' || bill.status === 'menunggu_konfirmasi' ? '⏳' : (bill.status === 'terlambat' ? '🚨' : (bill.status === 'ditolak' ? '❌' : '✅'))"></span>
                              <span :class="{
                                  'text-yellow-600': bill.status === 'belum_bayar' || bill.status === 'menunggu_konfirmasi',
                                  'text-red-600': bill.status === 'terlambat' || bill.status === 'ditolak',
                                  'text-green-600': bill.status === 'lunas'
                              }" class="font-semibold" x-text="bill.status_text"></span>
                          </p>

                          <!-- Details -->
                          <div class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                              <p>Jumlah: <span class="text-blue-600 font-semibold" x-text="formatCurrency(bill.jumlah)"></span></p>
                              <template x-if="bill.denda > 0">
                                  <p>Denda: <span class="text-red-600 font-semibold" x-text="formatCurrency(bill.denda)"></span></p>
                              </template>
                              <p>Jatuh Tempo: <span class="text-orange-600 font-semibold" x-text="bill.tanggal_jatuh_tempo_formatted"></span></p>
                              
                              <!-- Show rejection reason if status is ditolak -->
                              <template x-if="bill.status === 'ditolak' && bill.rejection_reason">
                                  <p class="mt-2 p-2 bg-red-50 dark:bg-red-900/20 rounded text-red-700 dark:text-red-300">
                                      <strong>Alasan Ditolak:</strong> <span x-text="bill.rejection_reason"></span>
                                  </p>
                              </template>
                          </div>
                      </div>

                      <!-- Buttons -->
                      <div class="flex-shrink-0 flex flex-col space-y-2 mt-4 sm:mt-0 sm:ml-4 w-full sm:w-auto">
                          <!-- Regular payment buttons for belum_bayar and terlambat -->
                          <template x-if="bill.can_pay && bill.status !== 'ditolak'">
                              <div class="flex flex-col space-y-2">
                                  <a :href="'/kas/' + bill.id + '/payment-form'" 
                                     class="inline-flex items-center justify-center px-3 py-1.5 bg-green-500 text-white text-xs font-medium rounded-md hover:bg-green-600 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                      <i data-lucide="credit-card" class="w-3 h-3 mr-1"></i>
                                      Bayar Sekarang
                                  </a>
                                  <a :href="'/kas/' + bill.id" 
                                     class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-500 text-white text-xs font-medium rounded-md hover:bg-blue-600 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                      <i data-lucide="info" class="w-3 h-3 mr-1"></i>
                                      Detail
                                  </a>
                              </div>
                          </template>
                          
                          <!-- Special button for rejected payments -->
                          <template x-if="bill.status === 'ditolak'">
                              <div class="flex flex-col space-y-2">
                                  <button @click="konfirmasiUlang(bill.id)" 
                                          class="inline-flex items-center justify-center px-3 py-1.5 bg-orange-500 text-white text-xs font-medium rounded-md hover:bg-orange-600 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                      <i data-lucide="refresh-cw" class="w-3 h-3 mr-1"></i>
                                      Konfirmasi Ulang
                                  </button>
                                  <a :href="'/kas/' + bill.id" 
                                     class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-500 text-white text-xs font-medium rounded-md hover:bg-blue-600 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                      <i data-lucide="info" class="w-3 h-3 mr-1"></i>
                                      Detail
                                  </a>
                              </div>
                          </template>
                          
                          <!-- Buttons for menunggu_konfirmasi -->
                          <template x-if="bill.status === 'menunggu_konfirmasi'">
                              <div class="flex flex-col space-y-2">
                                  <span class="inline-flex items-center justify-center px-3 py-1.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-md">
                                      <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                      Menunggu Konfirmasi
                                  </span>
                                  <a :href="'/kas/' + bill.id" 
                                     class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-500 text-white text-xs font-medium rounded-md hover:bg-blue-600 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                      <i data-lucide="info" class="w-3 h-3 mr-1"></i>
                                      Detail
                                  </a>
                              </div>
                          </template>
                      </div>
                  </div>
              </template>
              <div x-show="userKasBills.length === 0" class="text-center py-12">
                  <div class="w-16 h-16 mx-auto mb-4 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                      <i data-lucide="check-circle-2" class="w-8 h-8 text-green-600"></i>
                  </div>
                  <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">🎉 Semua Lunas!</h4>
                  <p class="text-gray-500 dark:text-gray-400">Tidak ada tagihan kas yang belum lunas. Keren banget!</p>
              </div>
          </div>
      </div>
  </div>

  <!-- Riwayat Pembayaran and Quick Actions -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Riwayat Pembayaran -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between mb-6">
              <div>
                  <h3 class="text-lg font-semibold text-gray-800 dark:text-white">📜 Riwayat Pembayaran</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400">Histori pembayaran kas yang sudah lunas.</p>
              </div>
              <button @click="loadPaymentHistory()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                  <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
              </button>
          </div>
          
          <div class="space-y-4 max-h-96 overflow-y-auto">
              <template x-for="payment in paymentHistory" :key="payment.id">
                  <div class="flex items-center space-x-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                      <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                          <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                      </div>
                      <div class="flex-1">
                          <p class="text-sm font-medium text-gray-800 dark:text-white">
                              ✅ Minggu ke-<span x-text="payment.minggu_ke"></span> Tahun <span x-text="payment.tahun"></span>
                          </p>
                          <p class="text-xs text-gray-500 dark:text-gray-400">
                              Jumlah: <span class="font-semibold text-green-600" x-text="formatCurrency(payment.jumlah)"></span>
                          </p>
                          <p class="text-xs text-gray-500 dark:text-gray-400">
                              Dibayar: <span x-text="payment.tanggal_bayar_formatted"></span>
                          </p>
                      </div>
                      <div class="text-xs text-green-600 font-medium">
                          LUNAS
                      </div>
                  </div>
              </template>
              <div x-show="paymentHistory.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-8">
                  <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                      <i data-lucide="history" class="w-8 h-8 text-gray-400"></i>
                  </div>
                  <p>Belum ada riwayat pembayaran.</p>
              </div>
          </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6">🚀 Aksi Cepat</h3>
          <div class="grid grid-cols-2 gap-4">
              <a href="{{ route('kas.index') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl hover:from-blue-100 hover:to-blue-200 dark:hover:from-blue-800/30 dark:hover:to-blue-700/30 transition-all duration-200 transform hover:scale-105 border border-blue-200 dark:border-blue-700">
                  <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mb-3">
                      <i data-lucide="wallet" class="w-5 h-5 text-white"></i>
                  </div>
                  <span class="text-xs font-semibold text-blue-700 dark:text-blue-300 text-center">Lihat Semua Tagihan</span>
              </a>
              
              <a href="{{ route('notifikasi.index') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl hover:from-green-100 hover:to-green-200 dark:hover:from-green-800/30 dark:hover:to-green-700/30 transition-all duration-200 transform hover:scale-105 border border-green-200 dark:border-green-700">
                  <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center mb-3">
                      <i data-lucide="bell" class="w-5 h-5 text-white"></i>
                  </div>
                  <span class="text-xs font-semibold text-green-700 dark:text-green-300 text-center">Notifikasi</span>
              </a>
              
              <a href="{{ route('profile.index') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl hover:from-purple-100 hover:to-purple-200 dark:hover:from-purple-800/30 dark:hover:to-purple-700/30 transition-all duration-200 transform hover:scale-105 border border-purple-200 dark:border-purple-700">
                  <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center mb-3">
                      <i data-lucide="user" class="w-5 h-5 text-white"></i>
                  </div>
                  <span class="text-xs font-semibold text-purple-700 dark:text-purple-300 text-center">Profil Saya</span>
              </a>
              
              <button @click="refreshAllData()" class="flex flex-col items-center p-4 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-xl hover:from-orange-100 hover:to-orange-200 dark:hover:from-orange-800/30 dark:hover:to-orange-700/30 transition-all duration-200 transform hover:scale-105 border border-orange-200 dark:border-orange-700">
                  <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center mb-3">
                      <i data-lucide="refresh-cw" class="w-5 h-5 text-white"></i>
                  </div>
                  <span class="text-xs font-semibold text-orange-700 dark:text-orange-300 text-center">Refresh Data</span>
              </button>
          </div>
      </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
});

function masyarakatDashboardData() {
return {
    currentDate: '',
    kasLunas: 0,
    kasBelumBayar: 0,
    kasTerlambat: 0,
    kasMenungguKonfirmasi: 0,
    kasDitolak: 0,
    totalKasAnda: 0,
    isYearCompleted: false,
    notifikasiUnread: 0,
    activities: [],
    paymentInfo: null,
    paymentHistory: [],
    userKasBills: [],
    paymentAlerts: [],
    isOnline: true,
    connectionStatus: 'online',
    refreshInterval: null,

    async initDashboard() {
        console.log('🚀 Initializing Masyarakat Dashboard...');
        this.currentDate = new Date().toLocaleDateString('id-ID', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        this.setupEventListeners();
        await this.loadAllData();
        this.startAutoRefresh();

        setTimeout(() => {
            lucide.createIcons();
        }, 100);
        
        console.log('✅ Masyarakat Dashboard initialized successfully');
    },

    async loadAllData() {
        await Promise.all([
            this.loadDashboardData(),
            this.loadActivities(),
            this.loadPaymentHistory(),
            this.loadUserKasBills(),
            this.loadPaymentAlerts()
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

        window.addEventListener('paymentCompleted', (event) => {
            this.loadAllData();
            this.showSuccessMessage('Pembayaran berhasil diproses!');
        });
    },

    startAutoRefresh() {
        this.refreshInterval = setInterval(() => {
            if (this.isOnline) {
                this.loadDashboardData();
                this.loadPaymentAlerts();
                this.loadUserKasBills();
                this.loadPaymentHistory();
            }
        }, 30000);
    },

    async refreshAllData() {
        console.log('🔄 Refreshing all dashboard data...');
        await this.loadAllData();
        this.showSuccessMessage('Data berhasil diperbarui!');
    },

    async loadDashboardData() {
        try {
            console.log('📊 Loading masyarakat dashboard data...');
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
                    Object.assign(this, data.data);
                    this.isOnline = true;
                    this.connectionStatus = 'online';
                    console.log('✅ Masyarakat data loaded successfully:', data.data);
                } else {
                    throw new Error(data.message || 'Gagal memuat data');
                }
            } else {
                throw new Error('Response tidak OK: ' + response.status);
            }
        } catch (error) {
            console.error('❌ Error loading masyarakat dashboard data:', error);
            this.isOnline = false;
            this.connectionStatus = 'offline';
            this.showConnectionError('Gagal memuat data dashboard');
        }
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

    async loadPaymentHistory() {
        try {
            console.log('📜 Loading payment history...');
            const response = await fetch('/api/payment/index?status=lunas&limit=10', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.paymentHistory = data.data;
                    console.log('✅ Payment history loaded successfully:', data.data);
                } else {
                    this.paymentHistory = [];
                    console.warn('⚠️ No payment history found:', data.message);
                }
            } else {
                this.paymentHistory = [];
                console.error('❌ Failed to load payment history: HTTP ' + response.status);
            }
        } catch (error) {
            this.paymentHistory = [];
            console.error('❌ Error loading payment history:', error);
        }
    },

    async loadUserKasBills() {
        try {
            console.log('🧾 Loading user kas bills...');
            const response = await fetch('/api/payment/index?status=belum_bayar,terlambat,menunggu_konfirmasi,ditolak', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.userKasBills = data.data;
                    console.log('✅ User kas bills loaded successfully:', data.data);
                    
                    window.dispatchEvent(new CustomEvent('kasBillsUpdated', {
                        detail: { bills: this.userKasBills }
                    }));
                } else {
                    this.userKasBills = [];
                    console.warn('⚠️ No user kas bills found:', data.message);
                }
            } else {
                this.userKasBills = [];
                console.error('❌ Failed to load user kas bills: HTTP ' + response.status);
            }
        } catch (error) {
            this.userKasBills = [];
            console.error('❌ Error loading user kas bills:', error);
        }
    },

    async loadPaymentAlerts() {
        try {
            console.log('🚨 Loading payment alerts...');
            const response = await fetch('/api/dashboard/payment-alerts', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.paymentAlerts = data.data;
                    console.log('✅ Payment alerts loaded successfully:', data.data);
                    
                    if (data.has_overdue && 'Notification' in window) {
                        this.showBrowserNotification();
                    }
                } else {
                    this.paymentAlerts = [];
                    console.warn('⚠️ No payment alerts found:', data.message);
                }
            } else {
                this.paymentAlerts = [];
                console.error('❌ Failed to load payment alerts: HTTP ' + response.status);
            }
        } catch (error) {
            this.paymentAlerts = [];
            console.error('❌ Error loading payment alerts:', error);
        }
    },

    async konfirmasiUlang(kasId) {
        try {
            console.log('🔄 Konfirmasi ulang kas ID:', kasId);
            
            const response = await fetch(`/kas/${kasId}/konfirmasi-ulang`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.showSuccessMessage(data.message || 'Kas berhasil dikonfirmasi ulang!');
                await this.loadAllData(); // Refresh all data
            } else {
                this.showErrorMessage(data.message || 'Gagal mengkonfirmasi ulang kas');
            }
        } catch (error) {
            console.error('❌ Error konfirmasi ulang:', error);
            this.showErrorMessage('Terjadi kesalahan saat mengkonfirmasi ulang kas');
        }
    },

    showBrowserNotification() {
        if (Notification.permission === 'granted') {
            new Notification('Pembayaran Kas Terlambat!', {
                body: 'Anda memiliki pembayaran kas yang sudah terlambat. Segera lakukan pembayaran.',
                icon: '/favicon.ico'
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    this.showBrowserNotification();
                }
            });
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

    showSuccessMessage(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        successDiv.innerHTML = `
            <div class="flex items-center">
                <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(successDiv);
        setTimeout(() => {
            successDiv.remove();
        }, 3000);
    },

    showErrorMessage(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        errorDiv.innerHTML = `
            <div class="flex items-center">
                <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(errorDiv);
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    },

    hideConnectionError() {
        const errorDiv = document.getElementById('connection-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    },
    
    formatCurrency(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
    },
    
    formatTime(timestamp) {
        return new Date(timestamp).toLocaleString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            day: '2-digit',
            month: 'short'
        });
    },

    destroy() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
    }
}
}
</script>
@endpush
@endsection
