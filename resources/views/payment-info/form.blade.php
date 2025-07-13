@extends('layouts.app')

@section('title', isset($paymentInfo) ? 'Edit Info Pembayaran' : 'Tambah Info Pembayaran')
@section('page-title', isset($paymentInfo) ? 'Edit Informasi Pembayaran' : 'Tambah Informasi Pembayaran')
@section('page-description', isset($paymentInfo) ? 'Formulir untuk mengedit detail informasi pembayaran.' : 'Formulir untuk menambahkan informasi pembayaran baru.')

@section('content')
<div class="container mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ isset($paymentInfo) ? 'Edit Informasi Pembayaran' : 'Tambah Informasi Pembayaran' }}</h2>
        <a href="{{ route('payment-info.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
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

        <div>
            <label for="rt_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih RT <span class="text-red-500">*</span></label>
            <select name="rt_id" id="rt_id" required {{ Auth::user()->role === 'rt' ? 'disabled' : '' }}
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                <option value="">Pilih RT</option>
                @foreach($rts as $rt)
                    <option value="{{ $rt->id }}" 
                        {{ (isset($paymentInfo) && $paymentInfo->rt_id == $rt->id) || (Auth::user()->role === 'rt' && Auth::user()->penduduk->kk->rt_id == $rt->id) ? 'selected' : '' }}>
                        RT {{ $rt->no_rt }} / RW {{ $rt->rw->no_rw ?? '-' }}
                    </option>
                @endforeach
            </select>
            @error('rt_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @if(Auth::user()->role === 'rt')
                <input type="hidden" name="rt_id" value="{{ Auth::user()->penduduk->kk->rt_id ?? '' }}">
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Bank Transfer Info -->
            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Transfer Bank</h3>
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Bank</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $paymentInfo->bank_transfer['bank_name'] ?? '') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('bank_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mt-4">
                    <label for="account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Rekening</label>
                    <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $paymentInfo->bank_transfer['account_number'] ?? '') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('account_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mt-4">
                    <label for="account_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Atas Nama</label>
                    <input type="text" name="account_name" id="account_name" value="{{ old('account_name', $paymentInfo->bank_transfer['account_name'] ?? '') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('account_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- E-Wallet Info -->
            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">E-Wallet</h3>
                <div>
                    <label for="e_wallet_dana" class="block text-sm font-medium text-gray-700 dark:text-gray-300">DANA (Nomor HP)</label>
                    <input type="text" name="e_wallet_dana" id="e_wallet_dana" value="{{ old('e_wallet_dana', $paymentInfo->e_wallet['dana'] ?? '') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('e_wallet_dana')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mt-4">
                    <label for="e_wallet_ovo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">OVO (Nomor HP)</label>
                    <input type="text" name="e_wallet_ovo" id="e_wallet_ovo" value="{{ old('e_wallet_ovo', $paymentInfo->e_wallet['ovo'] ?? '') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('e_wallet_ovo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mt-4">
                    <label for="e_wallet_gopay" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gopay (Nomor HP)</label>
                    <input type="text" name="e_wallet_gopay" id="e_wallet_gopay" value="{{ old('e_wallet_gopay', $paymentInfo->e_wallet['gopay'] ?? '') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('e_wallet_gopay')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- QR Code Info -->
        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">QR Code</h3>
            @if(isset($paymentInfo) && $paymentInfo->qr_code && $paymentInfo->qr_code['image_url'])
                <div class="mb-4">
                    <p class="block text-sm font-medium text-gray-700 dark:text-gray-300">QR Code Saat Ini:</p>
                    <img src="{{ $paymentInfo->qr_code['image_url'] }}" alt="Current QR Code" class="w-32 h-32 object-cover rounded-md mt-2">
                    <div class="flex items-center mt-2">
                        <input type="checkbox" name="remove_qr_code" id="remove_qr_code" value="1" class="form-checkbox h-4 w-4 text-red-600">
                        <label for="remove_qr_code" class="ml-2 text-sm text-red-600 dark:text-red-400">Hapus QR Code</label>
                    </div>
                </div>
            @endif
            <div>
                <label for="qr_code_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload QR Code Baru (Opsional)</label>
                <input type="file" name="qr_code_file" id="qr_code_file" accept="image/*"
                       class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Format: JPG, PNG. Maksimal 2MB.</p>
                @error('qr_code_file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-4">
                <label for="qr_code_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi QR Code (Opsional)</label>
                <textarea name="qr_code_description" id="qr_code_description" rows="2"
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('qr_code_description', $paymentInfo->qr_code['description'] ?? '') }}</textarea>
                @error('qr_code_description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- General Notes and Active Status -->
        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pengaturan Lainnya</h3>
            <div>
                <label for="payment_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan Pembayaran Umum (Opsional)</label>
                <textarea name="payment_notes" id="payment_notes" rows="3"
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">{{ old('payment_notes', $paymentInfo->payment_notes ?? '') }}</textarea>
                @error('payment_notes')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-4 flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="form-checkbox h-5 w-5 text-blue-600"
                    {{ (isset($paymentInfo) && $paymentInfo->is_active) || !isset($paymentInfo) ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktifkan Informasi Pembayaran Ini</label>
                @error('is_active')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                {{ isset($paymentInfo) ? 'Update Informasi' : 'Simpan Informasi' }}
            </button>
        </div>
    </form>
</div>
@endsection
