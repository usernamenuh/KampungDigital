@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('penduduk.index') }}" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Penduduk</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $penduduk->nama_lengkap }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-center">
                        @if($penduduk->foto)
                            <img src="{{ asset('storage/' . $penduduk->foto) }}" alt="Foto {{ $penduduk->nama_lengkap }}" class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-gray-200 dark:border-gray-600 mb-4">
                        @else
                            <div class="w-32 h-32 bg-gray-300 dark:bg-gray-700 rounded-full mx-auto flex items-center justify-center border-4 border-gray-200 dark:border-gray-600 mb-4">
                                <svg class="w-16 h-16 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $penduduk->nama_lengkap }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $penduduk->nik }}</p>
                        
                        <div class="flex justify-center space-x-2 mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $penduduk->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300' }}">
                                {{ $penduduk->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $penduduk->status == 'aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                {{ ucfirst($penduduk->status) }}
                            </span>
                        </div>
                        
                        <div class="flex space-x-3">
                            <a href="{{ route('penduduk.edit', $penduduk) }}" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 text-center">
                                Edit Data
                            </a>
                            <button onclick="confirmDelete('{{ $penduduk->id }}', '{{ $penduduk->nama_lengkap }}')" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Lengkap</h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Data Pribadi -->
                            <div class="space-y-4">
                                <h4 class="font-semibold text-gray-900 dark:text-white border-b pb-2 border-gray-200 dark:border-gray-700">Data Pribadi</h4>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Tempat, Tanggal Lahir</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $penduduk->tempat_tanggal_lahir }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Umur</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $penduduk->umur }} tahun</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Agama</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $penduduk->agama }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Pendidikan</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $penduduk->pendidikan ?? '-' }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Pekerjaan</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $penduduk->pekerjaan ?? '-' }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status Perkawinan</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $penduduk->status_perkawinan }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Kewarganegaraan</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $penduduk->kewarganegaraan }}</p>
                                </div>
                            </div>
                            
                            <!-- Data Keluarga -->
                            <div class="space-y-4">
                                <h4 class="font-semibold text-gray-900 dark:text-white border-b pb-2 border-gray-200 dark:border-gray-700">Data Keluarga</h4>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">No. Kartu Keluarga</label>
                                    <p class="text-sm text-gray-900 dark:text-white font-mono">{{ $penduduk->kk->no_kk ?? '-' }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Alamat</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $penduduk->kk->alamat ?? '-' }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">RT/RW</label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        @if($penduduk->kk)
                                            RT {{ $penduduk->kk->rt->nama_rt ?? '-' }} / RW {{ $penduduk->kk->rt->rw->nama_rw ?? '-' }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Hubungan dalam Keluarga</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $penduduk->hubungan_keluarga == 'Kepala Keluarga' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                        {{ $penduduk->hubungan_keluarga }}
                                    </span>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nama Ayah</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $penduduk->nama_ayah ?? '-' }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nama Ibu</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $penduduk->nama_ibu ?? '-' }}</p>
                                </div>
                                
                                @if($penduduk->user)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Akun User</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $penduduk->user->name }} ({{ $penduduk->user->email }}) - Role: {{ ucfirst($penduduk->user->role) }}</p>
                                </div>
                                @endif

                                <!-- Jabatan Section -->
                                @php
                                    $jabatan = null;
                                    if ($penduduk->rtKetua) {
                                        $jabatan = 'Ketua RT ' . $penduduk->rtKetua->nama_rt . ' (RW ' . ($penduduk->rtKetua->rw->nama_rw ?? '-') . ')';
                                    } elseif ($penduduk->rwKetua) {
                                        $jabatan = 'Ketua RW ' . $penduduk->rwKetua->nama_rw . ' (Desa ' . ($penduduk->rwKetua->desa->nama_desa ?? '-') . ')';
                                    } elseif ($penduduk->kepalaDesa) {
                                        $jabatan = 'Kepala Desa ' . $penduduk->kepalaDesa->nama_desa;
                                    }
                                @endphp

                                @if($jabatan)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Jabatan</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $jabatan }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        @if($penduduk->keterangan)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Keterangan</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $penduduk->keterangan }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800 dark:border-gray-700">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                <svg class="h-6 w-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-4">Konfirmasi Hapus</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus data penduduk <span id="deleteName" class="font-semibold"></span>? 
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Hapus
                    </button>
                </form>
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteName').textContent = name;
    document.getElementById('deleteForm').action = `/penduduk/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>
@endsection
