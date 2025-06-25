@props([
    'title' => 'Membangun Ekosistem Digital Berkelanjutan',
    'subtitle' => 'Platform inovatif yang mengintegrasikan teknologi terdepan untuk menciptakan desa mandiri dan berdaya saing global',
    'primaryButton' => [
        'text' => 'Bergabung Sekarang',
        'href' => '#features'
    ],
    'secondaryButton' => [
        'text' => 'Lihat Demo',
        'href' => '#demo'
    ],
    'badge' => '🏆 Terpercaya oleh 1000+ Desa di Indonesia',
    'stats' => [
        ['number' => '1,500+', 'label' => 'Desa Mitra', 'icon' => '🏘️'],
        ['number' => '150K+', 'label' => 'Pengguna Aktif', 'icon' => '👥'],
        ['number' => '99.2%', 'label' => 'Uptime System', 'icon' => '⚡'],
        ['number' => '24/7', 'label' => 'Expert Support', 'icon' => '🛡️']
    ]
])

<section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden seamless-section-bg pt-20 transition-all duration-700">

    <!-- Seamless Grid Background -->
    <div class="absolute inset-0 seamless-grid opacity-30 dark:opacity-20"></div>

    <!-- Premium Mesh Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-purple-500/5 to-indigo-500/10 dark:from-blue-400/10 dark:via-purple-400/10 dark:to-indigo-400/15"></div>

    <!-- Floating Elements Ecosystem -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <!-- Premium Orbs -->
        <div class="premium-orb orb-1"></div>
        <div class="premium-orb orb-2"></div>
        <div class="premium-orb orb-3"></div>
        <div class="premium-orb orb-4"></div>
        <div class="premium-orb orb-5"></div>

        <!-- Connecting Lines -->
        <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="lineGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:rgba(59,130,246,0.3);stop-opacity:0" />
                    <stop offset="50%" style="stop-color:rgba(59,130,246,0.6);stop-opacity:1" />
                    <stop offset="100%" style="stop-color:rgba(59,130,246,0.3);stop-opacity:0" />
                </linearGradient>
            </defs>
            <path class="connection-line line-1" stroke="url(#lineGradient)" stroke-width="2" fill="none" />
            <path class="connection-line line-2" stroke="url(#lineGradient)" stroke-width="2" fill="none" />
            <path class="connection-line line-3" stroke="url(#lineGradient)" stroke-width="2" fill="none" />
        </svg>
    </div>

    <!-- Advanced Particle System -->
    <div id="advanced-particles" class="absolute inset-0 pointer-events-none"></div>

    <!-- Main Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <!-- Premium Trust Badge -->
        <div class="inline-flex items-center px-6 py-3 rounded-full bg-gradient-to-r from-white/90 to-blue-50/90 dark:from-gray-800/90 dark:to-blue-900/30 backdrop-blur-xl border border-blue-200/30 dark:border-blue-700/30 text-gray-700 dark:text-gray-200 text-sm font-medium mb-8 animate-fade-in-up hover:scale-105 transition-all duration-500 cursor-pointer premium-trust-badge shadow-xl shadow-blue-500/10 dark:shadow-blue-400/20">
            <span class="mr-3 text-blue-600 dark:text-blue-400 font-semibold">{{ $badge }}</span>
            <div class="flex space-x-1">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse delay-75"></div>
                <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse delay-150"></div>
            </div>
        </div>

        <!-- Hero Title with Advanced Typography -->
        <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-8 leading-tight animate-fade-in-up delay-200 tracking-tight">
            <span class="block text-gray-900 dark:text-white font-light mb-3 hero-text-shadow">
                Kampung Digital
            </span>
            <span class="block font-black bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 dark:from-blue-400 dark:via-indigo-400 dark:to-purple-400 bg-clip-text text-transparent animate-gradient-x hero-gradient-text">
                Akar Kuat, Bangsa Hebat
            </span>
        </h1>

        <!-- Enhanced Subtitle with Typing Animation -->
        <div class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 mb-12 max-w-4xl mx-auto leading-relaxed animate-fade-in-up delay-400 font-light">
            <p id="advanced-typewriter" class="advanced-typewriter min-h-[3rem]"></p>
        </div>

        <!-- Premium Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center animate-fade-in-up delay-600 mb-20">
            <a href="{{ is_array($primaryButton) ? $primaryButton['href'] : '#features' }}"
               class="group relative px-10 py-5 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 dark:from-blue-500 dark:via-indigo-500 dark:to-purple-500 text-white font-semibold rounded-2xl overflow-hidden transition-all duration-500 transform hover:scale-105 hover:shadow-2xl premium-primary-cta shadow-xl">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-700 dark:from-blue-600 dark:via-indigo-600 dark:to-purple-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                <span class="relative z-10 flex items-center gap-3 text-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    {{ is_array($primaryButton) ? $primaryButton['text'] : $primaryButton }}
                    <svg class="w-5 h-5 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </span>
                <div class="absolute inset-0 rounded-2xl bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </a>

            <a href="{{ is_array($secondaryButton) ? $secondaryButton['href'] : '#demo' }}"
               class="group relative px-10 py-5 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-2xl transition-all duration-500 transform hover:scale-105 hover:border-blue-400 dark:hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 hover:shadow-xl bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl premium-secondary-cta">
                <span class="flex items-center gap-3 text-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.5a2.5 2.5 0 000-5H9v5zm0 0H7.5a2.5 2.5 0 000 5H9v-5z"></path>
                    </svg>
                    {{ is_array($secondaryButton) ? $secondaryButton['text'] : $secondaryButton }}
                    <svg class="w-5 h-5 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </span>
            </a>
        </div>

        <!-- Partners & Achievement Showcase -->
        <div class="flex flex-col items-center space-y-8 animate-fade-in-up delay-800">
            <!-- Achievement Badge -->
            <div class="flex items-center space-x-4 bg-gradient-to-r from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 px-8 py-4 rounded-full border border-amber-200/50 dark:border-amber-700/50 backdrop-blur-xl">
                <div class="flex items-center space-x-2">
                    <span class="text-2xl">🏆</span>
                    <span class="text-amber-700 dark:text-amber-300 font-semibold">Penghargaan Terbaik 2024</span>
                </div>
                <div class="w-px h-6 bg-amber-300 dark:bg-amber-600"></div>
                <div class="flex items-center space-x-2">
                    <span class="text-2xl">🇮🇩</span>
                    <span class="text-amber-700 dark:text-amber-300 font-semibold">Kementerian Desa PDTT</span>
                </div>
            </div>

            <!-- Partner Logos -->
            <div class="flex flex-wrap justify-center items-center gap-8 opacity-60 hover:opacity-80 transition-opacity duration-500">
                <div class="flex items-center space-x-2 px-4 py-2 bg-white/50 dark:bg-gray-800/50 rounded-lg backdrop-blur-sm">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-bold">KD</span>
                    </div>
                    <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">Kemendesa</span>
                </div>

                <div class="flex items-center space-x-2 px-4 py-2 bg-white/50 dark:bg-gray-800/50 rounded-lg backdrop-blur-sm">
                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-bold">BI</span>
                    </div>
                    <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">Bank Indonesia</span>
                </div>

                <div class="flex items-center space-x-2 px-4 py-2 bg-white/50 dark:bg-gray-800/50 rounded-lg backdrop-blur-sm">
                    <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-bold">TI</span>
                    </div>
                    <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">Telkom Indonesia</span>
                </div>

                <div class="flex items-center space-x-2 px-4 py-2 bg-white/50 dark:bg-gray-800/50 rounded-lg backdrop-blur-sm">
                    <div class="w-8 h-8 bg-gradient-to-r from-red-500 to-orange-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-bold">GO</span>
                    </div>
                    <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">Go-Jek</span>
                </div>
            </div>

            <!-- Call to Action with Social Proof -->
            <div class="text-center space-y-4">
                <p class="text-gray-600 dark:text-gray-400 text-lg">
                    Bergabunglah dengan <span class="font-semibold text-blue-600 dark:text-blue-400">1000+ desa</span> yang telah merasakan transformasi digital
                </p>

                <!-- Animated Tooltip Avatars -->
                <div class="flex justify-center items-center space-x-2">
                    <div class="animated-tooltip-container flex -space-x-4">
                        <div class="tooltip-item group relative"
                             data-name="Budi Santoso"
                             data-designation="Kepala Desa Sukamaju"
                             data-image="https://images.unsplash.com/photo-1599566150163-29194dcaad36?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80">
                            <div class="tooltip-content absolute -top-20 left-1/2 transform -translate-x-1/2 z-50 opacity-0 pointer-events-none transition-all duration-300">
                                <div class="bg-black text-white px-4 py-2 rounded-lg shadow-xl relative">
                                    <div class="absolute inset-x-4 -bottom-px h-px bg-gradient-to-r from-transparent via-emerald-500 to-transparent"></div>
                                    <div class="absolute -bottom-px left-4 h-px w-8 bg-gradient-to-r from-transparent via-sky-500 to-transparent"></div>
                                    <div class="font-bold text-sm">Budi Santoso</div>
                                    <div class="text-xs opacity-80">Kepala Desa Sukamaju</div>
                                </div>
                            </div>
                            <img src="https://images.unsplash.com/photo-1599566150163-29194dcaad36?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
                                 alt="Budi Santoso"
                                 class="w-12 h-12 rounded-full border-2 border-white dark:border-gray-800 object-cover transition-all duration-300 group-hover:scale-110 group-hover:z-30">
                        </div>

                        <div class="tooltip-item group relative"
                             data-name="Siti Rahayu"
                             data-designation="Kepala Desa Makmur"
                             data-image="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80">
                            <div class="tooltip-content absolute -top-20 left-1/2 transform -translate-x-1/2 z-50 opacity-0 pointer-events-none transition-all duration-300">
                                <div class="bg-black text-white px-4 py-2 rounded-lg shadow-xl relative">
                                    <div class="absolute inset-x-4 -bottom-px h-px bg-gradient-to-r from-transparent via-emerald-500 to-transparent"></div>
                                    <div class="absolute -bottom-px left-4 h-px w-8 bg-gradient-to-r from-transparent via-sky-500 to-transparent"></div>
                                    <div class="font-bold text-sm">Siti Rahayu</div>
                                    <div class="text-xs opacity-80">Kepala Desa Makmur</div>
                                </div>
                            </div>
                            <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
                                 alt="Siti Rahayu"
                                 class="w-12 h-12 rounded-full border-2 border-white dark:border-gray-800 object-cover transition-all duration-300 group-hover:scale-110 group-hover:z-30">
                        </div>

                        <div class="tooltip-item group relative"
                             data-name="Ahmad Wijaya"
                             data-designation="Kepala Desa Sejahtera"
                             data-image="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80">
                            <div class="tooltip-content absolute -top-20 left-1/2 transform -translate-x-1/2 z-50 opacity-0 pointer-events-none transition-all duration-300">
                                <div class="bg-black text-white px-4 py-2 rounded-lg shadow-xl relative">
                                    <div class="absolute inset-x-4 -bottom-px h-px bg-gradient-to-r from-transparent via-emerald-500 to-transparent"></div>
                                    <div class="absolute -bottom-px left-4 h-px w-8 bg-gradient-to-r from-transparent via-sky-500 to-transparent"></div>
                                    <div class="font-bold text-sm">Ahmad Wijaya</div>
                                    <div class="text-xs opacity-80">Kepala Desa Sejahtera</div>
                                </div>
                            </div>
                            <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
                                 alt="Ahmad Wijaya"
                                 class="w-12 h-12 rounded-full border-2 border-white dark:border-gray-800 object-cover transition-all duration-300 group-hover:scale-110 group-hover:z-30">
                        </div>

                        <div class="tooltip-item group relative"
                             data-name="Dewi Lestari"
                             data-designation="Kepala Desa Mandiri"
                             data-image="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80">
                            <div class="tooltip-content absolute -top-20 left-1/2 transform -translate-x-1/2 z-50 opacity-0 pointer-events-none transition-all duration-300">
                                <div class="bg-black text-white px-4 py-2 rounded-lg shadow-xl relative">
                                    <div class="absolute inset-x-4 -bottom-px h-px bg-gradient-to-r from-transparent via-emerald-500 to-transparent"></div>
                                    <div class="absolute -bottom-px left-4 h-px w-8 bg-gradient-to-r from-transparent via-sky-500 to-transparent"></div>
                                    <div class="font-bold text-sm">Dewi Lestari</div>
                                    <div class="text-xs opacity-80">Kepala Desa Mandiri</div>
                                </div>
                            </div>
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
                                 alt="Dewi Lestari"
                                 class="w-12 h-12 rounded-full border-2 border-white dark:border-gray-800 object-cover transition-all duration-300 group-hover:scale-110 group-hover:z-30">
                        </div>

                        <div class="tooltip-item group relative"
                             data-name="Rudi Hartono"
                             data-designation="Kepala Desa Maju"
                             data-image="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80">
                            <div class="tooltip-content absolute -top-20 left-1/2 transform -translate-x-1/2 z-50 opacity-0 pointer-events-none transition-all duration-300">
                                <div class="bg-black text-white px-4 py-2 rounded-lg shadow-xl relative">
                                    <div class="absolute inset-x-4 -bottom-px h-px bg-gradient-to-r from-transparent via-emerald-500 to-transparent"></div>
                                    <div class="absolute -bottom-px left-4 h-px w-8 bg-gradient-to-r from-transparent via-sky-500 to-transparent"></div>
                                    <div class="font-bold text-sm">Rudi Hartono</div>
                                    <div class="text-xs opacity-80">Kepala Desa Maju</div>
                                </div>
                            </div>
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
                                 alt="Rudi Hartono"
                                 class="w-12 h-12 rounded-full border-2 border-white dark:border-gray-800 object-cover transition-all duration-300 group-hover:scale-110 group-hover:z-30">
                        </div>

                        <div class="tooltip-item group relative"
                             data-name="+95 Lainnya"
                             data-designation="Kepala Desa se-Indonesia"
                             data-image="">
                            <div class="tooltip-content absolute -top-20 left-1/2 transform -translate-x-1/2 z-50 opacity-0 pointer-events-none transition-all duration-300">
                                <div class="bg-black text-white px-4 py-2 rounded-lg shadow-xl relative">
                                    <div class="absolute inset-x-4 -bottom-px h-px bg-gradient-to-r from-transparent via-emerald-500 to-transparent"></div>
                                    <div class="absolute -bottom-px left-4 h-px w-8 bg-gradient-to-r from-transparent via-sky-500 to-transparent"></div>
                                    <div class="font-bold text-sm">+95 Lainnya</div>
                                    <div class="text-xs opacity-80">Kepala Desa se-Indonesia</div>
                                </div>
                            </div>
                            <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-600 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center text-white text-xs font-bold transition-all duration-300 group-hover:scale-110 group-hover:z-30">
                                +95
                            </div>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-4">Kepala desa bergabung minggu ini</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Seamless Transition Element -->
    <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-b from-transparent to-white dark:to-gray-900 pointer-events-none"></div>
