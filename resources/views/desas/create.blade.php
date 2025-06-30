@extends('layouts.app')

@section('title', 'Tambah Data Desa')

@push('styles')
<style>
    .step-content {
        min-height: 400px;
    }

    .form-step {
        display: none;
    }

    .form-step.active {
        display: block;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .step-indicator {
        transition: all 0.3s ease;
    }

    .step-indicator.active {
        background-color: #8B5CF6;
        color: white;
        transform: scale(1.1);
    }

    .step-indicator.completed {
        background-color: #10B981;
        color: white;
    }

    .step-indicator.inactive {
        background-color: #e5e7eb;
        color: #6b7280;
    }

    .dark .step-indicator.inactive {
        background-color: #374151;
        color: #9ca3af;
    }

    .step-line {
        height: 2px;
        background: linear-gradient(to right, #10B981 0%, #10B981 var(--progress, 0%), #e5e7eb var(--progress, 0%), #e5e7eb 100%);
        transition: all 0.3s ease;
    }

    .dark .step-line {
        background: linear-gradient(to right, #10B981 0%, #10B981 var(--progress, 0%), #374151 var(--progress, 0%), #374151 100%);
    }

    .form-section {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.02) 0%, rgba(168, 85, 247, 0.02) 100%);
        border: 1px solid rgba(139, 92, 246, 0.08);
    }

    .dark .form-section {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.03) 0%, rgba(168, 85, 247, 0.03) 100%);
        border: 1px solid rgba(139, 92, 246, 0.12);
    }
</style>
@endpush

