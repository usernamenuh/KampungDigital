@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-6">
        <!-- Success Alert -->
        @if(session('success'))
        <div id="success-alert" class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative dark:bg-green-900 dark:border-green-700 dark:text-green-300">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ session('success') }}</span>
                <button onclick="document.getElementById('success-alert').remove()" class="absolute top-0 right-0 mt-2 mr-2 text-green-500 hover:text-green-700 dark:text-green-300 dark:hover:text-green-100">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        <!-- Error Alert -->
        @if(session('error'))
        <div id="error-alert" class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative dark:bg-red-900 dark:border-red-700 dark:text-red-300">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ session('error') }}</span>
                <button onclick="document.getElementById('error-alert').remove()" class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700 dark:text-red-300 dark:hover:text-red-100">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('penduduk.index') }}" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Data Penduduk</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Lengkapi data penduduk dengan mengikuti langkah-langkah berikut</p>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto">
            <!-- Step Indicator -->
            <div class="mb-8">
                <div class="flex items-center justify-center">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div id="step-1-indicator" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold">1</div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-600">Data Pribadi</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Informasi dasar</p>
                            </div>
                        </div>
                        <div class="w-16 h-1 bg-gray-300 dark:bg-gray-700" id="step-1-line"></div>
                        <div class="flex items-center">
                            <div id="step-2-indicator" class="w-10 h-10 bg-gray-300 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full flex items-center justify-center font-semibold">2</div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Data Keluarga</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Hubungan keluarga</p>
                            </div>
                        </div>
                        <div class="w-16 h-1 bg-gray-300 dark:bg-gray-700" id="step-2-line"></div>
                        <div class="flex items-center">
                            <div id="step-3-indicator" class="w-10 h-10 bg-gray-300 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full flex items-center justify-center font-semibold">3</div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Data Tambahan</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Informasi lainnya</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('penduduk.store') }}" method="POST" enctype="multipart/form-data" id="multi-step-form">
                @csrf
                
                <!-- Step 1: Data Pribadi -->
                <div id="step-1" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Step 1: Data Pribadi</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nik" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">NIK <span class="text-red-500">*</span></label>
                            <input type="text" id="nik" name="nik" maxlength="16" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('nik') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" value="{{ old('nik') }}" required>
                            @error('nik')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('nama_lengkap') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" value="{{ old('nama_lengkap') }}" required>
                            @error('nama_lengkap')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select id="jenis_kelamin" name="jenis_kelamin" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('jenis_kelamin') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" id="tempat_lahir" name="tempat_lahir" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('tempat_lahir') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" value="{{ old('tempat_lahir') }}" required>
                            @error('tempat_lahir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('tanggal_lahir') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" value="{{ old('tanggal_lahir') }}" required>
                            @error('tanggal_lahir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="agama" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Agama <span class="text-red-500">*</span></label>
                            <select id="agama" name="agama" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('agama') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" required>
                                <option value="">Pilih Agama</option>
                                <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Khonghucu" {{ old('agama') == 'Khonghucu' ? 'selected' : '' }}>Khonghucu</option>
                                <option value="Lainnya" {{ old('agama') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('agama')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="button" id="next-step-1" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            Selanjutnya
                        </button>
                    </div>
                </div>

                <!-- Step 2: Data Keluarga -->
                <div id="step-2" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hidden">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Step 2: Data Keluarga</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="kk_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kartu Keluarga <span class="text-red-500">*</span></label>
                            <select id="kk_id" name="kk_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('kk_id') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" required>
                                <option value="">Pilih Kartu Keluarga</option>
                                @foreach($kks as $kk)
                                    <option value="{{ $kk->id }}" {{ (old('kk_id', request('kk_id')) == $kk->id) ? 'selected' : '' }} data-has-kepala="{{ $kk->kepala_keluarga_id ? 'true' : 'false' }}">
                                        {{ $kk->no_kk }} - {{ $kk->alamat }} {{ $kk->kepala_keluarga_id ? '(Sudah ada kepala keluarga)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kk_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="hubungan_keluarga" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hubungan Keluarga <span class="text-red-500">*</span></label>
                            <select id="hubungan_keluarga" name="hubungan_keluarga" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('hubungan_keluarga') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" required>
                                <option value="">Pilih Hubungan</option>
                                <option value="Kepala Keluarga" {{ old('hubungan_keluarga') == 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                                <option value="Istri" {{ old('hubungan_keluarga') == 'Istri' ? 'selected' : '' }}>Istri</option>
                                <option value="Anak" {{ old('hubungan_keluarga') == 'Anak' ? 'selected' : '' }}>Anak</option>
                                <option value="Menantu" {{ old('hubungan_keluarga') == 'Menantu' ? 'selected' : '' }}>Menantu</option>
                                <option value="Cucu" {{ old('hubungan_keluarga') == 'Cucu' ? 'selected' : '' }}>Cucu</option>
                                <option value="Orangtua" {{ old('hubungan_keluarga') == 'Orangtua' ? 'selected' : '' }}>Orangtua</option>
                                <option value="Mertua" {{ old('hubungan_keluarga') == 'Mertua' ? 'selected' : '' }}>Mertua</option>
                                <option value="Famili Lain" {{ old('hubungan_keluarga') == 'Famili Lain' ? 'selected' : '' }}>Famili Lain</option>
                                <option value="Pembantu" {{ old('hubungan_keluarga') == 'Pembantu' ? 'selected' : '' }}>Pembantu</option>
                                <option value="Lainnya" {{ old('hubungan_keluarga') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('hubungan_keluarga')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div id="kepala-keluarga-warning" class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg hidden dark:bg-yellow-900 dark:border-yellow-700">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300">Kartu Keluarga yang dipilih sudah memiliki Kepala Keluarga. Silakan pilih hubungan keluarga yang lain.</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="status_perkawinan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Perkawinan <span class="text-red-500">*</span></label>
                            <select id="status_perkawinan" name="status_perkawinan" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('status_perkawinan') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" required>
                                <option value="">Pilih Status</option>
                                <option value="Belum Kawin" {{ old('status_perkawinan') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                                <option value="Kawin" {{ old('status_perkawinan') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                                <option value="Cerai Hidup" {{ old('status_perkawinan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                <option value="Cerai Mati" {{ old('status_perkawinan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                            </select>
                            @error('status_perkawinan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nama_ayah" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Ayah</label>
                            <input type="text" id="nama_ayah" name="nama_ayah" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('nama_ayah') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" value="{{ old('nama_ayah') }}">
                            @error('nama_ayah')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nama_ibu" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Ibu</label>
                            <input type="text" id="nama_ibu" name="nama_ibu" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('nama_ibu') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" value="{{ old('nama_ibu') }}">
                            @error('nama_ibu')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <button type="button" id="prev-step-2" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                            Sebelumnya
                        </button>
                        <button type="button" id="next-step-2" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            Selanjutnya
                        </button>
                    </div>
                </div>

                <!-- Step 3: Data Tambahan -->
                <div id="step-3" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hidden">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Step 3: Data Tambahan</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="pendidikan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pendidikan</label>
                            <select id="pendidikan" name="pendidikan" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('pendidikan') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Pilih Pendidikan</option>
                                <option value="Tidak/Belum Sekolah" {{ old('pendidikan') == 'Tidak/Belum Sekolah' ? 'selected' : '' }}>Tidak/Belum Sekolah</option>
                                <option value="Belum Tamat SD/Sederajat" {{ old('pendidikan') == 'Belum Tamat SD/Sederajat' ? 'selected' : '' }}>Belum Tamat SD/Sederajat</option>
                                <option value="Tamat SD/Sederajat" {{ old('pendidikan') == 'Tamat SD/Sederajat' ? 'selected' : '' }}>Tamat SD/Sederajat</option>
                                <option value="SLTP/Sederajat" {{ old('pendidikan') == 'SLTP/Sederajat' ? 'selected' : '' }}>SLTP/Sederajat</option>
                                <option value="SLTA/Sederajat" {{ old('pendidikan') == 'SLTA/Sederajat' ? 'selected' : '' }}>SLTA/Sederajat</option>
                                <option value="Diploma I/II" {{ old('pendidikan') == 'Diploma I/II' ? 'selected' : '' }}>Diploma I/II</option>
                                <option value="Akademi/Diploma III/S.Muda" {{ old('pendidikan') == 'Akademi/Diploma III/S.Muda' ? 'selected' : '' }}>Akademi/Diploma III/S.Muda</option>
                                <option value="Diploma IV/Strata I" {{ old('pendidikan') == 'Diploma IV/Strata I' ? 'selected' : '' }}>Diploma IV/Strata I</option>
                                <option value="Strata II" {{ old('pendidikan') == 'Strata II' ? 'selected' : '' }}>Strata II</option>
                                <option value="Strata III" {{ old('pendidikan') == 'Strata III' ? 'selected' : '' }}>Strata III</option>
                            </select>
                            @error('pendidikan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="pekerjaan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pekerjaan</label>
                            <input type="text" id="pekerjaan" name="pekerjaan" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('pekerjaan') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" value="{{ old('pekerjaan') }}">
                            @error('pekerjaan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kewarganegaraan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kewarganegaraan</label>
                            <input type="text" id="kewarganegaraan" name="kewarganegaraan" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('kewarganegaraan') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" value="{{ old('kewarganegaraan', 'WNI') }}">
                            @error('kewarganegaraan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User Account</label>
                            <select id="user_id" name="user_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('user_id') border-red-500 @enderror bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Pilih User (Opsional)</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }}) - Role: {{ ucfirst($user->role) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-gray-400 dark:hover:border-gray-500 transition-colors duration-200">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                        <label for="foto" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload foto</span>
                                            <input id="foto" name="foto" type="file" class="sr-only" accept="image/*">
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">PNG, JPG, JPEG up to 2MB</p>
                                </div>
                            </div>
                            @error('foto')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <button type="button" id="prev-step-3" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                            Sebelumnya
                        </button>
                        <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                            Simpan Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    
    // Step navigation functions
    function showStep(step) {
        // Hide all steps
        document.getElementById('step-1').classList.add('hidden');
        document.getElementById('step-2').classList.add('hidden');
        document.getElementById('step-3').classList.add('hidden');
        
        // Show current step
        document.getElementById('step-' + step).classList.remove('hidden');
        
        // Update indicators
        updateStepIndicators(step);
        currentStep = step;
    }
    
    function updateStepIndicators(step) {
        // Reset all indicators
        for (let i = 1; i <= 3; i++) {
            const indicator = document.getElementById('step-' + i + '-indicator');
            const line = document.getElementById('step-' + i + '-line');
            
            if (i < step) {
                // Completed step
                indicator.className = 'w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center font-semibold';
                indicator.innerHTML = '✓';
                if (line) line.className = 'w-16 h-1 bg-green-600 dark:bg-green-600';
            } else if (i === step) {
                // Current step
                indicator.className = 'w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold';
                indicator.innerHTML = i;
                if (line) line.className = 'w-16 h-1 bg-gray-300 dark:bg-gray-700';
            } else {
                // Future step
                indicator.className = 'w-10 h-10 bg-gray-300 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full flex items-center justify-center font-semibold';
                indicator.innerHTML = i;
                if (line) line.className = 'w-16 h-1 bg-gray-300 dark:bg-gray-700';
            }
        }
    }
    
    function validateStep(step) {
        const requiredFields = {
            1: ['nik', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama'],
            2: ['kk_id', 'hubungan_keluarga', 'status_perkawinan'],
            3: []
        };
        
        const fields = requiredFields[step];
        let isValid = true;
        
        fields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        return isValid;
    }
    
    // Check KK and Hubungan Keluarga validation
    function checkKepalaKeluargaValidation() {
        const kkSelect = document.getElementById('kk_id');
        const hubunganSelect = document.getElementById('hubungan_keluarga');
        const warningDiv = document.getElementById('kepala-keluarga-warning');
        
        if (kkSelect.value && hubunganSelect.value === 'Kepala Keluarga') {
            const selectedOption = kkSelect.options[kkSelect.selectedIndex];
            const hasKepala = selectedOption.getAttribute('data-has-kepala') === 'true';
            
            if (hasKepala) {
                warningDiv.classList.remove('hidden');
                hubunganSelect.classList.add('border-red-500');
                return false;
            } else {
                warningDiv.classList.add('hidden');
                hubunganSelect.classList.remove('border-red-500');
                return true;
            }
        } else {
            warningDiv.classList.add('hidden');
            hubunganSelect.classList.remove('border-red-500');
            return true;
        }
    }
    
    // Event listeners
    document.getElementById('next-step-1').addEventListener('click', function() {
        if (validateStep(1)) {
            showStep(2);
        } else {
            alert('Mohon lengkapi semua field yang wajib diisi');
        }
    });
    
    document.getElementById('next-step-2').addEventListener('click', function() {
        if (validateStep(2) && checkKepalaKeluargaValidation()) {
            showStep(3);
        } else {
            alert('Mohon lengkapi semua field yang wajib diisi dan periksa validasi');
        }
    });
    
    document.getElementById('prev-step-2').addEventListener('click', function() {
        showStep(1);
    });
    
    document.getElementById('prev-step-3').addEventListener('click', function() {
        showStep(2);
    });
    
    // KK and Hubungan Keluarga change listeners
    document.getElementById('kk_id').addEventListener('change', checkKepalaKeluargaValidation);
    document.getElementById('hubungan_keluarga').addEventListener('change', checkKepalaKeluargaValidation);
    
    // File upload preview
    document.getElementById('foto').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                console.log('File selected:', file.name);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Auto hide alerts after 5 seconds
    setTimeout(function() {
        const successAlert = document.getElementById('success-alert');
        const errorAlert = document.getElementById('error-alert');
        if (successAlert) successAlert.remove();
        if (errorAlert) errorAlert.remove();
    }, 5000);
});
</script>
@endsection
