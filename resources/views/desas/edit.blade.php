@extends('layouts.app')

@section('title', 'Edit Desa - ' . ($desa->village->village_name ?? 'Desa'))

@push('styles')
<style>
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-section {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.02) 0%, rgba(251, 146, 60, 0.02) 100%);
        border: 1px solid rgba(245, 158, 11, 0.08);
    }

    .dark .form-section {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.03) 0%, rgba(251, 146, 60, 0.03) 100%);
        border: 1px solid rgba(245, 158, 11, 0.12);
    }
</style>
@endpush

@section('content')
<div class="p-6 animate-fade-in" x-data="desaEdit()" x-init="init()">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="space-y-2">
                <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                    <a href="{{ route('desas.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-200">Data Desa</a>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    <span>Edit Desa</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-yellow-500 to-orange-500 flex items-center justify-center mr-4">
                        <i data-lucide="edit" class="w-5 h-5 text-white"></i>
                    </div>
                    Edit {{ $desa->village->village_name ?? 'Desa' }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Perbarui informasi desa</p>
            </div>
            
            <div class="flex space-x-3">
                <a href="{{ route('desas.show', $desa->id) }}" 
                   class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                    Lihat Detail
                </a>
                <a href="{{ route('desas.index') }}" 
                   class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form Container -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Loading Overlay -->
            <div x-show="isLoading"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-90 backdrop-blur-sm flex items-center justify-center z-20"
                 style="display: none;">
                <div class="flex flex-col items-center space-y-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-yellow-500"></div>
                    <span class="text-gray-600 dark:text-gray-400 font-medium">Memproses perubahan<span class="loading-dots"></span></span>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-red-50 dark:bg-red-900/10">
                    <div class="bg-red-100 dark:bg-red-900/20 border-l-4 border-red-400 text-red-800 dark:text-red-200 px-4 py-3 rounded-r-lg">
                        <div class="flex items-center mb-2">
                            <i data-lucide="alert-triangle" class="w-5 h-5 mr-2"></i>
                            <span class="font-medium">Terdapat kesalahan pada form:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('desas.update', $desa->id) }}" method="POST" enctype="multipart/form-data" @submit="handleSubmit">
                @csrf
                @method('PUT')

                <div class="p-8 space-y-8">
                    <!-- Location Information -->
                    <div class="form-section p-6 rounded-xl">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                                <i data-lucide="map-pin" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Informasi Lokasi</h2>
                                <p class="text-gray-600 dark:text-gray-400">Lokasi administratif desa</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-6">
                                <div>
                                    <label for="province" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i data-lucide="flag" class="w-4 h-4 inline mr-2"></i>
                                        Provinsi
                                    </label>
                                    <select id="province" name="province_code"
                                            class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                            required @change="loadRegencies">
                                        <option value="">-- Pilih Provinsi --</option>
                                        @foreach(\Vermaysha\Territory\Models\Province::all() as $prov)
                                            <option value="{{ $prov->province_code }}" {{ $desa->province_code == $prov->province_code ? 'selected' : '' }}>
                                                {{ $prov->province_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="regency" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i data-lucide="building" class="w-4 h-4 inline mr-2"></i>
                                        Kabupaten/Kota
                                    </label>
                                    <select id="regency" name="regency_code"
                                            class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                            required @change="loadDistricts">
                                        <option value="">-- Pilih Kabupaten/Kota --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label for="district" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i data-lucide="map" class="w-4 h-4 inline mr-2"></i>
                                        Kecamatan
                                    </label>
                                    <select id="district" name="district_code"
                                            class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                            required @change="loadVillages">
                                        <option value="">-- Pilih Kecamatan --</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="village" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i data-lucide="home" class="w-4 h-4 inline mr-2"></i>
                                        Desa
                                    </label>
                                    <select id="village" name="village_code"
                                            class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                            required>
                                        <option value="">-- Pilih Desa --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Information -->
                    <div class="form-section p-6 rounded-xl">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                                <i data-lucide="info" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Detail Informasi</h2>
                                <p class="text-gray-600 dark:text-gray-400">Informasi detail desa</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="lg:col-span-2">
                                <label for="alamat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                    <i data-lucide="map-pin" class="w-4 h-4 inline mr-2"></i>
                                    Alamat Lengkap
                                </label>
                                <textarea name="alamat" id="alamat" rows="4"
                                          class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200 resize-none"
                                          required placeholder="Masukkan alamat lengkap desa...">{{ old('alamat', $desa->alamat) }}</textarea>
                            </div>

                            <div>
                                <label for="kode_pos" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                    <i data-lucide="mail" class="w-4 h-4 inline mr-2"></i>
                                    Kode Pos
                                </label>
                                <input type="number" name="kode_pos" id="kode_pos"
                                       class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                       required placeholder="12345" value="{{ old('kode_pos', $desa->kode_pos) }}">
                            </div>

                            <div>
                                <label for="saldo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                    <i data-lucide="dollar-sign" class="w-4 h-4 inline mr-2"></i>
                                    Saldo (Rp)
                                </label>
                                <input type="number" name="saldo" id="saldo"
                                       class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                       required placeholder="0" value="{{ old('saldo', $desa->saldo) }}">
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                    <i data-lucide="toggle-left" class="w-4 h-4 inline mr-2"></i>
                                    Status
                                </label>
                                <select name="status" id="status"
                                        class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                        required>
                                    <option value="aktif" {{ old('status', $desa->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="tidak_aktif" {{ old('status', $desa->status) == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>

                            <div class="lg:col-span-2">
                                <label for="foto" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                    <i data-lucide="camera" class="w-4 h-4 inline mr-2"></i>
                                    Foto Desa (Opsional)
                                </label>
                                
                                @if($desa->foto)
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Foto saat ini:</p>
                                        <img src="{{ asset('storage/' . $desa->foto) }}" 
                                             alt="Current photo" 
                                             class="w-32 h-32 object-cover rounded-xl border border-gray-200 dark:border-gray-600"
                                             onerror="this.onerror=null; this.src='{{ asset('images/placeholder-village.jpg') }}'; this.alt='Foto tidak tersedia';">
                                    </div>
                                @endif
                                
                                <div class="relative">
                                    <input type="file" name="foto" id="foto"
                                           class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-yellow-500 rounded-xl text-gray-900 dark:text-gray-100 focus:border-transparent transition-all duration-200"
                                           accept="image/*" @change="previewImage">
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 flex items-center">
                                    <i data-lucide="info" class="w-4 h-4 mr-1"></i>
                                    Format yang didukung: JPG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.
                                </p>
                                <div x-show="imagePreview" class="mt-4 text-center" style="display: none;">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Preview foto baru:</p>
                                    <img :src="imagePreview" alt="Preview" class="max-w-xs h-auto rounded-xl border border-gray-200 dark:border-gray-600 shadow-lg mx-auto">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="px-8 py-6 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 flex flex-col sm:flex-row justify-between space-y-3 sm:space-y-0">
                    <div class="flex space-x-3">
                        <a href="{{ route('desas.show', $desa->id) }}" 
                           class="inline-flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                            Lihat Detail
                        </a>
                        <a href="{{ route('desas.index') }}" 
                           class="inline-flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i data-lucide="list" class="w-4 h-4 mr-2"></i>
                            Lihat Semua
                        </a>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" onclick="history.back()" 
                                class="inline-flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl"
                                :disabled="isLoading">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            <span x-text="isLoading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function desaEdit() {
    return {
        isLoading: false,
        imagePreview: null,
        currentData: {
            province_code: '{{ $desa->province_code }}',
            regency_code: '{{ $desa->regency_code }}',
            district_code: '{{ $desa->district_code }}',
            village_code: '{{ $desa->village_code }}'
        },

        async init() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Load initial data
            await this.loadRegencies();
            await this.loadDistricts();
            await this.loadVillages();
        },

        async loadRegencies() {
            const provinceSelect = document.getElementById('province');
            const regencySelect = document.getElementById('regency');
            const districtSelect = document.getElementById('district');
            const villageSelect = document.getElementById('village');

            if (!provinceSelect.value) return;

            regencySelect.innerHTML = '<option value="">Memuat...</option>';
            districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            villageSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';

            try {
                const response = await axios.get(`/api/regencies/${provinceSelect.value}`);
                let options = '<option value="">-- Pilih Kabupaten/Kota --</option>';

                if (response.data.success && response.data.data.length > 0) {
                    response.data.data.forEach(item => {
                        const selected = item.regency_code === this.currentData.regency_code ? 'selected' : '';
                        options += `<option value="${item.regency_code}" ${selected}>${item.regency_name}</option>`;
                    });
                }

                regencySelect.innerHTML = options;
            } catch (error) {
                console.error('Error loading regencies:', error);
                regencySelect.innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
                window.showNotification('Gagal mengambil data kabupaten/kota', 'error');
            }
        },

        async loadDistricts() {
            const provinceSelect = document.getElementById('province');
            const regencySelect = document.getElementById('regency');
            const districtSelect = document.getElementById('district');
            const villageSelect = document.getElementById('village');

            if (!provinceSelect.value || !regencySelect.value) return;

            districtSelect.innerHTML = '<option value="">Memuat...</option>';
            villageSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';

            try {
                const response = await axios.get(`/api/districts/${provinceSelect.value}/${regencySelect.value}`);
                let options = '<option value="">-- Pilih Kecamatan --</option>';

                if (response.data.success && response.data.data.length > 0) {
                    response.data.data.forEach(item => {
                        const selected = item.district_code === this.currentData.district_code ? 'selected' : '';
                        options += `<option value="${item.district_code}" ${selected}>${item.district_name}</option>`;
                    });
                }

                districtSelect.innerHTML = options;
            } catch (error) {
                console.error('Error loading districts:', error);
                districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                window.showNotification('Gagal mengambil data kecamatan', 'error');
            }
        },

        async loadVillages() {
            const provinceSelect = document.getElementById('province');
            const regencySelect = document.getElementById('regency');
            const districtSelect = document.getElementById('district');
            const villageSelect = document.getElementById('village');

            if (!provinceSelect.value || !regencySelect.value || !districtSelect.value) return;

            villageSelect.innerHTML = '<option value="">Memuat...</option>';

            try {
                const response = await axios.get(`/api/villages/${provinceSelect.value}/${regencySelect.value}/${districtSelect.value}`);
                let options = '<option value="">-- Pilih Desa --</option>';

                if (response.data.success && response.data.data.length > 0) {
                    response.data.data.forEach(item => {
                        const selected = item.village_code === this.currentData.village_code ? 'selected' : '';
                        options += `<option value="${item.village_code}" ${selected}>${item.village_name}</option>`;
                    });
                }

                villageSelect.innerHTML = options;
            } catch (error) {
                console.error('Error loading villages:', error);
                villageSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
                window.showNotification('Gagal mengambil data desa', 'error');
            }
        },

        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.imagePreview = null;
            }
        },

        handleSubmit(event) {
            this.isLoading = true;
            // Let the form submit normally
        }
    }
}
</script>

@endsection
