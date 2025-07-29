@extends('layouts.app')

@section('title', 'Berita')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Berita</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola dan lihat berita terbaru</p>
        </div>
        
        @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
        <div class="flex items-center space-x-3">
            <a href="{{ route('berita.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Buat Berita
            </a>
        </div>
        @endif
    </div>

    <!-- Statistics (for authorized users) -->
    @if(!empty($stats))
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-xl">
                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Berita</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-xl">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Published</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['published']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-xl">
                    <i data-lucide="edit" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Draft</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['draft']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-xl">
                    <i data-lucide="eye" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Views</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_views']) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pencarian</label>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari berita..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                <select name="kategori" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Kategori</option>
                    <option value="umum" {{ request('kategori') == 'umum' ? 'selected' : '' }}>Umum</option>
                    <option value="pengumuman" {{ request('kategori') == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                    <option value="kegiatan" {{ request('kategori') == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                    <option value="pembangunan" {{ request('kategori') == 'pembangunan' ? 'selected' : '' }}>Pembangunan</option>
                    <option value="kesehatan" {{ request('kategori') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                    <option value="pendidikan" {{ request('kategori') == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                    <option value="ekonomi" {{ request('kategori') == 'ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                    <option value="sosial" {{ request('kategori') == 'sosial' ? 'selected' : '' }}>Sosial</option>
                    <option value="lingkungan" {{ request('kategori') == 'lingkungan' ? 'selected' : '' }}>Lingkungan</option>
                    <option value="keamanan" {{ request('kategori') == 'keamanan' ? 'selected' : '' }}>Keamanan</option>
                </select>
            </div>
            
            @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tingkat Akses</label>
                <select name="tingkat_akses" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Tingkat</option>
                    <option value="desa" {{ request('tingkat_akses') == 'desa' ? 'selected' : '' }}>Desa</option>
                    <option value="rw" {{ request('tingkat_akses') == 'rw' ? 'selected' : '' }}>RW</option>
                    <option value="rt" {{ request('tingkat_akses') == 'rt' ? 'selected' : '' }}>RT</option>
                </select>
            </div>
            @endif
            
            <div class="flex items-end">
                <button type="submit" 
                        class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                    <i data-lucide="search" class="w-4 h-4 mr-2 inline"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- News Cards Grid -->
    @if($berita->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($berita as $item)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:border-primary-200 dark:hover:border-primary-700 transition-all duration-200 group">
            <!-- Card Header with Image/Video -->
            <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600">
                @if($item->gambar)
                <img src="{{ Storage::url($item->gambar) }}" 
                     alt="{{ $item->judul }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @elseif($item->video)
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700">
                    <div class="text-center">
                        <i data-lucide="play-circle" class="w-16 h-16 text-gray-400 mx-auto mb-2"></i>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Video</p>
                    </div>
                </div>
                @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20">
                    <div class="text-center">
                        <i data-lucide="file-text" class="w-16 h-16 text-primary-400 mx-auto mb-2"></i>
                        <p class="text-sm text-primary-500 dark:text-primary-400">Artikel</p>
                    </div>
                </div>
                @endif
                
                <!-- Overlay Badges -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                
                <!-- Top Badges -->
                <div class="absolute top-3 left-3 right-3 flex justify-between items-start">
                    <div class="flex flex-wrap gap-2">
                        @if($item->is_pinned)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-500 text-white shadow-sm backdrop-blur-sm">
                            <i data-lucide="pin" class="w-3 h-3 mr-1"></i>
                            Pinned
                        </span>
                        @endif
                    </div>
                    
                    @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
                    <div>
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-500/90',
                                'published' => 'bg-green-500/90',
                                'archived' => 'bg-yellow-500/90'
                            ];
                            $statusColor = $statusColors[$item->status] ?? 'bg-gray-500/90';
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }} text-white shadow-sm backdrop-blur-sm">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Card Content -->
            <div class="p-6">
                <!-- Category and Access Level Badges -->
                <div class="flex flex-wrap gap-2 mb-3">
                    @php
                        $categoryColors = [
                            'umum' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            'pengumuman' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                            'kegiatan' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'pembangunan' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                            'kesehatan' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'pendidikan' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                            'ekonomi' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                            'sosial' => 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400',
                            'lingkungan' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                            'keamanan' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400'
                        ];
                        $categoryColor = $categoryColors[$item->kategori] ?? $categoryColors['umum'];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $categoryColor }}">
                        {{ ucfirst($item->kategori) }}
                    </span>
                    
                    @php
                        $accessColors = [
                            'desa' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'rw' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                            'rt' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'
                        ];
                        $accessColor = $accessColors[$item->tingkat_akses] ?? $accessColors['desa'];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $accessColor }}">
                        {{ strtoupper($item->tingkat_akses) }}
                    </span>
                </div>
                
                <!-- Title -->
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 line-clamp-2 leading-tight group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors duration-200">
                    <a href="{{ route('berita.show', $item) }}" class="block">
                        {{ $item->judul }}
                    </a>
                </h3>
                
                <!-- Excerpt -->
                <div class="mb-4">
                    @if($item->excerpt)
                    <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 leading-relaxed">
                        {{ $item->excerpt }}
                    </p>
                    @else
                    <p class="text-gray-500 dark:text-gray-500 text-sm italic">
                        Tidak ada ringkasan tersedia
                    </p>
                    @endif
                </div>
                
                <!-- Meta Info -->
                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center space-x-1">
                        <i data-lucide="user" class="w-3 h-3"></i>
                        <span class="truncate max-w-20">{{ $item->user->name }}</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <i data-lucide="calendar" class="w-3 h-3"></i>
                        <span>{{ $item->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <i data-lucide="eye" class="w-3 h-3"></i>
                        <span>{{ number_format($item->views ?? 0) }}</span>
                    </div>
                </div>
                
                <!-- Location Info -->
                @if($item->rt || $item->rw)
                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mb-4">
                    @if($item->rt)
                    <div class="flex items-center space-x-1">
                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                        <span>RT {{ $item->rt->no_rt }}</span>
                    </div>
                    @endif
                    @if($item->rw)
                    <div class="flex items-center space-x-1">
                        <i data-lucide="map" class="w-3 h-3"></i>
                        <span>RW {{ $item->rw->no_rw }}</span>
                    </div>
                    @endif
                </div>
                @endif
                
                <!-- Actions -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('berita.show', $item) }}" 
                       class="inline-flex items-center text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium transition-colors duration-200 group">
                        Baca Selengkapnya
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform duration-200"></i>
                    </a>
                    
                    @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
                    <div class="flex items-center space-x-1">
                        @php
                            $canEdit = (Auth::user()->role === 'admin') || 
                                      ($item->user_id === Auth::id()) || 
                                      (Auth::user()->role === 'kades');
                            $canDelete = (Auth::user()->role === 'admin') || 
                                        ($item->user_id === Auth::id()) || 
                                        (Auth::user()->role === 'kades');
                        @endphp
                        
                        @if($canEdit)
                        <a href="{{ route('berita.edit', $item) }}" 
                           class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200"
                           title="Edit">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </a>
                        
                        <form action="{{ route('berita.togglePin', $item) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="p-2 text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 rounded-lg hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors duration-200"
                                    title="{{ $item->is_pinned ? 'Unpin' : 'Pin' }}">
                                <i data-lucide="{{ $item->is_pinned ? 'pin-off' : 'pin' }}" class="w-4 h-4"></i>
                            </button>
                        </form>
                        @endif
                        
                        @if($canDelete)
                        <form action="{{ route('berita.destroy', $item) }}" method="POST" class="inline"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200"
                                    title="Hapus">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
        <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="file-text" class="w-12 h-12 text-gray-400"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Belum ada berita</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">Belum ada berita yang tersedia saat ini. Mulai dengan membuat berita pertama Anda.</p>
        @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
        <a href="{{ route('berita.create') }}" 
           class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Buat Berita Pertama
        </a>
        @endif
    </div>
    @endif

    <!-- Pagination -->
    @if($berita->hasPages())
    <div class="flex justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-2">
            {{ $berita->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar for better aesthetics */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.dark ::-webkit-scrollbar-track {
    background: #1e293b;
}

.dark ::-webkit-scrollbar-thumb {
    background: #475569;
}

.dark ::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}
</style>
@endpush
