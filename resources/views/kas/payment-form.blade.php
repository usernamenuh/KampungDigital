@extends('layouts.app')

@section('title', 'Form Pembayaran Kas')
@section('page-title', 'Form Pembayaran Kas')
@section('page-description', 'Lengkapi detail pembayaran kas mingguan Anda.')

@section('content')
<div class="container mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Form Pembayaran Kas</h2>
        <a href="{{ route('dashboard.masyarakat') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Kembali ke Dashboard
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

    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-800 dark:text-blue-200">
        <h3 class="text-lg font-semibold mb-2">Detail Kas</h3>
        <p class="text-sm"><strong>Warga:</strong> {{ $kas->penduduk->nama_lengkap ?? 'N/A' }}</p>
        <p class="text-sm"><strong>RT/RW:</strong> RT {{ $kas->rt->no_rt ?? '-' }} / RW {{ $kas->rt->rw->no_rw ?? '-' }}</p>
        <p class="text-sm"><strong>Minggu Ke-:</strong> {{ $kas->minggu_ke }} ({{ $kas->tahun }})</p>
        <p class="text-sm"><strong>Jumlah:</strong> Rp {{ number_format($kas->jumlah, 0, ',', '.') }}</p>
        <p class="text-sm"><strong>Jatuh Tempo:</strong> {{ $kas->tanggal_jatuh_tempo->format('d M Y') }}</p>
        <p class="text-sm"><strong>Status:</strong> 
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                @if($kas->status == 'lunas') bg-green-100 text-green-800
                @elseif($kas->status == 'menunggu_konfirmasi') bg-yellow-100 text-yellow-800
                @elseif($kas->is_overdue) bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ $kas->status_text }}
            </span>
        </p>
    </div>

    @if($paymentInfo)
    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-green-800 dark:text-green-200">
        <h3 class="text-lg font-semibold mb-2">Informasi Pembayaran RT Anda</h3>
        @if(isset($paymentInfo->bank_transfer['account_number']) && $paymentInfo->bank_transfer['account_number'])
            <div class="mb-2">
                <p class="text-sm font-medium">Transfer Bank:</p>
                <p class="text-sm ml-2">Bank: {{ $paymentInfo->bank_transfer['bank_name'] ?? '-' }}</p>
                <p class="text-sm ml-2">No. Rek: {{ $paymentInfo->bank_transfer['account_number'] ?? '-' }}</p>
                <p class="text-sm ml-2">A.N.: {{ $paymentInfo->bank_transfer['account_name'] ?? '-' }}</p>
            </div>
        @endif
        @if(isset($paymentInfo->e_wallet) && (isset($paymentInfo->e_wallet['dana']) || isset($paymentInfo->e_wallet['ovo']) || isset($paymentInfo->e_wallet['gopay'])))
            <div class="mb-2">
                <p class="text-sm font-medium">E-Wallet:</p>
                @if(isset($paymentInfo->e_wallet['dana']))
                    <p class="text-sm ml-2">DANA: {{ $paymentInfo->e_wallet['dana'] }}</p>
                @endif
                @if(isset($paymentInfo->e_wallet['ovo']))
                    <p class="text-sm ml-2">OVO: {{ $paymentInfo->e_wallet['ovo'] }}</p>
                @endif
                @if(isset($paymentInfo->e_wallet['gopay']))
                    <p class="text-sm ml-2">GOPAY: {{ $paymentInfo->e_wallet['gopay'] }}</p>
                @endif
            </div>
        @endif
        @if(isset($paymentInfo->qr_code['image_url']) && $paymentInfo->qr_code['image_url'])
            <div class="mb-2 text-center">
                <p class="text-sm font-medium">QR Code:</p>
                <img src="{{ $paymentInfo->qr_code['image_url'] }}" alt="QR Code Pembayaran" class="w-48 h-48 mx-auto border rounded-lg mt-2">
                @if(isset($paymentInfo->qr_code['description']))
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $paymentInfo->qr_code['description'] }}</p>
                @endif
            </div>
        @endif
        @if($paymentInfo->payment_notes)
            <div class="mt-3 pt-3 border-t border-green-200 dark:border-green-700">
                <p class="text-sm font-medium">Catatan Pembayaran:</p>
                <p class="text-sm">{{ $paymentInfo->payment_notes }}</p>
            </div>
        @endif
    </div>
    @else
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg text-red-800 dark:text-red-200">
        <p class="text-sm font-medium">
            <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-2"></i>
            Informasi pembayaran untuk RT Anda belum diatur. Silakan hubungi pengurus RT/RW Anda.
        </p>
    </div>
    @endif

    <form action="{{ route('kas.payment.submit', $kas->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div>
            <label for="metode_bayar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Metode Pembayaran <span class="text-red-500">*</span></label>
            <select name="metode_bayar" id="metode_bayar" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                <option value="">Pilih Metode</option>
                <option value="tunai">Tunai</option>
                @if($paymentInfo)
                    @if(isset($paymentInfo->bank_transfer['account_number']) && $paymentInfo->bank_transfer['account_number'])
                        <option value="bank_transfer">Transfer Bank</option>
                    @endif
                    @if(isset($paymentInfo->e_wallet) && (isset($paymentInfo->e_wallet['dana']) || isset($paymentInfo->e_wallet['ovo']) || isset($paymentInfo->e_wallet['gopay'])))
                        <option value="e_wallet">E-Wallet</option>
                    @endif
                    @if(isset($paymentInfo->qr_code['image_url']) && $paymentInfo->qr_code['image_url'])
                        <option value="qr_code">QR Code</option>
                    @endif
                @endif
            </select>
            @error('metode_bayar')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="jumlah_dibayar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Dibayar <span class="text-red-500">*</span></label>
            <input type="number" name="jumlah_dibayar" id="jumlah_dibayar" step="0.01" min="0" required
                   value="{{ old('jumlah_dibayar', $kas->jumlah) }}"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
            @error('jumlah_dibayar')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="bukti_bayar_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Bukti Pembayaran <span class="text-red-500">*</span></label>
            <input type="file" name="bukti_bayar_file" id="bukti_bayar_file" accept="image/*,application/pdf" required
                   class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Format: JPG, PNG, PDF. Maksimal 5MB.</p>
            @error('bukti_bayar_file')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="bukti_bayar_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan (Opsional)</label>
            <textarea name="bukti_bayar_notes" id="bukti_bayar_notes" rows="3"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                      placeholder="Nomor referensi, keterangan, atau catatan lainnya...">{{ old('bukti_bayar_notes') }}</textarea>
            @error('bukti_bayar_notes')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                Konfirmasi Pembayaran
            </button>
        </div>
    </form>
</div>
@endsection
