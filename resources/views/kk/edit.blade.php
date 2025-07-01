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
                <a href="{{ route('kk.index') }}" class="p-2 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Kartu Keluarga</h1>
                    <p class="text-sm text-gray-600">{{ $kk->no_kk }}</p>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form Section -->
                <div class="lg:col-span-2">
                    <form action="{{ route('kk.update', $kk) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Informasi Kartu Keluarga</h3>
                            
                            <div class="space-y-6">
                                <div>
                                    <label for="no_kk" class="block text-sm font-medium text-gray-700 mb-2">Nomor Kartu Keluarga <span class="text-red-500">*</span></label>
                                    <input type="text" id="no_kk" name="no_kk" maxlength="16" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('no_kk') border-red-500 @enderror" value="{{ old('no_kk', $kk->no_kk) }}" required>
                                    @error('no_kk')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">16 digit nomor kartu keluarga</p>
                                </div>

                                <div>
                                    <label for="rt_id" class="block text-sm font-medium text-gray-700 mb-2">RT/RW <span class="text-red-500">*</span></label>
                                    <select id="rt_id" name="rt_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('rt_id') border-red-500 @enderror" required>
                                        <option value="">Pilih RT/RW</option>
                                        @foreach($rts as $rt)
                                            <option value="{{ $rt->id }}" {{ old('rt_id', $kk->rt_id) == $rt->id ? 'selected' : '' }}>
                                                RT {{ $rt->nama_rt }} - RW {{ $rt->rw->nama_rw }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('rt_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat <span class="text-red-500">*</span></label>
                                    <textarea id="alamat" name="alamat" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('alamat') border-red-500 @enderror" required>{{ old('alamat', $kk->alamat) }}</textarea>
                                    @error('alamat')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="kepala_keluarga_id" class="block text-sm font-medium text-gray-700 mb-2">Kepala Keluarga</label>
                                    <select id="kepala_keluarga_id" name="kepala_keluarga_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('kepala_keluarga_id') border-red-500 @enderror">
                                        <option value="">Pilih Kepala Keluarga</option>
                                        @foreach($penduduks as $penduduk)
                                            <option value="{{ $penduduk->id }}" {{ old('kepala_keluarga_id', $kk->kepala_keluarga_id) == $penduduk->id ? 'selected' : '' }}>
                                                {{ $penduduk->nama_lengkap }} ({{ $penduduk->nik }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kepala_keluarga_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Hanya anggota keluarga yang dapat dipilih sebagai kepala keluarga</p>
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('status') border-red-500 @enderror" required>
                                        <option value="">Pilih Status</option>
                                        <option value="aktif" {{ old('status', $kk->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="tidak_aktif" {{ old('status', $kk->status) == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tanggal_dibuat" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dibuat <span class="text-red-500">*</span></label>
                                    <input type="date" id="tanggal_dibuat" name="tanggal_dibuat" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('tanggal_dibuat') border-red-500 @enderror" value="{{ old('tanggal_dibuat', $kk->tanggal_dibuat->format('Y-m-d')) }}" required>
                                    @error('tanggal_dibuat')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                                    <textarea id="keterangan" name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $kk->keterangan) }}</textarea>
                                    @error('keterangan')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-between mt-8">
                                <a href="{{ route('kk.index') }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    Batal
                                </a>
                                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    Perbarui Kartu Keluarga
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Info Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Saat Ini</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">No. Kartu Keluarga</label>
                                <p class="text-sm text-gray-900 font-mono">{{ $kk->no_kk }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">RT/RW</label>
                                <p class="text-sm text-gray-900">RT {{ $kk->rt->nama_rt }} / RW {{ $kk->rt->rw->nama_rw }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Jumlah Anggota</label>
                                <p class="text-sm text-gray-900">{{ $kk->penduduks->count() }} orang</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $kk->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($kk->status) }}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Kepala Keluarga Saat Ini</label>
                                @if($kk->kepalaKeluarga)
                                    <div class="flex items-center space-x-3 mt-2">
                                        @if($kk->kepalaKeluarga->foto)
                                            <img src="{{ asset('storage/' . $kk->kepalaKeluarga->foto) }}" alt="Foto" class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $kk->kepalaKeluarga->nama_lengkap }}</p>
                                            <p class="text-xs text-gray-500">{{ $kk->kepalaKeluarga->nik }}</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 italic">Belum ditetapkan</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <a href="{{ route('kk.show', $kk) }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Lihat Detail
                            </a>
                        </div>
                    </div>

                    <!-- Anggota Keluarga -->
                    @if($penduduks->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Anggota Keluarga</h3>
                        
                        <div class="space-y-3">
                            @foreach($penduduks as $penduduk)
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                @if($penduduk->foto)
                                    <img src="{{ asset('storage/' . $penduduk->foto) }}" alt="Foto" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $penduduk->nama_lengkap }}</p>
                                    <p class="text-xs text-gray-500">{{ $penduduk->hubungan_keluarga }}</p>
                                </div>
                                @if($penduduk->id === $kk->kepala_keluarga_id)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        Kepala
                                    </span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
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
</script>
@endsection
