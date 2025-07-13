@extends('layouts.app')

@section('title', 'Kelola Info Pembayaran')
@section('page-title', 'Kelola Info Pembayaran')
@section('page-description', 'Atur informasi pembayaran kas untuk RT Anda.')

@section('content')
<div x-data="paymentInfoData()" x-init="initPaymentInfo()" class="p-6 space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Informasi Pembayaran RT</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Atur metode pembayaran yang tersedia untuk warga RT Anda</p>
            </div>
            
            <button @click="showCreateModal = true" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Info Pembayaran
            </button>
        </div>
    </div>

    <!-- Payment Info List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <!-- Loading State -->
        <div x-show="isLoading" class="p-8 text-center">
            <svg class="w-8 h-8 animate-spin mx-auto mb-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <p class="text-gray-600 dark:text-gray-400">Memuat informasi pembayaran...</p>
        </div>

        <!-- Empty State -->
        <div x-show="!isLoading && paymentInfos.length === 0" class="p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Belum Ada Info Pembayaran</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Tambahkan informasi pembayaran untuk memudahkan warga melakukan pembayaran kas.</p>
            <button @click="showCreateModal = true" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Tambah Sekarang
            </button>
        </div>

        <!-- Payment Info Cards -->
        <div x-show="!isLoading && paymentInfos.length > 0" class="p-6 space-y-4">
            <template x-for="info in paymentInfos" :key="info.id">
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-4">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-white" x-text="`RT ${info.rt.no_rt} / RW ${info.rt.rw.no_rw}`"></h4>
                                <span :class="info.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                      class="ml-3 px-2 py-1 rounded-full text-xs font-medium" 
                                      x-text="info.is_active ? 'Aktif' : 'Nonaktif'"></span>
                            </div>

                            <!-- Bank Transfer Info -->
                            <div x-show="info.bank_transfer && info.bank_transfer.account_number" class="mb-4">
                                <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-2">🏦 Transfer Bank</h5>
                                <div class="bg-gray-50 dark:bg-gray-600 p-3 rounded-lg text-sm">
                                    <p><span class="font-medium">Bank:</span> <span x-text="info.bank_transfer.bank_name || '-'"></span></p>
                                    <p><span class="font-medium">No. Rekening:</span> <span x-text="info.bank_transfer.account_number"></span></p>
                                    <p><span class="font-medium">Atas Nama:</span> <span x-text="info.bank_transfer.account_name || '-'"></span></p>
                                </div>
                            </div>

                            <!-- E-Wallet Info -->
                            <div x-show="info.e_wallet && (info.e_wallet.dana || info.e_wallet.ovo || info.e_wallet.gopay)" class="mb-4">
                                <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-2">💳 E-Wallet</h5>
                                <div class="bg-gray-50 dark:bg-gray-600 p-3 rounded-lg text-sm space-y-1">
                                    <p x-show="info.e_wallet.dana"><span class="font-medium">DANA:</span> <span x-text="info.e_wallet.dana"></span></p>
                                    <p x-show="info.e_wallet.ovo"><span class="font-medium">OVO:</span> <span x-text="info.e_wallet.ovo"></span></p>
                                    <p x-show="info.e_wallet.gopay"><span class="font-medium">GOPAY:</span> <span x-text="info.e_wallet.gopay"></span></p>
                                </div>
                            </div>

                            <!-- QR Code Info -->
                            <div x-show="info.qr_code && info.qr_code.image_url" class="mb-4">
                                <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-2">📸 QR Code</h5>
                                <div class="bg-gray-50 dark:bg-gray-600 p-3 rounded-lg">
                                    <img :src="info.qr_code.image_url" alt="QR Code" class="w-32 h-32 mx-auto border rounded-lg">
                                    <p x-show="info.qr_code.description" class="text-xs text-center mt-2 text-gray-600 dark:text-gray-400" x-text="info.qr_code.description"></p>
                                </div>
                            </div>

                            <!-- Payment Notes -->
                            <div x-show="info.payment_notes" class="mb-4">
                                <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-2">📝 Catatan</h5>
                                <div class="bg-gray-50 dark:bg-gray-600 p-3 rounded-lg text-sm">
                                    <p x-text="info.payment_notes"></p>
                                </div>
                            </div>
                        </div>

                        <div class="ml-4 flex flex-col space-y-2">
                            <button @click="editPaymentInfo(info)" 
                                    class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                Edit
                            </button>
                            <button @click="toggleStatus(info)" 
                                    :class="info.is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'" 
                                    class="px-3 py-1 text-sm text-white rounded transition-colors" 
                                    x-text="info.is_active ? 'Nonaktifkan' : 'Aktifkan'">
                            </button>
                            <button @click="deletePaymentInfo(info)" 
                                    class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showCreateModal || showEditModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white" x-text="showEditModal ? 'Edit Info Pembayaran' : 'Tambah Info Pembayaran'"></h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form @submit.prevent="savePaymentInfo()">
                    <!-- RT Selection (only for create) -->
                    <div x-show="showCreateModal" class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">RT <span class="text-red-500">*</span></label>
                        <select x-model="formData.rt_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">Pilih RT</option>
                            @if(auth()->user()->role === 'rt' && auth()->user()->penduduk && auth()->user()->penduduk->rtKetua)
                                <option value="{{ auth()->user()->penduduk->rtKetua->id }}">RT {{ auth()->user()->penduduk->rtKetua->no_rt }}</option>
                            @else
                                <!-- For RW/Admin/Kades - show all RTs they can manage -->
                                @foreach($availableRts ?? [] as $rt)
                                    <option value="{{ $rt->id }}">RT {{ $rt->no_rt }} / RW {{ $rt->rw->no_rw }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Bank Transfer Section -->
                    <div class="mb-6">
                        <div class="flex items-center mb-3">
                            <input type="checkbox" x-model="formData.enableBankTransfer" id="enableBankTransfer" class="mr-2">
                            <label for="enableBankTransfer" class="text-sm font-medium text-gray-700 dark:text-gray-300">🏦 Transfer Bank</label>
                        </div>
                        <div x-show="formData.enableBankTransfer" class="space-y-3 pl-6 border-l-2 border-blue-200">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Bank</label>
                                <input type="text" x-model="formData.bankName" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Contoh: BCA, Mandiri, BRI">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor Rekening <span class="text-red-500">*</span></label>
                                <input type="text" x-model="formData.accountNumber" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="1234567890">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Atas Nama</label>
                                <input type="text" x-model="formData.accountName" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Nama pemilik rekening">
                            </div>
                        </div>
                    </div>

                    <!-- E-Wallet Section -->
                    <div class="mb-6">
                        <div class="flex items-center mb-3">
                            <input type="checkbox" x-model="formData.enableEWallet" id="enableEWallet" class="mr-2">
                            <label for="enableEWallet" class="text-sm font-medium text-gray-700 dark:text-gray-300">💳 E-Wallet</label>
                        </div>
                        <div x-show="formData.enableEWallet" class="space-y-3 pl-6 border-l-2 border-green-200">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">DANA</label>
                                <input type="text" x-model="formData.dana" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="08123456789">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OVO</label>
                                <input type="text" x-model="formData.ovo" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="08123456789">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">GOPAY</label>
                                <input type="text" x-model="formData.gopay" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="08123456789">
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Section -->
                    <div class="mb-6">
                        <div class="flex items-center mb-3">
                            <input type="checkbox" x-model="formData.enableQRCode" id="enableQRCode" class="mr-2">
                            <label for="enableQRCode" class="text-sm font-medium text-gray-700 dark:text-gray-300">📸 QR Code</label>
                        </div>
                        <div x-show="formData.enableQRCode" class="space-y-3 pl-6 border-l-2 border-purple-200">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload QR Code</label>
                                <input type="file" x-ref="qrCodeFile" accept="image/*" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi QR Code</label>
                                <input type="text" x-model="formData.qrDescription" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Contoh: Scan untuk pembayaran DANA">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan Pembayaran</label>
                        <textarea x-model="formData.paymentNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Tambahkan catatan atau instruksi khusus untuk pembayaran..."></textarea>
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" x-model="formData.isActive" id="isActive" class="mr-2">
                            <label for="isActive" class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktifkan info pembayaran ini</label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="isProcessing" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                            <span x-show="!isProcessing" x-text="showEditModal ? 'Update' : 'Simpan'"></span>
                            <span x-show="isProcessing">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function paymentInfoData() {
    return {
        paymentInfos: [],
        isLoading: true,
        showCreateModal: false,
        showEditModal: false,
        isProcessing: false,
        editingId: null,
        formData: {
            rt_id: '',
            enableBankTransfer: false,
            bankName: '',
            accountNumber: '',
            accountName: '',
            enableEWallet: false,
            dana: '',
            ovo: '',
            gopay: '',
            enableQRCode: false,
            qrDescription: '',
            paymentNotes: '',
            isActive: true
        },

        async initPaymentInfo() {
            await this.loadPaymentInfos();
        },

        async loadPaymentInfos() {
            this.isLoading = true;
            try {
                const response = await fetch('/payment-info');
                if (response.ok) {
                    const html = await response.text();
                    // This would need to be adapted to return JSON data
                    // For now, we'll simulate the data
                    this.paymentInfos = [];
                }
            } catch (error) {
                console.error('Error loading payment infos:', error);
                this.showNotification('Gagal memuat informasi pembayaran', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        resetForm() {
            this.formData = {
                rt_id: '',
                enableBankTransfer: false,
                bankName: '',
                accountNumber: '',
                accountName: '',
                enableEWallet: false,
                dana: '',
                ovo: '',
                gopay: '',
                enableQRCode: false,
                qrDescription: '',
                paymentNotes: '',
                isActive: true
            };
        },

        editPaymentInfo(info) {
            this.editingId = info.id;
            this.formData = {
                rt_id: info.rt_id,
                enableBankTransfer: !!(info.bank_transfer && info.bank_transfer.account_number),
                bankName: info.bank_transfer?.bank_name || '',
                accountNumber: info.bank_transfer?.account_number || '',
                accountName: info.bank_transfer?.account_name || '',
                enableEWallet: !!(info.e_wallet && (info.e_wallet.dana || info.e_wallet.ovo || info.e_wallet.gopay)),
                dana: info.e_wallet?.dana || '',
                ovo: info.e_wallet?.ovo || '',
                gopay: info.e_wallet?.gopay || '',
                enableQRCode: !!(info.qr_code && info.qr_code.image_url),
                qrDescription: info.qr_code?.description || '',
                paymentNotes: info.payment_notes || '',
                isActive: info.is_active
            };
            this.showEditModal = true;
        },

        closeModal() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.editingId = null;
            this.isProcessing = false;
            this.resetForm();
        },

        async savePaymentInfo() {
            this.isProcessing = true;
            try {
                const formData = new FormData();
                
                // Basic data
                if (this.showCreateModal) {
                    formData.append('rt_id', this.formData.rt_id);
                }
                formData.append('is_active', this.formData.isActive ? '1' : '0');
                formData.append('payment_notes', this.formData.paymentNotes);

                // Bank transfer data
                if (this.formData.enableBankTransfer) {
                    formData.append('bank_transfer[bank_name]', this.formData.bankName);
                    formData.append('bank_transfer[account_number]', this.formData.accountNumber);
                    formData.append('bank_transfer[account_name]', this.formData.accountName);
                }

                // E-wallet data
                if (this.formData.enableEWallet) {
                    if (this.formData.dana) formData.append('e_wallet[dana]', this.formData.dana);
                    if (this.formData.ovo) formData.append('e_wallet[ovo]', this.formData.ovo);
                    if (this.formData.gopay) formData.append('e_wallet[gopay]', this.formData.gopay);
                }

                // QR Code data
                if (this.formData.enableQRCode) {
                    const qrFile = this.$refs.qrCodeFile.files[0];
                    if (qrFile) {
                        formData.append('qr_code_image', qrFile);
                    }
                    formData.append('qr_code[description]', this.formData.qrDescription);
                }

                const url = this.showEditModal ? `/payment-info/${this.editingId}` : '/payment-info';
                const method = this.showEditModal ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification(data.message, 'success');
                    this.closeModal();
                    await this.loadPaymentInfos();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error saving payment info:', error);
                this.showNotification('Gagal menyimpan informasi pembayaran: ' + error.message, 'error');
            } finally {
                this.isProcessing = false;
            }
        },

        async toggleStatus(info) {
            try {
                const response = await fetch(`/payment-info/${info.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        is_active: !info.is_active
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification(data.message, 'success');
                    await this.loadPaymentInfos();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error toggling status:', error);
                this.showNotification('Gagal mengubah status: ' + error.message, 'error');
            }
        },

        async deletePaymentInfo(info) {
            if (!confirm('Apakah Anda yakin ingin menghapus informasi pembayaran ini?')) {
                return;
            }

            try {
                const response = await fetch(`/payment-info/${info.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification(data.message, 'success');
                    await this.loadPaymentInfos();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error deleting payment info:', error);
                this.showNotification('Gagal menghapus informasi pembayaran: ' + error.message, 'error');
            }
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
