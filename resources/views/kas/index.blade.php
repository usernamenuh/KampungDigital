@extends('layouts.app') {{-- Pastikan ini mengarah ke layout utama Anda --}}

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Data Kas RT</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Manajemen data kas warga.</p>
            </div>
            @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('kas.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Tambah Kas
                </a>
                <button type="button" onclick="openGenerateModal()" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i data-lucide="calendar-plus" class="w-4 h-4 mr-2"></i>
                    Generate Kas Mingguan
                </button>
            </div>
            @endif
        </div>

        <!-- Statistics Cards -->
        <div class="mb-8">
            <!-- Main Stats Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Kas Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Kas Tertagih</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($stats['total_nominal_tertagih'] ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total keseluruhan tagihan</p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-xl">
                            <i data-lucide="wallet" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Lunas Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Lunas</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['lunas'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Jumlah kas lunas</p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-xl">
                            <i data-lucide="check-circle" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Belum Bayar Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Belum Bayar</p>
                            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($stats['belum_bayar'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Jumlah kas belum dibayar</p>
                        </div>
                        <div class="p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-xl">
                            <i data-lucide="clock" class="w-8 h-8 text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Terlambat Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Terlambat</p>
                            <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['terlambat'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Jumlah kas terlambat</p>
                        </div>
                        <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-xl">
                            <i data-lucide="alert-circle" class="w-8 h-8 text-red-600 dark:text-red-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Total Terkumpul Card -->
                <div class="bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-2xl shadow-sm border border-emerald-200 dark:border-emerald-800 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                    <i data-lucide="trending-up" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                                </div>
                                <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Total Terkumpul</p>
                            </div>
                            <p class="text-2xl font-bold text-emerald-800 dark:text-emerald-200">
                                Rp {{ number_format($stats['total_terkumpul'] ?? 0, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">
                                Dana yang sudah terkumpul dari kas lunas
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                {{ $stats['lunas'] ?? 0 }} kas lunas
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Belum Terkumpul Card -->
                <div class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-2xl shadow-sm border border-orange-200 dark:border-orange-800 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                    <i data-lucide="trending-down" class="w-5 h-5 text-orange-600 dark:text-orange-400"></i>
                                </div>
                                <p class="text-sm font-semibold text-orange-700 dark:text-orange-300">Belum Terkumpul</p>
                            </div>
                            <p class="text-2xl font-bold text-orange-800 dark:text-orange-200">
                                Rp {{ number_format($stats['total_outstanding'] ?? 0, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                                Dana yang belum terkumpul dari kas belum bayar/terlambat/menunggu konfirmasi
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">
                                {{ ($stats['belum_bayar'] ?? 0) + ($stats['terlambat'] ?? 0) + ($stats['menunggu_konfirmasi'] ?? 0) }} kas belum lunas
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-lg flex items-center justify-center">
                            <i data-lucide="filter" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Filter & Pencarian</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Gunakan filter untuk mempersempit hasil pencarian</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if(request()->hasAny(['status', 'rt_id', 'minggu_ke', 'tahun', 'nama', 'email']))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                <i data-lucide="filter-x" class="w-3 h-3 mr-1"></i>
                                Filter Aktif
                            </span>
                        @endif
                        <button type="button" onclick="toggleFilters()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <i data-lucide="chevron-down" class="w-5 h-5" id="filterToggleIcon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div id="filterContent" class="p-6">
                <form method="GET" action="{{ route('kas.index') }}" class="space-y-6">
                    <!-- Primary Filters Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Status Filter -->
                        <div class="space-y-2">
                            <label for="status" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <div class="w-5 h-5 bg-green-100 dark:bg-green-900/20 rounded-md flex items-center justify-center mr-2">
                                    <i data-lucide="check-circle" class="w-3 h-3 text-green-600 dark:text-green-400"></i>
                                </div>
                                Status Pembayaran
                            </label>
                            <select name="status" id="status" class="w-full px-3 py-2.5 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 rounded-lg text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200">
                                <option value="">Semua Status</option>
                                <option value="belum_bayar" {{ request('status') == 'belum_bayar' ? 'selected' : '' }}>
                                    🟡 Belum Bayar
                                </option>
                                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>
                                    🟢 Lunas
                                </option>
                                <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>
                                    🔴 Terlambat
                                </option>
                                <option value="menunggu_konfirmasi" {{ request('status') == 'menunggu_konfirmasi' ? 'selected' : '' }}>
                                    🟠 Menunggu Konfirmasi
                                </option>
                            </select>
                        </div>

                        <!-- RT Filter -->
                        @if(count($rtList) > 1 || (Auth::user()->role === 'rt' && count($rtList) === 1))
                        <div class="space-y-2">
                            <label for="rt_id" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <div class="w-5 h-5 bg-blue-100 dark:bg-blue-900/20 rounded-md flex items-center justify-center mr-2">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-blue-600 dark:text-blue-400"></i>
                                </div>
                                Rukun Tetangga
                            </label>
                            <select name="rt_id" id="rt_id" class="w-full px-3 py-2.5 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 rounded-lg text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200">
                                <option value="">Semua RT</option>
                                @foreach($rtList as $rt)
                                    <option value="{{ $rt->id }}" {{ request('rt_id') == $rt->id ? 'selected' : '' }}>
                                        🏘️ RT {{ $rt->no_rt }} / RW {{ $rt->rw->no_rw }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Week Filter -->
                        <div class="space-y-2">
                            <label for="minggu_ke" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <div class="w-5 h-5 bg-orange-100 dark:bg-orange-900/20 rounded-md flex items-center justify-center mr-2">
                                    <i data-lucide="calendar-days" class="w-3 h-3 text-orange-600 dark:text-orange-400"></i>
                                </div>
                                Minggu Ke
                            </label>
                            <select name="minggu_ke" id="minggu_ke" class="w-full px-3 py-2.5 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 rounded-lg text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200">
                                <option value="">Semua Minggu</option>
                                @for($i = 1; $i <= 53; $i++)
                                    <option value="{{ $i }}" {{ request('minggu_ke') == $i ? 'selected' : '' }}>
                                        📅 Minggu {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="space-y-2">
                            <label for="tahun" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <div class="w-5 h-5 bg-purple-100 dark:bg-purple-900/20 rounded-md flex items-center justify-center mr-2">
                                    <i data-lucide="calendar" class="w-3 h-3 text-purple-600 dark:text-purple-400"></i>
                                </div>
                                Tahun
                            </label>
                            <select name="tahun" id="tahun" class="w-full px-3 py-2.5 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 rounded-lg text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200">
                                <option value="">Semua Tahun</option>
                                @for($year = date('Y'); $year >= 2020; $year--)
                                    <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                                        🗓️ {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Search Filters Row -->
                    <div class="grid grid-cols-1 {{ in_array(Auth::user()->role, ['admin', 'kades']) ? 'md:grid-cols-2' : '' }} gap-4">
                        <!-- Name Search -->
                        <div class="space-y-2">
                            <label for="nama" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <div class="w-5 h-5 bg-indigo-100 dark:bg-indigo-900/20 rounded-md flex items-center justify-center mr-2">
                                    <i data-lucide="user-search" class="w-3 h-3 text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                Cari Nama Penduduk
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                                </div>
                                <input type="text" name="nama" id="nama" 
                                       class="w-full pl-10 pr-4 py-2.5 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 rounded-lg text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200" 
                                       value="{{ request('nama') }}" 
                                       placeholder="Ketik nama penduduk...">
                            </div>
                        </div>

                        <!-- Email Search (Admin Only) -->
                        @if(in_array(Auth::user()->role, ['admin', 'kades']))
                        <div class="space-y-2">
                            <label for="email" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <div class="w-5 h-5 bg-teal-100 dark:bg-teal-900/20 rounded-md flex items-center justify-center mr-2">
                                    <i data-lucide="mail-search" class="w-3 h-3 text-teal-600 dark:text-teal-400"></i>
                                </div>
                                Cari Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="at-sign" class="w-4 h-4 text-gray-400"></i>
                                </div>
                                <input type="email" name="email" id="email" 
                                       class="w-full pl-10 pr-4 py-2.5 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 rounded-lg text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200" 
                                       value="{{ request('email') }}" 
                                       placeholder="Ketik email penduduk...">
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                            <i data-lucide="info" class="w-4 h-4"></i>
                            <span>Menampilkan {{ $kas->count() }} dari {{ $kas->total() }} data kas</span>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('kas.index') }}" 
                               class="inline-flex items-center px-4 py-2.5 text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i>
                                Reset Filter
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl">
                                <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Penduduk</th>
                            @if(in_array(Auth::user()->role, ['admin', 'kades']))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">RT/RW</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jatuh Tempo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($kas as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                            <i data-lucide="user" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $item->penduduk->nama_lengkap }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            NIK: {{ $item->penduduk->nik }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            @if(in_array(Auth::user()->role, ['admin', 'kades']))
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->penduduk->user && $item->penduduk->user->email)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="mail" class="w-4 h-4 text-blue-500"></i>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $item->penduduk->user->email }}</span>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="mail-x" class="w-4 h-4 text-gray-400"></i>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Tidak ada email</span>
                                    </div>
                                @endif
                            </td>
                            @endif

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                RT {{ $item->rt->no_rt }} / RW {{ $item->rt->rw->no_rw }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                Minggu {{ $item->minggu_ke }} / {{ $item->tahun }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->status == 'lunas')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                        Lunas
                                    </span>
                                @elseif($item->status == 'belum_bayar')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                        <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                        Belum Bayar
                                    </span>
                                @elseif($item->status == 'menunggu_konfirmasi')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400">
                                        <i data-lucide="hourglass" class="w-3 h-3 mr-1"></i>
                                        Menunggu Konfirmasi
                                    </span>
                                @else {{-- terlambat --}}
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                        Terlambat
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('kas.show', $item) }}" 
                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                       title="Lihat Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    
                                    @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
                                    <a href="{{ route('kas.edit', $item) }}" 
                                       class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                       title="Edit Kas">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    @endif

                                    @if(Auth::user()->role === 'admin')
                                    <button type="button" onclick="confirmDelete({{ $item->id }}, '{{ $item->penduduk->nama_lengkap }}', 'Minggu {{ $item->minggu_ke }}/{{ $item->tahun }}')" 
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            title="Hapus Kas">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ in_array(Auth::user()->role, ['admin', 'kades']) ? '8' : '7' }}" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="inbox" class="w-12 h-12 text-gray-400 mb-4"></i>
                                    <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Tidak ada data kas</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm">Data kas akan muncul di sini setelah dibuat</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($kas->hasPages())
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                {{ $kas->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Generate Weekly Kas Modal -->
<div id="generateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Generate Kas Mingguan</h3>
                <button onclick="closeGenerateModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <form action="{{ route('kas.generate-weekly') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="generate_rt_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">RT</label>
                        <select name="rt_id" id="generate_rt_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih RT</option>
                            @foreach($rtList as $rt)
                                <option value="{{ $rt->id }}">RT {{ $rt->no_rt }} / RW {{ $rt->rw->no_rw }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="generate_jumlah" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" id="generate_jumlah" required min="1000" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="10000">
                    </div>
                    
                    <div>
                        <label for="generate_tahun" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tahun</label>
                        <select name="tahun" id="generate_tahun" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div>
                        <label for="generate_minggu_mulai" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minggu Mulai</label>
                        <select name="minggu_mulai" id="generate_minggu_mulai" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @for($i = 1; $i <= 52; $i++)
                                <option value="{{ $i }}" {{ $i == date('W') ? 'selected' : '' }}>Minggu {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div>
                        <label for="generate_minggu_selesai" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minggu Selesai</label>
                        <select name="minggu_selesai" id="generate_minggu_selesai" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @for($i = 1; $i <= 52; $i++)
                                <option value="{{ $i }}" {{ $i == 52 ? 'selected' : '' }}>Minggu {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeGenerateModal()" 
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                        Generate Kas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
@if(session('show_success_modal'))
<div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/20">
                <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-4">Berhasil!</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ session('success') }}
                </p>
                @if(session('kas_created'))
                <div class="mt-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Kas Dibuat:</span>
                            <span class="text-gray-900 dark:text-white">{{ session('kas_created') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Total Nominal:</span>
                            <span class="text-gray-900 dark:text-white">Rp {{ number_format(session('total_amount'), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeSuccessModal()" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-base font-medium rounded-md w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/20">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-4">Konfirmasi Hapus</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus kas untuk:
                </p>
                <div class="mt-3 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <p class="font-medium text-gray-900 dark:text-white" id="deleteName"></p>
                    <p class="text-sm text-gray-600 dark:text-gray-400" id="deletePeriod"></p>
                </div>
                <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                    Tindakan ini tidak dapat dibatalkan!
                </p>
            </div>
            <div class="items-center px-4 py-3 flex space-x-3">
                <button onclick="closeDeleteModal()" 
                        class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});


function toggleFilters() {
    const content = document.getElementById('filterContent');
    const icon = document.getElementById('filterToggleIcon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}

// Auto-expand filters if any filter is active
document.addEventListener('DOMContentLoaded', function() {
    const hasActiveFilters = {{ request()->hasAny(['status', 'rt_id', 'minggu_ke', 'tahun', 'nama', 'email']) ? 'true' : 'false' }};
    if (!hasActiveFilters) {
        document.getElementById('filterContent').style.display = 'none';
    }
});


function openGenerateModal() {
    document.getElementById('generateModal').classList.remove('hidden');
    // Re-initialize icons after modal opens
    setTimeout(() => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 100);
}

function closeGenerateModal() {
    document.getElementById('generateModal').classList.add('hidden');
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
}

function confirmDelete(kasId, nama, periode) {
    document.getElementById('deleteName').textContent = nama;
    document.getElementById('deletePeriod').textContent = periode;
    document.getElementById('deleteForm').action = `/kas/${kasId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
    
    // Re-initialize icons after modal opens
    setTimeout(() => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 100);
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    const generateModal = document.getElementById('generateModal');
    const deleteModal = document.getElementById('deleteModal');
    const successModal = document.getElementById('successModal'); // Added for success modal
    
    if (e.target === generateModal) {
        closeGenerateModal();
    }
    if (e.target === deleteModal) {
        closeDeleteModal();
    }
    if (e.target === successModal) { // Added for success modal
        closeSuccessModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeGenerateModal();
        closeDeleteModal();
        closeSuccessModal();
    }
});

// Auto-close success modal after 5 seconds
@if(session('show_success_modal'))
setTimeout(() => {
    closeSuccessModal();
}, 5000);
@endif
</script>
@endsection
