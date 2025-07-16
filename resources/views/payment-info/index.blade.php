@extends('layouts.app')

@section('title', 'Informasi Pembayaran RT')
@section('page-title', 'Informasi Pembayaran RT')
@section('page-description', 'Kelola informasi rekening dan metode pembayaran untuk RT Anda.')

@section('content')
<div x-data="paymentInfoData()" x-init="init()" class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Daftar Informasi Pembayaran</h3>
            <button @click="openModalForCreate()" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 text-white text-base font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 ease-in-out shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 w-full sm:w-auto">
                <i data-lucide="plus" class="w-5 h-5 mr-2"></i> Tambah Baru
            </button>
        </div>

        <div x-show="paymentInfos.length > 0" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-2">
            <template x-for="info in paymentInfos" :key="info.id">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-600 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-bold text-lg text-gray-800 dark:text-white" x-text="'Informasi Pembayaran #' + info.id + ' (RT ' + info.rt_no + ')'"></h4>
                        <div class="flex items-center space-x-2">
                            <span :class="info.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300'" class="px-3 py-1 rounded-full text-xs font-semibold">
                                <span x-text="info.is_active ? 'Aktif' : 'Tidak Aktif'"></span>
                            </span>
                            <button @click="openModalForEdit(info)" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md text-gray-600 dark:text-gray-300 transition-colors duration-150" title="Edit">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button @click="deletePaymentInfo(info.id)" class="p-2 hover:bg-red-100 dark:hover:bg-red-900 rounded-md text-red-600 dark:text-red-400 transition-colors duration-150" title="Hapus">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400 flex-grow">
                        <template x-if="info.has_bank_transfer">
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md border border-gray-100 dark:border-gray-600 shadow-sm">
                                <p class="font-semibold text-gray-700 dark:text-gray-200 mb-1 flex items-center"><i data-lucide="banknote" class="w-4 h-4 mr-2 text-blue-500"></i>Transfer Bank:</p>
                                <p><span class="font-medium" x-text="info.bank_name"></span></p>
                                <p>No. Rek: <span x-text="info.bank_account_number"></span></p>
                                <p>A/N: <span x-text="info.bank_account_name"></span></p>
                            </div>
                        </template>
                        <template x-if="info.has_e_wallet">
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md border border-gray-100 dark:border-gray-600 shadow-sm">
                                <p class="font-semibold text-gray-700 dark:text-gray-200 mb-1 flex items-center"><i data-lucide="wallet" class="w-4 h-4 mr-2 text-purple-500"></i>E-Wallet:</p>
                                <template x-for="(number, wallet) in info.e_wallet_list" :key="wallet">
                                    <p><span class="font-medium" x-text="wallet.toUpperCase()"></span>: <span x-text="number"></span></p>
                                </template>
                            </div>
                        </template>
                        <template x-if="info.has_qr_code">
                            <div class="col-span-full bg-white dark:bg-gray-800 p-3 rounded-md border border-gray-100 dark:border-gray-600 shadow-sm flex flex-col items-center">
                                <p class="font-semibold text-gray-700 dark:text-gray-200 mb-2 flex items-center"><i data-lucide="qr-code" class="w-4 h-4 mr-2 text-orange-500"></i>QR Code:</p>
                                <img :src="info.qr_code_url" alt="QR Code" class="w-24 h-24 object-contain border border-gray-200 dark:border-gray-600 rounded-md p-1 mb-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 text-center" x-text="info.qr_code_description"></p>
                            </div>
                        </template>
                        <template x-if="info.payment_notes">
                            <div class="col-span-full bg-white dark:bg-gray-800 p-3 rounded-md border border-gray-100 dark:border-gray-600 shadow-sm">
                                <p class="font-semibold text-gray-700 dark:text-gray-200 mb-1 flex items-center"><i data-lucide="clipboard-list" class="w-4 h-4 mr-2 text-gray-500"></i>Catatan Umum:</p>
                                <p x-text="info.payment_notes"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
        <div x-show="paymentInfos.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-12">
            <i data-lucide="info" class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-500"></i>
            <p class="text-lg font-medium mb-4">Belum ada informasi pembayaran yang diatur untuk RT Anda.</p>
            <button @click="openModalForCreate()" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 text-white text-base font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 ease-in-out shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                <i data-lucide="plus" class="w-5 h-5 mr-2"></i> Tambah Informasi Pembayaran
            </button>
        </div>
    </div>

    <!-- Payment Info Modal -->
    <div x-show="isModalOpen" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4 sm:p-6" x-cloak>
        <div @click.away="closeModal()" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-3xl max-h-[95vh] overflow-y-auto transform transition-all duration-300 ease-out scale-95 sm:scale-100 opacity-0"
             :class="{'scale-100 opacity-100': isModalOpen, 'scale-95 opacity-0': !isModalOpen}">
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white" x-text="isEditMode ? 'Edit Informasi Pembayaran' : 'Tambah Informasi Pembayaran Baru'"></h3>
                <button @click="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <form @submit.prevent="submitForm()" class="p-6 sm:p-8 space-y-6">
                {{-- RT Selection for Admin/Kades, or Hidden for RT --}}
                @php
                    $user = Auth::user();
                    $isRtRole = $user->hasRole('rt');
                    $isAdminOrKades = $user->hasRole('admin') || $user->hasRole('kades');
                @endphp

                @if($isAdminOrKades)
                <div>
                    <label for="rt_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih RT <span class="text-red-500">*</span></label>
                    <select x-model="form.rt_id" id="rt_id" required
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white">
                        <option value="">Pilih RT</option>
                        <template x-for="rt in rts" :key="rt.id">
                            <option :value="rt.id" x-text="'RT ' + rt.no_rt + ' / RW ' + rt.no_rw"></option>
                        </template>
                    </select>
                    <p x-show="errors.rt_id" x-text="errors.rt_id" class="text-red-500 text-xs mt-1"></p>
                </div>
                @elseif($isRtRole)
                {{-- Hidden input for RT role, value set in Alpine.js init/openModalForCreate --}}
                <input type="hidden" x-model="form.rt_id" id="rt_id">
                @endif

                <!-- Bank Transfer Section -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center"><i data-lucide="banknote" class="w-5 h-5 mr-2 text-blue-500"></i>Transfer Bank</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Bank</label>
                            <input type="text" x-model="form.bank_name" id="bank_name" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                            <p x-show="errors.bank_name" x-text="errors.bank_name" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <div>
                            <label for="bank_account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor Rekening</label>
                            <input type="text" x-model="form.bank_account_number" id="bank_account_number" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                            <p x-show="errors.bank_account_number" x-text="errors.bank_account_number" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <div class="md:col-span-2">
                            <label for="bank_account_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Pemilik Rekening</label>
                            <input type="text" x-model="form.bank_account_name" id="bank_account_name" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                            <p x-show="errors.bank_account_name" x-text="errors.bank_account_name" class="text-red-500 text-xs mt-1"></p>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <!-- E-Wallet Section -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center"><i data-lucide="wallet" class="w-5 h-5 mr-2 text-purple-500"></i>E-Wallet</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="dana_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor DANA</label>
                            <input type="text" x-model="form.dana_number" id="dana_number" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                            <p x-show="errors.dana_number" x-text="errors.dana_number" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <div>
                            <label for="gopay_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor GoPay</label>
                            <input type="text" x-model="form.gopay_number" id="gopay_number" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                            <p x-show="errors.gopay_number" x-text="errors.gopay_number" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <div>
                            <label for="ovo_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor OVO</label>
                            <input type="text" x-model="form.ovo_number" id="ovo_number" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                            <p x-show="errors.ovo_number" x-text="errors.ovo_number" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <div>
                            <label for="shopeepay_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor ShopeePay</label>
                            <input type="text" x-model="form.shopeepay_number" id="shopeepay_number" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                            <p x-show="errors.shopeepay_number" x-text="errors.shopeepay_number" class="text-red-500 text-xs mt-1"></p>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <!-- QR Code Section -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center"><i data-lucide="qr-code" class="w-5 h-5 mr-2 text-orange-500"></i>QR Code Pembayaran</h4>
                    <div class="space-y-4">
                        <div>
                            <label for="qr_code_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unggah QR Code (Gambar)</label>
                            <input type="file" @change="handleQrCodeUpload($event)" id="qr_code_file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300 dark:hover:file:bg-blue-800 cursor-pointer">
                            <p x-show="errors.qr_code_file" x-text="errors.qr_code_file" class="text-red-500 text-xs mt-1"></p>
                        </div>
                        <div x-show="form.qr_code_url || qrCodePreview" class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4 p-3 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800">
                            <template x-if="qrCodePreview">
                                <img :src="qrCodePreview" alt="QR Code Preview" class="w-28 h-28 object-contain border border-gray-200 dark:border-gray-600 rounded-md p-1 shadow-sm">
                            </template>
                            <template x-if="!qrCodePreview && form.qr_code_url">
                                <img :src="form.qr_code_url" alt="Current QR Code" class="w-28 h-28 object-contain border border-gray-200 dark:border-gray-600 rounded-md p-1 shadow-sm">
                            </template>
                            <button type="button" @click="removeQrCode()" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 text-sm font-medium transition-colors duration-150 flex items-center">
                                <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Hapus QR Code
                            </button>
                        </div>
                        <div>
                            <label for="qr_code_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi QR Code (Opsional)</label>
                            <input type="text" x-model="form.qr_code_description" id="qr_code_description" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                            <p x-show="errors.qr_code_description" x-text="errors.qr_code_description" class="text-red-500 text-xs mt-1"></p>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <!-- General Notes and Active Status -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center"><i data-lucide="clipboard-list" class="w-5 h-5 mr-2 text-gray-500"></i>Pengaturan Lainnya</h4>
                    <div>
                        <label for="payment_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Pembayaran (Opsional)</label>
                        <textarea x-model="form.payment_notes" id="payment_notes" rows="3" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"></textarea>
                        <p x-show="errors.payment_notes" x-text="errors.payment_notes" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div class="flex items-center mt-4">
                        <input type="checkbox" x-model="form.is_active" id="is_active" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:checked:bg-blue-600 cursor-pointer">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Aktifkan informasi pembayaran ini (akan menonaktifkan yang lain)</label>
                    </div>
                    <p x-show="errors.is_active" x-text="errors.is_active" class="text-red-500 text-xs mt-1"></p>
                </div>

                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 mt-6">
                    <button type="button" @click="closeModal()" class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800">
                        Batal
                    </button>
                    <button type="submit" :disabled="isLoading" class="w-full sm:w-auto px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 ease-in-out">
                        <span x-show="!isLoading" x-text="isEditMode ? 'Simpan Perubahan' : 'Tambah Informasi'"></span>
                        <span x-show="isLoading" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
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
            rts: [], // New property to store RTs for selection
            isModalOpen: false,
            isEditMode: false,
            currentPaymentInfoId: null,
            form: {
                rt_id: '', // Added rt_id to the form data
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
                        this.paymentInfos = data.data ? (Array.isArray(data.data) ? data.data : [data.data]) : [];
                        this.rts = data.rts_for_selection || []; // Populate rts for selection
                        console.log('Payment infos fetched:', this.paymentInfos);
                        console.log('RTs for selection fetched:', this.rts);
                    } else {
                        console.error('Failed to fetch payment infos:', data.message);
                        this.paymentInfos = [];
                        this.rts = [];
                    }
                } catch (error) {
                    console.error('Error fetching payment infos:', error);
                    this.paymentInfos = [];
                    this.rts = [];
                }
            },

            resetForm() {
                this.form = {
                    rt_id: '',
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
                const qrCodeFileInput = document.getElementById('qr_code_file');
                if (qrCodeFileInput) {
                    qrCodeFileInput.value = ''; // Clear file input
                }
            },

            openModalForCreate() {
                this.resetForm();
                this.isEditMode = false;
                // Automatically set rt_id for 'rt' role
                @if(Auth::user()->hasRole('rt'))
                    this.form.rt_id = "{{ Auth::user()->penduduk->rtKetua->id ?? '' }}";
                @endif
                this.isModalOpen = true;
            },

            openModalForEdit(info) {
                this.resetForm();
                this.isEditMode = true;
                this.currentPaymentInfoId = info.id;
                this.form = {
                    ...info,
                    rt_id: info.rt_id, // Ensure rt_id is set for edit mode
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
                const qrCodeFileInput = document.getElementById('qr_code_file');
                if (qrCodeFileInput) {
                    qrCodeFileInput.value = ''; // Clear file input
                }
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
