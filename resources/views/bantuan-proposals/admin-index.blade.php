@extends('layouts.app')

@section('title', 'Kelola Proposal Bantuan')
@section('page-title', 'Kelola Proposal Bantuan')
@section('page-description', 'Kelola semua proposal bantuan dari RW')

@push('styles')
<style>
    .proposal-card {
        transition: all 0.3s ease;
        border-radius: 1rem;
    }
    .proposal-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }
    .status-approved {
        background-color: #d1fae5;
        color: #065f46;
    }
    .status-rejected {
        background-color: #fee2e2;
        color: #991b1b;
    }
</style>
@endpush

@section('content')
<div class="p-6 space-y-6">
    <!-- Header with Statistics -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Proposal Bantuan</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola semua proposal bantuan dari RW</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.proposals.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-all duration-200">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Refresh
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Proposal</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-xl">
                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Menunggu Review</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2">{{ $stats['pending'] }}</p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-xl">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Disetujui</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2">{{ $stats['approved'] }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-xl">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ditolak</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2">{{ $stats['rejected'] }}</p>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-xl">
                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Bantuan</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-white mt-2">Rp {{ number_format($stats['total_amount_approved'], 0, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-xl">
                    <i data-lucide="banknote" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" action="{{ route('admin.proposals.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Filter Status</label>
                <select name="status" onchange="this.form.submit()" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cari Proposal</label>
                <div class="flex">
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Cari berdasarkan judul atau RW..."
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-r-lg transition-colors">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Proposals List -->
    <div class="space-y-4">
        @forelse($proposals as $proposal)
            <div class="proposal-card bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col lg:flex-row justify-between items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $proposal->judul_proposal }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    <span>RW {{ $proposal->rw->no_rw ?? 'N/A' }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $proposal->created_at->format('d M Y') }}</span>
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="status-badge 
                                    @if($proposal->status === 'pending') status-pending
                                    @elseif($proposal->status === 'approved') status-approved
                                    @elseif($proposal->status === 'rejected') status-rejected
                                    @endif">
                                    @if($proposal->status === 'pending') Menunggu
                                    @elseif($proposal->status === 'approved') Disetujui
                                    @elseif($proposal->status === 'rejected') Ditolak
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <p class="text-gray-700 dark:text-gray-300 mb-4 line-clamp-2">{{ Str::limit($proposal->deskripsi, 150) }}</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Jumlah Diminta</p>
                                <p class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($proposal->jumlah_bantuan, 0, ',', '.') }}</p>
                            </div>
                            @if($proposal->status === 'approved' && $proposal->jumlah_disetujui)
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Jumlah Disetujui</p>
                                <p class="font-semibold text-green-600">Rp {{ number_format($proposal->jumlah_disetujui, 0, ',', '.') }}</p>
                            </div>
                            @endif
                            @if($proposal->reviewed_at)
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal Review</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $proposal->reviewed_at->format('d M Y') }}</p>
                            </div>
                            @endif
                        </div>

                        @if($proposal->catatan_review)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Catatan Review:</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $proposal->catatan_review }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('bantuan-proposals.show', $proposal) }}" 
                           class="inline-flex items-center px-3 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 text-sm font-medium rounded-lg transition-colors">
                            <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                            Detail
                        </a>
                        
                        @if($proposal->status === 'pending')
                        <a href="{{ route('bantuan-proposals.process', $proposal) }}" 
                           class="inline-flex items-center px-3 py-2 bg-green-100 hover:bg-green-200 dark:bg-green-900 dark:hover:bg-green-800 text-green-700 dark:text-green-300 text-sm font-medium rounded-lg transition-colors">
                            <i data-lucide="check-square" class="w-4 h-4 mr-2"></i>
                            Proses
                        </a>
                        @endif
                        
                        @if($proposal->file_proposal)
                        <a href="{{ route('bantuan-proposals.download', $proposal) }}" 
                           class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                            File
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="file-text" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak ada proposal</h3>
                <p class="text-gray-500 dark:text-gray-400">Belum ada proposal bantuan yang diajukan.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($proposals->hasPages())
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700 dark:text-gray-300">
                Menampilkan {{ $proposals->firstItem() }} sampai {{ $proposals->lastItem() }} dari {{ $proposals->total() }} proposal
            </div>
            <div class="flex space-x-2">
                {{ $proposals->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
    @endif
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
