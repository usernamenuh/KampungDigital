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

  <!-- RT Statistics Cards - 2x2 Grid on Mobile -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
      <!-- Total KK in RT -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4 lg:p-6 transition-all hover:shadow-md">
          <div class="flex items-center justify-between">
              <div>
                  <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Total KK</p>
                  <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 dark:text-white mt-1 sm:mt-2" x-text="totalKk">0</p>
                  <div class="flex items-center mt-1 sm:mt-2">
                      <span class="text-xs sm:text-sm text-blue-600 font-medium">Di RT Anda</span>
                  </div>
              </div>
              <div class="p-2 sm:p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                  <i data-lucide="home" class="w-4 h-4 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-blue-600 dark:text-blue-400"></i>
              </div>
          </div>
      </div>

      <!-- Total Penduduk in RT -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4 lg:p-6 transition-all hover:shadow-md">
          <div class="flex items-center justify-between">
              <div>
                  <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Total Penduduk</p>
                  <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 dark:text-white mt-1 sm:mt-2" x-text="totalPenduduk">0</p>
                  <div class="flex items-center mt-1 sm:mt-2">
                      <span class="text-xs sm:text-sm text-green-600 font-medium">Di RT Anda</span>
                  </div>
              </div>
              <div class="p-2 sm:p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                  <i data-lucide="users" class="w-4 h-4 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-green-600 dark:text-green-400"></i>
              </div>
          </div>
      </div>

      <!-- Total Kas Lunas in RT -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4 lg:p-6 transition-all hover:shadow-md">
          <div class="flex items-center justify-between">
              <div>
                  <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Kas Lunas</p>
                  <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 dark:text-white mt-1 sm:mt-2" x-text="kasLunas">0</p>
                  <div class="flex items-center mt-1 sm:mt-2">
                      <span class="text-xs sm:text-sm text-teal-600 font-medium">Tahun ini</span>
                  </div>
              </div>
              <div class="p-2 sm:p-3 bg-teal-100 dark:bg-teal-900 rounded-xl">
                  <i data-lucide="check-circle" class="w-4 h-4 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-teal-600 dark:text-teal-400"></i>
              </div>
          </div>
      </div>

      <!-- Total Kas Belum Bayar in RT -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4 lg:p-6 transition-all hover:shadow-md">
          <div class="flex items-center justify-between">
              <div>
                  <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Kas Belum Bayar</p>
                  <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 dark:text-white mt-1 sm:mt-2" x-text="kasBelumBayar">0</p>
                  <div class="flex items-center mt-1 sm:mt-2">
                      <span class="text-xs sm:text-sm text-red-600 font-medium">Perlu perhatian</span>
                  </div>
              </div>
              <div class="p-2 sm:p-3 bg-red-100 dark:bg-red-900 rounded-xl">
                  <i data-lucide="alert-triangle" class="w-4 h-4 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-red-600 dark:text-red-400"></i>
              </div>
          </div>
      </div>
  </div>

  <!-- Saldo RT dan Kas Terkumpul Cards -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Total Saldo RT Card -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between mb-4">
              <div class="flex items-center space-x-4">
                  <div class="p-3 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-xl">
                      <i data-lucide="wallet" class="w-8 h-8 text-white"></i>
                  </div>
                  <div>
                      <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Saldo RT</p>
                      <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1" x-text="formatRupiah(totalSaldoRt)">Rp 0</p>
                      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Saldo operasional RT</p>
                  </div>
              </div>
              <div class="flex flex-col space-y-2">
                  <button @click="showTransferKasModal = true" class="px-3 py-1.5 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition-colors">
                      <i data-lucide="arrow-down-circle" class="w-4 h-4 mr-1 inline"></i>
                      Transfer Kas
                  </button>
                  <button @click="showAddIncomeModal = true" class="px-3 py-1.5 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                      <i data-lucide="plus-circle" class="w-4 h-4 mr-1 inline"></i>
                      Tambah Pemasukan
                  </button>
                  <button @click="showAddExpenseModal = true" class="px-3 py-1.5 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors">
                      <i data-lucide="minus-circle" class="w-4 h-4 mr-1 inline"></i>
                      Catat Pengeluaran
                  </button>
              </div>
          </div>
      </div>

      <!-- Kas Terkumpul Card -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                  <div class="p-3 bg-gradient-to-r from-green-500 to-teal-500 rounded-xl">
                      <i data-lucide="piggy-bank" class="w-8 h-8 text-white"></i>
                  </div>
                  <div>
                      <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kas Terkumpul</p>
                      <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1" x-text="formatRupiah(kasAvailableForTransfer)">Rp 0</p>
                      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Belum ditransfer ke saldo</p>
                  </div>
              </div>
              <div class="text-right space-y-2">
                  <div class="flex items-center justify-end space-x-2">
                      <div class="w-3 h-3 bg-teal-500 rounded-full"></div>
                      <span class="text-sm text-gray-600 dark:text-gray-400">Lunas: <span class="font-semibold" x-text="kasLunas">0</span></span>
                  </div>
                  <div class="flex items-center justify-end space-x-2">
                      <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                      <span class="text-sm text-gray-600 dark:text-gray-400">Belum Bayar: <span class="font-semibold" x-text="kasBelumBayar">0</span></span>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Transfer Kas Modal -->
  <div x-show="showTransferKasModal" x-cloak
      x-transition:enter="ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95"
      x-transition:enter-end="opacity-100 scale-100"
      x-transition:leave="ease-in duration-200"
      x-transition:leave-start="opacity-100 scale-100"
      x-transition:leave-end="opacity-0 scale-95"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]"
      style="margin: 0; padding: 0; top: 0; left: 0; right: 0; bottom: 0;"
      @click.self="showTransferKasModal = false">
      <div class="modal-container bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-md mx-4 transform transition-all">
          <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Transfer Kas ke Saldo RT</h3>
          <form @submit.prevent="transferKasToSaldo()">
              <div class="mb-4">
                  <label for="transfer-amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Jumlah Transfer
                  </label>
                  <input type="text" id="transfer-amount" x-model="transferForm.displayAmount" 
                         @input="formatTransferAmount($event)" 
                         placeholder="0"
                         class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kas tersedia: <span x-text="formatRupiah(kasAvailableForTransfer)"></span></p>
              </div>
              <div class="mb-6">
                  <label for="transfer-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Keterangan (Opsional)
                  </label>
                  <textarea id="transfer-description" x-model="transferForm.description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Keterangan transfer kas..."></textarea>
              </div>
              <div class="flex justify-end space-x-3">
                  <button type="button" @click="showTransferKasModal = false"
                          class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                      Batal
                  </button>
                  <button type="submit" :disabled="transferForm.loading"
                          class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                      <span x-show="!transferForm.loading">Transfer</span>
                      <span x-show="transferForm.loading">Memproses...</span>
                  </button>
              </div>
          </form>
      </div>
  </div>

  <!-- Add Income Modal -->
  <div x-show="showAddIncomeModal" x-cloak
      x-transition:enter="ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95"
      x-transition:enter-end="opacity-100 scale-100"
      x-transition:leave="ease-in duration-200"
      x-transition:leave-start="opacity-100 scale-100"
      x-transition:leave-end="opacity-0 scale-95"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]"
      style="margin: 0; padding: 0; top: 0; left: 0; right: 0; bottom: 0;"
      @click.self="showAddIncomeModal = false">
      <div class="modal-container bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-md mx-4 transform transition-all">
          <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Tambah Pemasukan</h3>
          <form @submit.prevent="addIncome()">
              <div class="mb-4">
                  <label for="income-amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Jumlah Pemasukan
                  </label>
                  <input type="text" id="income-amount" x-model="incomeForm.displayAmount" 
                         @input="formatIncomeAmount($event)" 
                         placeholder="0"
                         class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
              </div>
              <div class="mb-6">
                  <label for="income-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Keterangan
                  </label>
                  <textarea id="income-description" x-model="incomeForm.description" rows="3" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Sumber pemasukan..."></textarea>
              </div>
              <div class="flex justify-end space-x-3">
                  <button type="button" @click="showAddIncomeModal = false"
                          class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                      Batal
                  </button>
                  <button type="submit" :disabled="incomeForm.loading"
                          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                      <span x-show="!incomeForm.loading">Tambah</span>
                      <span x-show="incomeForm.loading">Memproses...</span>
                  </button>
              </div>
          </form>
      </div>
  </div>

  <!-- Add Expense Modal -->
  <div x-show="showAddExpenseModal" x-cloak
      x-transition:enter="ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95"
      x-transition:enter-end="opacity-100 scale-100"
      x-transition:leave="ease-in duration-200"
      x-transition:leave-start="opacity-100 scale-100"
      x-transition:leave-end="opacity-0 scale-95"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]"
      style="margin: 0; padding: 0; top: 0; left: 0; right: 0; bottom: 0;"
      @click.self="showAddExpenseModal = false">
      <div class="modal-container bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-md mx-4 transform transition-all">
          <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Catat Pengeluaran</h3>
          <form @submit.prevent="addExpense()">
              <div class="mb-4">
                  <label for="expense-amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Jumlah Pengeluaran
                  </label>
                  <input type="text" id="expense-amount" x-model="expenseForm.displayAmount" 
                         @input="formatExpenseAmount($event)" 
                         placeholder="0"
                         class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Saldo tersedia: <span x-text="formatRupiah(totalSaldoRt)"></span></p>
              </div>
              <div class="mb-6">
                  <label for="expense-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Keterangan
                  </label>
                  <textarea id="expense-description" x-model="expenseForm.description" rows="3" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                            placeholder="Keperluan pengeluaran..."></textarea>
              </div>
              <div class="flex justify-end space-x-3">
                  <button type="button" @click="showAddExpenseModal = false"
                          class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                      Batal
                  </button>
                  <button type="submit" :disabled="expenseForm.loading"
                          class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                      <span x-show="!expenseForm.loading">Catat</span>
                      <span x-show="expenseForm.loading">Memproses...</span>
                  </button>
              </div>
          </form>
      </div>
  </div>

  <!-- Custom Alert Modal -->
  <div x-show="showAlertModal" x-cloak
      x-transition:enter="ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95"
      x-transition:enter-end="opacity-100 scale-100"
      x-transition:leave="ease-in duration-200"
      x-transition:leave-start="opacity-100 scale-100"
      x-transition:leave-end="opacity-0 scale-95"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]"
      style="margin: 0; padding: 0; top: 0; left: 0; right: 0; bottom: 0;"
      @click.self="showAlertModal = false">
      <div class="modal-container bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-sm mx-4 transform transition-all">
          <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4" x-text="alertTitle"></h3>
          <p class="text-gray-700 dark:text-gray-300 mb-6" x-text="alertMessage"></p>
          <div class="flex justify-end">
              <button @click="showAlertModal = false"
                      class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                  OK
              </button>
          </div>
      </div>
  </div>

  <!-- Informasi Rekening RT Card -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Informasi Rekening RT</h3>
              <div class="flex space-x-2">
                  <button @click="loadPaymentInfo()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                      <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
                  </button>
                  <a href="{{ route('payment-info.index') }}" class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                      <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Atur
                  </a>
              </div>
          </div>
          
          <div x-show="paymentInfo" class="space-y-4">
              <template x-if="paymentInfo && paymentInfo.has_bank_transfer">
                  <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                      <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                          <i data-lucide="banknote" class="w-5 h-5 text-blue-600"></i>
                      </div>
                      <div class="flex-1">
                          <p class="text-sm font-medium text-gray-800 dark:text-white">Transfer Bank</p>
                          <p class="text-xs text-gray-500 dark:text-gray-400" x-text="paymentInfo.bank_name + ' - ' + paymentInfo.bank_account_number"></p>
                          <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'A/N: ' + paymentInfo.bank_account_name"></p>
                      </div>
                  </div>
              </template>
              <template x-if="paymentInfo && paymentInfo.has_e_wallet">
                  <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                      <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                          <i data-lucide="wallet" class="w-5 h-5 text-purple-600"></i>
                      </div>
                      <div class="flex-1">
                          <p class="text-sm font-medium text-gray-800 dark:text-white">E-Wallet</p>
                          <template x-for="(walletData, walletName) in (paymentInfo ? paymentInfo.e_wallet_list : {})" :key="walletName">
                              <p class="text-xs text-gray-500 dark:text-gray-400" x-text="walletName.toUpperCase() + ': ' + walletData.number + ' (A/N: ' + walletData.name + ')'"></p>
                          </template>
                      </div>
                  </div>
              </template>
          </div>
          <div x-show="!paymentInfo" class="text-center text-gray-500 dark:text-gray-400 py-4">
              <p>Informasi rekening pembayaran belum diatur untuk RT Anda.</p>
          </div>
      </div>

      <!-- Riwayat Transaksi Saldo -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Riwayat Transaksi Saldo</h3>
              <button @click="loadSaldoHistory()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                  <i data-lucide="refresh-cw" class="w-4 h-4 text-gray-500"></i>
              </button>
          </div>
          
          <div class="space-y-3 max-h-96 overflow-y-auto">
              <template x-for="transaction in saldoHistory" :key="transaction.id">
                  <div class="flex items-center space-x-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                      <div :class="{
                          'bg-green-100 text-green-600': transaction.amount > 0,
                          'bg-red-100 text-red-600': transaction.amount < 0
                      }" class="p-2 rounded-lg">
                          <i :data-lucide="transaction.amount > 0 ? 'plus-circle' : 'minus-circle'" class="w-4 h-4"></i>
                      </div>
                      <div class="flex-1">
                          <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="transaction.description"></p>
                          <p class="text-xs text-gray-500 dark:text-gray-400" x-text="transaction.created_at_formatted"></p>
                      </div>
                      <div class="text-sm font-semibold" :class="{
                          'text-green-600': transaction.amount > 0,
                          'text-red-600': transaction.amount < 0
                      }" x-text="transaction.formatted_amount"></div>
                  </div>
              </template>
              <div x-show="saldoHistory.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                  <p>Belum ada transaksi saldo.</p>
              </div>
          </div>
      </div>
  </div>

  <!-- Quick Actions -->
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Aksi Cepat</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <a href="{{ route('kk.index') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl hover:from-blue-100 hover:to-blue-200 dark:hover:from-blue-800/30 dark:hover:to-blue-700/30 transition-all duration-200 transform hover:scale-105 border border-blue-200 dark:border-blue-700">
              <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mb-3">
                  <i data-lucide="home" class="w-5 h-5 text-white"></i>
              </div>
              <span class="text-xs font-semibold text-blue-700 dark:text-blue-300 text-center">Kelola KK</span>
          </a>
          <a href="{{ route('penduduk.index') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl hover:from-green-100 hover:to-green-200 dark:hover:from-green-800/30 dark:hover:to-green-700/30 transition-all duration-200 transform hover:scale-105 border border-green-200 dark:border-green-700">
              <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center mb-3">
                  <i data-lucide="users" class="w-5 h-5 text-white"></i>
              </div>
              <span class="text-xs font-semibold text-green-700 dark:text-green-300 text-center">Kelola Penduduk</span>
          </a>
          <a href="{{ route('kas.index') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl hover:from-purple-100 hover:to-purple-200 dark:hover:from-purple-800/30 dark:hover:to-purple-700/30 transition-all duration-200 transform hover:scale-105 border border-purple-200 dark:border-purple-700">
              <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center mb-3">
                  <i data-lucide="wallet" class="w-5 h-5 text-white"></i>
              </div>
              <span class="text-xs font-semibold text-purple-700 dark:text-purple-300 text-center">Kelola Kas</span>
          </a>
          <a href="{{ route('payments.list') }}" class="flex flex-col items-center p-4 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-xl hover:from-orange-100 hover:to-orange-200 dark:hover:from-orange-800/30 dark:hover:to-orange-700/30 transition-all duration-200 transform hover:scale-105 border border-orange-200 dark:border-orange-700">
              <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center mb-3">
                  <i data-lucide="receipt" class="w-5 h-5 text-white"></i>
              </div>
              <span class="text-xs font-semibold text-orange-700 dark:text-orange-300 text-center">Konfirmasi Pembayaran</span>
          </a>
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

