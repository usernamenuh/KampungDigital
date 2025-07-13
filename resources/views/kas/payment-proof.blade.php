@extends('layouts.app')

@section('title', 'Bukti Pembayaran Kas')
@section('page-title', 'Bukti Pembayaran Kas')
@section('page-description', 'Lihat dan konfirmasi bukti pembayaran kas.')

@section('content')
<div class="container mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Bukti Pembayaran Kas</h2>
        <a href="{{ route('payments.list') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Kembali ke Daftar Pembayaran
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Payment Details -->
        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Detail Pembayaran</h3>
            <div class="space-y-3 text-gray-700 dark:text-gray-300">
                <p><strong>Warga:</strong> {{ $kas->penduduk->nama_lengkap ?? 'N/A' }}</p>
                <p><strong>RT/RW:</strong> RT {{ $kas->rt->no_rt ?? '-' }} / RW {{ $kas->rt->rw->no_rw ?? '-' }}</p>
                <p><strong>Minggu Ke-:</strong> {{ $kas->minggu_ke }} ({{ $kas->tahun }})</p>
                <p><strong>Jumlah:</strong> Rp {{ number_format($kas->jumlah, 0, ',', '.') }}</p>
                <p><strong>Jatuh Tempo:</strong> {{ $kas->tanggal_jatuh_tempo->format('d M Y') }}</p>
                <p><strong>Status:</strong> 
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($kas->status == 'lunas') bg-green-100 text-green-800
                        @elseif($kas->status == 'menunggu_konfirmasi') bg-yellow-100 text-yellow-800
                        @elseif($kas->is_overdue) bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $kas->status_text }}
                    </span>
                </p>
                <p><strong>Metode Bayar:</strong> {{ $kas->metode_bayar_formatted }}</p>
                <p><strong>Waktu Upload Bukti:</strong> {{ $kas->bukti_bayar_uploaded_at_formatted }}</p>
                @if($kas->bukti_bayar_notes)
                    <p><strong>Keterangan:</strong> {{ $kas->bukti_bayar_notes }}</p>
                @endif
                @if($kas->status == 'lunas')
                    <p><strong>Tanggal Bayar:</strong> {{ $kas->tanggal_bayar_formatted }}</p>
                    <p><strong>Dikonfirmasi Oleh:</strong> {{ $kas->confirmedBy->name ?? 'N/A' }}</p>
                    <p><strong>Waktu Konfirmasi:</strong> {{ $kas->confirmed_at->format('d M Y H:i') }}</p>
                    @if($kas->confirmation_notes)
                        <p><strong>Catatan Konfirmasi:</strong> {{ $kas->confirmation_notes }}</p>
                    @endif
                @endif
            </div>
        </div>

        <!-- Payment Proof Image -->
        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm flex flex-col items-center justify-center">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Bukti Pembayaran</h3>
            @if($kas->bukti_bayar_file)
                <img src="{{ Storage::url($kas->bukti_bayar_file) }}" alt="Bukti Pembayaran" class="max-w-full h-auto rounded-lg shadow-md mb-4">
                <a href="{{ route('payments.download-proof', $kas->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                    Download Bukti
                </a>
            @else
                <p class="text-gray-500 dark:text-gray-400">Tidak ada bukti pembayaran diunggah.</p>
            @endif
        </div>
    </div>

    <!-- Confirmation Actions -->
    @if($kas->status == 'menunggu_konfirmasi' && in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
    <div x-data="{ confirmationNotes: '' }" class="mt-6 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Konfirmasi Pembayaran</h3>
        <form action="{{ route('payments.confirm', $kas->id) }}" method="POST" class="space-y-4">
            @csrf
            <div class="mb-4">
                <label for="confirmation_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan Konfirmasi (Opsional)</label>
                <textarea name="confirmation_notes" id="confirmation_notes" x-model="confirmationNotes" rows="3"
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                          placeholder="Tambahkan catatan untuk konfirmasi atau penolakan..."></textarea>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="submit" name="action" value="reject" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                    Tolak
                </button>
                <button type="submit" name="action" value="approve" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                    Setujui
                </button>
            </div>
        </form>
    </div>
    @elseif($kas->status == 'lunas')
    <div class="mt-6 p-4 bg-green-100 dark:bg-green-900/20 rounded-lg text-green-800 dark:text-green-200">
        <p class="text-sm font-medium text-center">
            <i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>
            Pembayaran ini sudah LUNAS.
        </p>
    </div>
    @endif
</div>
@endsection
