@extends('layouts.app')

@section('title', 'Manajemen RT & RW')

@section('content')
<div class="p-6" x-data="rtRwManager()" x-init="init()">
    <!-- Success/Error Alert with Auto Dismiss (Shorter Duration) -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center">
            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
            {{ session('success') }}
            <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center">
            <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
            {{ session('error') }}
            <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <div class="flex items-center mb-2">
                <i data-lucide="alert-triangle" class="w-5 h-5 mr-2"></i>
                <strong>Terjadi kesalahan:</strong>
                <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen RT & RW</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola data Rukun Warga (RW) dan Rukun Tetangga (RT) dalam satu tempat</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="openModal('rw', 'create')" 
                    class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Tambah RW
            </button>
            <button @click="openModal('rt', 'create')" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Tambah RT
            </button>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cari</label>
                <input type="text" 
                       x-model="searchQuery"
                       @input="filterData()"
                       placeholder="Cari RW atau RT..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Desa</label>
                <select x-model="selectedDesa" @change="filterData()" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Semua Desa</option>
                    @foreach($desas as $desa)
                        <option value="{{ $desa->id }}">{{ $desa->alamat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select x-model="selectedStatus" @change="filterData()" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="tidak_aktif">Tidak Aktif</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tampilkan</label>
                <select x-model="viewMode" @change="filterData()" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="all">Semua</option>
                    <option value="rw">Hanya RW</option>
                    <option value="rt">Hanya RT</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <i data-lucide="home" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total RW</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $rws->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <i data-lucide="users" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total RT</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $rts->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Aktif</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $rws->where('status', 'aktif')->count() + $rts->where('status', 'aktif')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <i data-lucide="users-2" class="w-5 h-5 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total KK</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $rts->sum('jumlah_kk') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900 rounded-lg">
                    <i data-lucide="banknote" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Saldo</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">Rp {{ number_format($rws->sum('saldo') + $rts->sum('saldo'), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nomor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Wilayah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ketua</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">KK/RT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Saldo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- RW Data -->
                    @foreach($rws as $rw)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200" 
                        x-show="shouldShowItem('rw', {{ $rw->id }}, '{{ addslashes($rw->nama_rw) }}', '{{ addslashes($rw->desa->alamat ?? '') }}', '{{ $rw->status }}')">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                <i data-lucide="home" class="w-3 h-3 mr-1"></i>
                                RW
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $rw->no_rw ? str_pad($rw->no_rw, 3, '0', STR_PAD_LEFT) : '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $rw->nama_rw }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $rw->alamat }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $rw->desa->alamat ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $rw->ketua->nama_lengkap ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $rw->no_telpon ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $rw->rts->count() }} RT</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($rw->saldo, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $rw->status == 'aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ ucfirst($rw->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <button @click="viewDetail('rw', {{ $rw->id }})" 
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button @click="openModal('rw', 'edit', {{ $rw->id }})" 
                                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 p-1 rounded">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button @click="openDeleteModal('rw', {{ $rw->id }}, '{{ addslashes($rw->nama_rw) }}')" 
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1 rounded">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                    <!-- RT Data -->
                    @foreach($rts as $rt)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200" 
                        x-show="shouldShowItem('rt', {{ $rt->id }}, '{{ addslashes($rt->nama_rt) }}', '{{ addslashes($rt->rw->desa->alamat ?? '') }}', '{{ $rt->status }}')">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <i data-lucide="users" class="w-3 h-3 mr-1"></i>
                                RT
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $rt->no_rt ? str_pad($rt->no_rt, 3, '0', STR_PAD_LEFT) : '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $rt->nama_rt }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $rt->alamat ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $rt->rw->nama_rw ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $rt->rw->desa->alamat ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $rt->ketua->nama_lengkap ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $rt->no_telpon ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $rt->jumlah_kk }} KK</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($rt->saldo, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $rt->status == 'aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ ucfirst($rt->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <button @click="viewDetail('rt', {{ $rt->id }})" 
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button @click="openModal('rt', 'edit', {{ $rt->id }})" 
                                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 p-1 rounded">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button @click="openDeleteModal('rt', {{ $rt->id }}, '{{ addslashes($rt->nama_rt) }}')" 
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1 rounded">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for RW CRUD -->
    <div x-show="showModal && modalType === 'rw'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit="submitForm('rw', $event)" data-type="rw">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4" 
                                    x-text="modalAction === 'create' ? 'Tambah RW' : 'Edit RW'"></h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Desa <span class="text-red-500">*</span>
                                        </label>
                                        <select x-model="formData.desa_id" required
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            <option value="">Pilih Desa</option>
                                            @foreach($desas as $desa)
                                                <option value="{{ $desa->id }}">{{ $desa->village->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                No. RW <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" x-model="formData.no_rw" required
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                   placeholder="001">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Nama RW <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" x-model="formData.nama_rw" required
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                   placeholder="Kp. Digital">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Alamat <span class="text-red-500">*</span>
                                        </label>
                                        <textarea x-model="formData.alamat" required rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                  placeholder="Masukkan alamat RW"></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Ketua RW
                                        </label>
                                        <select x-model="formData.ketua_rw_id"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            <option value="">Pilih Ketua RW</option>
                                            @foreach($penduduks as $penduduk)
                                                <option value="{{ $penduduk->id }}">{{ $penduduk->nik }} - {{ $penduduk->nama_lengkap }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            No. Telepon
                                        </label>
                                        <input type="text" x-model="formData.no_telpon"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                               placeholder="Masukkan nomor telepon">
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Saldo
                                            </label>
                                            <input type="text"
                                                   x-init="$el.value = formatNumber(formData.saldo)"
                                                   @input="formData.saldo = unformatNumber($event.target.value); $event.target.value = formatNumber(formData.saldo)"
                                                   @focus="$event.target.value = unformatNumber($event.target.value)"
                                                   @blur="$event.target.value = formatNumber($event.target.value)"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                   placeholder="0">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Status <span class="text-red-500">*</span>
                                            </label>
                                            <select x-model="formData.status" required
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                <option value="aktif">Aktif</option>
                                                <option value="tidak_aktif">Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" :disabled="isSubmitting"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span x-show="!isSubmitting" x-text="modalAction === 'create' ? 'Simpan' : 'Update'"></span>
                            <span x-show="isSubmitting">Menyimpan...</span>
                        </button>
                        <button type="button" @click="closeModal()" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for RT CRUD -->
    <div x-show="showModal && modalType === 'rt'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit="submitForm('rt', $event)" data-type="rt">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4" 
                                    x-text="modalAction === 'create' ? 'Tambah RT' : 'Edit RT'"></h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            RW <span class="text-red-500">*</span>
                                        </label>
                                        <select x-model="formData.rw_id" required
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            <option value="">Pilih RW</option>
                                            @foreach($rws as $rw)
                                                <option value="{{ $rw->id }}">{{ $rw->nama_rw }} - {{ $rw->desa->alamat ?? 'N/A' }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                No. RT <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" x-model="formData.no_rt" required
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                   placeholder="001">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Nama RT <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" x-model="formData.nama_rt" required
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                   placeholder="Kp. Juara">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Alamat
                                        </label>
                                        <textarea x-model="formData.alamat" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                  placeholder="Masukkan alamat RT"></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Ketua RT
                                        </label>
                                        <select x-model="formData.ketua_rt_id"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            <option value="">Pilih Ketua RT</option>
                                            @foreach($penduduks as $penduduk)
                                                <option value="{{ $penduduk->id }}">{{ $penduduk->nik }} - {{ $penduduk->nama_lengkap }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            No. Telepon
                                        </label>
                                        <input type="text" x-model="formData.no_telpon"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                               placeholder="Masukkan nomor telepon">
                                    </div>

                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Jumlah KK
                                            </label>
                                            <input type="number" x-model="formData.jumlah_kk" min="0"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                   placeholder="0">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Saldo
                                            </label>
                                            <input type="text"
                                                   x-init="$el.value = formatNumber(formData.saldo)"
                                                   @input="formData.saldo = unformatNumber($event.target.value); $event.target.value = formatNumber(formData.saldo)"
                                                   @focus="$event.target.value = unformatNumber($event.target.value)"
                                                   @blur="$event.target.value = formatNumber($event.target.value)"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                   placeholder="0">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Status <span class="text-red-500">*</span>
                                            </label>
                                            <select x-model="formData.status" required
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                <option value="aktif">Aktif</option>
                                                <option value="tidak_aktif">Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" :disabled="isSubmitting"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span x-show="!isSubmitting" x-text="modalAction === 'create' ? 'Simpan' : 'Update'"></span>
                            <span x-show="isSubmitting">Menyimpan...</span>
                        </button>
                        <button type="button" @click="closeModal()" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
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
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeDeleteModal()"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                            <i data-lucide="alert-triangle" class="h-6 w-6 text-red-600 dark:text-red-400"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                Konfirmasi Hapus
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Apakah Anda yakin ingin menghapus <span class="font-semibold" x-text="deleteData.type?.toUpperCase()"></span> 
                                    "<span class="font-semibold" x-text="deleteData.name"></span>"?
                                </p>
                                <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                                    Tindakan ini tidak dapat dibatalkan!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="confirmDelete()" :disabled="isDeleting"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                        <span x-show="!isDeleting">Hapus</span>
                        <span x-show="isDeleting">Menghapus...</span>
                    </button>
                    <button @click="closeDeleteModal()" :disabled="isDeleting"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="showDetailModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeDetailModal()"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4" 
                                x-text="'Detail ' + (detailData.type === 'rw' ? 'RW' : 'RT')"></h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="detailData">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400" x-text="'No. ' + (detailData.type === 'rw' ? 'RW' : 'RT')"></label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="detailData.nomor || '-'"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nama</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="detailData.nama"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                          :class="detailData.status === 'aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'"
                                          x-text="detailData.status ? detailData.status.charAt(0).toUpperCase() + detailData.status.slice(1) : ''"></span>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Alamat</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="detailData.alamat || '-'"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400" x-text="'Ketua ' + (detailData.type === 'rw' ? 'RW' : 'RT')"></label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="detailData.ketua || '-'"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">No. Telepon</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="detailData.no_telpon || '-'"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Saldo</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="'Rp ' + (detailData.saldo ? new Intl.NumberFormat('id-ID').format(detailData.saldo) : '0')"></p>
                                </div>
                                <div x-show="detailData.type === 'rt'">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah KK</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="(detailData.jumlah_kk || 0) + ' KK'"></p>
                                </div>
                                <div x-show="detailData.type === 'rw'">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah RT</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="(detailData.jumlah_rt || 0) + ' RT'"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400" x-text="detailData.type === 'rw' ? 'Desa' : 'RW'"></label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="detailData.parent || '-'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="closeDetailModal()" 
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function rtRwManager() {
    return {
        // Modal state
        showModal: false,
        showDetailModal: false,
        showDeleteModal: false,
        modalType: '', // 'rw' or 'rt'
        modalAction: '', // 'create' or 'edit'
        editId: null,
        isSubmitting: false,
        isDeleting: false,
        
        // Filter state
        searchQuery: '',
        selectedDesa: '',
        selectedStatus: '',
        viewMode: 'all',
        
        // Form data
        formData: {},
        detailData: {},
        deleteData: {},
        
        // Data arrays
        rwData: @json($rws),
        rtData: @json($rts),
        pendudukData: @json($penduduks),
        
        init() {
            console.log('RT RW Manager initialized');
            this.resetFormData();
            this.initializeIcons();
        },
        
        initializeIcons() {
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                    console.log('Icons initialized');
                }
            });
        },
        
        resetFormData() {
            this.formData = {
                // RW fields
                desa_id: '',
                no_rw: '',
                nama_rw: '',
                alamat: '',
                ketua_rw_id: '',
                no_telpon: '',
                saldo: '', // Keep as empty string for initial state
                status: 'aktif',
                
                // RT fields
                rw_id: '',
                no_rt: '',
                nama_rt: '',
                ketua_rt_id: '',
                jumlah_kk: ''
            };
        },
        
        openModal(type, action, id = null) {
            console.log('Opening modal:', type, action, id);
            this.modalType = type;
            this.modalAction = action;
            this.editId = id;
            this.resetFormData();
            
            if (action === 'edit' && id) {
                this.loadEditData(type, id);
            }
            
            this.showModal = true;
            this.$nextTick(() => {
                this.initializeIcons();
            });
        },
        
        closeModal() {
            this.showModal = false;
            this.resetFormData();
            this.isSubmitting = false;
        },

        openDeleteModal(type, id, name) {
            console.log('Opening delete modal:', type, id, name);
            this.deleteData = {
                type: type,
                id: id,
                name: name
            };
            this.showDeleteModal = true;
            this.$nextTick(() => {
                this.initializeIcons();
            });
        },

        closeDeleteModal() {
            this.showDeleteModal = false;
            this.deleteData = {};
            this.isDeleting = false;
        },

        async confirmDelete() {
            if (this.isDeleting) return;
            
            this.isDeleting = true;
            
            // Buat form untuk delete
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/${this.deleteData.type}/${this.deleteData.id}`;
            
            // Tambahkan CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = csrfToken.getAttribute('content');
                form.appendChild(tokenInput);
            }
            
            // Tambahkan method DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        },
        
        loadEditData(type, id) {
            const data = type === 'rw' 
                ? this.rwData.find(item => item.id === id)
                : this.rtData.find(item => item.id === id);
                
            if (data) {
                console.log('Loading edit data:', data);
                
                // Load semua data form
                this.formData.desa_id = data.desa_id || '';
                this.formData.rw_id = data.rw_id || '';
                this.formData.alamat = data.alamat || '';
                this.formData.no_telpon = data.no_telpon || '';
                this.formData.saldo = data.saldo !== undefined && data.saldo !== null ? data.saldo : ''; // Ensure saldo is a number or empty string
                this.formData.status = data.status || 'aktif';
                this.formData.jumlah_kk = data.jumlah_kk || '';
                
                if (type === 'rw') {
                    this.formData.no_rw = data.no_rw ? String(data.no_rw).padStart(3, '0') : '';
                    this.formData.nama_rw = data.nama_rw || '';
                    this.formData.ketua_rw_id = data.ketua_rw_id || '';
                } else {
                    this.formData.no_rt = data.no_rt ? String(data.no_rt).padStart(3, '0') : '';
                    this.formData.nama_rt = data.nama_rt || '';
                    this.formData.ketua_rt_id = data.ketua_rt_id || '';
                }
                
                console.log('Form data after loading:', this.formData);
            }
        },
        
        submitForm(type, event) {
            event.preventDefault();
            
            if (this.isSubmitting) return false;
            
            console.log('Submitting form:', type, this.formData);
            this.isSubmitting = true;
            
            // Buat form element
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = this.modalAction === 'create' 
                ? `/${type}` 
                : `/${type}/${this.editId}`;
            
            // Tambahkan CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = csrfToken.getAttribute('content');
                form.appendChild(tokenInput);
            }
            
            // Tambahkan method untuk edit
            if (this.modalAction === 'edit') {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            }
            
            // Tambahkan semua form data
            Object.keys(this.formData).forEach(key => {
                if (this.formData[key] !== '' && this.formData[key] !== null && this.formData[key] !== undefined) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    // Ensure saldo is unformatted before sending
                    if (key === 'saldo') {
                        input.value = this.unformatNumber(this.formData[key]);
                    } else {
                        input.value = this.formData[key];
                    }
                    form.appendChild(input);
                }
            });
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
            
            return false;
        },
        
        viewDetail(type, id) {
            console.log('Viewing detail:', type, id);
            const data = type === 'rw' 
                ? this.rwData.find(item => item.id === id)
                : this.rtData.find(item => item.id === id);
                
            if (data) {
                this.detailData = {
                    type: type,
                    nomor: type === 'rw' ? (data.no_rw ? String(data.no_rw).padStart(3, '0') : '-') : (data.no_rt ? String(data.no_rt).padStart(3, '0') : '-'),
                    nama: type === 'rw' ? data.nama_rw : data.nama_rt,
                    alamat: data.alamat,
                    ketua: type === 'rw' ? (data.ketua ? data.ketua.nama_lengkap : '-') : (data.ketua ? data.ketua.nama_lengkap : '-'),
                    no_telpon: data.no_telpon,
                    saldo: data.saldo,
                    status: data.status,
                    jumlah_kk: data.jumlah_kk,
                    jumlah_rt: type === 'rw' ? (data.rts ? data.rts.length : 0) : null,
                    parent: type === 'rw' ? (data.desa ? data.desa.alamat : '') : (data.rw ? data.rw.nama_rw : '')
                };
                this.showDetailModal = true;
                this.$nextTick(() => {
                    this.initializeIcons();
                });
            }
        },
        
        closeDetailModal() {
            this.showDetailModal = false;
            this.detailData = {};
        },
        
        shouldShowItem(type, id, name, desa, status) {
            // Filter by view mode
            if (this.viewMode !== 'all' && this.viewMode !== type) {
                return false;
            }
            
            // Filter by search query
            if (this.searchQuery && 
                !name.toLowerCase().includes(this.searchQuery.toLowerCase()) && 
                !desa.toLowerCase().includes(this.searchQuery.toLowerCase())) {
                return false;
            }
            
            // Filter by desa
            if (this.selectedDesa) {
                const item = type === 'rw' 
                    ? this.rwData.find(rw => rw.id === id)
                    : this.rtData.find(rt => rt.id === id);
                    
                if (type === 'rw' && item && item.desa_id != this.selectedDesa) {
                    return false;
                }
                if (type === 'rt' && item && item.rw && item.rw.desa_id != this.selectedDesa) {
                    return false;
                }
            }
            
            // Filter by status
            if (this.selectedStatus && status !== this.selectedStatus) {
                return false;
            }
            
            return true;
        },
        
        filterData() {
            console.log('Filtering data with:', {
                search: this.searchQuery,
                desa: this.selectedDesa,
                status: this.selectedStatus,
                view: this.viewMode
            });
            // This method is called when filters change
            // The actual filtering is done in shouldShowItem method
            this.$nextTick(() => {
                this.initializeIcons();
            });
        },

        // Utility functions for number formatting
        formatNumber(value) {
            if (value === null || value === undefined || value === '') {
                return '';
            }
            const num = parseFloat(value);
            if (isNaN(num)) {
                return '';
            }
            return new Intl.NumberFormat('id-ID').format(num);
        },

        unformatNumber(value) {
            if (value === null || value === undefined || value === '') {
                return '';
            }
            // Remove all non-digit characters except for a potential comma for decimals
            // Then replace comma with dot for parseFloat
            return value.replace(/\./g, '').replace(/,/g, '.');
        }
    }
}

// Initialize icons after page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing icons');
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

// Debug Alpine.js
document.addEventListener('alpine:init', () => {
    console.log('Alpine.js initialized');
});
</script>
@endpush
