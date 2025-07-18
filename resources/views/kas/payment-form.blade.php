@extends('layouts.app')

@section('title', 'Form Pembayaran Kas')

@push('styles')
<style>
    /* Custom styles for payment form */
    .payment-method-card {
        transition: all 0.2s ease;
        border: 2px solid transparent;
        cursor: pointer;
        display: flex;
        align-items: center;
        padding: 1rem;
        border-radius: 0.75rem; /* rounded-xl */
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06); /* shadow-sm */
        background-color: #f9fafb; /* gray-50 */
    }
    .dark .payment-method-card {
        background-color: #374151; /* gray-700 */
    }
    .payment-method-card.selected {
        border-color: #4f46e5; /* Indigo-600 */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background-color: #eef2ff; /* indigo-50 */
    }
    .dark .payment-method-card.selected {
        border-color: #818cf8; /* Indigo-400 */
        background-color: #1e293b; /* slate-800 */
    }
    .payment-method-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background-color: #f3f4f6; /* gray-100 */
    }
    .dark .payment-method-card:hover {
        background-color: #4a5568; /* darker gray for hover in dark mode */
    }
    .file-upload-preview {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin-top: 1rem;
        border: 1px solid #e5e7eb; /* Gray-200 */
        display: none; /* Hidden by default */
    }
    .dark .file-upload-preview {
        border-color: #4b5563; /* Gray-600 */
    }
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px; /* full rounded */
        font-size: 0.75rem; /* text-xs */
        font-weight: 600; /* font-semibold */
        display: inline-block;
    }
    .status-belum_bayar {
        background-color: #fef3c7; /* yellow-100 */
        color: #b45309; /* yellow-800 */
    }
    .dark .status-belum_bayar {
        background-color: #422006; /* yellow-900 */
        color: #fcd34d; /* yellow-300 */
    }
    .status-lunas {
        background-color: #d1fae5; /* green-100 */
        color: #065f46; /* green-800 */
    }
    .dark .status-lunas {
        background-color: #064e3b; /* green-900 */
        color: #a7f3d0; /* green-300 */
    }
    .status-menunggu_konfirmasi {
        background-color: #bfdbfe; /* blue-200 */
        color: #1e40af; /* blue-800 */
    }
    .dark .status-menunggu_konfirmasi {
        background-color: #1e3a8a; /* blue-900 */
        color: #93c5fd; /* blue-300 */
    }
    .status-terlambat {
        background-color: #fee2e2; /* red-100 */
        color: #991b1b; /* red-800 */
    }
    .dark .status-terlambat {
        background-color: #7f1d1d; /* red-900 */
        color: #fca5a5; /* red-300 */
    }
    .status-ditolak {
        background-color: #fecaca; /* red-200 */
        color: #b91c1c; /* red-800 */
    }
    .dark .status-ditolak {
        background-color: #991b1b; /* red-900 */
        color: #f87171; /* red-400 */
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 animate-fade-in" x-data="{ selectedMethod: '{{ old('metode_bayar', '') }}', filePreview: null, filePreviewType: null }" x-init="
    if (typeof lucide !== 'undefined') { lucide.createIcons(); }
    // Set initial selected method if old input exists or default to first available
    if (selectedMethod === '') {
        const firstMethod = document.querySelector('input[name=\'metode_bayar\']');
        if (firstMethod) {
            selectedMethod = firstMethod.value;
        }
    }
">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-lucide="credit-card" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Form Pembayaran Kas</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Lengkapi detail pembayaran untuk kas minggu ini.</p>
                        </div>
                    </div>
                    <a href="{{ route('kas.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-xl transition-all duration-200 shadow-md dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment Details Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Detail Kas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 dark:text-gray-300">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Penduduk:</p>
                    <p class="text-base font-semibold">{{ $kas->penduduk->nama_lengkap ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">RT/RW:</p>
                    <p class="text-base font-semibold">RT {{ $kas->rt->no_rt ?? 'N/A' }} / RW {{ $kas->rt->rw->no_rw ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Minggu/Tahun:</p>
                    <p class="text-base font-semibold">Minggu ke-{{ $kas->minggu_ke }} / {{ $kas->tahun }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Tagihan:</p>
                    <p class="text-base font-semibold text-blue-600 dark:text-blue-400">{{ $kas->formatted_amount }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jatuh Tempo:</p>
                    <p class="text-base font-semibold">{{ $kas->tanggal_jatuh_tempo_formatted }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</p>
                    <p class="text-base font-semibold">
                        <span class="status-badge status-{{ $kas->status }}">
                            {{ $kas->status_text }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Form Pembayaran</h2>
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 dark:bg-red-900/20 dark:text-red-300">
                    <strong class="font-bold">Oops!</strong>
                    <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>
                    <ul class="mt-3 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('kas.payment.process', $kas->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label for="metode_bayar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Metode Pembayaran <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Tunai -->
                        <label for="metode_tunai" class="payment-method-card" :class="{ 'selected': selectedMethod === 'tunai' }">
                            <input type="radio" id="metode_tunai" name="metode_bayar" value="tunai" x-model="selectedMethod" class="hidden">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mr-3">
                                <i data-lucide="wallet" class="w-5 h-5"></i>
                            </div>
                            <span class="font-medium text-gray-800 dark:text-gray-200">Tunai</span>
                        </label>

                        <!-- Transfer Bank -->
                        @if($paymentInfo && $paymentInfo->has_bank_transfer)
                        <label for="metode_bank_transfer" class="payment-method-card" :class="{ 'selected': selectedMethod === 'bank_transfer' }">
                            <input type="radio" id="metode_bank_transfer" name="metode_bayar" value="bank_transfer" x-model="selectedMethod" class="hidden">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mr-3">
                                <i data-lucide="banknote" class="w-5 h-5"></i>
                            </div>
                            <span class="font-medium text-gray-800 dark:text-gray-200">Transfer Bank</span>
                        </label>
                        @endif

                        <!-- E-Wallet -->
                        @if($paymentInfo && $paymentInfo->has_e_wallet)
                        <label for="metode_e_wallet" class="payment-method-card" :class="{ 'selected': selectedMethod === 'e_wallet' }">
                            <input type="radio" id="metode_e_wallet" name="metode_bayar" value="e_wallet" x-model="selectedMethod" class="hidden">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 mr-3">
                                <i data-lucide="smartphone" class="w-5 h-5"></i>
                            </div>
                            <span class="font-medium text-gray-800 dark:text-gray-200">E-Wallet</span>
                        </label>
                        @endif

                        <!-- QR Code -->
                        @if($paymentInfo && $paymentInfo->has_qr_code)
                        <label for="metode_qr_code" class="payment-method-card" :class="{ 'selected': selectedMethod === 'qr_code' }">
                            <input type="radio" id="metode_qr_code" name="metode_bayar" value="qr_code" x-model="selectedMethod" class="hidden">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 mr-3">
                                <i data-lucide="qr-code" class="w-5 h-5"></i>
                            </div>
                            <span class="font-medium text-gray-800 dark:text-gray-200">QR Code</span>
                        </label>
                        @endif
                    </div>
                    @error('metode_bayar')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method Details (Conditional Display) -->
                @if($paymentInfo)
                <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600" x-show="selectedMethod !== ''">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Detail Pembayaran</h3>

                    <!-- Tunai Details -->
                    <div x-show="selectedMethod === 'tunai'" class="text-gray-700 dark:text-gray-300">
                        <p>Pembayaran tunai dilakukan langsung kepada pengurus RT.</p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Setelah pembayaran, pengurus RT akan mengkonfirmasi status pembayaran Anda.</p>
                    </div>

                    <!-- Bank Transfer Details -->
                    <div x-show="selectedMethod === 'bank_transfer'" class="text-gray-700 dark:text-gray-300">
                        @if($paymentInfo->has_bank_transfer)
                            <p>Bank: <span class="font-semibold">{{ $paymentInfo->bank_name }}</span></p>
                            <p>No. Rekening: <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $paymentInfo->bank_account_number }}</span></p>
                            <p>Atas Nama: <span class="font-semibold">{{ $paymentInfo->bank_account_name }}</span></p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Mohon transfer sesuai jumlah tagihan dan unggah bukti transfer.</p>
                            {{-- TODO: If you want specific bank logos here, you would need to add a 'bank_logo_url' field to your PaymentInfo model for each bank, and then display it here. Example: <img src="{{ $paymentInfo->bank_logo_url }}" alt="{{ $paymentInfo->bank_name }} Logo" class="h-8 w-auto mt-2"> --}}
                        @else
                            <p class="text-red-500">Informasi transfer bank belum tersedia.</p>
                        @endif
                    </div>

                    <!-- E-Wallet Details -->
                    <div x-show="selectedMethod === 'e_wallet'" class="text-gray-700 dark:text-gray-300">
                        @if($paymentInfo->has_e_wallet)
                            <p class="font-semibold mb-2">Pilih E-Wallet:</p>
                            <div class="space-y-2">
                                @foreach($paymentInfo->e_wallet_list as $walletName => $walletDetails)
                                    <div class="flex items-center space-x-2">
                                        {{-- TODO: If you want specific e-wallet logos here, you would need to add a 'dana_logo_url', 'gopay_logo_url' etc. field to your PaymentInfo model, and then display it here. Example: <img src="{{ $paymentInfo->dana_logo_url }}" alt="DANA Logo" class="h-6 w-auto"> --}}
                                        <i data-lucide="{{ 
                                            $walletName === 'dana' ? 'credit-card' : 
                                            ($walletName === 'gopay' ? 'smartphone' : 
                                            ($walletName === 'ovo' ? 'wallet' : 
                                            ($walletName === 'shopeepay' ? 'shopping-bag' : 'circle'))) 
                                        }}" class="w-5 h-5 text-gray-600 dark:text-gray-400"></i>
                                        <p class="capitalize">{{ $walletName }}: <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $walletDetails['number'] }}</span> (A.N: {{ $walletDetails['name'] }})</p>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Mohon transfer sesuai jumlah tagihan dan unggah bukti pembayaran.</p>
                        @else
                            <p class="text-red-500">Informasi E-Wallet belum tersedia.</p>
                        @endif
                    </div>

                    <!-- QR Code Details -->
                    <div x-show="selectedMethod === 'qr_code'" class="text-center text-gray-700 dark:text-gray-300">
                        @if($paymentInfo->has_qr_code)
                            <h4 class="font-semibold mb-2">Scan QR Code Ini:</h4>
                            <img src="{{ $paymentInfo->qr_code_url }}" alt="QR Code Pembayaran" class="mx-auto border border-gray-300 dark:border-gray-600 rounded-lg shadow-md" style="max-width: 250px;">
                            <p class="mt-2">{{ $paymentInfo->qr_code_description }}</p>
                            <p>A.N: <span class="font-semibold">{{ $paymentInfo->qr_code_account_name }}</span></p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Setelah scan dan pembayaran, mohon unggah bukti pembayaran.</p>
                        @else
                            <p class="text-red-500">QR Code pembayaran belum tersedia.</p>
                        @endif
                    </div>
                </div>
                @endif

                <div class="mb-6">
                    <label for="tanggal_bayar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_bayar" id="tanggal_bayar" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100" 
                           value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                    @error('tanggal_bayar')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="bukti_bayar_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unggah Bukti Pembayaran (Opsional)</label>
                    <input type="file" name="bukti_bayar_file" id="bukti_bayar_file" 
                           class="block w-full text-sm text-gray-900 dark:text-gray-100
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400 dark:hover:file:bg-blue-900/50"
                           accept="image/*,application/pdf"
                           @change="
                                const file = $event.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        if (file.type.startsWith('image/')) {
                                            filePreview = e.target.result;
                                            filePreviewType = 'image';
                                        } else if (file.type === 'application/pdf') {
                                            filePreview = null; // No direct PDF preview in img tag
                                            filePreviewType = 'pdf';
                                        } else {
                                            filePreview = null;
                                            filePreviewType = null;
                                        }
                                    };
                                    reader.readAsDataURL(file);
                                } else {
                                    filePreview = null;
                                    filePreviewType = null;
                                }
                           ">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: JPG, PNG, PDF. Maksimal 5MB.</p>
                    @error('bukti_bayar_file')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <template x-if="filePreviewType === 'image'">
                        <img :src="filePreview" alt="Pratinjau Bukti Pembayaran" class="file-upload-preview" style="display: block;">
                    </template>
                    <template x-if="filePreviewType === 'pdf'">
                        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center space-x-3 text-blue-700 dark:text-blue-300">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                            <span>File PDF terpilih. Pratinjau tidak tersedia.</span>
                        </div>
                    </template>
                </div>

                <div class="mb-6">
                    <label for="bukti_bayar_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan (Opsional)</label>
                    <textarea name="bukti_bayar_notes" id="bukti_bayar_notes" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 resize-y"
                              placeholder="Tambahkan catatan pembayaran Anda...">{{ old('bukti_bayar_notes') }}</textarea>
                    @error('bukti_bayar_notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-teal-600 hover:from-green-600 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg transition-all duration-200 hover:shadow-xl">
                        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                        Konfirmasi Pembayaran
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
</script>
@endpush
@endsection
