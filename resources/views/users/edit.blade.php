@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('users.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Pengguna</h1>
                    <p class="mt-1 text-sm text-gray-600">Ubah informasi pengguna {{ $user->name }}</p>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Pengguna</h3>
                </div>
                
                <form action="{{ route('users.update', $user) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kolom Kiri -->
                        <div class="space-y-4">
                            <!-- Nama Lengkap -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('name') border-red-500 @enderror" 
                                       placeholder="Masukkan nama lengkap"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}"
                                       autocomplete="off"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('email') border-red-500 @enderror" 
                                       placeholder="Masukkan email"
                                       required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password Baru
                                </label>
                                <input type="password" 
                                       id="password" 
                                       name="password"
                                       autocomplete="new-password"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('password') border-red-500 @enderror" 
                                       placeholder="Kosongkan jika tidak ingin mengubah">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah password</p>
                            </div>

                            <!-- Konfirmasi Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Konfirmasi Password Baru
                                </label>
                                <input type="password" 
                                       id="password_confirmation" 
                                       name="password_confirmation"
                                       autocomplete="new-password"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                       placeholder="Ulangi password baru">
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="space-y-4">
                            <!-- Role -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <select id="role" 
                                        name="role" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors duration-200 @error('role') border-red-500 @enderror"
                                        required>
                                    <option value="">Pilih Role</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="kades" {{ old('role', $user->role) == 'kades' ? 'selected' : '' }}>Kepala Desa</option>
                                    <option value="rw" {{ old('role', $user->role) == 'rw' ? 'selected' : '' }}>RW</option>
                                    <option value="rt" {{ old('role', $user->role) == 'rt' ? 'selected' : '' }}>RT</option>
                                    <option value="masyarakat" {{ old('role', $user->role) == 'masyarakat' ? 'selected' : '' }}>Masyarakat</option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select id="status" 
                                        name="status" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors duration-200 @error('status') border-red-500 @enderror"
                                        required>
                                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Link dengan Penduduk -->
                            <div>
                                <label for="penduduk_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Link dengan Data Penduduk
                                </label>
                                <select id="penduduk_id" 
                                        name="penduduk_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors duration-200">
                                    <option value="">Tidak terhubung dengan penduduk</option>
                                    @foreach($penduduks as $penduduk)
                                        <option value="{{ $penduduk->id }}" {{ old('penduduk_id', $user->penduduk?->id) == $penduduk->id ? 'selected' : '' }}>
                                            {{ $penduduk->nama_lengkap }} - {{ $penduduk->nik }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Jika dipilih, akun ini akan terhubung dengan data penduduk</p>
                            </div>

                            <!-- Current Link Info -->
                            @if($user->penduduk)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-900">Saat ini terhubung dengan:</h4>
                                        <p class="text-sm text-blue-700 mt-1">
                                            <strong>{{ $user->penduduk->nama_lengkap }}</strong><br>
                                            NIK: {{ $user->penduduk->nik }}<br>
                                            Status: {{ ucfirst($user->penduduk->status) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 mt-6 border-t border-gray-200">
                        <a href="{{ route('users.index') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Fix untuk autocomplete styling */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 30px white inset !important;
    -webkit-text-fill-color: #374151 !important;
}

/* Fix untuk dropdown styling */
select:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Remove default select styling */
select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}
</style>
@endsection
