@extends('layouts.app')

@section('title', 'Ajukan Proposal Bantuan')

@section('content')
<div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i data-lucide="heart-handshake" class="w-8 h-8 text-primary-500 mr-3"></i>
                    Ajukan Proposal Bantuan
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Lengkapi form berikut untuk mengajukan proposal bantuan kepada Kepala Desa
                </p>
            </div>
            <a href="{{ route('bantuan-proposals.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white">
                        <h2 class="text-xl font-semibold flex items-center">
                            <i data-lucide="file-text" class="w-5 h-5 mr-2"></i>
                            Informasi Proposal
                        </h2>
                        <p class="text-primary-100 text-sm mt-1">Isi semua informasi dengan lengkap dan jelas</p>
                    </div>
                    
                    <div class="p-6">
                        <form id="proposalForm" action="{{ route('bantuan-proposals.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Judul Proposal -->
                            <div class="mb-6">
                                <label for="judul_proposal" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                    <i data-lucide="heading" class="w-4 h-4 inline mr-1"></i>
                                    Judul Proposal <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="judul_proposal" 
                                       name="judul_proposal" 
                                       value="{{ old('judul_proposal') }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-white transition-colors duration-200 @error('judul_proposal') border-red-500 @enderror" 
                                       placeholder="Contoh: Bantuan Renovasi Balai RW"
                                       maxlength="255"
                                       required>
                                @error('judul_proposal')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                                    <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                                    Maksimal 255 karakter. Buatlah judul yang jelas dan spesifik.
                                </p>
                            </div>

                            <!-- Jumlah Bantuan -->
                            <div class="mb-6">
                                <label for="jumlah_bantuan" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                    <i data-lucide="banknote" class="w-4 h-4 inline mr-1"></i>
                                    Jumlah Dana yang Diminta <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 font-medium">Rp</span>
                                    </div>
                                    <input type="text" 
                                           id="jumlah_bantuan" 
                                           name="jumlah_bantuan" 
                                           value="{{ old('jumlah_bantuan') }}"
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-white transition-colors duration-200 @error('jumlah_bantuan') border-red-500 @enderror" 
                                           placeholder="0"
                                           required>
                                    <input type="hidden" id="jumlah_bantuan_raw" name="jumlah_bantuan_raw">
                                </div>
                                @error('jumlah_bantuan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                                    <i data-lucide="calculator" class="w-3 h-3 inline mr-1"></i>
                                    Masukkan jumlah dana yang dibutuhkan. Minimal Rp 1.000
                                </p>
                            </div>

                            <!-- Deskripsi Proposal -->
                            <div class="mb-6">
                                <label for="deskripsi" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                    <i data-lucide="align-left" class="w-4 h-4 inline mr-1"></i>
                                    Deskripsi Proposal <span class="text-red-500">*</span>
                                </label>
                                <textarea id="deskripsi" 
                                          name="deskripsi" 
                                          rows="6"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:text-white transition-colors duration-200 @error('deskripsi') border-red-500 @enderror" 
                                          placeholder="Jelaskan secara detail:&#10;1. Tujuan dan latar belakang proposal&#10;2. Manfaat untuk warga RW&#10;3. Rencana penggunaan dana&#10;4. Timeline pelaksanaan"
                                          minlength="50"
                                          required>{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <div class="flex justify-between text-sm mt-1">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        <i data-lucide="pen-tool" class="w-3 h-3 inline mr-1"></i>
                                        Minimal 50 karakter. Jelaskan dengan detail untuk memperkuat proposal.
                                    </span>
                                    <span id="charCount" class="text-gray-500 dark:text-gray-400 font-medium">(0 karakter)</span>
                                </div>
                            </div>

                            <!-- File Proposal -->
                            <div class="mb-8">
                                <label for="file_proposal" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                    <i data-lucide="paperclip" class="w-4 h-4 inline mr-1"></i>
                                    File Proposal (Opsional)
                                </label>
                                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-primary-400 transition-colors duration-200">
                                    <input type="file" 
                                           id="file_proposal" 
                                           name="file_proposal" 
                                           accept=".pdf,.doc,.docx"
                                           class="hidden">
                                    <div id="fileDropZone" class="cursor-pointer">
                                        <i data-lucide="upload" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                                        <p class="text-gray-600 dark:text-gray-400 mb-1">
                                            Klik untuk memilih file atau drag & drop
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-500">
                                            PDF, DOC, DOCX (Maksimal 2MB)
                                        </p>
                                    </div>
                                    <div id="filePreview" class="hidden mt-4 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <i data-lucide="file" class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2"></i>
                                                <span id="fileName" class="text-blue-700 dark:text-blue-300 font-medium"></span>
                                            </div>
                                            <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-700">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @error('file_proposal')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex flex-col sm:flex-row justify-between space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('bantuan-proposals.index') }}" 
                                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                    Batal
                                </a>
                                <button type="submit" 
                                        id="submitBtn"
                                        class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                                    Ajukan Proposal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- RW Info Card -->
                <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i data-lucide="home" class="w-5 h-5 mr-2"></i>
                            Informasi RW
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="home" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $rw->nama_rw ?? 'RW ' . $rw->no_rw }}
                            </h4>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Saldo RW</p>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($rw->saldo ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Ketua RW</p>
                                <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                    {{ $rw->ketuaRw->nama ?? 'Tidak tersedia' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-yellow-500 to-orange-500 text-white">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i data-lucide="lightbulb" class="w-5 h-5 mr-2"></i>
                            Tips Proposal Sukses
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Jelaskan tujuan dengan spesifik</span>
                            </div>
                            <div class="flex items-start">
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Sertakan manfaat untuk warga</span>
                            </div>
                            <div class="flex items-start">
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Rincian penggunaan dana</span>
                            </div>
                            <div class="flex items-start">
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Lampirkan dokumen pendukung</span>
                            </div>
                            <div class="flex items-start">
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Timeline yang realistis</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Process Timeline -->
                <div class="card-default rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-600 to-gray-700 text-white">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i data-lucide="route" class="w-5 h-5 mr-2"></i>
                            Alur Proses
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">1</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Pengajuan</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">RW mengajukan proposal</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-sm font-bold text-yellow-600 dark:text-yellow-400">2</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Review</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Kepala Desa meninjau</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-sm font-bold text-green-600 dark:text-green-400">3</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Keputusan</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Disetujui/Ditolak</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('proposalForm');
    const submitBtn = document.getElementById('submitBtn');
    const deskripsiTextarea = document.getElementById('deskripsi');
    const charCount = document.getElementById('charCount');
    const jumlahBantuanInput = document.getElementById('jumlah_bantuan');
    const jumlahBantuanRaw = document.getElementById('jumlah_bantuan_raw');
    const fileInput = document.getElementById('file_proposal');
    const fileDropZone = document.getElementById('fileDropZone');

    // Currency formatting function
    function formatRupiah(angka) {
        const number_string = angka.replace(/[^,\d]/g, '').toString();
        const split = number_string.split(',');
        const sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            const separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    // Format currency input with dots
    jumlahBantuanInput.addEventListener('input', function() {
        let value = this.value.replace(/[^\d]/g, '');
        
        if (value) {
            jumlahBantuanRaw.value = value;
            this.value = formatRupiah(value);
        } else {
            jumlahBantuanRaw.value = '';
            this.value = '';
        }
    });

    // Character counter for description
    function updateCharCount() {
        const length = deskripsiTextarea.value.length;
        charCount.textContent = `(${length} karakter)`;
        
        if (length < 50) {
            charCount.classList.add('text-red-500');
            charCount.classList.remove('text-green-500');
        } else {
            charCount.classList.add('text-green-500');
            charCount.classList.remove('text-red-500');
        }
    }

    deskripsiTextarea.addEventListener('input', updateCharCount);
    updateCharCount();

    // File handling
    fileDropZone.addEventListener('click', () => fileInput.click());
    
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            validateAndPreviewFile(file);
        }
    });

    // Drag and drop
    fileDropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileDropZone.classList.add('border-primary-400', 'bg-primary-50');
    });

    fileDropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        fileDropZone.classList.remove('border-primary-400', 'bg-primary-50');
    });

    fileDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        fileDropZone.classList.remove('border-primary-400', 'bg-primary-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            validateAndPreviewFile(files[0]);
        }
    });

    function validateAndPreviewFile(file) {
        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (!allowedTypes.includes(file.type)) {
            window.showNotification('File harus berformat PDF, DOC, atau DOCX', 'error');
            fileInput.value = '';
            return;
        }

        if (file.size > maxSize) {
            window.showNotification('Ukuran file maksimal 2MB', 'error');
            fileInput.value = '';
            return;
        }

        // Show preview
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('filePreview').classList.remove('hidden');
    }

    window.removeFile = function() {
        fileInput.value = '';
        document.getElementById('filePreview').classList.add('hidden');
    };

    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const errors = [];
        
        // Validate fields
        const judul = document.getElementById('judul_proposal').value.trim();
        if (!judul) {
            errors.push('Judul proposal harus diisi');
        } else if (judul.length > 255) {
            errors.push('Judul proposal maksimal 255 karakter');
        }

        const deskripsi = deskripsiTextarea.value.trim();
        if (!deskripsi) {
            errors.push('Deskripsi proposal harus diisi');
        } else if (deskripsi.length < 50) {
            errors.push('Deskripsi proposal minimal 50 karakter');
        }

        const jumlahRaw = parseInt(jumlahBantuanRaw.value);
        if (!jumlahRaw || jumlahRaw < 1000) {
            errors.push('Jumlah bantuan minimal Rp 1.000');
        }

        if (errors.length > 0) {
            window.showNotification(errors.join(', '), 'error');
            return;
        }

        // Show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Mengirim...';
        
        // Set the raw value for submission
        jumlahBantuanInput.name = 'jumlah_bantuan';
        jumlahBantuanInput.value = jumlahRaw;
        
        // Submit form
        form.submit();
    });

    // Initialize icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Show messages
    @if(session('success'))
        window.showNotification('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        window.showNotification('{{ session('error') }}', 'error');
    @endif
});
</script>
@endsection
