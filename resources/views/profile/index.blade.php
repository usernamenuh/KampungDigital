@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
@php
    $user = $user ?? Auth::user();
    $avatarUrl = $user->avatar ? asset('storage/' . $user->avatar) : null;
@endphp
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Profil Saya</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Kelola informasi profil dan pengaturan akun Anda</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($user->role ?? '-') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Profile Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <!-- Avatar Section (Read Only) -->
                    <div class="text-center mb-6">
                        <div class="relative inline-block">
                            <!-- Avatar Display -->
                            <div id="avatarDisplay" class="relative">
                                @if($avatarUrl)
                                    <img id="avatarImage" 
                                         src="{{ $avatarUrl }}" 
                                         alt="{{ $user->name ?? '-' }}" 
                                         class="w-24 h-24 rounded-full border-4 border-white dark:border-gray-700 shadow-lg object-cover"
                                         onerror="this.style.display='none'; document.getElementById('avatarInitials').style.display='flex';">
                                    <div id="avatarInitials" 
                                         class="w-24 h-24 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white text-2xl font-bold border-4 border-white dark:border-gray-700 shadow-lg" 
                                         style="display: none;">
                                        @php
                                            $initials = isset($user->name) ? collect(explode(' ', $user->name))
                                                            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                                            ->take(2)
                                                            ->implode('') : '-';
                                        @endphp
                                        {{ $initials }}
                                    </div>
                                @else
                                    @php
                                        $initials = isset($user->name) ? collect(explode(' ', $user->name))
                                                        ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                                        ->take(2)
                                                        ->implode('') : '-';
                                    @endphp
                                    <div id="avatarInitials" class="w-24 h-24 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center text-white text-2xl font-bold border-4 border-white dark:border-gray-700 shadow-lg">
                                        {{ $initials }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">{{ $user->name ?? '-' }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email ?? '-' }}</p>
                        
                        <!-- Role Badge -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mt-2
                            @if(!empty($user->role) && $user->role === 'admin') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @elseif(!empty($user->role) && $user->role === 'kades') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                            @elseif(!empty($user->role) && $user->role === 'rw') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                            @elseif(!empty($user->role) && $user->role === 'rt') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                            @endif">
                            {{ ucfirst($user->role ?? '-') }}
                        </span>
                    </div>

                    <!-- User Stats -->
                    <div class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Bergabung</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $user->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Terakhir Aktif</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $user->updated_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        @if(!empty($user->penduduk))
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">NIK</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ !empty($user->penduduk->nik) ? substr($user->penduduk->nik, 0, 8) . '****' : '-' }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Location Info -->
                @if(!empty($user->rt) || !empty($user->rw) || !empty($user->desa))
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Wilayah</h4>
                    <div class="space-y-3">
                        @if(!empty($user->desa))
                        <div class="flex items-center">
                            <i data-lucide="map-pin" class="w-4 h-4 text-gray-400 mr-3"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Desa: {{ $user->desa->nama ?? '-' }}</span>
                        </div>
                        @endif
                        @if(!empty($user->rw))
                        <div class="flex items-center">
                            <i data-lucide="home" class="w-4 h-4 text-gray-400 mr-3"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-400">RW: {{ $user->rw->nomor_rw ?? '-' }}</span>
                        </div>
                        @endif
                        @if(!empty($user->rt))
                        <div class="flex items-center">
                            <i data-lucide="users" class="w-4 h-4 text-gray-400 mr-3"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-400">RT: {{ $user->rt->nomor_rt ?? '-' }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-8">
                <!-- Success/Error Messages -->
                @if(session('success'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                    <div class="flex items-center">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-3"></i>
                        <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                    <div class="flex items-center">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mr-3"></i>
                        <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
                @endif

                <!-- Profile Information -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Profil</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Perbarui informasi profil dan alamat email Anda</p>
                    </div>
                    
                    <form action="{{ route('profile.update') }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nama Lengkap
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email
                                </label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role (Read Only) -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Role
                                </label>
                                <input type="text" 
                                       value="{{ ucfirst($user->role) }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-600 text-gray-500 dark:text-gray-400"
                                       readonly>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Security Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Keamanan Akun</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Perbarui password untuk menjaga keamanan akun</p>
                    </div>
                    
                    <form action="{{ route('profile.password.update') }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <!-- Current Password -->
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Password Saat Ini
                                </label>
                                <input type="password" 
                                       id="current_password" 
                                       name="current_password"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       required>
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- New Password -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Password Baru
                                    </label>
                                    <input type="password" 
                                           id="password" 
                                           name="password"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                           required>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Konfirmasi Password
                                    </label>
                                    <input type="password" 
                                           id="password_confirmation" 
                                           name="password_confirmation"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                           required>
                                </div>
                            </div>

                            <!-- Password Requirements -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Persyaratan Password:</h4>
                                <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                                    <li>• Minimal 8 karakter</li>
                                    <li>• Kombinasi huruf besar dan kecil</li>
                                    <li>• Minimal 1 angka</li>
                                    <li>• Minimal 1 karakter khusus</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                <i data-lucide="shield" class="w-4 h-4 inline mr-2"></i>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Lucide icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endsection