function rtDashboardData() {
  return {
      currentDate: '',
      rtNumber: 'Loading...',
      rtId: null,
      totalKk: 0,
      totalPenduduk: 0,
      kasLunas: 0,
      kasBelumBayar: 0,
      totalSaldoRt: 0,
      kasTerkumpul: 0,
      kasAvailableForTransfer: 0,
      paymentInfo: null,
      saldoHistory: [],
      isOnline: true,
      
      // Modal states
      showTransferKasModal: false,
      showAddIncomeModal: false,
      showAddExpenseModal: false,
      showAlertModal: false,
      alertTitle: '',
      alertMessage: '',
      
      // Form data with display amounts for formatting
      transferForm: {
          amount: '',
          displayAmount: '',
          description: '',
          loading: false
      },
      incomeForm: {
          amount: '',
          displayAmount: '',
          description: '',
          loading: false
      },
      expenseForm: {
          amount: '',
          displayAmount: '',
          description: '',
          loading: false
      },

      async initDashboard() {
          console.log('🚀 Initializing RT Dashboard...');
          this.currentDate = new Date().toLocaleDateString('id-ID', { 
              weekday: 'long', 
              year: 'numeric', 
              month: 'long', 
              day: 'numeric' 
          });
          
          await this.loadDashboardData();
          await Promise.all([
              this.loadPaymentInfo(),
              this.loadSaldoHistory()
          ]);

          this.createLucideIcons();
          console.log('✅ RT Dashboard initialized successfully');
      },

      async loadDashboardData() {
          try {
              console.log('📊 Loading RT dashboard data...');
              const response = await fetch('/api/dashboard/stats', {
                  method: 'GET',
                  headers: {
                      'X-Requested-With': 'XMLHttpRequest',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                      'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                  }
              });
              
              if (response.ok) {
                  const data = await response.json();
                  if (data.success) {
                      this.rtNumber = data.data.rtNumber || 'N/A';
                      this.rtId = data.data.rtId;
                      this.totalKk = data.data.totalKk || 0;
                      this.totalPenduduk = data.data.totalPenduduk || 0;
                      this.kasLunas = data.data.kasLunas || 0;
                      this.kasBelumBayar = data.data.kasBelumBayar || 0;
                      this.totalSaldoRt = data.data.totalSaldoRt || 0;
                      this.kasTerkumpul = data.data.kasTerkumpul || 0;
                      this.kasAvailableForTransfer = data.data.kasAvailableForTransfer || 0;
                      this.isOnline = true;
                      console.log('✅ RT data loaded successfully:', data.data);
                  } else {
                      console.warn('API response success is false:', data.message);
                      this.isOnline = false;
                      this.showAlert('Gagal Memuat Data', data.message || 'Gagal memuat data dashboard.');
                  }
              } else {
                  const errorText = await response.text();
                  console.error('HTTP error loading RT dashboard data:', response.status, errorText);
                  this.isOnline = false;
                  this.showAlert('Kesalahan Jaringan', 'Terjadi kesalahan jaringan atau server saat memuat data dashboard.');
              }
          } catch (error) {
              console.error('❌ Error loading RT dashboard data:', error);
              this.isOnline = false;
              this.showAlert('Kesalahan', 'Terjadi kesalahan saat memuat data dashboard: ' + error.message);
          }
      },

      async loadPaymentInfo() {
          try {
              const response = await fetch('/api/payment-info/for-user-rt', { 
                  method: 'GET',
                  headers: {
                      'X-Requested-With': 'XMLHttpRequest',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                      'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                  }
              });
              
              if (response.ok) {
                  const data = await response.json();
                  if (data.success) {
                      this.paymentInfo = data.data;
                  }
              }
          } catch (error) {
              console.error('❌ Error loading payment info:', error);
          }
      },

      async loadSaldoHistory() {
          try {
              const response = await fetch('/api/saldo/history', {
                  method: 'GET',
                  headers: {
                      'X-Requested-With': 'XMLHttpRequest',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                      'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                  }
              });
              
              if (response.ok) {
                  const data = await response.json();
                  if (data.success) {
                      this.saldoHistory = data.data.transactions.slice(0, 10);
                      this.$nextTick(() => {
                          this.createLucideIcons();
                      });
                  }
              }
          } catch (error) {
              console.error('❌ Error loading saldo history:', error);
          }
      },

      // Number formatting functions
      formatNumber(value) {
          // Remove all non-digit characters
          const numericValue = value.replace(/\D/g, '');
          // Add thousand separators
          return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      },

      parseNumber(formattedValue) {
          // Remove dots and convert to number
          return parseInt(formattedValue.replace(/\./g, '')) || 0;
      },

      formatTransferAmount(event) {
          const formatted = this.formatNumber(event.target.value);
          this.transferForm.displayAmount = formatted;
          this.transferForm.amount = this.parseNumber(formatted);
      },

      formatIncomeAmount(event) {
          const formatted = this.formatNumber(event.target.value);
          this.incomeForm.displayAmount = formatted;
          this.incomeForm.amount = this.parseNumber(formatted);
      },

      formatExpenseAmount(event) {
          const formatted = this.formatNumber(event.target.value);
          this.expenseForm.displayAmount = formatted;
          this.expenseForm.amount = this.parseNumber(formatted);
      },

      async transferKasToSaldo() {
          if (this.transferForm.loading || !this.transferForm.amount) return;
          
          this.transferForm.loading = true;
          
          try {
              const response = await fetch('/api/saldo/transfer-kas', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                      'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                  },
                  body: JSON.stringify({
                      amount: this.transferForm.amount,
                      description: this.transferForm.description
                  })
              });

              const data = await response.json();
              
              if (data.success) {
                  this.showTransferKasModal = false;
                  this.transferForm = { amount: '', displayAmount: '', description: '', loading: false };
                  await this.loadDashboardData();
                  await this.loadSaldoHistory();
                  this.showAlert('Transfer Berhasil', 'Kas berhasil ditransfer ke saldo RT!');
              } else {
                  this.showAlert('Transfer Gagal', data.message || 'Gagal transfer kas');
              }
          } catch (error) {
              console.error('Error transferring kas:', error);
              this.showAlert('Kesalahan', 'Terjadi kesalahan saat transfer kas');
          } finally {
              this.transferForm.loading = false;
          }
      },

      async addIncome() {
          if (this.incomeForm.loading || !this.incomeForm.amount) return;
          
          this.incomeForm.loading = true;
          
          try {
              const response = await fetch('/api/saldo/add-income', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                      'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                  },
                  body: JSON.stringify({
                      amount: this.incomeForm.amount,
                      description: this.incomeForm.description
                  })
              });

              const data = await response.json();
              
              if (data.success) {
                  this.showAddIncomeModal = false;
                  this.incomeForm = { amount: '', displayAmount: '', description: '', loading: false };
                  await this.loadDashboardData();
                  await this.loadSaldoHistory();
                  this.showAlert('Pemasukan Berhasil', 'Pemasukan berhasil ditambahkan!');
              } else {
                  this.showAlert('Pemasukan Gagal', data.message || 'Gagal menambah pemasukan');
              }
          } catch (error) {
              console.error('Error adding income:', error);
              this.showAlert('Kesalahan', 'Terjadi kesalahan saat menambah pemasukan');
          } finally {
              this.incomeForm.loading = false;
          }
      },

      async addExpense() {
          if (this.expenseForm.loading || !this.expenseForm.amount) return;
          
          this.expenseForm.loading = true;
          
          try {
              const response = await fetch('/api/saldo/add-expense', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                      'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                  },
                  body: JSON.stringify({
                      amount: this.expenseForm.amount,
                      description: this.expenseForm.description
                  })
              });

              const data = await response.json();
              
              if (data.success) {
                  this.showAddExpenseModal = false;
                  this.expenseForm = { amount: '', displayAmount: '', description: '', loading: false };
                  await this.loadDashboardData();
                  await this.loadSaldoHistory();
                  this.showAlert('Pengeluaran Berhasil', 'Pengeluaran berhasil dicatat!');
              } else {
                  this.showAlert('Pengeluaran Gagal', data.message || 'Gagal mencatat pengeluaran');
              }
          } catch (error) {
              console.error('Error adding expense:', error);
              this.showAlert('Kesalahan', 'Terjadi kesalahan saat mencatat pengeluaran');
          } finally {
              this.expenseForm.loading = false;
          }
      },

      formatRupiah(amount) {
          return new Intl.NumberFormat('id-ID', {
              style: 'currency',
              currency: 'IDR',
              minimumFractionDigits: 0,
              maximumFractionDigits: 0
          }).format(amount);
      },

      showAlert(title, message) {
          this.alertTitle = title;
          this.alertMessage = message;
          this.showAlertModal = true;
      },

      createLucideIcons() {
          if (typeof lucide !== 'undefined') {
              lucide.createIcons();
          }
      }
  }
}
</script>
@endpush

@push('styles')
<style>
/* Ensure no top margin/padding on body and html */
html, body {
    margin: 0 !important;
    padding: 0 !important;
}

/* Fix modal positioning */
.fixed.inset-0 {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
}

/* Remove black border from modal containers */
.modal-container {
    border: 1px solid #e5e7eb !important; /* Light gray border */
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    outline: none !important;
}

.dark .modal-container {
    border: 1px solid #4b5563 !important; /* Dark gray border for dark mode */
}

/* Override any default dialog/modal borders */
div[role="dialog"], 
.bg-white.rounded-xl,
.dark\\:bg-gray-800.rounded-xl {
    border: 1px solid #e5e7eb !important;
    outline: none !important;
}

.dark div[role="dialog"],
.dark .bg-white.rounded-xl,
.dark .dark\\:bg-gray-800.rounded-xl {
    border: 1px solid #4b5563 !important;
}
</style>
@endpush
@endsection
