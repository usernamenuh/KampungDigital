@extends('layouts.app')

@section('title', $berita->judul)

@section('content')
<div class="max-w-4xl mx-auto p-6 space-y-6">
    <!-- Back Button -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('berita.index') }}" 
           class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Kembali ke Berita
        </a>
    </div>

    <!-- Article Header -->
    <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Featured Image/Video -->
        @if($berita->gambar || $berita->video)
        <div class="aspect-video bg-gray-100 dark:bg-gray-700">
            @if($berita->video)
            <video controls class="w-full h-full object-cover">
                <source src="{{ Storage::url($berita->video) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            @elseif($berita->gambar)
            <img src="{{ Storage::url($berita->gambar) }}" 
                 alt="{{ $berita->judul }}" 
                 class="w-full h-full object-cover">
            @endif
        </div>
        @endif

        <div class="p-8">
            <!-- Badges -->
            <div class="flex flex-wrap gap-2 mb-4">
                @if($berita->is_pinned)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                    <i data-lucide="pin" class="w-3 h-3 mr-1"></i>
                    Pinned
                </span>
                @endif
                
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $berita->getCategoryColorClass() }}">
                    {{ ucfirst($berita->kategori) }}
                </span>
                
                @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $berita->getStatusColorClass() }}">
                    {{ ucfirst($berita->status) }}
                </span>
                @endif
                
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                    {{ $berita->getTargetAudienceText() }}
                </span>
            </div>

            <!-- Title -->
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                {{ $berita->judul }}
            </h1>

            <!-- Meta Information -->
            <div class="flex flex-wrap items-center gap-6 text-sm text-gray-600 dark:text-gray-400 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/20 rounded-full flex items-center justify-center">
                        <span class="text-xs font-medium text-primary-600 dark:text-primary-400">
                            {{ substr($berita->user->name, 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $berita->user->name }}</p>
                        <p class="text-xs">{{ ucfirst($berita->user->role) }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-1">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    <span>{{ $berita->created_at->format('d M Y, H:i') }}</span>
                </div>
                
                <div class="flex items-center space-x-1">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                    <span>{{ number_format($berita->views ?? 0) }} views</span>
                </div>
                
                @if($berita->reading_time)
                <div class="flex items-center space-x-1">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span>{{ $berita->reading_time }}</span>
                </div>
                @endif
            </div>

            <!-- Excerpt -->
            @if($berita->excerpt)
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-6">
                <p class="text-gray-700 dark:text-gray-300 font-medium italic">
                    {{ $berita->excerpt }}
                </p>
            </div>
            @endif

            <!-- Content -->
            <div class="prose prose-lg max-w-none dark:prose-invert">
                {!! nl2br(e($berita->konten)) !!}
            </div>

            <!-- External Link -->
            @if($berita->link)
            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-sm text-blue-800 dark:text-blue-400 mb-2">Link terkait:</p>
                <a href="{{ $berita->link }}" target="_blank" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                    {{ $berita->link }}
                    <i data-lucide="external-link" class="w-4 h-4 ml-1"></i>
                </a>
            </div>
            @endif

            <!-- Tags -->
            @if($berita->tags && is_array($berita->tags) && count($berita->tags) > 0)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Tags:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($berita->tags as $tag)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                        #{{ $tag }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            @if(in_array(Auth::user()->role, ['admin', 'kades', 'rw', 'rt']))
            @php
                $canEdit = $berita->canBeEditedBy(Auth::user());
                $canDelete = $berita->canBeDeletedBy(Auth::user());
            @endphp
            
            @if($canEdit || $canDelete)
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        @if($canEdit)
                        <a href="{{ route('berita.edit', $berita) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                            Edit Berita
                        </a>
                        
                        <form action="{{ route('berita.togglePin', $berita) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <i data-lucide="{{ $berita->is_pinned ? 'pin-off' : 'pin' }}" class="w-4 h-4 mr-2"></i>
                                {{ $berita->is_pinned ? 'Unpin' : 'Pin' }} Berita
                            </button>
                        </form>
                        @endif
                    </div>
                    
                    @if($canDelete)
                    <form action="{{ route('berita.destroy', $berita) }}" method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?')"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                            Hapus Berita
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif
            @endif
        </div>
    </article>
</div>
@endsection

@push('styles')
<style>
.prose {
    color: inherit;
}
.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    color: inherit;
}
.prose strong {
    color: inherit;
}
.prose a {
    color: rgb(37 99 235);
    text-decoration: underline;
}
.prose a:hover {
    color: rgb(29 78 216);
}
.dark .prose a {
    color: rgb(96 165 250);
}
.dark .prose a:hover {
    color: rgb(147 197 253);
}
</style>
@endpush
