@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('kk.index') }}" class="p-2 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tambah Kartu Keluarga</h1>
                    <p class="text-sm text-gray-600">Buat kartu keluarga baru untuk penduduk</p>
                </div>
            </div>
        </div>

        <div class="max-w-2xl mx-auto">
            <form action="{{ route('kk.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label for="no_kk" class="block text-sm font-medium text-gray-700 mb-2">Nomor Kartu Keluarga <span class="text-red-500">*</span></label>
                        <input type="text" id="no_kk" name="no_kk" maxlength="16" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('no_kk') border-red-500 @enderror" value="{{ old('no_kk') }}" required>
                        @error('no_kk')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">16 digit nomor kartu keluarga</p>
                    </div>

                    <div>
                        <label for="rt_id" class="block text-sm font-medium text-gray-700 mb-2">RT/RW <span class="text-red-500">*</span></label>
                        <select id="rt_id" name="rt_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rt_id') border-red-500 @enderror" required>
                            <option value="">Pilih RT/RW</option>
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}" {{ old('rt_id') == $rt->id ? 'selected' : '' }}>
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
                        <textarea id="alamat" name="alamat" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('alamat') border-red-500 @enderror" required>{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_dibuat" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dibuat <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_dibuat" name="tanggal_dibuat" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_dibuat') border-red-500 @enderror" value="{{ old('tanggal_dibuat', date('Y-m-d')) }}" required>
                        @error('tanggal_dibuat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-between mt-8">
                    <a href="{{ route('kk.index') }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                        Simpan Kartu Keluarga
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
