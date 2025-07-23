@extends('layouts.app')

@section('title', 'Proposal Bantuan')

@section('content')
<div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i data-lucide="heart-handshake" class="w-8 h-8 text-primary-500 mr-3"></i>
                    Proposal Bantuan
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Kelola proposal bantuan untuk {{ $rw->nama_rw ?? 'RW ' . $rw->no_rw }}
                </p>
            </div>
            <a href="{{ route('bantuan-proposals.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Ajukan Proposal Baru
            </a>
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
                            {{ $proposals->total() }}
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
                            {{ $proposals->where('status', 'pending')->count() }}
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
                            {{ $proposals->where('status', 'approved')->count() }}
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
                            {{ $proposals->where('status', 'rejected')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-full">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Proposals Table -->
        <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i data-lucide="list" class="w-5 h-5 mr-2"></i>
                    Daftar Proposal Bantuan
                </h3>
            </div>
            
            <div class="p-6">
                @if($proposals->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">No</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Judul Proposal</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Jumlah Bantuan</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Status</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Tanggal Ajukan</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($proposals as $index => $proposal)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                                        <td class="py-4 px-4 text-gray-900 dark:text-white">
                                            {{ $proposals->firstItem() + $index }}
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-semibold text-gray-900 dark:text-white">
                                                {{ $proposal->judul_proposal }}
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ Str::limit($proposal->deskripsi, 100) }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-green-600 dark:text-green-400">
                                                Rp {{ number_format($proposal->jumlah_bantuan, 0, ',', '.') }}
                                            </div>
                                            @if($proposal->status === 'approved' && $proposal->jumlah_disetujui)
                                                <div class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                                                    Disetujui: Rp {{ number_format($proposal->jumlah_disetujui, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($proposal->status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                                    Menunggu Review
                                                </span>
                                            @elseif($proposal->status === 'approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                                    Disetujui
                                                </span>
                                            @elseif($proposal->status === 'rejected')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
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
                                            @if($proposal->reviewed_at)
                                                <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                                    Diproses: {{ $proposal->reviewed_at->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('bantuan-proposals.show', $proposal) }}" 
                                                   class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm font-medium rounded-lg transition-colors duration-200"
                                                   title="Lihat Detail">
                                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                                </a>
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
                        <div class="mt-6 flex justify-center">
                            {{ $proposals->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="file-text" class="w-12 h-12 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            Belum Ada Proposal
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Anda belum mengajukan proposal bantuan apapun.
                        </p>
                        <a href="{{ route('bantuan-proposals.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Ajukan Proposal Pertama
                        </a>
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
