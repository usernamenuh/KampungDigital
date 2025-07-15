@extends('layouts.app')

@section('title', 'Dashboard RT')
@section('page-title', 'Dashboard RT')
@section('page-description', 'Selamat datang, ' . auth()->user()->name . '! Kelola kas dan data RT Anda.')

@section('content')
<div x-data="rtDashboardData()" x-init="initDashboard()" class="p-6 space-y-6">
  <!-- RT Info Header -->
  <div class="p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 transition-all hover:shadow-lg bg-gradient-to-r from-emerald-600 to-cyan-600 text-white">
      <div class="flex items-center justify-between">
          <div>
              <h2 class="text-2xl font-bold" x-text="rtName">RT 001</h2>
              <p class="text-emerald-100" x-text="rwName + ' - ' + villageName">RW 01 - Desa Sukamaju</p>
          </div>
          <div class="text-right">
              <p class="text-sm text-emerald-100">Saldo RT</p>
              <p class="text-3xl font-bold" x-text="formatCurrency(balance)">Rp 0</p>
              <button @click="refreshBalance()" class="text-xs text-emerald-200 hover:text-white mt-1">
                  <i data-lucide="refresh-cw" class="w-3 h-3 inline mr-1"></i>
                  Refresh
              </button>
          </div>
      </div>
  </div>

  <!-- Saldo Management Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Saldo RT -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
          <div class="flex items-center justify-between">
              <div>
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Saldo Kas RT</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(balance)">Rp 0</p>
                  <div class="flex items-center mt-2">
                      <span class="text-sm text-emerald-600 font-medium">+4.1%</span>
                      <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">dana operasional</span>
                  </div>
              </div>
              <div class="p-3 bg-emerald-100 dark:bg-emerald-900 rounded-xl">
                  <i data-lucide="wallet" class="w-8 h-8 text-emerald-600 dark:text-emerald-400"></i>
              </div>
          </div>
      </div>

      <!-- Kas Masuk Bulan Ini -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
          <div class="flex items-center justify-between">
              <div>
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Masuk Bulan Ini</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(kasMasukBulanIni)">Rp 0</p>
                  <div class="flex items-center mt-2">
                      <span class="text-sm text-green-600 font-medium">Dari Warga</span>
                  </div>
              </div>
              <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                  <i data-lucide="trending-up" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
              </div>
          </div>
      </div>

      <!-- Iuran Mingguan -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
          <div class="flex items-center justify-between">
              <div>
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Iuran Mingguan</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatCurrency(iuranMingguan)">Rp 0</p>
                  <div class="flex items-center mt-2">
                      <span class="text-sm text-blue-600 font-medium">Per Warga</span>
                  </div>
              </div>
              <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                  <i data-lucide="calendar" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
              </div>
          </div>
      </div>
  </div>

  <!-- Stats Cards -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Total Warga -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
          <div class="flex items-center justify-between">
              <div>
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Warga</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="formatNumber(totalWarga)">0</p>
                  <div class="flex items-center mt-2">
                      <span class="text-sm text-blue-600 font-medium">Jiwa</span>
                  </div>
              </div>
              <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                  <i data-lucide="users" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
              </div>
          </div>
      </div>

      <!-- Kas Belum Bayar -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
          <div class="flex items-center justify-between">
              <div>
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Belum Bayar</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="kasBelumBayar">0</p>
                  <div class="flex items-center mt-2">
                      <span class="text-sm text-yellow-600 font-medium" x-text="formatCurrency(totalKasBelumBayar)">Rp 0</span>
                  </div>
              </div>
              <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-xl">
                  <i data-lucide="alert-triangle" class="w-8 h-8 text-yellow-600 dark:text-yellow-400"></i>
              </div>
          </div>
      </div>

      <!-- Kas Terlambat -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
          <div class="flex items-center justify-between">
              <div>
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Terlambat</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="kasTerlambat">0</p>
                  <div class="flex items-center mt-2">
                      <span class="text-sm text-red-600 font-medium">Perlu Tindakan</span>
                  </div>
              </div>
              <div class="p-3 bg-red-100 dark:bg-red-900 rounded-xl">
                  <i data-lucide="clock" class="w-8 h-8 text-red-600 dark:text-red-400"></i>
              </div>
          </div>
      </div>

      <!-- Kas Lunas -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all hover:shadow-md">
          <div class="flex items-center justify-between">
              <div>
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Lunas</p>
                  <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2" x-text="kasLunas">0</p>
                  <div class="flex items-center mt-2">
                      <span class="text-sm text-green-600 font-medium">Bulan Ini</span>
                  </div>
              </div>
              <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                  <i data-lucide="check-circle" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
              </div>
          </div>
      </div>
  </div>

  <!-- Alert Kas Terlambat -->
  <div x-show="kasTerlambat > 0" class="p-4 rounded-xl shadow-sm border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20">
      <div class="flex items-center">
          <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 mr-3"></i>
          <div>
              <h4 class="text-sm font-medium text-red-800 dark:text-red-200">Kas Terlambat!</h4>
              <p class="text-sm text-red-700 dark:text-red-300" x-text="`Ada ${kasTerlambat} warga yang terlambat membayar kas.`"></p>
          </div>
          <button class="ml-auto bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">
              Kirim Pengingat
          </button>
      </div>
  </div>

  <!-- Charts and Tables -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Daftar Warga -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between mb-6">
              <div>
                  <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Daftar Warga RT</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400">Status pembayaran kas terbaru</p>
              </div>
              <a href="{{ route('penduduk.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                  Lihat Semua
              </a>
          </div>
          
          <div class="space-y-3">
              <template x-for="warga in daftarWarga" :key="warga.id">
                  <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                      <div class="flex items-center space-x-3">
                          <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                              <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                          </div>
                          <div>
                              <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="warga.nama"></p>
                              <p class="text-xs text-gray-500 dark:text-gray-400" x-text="warga.alamat"></p>
                          </div>
                      </div>
                      <div class="text-right">
                          <span :class="{
                              'bg-green-100 text-green-800': warga.status === 'lunas',
                              'bg-yellow-100 text-yellow-800': warga.status === 'belum_bayar' || warga.status === 'menunggu_konfirmasi',
                              'bg-red-100 text-red-800': warga.status === 'terlambat',
                              'bg-gray-100 text-gray-800': warga.status === 'belum_ada_kas'
                          }" class="px-2 py-1 rounded-full text-xs font-medium" x-text="warga.statusText"></span>
                      </div>
                  </div>
              </template>
          </div>
      </div>

      <!-- Chart Kas Bulanan -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between mb-6">
              <div>
                  <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Kas Bulanan</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400">Tren pembayaran 6 bulan terakhir</p>
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
  </div>

  <!-- Quick Actions -->
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Aksi Cepat</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <a href="{{ route('kas.create') }}" class="flex flex-col items-center p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors">
              <i data-lucide="plus" class="w-8 h-8 text-emerald-600 mb-2"></i>
              <span class="text-sm font-medium text-emerald-600">Buat Tagihan</span>
          </a>
          <a href="{{ route('payments.list') }}" class="flex flex-col items-center p-4 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg hover:bg-cyan-100 dark:hover:bg-cyan-900/30 transition-colors">
              <i data-lucide="check-square" class="w-8 h-8 text-cyan-600 mb-2"></i>
              <span class="text-sm font-medium text-cyan-600">Konfirmasi Bayar</span>
          </a>
          <a href="{{ route('pengaturan-kas.index') }}" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
              <i data-lucide="credit-card" class="w-8 h-8 text-blue-600 mb-2"></i>
              <span class="text-sm font-medium text-blue-600">Kelola Info Bayar</span>
          </a>
          <button class="flex flex-col items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
              <i data-lucide="bell" class="w-8 h-8 text-yellow-600 mb-2"></i>
              <span class="text-sm font-medium text-yellow-600">Kirim Pengingat</span>
          </button>
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
      rtName: 'RT 001',
      rwName: 'RW 01',
      villageName: 'Desa Sukamaju',
      balance: 0,
      kasMasukBulanIni: 0,
      iuranMingguan: 0,
      totalWarga: 0,
      kasBelumBayar: 0,
      totalKasBelumBayar: 0,
      kasTerlambat: 0,
      kasLunas: 0,
      daftarWarga: [],
      charts: {
          kas: null
      },
      
      // Settings
      cardStyle: localStorage.getItem('cardStyle') || 'default',
      chartTheme: localStorage.getItem('chartTheme') || 'default',
      isDarkMode: localStorage.getItem('darkMode') === 'true',
      
      async initDashboard() {
          console.log('🚀 Initializing RT Dashboard...');
          
          this.setupEventListeners();
          await this.loadDashboardData();
          
          setTimeout(() => {
              this.initializeCharts();
              lucide.createIcons();
          }, 100);
          
          console.log('✅ RT Dashboard initialized successfully');
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
          
          window.addEventListener('dataRefresh', () => {
              this.loadDashboardData();
          });
      },
      
      async loadDashboardData() {
          try {
              console.log('📊 Loading RT dashboard data...');
              
              // LOAD REAL DATA FROM API
              const response = await fetch('/api/dashboard/stats', { // Corrected API route
                  method: 'GET',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-Requested-With': 'XMLHttpRequest',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                  },
                  credentials: 'same-origin'
              });
              
              if (response.ok) {
                  const data = await response.json();
                  if (data.success) {
                      // Assign real data
                      Object.assign(this, data.data);
                      console.log('✅ RT data loaded successfully:', data.data);
                  } else {
                      throw new Error(data.message || 'Failed to load data');
                  }
              } else {
                  throw new Error(`HTTP ${response.status}: ${response.statusText}`);
              }
              
          } catch (error) {
              console.error('❌ Error loading RT dashboard data:', error);
              if (window.showNotification) {
                  window.showNotification('Gagal memuat data dashboard: ' + error.message, 'error');
              }
          }
      },
      
      async refreshBalance() {
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

          // Fetch monthly kas data for RT
          fetch('/api/dashboard/monthly-kas') // Corrected API route
              .then(response => response.json())
              .then(data => {
                  const labels = data.success ? data.data.labels : ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
                  const values = data.success ? data.data.values : [0, 0, 0, 0, 0, 0];

                  this.charts.kas = new Chart(ctx, {
                      type: 'line',
                      data: {
                          labels: labels,
                          datasets: [{
                              label: 'Kas Terkumpul (Rp)',
                              data: values,
                              borderColor: this.getChartColors().primary,
                              backgroundColor: this.getChartColors().primaryAlpha,
                              borderWidth: 2,
                              fill: true,
                              tension: 0.4
                          }]
                      },
                      options: this.getChartOptions()
                  });
              })
              .catch(error => {
                  console.error('Error fetching monthly kas data for chart:', error);
                  // Fallback to default data if API fails
                  this.charts.kas = new Chart(ctx, {
                      type: 'line',
                      data: {
                          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                          datasets: [{
                              label: 'Kas Terkumpul (Rp)',
                              data: [800000, 950000, 1100000, 1050000, 1200000, 1200000],
                              borderColor: this.getChartColors().primary,
                              backgroundColor: this.getChartColors().primaryAlpha,
                              borderWidth: 2,
                              fill: true,
                              tension: 0.4
                          }]
                      },
                      options: this.getChartOptions()
                  });
              });
      },
      
      getChartColors() {
          const themes = {
              default: {
                  primary: '#10B981',
                  primaryAlpha: 'rgba(16, 185, 129, 0.1)'
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
                              return 'Rp ' + (value / 1000) + 'K';
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
      }
  }
}
</script>
@endpush
@endsection
