@props([
    'title' => 'Fitur Unggulan Kami',
    'subtitle' => 'Solusi komprehensif untuk transformasi digital kampung dan desa',
    'features' => []
])

<section id="features" class="overflow-hidden relative seamless-section-bg">
    <!-- 3D Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <!-- Geometric Shapes -->
        <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-blue-400/20 to-purple-400/20 rounded-full blur-xl animate-float"></div>
        <div class="absolute top-40 right-20 w-24 h-24 bg-gradient-to-r from-indigo-400/20 to-pink-400/20 rounded-full blur-xl animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 left-20 w-40 h-40 bg-gradient-to-r from-purple-400/20 to-blue-400/20 rounded-full blur-xl animate-float" style="animation-delay: 4s;"></div>
        
        <!-- 3D Grid Pattern -->
        <div class="absolute inset-0 opacity-[0.05] dark:opacity-[0.08]">
            <div class="seamless-grid-3d"></div>
        </div>
        
        <!-- Floating 3D Cubes -->
        <div class="absolute top-32 right-32 w-8 h-8 bg-gradient-to-br from-blue-500/30 to-indigo-600/30 transform rotate-45 animate-spin-slow"></div>
        <div class="absolute bottom-32 left-32 w-6 h-6 bg-gradient-to-br from-purple-500/30 to-pink-600/30 transform rotate-45 animate-spin-slow" style="animation-delay: 3s;"></div>
        
        <!-- Gradient Orbs -->
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-gradient-radial from-blue-500/10 via-purple-500/5 to-transparent rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-1/4 right-1/4 w-48 h-48 bg-gradient-radial from-green-500/10 via-emerald-500/5 to-transparent rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 2s;"></div>
    </div>

    <!-- Container Scroll Animation -->
    <div class="container-scroll-section h-[60rem] md:h-[80rem] flex items-center justify-center relative p-2 md:p-20" id="container-scroll">
        <div class="py-10 md:py-40 w-full relative" style="perspective: 1000px;">

            <!-- Animated Header with Sparkles Text -->
            <div class="scroll-header max-w-6xl mx-auto text-center mb-6" id="scroll-header">
                <h1 class="text-4xl font-semibold text-black dark:text-white mb-2">
                    Unleash the power of <br />
                    <!-- Sparkles Text Container -->
                    <div class="sparkles-text-container relative inline-block">
                        <span class="sparkles-text text-4xl md:text-[6rem] font-bold mt-1 leading-none bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent" id="sparkles-text">
                            Digital Solutions
                        </span>
                        <!-- Sparkles Container -->
                        <div class="sparkles-overlay absolute inset-0 pointer-events-none" id="sparkles-container"></div>
                    </div>
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto mt-4">
                    {{ $subtitle }}
                </p>
            </div>

            <!-- Animated Card Container -->
            <div class="scroll-card max-w-6xl -mt-8 mx-auto h-[35rem] md:h-[45rem] w-full border-4 border-[#6C6C6C] p-3 md:p-8 bg-[#222222] rounded-[30px] shadow-2xl backdrop-blur-sm"
                 id="scroll-card">

                <div class="h-full w-full rounded-2xl bg-zinc-900 md:rounded-2xl p-4 md:p-6 shadow-inner">
                    <!-- Features Content -->
                    <div class="h-full w-full flex items-center justify-center">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 w-full max-w-5xl">

                            <!-- Card 1: Manajemen Desa Digital -->
                            <div class="glowing-card group cursor-pointer relative h-[14rem] md:h-[16rem] w-full" data-card="1">
                                <!-- Glowing Effect Container -->
                                <div class="glowing-effect-container absolute inset-0 rounded-2xl pointer-events-none">
                                    <div class="glowing-effect absolute inset-0 rounded-2xl opacity-0 transition-opacity duration-300"></div>
                                </div>
                                
                                <!-- Card Background -->
                                <div class="absolute inset-0 bg-gray-900/90 backdrop-blur-sm rounded-2xl border border-gray-700/50 shadow-xl transition-all duration-500 group-hover:border-gray-600/70"></div>

                                <!-- Card Content -->
                                <div class="relative z-10 h-full p-4 md:p-6 flex flex-col items-center text-center">
                                    <!-- Circular Icon -->
                                    <div class="w-12 h-12 md:w-16 md:h-16 mb-4 md:mb-6 relative">
                                        <div class="w-full h-full bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110">
                                            <span class="text-xl md:text-2xl">🏘️</span>
                                        </div>
                                        <div class="absolute -top-1 -right-1 w-3 h-3 md:w-4 md:h-4 bg-green-500 rounded-full border-2 border-gray-900 shadow-sm"></div>
                                    </div>

                                    <h3 class="text-white text-sm md:text-lg font-medium mb-3 md:mb-4 leading-tight">
                                        Manajemen Desa<br />Digital
                                    </h3>

                                    <div class="mt-auto">
                                        <span class="px-3 py-1.5 md:px-4 md:py-2 bg-blue-500/20 border border-blue-500 text-blue-400 text-xs md:text-sm font-medium rounded-full">
                                            Advanced
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: E-Commerce & Fintech -->
                            <div class="glowing-card group cursor-pointer relative h-[14rem] md:h-[16rem] w-full" data-card="2">
                                <div class="glowing-effect-container absolute inset-0 rounded-2xl pointer-events-none">
                                    <div class="glowing-effect absolute inset-0 rounded-2xl opacity-0 transition-opacity duration-300"></div>
                                </div>
                                
                                <div class="absolute inset-0 bg-gray-900/90 backdrop-blur-sm rounded-2xl border border-gray-700/50 shadow-xl transition-all duration-500 group-hover:border-gray-600/70"></div>

                                <div class="relative z-10 h-full p-4 md:p-6 flex flex-col items-center text-center">
                                    <div class="w-12 h-12 md:w-16 md:h-16 mb-4 md:mb-6 relative">
                                        <div class="w-full h-full bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110">
                                            <span class="text-xl md:text-2xl">💰</span>
                                        </div>
                                        <div class="absolute -top-1 -right-1 w-3 h-3 md:w-4 md:h-4 bg-orange-500 rounded-full border-2 border-gray-900 shadow-sm"></div>
                                    </div>

                                    <h3 class="text-white text-sm md:text-lg font-medium mb-3 md:mb-4 leading-tight">
                                        E-Commerce &<br />Fintech
                                    </h3>

                                    <div class="mt-auto">
                                        <span class="px-3 py-1.5 md:px-4 md:py-2 bg-green-500/20 border border-green-500 text-green-400 text-xs md:text-sm font-medium rounded-full">
                                            Profitable
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3: Edukasi & Pelatihan -->
                            <div class="glowing-card group cursor-pointer relative h-[14rem] md:h-[16rem] w-full" data-card="3">
                                <div class="glowing-effect-container absolute inset-0 rounded-2xl pointer-events-none">
                                    <div class="glowing-effect absolute inset-0 rounded-2xl opacity-0 transition-opacity duration-300"></div>
                                </div>
                                
                                <div class="absolute inset-0 bg-gray-900/90 backdrop-blur-sm rounded-2xl border border-gray-700/50 shadow-xl transition-all duration-500 group-hover:border-gray-600/70"></div>

                                <div class="relative z-10 h-full p-4 md:p-6 flex flex-col items-center text-center">
                                    <div class="w-12 h-12 md:w-16 md:h-16 mb-4 md:mb-6 relative">
                                        <div class="w-full h-full bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110">
                                            <span class="text-xl md:text-2xl">📚</span>
                                        </div>
                                        <div class="absolute -top-1 -right-1 w-3 h-3 md:w-4 md:h-4 bg-blue-500 rounded-full border-2 border-gray-900 shadow-sm"></div>
                                    </div>

                                    <h3 class="text-white text-sm md:text-lg font-medium mb-3 md:mb-4 leading-tight">
                                        Edukasi &<br />Pelatihan
                                    </h3>

                                    <div class="mt-auto">
                                        <span class="px-3 py-1.5 md:px-4 md:py-2 bg-purple-500/20 border border-purple-500 text-purple-400 text-xs md:text-sm font-medium rounded-full">
                                            Essential
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 4: AI Analytics -->
                            <div class="glowing-card group cursor-pointer relative h-[14rem] md:h-[16rem] w-full" data-card="4">
                                <div class="glowing-effect-container absolute inset-0 rounded-2xl pointer-events-none">
                                    <div class="glowing-effect absolute inset-0 rounded-2xl opacity-0 transition-opacity duration-300"></div>
                                </div>
                                
                                <div class="absolute inset-0 bg-gray-900/90 backdrop-blur-sm rounded-2xl border border-gray-700/50 shadow-xl transition-all duration-500 group-hover:border-gray-600/70"></div>

                                <div class="relative z-10 h-full p-4 md:p-6 flex flex-col items-center text-center">
                                    <div class="w-12 h-12 md:w-16 md:h-16 mb-4 md:mb-6 relative">
                                        <div class="w-full h-full bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110">
                                            <span class="text-xl md:text-2xl">🤖</span>
                                        </div>
                                        <div class="absolute -top-1 -right-1 w-3 h-3 md:w-4 md:h-4 bg-red-500 rounded-full border-2 border-gray-900 shadow-sm"></div>
                                    </div>

                                    <h3 class="text-white text-sm md:text-lg font-medium mb-3 md:mb-4 leading-tight">
                                        AI Analytics &<br />Insights
                                    </h3>

                                    <div class="mt-auto">
                                        <span class="px-3 py-1.5 md:px-4 md:py-2 bg-cyan-500/20 border border-cyan-500 text-cyan-400 text-xs md:text-sm font-medium rounded-full">
                                            Innovative
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 5: Security -->
                            <div class="glowing-card group cursor-pointer relative h-[14rem] md:h-[16rem] w-full" data-card="5">
                                <div class="glowing-effect-container absolute inset-0 rounded-2xl pointer-events-none">
                                    <div class="glowing-effect absolute inset-0 rounded-2xl opacity-0 transition-opacity duration-300"></div>
                                </div>
                                
                                <div class="absolute inset-0 bg-gray-900/90 backdrop-blur-sm rounded-2xl border border-gray-700/50 shadow-xl transition-all duration-500 group-hover:border-gray-600/70"></div>

                                <div class="relative z-10 h-full p-4 md:p-6 flex flex-col items-center text-center">
                                    <div class="w-12 h-12 md:w-16 md:h-16 mb-4 md:mb-6 relative">
                                        <div class="w-full h-full bg-gradient-to-br from-red-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110">
                                            <span class="text-xl md:text-2xl">🔒</span>
                                        </div>
                                        <div class="absolute -top-1 -right-1 w-3 h-3 md:w-4 md:h-4 bg-green-500 rounded-full border-2 border-gray-900 shadow-sm"></div>
                                    </div>

                                    <h3 class="text-white text-sm md:text-lg font-medium mb-3 md:mb-4 leading-tight">
                                        Security &<br />Privacy
                                    </h3>

                                    <div class="mt-auto">
                                        <span class="px-3 py-1.5 md:px-4 md:py-2 bg-red-500/20 border border-red-500 text-red-400 text-xs md:text-sm font-medium rounded-full">
                                            Critical
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 6: IoT Integration -->
                            <div class="glowing-card group cursor-pointer relative h-[14rem] md:h-[16rem] w-full" data-card="6">
                                <div class="glowing-effect-container absolute inset-0 rounded-2xl pointer-events-none">
                                    <div class="glowing-effect absolute inset-0 rounded-2xl opacity-0 transition-opacity duration-300"></div>
                                </div>
                                
                                <div class="absolute inset-0 bg-gray-900/90 backdrop-blur-sm rounded-2xl border border-gray-700/50 shadow-xl transition-all duration-500 group-hover:border-gray-600/70"></div>

                                <div class="relative z-10 h-full p-4 md:p-6 flex flex-col items-center text-center">
                                    <div class="w-12 h-12 md:w-16 md:h-16 mb-4 md:mb-6 relative">
                                        <div class="w-full h-full bg-gradient-to-br from-teal-400 to-green-500 rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110">
                                            <span class="text-xl md:text-2xl">📡</span>
                                        </div>
                                        <div class="absolute -top-1 -right-1 w-3 h-3 md:w-4 md:h-4 bg-purple-500 rounded-full border-2 border-gray-900 shadow-sm"></div>
                                    </div>

                                    <h3 class="text-white text-sm md:text-lg font-medium mb-3 md:mb-4 leading-tight">
                                        IoT Smart<br />Integration
                                    </h3>

                                    <div class="mt-auto">
                                        <span class="px-3 py-1.5 md:px-4 md:py-2 bg-teal-500/20 border border-teal-500 text-teal-400 text-xs md:text-sm font-medium rounded-full">
                                            Connected
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Seamless Section Background */
    .seamless-section-bg {
        background: #ffffff;
        transition: background-color 0.3s ease;
    }
    .dark .seamless-section-bg {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    }

    /* Enhanced 3D Grid Pattern */
    .seamless-grid-3d {
        background-image:
            linear-gradient(rgba(59, 130, 246, 0.08) 1px, transparent 1px),
            linear-gradient(90deg, rgba(59, 130, 246, 0.08) 1px, transparent 1px),
            linear-gradient(45deg, rgba(147, 51, 234, 0.04) 1px, transparent 1px),
            linear-gradient(-45deg, rgba(147, 51, 234, 0.04) 1px, transparent 1px);
        background-size: 60px 60px, 60px 60px, 30px 30px, 30px 30px;
        animation: gridFlow3D 40s linear infinite;
    }

    .dark .seamless-grid-3d {
        background-image:
            linear-gradient(rgba(96, 165, 250, 0.12) 1px, transparent 1px),
            linear-gradient(90deg, rgba(96, 165, 250, 0.12) 1px, transparent 1px),
            linear-gradient(45deg, rgba(168, 85, 247, 0.08) 1px, transparent 1px),
            linear-gradient(-45deg, rgba(168, 85, 247, 0.08) 1px, transparent 1px);
    }

    @keyframes gridFlow3D {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(60px, 60px) rotate(360deg); }
    }

    /* Enhanced Floating Animation */
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        33% { transform: translateY(-15px) rotate(120deg); }
        66% { transform: translateY(-5px) rotate(240deg); }
    }

    .animate-float {
        animation: float 8s ease-in-out infinite;
    }

    /* Slow Spin Animation */
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-spin-slow {
        animation: spin-slow 20s linear infinite;
    }

    /* Pulse Slow Animation */
    @keyframes pulse-slow {
        0%, 100% { opacity: 0.3; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(1.05); }
    }

    .animate-pulse-slow {
        animation: pulse-slow 6s ease-in-out infinite;
    }

    /* Gradient Radial */
    .bg-gradient-radial {
        background: radial-gradient(circle, var(--tw-gradient-stops));
    }

    /* Sparkles Text Styles */
    .sparkles-text-container {
        position: relative;
        display: inline-block;
    }

    .sparkles-text {
        position: relative;
        z-index: 2;
        animation: textShimmer 3s ease-in-out infinite;
    }

    @keyframes textShimmer {
        0%, 100% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
    }

    .sparkles-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
        z-index: 3;
    }

    /* Individual Sparkle Styles */
    .sparkle {
        position: absolute;
        pointer-events: none;
        z-index: 10;
        animation: sparkleAnimation 0.8s ease-in-out infinite;
    }

    .sparkle svg {
        width: 21px;
        height: 21px;
        filter: drop-shadow(0 0 6px currentColor);
    }

    @keyframes sparkleAnimation {
        0% {
            opacity: 0;
            transform: scale(0) rotate(75deg);
        }
        50% {
            opacity: 1;
            transform: scale(1) rotate(120deg);
        }
        100% {
            opacity: 0;
            transform: scale(0) rotate(150deg);
        }
    }

    /* Sparkle Colors */
    .sparkle-purple {
        color: #9E7AFF;
    }

    .sparkle-pink {
        color: #FE8BBB;
    }

    .sparkle-blue {
        color: #3B82F6;
    }

    .sparkle-cyan {
        color: #06B6D4;
    }

    .sparkle-green {
        color: #10B981;
    }

    .sparkle-yellow {
        color: #F59E0B;
    }

    /* Container Scroll Animation */
    #scroll-header {
        transform: translateY(0px);
        will-change: transform;
    }

    #scroll-card {
        transform: rotateX(15deg) scale(1.02);
        transform-origin: center center;
        will-change: transform;
        box-shadow:
            0 0 #0000004d,
            0 9px 20px #0000004a,
            0 37px 37px #00000042,
            0 84px 50px #00000026,
            0 149px 60px #0000000a,
            0 233px 65px #00000003;
    }

    /* Glowing Card Styles - Maintain 3D Effect */
    .glowing-card {
        transform: rotateX(8deg) rotateY(-2deg);
        transform-style: preserve-3d;
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glowing-card:hover {
        transform: rotateX(2deg) rotateY(0deg) translateY(-4px);
    }

    .glowing-card:nth-child(2) { transform: rotateX(6deg) rotateY(1deg); }
    .glowing-card:nth-child(3) { transform: rotateX(7deg) rotateY(3deg); }
    .glowing-card:nth-child(4) { transform: rotateX(5deg) rotateY(-1deg); }
    .glowing-card:nth-child(5) { transform: rotateX(9deg) rotateY(2deg); }
    .glowing-card:nth-child(6) { transform: rotateX(4deg) rotateY(-3deg); }

    .glowing-card:nth-child(2):hover { transform: rotateX(1deg) rotateY(0deg) translateY(-4px); }
    .glowing-card:nth-child(3):hover { transform: rotateX(2deg) rotateY(1deg) translateY(-4px); }
    .glowing-card:nth-child(4):hover { transform: rotateX(1deg) rotateY(0deg) translateY(-4px); }
    .glowing-card:nth-child(5):hover { transform: rotateX(2deg) rotateY(1deg) translateY(-4px); }
    .glowing-card:nth-child(6):hover { transform: rotateX(1deg) rotateY(-1deg) translateY(-4px); }

    /* Glowing Effect Styles - Dual Mode Colors */
    .glowing-effect {
        --blur: 0px;
        --spread: 40;
        --start: 0;
        --active: 0;
        --border-width: 3px;
        --proximity: 64px;
        --inactive-zone: 0.01;
        
        /* Dark Mode Colors (Default) */
        background: 
            radial-gradient(circle, #dd7bbb 10%, #dd7bbb00 20%),
            radial-gradient(circle at 40% 40%, #d79f1e 5%, #d79f1e00 15%),
            radial-gradient(circle at 60% 60%, #5a922c 10%, #5a922c00 20%), 
            radial-gradient(circle at 40% 60%, #4c7894 10%, #4c789400 20%),
            conic-gradient(
                from calc(var(--start) * 1deg),
                #dd7bbb 0%,
                #d79f1e 25%,
                #5a922c 50%, 
                #4c7894 75%,
                #dd7bbb 100%
            );
        
        mask: conic-gradient(
            from calc((var(--start) - var(--spread)) * 1deg),
            transparent 0deg,
            white calc(var(--spread) * 1deg),
            white calc(var(--spread) * 2deg),
            transparent calc(var(--spread) * 2deg)
        );
        
        border: var(--border-width) solid transparent;
        background-clip: padding-box, border-box;
        background-origin: padding-box, border-box;
        opacity: var(--active);
        transition: opacity 0.3s ease;
    }

    /* Light Mode Glowing Colors */
    .light .glowing-effect,
    html:not(.dark) .glowing-effect {
        background: 
            radial-gradient(circle, #3b82f6 10%, #3b82f600 20%),
            radial-gradient(circle at 40% 40%, #8b5cf6 5%, #8b5cf600 15%),
            radial-gradient(circle at 60% 60%, #10b981 10%, #10b98100 20%), 
            radial-gradient(circle at 40% 60%, #f59e0b 10%, #f59e0b00 20%),
            conic-gradient(
                from calc(var(--start) * 1deg),
                #3b82f6 0%,
                #8b5cf6 25%,
                #10b981 50%, 
                #f59e0b 75%,
                #3b82f6 100%
            );
    }

    .glowing-card:hover .glowing-effect {
        --active: 1;
    }

    /* Card Background Effects */
    .glowing-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        border-radius: 1rem;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1;
    }

    .glowing-card:hover::before {
        opacity: 1;
    }

    /* Icon Hover Effects */
    .glowing-card:hover .w-12.h-12 > div,
    .glowing-card:hover .w-16.h-16 > div {
        transform: scale(1.1);
        transition: transform 0.3s ease;
    }

    /* Badge Hover Effects */
    .glowing-card:hover span {
        transform: scale(1.05);
        transition: transform 0.3s ease;
    }

    /* Perfect Grid Layout */
    @media (min-width: 768px) {
        .grid.grid-cols-1.md\\:grid-cols-3 {
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(2, 1fr);
        }
    }

    /* Mobile Responsive */
    @media (max-width: 767px) {
        .container-scroll-section {
            height: 50rem !important;
        }

        #scroll-card {
            height: 28rem !important;
            transform: rotateX(12deg) scale(0.9);
        }

        .glowing-card {
            height: 12rem !important;
        }

        .grid.grid-cols-1.md\\:grid-cols-3 {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .sparkle svg {
            width: 16px;
            height: 16px;
        }
    }

    /* Smooth animations */
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const containerSection = document.getElementById('container-scroll');
    const scrollHeader = document.getElementById('scroll-header');
    const scrollCard = document.getElementById('scroll-card');

    if (!containerSection || !scrollHeader || !scrollCard) {
        console.log('Elements not found');
        return;
    }

    let isMobile = window.innerWidth <= 768;

    window.addEventListener('resize', () => {
        isMobile = window.innerWidth <= 768;
    });

    function updateScrollAnimation() {
        const rect = containerSection.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        const containerHeight = rect.height;

        let scrollProgress = 0;

        const startOffset = windowHeight * 0.7;
        const endOffset = -containerHeight * 0.3;

        if (rect.top <= startOffset && rect.bottom >= endOffset) {
            const totalDistance = startOffset - endOffset;
            const currentDistance = startOffset - rect.top;
            scrollProgress = Math.max(0, Math.min(1, currentDistance / totalDistance));
            scrollProgress = easeOutQuad(scrollProgress);
        }

        const headerTranslateY = scrollProgress * -80;
        const cardRotateX = 15 - (scrollProgress * 15);

        let cardScale;
        if (isMobile) {
            cardScale = 0.9 + (scrollProgress * 0.08);
        } else {
            cardScale = 1.02 - (scrollProgress * 0.04);
        }

        scrollHeader.style.transform = `translateY(${headerTranslateY}px)`;
        scrollCard.style.transform = `rotateX(${cardRotateX}deg) scale(${cardScale})`;

        const headerOpacity = 1 - (scrollProgress * 0.2);
        scrollHeader.style.opacity = Math.max(0.8, headerOpacity);

        // Update individual card rotations to maintain 3D effect
        const glowingCards = document.querySelectorAll('.glowing-card');
        glowingCards.forEach((card, index) => {
            const baseRotations = [
                { x: 8, y: -2 },
                { x: 6, y: 1 },
                { x: 7, y: 3 },
                { x: 5, y: -1 },
                { x: 9, y: 2 },
                { x: 4, y: -3 }
            ];
            
            const base = baseRotations[index] || { x: 6, y: 0 };
            const minRotationX = 0;
            const minRotationY = 0;
            
            const currentRotationX = base.x - (scrollProgress * (base.x - minRotationX));
            const currentRotationY = base.y - (scrollProgress * (base.y - minRotationY));
            
            if (!card.matches(':hover')) {
                card.style.transform = `rotateX(${currentRotationX}deg) rotateY(${currentRotationY}deg)`;
            }
        });
    }

    function easeOutQuad(t) {
        return 1 - (1 - t) * (1 - t);
    }

    let animationId;
    function onScroll() {
        if (animationId) {
            cancelAnimationFrame(animationId);
        }
        animationId = requestAnimationFrame(updateScrollAnimation);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    updateScrollAnimation();

    window.addEventListener('resize', function() {
        updateScrollAnimation();
    });

    // Sparkles Text Animation
    function initSparklesText() {
        const sparklesContainer = document.getElementById('sparkles-container');
        const sparklesText = document.getElementById('sparkles-text');
        
        if (!sparklesContainer || !sparklesText) return;

        const colors = ['sparkle-purple', 'sparkle-pink', 'sparkle-blue', 'sparkle-cyan', 'sparkle-green', 'sparkle-yellow'];
        const sparklesCount = 12;
        let sparkles = [];

        // SVG Star Path
        const starPath = "M9.82531 0.843845C10.0553 0.215178 10.9446 0.215178 11.1746 0.843845L11.8618 2.72026C12.4006 4.19229 12.3916 6.39157 13.5 7.5C14.6084 8.60843 16.8077 8.59935 18.2797 9.13822L20.1561 9.82534C20.7858 10.0553 20.7858 10.9447 20.1561 11.1747L18.2797 11.8618C16.8077 12.4007 14.6084 12.3916 13.5 13.5C12.3916 14.6084 12.4006 16.8077 11.8618 18.2798L11.1746 20.1562C10.9446 20.7858 10.0553 20.7858 9.82531 20.1562L9.13819 18.2798C8.59932 16.8077 8.60843 14.6084 7.5 13.5C6.39157 12.3916 4.19225 12.4007 2.72023 11.8618L0.843814 11.1747C0.215148 10.9447 0.215148 10.0553 0.843814 9.82534L2.72023 9.13822C4.19225 8.59935 6.39157 8.60843 7.5 7.5C8.60843 6.39157 8.59932 4.19229 9.13819 2.72026L9.82531 0.843845Z";

        function createSparkle() {
            const sparkle = document.createElement('div');
            sparkle.className = `sparkle ${colors[Math.floor(Math.random() * colors.length)]}`;
            
            // Random position within text bounds
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            
            sparkle.style.left = x + '%';
            sparkle.style.top = y + '%';
            
            // Create SVG
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('width', '21');
            svg.setAttribute('height', '21');
            svg.setAttribute('viewBox', '0 0 21 21');
            
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', starPath);
            path.setAttribute('fill', 'currentColor');
            
            svg.appendChild(path);
            sparkle.appendChild(svg);
            
            // Random scale and delay
            const scale = Math.random() * 0.7 + 0.3;
            const delay = Math.random() * 2;
            const duration = Math.random() * 1 + 0.5;
            
            sparkle.style.transform = `scale(${scale})`;
            sparkle.style.animationDelay = delay + 's';
            sparkle.style.animationDuration = duration + 's';
            
            return {
                element: sparkle,
                lifespan: Math.random() * 10 + 5,
                maxLifespan: Math.random() * 10 + 5
            };
        }

        function updateSparkles() {
            // Remove expired sparkles
            sparkles = sparkles.filter(sparkle => {
                sparkle.lifespan -= 0.1;
                if (sparkle.lifespan <= 0) {
                    sparkle.element.remove();
                    return false;
                }
                return true;
            });

            // Add new sparkles if needed
            while (sparkles.length < sparklesCount) {
                const newSparkle = createSparkle();
                sparklesContainer.appendChild(newSparkle.element);
                sparkles.push(newSparkle);
            }
        }

        // Initialize sparkles
        for (let i = 0; i < sparklesCount; i++) {
            const sparkle = createSparkle();
            sparklesContainer.appendChild(sparkle.element);
            sparkles.push(sparkle);
        }

        // Update sparkles periodically
        setInterval(updateSparkles, 100);
    }

    // Initialize sparkles after a short delay
    setTimeout(initSparklesText, 1000);

    // Glowing Effect Logic
    const glowingCards = document.querySelectorAll('.glowing-card');
    let lastPosition = { x: 0, y: 0 };
    let animationFrameRef = null;

    function handleMove(e) {
        if (animationFrameRef) {
            cancelAnimationFrame(animationFrameRef);
        }

        animationFrameRef = requestAnimationFrame(() => {
            const mouseX = e?.clientX ?? lastPosition.x;
            const mouseY = e?.clientY ?? lastPosition.y;

            if (e) {
                lastPosition = { x: mouseX, y: mouseY };
            }

            glowingCards.forEach((card) => {
                const glowingEffect = card.querySelector('.glowing-effect');
                if (!glowingEffect) return;

                const { left, top, width, height } = card.getBoundingClientRect();
                const center = [left + width * 0.5, top + height * 0.5];
                const distanceFromCenter = Math.hypot(mouseX - center[0], mouseY - center[1]);
                const inactiveRadius = 0.5 * Math.min(width, height) * 0.01;

                if (distanceFromCenter < inactiveRadius) {
                    glowingEffect.style.setProperty('--active', '0');
                    return;
                }

                const proximity = 64;
                const isActive = 
                    mouseX > left - proximity &&
                    mouseX < left + width + proximity &&
                    mouseY > top - proximity &&
                    mouseY < top + height + proximity;

                glowingEffect.style.setProperty('--active', isActive ? '1' : '0');

                if (!isActive) return;

                let targetAngle = (180 * Math.atan2(mouseY - center[1], mouseX - center[0])) / Math.PI + 90;
                
                const currentAngle = parseFloat(glowingEffect.style.getPropertyValue('--start')) || 0;
                const angleDiff = ((targetAngle - currentAngle + 180) % 360) - 180;
                const newAngle = currentAngle + angleDiff * 0.1;

                glowingEffect.style.setProperty('--start', String(newAngle));
            });
        });
    }

    function handleScroll() {
        handleMove();
    }

    window.addEventListener('scroll', handleScroll, { passive: true });
    document.addEventListener('mousemove', handleMove, { passive: true });

    window.addEventListener('beforeunload', () => {
        if (animationFrameRef) {
            cancelAnimationFrame(animationFrameRef);
        }
    });

    console.log('Features section with sparkles text and enhanced glowing effects initialized');
});
</script>