@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')
@section('page-title', 'Pembayaran Berhasil')
@section('page-description', 'Pembayaran kas Anda telah berhasil diproses.')

@section('content')
<div class="container mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md text-center">
    <div class="flex flex-col items-center justify-center py-12">
        <div class="w-24 h-24 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="check-circle" class="w-12 h-12 text-green-600"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Pembayaran Berhasil!</h2>
        <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
            Terima kasih! Bukti pembayaran kas Anda untuk minggu ke-<strong>{{ $payment->minggu_ke }}</strong> tahun <strong>{{ $payment->tahun }}</strong> telah berhasil diunggah.
        </p>
        <p class="text-md text-gray-600 dark:text-gray-400 mb-8">
            Pembayaran Anda sekarang berstatus <span class="font-semibold text-yellow-600">Menunggu Konfirmasi</span>. Pengurus RT/RW Anda akan segera memverifikasi pembayaran ini.
        </p>
        
        <div class="space-y-4">
            <a href="{{ route('dashboard.masyarakat') }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i data-lucide="home" class="w-5 h-5 mr-2"></i>
                Kembali ke Dashboard
            </a>
            <a href="{{ route('payments.download-proof', $payment->id) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 ml-4">
                <i data-lucide="download" class="w-5 h-5 mr-2"></i>
                Unduh Bukti Pembayaran
            </a>
        </div>
    </div>
</div>
@endsection
