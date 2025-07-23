@extends('layouts.app')

@section('title', 'Detail Proposal Bantuan')

@section('content')
<div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i data-lucide="file-text" class="w-8 h-8 text-primary-500 mr-3"></i>
                    Detail Proposal Bantuan
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ $proposal->judul_proposal }}
                </p>
            </div>
            <div class="flex space-x-3">
                @if(auth()->user()->role === 'rw')
                    <a href="{{ route('bantuan-proposals.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Kembali
                    </a>
                @else
                    <a href="{{ route('bantuan-proposals.kades.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Kembali
                    </a>
                @endif
                @if($proposal->status === 'pending' && in_array(auth()->user()->role, ['kades', 'admin']))
                    <a href="{{ route('bantuan-proposals.process', $proposal) }}" 
                       class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i data-lucide="settings" class="w-4 h-4 mr-2"></i>
                        Proses Proposal
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Proposal Info -->
                <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                        <h2 class="text-xl font-semibold flex items-center">
                            <i data-lucide="info" class="w-5 h-5 mr-2"></i>
                            Informasi Proposal
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Basic Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    Judul Proposal
                                </label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $proposal->judul_proposal }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    RW Pengaju
                                </label>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mr-3">
                                        <i data-lucide="home" class="w-4 h-4 text-primary-600 dark:text-primary-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ $proposal->rw->nama_rw ?? 'RW ' . $proposal->rw->no_rw }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $proposal->submittedBy->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    Jumlah Bantuan Diminta
                                </label>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($proposal->jumlah_bantuan, 0, ',', '.') }}
                                </p>
                            </div>
                            @if($proposal->status === 'approved' && $proposal->jumlah_disetujui)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">
                                        Jumlah Disetujui
                                    </label>
                                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                        Rp {{ number_format($proposal->jumlah_disetujui, 0, ',', '.') }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                                Status Proposal
                            </label>
                            @if($proposal->status === 'pending')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                    Menunggu Review
                                </span>
                            @elseif($proposal->status === 'approved')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                                    Disetujui
                                </span>
                            @elseif($proposal->status === 'rejected')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                                    Ditolak
                                </span>
                            @endif
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                                Deskripsi Proposal
                            </label>
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-gray-900 dark:text-white whitespace-pre-line">{{ $proposal->deskripsi }}</p>
                            </div>
                        </div>

                        <!-- File Attachment -->
                        @if($proposal->file_proposal)
                            <div>
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                                    File Proposal
                                </label>
                                <a href="{{ route('bantuan-proposals.download', $proposal) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium rounded-lg transition-colors duration-200">
                                    <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                                    Download File Proposal
                                </a>
                            </div>
                        @endif

                        <!-- Review Notes -->
                        @if($proposal->catatan_review)
                            <div>
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                                    Catatan Review
                                </label>
                                <div class="p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg border-l-4 border-blue-400">
                                    <div class="flex items-start">
                                        <i data-lucide="sticky-note" class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0"></i>
                                        <p class="text-blue-800 dark:text-blue-200 whitespace-pre-line">{{ $proposal->catatan_review }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Timeline -->
                <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i data-lucide="clock" class="w-5 h-5 mr-2"></i>
                            Timeline Proposal
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Submitted -->
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-3 mt-1">
                                    <i data-lucide="send" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Proposal Diajukan</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $proposal->created_at->format('d F Y, H:i') }}
                                    </p>
                                </div>
                            </div>

                            @if($proposal->reviewed_at)
                                <!-- Reviewed -->
                                <div class="flex items-start">
                                    <div class="w-8 h-8 {{ $proposal->status === 'approved' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} rounded-full flex items-center justify-center mr-3 mt-1">
                                        @if($proposal->status === 'approved')
                                            <i data-lucide="check" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                                        @else
                                            <i data-lucide="x" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">
                                            Proposal {{ $proposal->status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $proposal->reviewed_at->format('d F Y, H:i') }}
                                            @if($proposal->reviewedBy)
                                                <br>oleh {{ $proposal->reviewedBy->name }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @if($proposal->status === 'approved' && $proposal->tanggal_pencairan)
                                    <!-- Disbursed -->
                                    <div class="flex items-start">
                                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i data-lucide="banknote" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 dark:text-white">Dana Dicairkan</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $proposal->tanggal_pencairan->format('d F Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <!-- Pending -->
                                <div class="flex items-start">
                                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mr-3 mt-1">
                                        <i data-lucide="clock" class="w-4 h-4 text-yellow-600 dark:text-yellow-400"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Menunggu Review</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Proposal sedang menunggu review dari Kepala Desa
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                @if(in_array(auth()->user()->role, ['kades', 'admin']) && $proposal->status === 'pending')
                    <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white">
                            <h3 class="text-lg font-semibold flex items-center">
                                <i data-lucide="zap" class="w-5 h-5 mr-2"></i>
                                Aksi Cepat
                            </h3>
                        </div>
                        <div class="p-6">
                            <a href="{{ route('bantuan-proposals.process', $proposal) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <i data-lucide="settings" class="w-4 h-4 mr-2"></i>
                                Proses Proposal
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Show success/error messages
@if(session('success'))
    window.showNotification('{{ session('success') }}', 'success');
@endif

@if(session('error'))
    window.showNotification('{{ session('error') }}', 'error');
@endif

// Initialize icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endsection