</section>

<style>
    /* FIXED: Seamless Section Background - Putih di light mode, dark di dark mode */
    .seamless-section-bg {
        background: #ffffff !important;
        transition: background-color 0.3s ease;
    }
    .dark .seamless-section-bg {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
    }

    .seamless-grid {
        background-image:
            linear-gradient(rgba(59, 130, 246, 0.1) 1px, transparent 1px),
            linear-gradient(90deg, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
        background-size: 60px 60px;
        animation: gridFlow 30s linear infinite;
    }

    .dark .seamless-grid {
        background-image:
            linear-gradient(rgba(96, 165, 250, 0.15) 1px, transparent 1px),
            linear-gradient(90deg, rgba(96, 165, 250, 0.15) 1px, transparent 1px);
    }

    @keyframes gridFlow {
        0% { transform: translate(0, 0); }
        100% { transform: translate(60px, 60px); }
    }

    /* Premium Orbs */
    .premium-orb {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.15), rgba(147, 51, 234, 0.1), transparent);
        border: 2px solid rgba(59, 130, 246, 0.2);
        animation: premiumFloat 12s ease-in-out infinite;
        backdrop-filter: blur(20px);
        box-shadow: 0 0 40px rgba(59, 130, 246, 0.2);
    }

    .dark .premium-orb {
        background: radial-gradient(circle, rgba(59, 130, 246, 0.25), rgba(147, 51, 234, 0.2), transparent);
        border: 2px solid rgba(59, 130, 246, 0.3);
        box-shadow: 0 0 60px rgba(59, 130, 246, 0.3);
    }

    .orb-1 { width: 120px; height: 120px; top: 10%; left: 8%; animation-delay: 0s; }
    .orb-2 { width: 80px; height: 80px; top: 20%; right: 12%; animation-delay: 3s; }
    .orb-3 { width: 100px; height: 100px; bottom: 25%; left: 15%; animation-delay: 6s; }
    .orb-4 { width: 90px; height: 90px; bottom: 15%; right: 8%; animation-delay: 9s; }
    .orb-5 { width: 60px; height: 60px; top: 50%; left: 5%; animation-delay: 12s; }

    @keyframes premiumFloat {
        0%, 100% { transform: translateY(0px) scale(1) rotate(0deg); opacity: 0.7; }
        25% { transform: translateY(-30px) scale(1.1) rotate(90deg); opacity: 1; }
        50% { transform: translateY(-15px) scale(0.9) rotate(180deg); opacity: 0.8; }
        75% { transform: translateY(-25px) scale(1.05) rotate(270deg); opacity: 0.9; }
    }

    /* Connection Lines Animation */
    .connection-line {
        animation: drawLine 8s ease-in-out infinite;
        stroke-dasharray: 200;
        stroke-dashoffset: 200;
    }

    .line-1 { d: path("M 100 200 Q 400 100 700 300"); animation-delay: 0s; }
    .line-2 { d: path("M 200 500 Q 600 300 900 600"); animation-delay: 2s; }
    .line-3 { d: path("M 50 400 Q 500 200 800 500"); animation-delay: 4s; }

    @keyframes drawLine {
        0%, 20% { stroke-dashoffset: 200; opacity: 0; }
        40%, 80% { stroke-dashoffset: 0; opacity: 1; }
        100% { stroke-dashoffset: -200; opacity: 0; }
    }

    /* Premium Trust Badge */
    .premium-trust-badge {
        box-shadow:
            0 10px 40px rgba(59, 130, 246, 0.2),
            0 0 0 1px rgba(255, 255, 255, 0.1) inset,
            0 0 20px rgba(59, 130, 246, 0.1);
    }

    .dark .premium-trust-badge {
        box-shadow:
            0 10px 40px rgba(59, 130, 246, 0.3),
            0 0 0 1px rgba(255, 255, 255, 0.05) inset,
            0 0 30px rgba(59, 130, 246, 0.2);
    }

    /* Hero Text Effects */
    .hero-text-shadow {
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .dark .hero-text-shadow {
        text-shadow: 0 4px 20px rgba(255, 255, 255, 0.1);
    }

    .hero-gradient-text {
        background-size: 200% 200%;
        animation: gradientShift 4s ease infinite;
        filter: drop-shadow(0 4px 20px rgba(59, 130, 246, 0.3));
    }

    .dark .hero-gradient-text {
        filter: drop-shadow(0 4px 20px rgba(96, 165, 250, 0.4));
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .animate-gradient-x {
        background-size: 200% 200%;
        animation: gradient-x 3s ease infinite;
    }

    @keyframes gradient-x {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Advanced Typewriter */
    .advanced-typewriter {
        border-right: 3px solid #3b82f6;
        animation: advancedBlink 1.2s infinite;
        font-weight: 300;
        letter-spacing: 0.5px;
    }

    .dark .advanced-typewriter {
        border-right: 3px solid #60a5fa;
    }

    @keyframes advancedBlink {
        0%, 70% { border-color: #3b82f6; }
        71%, 100% { border-color: transparent; }
    }

    /* Premium CTA Buttons */
    .premium-primary-cta {
        box-shadow:
            0 10px 30px rgba(59, 130, 246, 0.4),
            0 0 0 1px rgba(255, 255, 255, 0.1) inset,
            0 0 20px rgba(59, 130, 246, 0.2);
    }

    .premium-primary-cta:hover {
        box-shadow:
            0 15px 40px rgba(59, 130, 246, 0.5),
            0 0 0 1px rgba(255, 255, 255, 0.2) inset,
            0 0 30px rgba(59, 130, 246, 0.3);
    }

    .premium-secondary-cta {
        box-shadow:
            0 10px 30px rgba(0, 0, 0, 0.1),
            0 0 0 1px rgba(255, 255, 255, 0.1) inset;
    }

    .dark .premium-secondary-cta {
        box-shadow:
            0 10px 30px rgba(0, 0, 0, 0.3),
            0 0 0 1px rgba(255, 255, 255, 0.05) inset;
    }

    /* Animated Tooltip Styles */
    .animated-tooltip-container {
        perspective: 1000px;
    }

    .tooltip-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .tooltip-item:hover .tooltip-content {
        opacity: 1;
        transform: translateX(-50%) translateY(-8px) scale(1);
        pointer-events: auto;
    }

    .tooltip-content {
        transform: translateX(-50%) translateY(0) scale(0.6);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .tooltip-item:hover {
        z-index: 50;
    }

    /* Tooltip animation on mouse move */
    .tooltip-content.animate {
        animation: tooltipFloat 0.6s ease-out;
    }

    @keyframes tooltipFloat {
        0% {
            opacity: 0;
            transform: translateX(-50%) translateY(20px) scale(0.6) rotate(-10deg);
        }
        50% {
            transform: translateX(-50%) translateY(-12px) scale(1.05) rotate(5deg);
        }
        100% {
            opacity: 1;
            transform: translateX(-50%) translateY(-8px) scale(1) rotate(0deg);
        }
    }

    /* Hover effects for avatars */
    .tooltip-item img {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .tooltip-item:hover img {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
        transform: scale(1.1) translateY(-2px);
    }

    /* Staggered animation for multiple tooltips */
    .tooltip-item:nth-child(1) .tooltip-content { transition-delay: 0ms; }
    .tooltip-item:nth-child(2) .tooltip-content { transition-delay: 50ms; }
    .tooltip-item:nth-child(3) .tooltip-content { transition-delay: 100ms; }
    .tooltip-item:nth-child(4) .tooltip-content { transition-delay: 150ms; }
    .tooltip-item:nth-child(5) .tooltip-content { transition-delay: 200ms; }
    .tooltip-item:nth-child(6) .tooltip-content { transition-delay: 250ms; }

    /* Advanced Animations */
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fade-in-up 1s ease-out forwards;
        opacity: 0;
    }

    .delay-200 { animation-delay: 0.2s; }
    .delay-400 { animation-delay: 0.4s; }
    .delay-600 { animation-delay: 0.6s; }
    .delay-800 { animation-delay: 0.8s; }

    /* Advanced Bounce */
    .animate-advanced-bounce {
        animation: advancedBounce 5s infinite;
    }

    @keyframes advancedBounce {
        0%, 100% { transform: translateX(-50%) translateY(0); }
        50% { transform: translateX(-50%) translateY(-12px); }
    }

    /* Advanced Scroll Indicator */
    .animate-advanced-scroll {
        animation: advancedScroll 3s ease-in-out infinite;
    }

    @keyframes advancedScroll {
        0% { transform: translateY(0); opacity: 0; }
        30% { opacity: 1; }
        100% { transform: translateY(20px); opacity: 0; }
    }

    /* Advanced Particles */
    .advanced-particle {
        position: absolute;
        width: 6px;
        height: 6px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.8), rgba(147, 51, 234, 0.6), transparent);
        border-radius: 50%;
        pointer-events: none;
        animation: advancedParticleFloat 10s linear infinite;
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
    }

    .dark .advanced-particle {
        background: radial-gradient(circle, rgba(96, 165, 250, 0.9), rgba(168, 85, 247, 0.7), transparent);
        box-shadow: 0 0 15px rgba(96, 165, 250, 0.6);
    }

    @keyframes advancedParticleFloat {
        0% {
            transform: translateY(100vh) translateX(0) scale(0) rotate(0deg);
            opacity: 0;
        }
        10% {
            opacity: 1;
            transform: scale(1);
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateY(-100px) translateX(200px) scale(0) rotate(360deg);
            opacity: 0;
        }
    }

    /* Responsive Enhancements */
    @media (max-width: 768px) {
        .premium-orb {
            width: 60px !important;
            height: 60px !important;
        }
        .hero-gradient-text {
            filter: drop-shadow(0 2px 10px rgba(59, 130, 246, 0.3));
        }
    }

    /* Ultra-smooth transitions */
    * {
        transition-property: color, background-color, border-color, box-shadow, backdrop-filter, transform, opacity;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 400ms;
    }

    /* Prevent hover offset issues */
    .advanced-stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        z-index: 10;
    }

    .advanced-stat-card {
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.5s ease;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Advanced Typewriter Effect with Multiple Phrases
    const typewriterText = document.getElementById('advanced-typewriter');
    const phrases = [
        '{{ $subtitle }}',
        'Menghadirkan solusi digital terpadu untuk kemajuan desa Indonesia',
        'Bersama membangun Indonesia Emas melalui transformasi digital kampung'
    ];

    let phraseIndex = 0;
    let charIndex = 0;
    let isDeleting = false;

    function advancedTypeWriter() {
        const currentPhrase = phrases[phraseIndex];

        if (isDeleting) {
            typewriterText.innerHTML = currentPhrase.substring(0, charIndex - 1);
            charIndex--;
        } else {
            typewriterText.innerHTML = currentPhrase.substring(0, charIndex + 1);
            charIndex++;
        }

        let typeSpeed = isDeleting ? 30 : 80;

        if (!isDeleting && charIndex === currentPhrase.length) {
            typeSpeed = 2000;
            isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            phraseIndex = (phraseIndex + 1) % phrases.length;
            typeSpeed = 500;
        }

        setTimeout(advancedTypeWriter, typeSpeed);
    }

    setTimeout(advancedTypeWriter, 1500);

    // Advanced Counter Animation
    const counters = document.querySelectorAll('.advanced-counter');
    const observerOptions = {
        threshold: 0.7,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseFloat(counter.getAttribute('data-target'));
                const suffix = counter.textContent.replace(/[0-9.]/g, '');
                let current = 0;
                const increment = target / 120;

                const updateCounter = () => {
                    if (current < target) {
                        current += increment;
                        if (suffix.includes('K')) {
                            counter.textContent = Math.ceil(current) + 'K+';
                        } else if (suffix.includes('%')) {
                            counter.textContent = current.toFixed(1) + '%';
                        } else if (suffix.includes('/')) {
                            counter.textContent = Math.ceil(current) + '/7';
                        } else {
                            counter.textContent = Math.ceil(current).toLocaleString() + '+';
                        }
                        requestAnimationFrame(updateCounter);
                    } else {
                        if (suffix.includes('K')) {
                            counter.textContent = target + 'K+';
                        } else if (suffix.includes('%')) {
                            counter.textContent = target + '%';
                        } else if (suffix.includes('/')) {
                            counter.textContent = '24/7';
                        } else {
                            counter.textContent = target.toLocaleString() + '+';
                        }
                    }
                };

                updateCounter();
                observer.unobserve(counter);
            }
        });
    }, observerOptions);

    counters.forEach(counter => observer.observe(counter));

    // Advanced Particle System
    function createAdvancedParticle() {
        const particle = document.createElement('div');
        particle.className = 'advanced-particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 10 + 's';
        particle.style.animationDuration = (Math.random() * 6 + 8) + 's';

        // Random size variation
        const size = Math.random() * 4 + 4;
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';

        document.getElementById('advanced-particles').appendChild(particle);

        setTimeout(() => {
            particle.remove();
        }, 10000);
    }

    // Create particles less frequently for premium feel
    setInterval(createAdvancedParticle, 1200);

    // Smooth scroll for CTA buttons
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

    // Advanced Mouse Interaction
    const hero = document.getElementById('home');
    const orbs = document.querySelectorAll('.premium-orb');

    hero.addEventListener('mousemove', (e) => {
        const { clientX, clientY } = e;
        const { innerWidth, innerHeight } = window;

        const xPercent = (clientX / innerWidth - 0.5) * 2;
        const yPercent = (clientY / innerHeight - 0.5) * 2;

        orbs.forEach((orb, index) => {
            const speed = (index + 1) * 0.4;
            const rotation = xPercent * 20;
            orb.style.transform = `translate(${xPercent * speed}px, ${yPercent * speed}px) rotate(${rotation}deg) scale(${1 + Math.abs(xPercent) * 0.1})`;
        });
    });

    // Reset on mouse leave
    hero.addEventListener('mouseleave', () => {
        orbs.forEach(orb => {
            orb.style.transform = '';
        });
    });

    // Advanced scroll effects
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallax = scrolled * 0.5;

        orbs.forEach((orb, index) => {
            const speed = (index + 1) * 0.1;
            orb.style.transform = `translateY(${parallax * speed}px)`;
        });
    });

    // Initialize animated tooltips
    initAnimatedTooltips();
});

