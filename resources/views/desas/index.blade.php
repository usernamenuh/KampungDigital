@extends('layouts.app')

@section('title', 'Daftar Desa')

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
<style>
    /* Custom DataTables Styling */
    .dataTables_wrapper {
        font-family: inherit;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        margin: 0.5rem 0;
    }

    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        @apply border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        @apply px-3 py-2 mx-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all text-sm;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        @apply bg-purple-600 text-white border-purple-600 hover:bg-purple-700;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        @apply opacity-50 cursor-not-allowed;
    }

    table.dataTable {
        border-collapse: separate;
        border-spacing: 0;
    }

    table.dataTable thead th {
        @apply bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-b-2 border-gray-200 dark:border-gray-700 font-semibold text-sm py-4 px-4;
        position: relative;
    }

    table.dataTable tbody tr {
        @apply border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors;
    }

    table.dataTable tbody td {
        @apply text-gray-900 dark:text-gray-100 py-4 px-4 text-sm;
    }

    /* Sticky Action Column */
    table.dataTable thead th:last-child,
    table.dataTable tbody td:last-child {
        position: sticky;
        right: 0;
        z-index: 10;
        background: white;
        box-shadow: -2px 0 4px rgba(0,0,0,0.1);
    }

    .dark table.dataTable thead th:last-child,
    .dark table.dataTable tbody td:last-child {
        background: rgb(31 41 55);
    }

    .dark table.dataTable thead th:last-child {
        background: rgb(31 41 55);
    }

    table.dataTable tbody tr:hover td:last-child {
        background: rgb(249 250 251);
    }

    .dark table.dataTable tbody tr:hover td:last-child {
        background: rgb(55 65 81);
    }

    /* Stats Cards Animation */
    .stats-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    /* Image hover effect */
    .village-image {
        transition: all 0.3s ease;
    }

    .village-image:hover {
        transform: scale(1.05);
    }

    /* Custom scrollbar */
    .table-container::-webkit-scrollbar {
        height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .dark .table-container::-webkit-scrollbar-track {
        background: #374151;
    }

    .dark .table-container::-webkit-scrollbar-thumb {
        background: #6b7280;
    }

    .dark .table-container::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="desaManager()">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-lucide="map-pin" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Desa</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola data desa dalam sistem</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button @click="refreshData()" 
                                :disabled="isLoading"
                                class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50">
                            <i data-lucide="refresh-cw" class="w-4 h-4 mr-2" :class="{ 'animate-spin': isLoading }"></i>
                            <span x-text="isLoading ? 'Loading...' : 'Refresh'"></span>
                        </button>
                        <a href="{{ route('desas.create') }}"
                           class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Tambah Desa
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Desa Aktif -->
            <div class="stats-card bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-2 mb-2">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            <span class="text-green-100 text-sm font-medium uppercase tracking-wide">Desa Aktif</span>
                        </div>
                        <p class="text-3xl font-bold">{{ $desas->where('status', 'aktif')->count() }}</p>
                        <p class="text-green-100 text-sm mt-1">Status aktif</p>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <!-- Tidak Aktif -->
            <div class="stats-card bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-2 mb-2">
                            <i data-lucide="x-circle" class="w-5 h-5"></i>
                            <span class="text-red-100 text-sm font-medium uppercase tracking-wide">Tidak Aktif</span>
                        </div>
                        <p class="text-3xl font-bold">{{ $desas->where('status', 'tidak_aktif')->count() }}</p>
                        <p class="text-red-100 text-sm mt-1">Status tidak aktif</p>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i data-lucide="x-circle" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <!-- Total Saldo -->
            <div class="stats-card bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-2 mb-2">
                            <i data-lucide="banknote" class="w-5 h-5"></i>
                            <span class="text-blue-100 text-sm font-medium uppercase tracking-wide">Total Saldo</span>
                        </div>
                        <p class="text-2xl font-bold">Rp {{ number_format($desas->sum('saldo'), 0, ',', '.') }}</p>
                        <p class="text-blue-100 text-sm mt-1">Keseluruhan</p>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i data-lucide="banknote" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <!-- Total Desa -->
            <div class="stats-card bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-2 mb-2">
                            <i data-lucide="building-2" class="w-5 h-5"></i>
                            <span class="text-purple-100 text-sm font-medium uppercase tracking-wide">Total Desa</span>
                        </div>
                        <p class="text-3xl font-bold">{{ $desas->count() }}</p>
                        <p class="text-purple-100 text-sm mt-1">Semua data</p>
                    </div>
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i data-lucide="building-2" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 text-green-800 dark:text-green-200 px-6 py-4 rounded-r-xl shadow-sm"
                 x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i data-lucide="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                        <div>
                            <p class="font-medium">Berhasil!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 text-red-800 dark:text-red-200 px-6 py-4 rounded-r-xl shadow-sm"
                 x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-3 text-red-500"></i>
                        <div>
                            <p class="font-medium">Terjadi Kesalahan!</p>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Data Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-700">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                            <i data-lucide="table" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Data Desa</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Menampilkan {{ $desas->count() }} dari {{ $desas->count() }} data</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button @click="exportData()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                            Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Content -->
            <div class="table-container overflow-x-auto">
                <table id="desasTable" class="w-full min-w-full">
                    <thead>
                        <tr>
                            <th class="text-left font-semibold w-16">No</th>
                            <th class="text-left font-semibold w-20">Foto</th>
                            <th class="text-left font-semibold min-w-32">Provinsi</th>
                            <th class="text-left font-semibold min-w-32">Kabupaten</th>
                            <th class="text-left font-semibold min-w-32">Kecamatan</th>
                            <th class="text-left font-semibold min-w-32">Desa</th>
                            <th class="text-left font-semibold min-w-48">Alamat</th>
                            <th class="text-left font-semibold w-24">Kode Pos</th>
                            <th class="text-left font-semibold min-w-32">Saldo</th>
                            <th class="text-left font-semibold w-24">Status</th>
                            <th class="text-center font-semibold w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($desas as $index => $desa)
                        <tr>
                            <td class="font-medium text-center">{{ $index + 1 }}</td>
                            <td>
                                @if($desa->foto)
                                    <img src="{{ asset('storage/' . $desa->foto) }}"
                                         alt="Foto Desa"
                                         class="w-12 h-12 rounded-xl object-cover village-image cursor-pointer shadow-md"
                                         @click="showImageModal('{{ asset('storage/' . $desa->foto) }}')"
                                         onerror="this.onerror=null; this.src='{{ asset('images/placeholder-village.jpg') }}'; this.alt='Foto tidak tersedia';">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-xl flex items-center justify-center shadow-md">
                                        <i data-lucide="image" class="w-5 h-5 text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="font-medium">{{ $desa->province->province_name ?? '-' }}</td>
                            <td>{{ $desa->regency->regency_name ?? '-' }}</td>
                            <td>{{ $desa->district->district_name ?? '-' }}</td>
                            <td class="font-semibold text-gray-900 dark:text-white">{{ $desa->village->village_name ?? '-' }}</td>
                            <td>
                                <div class="max-w-xs truncate" title="{{ $desa->alamat }}">{{ $desa->alamat }}</div>
                            </td>
                            <td class="text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border">
                                    {{ $desa->kode_pos }}
                                </span>
                            </td>
                            <td>
                                <span class="font-bold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($desa->saldo, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $desa->status == 'aktif' ? 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800' : 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800' }}">
                                    <div class="w-2 h-2 rounded-full {{ $desa->status == 'aktif' ? 'bg-green-500' : 'bg-red-500' }} mr-1"></div>
                                    {{ ucfirst($desa->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('desas.show', $desa->id) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-200 group"
                                       title="Lihat Detail">
                                        <i data-lucide="eye" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                                    </a>
                                    <a href="{{ route('desas.edit', $desa->id) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 text-amber-600 hover:text-amber-700 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-all duration-200 group"
                                       title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                                    </a>
                                    <button @click="confirmDelete({{ $desa->id }}, '{{ addslashes($desa->village->village_name ?? 'Desa') }}')"
                                            class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200 group"
                                            title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="building-2" class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum ada data desa</h3>
                                    <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan menambahkan desa pertama Anda.</p>
                                    <a href="{{ route('desas.create') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                        Tambah Desa
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75 backdrop-blur-sm"
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" @click="showDeleteModal = false"></div>

                <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-2xl rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 dark:bg-red-900/20 rounded-full mb-4">
                        <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600 dark:text-red-400"></i>
                    </div>

                    <div class="text-center space-y-3">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Konfirmasi Hapus</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Apakah Anda yakin ingin menghapus data desa
                            <span class="font-semibold text-gray-900 dark:text-white" x-text="deleteItemName"></span>?
                        </p>
                        <p class="text-sm text-red-600 dark:text-red-400 font-medium">
                            Tindakan ini tidak dapat dibatalkan!
                        </p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button @click="showDeleteModal = false"
                                class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                            <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                            Batal
                        </button>
                        <form :action="deleteAction" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                                <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i>
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div x-show="showImageModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-75 backdrop-blur-sm"
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" @click="showImageModal = false"></div>

                <div class="inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-2xl rounded-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Foto Desa</h3>
                        <button @click="showImageModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                    <div class="text-center">
                        <img :src="selectedImage" alt="Foto Desa" class="max-w-full h-auto rounded-xl shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function desaManager() {
    return {
        showDeleteModal: false,
        showImageModal: false,
        deleteItemName: '',
        deleteAction: '',
        selectedImage: '',
        isLoading: false,

        init() {
            // Initialize DataTable
            if ($.fn.DataTable.isDataTable('#desasTable')) {
                $('#desasTable').DataTable().destroy();
            }

            $('#desasTable').DataTable({
                processing: false,
                responsive: false,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                scrollX: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                columnDefs: [
                    { orderable: false, targets: [1, 10] },
                    { className: "text-center", targets: [0, 7, 10] },
                    { width: "60px", targets: [0] },
                    { width: "80px", targets: [1] },
                    { width: "120px", targets: [2, 3, 4, 5] },
                    { width: "200px", targets: [6] },
                    { width: "100px", targets: [7, 8] },
                    { width: "100px", targets: [9] },
                    { width: "120px", targets: [10] }
                ],
                order: [[0, 'asc']],
                dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4 px-6 py-4"<"flex items-center"l><"flex items-center"f>>rt<"flex flex-col sm:flex-row justify-between items-center mt-4 px-6 py-4"<"flex items-center"i><"flex items-center"p>>',
                drawCallback: function() {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            });

            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        },

        confirmDelete(id, name) {
            this.deleteItemName = name;
            this.deleteAction = `/desas/${id}`;
            this.showDeleteModal = true;
        },

        showImageModal(imageSrc) {
            this.selectedImage = imageSrc;
            this.showImageModal = true;
        },

        refreshData() {
            this.isLoading = true;
            setTimeout(() => {
                location.reload();
            }, 1000);
        },

        exportData() {
            // Implement export functionality
            alert('Fitur export akan segera tersedia!');
        }
    }
}
</script>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
    // Initialize icons after page load
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
