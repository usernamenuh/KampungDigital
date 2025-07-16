@extends('layouts.app')

@section('title', isset($paymentInfo) ? 'Edit Info Pembayaran' : 'Tambah Info Pembayaran')
@section('page-title', isset($paymentInfo) ? 'Edit Informasi Pembayaran' : 'Tambah Informasi Pembayaran')
@section('page-description', isset($paymentInfo) ? 'Formulir untuk mengedit detail informasi pembayaran.' : 'Formulir untuk menambahkan informasi pembayaran baru.')

@section('content')
<div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isset($paymentInfo) ? 'Edit Informasi Pembayaran' : 'Tambah Informasi Pembayaran' }}</h2>
            <a href="{{ route('payment-info.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800 w-full sm:w-auto">
                <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                Kembali
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Sukses!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ isset($paymentInfo) ? route('payment-info.update', $paymentInfo->id) : route('payment-info.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($paymentInfo))
                @method('PUT')
            @endif

            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center"><i data-lucide="home" class="w-5 h-5 mr-2 text-gray-500"></i>Informasi RT</h4>
                <div>
                    <label for="rt_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih RT <span class="text-red-500">*</span></label>
                    <select name="rt_id" id="rt_id" required {{ Auth::user()->role === 'rt' ? 'disabled' : '' }}
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white">
                        <option value="">Pilih RT</option>
                        @foreach($rts as $rt)
                            <option value="{{ $rt->id }}" 
                                {{ (isset($paymentInfo) && $paymentInfo->rt_id == $rt->id) || (Auth::user()->role === 'rt' && Auth::user()->penduduk->rt_id == $rt->id) ? 'selected' : '' }}>
                                RT {{ $rt->no_rt }} / RW {{ $rt->rw->no_rw ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    @error('rt_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if(Auth::user()->role === 'rt')
                        <input type="hidden" name="rt_id" value="{{ Auth::user()->penduduk->rt_id ?? '' }}">
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bank Transfer Info -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center"><i data-lucide="banknote" class="w-5 h-5 mr-2 text-blue-500"></i>Transfer Bank</h3>
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Bank</label>
                        <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $paymentInfo->bank_name ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                        @error('bank_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <label for="bank_account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor Rekening</label>
                        <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', $paymentInfo->bank_account_number ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                        @error('bank_account_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <label for="bank_account_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Atas Nama</label>
                        <input type="text" name="bank_account_name" id="bank_account_name" value="{{ old('bank_account_name', $paymentInfo->bank_account_name ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                        @error('bank_account_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- E-Wallet Info -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center"><i data-lucide="wallet" class="w-5 h-5 mr-2 text-purple-500"></i>E-Wallet</h3>
                    <div>
                        <label for="dana_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">DANA (Nomor HP)</label>
                        <input type="text" name="dana_number" id="dana_number" value="{{ old('dana_number', $paymentInfo->dana_number ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                        @error('dana_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <label for="ovo_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OVO (Nomor HP)</label>
                        <input type="text" name="ovo_number" id="ovo_number" value="{{ old('ovo_number', $paymentInfo->ovo_number ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                        @error('ovo_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <label for="gopay_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gopay (Nomor HP)</label>
                        <input type="text" name="gopay_number" id="gopay_number" value="{{ old('gopay_number', $paymentInfo->gopay_number ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                        @error('gopay_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <label for="shopeepay_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ShopeePay (Nomor HP)</label>
                        <input type="text" name="shopeepay_number" id="shopeepay_number" value="{{ old('shopeepay_number', $paymentInfo->shopeepay_number ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                        @error('shopeepay_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- QR Code Info -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center"><i data-lucide="qr-code" class="w-5 h-5 mr-2 text-orange-500"></i>QR Code</h3>
                @if(isset($paymentInfo) && $paymentInfo->qr_code_path)
                    <div class="mb-4 p-3 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4">
                        <p class="block text-sm font-medium text-gray-700 dark:text-gray-300">QR Code Saat Ini:</p>
                        <img src="{{ Storage::url($paymentInfo->qr_code_path) }}" alt="Current QR Code" class="w-28 h-28 object-contain border border-gray-200 dark:border-gray-600 rounded-md p-1 shadow-sm">
                        <div class="flex items-center mt-2 sm:mt-0">
                            <input type="checkbox" name="clear_qr_code" id="clear_qr_code" value="1" class="h-5 w-5 text-red-600 border-gray-300 rounded focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:checked:bg-red-600 cursor-pointer">
                            <label for="clear_qr_code" class="ml-2 text-sm text-red-600 dark:text-red-400 font-medium">Hapus QR Code Ini</label>
                        </div>
                    </div>
                @endif
                <div>
                    <label for="qr_code_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload QR Code Baru (Opsional)</label>
                    <input type="file" name="qr_code_file" id="qr_code_file" accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300 dark:hover:file:bg-blue-800 cursor-pointer">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">Format: JPG, PNG. Maksimal 2MB.</p>
                    @error('qr_code_file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mt-4">
                    <label for="qr_code_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi QR Code (Opsional)</label>
                    <textarea name="qr_code_description" id="qr_code_description" rows="2"
                              class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">{{ old('qr_code_description', $paymentInfo->qr_code_description ?? '') }}</textarea>
                    @error('qr_code_description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- General Notes and Active Status -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center"><i data-lucide="info" class="w-5 h-5 mr-2 text-gray-500"></i>Pengaturan Lainnya</h3>
                <div>
                    <label for="payment_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Pembayaran Umum (Opsional)</label>
                    <textarea name="payment_notes" id="payment_notes" rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">{{ old('payment_notes', $paymentInfo->payment_notes ?? '') }}</textarea>
                    @error('payment_notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mt-4 flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:checked:bg-blue-600 cursor-pointer"
                        {{ (isset($paymentInfo) && $paymentInfo->is_active) || !isset($paymentInfo) ? 'checked' : '' }}>
                    <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktifkan Informasi Pembayaran Ini</label>
                    @error('is_active')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 mt-6">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-offset-gray-800">
                    <i data-lucide="save" class="w-5 h-5 mr-2"></i>
                    {{ isset($paymentInfo) ? 'Update Informasi' : 'Simpan Informasi' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
