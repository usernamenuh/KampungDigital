@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-6">
        <!-- Success Alert -->
        @if(session('success'))
        <div id="success-alert" class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ session('success') }}</span>
                <button onclick="document.getElementById('success-alert').remove()" class="absolute top-0 right-0 mt-2 mr-2 text-green-500 hover:text-green-700">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        <!-- Error Alert -->
        @if(session('error'))
        <div id="error-alert" class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ session('error') }}</span>
                <button onclick="document.getElementById('error-alert').remove()" class="absolute top-0 right-0 mt-2 mr-2 text-red-500 hover:text-red-700">
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
                <a href="{{ route('penduduk.index') }}" class="p-2 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Data Penduduk</h1>
                    <p class="text-sm text-gray-600">{{ $penduduk->nama_lengkap }} - {{ $penduduk->nik }}</p>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto">
            <form action="{{ route('penduduk.update', $penduduk) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Current Photo Display -->
                @if($penduduk->foto)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Foto Saat Ini</h3>
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('storage/' . $penduduk->foto) }}" alt="Foto {{ $penduduk->nama_lengkap }}" class="w-24 h-24 rounded-full object-cover border-4 border-gray-200">
                        <div>
                            <p class="text-sm text-gray-600">Foto yang sedang digunakan</p>
                            <p class="text-xs text-gray-500">Upload foto baru untuk mengganti</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Data Pribadi -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Data Pribadi</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">NIK <span class="text-red-500">*</span></label>
                            <input type="text" id="nik" name="nik" maxlength="16" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('nik') border-red-500 @enderror" value="{{ old('nik', $penduduk->nik) }}" required>
                            @error('nik')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('nama_lengkap') border-red-500 @enderror" value="{{ old('nama_lengkap', $penduduk->nama_lengkap) }}" required>
                            @error('nama_lengkap')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select id="jenis_kelamin" name="jenis_kelamin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('jenis_kelamin') border-red-500 @enderror" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('jenis_kelamin', $penduduk->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $penduduk->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" id="tempat_lahir" name="tempat_lahir" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('tempat_lahir') border-red-500 @enderror" value="{{ old('tempat_lahir', $penduduk->tempat_lahir) }}" required>
                            @error('tempat_lahir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('tanggal_lahir') border-red-500 @enderror" value="{{ old('tanggal_lahir', $penduduk->tanggal_lahir->format('Y-m-d')) }}" required>
                            @error('tanggal_lahir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="agama" class="block text-sm font-medium text-gray-700 mb-2">Agama <span class="text-red-500">*</span></label>
                            <select id="agama" name="agama" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('agama') border-red-500 @enderror" required>
                                <option value="">Pilih Agama</option>
                                <option value="Islam" {{ old('agama', $penduduk->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama', $penduduk->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama', $penduduk->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama', $penduduk->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ old('agama', $penduduk->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Khonghucu" {{ old('agama', $penduduk->agama) == 'Khonghucu' ? 'selected' : '' }}>Khonghucu</option>
                                <option value="Lainnya" {{ old('agama', $penduduk->agama) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('agama')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Data Keluarga -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Data Keluarga</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="kk_id" class="block text-sm font-medium text-gray-700 mb-2">Kartu Keluarga <span class="text-red-500">*</span></label>
                            <select id="kk_id" name="kk_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('kk_id') border-red-500 @enderror" required>
                                <option value="">Pilih Kartu Keluarga</option>
                                @foreach($kks as $kk)
                                    <option value="{{ $kk->id }}" {{ old('kk_id', $penduduk->kk_id) == $kk->id ? 'selected' : '' }}>
                                        {{ $kk->no_kk }} - {{ $kk->alamat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kk_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="hubungan_keluarga" class="block text-sm font-medium text-gray-700 mb-2">Hubungan Keluarga <span class="text-red-500">*</span></label>
                            <select id="hubungan_keluarga" name="hubungan_keluarga" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('hubungan_keluarga') border-red-500 @enderror" required>
                                <option value="">Pilih Hubungan</option>
                                <option value="Kepala Keluarga" {{ old('hubungan_keluarga', $penduduk->hubungan_keluarga) == 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                                <option value="Istri" {{ old('hubungan_keluarga', $penduduk->hubungan_keluarga) == 'Istri' ? 'selected' : '' }}>Istri</option>
                                <option value="Anak" {{ old('hubungan_keluarga', $penduduk->hubungan_keluarga) == 'Anak' ? 'selected' : '' }}>Anak</option>
                                <option value="Menantu" {{ old('hubungan_keluarga', $penduduk->hubungan_keluarga) == 'Menantu' ? 'selected' : '' }}>Menantu</option>
                                <option value="Cucu" {{ old('hubungan_keluarga', $penduduk->hubungan_keluarga) == 'Cucu' ? 'selected' : '' }}>Cucu</option>
                                <option value="Orangtua" {{ old('hubungan_keluarga', $penduduk->hubungan_keluarga) == 'Orangtua' ? 'selected' : '' }}>Orangtua</option>
                                <option value="Mertua" {{ old('hubungan_keluarga', $penduduk->hubungan_keluarga) == 'Mertua' ? 'selected' : '' }}>Mertua</option>
                                <option value="Famili Lain" {{ old('hubungan_keluarga', $penduduk->hubungan_keluarga) == 'Famili Lain' ? 'selected' : '' }}>Famili Lain</option>
                                <option value="Pembantu" {{ old('hubungan_keluarga', $penduduk->hubungan_keluarga) == 'Pembantu' ? 'selected' : '' }}>Pembantu</option>
                                <option value="Lainnya" {{ old('hubungan_keluarga', $penduduk->hubungan_keluarga) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('hubungan_keluarga')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status_perkawinan" class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan <span class="text-red-500">*</span></label>
                            <select id="status_perkawinan" name="status_perkawinan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('status_perkawinan') border-red-500 @enderror" required>
                                <option value="">Pilih Status</option>
                                <option value="Belum Kawin" {{ old('status_perkawinan', $penduduk->status_perkawinan) == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                                <option value="Kawin" {{ old('status_perkawinan', $penduduk->status_perkawinan) == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                                <option value="Cerai Hidup" {{ old('status_perkawinan', $penduduk->status_perkawinan) == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                <option value="Cerai Mati" {{ old('status_perkawinan', $penduduk->status_perkawinan) == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                            </select>
                            @error('status_perkawinan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
                            <input type="text" id="nama_ayah" name="nama_ayah" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('nama_ayah') border-red-500 @enderror" value="{{ old('nama_ayah', $penduduk->nama_ayah) }}">
                            @error('nama_ayah')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
                            <input type="text" id="nama_ibu" name="nama_ibu" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('nama_ibu') border-red-500 @enderror" value="{{ old('nama_ibu', $penduduk->nama_ibu) }}">
                            @error('nama_ibu')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Data Tambahan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Data Tambahan</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="pendidikan" class="block text-sm font-medium text-gray-700 mb-2">Pendidikan</label>
                            <select id="pendidikan" name="pendidikan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('pendidikan') border-red-500 @enderror">
                                <option value="">Pilih Pendidikan</option>
                                <option value="Tidak/Belum Sekolah" {{ old('pendidikan', $penduduk->pendidikan) == 'Tidak/Belum Sekolah' ? 'selected' : '' }}>Tidak/Belum Sekolah</option>
                                <option value="Belum Tamat SD/Sederajat" {{ old('pendidikan', $penduduk->pendidikan) == 'Belum Tamat SD/Sederajat' ? 'selected' : '' }}>Belum Tamat SD/Sederajat</option>
                                <option value="Tamat SD/Sederajat" {{ old('pendidikan', $penduduk->pendidikan) == 'Tamat SD/Sederajat' ? 'selected' : '' }}>Tamat SD/Sederajat</option>
                                <option value="SLTP/Sederajat" {{ old('pendidikan', $penduduk->pendidikan) == 'SLTP/Sederajat' ? 'selected' : '' }}>SLTP/Sederajat</option>
                                <option value="SLTA/Sederajat" {{ old('pendidikan', $penduduk->pendidikan) == 'SLTA/Sederajat' ? 'selected' : '' }}>SLTA/Sederajat</option>
                                <option value="Diploma I/II" {{ old('pendidikan', $penduduk->pendidikan) == 'Diploma I/II' ? 'selected' : '' }}>Diploma I/II</option>
                                <option value="Akademi/Diploma III/S.Muda" {{ old('pendidikan', $penduduk->pendidikan) == 'Akademi/Diploma III/S.Muda' ? 'selected' : '' }}>Akademi/Diploma III/S.Muda</option>
                                <option value="Diploma IV/Strata I" {{ old('pendidikan', $penduduk->pendidikan) == 'Diploma IV/Strata I' ? 'selected' : '' }}>Diploma IV/Strata I</option>
                                <option value="Strata II" {{ old('pendidikan', $penduduk->pendidikan) == 'Strata II' ? 'selected' : '' }}>Strata II</option>
                                <option value="Strata III" {{ old('pendidikan', $penduduk->pendidikan) == 'Strata III' ? 'selected' : '' }}>Strata III</option>
                            </select>
                            @error('pendidikan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="pekerjaan" class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan</label>
                            <input type="text" id="pekerjaan" name="pekerjaan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('pekerjaan') border-red-500 @enderror" value="{{ old('pekerjaan', $penduduk->pekerjaan) }}">
                            @error('pekerjaan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kewarganegaraan" class="block text-sm font-medium text-gray-700 mb-2">Kewarganegaraan</label>
                            <input type="text" id="kewarganegaraan" name="kewarganegaraan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('kewarganegaraan') border-red-500 @enderror" value="{{ old('kewarganegaraan', $penduduk->kewarganegaraan) }}">
                            @error('kewarganegaraan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status Penduduk <span class="text-red-500">*</span></label>
                            <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('status') border-red-500 @enderror" required>
                                <option value="">Pilih Status</option>
                                <option value="aktif" {{ old('status', $penduduk->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak_aktif" {{ old('status', $penduduk->status) == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="meninggal" {{ old('status', $penduduk->status) == 'meninggal' ? 'selected' : '' }}>Meninggal</option>
                                <option value="pindah" {{ old('status', $penduduk->status) == 'pindah' ? 'selected' : '' }}>Pindah</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">User Account</label>
                            <select id="user_id" name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('user_id') border-red-500 @enderror">
                                <option value="">Pilih User (Opsional)</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $penduduk->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">Foto Baru</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors duration-200">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="foto" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload foto baru</span>
                                            <input id="foto" name="foto" type="file" class="sr-only" accept="image/*">
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 2MB</p>
                                </div>
                            </div>
                            @error('foto')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                            <textarea id="keterangan" name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $penduduk->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between">
                    <a href="{{ route('penduduk.index') }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto hide alerts after 5 seconds
setTimeout(function() {
    const successAlert = document.getElementById('success-alert');
    const errorAlert = document.getElementById('error-alert');
    if (successAlert) successAlert.remove();
    if (errorAlert) errorAlert.remove();
}, 5000);

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
</script>
@endsection
