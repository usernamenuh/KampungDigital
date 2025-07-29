@extends('layouts.app')

@section('title', 'Tambah Berita')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('berita.index') }}" 
           class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Berita</h1>
            <p class="text-gray-600 dark:text-gray-400">Buat berita baru untuk dipublikasikan</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <form action="{{ route('berita.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            
            <!-- Basic Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Judul -->
                <div class="lg:col-span-2">
                    <label for="judul" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Judul Berita <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="judul" 
                           name="judul" 
                           value="{{ old('judul') }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('judul') border-red-500 @enderror"
                           placeholder="Masukkan judul berita..."
                           required>
                    @error('judul')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="kategori" 
                            name="kategori" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('kategori') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Kategori</option>
                        <option value="umum" {{ old('kategori') == 'umum' ? 'selected' : '' }}>Umum</option>
                        <option value="pengumuman" {{ old('kategori') == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                        <option value="kegiatan" {{ old('kategori') == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                        <option value="pembangunan" {{ old('kategori') == 'pembangunan' ? 'selected' : '' }}>Pembangunan</option>
                        <option value="kesehatan" {{ old('kategori') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                        <option value="pendidikan" {{ old('kategori') == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                        <option value="ekonomi" {{ old('ekonomi') == 'ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                        <option value="sosial" {{ old('sosial') == 'sosial' ? 'selected' : '' }}>Sosial</option>
                        <option value="lingkungan" {{ old('lingkungan') == 'lingkungan' ? 'selected' : '' }}>Lingkungan</option>
                        <option value="keamanan" {{ old('keamanan') == 'keamanan' ? 'selected' : '' }}>Keamanan</option>
                    </select>
                    @error('kategori')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" 
                            name="status" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('status') border-red-500 @enderror"
                            required>
                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tingkat Akses -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div>
                    <label for="tingkat_akses" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tingkat Akses <span class="text-red-500">*</span>
                    </label>
                    <select id="tingkat_akses" 
                            name="tingkat_akses" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('tingkat_akses') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Tingkat Akses</option>
                        <option value="desa" {{ old('tingkat_akses') == 'desa' ? 'selected' : '' }}>Seluruh Desa</option>
                        @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw']))
                        <option value="rw" {{ old('tingkat_akses') == 'rw' ? 'selected' : '' }}>RW Tertentu</option>
                        @endif
                        @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
                        <option value="rt" {{ old('tingkat_akses') == 'rt' ? 'selected' : '' }}>RT Tertentu</option>
                        @endif
                    </select>
                    @error('tingkat_akses')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- RW Selection -->
                <div id="rw_selection" style="display: none;">
                    <label for="rw_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih RW <span class="text-red-500">*</span>
                    </label>
                    <select id="rw_id" 
                            name="rw_id" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Pilih RW</option>
                        @foreach($rws as $rw)
                        <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>
                            RW {{ $rw->no_rw }}
                        </option>
                        @endforeach
                    </select>
                    @error('rw_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- RT Selection -->
                <div id="rt_selection" style="display: none;">
                    <label for="rt_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih RT <span class="text-red-500">*</span>
                    </label>
                    <select id="rt_id" 
                            name="rt_id" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Pilih RT</option>
                        @foreach($rts as $rt)
                        <option value="{{ $rt->id }}" data-rw="{{ $rt->rw_id }}" {{ old('rt_id') == $rt->id ? 'selected' : '' }}>
                            RT {{ $rt->no_rt }} - RW {{ $rt->rw->no_rw }}
                        </option>
                        @endforeach
                    </select>
                    @error('rt_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Excerpt -->
            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Ringkasan Berita
                </label>
                <textarea id="excerpt" 
                          name="excerpt" 
                          rows="3"
                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('excerpt') border-red-500 @enderror"
                          placeholder="Ringkasan singkat berita (opsional)...">{{ old('excerpt') }}</textarea>
                @error('excerpt')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Konten -->
            <div>
                <label for="konten" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Konten Berita <span class="text-red-500">*</span>
                </label>
                <textarea id="konten" 
                          name="konten" 
                          rows="10"
                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('konten') border-red-500 @enderror"
                          placeholder="Tulis konten berita di sini..."
                          required>{{ old('konten') }}</textarea>
                @error('konten')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Media Upload -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Gambar -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Gambar
                    </label>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-primary-500 dark:hover:border-primary-400 transition-colors duration-200">
                        <input type="file" 
                               id="gambar" 
                               name="gambar" 
                               accept="image/*"
                               class="hidden"
                               onchange="previewImage(this)">
                        <label for="gambar" class="cursor-pointer">
                            <div id="gambar_preview" class="space-y-2">
                                <i data-lucide="image" class="w-8 h-8 text-gray-400 mx-auto"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Klik untuk upload gambar atau drag & drop
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">
                                    Format: JPG, PNG, GIF. Maksimal 5MB
                                </p>
                            </div>
                        </label>
                    </div>
                    @error('gambar')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Video -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Video
                    </label>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-primary-500 dark:hover:border-primary-400 transition-colors duration-200">
                        <input type="file" 
                               id="video" 
                               name="video" 
                               accept="video/*"
                               class="hidden"
                               onchange="previewVideo(this)">
                        <label for="video" class="cursor-pointer">
                            <div id="video_preview" class="space-y-2">
                                <i data-lucide="video" class="w-8 h-8 text-gray-400 mx-auto"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Klik untuk upload video atau drag & drop
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">
                                    Format: MP4, AVI, MOV. Maksimal 50MB
                                </p>
                            </div>
                        </label>
                    </div>
                    @error('video')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="mt-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                            Jika Anda mengupload video dan gambar, video akan diprioritaskan untuk ditampilkan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Additional Options -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Link External -->
                <div>
                    <label for="link" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Link External (Optional)
                    </label>
                    <div class="relative">
                        <i data-lucide="link" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="url" 
                               id="link" 
                               name="link" 
                               value="{{ old('link') }}"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('link') border-red-500 @enderror"
                               placeholder="https://example.com">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Link terkait berita (optional)
                    </p>
                    @error('link')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tags -->
                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tags
                    </label>
                    <div class="relative">
                        <i data-lucide="tag" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="text" 
                               id="tags" 
                               name="tags" 
                               value="{{ old('tags') }}"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('tags') border-red-500 @enderror"
                               placeholder="Pisahkan dengan koma (contoh: kesehatan, covid, vaksin)">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Pisahkan setiap tag dengan koma
                    </p>
                    @error('tags')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Pin Option -->
            @if(in_array(Auth::user()->role, ['admin', 'kades']))
            <div class="flex items-center space-x-3">
                <input type="checkbox" 
                       id="is_pinned" 
                       name="is_pinned" 
                       value="1"
                       {{ old('is_pinned') ? 'checked' : '' }}
                       class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="is_pinned" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Pin berita ini (berita penting akan ditampilkan di atas)
                </label>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('berita.index') }}" 
                   class="w-full sm:w-auto px-6 py-3 text-center bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2 inline"></i>
                    Kembali
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i data-lucide="save" class="w-4 h-4 mr-2 inline"></i>
                    Simpan Berita
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tingkatAkses = document.getElementById('tingkat_akses');
    const rwSelection = document.getElementById('rw_selection');
    const rtSelection = document.getElementById('rt_selection');
    const rwSelect = document.getElementById('rw_id');
    const rtSelect = document.getElementById('rt_id');

    // Handle tingkat akses change
    tingkatAkses.addEventListener('change', function() {
        const value = this.value;
        
        // Hide all selections first
        rwSelection.style.display = 'none';
        rtSelection.style.display = 'none';
        
        // Clear selections
        rwSelect.value = '';
        rtSelect.value = '';
        
        // Show relevant selection
        if (value === 'rw') {
            rwSelection.style.display = 'block';
            rwSelect.required = true;
            rtSelect.required = false;
        } else if (value === 'rt') {
            rtSelection.style.display = 'block';
            rtSelect.required = true;
            rwSelect.required = false;
        } else {
            rwSelect.required = false;
            rtSelect.required = false;
        }
    });

    // Handle RW change to filter RT options
    rwSelect.addEventListener('change', function() {
        const selectedRwId = this.value;
        const rtOptions = rtSelect.querySelectorAll('option');
        
        rtOptions.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            
            const optionRwId = option.getAttribute('data-rw');
            if (selectedRwId === '' || optionRwId === selectedRwId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
        
        rtSelect.value = '';
    });

    // Initialize on page load
    tingkatAkses.dispatchEvent(new Event('change'));
});

// Image preview function
function previewImage(input) {
    const preview = document.getElementById('gambar_preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" class="max-w-full h-32 object-cover rounded-lg mx-auto">
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">${input.files[0].name}</p>
                <button type="button" onclick="clearImage()" class="text-xs text-red-600 hover:text-red-700 mt-1">Hapus gambar</button>
            `;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Video preview function
function previewVideo(input) {
    const preview = document.getElementById('video_preview');
    
    if (input.files && input.files[0]) {
        preview.innerHTML = `
            <video controls class="max-w-full h-32 rounded-lg mx-auto">
                <source src="${URL.createObjectURL(input.files[0])}" type="${input.files[0].type}">
            </video>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">${input.files[0].name}</p>
            <button type="button" onclick="clearVideo()" class="text-xs text-red-600 hover:text-red-700 mt-1">Hapus video</button>
        `;
    }
}

// Clear image function
function clearImage() {
    document.getElementById('gambar').value = '';
    document.getElementById('gambar_preview').innerHTML = `
        <i data-lucide="image" class="w-8 h-8 text-gray-400 mx-auto"></i>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Klik untuk upload gambar atau drag & drop
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-500">
            Format: JPG, PNG, GIF. Maksimal 5MB
        </p>
    `;
    lucide.createIcons();
}

// Clear video function
function clearVideo() {
    document.getElementById('video').value = '';
    document.getElementById('video_preview').innerHTML = `
        <i data-lucide="video" class="w-8 h-8 text-gray-400 mx-auto"></i>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Klik untuk upload video atau drag & drop
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-500">
            Format: MP4, AVI, MOV. Maksimal 50MB
        </p>
    `;
    lucide.createIcons();
}
</script>
@endpush