// Animated Tooltip Functionality
function initAnimatedTooltips() {
    const tooltipItems = document.querySelectorAll('.tooltip-item');

    tooltipItems.forEach((item, index) => {
        const tooltip = item.querySelector('.tooltip-content');
        const img = item.querySelector('img');

        let mouseX = 0;
        let mouseY = 0;

        // Mouse move effect for rotation and translation
        item.addEventListener('mousemove', (e) => {
            const rect = item.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;

            mouseX = (e.clientX - centerX) / rect.width;
            mouseY = (e.clientY - centerY) / rect.height;

            // Apply subtle rotation and translation to tooltip
            if (tooltip) {
                const rotateX = mouseY * 10;
                const rotateY = mouseX * -10;
                const translateX = mouseX * 5;

                tooltip.style.transform = `translateX(calc(-50% + ${translateX}px)) translateY(-8px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            }
        });

        // Reset on mouse leave
        item.addEventListener('mouseleave', () => {
            if (tooltip) {
                tooltip.style.transform = 'translateX(-50%) translateY(-8px) rotateX(0deg) rotateY(0deg)';
            }
        });

        // Add entrance animation
        item.addEventListener('mouseenter', () => {
            if (tooltip) {
                tooltip.classList.add('animate');
                setTimeout(() => {
                    tooltip.classList.remove('animate');
                }, 600);
            }
        });
    });
}
</script>
