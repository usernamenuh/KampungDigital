@extends('layouts.app')

@section('title', 'Daftar Pembayaran Kas')
@section('page-title', 'Daftar Pembayaran Kas')
@section('page-description', 'Kelola dan konfirmasi pembayaran kas dari warga.')

@section('content')
<div x-data="paymentsListData()" x-init="initPaymentsList()" class="p-6 space-y-6">
    <!-- Header with Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Filter Pembayaran</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Cari dan filter pembayaran yang perlu dikonfirmasi</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search -->
                <div class="relative">
                    <input type="text" x-model="searchQuery" @input="debounceSearch()" 
                           placeholder="Cari nama atau NIK..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <!-- RT Filter -->
                <select x-model="selectedRt" @change="loadPayments()" 
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">Semua RT</option>
                    @foreach($rts as $rt)
                        <option value="{{ $rt->id }}">RT {{ $rt->no_rt }}</option>
                    @endforeach
                </select>

                <!-- Method Filter -->
                <select x-model="selectedMethod" @change="loadPayments()" 
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Metode</option>
                    <option value="tunai">Tunai</option>
                    <option value="bank_transfer">Transfer Bank</option>
                    <option value="e_wallet">E-Wallet</option>
                    <option value="qr_code">QR Code</option>
                </select>

                <!-- Refresh Button -->
                <button @click="refreshPayments()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Payments List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <!-- Loading State -->
        <div x-show="isLoading" class="p-8 text-center">
            <svg class="w-8 h-8 animate-spin mx-auto mb-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <p class="text-gray-600 dark:text-gray-400">Memuat data pembayaran...</p>
        </div>

        <!-- Empty State -->
        <div x-show="!isLoading && payments.length === 0" class="p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Tidak Ada Pembayaran</h3>
            <p class="text-gray-500 dark:text-gray-400">Belum ada pembayaran yang perlu dikonfirmasi.</p>
        </div>

        <!-- Payments Table -->
        <div x-show="!isLoading && payments.length > 0" class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Warga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu Upload</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="payment in payments" :key="payment.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="payment.penduduk.nama_lengkap"></div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400" x-text="`RT ${payment.rt.no_rt} / RW ${payment.rt.rw.no_rw}`"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white" x-text="`Minggu ke-${payment.minggu_ke}`"></div>
                                <div class="text-sm text-gray-500 dark:text-gray-400" x-text="`Tahun ${payment.tahun}`"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-green-600" x-text="formatCurrency(payment.total_bayar)"></div>
                                <div x-show="payment.denda > 0" class="text-xs text-red-500" x-text="`Denda: ${formatCurrency(payment.denda)}`"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200" x-text="payment.metode_bayar_formatted"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="formatDateTime(payment.bukti_bayar_uploaded_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button @click="viewProof(payment)" 
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    Lihat Bukti
                                </button>
                                <button @click="approvePayment(payment)" 
                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                    Setujui
                                </button>
                                <button @click="rejectPayment(payment)" 
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    Tolak
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div x-show="!isLoading && payments.length > 0" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <span x-text="((currentPage - 1) * perPage) + 1"></span> - 
                    <span x-text="Math.min(currentPage * perPage, totalPayments)"></span> 
                    dari <span x-text="totalPayments"></span> pembayaran
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

    <!-- Confirmation Modal -->
    <div x-show="showConfirmModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white" x-text="confirmAction === 'approve' ? 'Konfirmasi Pembayaran' : 'Tolak Pembayaran'"></h3>
                    <button @click="closeConfirmModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4" x-show="selectedPayment">
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Warga: <span class="font-medium" x-text="selectedPayment?.penduduk?.nama_lengkap"></span></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Kas: <span class="font-medium" x-text="`Minggu ke-${selectedPayment?.minggu_ke} Tahun ${selectedPayment?.tahun}`"></span></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Jumlah: <span class="font-medium text-green-600" x-text="formatCurrency(selectedPayment?.total_bayar)"></span></p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (Opsional)</label>
                    <textarea x-model="confirmationNotes" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" 
                              placeholder="Tambahkan catatan konfirmasi..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button @click="closeConfirmModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button @click="processConfirmation()" :disabled="isProcessing" 
                            :class="confirmAction === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'" 
                            class="px-4 py-2 text-white rounded-lg transition-colors disabled:opacity-50">
                        <span x-show="!isProcessing" x-text="confirmAction === 'approve' ? 'Setujui' : 'Tolak'"></span>
                        <span x-show="isProcessing">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Proof Modal -->
    <div x-show="showProofModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Bukti Pembayaran</h3>
                    <button @click="closeProofModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div x-show="selectedPayment" class="space-y-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Warga:</p>
                                <p class="font-medium" x-text="selectedPayment?.penduduk?.nama_lengkap"></p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">RT/RW:</p>
                                <p class="font-medium" x-text="`RT ${selectedPayment?.rt?.no_rt} / RW ${selectedPayment?.rt?.rw?.no_rw}`"></p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Kas:</p>
                                <p class="font-medium" x-text="`Minggu ke-${selectedPayment?.minggu_ke} Tahun ${selectedPayment?.tahun}`"></p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Jumlah:</p>
                                <p class="font-medium text-green-600" x-text="formatCurrency(selectedPayment?.total_bayar)"></p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Metode:</p>
                                <p class="font-medium" x-text="selectedPayment?.metode_bayar_formatted"></p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Waktu Upload:</p>
                                <p class="font-medium" x-text="formatDateTime(selectedPayment?.bukti_bayar_uploaded_at)"></p>
                            </div>
                        </div>
                        <div x-show="selectedPayment?.bukti_bayar_notes" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Catatan:</p>
                            <p class="text-gray-900 dark:text-white" x-text="selectedPayment?.bukti_bayar_notes"></p>
                        </div>
                    </div>

                    <div class="text-center">
                        <img x-show="selectedPayment?.bukti_bayar_file" 
                             :src="selectedPayment?.bukti_bayar_url" 
                             alt="Bukti Pembayaran" 
                             class="max-w-full h-auto rounded-lg shadow-md mx-auto">
                    </div>

                    <div class="flex justify-center space-x-4">
                        <button @click="downloadProof(selectedPayment)" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download
                        </button>
                        <button @click="approvePayment(selectedPayment)" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Setujui Pembayaran
                        </button>
                        <button @click="rejectPayment(selectedPayment)" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Tolak Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function paymentsListData() {
    return {
        payments: [],
        isLoading: true,
        searchQuery: '',
        selectedRt: '',
        selectedMethod: '',
        currentPage: 1,
        perPage: 20,
        totalPayments: 0,
        totalPages: 0,
        showConfirmModal: false,
        showProofModal: false,
        selectedPayment: null,
        confirmAction: '',
        confirmationNotes: '',
        isProcessing: false,
        searchTimeout: null,

        async initPaymentsList() {
            await this.loadPayments();
        },

        async loadPayments() {
            this.isLoading = true;
            try {
                const params = new URLSearchParams({
                    page: this.currentPage,
                    limit: this.perPage,
                    search: this.searchQuery,
                    rt_id: this.selectedRt,
                    metode_bayar: this.selectedMethod
                });

                const response = await fetch(`/payments/list?${params}`);
                const data = await response.json();

                if (data.success) {
                    this.payments = data.data.map(payment => ({
                        ...payment,
                        bukti_bayar_url: payment.bukti_bayar_file ? `/storage/${payment.bukti_bayar_file}` : null
                    }));
                    this.totalPayments = data.pagination.total;
                    this.totalPages = data.pagination.last_page;
                }
            } catch (error) {
                console.error('Error loading payments:', error);
                this.showNotification('Gagal memuat data pembayaran', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.currentPage = 1;
                this.loadPayments();
            }, 500);
        },

        async refreshPayments() {
            this.currentPage = 1;
            await this.loadPayments();
            this.showNotification('Data pembayaran berhasil diperbarui', 'success');
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadPayments();
            }
        },

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.loadPayments();
            }
        },

        viewProof(payment) {
            this.selectedPayment = payment;
            this.showProofModal = true;
        },

        closeProofModal() {
            this.showProofModal = false;
            this.selectedPayment = null;
        },

        approvePayment(payment) {
            this.selectedPayment = payment;
            this.confirmAction = 'approve';
            this.confirmationNotes = '';
            this.showConfirmModal = true;
        },

        rejectPayment(payment) {
            this.selectedPayment = payment;
            this.confirmAction = 'reject';
            this.confirmationNotes = '';
            this.showConfirmModal = true;
        },

        closeConfirmModal() {
            this.showConfirmModal = false;
            this.selectedPayment = null;
            this.confirmAction = '';
            this.confirmationNotes = '';
            this.isProcessing = false;
        },

        async processConfirmation() {
            if (!this.selectedPayment) return;

            this.isProcessing = true;
            try {
                const response = await fetch(`/payments/${this.selectedPayment.id}/confirm`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        action: this.confirmAction,
                        confirmation_notes: this.confirmationNotes
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification(data.message, 'success');
                    this.closeConfirmModal();
                    await this.loadPayments();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error processing confirmation:', error);
                this.showNotification('Gagal memproses konfirmasi: ' + error.message, 'error');
            } finally {
                this.isProcessing = false;
            }
        },

        downloadProof(payment) {
            if (payment.bukti_bayar_file) {
                window.open(`/payments/${payment.id}/download-proof`, '_blank');
            }
        },

        formatCurrency(amount) {
            return '  '_blank');
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
