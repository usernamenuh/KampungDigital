@extends('layouts.app')

@section('title', 'Edit Berita')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('berita.index') }}" 
           class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Berita</h1>
            <p class="text-gray-600 dark:text-gray-400">Perbarui informasi berita</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
        <div class="flex items-center">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 mr-2"></i>
            <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <div class="flex items-center">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400 mr-2"></i>
            <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <div class="flex items-start">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 mt-0.5"></i>
            <div>
                <p class="text-red-800 dark:text-red-200 font-medium mb-2">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside text-red-700 dark:text-red-300 space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('berita.update', $berita) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="berita-form">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Informasi Berita</h2>
                    
                    <div class="space-y-6">
                        <!-- Judul -->
                        <div>
                            <label for="judul" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Judul Berita <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="judul" name="judul" value="{{ old('judul', $berita->judul) }}" required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('judul') border-red-500 @enderror"
                                   placeholder="Masukkan judul berita yang menarik">
                            @error('judul')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Konten -->
                        <div>
                            <label for="konten" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Konten Berita <span class="text-red-500">*</span>
                            </label>
                            <textarea id="konten" name="konten" rows="15" required
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('konten') border-red-500 @enderror"
                                      placeholder="Tulis konten berita di sini...">{{ old('konten', $berita->konten) }}</textarea>
                            @error('konten')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Link External -->
                        <div>
                            <label for="link" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Link External (Opsional)
                            </label>
                            <div class="relative">
                                <i data-lucide="external-link" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                <input type="url" id="link" name="link" value="{{ old('link', $berita->link) }}"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('link') border-red-500 @enderror"
                                       placeholder="https://example.com">
                            </div>
                            @error('link')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Link terkait berita (opsional)</p>
                        </div>

                        <!-- Tags -->
                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tags
                            </label>
                            <div class="relative">
                                <i data-lucide="tag" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                <input type="text" id="tags" name="tags" value="{{ old('tags', $berita->tags ? implode(', ', $berita->tags) : '') }}"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('tags') border-red-500 @enderror"
                                       placeholder="Pisahkan dengan koma (contoh: kesehatan, covid, vaksin)">
                            </div>
                            @error('tags')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Pisahkan setiap tag dengan koma</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Pengaturan Publikasi</h3>
                    
                    <div class="space-y-4">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select id="status" name="status" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                <option value="draft" {{ old('status', $berita->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $berita->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $berita->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="kategori" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select id="kategori" name="kategori" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('kategori') border-red-500 @enderror">
                                <option value="">Pilih Kategori</option>
                                <option value="umum" {{ old('kategori', $berita->kategori) == 'umum' ? 'selected' : '' }}>Umum</option>
                                <option value="pengumuman" {{ old('kategori', $berita->kategori) == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                                <option value="kegiatan" {{ old('kategori', $berita->kategori) == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                                <option value="pembangunan" {{ old('kategori', $berita->kategori) == 'pembangunan' ? 'selected' : '' }}>Pembangunan</option>
                                <option value="kesehatan" {{ old('kategori', $berita->kategori) == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                <option value="pendidikan" {{ old('kategori', $berita->kategori) == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                                <option value="ekonomi" {{ old('kategori', $berita->kategori) == 'ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                                <option value="sosial" {{ old('kategori', $berita->kategori) == 'sosial' ? 'selected' : '' }}>Sosial</option>
                                <option value="lingkungan" {{ old('kategori', $berita->kategori) == 'lingkungan' ? 'selected' : '' }}>Lingkungan</option>
                                <option value="keamanan" {{ old('kategori', $berita->kategori) == 'keamanan' ? 'selected' : '' }}>Keamanan</option>
                            </select>
                            @error('kategori')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pin Berita -->
                        <div class="flex items-center">
                            <input type="hidden" name="is_pinned" value="0">
                            <input type="checkbox" id="is_pinned" name="is_pinned" value="1" {{ old('is_pinned', $berita->is_pinned) ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="is_pinned" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Pin berita ini (tampil di atas)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Target Audience -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Target Pembaca</h3>
                    
                    <div class="space-y-4">
                        <!-- Tingkat Akses -->
                        <div>
                            <label for="tingkat_akses" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tingkat Akses <span class="text-red-500">*</span>
                            </label>
                            <select id="tingkat_akses" name="tingkat_akses" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('tingkat_akses') border-red-500 @enderror">
                                <option value="">Pilih Tingkat Akses</option>
                                @if(in_array(Auth::user()->role, ['admin', 'kades']))
                                <option value="desa" {{ old('tingkat_akses', $berita->tingkat_akses) == 'desa' ? 'selected' : '' }}>Seluruh Desa</option>
                                @endif
                                @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw']))
                                <option value="rw" {{ old('tingkat_akses', $berita->tingkat_akses) == 'rw' ? 'selected' : '' }}>Tingkat RW</option>
                                @endif
                                <option value="rt" {{ old('tingkat_akses', $berita->tingkat_akses) == 'rt' ? 'selected' : '' }}>Tingkat RT</option>
                            </select>
                            @error('tingkat_akses')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- RW Selection -->
                        <div id="rw_selection" class="hidden">
                            <label for="rw_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Pilih RW <span class="text-red-500">*</span>
                            </label>
                            <select id="rw_id" name="rw_id"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('rw_id') border-red-500 @enderror">
                                <option value="">Pilih RW</option>
                                @foreach($rws as $rw)
                                <option value="{{ $rw->id }}" {{ old('rw_id', $berita->rw_id) == $rw->id ? 'selected' : '' }}>
                                    RW {{ $rw->no_rw }} - {{ $rw->nama_rw }}
                                </option>
                                @endforeach
                            </select>
                            @error('rw_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- RT Selection -->
                        <div id="rt_selection" class="hidden">
                            <label for="rt_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Pilih RT <span class="text-red-500">*</span>
                            </label>
                            <select id="rt_id" name="rt_id"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('rt_id') border-red-500 @enderror">
                                <option value="">Pilih RT</option>
                                @foreach($rts as $rt)
                                <option value="{{ $rt->id }}" data-rw="{{ $rt->rw_id }}" {{ old('rt_id', $berita->rt_id) == $rt->id ? 'selected' : '' }}>
                                    RT {{ $rt->no_rt }} - RW {{ $rt->rw->no_rw }}
                                </option>
                                @endforeach
                            </select>
                            @error('rt_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="audience_info" class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <div class="flex items-start space-x-2">
                                <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0"></i>
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    Pilih tingkat akses untuk menentukan siapa yang dapat melihat berita ini.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Media -->
                @if($berita->gambar || $berita->video)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Media Saat Ini</h3>
                    
                    <div class="space-y-4">
                        @if($berita->video)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Video Saat Ini</label>
                            <video controls class="w-full h-48 rounded-lg">
                                <source src="{{ Storage::url($berita->video) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        @endif

                        @if($berita->gambar)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gambar Saat Ini</label>
                            <img src="{{ Storage::url($berita->gambar) }}" alt="{{ $berita->judul }}" 
                                 class="w-full h-48 object-cover rounded-lg">
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Media Upload -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Upload Media Baru</h3>
                    
                    <div class="space-y-4">
                        <!-- Image Upload -->
                        <div>
                            <label for="gambar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Gambar Baru
                            </label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-primary-500 dark:hover:border-primary-400 transition-colors duration-200">
                                <input type="file" id="gambar" name="gambar" accept="image/*" class="hidden">
                                <label for="gambar" class="cursor-pointer">
                                    <i data-lucide="image" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Klik untuk upload gambar baru atau drag & drop
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                        Format: JPG, PNG, GIF. Maksimal 5MB. Kosongkan jika tidak ingin mengubah.
                                    </p>
                                </label>
                            </div>
                            @error('gambar')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Image Preview -->
                        <div id="image_preview" class="hidden">
                            <img id="preview_img" src="/placeholder.svg?height=192&width=384" alt="Preview" class="w-full h-48 object-cover rounded-lg">
                        </div>

                        <!-- Video Upload -->
                        <div>
                            <label for="video" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Video Baru
                            </label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-primary-500 dark:hover:border-primary-400 transition-colors duration-200">
                                <input type="file" id="video" name="video" accept="video/*" class="hidden">
                                <label for="video" class="cursor-pointer">
                                    <i data-lucide="video" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Klik untuk upload video baru atau drag & drop
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                        Format: MP4, AVI, MOV, WMV, FLV. Maksimal 50MB. Kosongkan jika tidak ingin mengubah.
                                    </p>
                                </label>
                            </div>
                            @error('video')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Video Preview -->
                        <div id="video_preview" class="hidden">
                            <video id="preview_video" controls class="w-full h-48 rounded-lg">
                                <source src="/placeholder.svg?height=192&width=384" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>

                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex items-start space-x-2">
                                <i data-lucide="info" class="w-4 h-4 text-yellow-600 dark:text-yellow-400 mt-0.5 flex-shrink-0"></i>
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    Upload media baru akan menggantikan media yang sudah ada.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="space-y-3">
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            Update Berita
                        </button>
                        <a href="{{ route('berita.index') }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200">
                            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle tingkat akses change
    const tingkatAksesSelect = document.getElementById('tingkat_akses');
    const rwSelection = document.getElementById('rw_selection');
    const rtSelection = document.getElementById('rt_selection');
    const rwSelect = document.getElementById('rw_id');
    const rtSelect = document.getElementById('rt_id');
    const audienceInfo = document.getElementById('audience_info');

    function updateAudienceSelection() {
        const tingkat = tingkatAksesSelect.value;
        
        // Hide all selections first
        rwSelection.classList.add('hidden');
        rtSelection.classList.add('hidden');
        rwSelect.required = false;
        rtSelect.required = false;
        
        // Clear values when hiding
        if (tingkat !== 'rw') {
            rwSelect.value = '';
        }
        if (tingkat !== 'rt') {
            rtSelect.value = '';
        }
        
        const infoContent = audienceInfo.querySelector('p');
        
        switch(tingkat) {
            case 'desa':
                infoContent.innerHTML = '<i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0 inline mr-1"></i>Berita akan terlihat oleh seluruh warga desa.';
                break;
            case 'rw':
                rwSelection.classList.remove('hidden');
                rwSelect.required = true;
                infoContent.innerHTML = '<i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0 inline mr-1"></i>Berita akan terlihat oleh seluruh RT dalam RW yang dipilih.';
                break;
            case 'rt':
                rtSelection.classList.remove('hidden');
                rtSelect.required = true;
                infoContent.innerHTML = '<i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0 inline mr-1"></i>Berita akan terlihat oleh seluruh warga RT yang dipilih.';
                break;
            default:
                infoContent.innerHTML = '<i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0 inline mr-1"></i>Pilih tingkat akses untuk menentukan siapa yang dapat melihat berita ini.';
        }
        
        // Re-initialize lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    tingkatAksesSelect.addEventListener('change', updateAudienceSelection);

    // Trigger change on page load
    updateAudienceSelection();

    // Image preview
    const gambarInput = document.getElementById('gambar');
    const imagePreview = document.getElementById('image_preview');
    const previewImg = document.getElementById('preview_img');

    gambarInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.classList.add('hidden');
        }
    });

    // Video preview
    const videoInput = document.getElementById('video');
    const videoPreview = document.getElementById('video_preview');
    const previewVideo = document.getElementById('preview_video');

    videoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewVideo.querySelector('source').src = e.target.result;
                previewVideo.load();
                videoPreview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            videoPreview.classList.add('hidden');
        }
    });
});
</script>
@endpush
