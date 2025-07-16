@extends('layouts.app')

@section('title', 'Informasi Pembayaran RT')
@section('page-title', 'Informasi Pembayaran RT')
@section('page-description', 'Kelola informasi rekening dan metode pembayaran untuk RT Anda.')

@section('content')
<div x-data="paymentInfoData()" x-init="init()" class="p-6 space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Daftar Informasi Pembayaran</h3>
            <button @click="openModalForCreate()" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Baru
            </button>
        </div>

        <div x-show="paymentInfos.length > 0" class="space-y-4">
            <template x-for="info in paymentInfos" :key="info.id">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-sm border border-gray-100 dark:border-gray-600">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-800 dark:text-white" x-text="'Informasi Pembayaran #' + info.id"></h4>
                        <div class="flex items-center space-x-2">
                            <span :class="info.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300'" class="px-2.5 py-0.5 rounded-full text-xs font-medium">
                                <span x-text="info.is_active ? 'Aktif' : 'Tidak Aktif'"></span>
                            </span>
                            <button @click="openModalForEdit(info)" class="p-1.5 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md text-gray-600 dark:text-gray-300">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button @click="deletePaymentInfo(info.id)" class="p-1.5 hover:bg-red-100 dark:hover:bg-red-900 rounded-md text-red-600 dark:text-red-400">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-400">
                        <template x-if="info.has_bank_transfer">
                            <div>
                                <p class="font-medium text-gray-700 dark:text-gray-200">Transfer Bank:</p>
                                <p><span x-text="info.bank_name"></span> - <span x-text="info.bank_account_number"></span></p>
                                <p>A/N: <span x-text="info.bank_account_name"></span></p>
                            </div>
                        </template>
                        <template x-if="info.has_e_wallet">
                            <div>
                                <p class="font-medium text-gray-700 dark:text-gray-200">E-Wallet:</p>
                                <template x-for="(number, wallet) in info.e_wallet_list" :key="wallet">
                                    <p><span x-text="wallet.toUpperCase()"></span>: <span x-text="number"></span></p>
                                </template>
                            </div>
                        </template>
                        <template x-if="info.has_qr_code">
                            <div class="col-span-full flex flex-col items-center mt-2">
                                <p class="font-medium text-gray-700 dark:text-gray-200 mb-2">QR Code:</p>
                                <img :src="info.qr_code_url" alt="QR Code" class="w-24 h-24 object-contain border border-gray-200 dark:border-gray-600 rounded-md p-1">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="info.qr_code_description"></p>
                            </div>
                        </template>
                        <template x-if="info.payment_notes">
                            <div class="col-span-full">
                                <p class="font-medium text-gray-700 dark:text-gray-200">Catatan:</p>
                                <p x-text="info.payment_notes"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
        <div x-show="paymentInfos.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-8">
            <p>Belum ada informasi pembayaran yang diatur untuk RT Anda.</p>
            <button @click="openModalForCreate()" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Informasi Pembayaran
            </button>
        </div>
    </div>

    <!-- Payment Info Modal -->
    <div x-show="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" x-cloak>
        <div @click.away="closeModal()" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all sm:scale-100 sm:w-full">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white" x-text="isEditMode ? 'Edit Informasi Pembayaran' : 'Tambah Informasi Pembayaran Baru'"></h3>
                <button @click="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form @submit.prevent="submitForm()" class="p-6 space-y-6">
                <!-- Bank Transfer Section -->
                <div>
                    <h4 class="text-md font-medium text-gray-800 dark:text-white mb-3">Transfer Bank</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Bank</label>
                            <input type="text" x-model="form.bank_name" id="bank_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p x-show="errors.bank_name" x-text="errors.bank_name" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <div>
                            <label for="bank_account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Rekening</label>
                            <input type="text" x-model="form.bank_account_number" id="bank_account_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p x-show="errors.bank_account_number" x-text="errors.bank_account_number" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <div class="md:col-span-2">
                            <label for="bank_account_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pemilik Rekening</label>
                            <input type="text" x-model="form.bank_account_name" id="bank_account_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p x-show="errors.bank_account_name" x-text="errors.bank_account_name" class="text-red-500 text-xs mt-1"></p>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <!-- E-Wallet Section -->
                <div>
                    <h4 class="text-md font-medium text-gray-800 dark:text-white mb-3">E-Wallet</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="dana_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor DANA</label>
                            <input type="text" x-model="form.dana_number" id="dana_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p x-show="errors.dana_number" x-text="errors.dana_number" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <div>
                            <label for="gopay_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor GoPay</label>
                            <input type="text" x-model="form.gopay_number" id="gopay_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p x-show="errors.gopay_number" x-text="errors.gopay_number" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <div>
                            <label for="ovo_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor OVO</label>
                            <input type="text" x-model="form.ovo_number" id="ovo_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p x-show="errors.ovo_number" x-text="errors.ovo_number" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <div>
                            <label for="shopeepay_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor ShopeePay</label>
                            <input type="text" x-model="form.shopeepay_number" id="shopeepay_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p x-show="errors.shopeepay_number" x-text="errors.shopeepay_number" class="text-red-500 text-xs mt-1"></p>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <!-- QR Code Section -->
                <div>
                    <h4 class="text-md font-medium text-gray-800 dark:text-white mb-3">QR Code Pembayaran</h4>
                    <div class="space-y-4">
                        <div>
                            <label for="qr_code_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unggah QR Code (Gambar)</label>
                            <input type="file" @change="handleQrCodeUpload($event)" id="qr_code_file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300 dark:hover:file:bg-blue-800">
                            <p x-show="errors.qr_code_file" x-text="errors.qr_code_file" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <div x-show="form.qr_code_url || qrCodePreview" class="flex items-center space-x-4">
                            <template x-if="qrCodePreview">
                                <img :src="qrCodePreview" alt="QR Code Preview" class="w-24 h-24 object-contain border border-gray-200 dark:border-gray-600 rounded-md p-1">
                            </template>
                            <template x-if="!qrCodePreview && form.qr_code_url">
                                <img :src="form.qr_code_url" alt="Current QR Code" class="w-24 h-24 object-contain border border-gray-200 dark:border-gray-600 rounded-md p-1">
                            </template>
                            <button type="button" @click="removeQrCode()" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-200 text-sm">
                                Hapus QR Code
                            </button>
                        </div>
                        <div>
                            <label for="qr_code_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi QR Code (Opsional)</label>
                            <input type="text" x-model="form.qr_code_description" id="qr_code_description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p x-show="errors.qr_code_description" x-text="errors.qr_code_description" class="text-red-500 text-xs mt-1"></p>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <!-- General Notes and Active Status -->
                <div>
                    <label for="payment_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan Pembayaran (Opsional)</label>
                    <textarea x-model="form.payment_notes" id="payment_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    <p x-show="errors.payment_notes" x-text="errors.payment_notes" class="text-red-500 text-xs mt-1"></p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" x-model="form.is_active" id="is_active" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:checked:bg-blue-600">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Aktifkan informasi pembayaran ini (akan menonaktifkan yang lain)</label>
                </div>
                <p x-show="errors.is_active" x-text="errors.is_active" class="text-red-500 text-xs mt-1"></p>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                        Batal
                    </button>
                    <button type="submit" :disabled="isLoading" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-800">
                        <span x-show="!isLoading" x-text="isEditMode ? 'Simpan Perubahan' : 'Tambah Informasi'"></span>
                        <span x-show="isLoading">Memproses...</span>
                    </button>
                </div>
            </form>
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

    function paymentInfoData() {
        return {
            paymentInfos: [],
            isModalOpen: false,
            isEditMode: false,
            currentPaymentInfoId: null,
            form: {
                bank_name: '',
                bank_account_number: '',
                bank_account_name: '',
                dana_number: '',
                gopay_number: '',
                ovo_number: '',
                shopeepay_number: '',
                qr_code_file: null,
                qr_code_description: '',
                payment_notes: '',
                is_active: true,
                qr_code_url: null, // To display existing QR code
                clear_qr_code: false, // New field for explicit QR code deletion
            },
            qrCodePreview: null,
            errors: {},
            isLoading: false,

            async init() {
                console.log('Initializing Payment Info Page...');
                await this.fetchPaymentInfos();
                setTimeout(() => {
                    lucide.createIcons();
                }, 100);
            },

            async fetchPaymentInfos() {
                try {
                    const response = await fetch('/api/payment-info', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        // If data is a single object, wrap it in an array
                        this.paymentInfos = data.data ? (Array.isArray(data.data) ? data.data : [data.data]) : [];
                        console.log('Payment infos fetched:', this.paymentInfos);
                    } else {
                        console.error('Failed to fetch payment infos:', data.message);
                        this.paymentInfos = [];
                    }
                } catch (error) {
                    console.error('Error fetching payment infos:', error);
                    this.paymentInfos = [];
                }
            },

            resetForm() {
                this.form = {
                    bank_name: '',
                    bank_account_number: '',
                    bank_account_name: '',
                    dana_number: '',
                    gopay_number: '',
                    ovo_number: '',
                    shopeepay_number: '',
                    qr_code_file: null,
                    qr_code_description: '',
                    payment_notes: '',
                    is_active: true,
                    qr_code_url: null,
                    clear_qr_code: false,
                };
                this.qrCodePreview = null;
                this.errors = {};
                document.getElementById('qr_code_file').value = ''; // Clear file input
            },

            openModalForCreate() {
                this.resetForm();
                this.isEditMode = false;
                this.isModalOpen = true;
            },

            openModalForEdit(info) {
                this.resetForm();
                this.isEditMode = true;
                this.currentPaymentInfoId = info.id;
                this.form = {
                    ...info,
                    qr_code_file: null, // File input should be reset for edit
                    qr_code_url: info.qr_code_url, // Keep existing URL for display
                    clear_qr_code: false,
                };
                this.isModalOpen = true;
            },

            closeModal() {
                this.isModalOpen = false;
                this.resetForm();
            },

            handleQrCodeUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.form.qr_code_file = file;
                    this.qrCodePreview = URL.createObjectURL(file);
                    this.form.clear_qr_code = false; // If new file is uploaded, don't clear
                } else {
                    this.form.qr_code_file = null;
                    this.qrCodePreview = null;
                }
            },

            removeQrCode() {
                this.form.qr_code_file = null;
                this.form.qr_code_url = null; // Clear existing URL
                this.qrCodePreview = null;
                this.form.clear_qr_code = true; // Mark for deletion on server
                document.getElementById('qr_code_file').value = ''; // Clear file input
            },

            async submitForm() {
                this.isLoading = true;
                this.errors = {}; // Clear previous errors

                const formData = new FormData();
                for (const key in this.form) {
                    if (key === 'qr_code_file' && this.form[key]) {
                        formData.append(key, this.form[key]);
                    } else if (key === 'e_wallet_list' || key === 'qr_code_url') {
                        // Skip these as they are accessors or for display
                        continue;
                    } else if (key === 'is_active' || key === 'clear_qr_code') {
                        formData.append(key, this.form[key] ? 1 : 0); // Convert boolean to 0/1
                    } else if (this.form[key] !== null && this.form[key] !== '') {
                        formData.append(key, this.form[key]);
                    }
                }

                let url = '/api/payment-info';
                let method = 'POST';

                if (this.isEditMode) {
                    url = `/api/payment-info/${this.currentPaymentInfoId}`;
                    method = 'POST'; // Laravel uses POST for PUT/PATCH with _method field
                    formData.append('_method', 'PUT');
                }

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            // 'Content-Type': 'multipart/form-data' is automatically set by FormData
                        },
                        body: formData,
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert(data.message);
                        this.closeModal();
                        await this.fetchPaymentInfos(); // Refresh list
                    } else {
                        if (data.errors) {
                            this.errors = data.errors;
                        }
                        alert(data.message || 'Terjadi kesalahan saat menyimpan data.');
                    }
                } catch (error) {
                    console.error('Error submitting form:', error);
                    alert('Terjadi kesalahan jaringan atau server.');
                } finally {
                    this.isLoading = false;
                }
            },

            async deletePaymentInfo(id) {
                if (!confirm('Apakah Anda yakin ingin menghapus informasi pembayaran ini?')) {
                    return;
                }

                this.isLoading = true;
                try {
                    const response = await fetch(`/api/payment-info/${id}`, {
                        method: 'POST', // Laravel uses POST for DELETE with _method field
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new URLSearchParams({
                            _method: 'DELETE'
                        }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert(data.message);
                        await this.fetchPaymentInfos(); // Refresh list
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat menghapus data.');
                    }
                } catch (error) {
                    console.error('Error deleting payment info:', error);
                    alert('Terjadi kesalahan jaringan atau server.');
                } finally {
                    this.isLoading = false;
                }
            },
        };
    }
</script>
@endpush
@endsection
