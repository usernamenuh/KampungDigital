@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Detail Pengguna</h1>
                        <p class="mt-1 text-sm text-gray-600">Informasi lengkap pengguna {{ $user->name }}</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="text-center">
                            <div class="mx-auto h-24 w-24 mb-4">
                                @if($user->penduduk && $user->penduduk->foto)
                                    <img class="h-24 w-24 rounded-full object-cover mx-auto" src="{{ asset('storage/' . $user->penduduk->foto) }}" alt="">
                                @else
                                    <div class="h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center mx-auto">
                                        <svg class="h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                            
                            <!-- Status Badge -->
                            <div class="mt-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->status == 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            <!-- Role Badge -->
                            <div class="mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-800' : 
                                       ($user->role == 'kades' ? 'bg-blue-100 text-blue-800' : 
                                       ($user->role == 'rw' ? 'bg-green-100 text-green-800' : 
                                       ($user->role == 'rt' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))) }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details Card -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Akun</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($user->role) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->status == 'active' ? 'Aktif' : 'Nonaktif' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email Verified</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($user->email_verified_at)
                                            <span class="text-green-600">✓ Terverifikasi</span>
                                            <div class="text-xs text-gray-500">{{ $user->email_verified_at->format('d M Y H:i') }}</div>
                                        @else
                                            <span class="text-red-600">✗ Belum verifikasi</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Bergabung</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d M Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Data Penduduk Card -->
                    @if($user->penduduk)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Data Penduduk Terkait</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->penduduk->nama_lengkap }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">NIK</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->penduduk->nik }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Jenis Kelamin</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->penduduk->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tempat, Tanggal Lahir</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->penduduk->tempat_lahir }}, {{ \Carbon\Carbon::parse($user->penduduk->tanggal_lahir)->format('d M Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($user->penduduk->status) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->penduduk->alamat }}</dd>
                                </div>
                            </dl>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="{{ route('penduduk.show', $user->penduduk) }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Lihat Detail Penduduk
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mt-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-900">Tidak Terhubung dengan Data Penduduk</h4>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Pengguna ini belum terhubung dengan data penduduk. Anda dapat menghubungkannya melalui halaman edit.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
