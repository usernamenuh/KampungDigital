@extends('layouts.app')

@section('title', 'Review Proposal Bantuan')

@section('content')
<div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i data-lucide="clipboard-check" class="w-8 h-8 text-primary-500 mr-3"></i>
                    Review Proposal Bantuan
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Kelola dan review proposal bantuan dari RW
                </p>
            </div>
            <button onclick="refreshData()" 
                    class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                Refresh
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Proposal -->
            <div class="card-default rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                            Total Proposal
                        </p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                            {{ $stats['total'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                        <i data-lucide="file-text" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>

            <!-- Menunggu Review -->
            <div class="card-default rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                            Menunggu Review
                        </p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                            {{ $stats['pending'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-full">
                        <i data-lucide="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
            </div>

            <!-- Disetujui -->
            <div class="card-default rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                            Disetujui
                        </p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                            {{ $stats['approved'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>

            <!-- Ditolak -->
            <div class="card-default rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">
                            Ditolak
                        </p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                            {{ $stats['rejected'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-full">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Table Card -->
        <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i data-lucide="list" class="w-5 h-5 mr-2"></i>
                    Daftar Proposal Bantuan
                </h3>
                
                <!-- Filter -->
                <div class="flex items-center space-x-3">
                    <label for="statusFilter" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Filter Status:
                    </label>
                    <select id="statusFilter" 
                            onchange="filterByStatus()"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-white text-sm">
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                        <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    <button onclick="filterByStatus()" 
                            class="inline-flex items-center px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i data-lucide="filter" class="w-4 h-4 mr-1"></i>
                        Filter
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                @if($proposals->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">#</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Judul Proposal</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">RW Pengaju</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Jumlah Diminta</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Status</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Tanggal</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($proposals as $index => $proposal)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                                        <td class="py-4 px-4 text-gray-900 dark:text-white font-medium">
                                            {{ $proposals->firstItem() + $index }}
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-semibold text-gray-900 dark:text-white">
                                                {{ $proposal->judul_proposal }}
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ Str::limit($proposal->deskripsi, 50) }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mr-3">
                                                    <i data-lucide="home" class="w-4 h-4 text-primary-600 dark:text-primary-400"></i>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900 dark:text-white">
                                                        {{ $proposal->rw->nama_rw ?? 'RW ' . $proposal->rw->no_rw }}
                                                    </div>
                                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                                        {{ $proposal->submittedBy->name ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-green-600 dark:text-green-400">
                                                Rp {{ number_format($proposal->jumlah_bantuan, 0, ',', '.') }}
                                            </div>
                                            @if($proposal->jumlah_disetujui && $proposal->jumlah_disetujui != $proposal->jumlah_bantuan)
                                                <div class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                                                    Disetujui: Rp {{ number_format($proposal->jumlah_disetujui, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($proposal->status == 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                                    Menunggu Review
                                                </span>
                                            @elseif($proposal->status == 'approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                    <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                                    Disetujui
                                                </span>
                                            @elseif($proposal->status == 'rejected')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                    <i data-lucide="x" class="w-3 h-3 mr-1"></i>
                                                    Ditolak
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="text-gray-900 dark:text-white">
                                                {{ $proposal->created_at->format('d/m/Y') }}
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $proposal->created_at->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('bantuan-proposals.show', $proposal) }}" 
                                                   class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm font-medium rounded-lg transition-colors duration-200"
                                                   title="Lihat Detail">
                                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                                </a>
                                                @if($proposal->status == 'pending')
                                                    <a href="{{ route('bantuan-proposals.process', $proposal) }}" 
                                                       class="inline-flex items-center px-3 py-1.5 bg-primary-100 hover:bg-primary-200 text-primary-700 text-sm font-medium rounded-lg transition-colors duration-200"
                                                       title="Review Proposal">
                                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                                    </a>
                                                @endif
                                                @if($proposal->file_proposal)
                                                    <a href="{{ route('bantuan-proposals.download', $proposal) }}" 
                                                       class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200"
                                                       title="Download File">
                                                        <i data-lucide="download" class="w-4 h-4"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($proposals->hasPages())
                        <div class="mt-6 flex justify-between items-center">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Menampilkan {{ $proposals->firstItem() }} sampai {{ $proposals->lastItem() }} 
                                dari {{ $proposals->total() }} proposal
                            </div>
                            {{ $proposals->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="inbox" class="w-12 h-12 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            Tidak Ada Proposal
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            @if($status != 'all')
                                Tidak ada proposal dengan status "{{ ucfirst($status) }}" saat ini.
                            @else
                                Belum ada proposal bantuan yang diajukan.
                            @endif
                        </p>
                        @if($status != 'all')
                            <a href="{{ route('bantuan-proposals.kades.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <i data-lucide="list" class="w-4 h-4 mr-2"></i>
                                Lihat Semua Proposal
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Summary Card -->
        @if($stats['total'] > 0)
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i data-lucide="pie-chart" class="w-5 h-5 mr-2"></i>
                            Ringkasan Dana
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                <h4 class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                    Rp {{ number_format($stats['total_amount_requested'] ?? 0, 0, ',', '.') }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Diminta</p>
                            </div>
                            <div class="p-4 bg-green-50 dark:bg-green-900/30 rounded-lg">
                                <h4 class="text-lg font-bold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($stats['total_amount_approved'] ?? 0, 0, ',', '.') }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Disetujui</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function filterByStatus() {
    const status = document.getElementById('statusFilter').value;
    const url = new URL(window.location.href);
    url.searchParams.set('status', status);
    window.location.href = url.toString();
}

function refreshData() {
    window.showNotification('Memuat ulang data...', 'info');
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

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
