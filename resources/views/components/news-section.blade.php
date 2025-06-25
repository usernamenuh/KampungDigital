@props([
    'title' => 'Berita Terkini',
    'subtitle' => 'Update terbaru seputar perkembangan Kampung Digital di seluruh Indonesia',
    'news' => []
])

<section id="news" class="news-section seamless-section-bg py-16 relative">
    <div class="container mx-auto px-4 relative z-10">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center px-3 py-1 bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800/50 rounded-full text-blue-600 dark:text-blue-400 text-sm font-medium mb-4">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                Latest Updates
            </div>
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900 dark:text-white">{{ $title }}</h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">{{ $subtitle }}</p>
        </div>

        <!-- News Grid -->
        <div class="news-grid max-w-6xl mx-auto">
            @foreach($news as $index => $article)
                <article class="news-card-smooth {{ ($index === 0 || $index === 3) ? 'featured' : '' }} bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden cursor-pointer group"
                         data-card-index="{{ $index }}">
                    <!-- Image Container -->
                    <div class="relative h-48 {{ ($index === 0 || $index === 3) ? 'md:h-56' : '' }} overflow-hidden">
                        @if($article['image'])
                            <img src="{{ $article['image'] }}"
                                 alt="{{ $article['title'] }}"
                                 class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/20 to-transparent group-hover:from-black/60 transition-all duration-300"></div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center group-hover:from-gray-50 group-hover:to-gray-150 dark:group-hover:from-gray-600 dark:group-hover:to-gray-700 transition-all duration-300">
                                <div class="text-4xl group-hover:scale-110 transition-transform duration-300">{{ $article['icon'] ?? '📰' }}</div>
                            </div>
                        @endif

                        <!-- Blurred Category Badge -->
                        <div class="absolute top-3 left-3 z-10">
                            <span class="category-glass-badge px-3 py-1.5 bg-{{ $article['category_color'] ?? 'blue' }}-500/30 text-white text-xs font-semibold rounded-full border border-white/40 shadow-lg transition-all duration-300">
                                {{ $article['category'] }}
                            </span>
                        </div>

                        <!-- Date Badge -->
                        <div class="absolute bottom-3 right-3 bg-black/80 text-white text-xs px-3 py-1.5 rounded-lg backdrop-blur-md border border-white/10 shadow-lg group-hover:bg-black/90 transition-all duration-300">
                            {{ $article['date'] }}
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 relative">
                        <!-- Meta Info -->
                        <div class="flex items-center justify-between mb-3 transform group-hover:translate-y-[-1px] transition-transform duration-300">
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors duration-300">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $article['read_time'] ?? '3 min read' }}
                            </div>
                            <div class="flex items-center group-hover:scale-105 transition-transform duration-300">
                                <div class="w-6 h-6 bg-gradient-to-r {{ $article['author_gradient'] ?? 'from-blue-500 to-purple-500' }} rounded-full flex items-center justify-center mr-2 shadow-md group-hover:shadow-lg transition-shadow duration-300">
                                    <span class="text-white text-xs font-bold">{{ $article['author_initials'] ?? 'KD' }}</span>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors duration-300">{{ $article['author'] ?? 'Admin' }}</span>
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-3 line-clamp-2 leading-tight group-hover:text-{{ $article['category_color'] ?? 'blue' }}-600 dark:group-hover:text-{{ $article['category_color'] ?? 'blue' }}-400 transition-all duration-300">
                            {{ $article['title'] }}
                        </h3>

                        <!-- Excerpt -->
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-3 leading-relaxed group-hover:text-gray-700 dark:group-hover:text-gray-200 transition-all duration-300">
                            {{ $article['excerpt'] }}
                        </p>

                        <!-- Read More Button -->
                        <div class="transform group-hover:translate-y-[-1px] transition-transform duration-300">
                            <a href="{{ $article['link'] ?? '#' }}"
                               class="inline-flex items-center text-{{ $article['category_color'] ?? 'blue' }}-600 dark:text-{{ $article['category_color'] ?? 'blue' }}-400 hover:text-{{ $article['category_color'] ?? 'blue' }}-700 dark:hover:text-{{ $article['category_color'] ?? 'blue' }}-300 font-semibold text-sm transition-all duration-300 group/link relative">
                                <span class="relative z-10">Baca Selengkapnya</span>
                                <svg class="w-4 h-4 ml-2 transition-all duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <!-- Animated underline -->
                                <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-{{ $article['category_color'] ?? 'blue' }}-600 dark:bg-{{ $article['category_color'] ?? 'blue' }}-400 group-hover:w-full transition-all duration-300"></div>
                            </a>
                        </div>
                    </div>

                    <!-- Subtle Glow Effect -->
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-{{ $article['category_color'] ?? 'blue' }}-500/0 via-transparent to-{{ $article['category_color'] ?? 'blue' }}-500/0 group-hover:from-{{ $article['category_color'] ?? 'blue' }}-500/5 group-hover:to-{{ $article['category_color'] ?? 'blue' }}-500/5 transition-all duration-500 pointer-events-none opacity-0 group-hover:opacity-100"></div>
                </article>
            @endforeach
        </div>

        <!-- Empty State -->
        @if(empty($news))
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-200 dark:border-gray-600">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum ada berita</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm">Berita akan ditampilkan di sini setelah ditambahkan.</p>
            </div>
        @endif

        <!-- View All Button -->
        @if(!empty($news))
            <div class="text-center mt-12">
                <a href="#all-news"
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 dark:from-blue-500 dark:to-blue-600 dark:hover:from-blue-600 dark:hover:to-blue-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-lg hover:shadow-2xl group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span class="relative z-10 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                        Lihat Semua Berita
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </span>
                </a>
            </div>
        @endif
    </div>
