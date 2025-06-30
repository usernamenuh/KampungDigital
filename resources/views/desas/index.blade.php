@extends('layouts.app')

@section('title', 'Manajemen Data Desa')

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
<style>
    .stats-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .stats-card:hover::before {
        left: 100%;
    }

    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.15);
    }

    .village-image {
        transition: all 0.3s ease;
    }

    .village-image:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .dataTables_wrapper {
        font-family: inherit;
    }

    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        @apply border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-transparent;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        @apply px-3 py-2 mx-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: var(--color-primary) !important;
        @apply text-white border-transparent;
    }

    table.dataTable thead th {
        @apply bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 font-semibold;
    }

    table.dataTable tbody tr {
        @apply border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors;
    }

    table.dataTable tbody td {
        @apply text-gray-900 dark:text-gray-100;
    }

    .card-theme-default {
        @apply bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700;
    }

    .card-theme-gradient {
        background: linear-gradient(135deg, var(--color-primary) 0%, #764ba2 100%);
        @apply text-white border-0;
    }

    .card-theme-blur {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        @apply border border-white/20;
    }

    .dark .card-theme-blur {
        background: rgba(31, 41, 55, 0.8);
        @apply border-gray-700/20;
    }

    .card-theme-colored {
        background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        @apply text-gray-800 border-0;
    }
</style>
@endpush

@section('content')
<div class="p-6 space-y-6 animate-fade-in" x-data="desaIndex()" x-init="init()">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
        <div class="space-y-2">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center mr-4">
                    <i data-lucide="map-pin" class="w-5 h-5 text-white"></i>
                </div>
                Manajemen Data Desa
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Kelola data desa dengan interface modern dan responsif</p>
        </div>
        <div class="flex gap-3">
            <button @click="refreshAll()"
                    class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2" :class="{ 'animate-spin': isRefreshing }"></i>
                Refresh
            </button>
            <a href="{{ route('desas.create') }}"
               class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Tambah Desa Baru
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stats-card p-6 rounded-2xl shadow-lg"
             :class="getCardThemeClass()"
             style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);">
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <p class="text-purple-100 text-sm font-medium uppercase tracking-wide">Total Desa</p>
                    <p class="text-3xl font-bold text-white">{{ $desas->count() }}</p>
                    <div class="flex items-center text-purple-200 text-xs">
                        <i data-lucide="trending-up" class="w-3 h-3 mr-1"></i>
                        <span>Semua data</span>
                    </div>
                </div>
                <div class="p-4 bg-white bg-opacity-20 rounded-2xl backdrop-blur-sm">
                    <i data-lucide="home" class="w-8 h-8 text-white"></i>
                </div>
            </div>
        </div>

        <div class="stats-card p-6 rounded-2xl shadow-lg"
             style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <p class="text-emerald-100 text-sm font-medium uppercase tracking-wide">Desa Aktif</p>
                    <p class="text-3xl font-bold text-white">{{ $desas->where('status', 'aktif')->count() }}</p>
                    <div class="flex items-center text-emerald-200 text-xs">
                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                        <span>Status aktif</span>
                    </div>
                </div>
                <div class="p-4 bg-white bg-opacity-20 rounded-2xl backdrop-blur-sm">
                    <i data-lucide="check-circle" class="w-8 h-8 text-white"></i>
                </div>
            </div>
        </div>

        <div class="stats-card p-6 rounded-2xl shadow-lg"
             style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <p class="text-amber-100 text-sm font-medium uppercase tracking-wide">Rata-rata Saldo</p>
                    <p class="text-3xl font-bold text-white">Rp {{ number_format($desas->avg('saldo') ?? 0, 0, ',', '.') }}</p>
                    <div class="flex items-center text-amber-200 text-xs">
                        <i data-lucide="trending-up" class="w-3 h-3 mr-1"></i>
                        <span>Per desa</span>
                    </div>
                </div>
                <div class="p-4 bg-white bg-opacity-20 rounded-2xl backdrop-blur-sm">
                    <i data-lucide="trending-up" class="w-8 h-8 text-white"></i>
                </div>
            </div>
        </div>

        <div class="stats-card p-6 rounded-2xl shadow-lg"
             style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);">
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">Total Saldo</p>
                    <p class="text-3xl font-bold text-white">Rp {{ number_format($desas->sum('saldo') ?? 0, 0, ',', '.') }}</p>
                    <div class="flex items-center text-blue-200 text-xs">
                        <i data-lucide="dollar-sign" class="w-3 h-3 mr-1"></i>
                        <span>Keseluruhan</span>
                    </div>
                </div>
                <div class="p-4 bg-white bg-opacity-20 rounded-2xl backdrop-blur-sm">
                    <i data-lucide="dollar-sign" class="w-8 h-8 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 text-green-800 dark:text-green-200 px-6 py-4 rounded-r-xl shadow-sm animate-slide-up"
             x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 mr-3 text-green-500"></i>
                <div class="flex-1">
                    <p class="font-medium">Berhasil!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="ml-4 text-green-500 hover:text-green-700 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 text-red-800 dark:text-red-200 px-6 py-4 rounded-r-xl shadow-sm animate-slide-up"
             x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
            <div class="flex items-center">
                <i data-lucide="alert-circle" class="w-5 h-5 mr-3 text-red-500"></i>
                <div class="flex-1">
                    <p class="font-medium">Terjadi Kesalahan!</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="ml-4 text-red-500 hover:text-red-700 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center mr-3">
                        <i data-lucide="table" class="w-4 h-4 text-white"></i>
                    </div>
                    Data Desa
                </h2>
                <div class="flex gap-3">
                    <button @click="refreshTable()"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2" :class="{ 'animate-spin': isRefreshing }"></i>
                        Refresh
                    </button>
                    <button @click="exportData()"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="desasTable" class="w-full">
                    <thead>
                        <tr>
                            <th class="text-left py-4 px-4 font-semibold">No</th>
                            <th class="text-left py-4 px-4 font-semibold">Foto</th>
                            <th class="text-left py-4 px-4 font-semibold">Provinsi</th>
                            <th class="text-left py-4 px-4 font-semibold">Kabupaten/Kota</th>
                            <th class="text-left py-4 px-4 font-semibold">Kecamatan</th>
                            <th class="text-left py-4 px-4 font-semibold">Desa</th>
                            <th class="text-left py-4 px-4 font-semibold">Alamat</th>
                            <th class="text-left py-4 px-4 font-semibold">Kode Pos</th>
                            <th class="text-left py-4 px-4 font-semibold">Saldo</th>
                            <th class="text-left py-4 px-4 font-semibold">Status</th>
                            <th class="text-left py-4 px-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($desas as $index => $desa)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="py-4 px-4 font-medium">{{ $index + 1 }}</td>
                            <td class="py-4 px-4">
                                @if($desa->foto)
                                    <img src="{{ asset('storage/' . $desa->foto) }}"
                                         alt="Foto Desa"
                                         class="w-14 h-14 rounded-xl object-cover village-image cursor-pointer shadow-md"
                                         @click="showImageModal('{{ asset('storage/' . $desa->foto) }}')"
                                         onerror="this.onerror=null; this.src='{{ asset('images/placeholder-village.jpg') }}'; this.alt='Foto tidak tersedia';">
                                @else
                                    <div class="w-14 h-14 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-xl flex items-center justify-center shadow-md">
                                        <i data-lucide="image" class="w-6 h-6 text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-4">{{ $desa->province->province_name ?? '-' }}</td>
                            <td class="py-4 px-4">{{ $desa->regency->regency_name ?? '-' }}</td>
                            <td class="py-4 px-4">{{ $desa->district->district_name ?? '-' }}</td>
                            <td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">{{ $desa->village->village_name ?? '-' }}</td>
                            <td class="py-4 px-4 max-w-xs">
                                <div class="truncate" title="{{ $desa->alamat }}">{{ $desa->alamat }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border">
                                    {{ $desa->kode_pos }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-green-600 dark:text-green-400 text-lg">
                                    Rp {{ number_format($desa->saldo, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $desa->status == 'aktif' ? 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800' : 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800' }}">
                                    <div class="w-2 h-2 rounded-full {{ $desa->status == 'aktif' ? 'bg-green-500' : 'bg-red-500' }} mr-2"></div>
                                    {{ ucfirst($desa->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('desas.show', $desa->id) }}"
                                       class="inline-flex items-center justify-center w-9 h-9 text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-200 group"
                                       title="Lihat Detail">
                                        <i data-lucide="eye" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                                    </a>
                                    <a href="{{ route('desas.edit', $desa->id) }}"
                                       class="inline-flex items-center justify-center w-9 h-9 text-amber-600 hover:text-amber-700 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-all duration-200 group"
                                       title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                                    </a>
                                    <button @click="confirmDelete({{ $desa->id }}, '{{ $desa->village->village_name ?? 'Desa' }}')"
                                            class="inline-flex items-center justify-center w-9 h-9 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200 group"
                                            title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75"
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

<script>
function desaIndex() {
    return {
        showDeleteModal: false,
        showImageModal: false,
        deleteItemName: '',
        deleteAction: '',
        selectedImage: '',
        isRefreshing: false,
        cardStyle: localStorage.getItem('cardStyle') || 'default',

        init() {
            // Destroy existing DataTable instance if it exists
            if ($.fn.DataTable.isDataTable('#desasTable')) {
                $('#desasTable').DataTable().destroy();
            }

            // Initialize DataTable
            $('#desasTable').DataTable({
                processing: false,
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                columnDefs: [
                    { orderable: false, targets: [1, 10] },
                    { className: "text-center", targets: [0, 1, 7, 9, 10] }
                ],
                order: [[0, 'asc']],
                dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4"<"flex items-center"l><"flex items-center"f>>rtip',
                drawCallback: function() {
                    // Re-initialize icons after table redraw
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            });

            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Listen for card style changes
            window.addEventListener('cardStyleChanged', (e) => {
                this.cardStyle = e.detail;
            });
        },

        getCardThemeClass() {
            return `card-theme-${this.cardStyle}`;
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

        refreshTable() {
            this.isRefreshing = true;
            setTimeout(() => {
                // Since we're using server-side rendered data, just reload the page
                location.reload();
                this.isRefreshing = false;
                window.showNotification('Data berhasil diperbarui!', 'success');
            }, 1000);
        },

        refreshAll() {
            this.isRefreshing = true;
            setTimeout(() => {
                location.reload();
            }, 1000);
        },

        exportData() {
            window.showNotification('Fitur export akan segera tersedia!', 'info');
        }
    }
}
</script>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
@endpush
@endsection