@section('content')
<div class="p-6 animate-fade-in" x-data="desaCreate()" x-init="init()">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center mr-4">
                        <i data-lucide="plus-circle" class="w-5 h-5 text-white"></i>
                    </div>
                    Tambah Data Desa
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Lengkapi informasi desa dalam beberapa langkah mudah</p>
            </div>
            <a href="{{ route('desas.index') }}"
               class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Kembali
            </a>
        </div>

        <!-- Step Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-8">
                <!-- Step 1 -->
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg mb-2"
                         :class="currentStep >= 1 ? (currentStep > 1 ? 'completed' : 'active') : 'inactive'">
                        <span x-show="currentStep === 1">1</span>
                        <i data-lucide="check" class="w-6 h-6" x-show="currentStep > 1"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Informasi Lokasi</span>
                </div>

                <!-- Line 1 -->
                <div class="flex-1 h-1 mx-4 step-line" :style="'--progress: ' + (currentStep > 1 ? '100%' : '0%')"></div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg mb-2"
                         :class="currentStep >= 2 ? (currentStep > 2 ? 'completed' : 'active') : 'inactive'">
                        <span x-show="currentStep === 2">2</span>
                        <i data-lucide="check" class="w-6 h-6" x-show="currentStep > 2"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Detail Informasi</span>
                </div>

                <!-- Line 2 -->
                <div class="flex-1 h-1 mx-4 step-line" :style="'--progress: ' + (currentStep > 2 ? '100%' : '0%')"></div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg mb-2"
                         :class="currentStep >= 3 ? 'completed' : 'inactive'">
                        <span x-show="currentStep < 3">3</span>
                        <i data-lucide="check" class="w-6 h-6" x-show="currentStep >= 3"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Selesai</span>
                </div>
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
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500"></div>
                    <span class="text-gray-600 dark:text-gray-400 font-medium">Memproses data<span class="loading-dots"></span></span>
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

            <form action="{{ route('desas.store') }}" method="POST" enctype="multipart/form-data" @submit="handleSubmit">
                @csrf

                <div class="step-content p-8">
                    <!-- Step 1: Location Information -->
                    <div class="form-step" :class="{ 'active': currentStep === 1 }">
                        <div class="space-y-6">
                            <div class="text-center mb-8">
                                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="map-pin" class="w-8 h-8 text-white"></i>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Informasi Lokasi</h2>
                                <p class="text-gray-600 dark:text-gray-400">Pilih lokasi desa yang akan didaftarkan</p>
                            </div>

                            <div class="form-section p-6 rounded-xl">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="space-y-6">
                                        <div>
                                            <label for="province" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                                <i data-lucide="flag" class="w-4 h-4 inline mr-2"></i>
                                                Provinsi
                                            </label>
                                            <select id="province" name="province_code"
                                                    class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 rounded-xl"
                                                    required @change="loadRegencies" x-model="formData.province_code">
                                                <option value="">-- Pilih Provinsi --</option>
                                                @foreach(\Vermaysha\Territory\Models\Province::all() as $prov)
                                                    <option value="{{ $prov->province_code }}" {{ old('province_code') == $prov->province_code ? 'selected' : '' }}>
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
                                                    class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 rounded-xl"
                                                    required @change="loadDistricts" x-model="formData.regency_code">
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
                                                    class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 rounded-xl"
                                                    required @change="loadVillages" x-model="formData.district_code">
                                                <option value="">-- Pilih Kecamatan --</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="village" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                                <i data-lucide="home" class="w-4 h-4 inline mr-2"></i>
                                                Desa
                                            </label>
                                            <select id="village" name="village_code"
                                                    class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 rounded-xl"
                                                    required x-model="formData.village_code">
                                                <option value="">-- Pilih Desa --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Detail Information -->
                    <div class="form-step" :class="{ 'active': currentStep === 2 }">
                        <div class="space-y-6">
                            <div class="text-center mb-8">
                                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="info" class="w-8 h-8 text-white"></i>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Detail Informasi</h2>
                                <p class="text-gray-600 dark:text-gray-400">Lengkapi informasi detail desa</p>
                            </div>

                            <div class="form-section p-6 rounded-xl">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="lg:col-span-2">
                                        <label for="alamat" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="map-pin" class="w-4 h-4 inline mr-2"></i>
                                            Alamat Lengkap
                                        </label>
                                        <textarea name="alamat" id="alamat" rows="4"
                                                  class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 resize-none rounded-xl"
                                                  required placeholder="Masukkan alamat lengkap desa...">{{ old('alamat') }}</textarea>
                                    </div>

                                    <div>
                                        <label for="kode_pos" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="mail" class="w-4 h-4 inline mr-2"></i>
                                            Kode Pos
                                        </label>
                                        <input type="number" name="kode_pos" id="kode_pos"
                                               class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 rounded-xl"
                                               required placeholder="12345" value="{{ old('kode_pos') }}">
                                    </div>

                                    <div>
                                        <label for="saldo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="dollar-sign" class="w-4 h-4 inline mr-2"></i>
                                            Saldo (Rp)
                                        </label>
                                        <input type="number" name="saldo" id="saldo"
                                               class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 rounded-xl"
                                               required placeholder="0" value="{{ old('saldo') }}">
                                    </div>

                                    <div>
                                        <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="toggle-left" class="w-4 h-4 inline mr-2"></i>
                                            Status
                                        </label>
                                        <select name="status" id="status"
                                                class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 rounded-xl"
                                                required>
                                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="tidak_aktif" {{ old('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                        </select>
                                    </div>

                                    <div class="lg:col-span-2">
                                        <label for="foto" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i data-lucide="camera" class="w-4 h-4 inline mr-2"></i>
                                            Foto Desa (Opsional)
                                        </label>
                                        <div class="relative">
                                            <input type="file" name="foto" id="foto"
                                                   class="w-full px-4 py-3 border-0 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 rounded-xl"
                                                   accept="image/*" @change="previewImage">
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 flex items-center">
                                            <i data-lucide="info" class="w-4 h-4 mr-1"></i>
                                            Format yang didukung: JPG, PNG, GIF. Maksimal 2MB.
                                        </p>
                                        <div x-show="imagePreview" class="mt-4 text-center" style="display: none;">
                                            <img :src="imagePreview" alt="Preview" class="max-w-xs h-auto rounded-xl border border-gray-200 dark:border-gray-600 shadow-lg mx-auto">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Success -->
                    <div class="form-step" :class="{ 'active': currentStep === 3 }">
                        <div class="text-center space-y-6">
                            <div class="w-24 h-24 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="check" class="w-12 h-12 text-green-600 dark:text-green-400"></i>
                            </div>

                            <div class="space-y-4">
                                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Data Berhasil Disimpan!</h2>
                                <p class="text-lg text-gray-600 dark:text-gray-400">
                                    Data desa telah berhasil ditambahkan ke sistem.
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Anda akan diarahkan kembali ke halaman utama dalam <span x-text="redirectCountdown"></span> detik...
                                </p>
                            </div>

                            <div class="flex justify-center space-x-4">
                                <a href="{{ route('desas.index') }}"
                                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <i data-lucide="list" class="w-4 h-4 mr-2"></i>
                                    Lihat Semua Data
                                </a>
                                <a href="{{ route('desas.create') }}"
                                   class="inline-flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                    Tambah Lagi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="px-8 py-6 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 flex justify-between" x-show="currentStep < 3">
                    <button type="button" @click="previousStep()"
                            x-show="currentStep > 1"
                            class="inline-flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                        Sebelumnya
                    </button>

                    <div class="flex-1"></div>

                    <button type="button" @click="nextStep()"
                            x-show="currentStep === 1"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="!canProceedToStep2()">
                        Selanjutnya
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                    </button>

                    <button type="submit"
                            x-show="currentStep === 2"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl"
                            :disabled="isLoading">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        <span x-text="isLoading ? 'Menyimpan...' : 'Simpan Data'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function desaCreate() {
    return {
        currentStep: 1,
        isLoading: false,
        imagePreview: null,
        redirectCountdown: 5,
        formData: {
            province_code: '',
            regency_code: '',
            district_code: '',
            village_code: ''
        },

        init() {
            // Initialize icons immediately
            this.initializeIcons();
            
            // Re-initialize icons after a short delay to ensure DOM is ready
            setTimeout(() => {
                this.initializeIcons();
            }, 500);

            // Watch for form changes
            this.$watch('formData', () => {
                console.log('Form data changed:', this.formData);
            });
        },

        initializeIcons() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        },

        nextStep() {
            console.log('Next step clicked, can proceed:', this.canProceedToStep2());
            if (this.currentStep < 3 && this.canProceedToStep2()) {
                this.currentStep++;
                this.scrollToTop();
                // Re-initialize icons after step change
                setTimeout(() => {
                    this.initializeIcons();
                }, 100);
            } else {
                console.log('Cannot proceed to next step');
                if (!this.canProceedToStep2()) {
                    window.showNotification('Harap lengkapi semua field lokasi terlebih dahulu', 'warning');
                }
            }
        },

        previousStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                this.scrollToTop();
                // Re-initialize icons after step change
                setTimeout(() => {
                    this.initializeIcons();
                }, 100);
            }
        },

        scrollToTop() {
            document.querySelector('.step-content').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        },

        canProceedToStep2() {
            const province = this.formData.province_code || document.getElementById('province')?.value || '';
            const regency = this.formData.regency_code || document.getElementById('regency')?.value || '';
            const district = this.formData.district_code || document.getElementById('district')?.value || '';
            const village = this.formData.village_code || document.getElementById('village')?.value || '';

            console.log('Validation check:', { province, regency, district, village });
            
            return province && regency && district && village;
        },

        async loadRegencies() {
            const provinceSelect = document.getElementById('province');
            this.formData.province_code = provinceSelect.value;
            
            const regencySelect = document.getElementById('regency');
            const districtSelect = document.getElementById('district');
            const villageSelect = document.getElementById('village');

            // Reset dependent dropdowns
            this.formData.regency_code = '';
            this.formData.district_code = '';
            this.formData.village_code = '';

            regencySelect.innerHTML = '<option value="">Memuat...</option>';
            districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            villageSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';

            if (this.formData.province_code) {
                try {
                    const response = await axios.get(`/api/regencies/${this.formData.province_code}`);
                    let options = '<option value="">-- Pilih Kabupaten/Kota --</option>';

                    if (response.data.success && response.data.data.length > 0) {
                        response.data.data.forEach(item => {
                            options += `<option value="${item.regency_code}">${item.regency_name}</option>`;
                        });
                    } else {
                        options += '<option value="">Data tidak ditemukan</option>';
                    }

                    regencySelect.innerHTML = options;
                } catch (error) {
                    console.error('Error loading regencies:', error);
                    regencySelect.innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
                    window.showNotification('Gagal mengambil data kabupaten/kota', 'error');
                }
            }
        },

        async loadDistricts() {
            const regencySelect = document.getElementById('regency');
            this.formData.regency_code = regencySelect.value;

            const districtSelect = document.getElementById('district');
            const villageSelect = document.getElementById('village');

            // Reset dependent dropdowns
            this.formData.district_code = '';
            this.formData.village_code = '';

            districtSelect.innerHTML = '<option value="">Memuat...</option>';
            villageSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';

            if (this.formData.province_code && this.formData.regency_code) {
                try {
                    const response = await axios.get(`/api/districts/${this.formData.province_code}/${this.formData.regency_code}`);
                    let options = '<option value="">-- Pilih Kecamatan --</option>';

                    if (response.data.success && response.data.data.length > 0) {
                        response.data.data.forEach(item => {
                            options += `<option value="${item.district_code}">${item.district_name}</option>`;
                        });
                    } else {
                        options += '<option value="">Data tidak ditemukan</option>';
                    }

                    districtSelect.innerHTML = options;
                } catch (error) {
                    console.error('Error loading districts:', error);
                    districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                    window.showNotification('Gagal mengambil data kecamatan', 'error');
                }
            }
        },

        async loadVillages() {
            const districtSelect = document.getElementById('district');
            this.formData.district_code = districtSelect.value;

            const villageSelect = document.getElementById('village');

            // Reset dependent dropdown
            this.formData.village_code = '';
            
            villageSelect.innerHTML = '<option value="">Memuat...</option>';

            if (this.formData.province_code && this.formData.regency_code && this.formData.district_code) {
                try {
                    const response = await axios.get(`/api/villages/${this.formData.province_code}/${this.formData.regency_code}/${this.formData.district_code}`);
                    let options = '<option value="">-- Pilih Desa --</option>';

                    if (response.data.success && response.data.data.length > 0) {
                        response.data.data.forEach(item => {
                            options += `<option value="${item.village_code}">${item.village_name}</option>`;
                        });
                    } else {
                        options += '<option value="">Data tidak ditemukan</option>';
                    }

                    villageSelect.innerHTML = options;
                } catch (error) {
                    console.error('Error loading villages:', error);
                    villageSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
                    window.showNotification('Gagal mengambil data desa', 'error');
                }
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