</section>

<style>
    /* FIXED: Seamless Section Background */
    .seamless-section-bg {
        background: #ffffff !important;
        transition: background-color 0.3s ease;
    }
    .dark .seamless-section-bg {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
    }

    /* News Section Background */
    .news-section {
        background: linear-gradient(180deg, #ffffff 0%, #ffffff 50%, #f8fafc 100%);
        position: relative;
        overflow: hidden;
    }

    .dark .news-section {
        background: linear-gradient(180deg, #0f172a 0%, #1e293b 30%, #334155 70%, #475569 100%);
    }

    /* Subtle Grid Pattern */
    .news-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image:
            linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
        background-size: 30px 30px;
        animation: gridMove 25s linear infinite;
        opacity: 0.8;
    }

    .dark .news-section::before {
        background-image:
            linear-gradient(rgba(96, 165, 250, 0.1) 1px, transparent 1px),
            linear-gradient(90deg, rgba(96, 165, 250, 0.1) 1px, transparent 1px);
        background-size: 40px 40px;
        opacity: 0.5;
    }

    @keyframes gridMove {
        0% { transform: translate(0, 0); }
        100% { transform: translate(30px, 30px); }
    }

    /* News Grid */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2rem;
    }

    @media (min-width: 768px) {
        .news-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .news-card-smooth.featured {
            grid-column: span 2;
        }
    }

    /* Smooth Card Effects - NO SHAKE/JITTER */
    .news-card-smooth {
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        transform-origin: center center;
        will-change: transform;
    }

    .news-card-smooth:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.12),
            0 0 0 1px rgba(255, 255, 255, 0.1),
            0 0 20px rgba(59, 130, 246, 0.08);
        border-color: rgba(59, 130, 246, 0.2);
    }

    .dark .news-card-smooth:hover {
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.3),
            0 0 0 1px rgba(255, 255, 255, 0.05),
            0 0 25px rgba(96, 165, 250, 0.15);
        border-color: rgba(96, 165, 250, 0.3);
    }

    /* Glass Category Badge with Blurred Colors */
    .category-glass-badge {
        backdrop-filter: blur(20px) saturate(120%);
        -webkit-backdrop-filter: blur(20px) saturate(120%);
        box-shadow: 
            0 8px 32px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.4),
            0 0 0 1px rgba(255, 255, 255, 0.2);
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        position: relative;
        overflow: hidden;
    }

    .news-card-smooth:hover .category-glass-badge {
        backdrop-filter: blur(25px) saturate(140%);
        -webkit-backdrop-filter: blur(25px) saturate(140%);
        box-shadow: 
            0 12px 40px rgba(0, 0, 0, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.5),
            0 0 0 1px rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }

    /* Different blur intensities for different categories - MORE TRANSPARENT */
    .category-glass-badge[class*="bg-blue"] {
        background-color: rgba(59, 130, 246, 0.25) !important;
        backdrop-filter: blur(22px) saturate(110%);
    }

    .category-glass-badge[class*="bg-green"] {
        background-color: rgba(34, 197, 94, 0.25) !important;
        backdrop-filter: blur(24px) saturate(115%);
    }

    .category-glass-badge[class*="bg-purple"] {
        background-color: rgba(147, 51, 234, 0.25) !important;
        backdrop-filter: blur(20px) saturate(105%);
    }

    .category-glass-badge[class*="bg-orange"] {
        background-color: rgba(249, 115, 22, 0.25) !important;
        backdrop-filter: blur(26px) saturate(120%);
    }

    .category-glass-badge[class*="bg-red"] {
        background-color: rgba(239, 68, 68, 0.25) !important;
        backdrop-filter: blur(23px) saturate(108%);
    }

    .category-glass-badge[class*="bg-cyan"] {
        background-color: rgba(6, 182, 212, 0.25) !important;
        backdrop-filter: blur(21px) saturate(112%);
    }

    .category-glass-badge[class*="bg-teal"] {
        background-color: rgba(20, 184, 166, 0.25) !important;
        backdrop-filter: blur(25px) saturate(118%);
    }

    .category-glass-badge[class*="bg-indigo"] {
        background-color: rgba(99, 102, 241, 0.25) !important;
        backdrop-filter: blur(24px) saturate(114%);
    }

    /* Line Clamp Utility */
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

    /* Fade In Animation */
    .fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .news-card-smooth:hover {
            transform: translateY(-6px) scale(1.01);
        }
        
        .news-grid {
            gap: 1.5rem;
            grid-template-columns: 1fr;
        }
    }

    /* Performance Optimization */
    .news-card-smooth * {
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple Mouse Tracking - NO JITTER
    const cards = document.querySelectorAll('.news-card-smooth');
    
    cards.forEach(card => {
        let isHovering = false;
        
        card.addEventListener('mouseenter', () => {
            isHovering = true;
        });
        
        card.addEventListener('mouseleave', () => {
            isHovering = false;
            // Reset to clean state
            card.style.transform = '';
        });

        // REMOVED: mousemove event to prevent jitter/shake
    });

    // Staggered Animation on Load
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';

        setTimeout(() => {
            card.classList.add('fade-in-up');
        }, index * 100);
    });

    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
            }
        });
    }, observerOptions);

    cards.forEach(card => {
        observer.observe(card);
    });

    // Smooth scroll for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>
