@props([
    'title' => 'Struktur Organisasi Kampung Digital',
    'subtitle' => 'Hierarki kepemimpinan dan koordinasi dalam ekosistem digital kampung',
    'stats' => [
        ['label' => 'Total Penduduk', 'value' => '2,847', 'icon' => '👥', 'color' => 'blue'],
        ['label' => 'Kepala Keluarga', 'value' => '847', 'icon' => '🏠', 'color' => 'green'],
        ['label' => 'UMKM Aktif', 'value' => '156', 'icon' => '🏪', 'color' => 'purple'],
        ['label' => 'Program Digital', 'value' => '23', 'icon' => '💻', 'color' => 'orange'],
        ['label' => 'Tingkat Literasi', 'value' => '89%', 'icon' => '📚', 'color' => 'cyan'],
        ['label' => 'Konektivitas', 'value' => '95%', 'icon' => '📡', 'color' => 'pink']
    ]
])

<section id="village-structure" class="relative overflow-hidden seamless-section-bg">
    <!-- Seamless Grid Background -->
    <div class="absolute inset-0 seamless-grid opacity-30 dark:opacity-20"></div>
    
    <!-- Premium Mesh Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-purple-500/5 to-indigo-500/10 dark:from-blue-400/10 dark:via-purple-400/10 dark:to-indigo-400/15"></div>
    
    <!-- 3D Floating Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="floating-cube cube-1"></div>
        <div class="floating-cube cube-2"></div>
        <div class="floating-cube cube-3"></div>
        <div class="floating-sphere sphere-1"></div>
        <div class="floating-sphere sphere-2"></div>
    </div>
    
    <!-- Gemini Scroll Container -->
    <div class="gemini-scroll-container relative py-20" id="village-structure-container">
        <div class="container mx-auto px-4 relative z-10">
            <!-- Header -->
            <div class="text-center mb-20 gemini-element" data-delay="0">
                <div class="inline-flex items-center px-4 py-2 bg-green-100 dark:bg-green-900/30 rounded-full text-green-600 dark:text-green-400 text-sm font-medium mb-6">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Organizational Structure
                </div>
                <h2 class="text-4xl md:text-5xl font-bold mb-6 text-gray-900 dark:text-white">{{ $title }}</h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    {{ $subtitle }}
                </p>
            </div>

            <!-- 3D Statistics Dashboard -->
            <div class="stats-3d-container mb-20 gemini-element" data-delay="200">
                <div class="text-center mb-12">
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4">
                        Statistik Kampung Digital
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Data real-time dari seluruh wilayah kampung digital
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                    @foreach($stats as $index => $stat)
                    <div class="stats-3d-card group gemini-node" data-node-delay="{{ $index * 100 }}" data-color="{{ $stat['color'] }}">
                        <div class="stats-card-inner">
                            <!-- 3D Background Effect -->
                            <div class="stats-3d-bg"></div>
                            
                            <!-- Card Content -->
                            <div class="stats-content">
                                <!-- Icon with 3D Effect -->
                                <div class="stats-icon-container">
                                    <div class="stats-icon-3d">
                                        <div class="icon-face icon-front">
                                            <span class="text-3xl">{{ $stat['icon'] }}</span>
                                        </div>
                                        <div class="icon-face icon-back">
                                            <span class="text-3xl opacity-50">{{ $stat['icon'] }}</span>
                                        </div>
                                        <div class="icon-face icon-right"></div>
                                        <div class="icon-face icon-left"></div>
                                        <div class="icon-face icon-top"></div>
                                        <div class="icon-face icon-bottom"></div>
                                    </div>
                                </div>
                                
                                <!-- Stats Value with Counter Animation -->
                                <div class="stats-value-container">
                                    <div class="stats-value" data-target="{{ $stat['value'] }}">0</div>
                                    <div class="stats-label">{{ $stat['label'] }}</div>
                                </div>
                                
                                <!-- 3D Progress Bar -->
                                <div class="stats-progress-3d">
                                    <div class="progress-track">
                                        <div class="progress-fill" data-progress="{{ is_numeric(str_replace('%', '', $stat['value'])) ? str_replace('%', '', $stat['value']) : rand(60, 95) }}"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Holographic Effect -->
                            <div class="holographic-overlay"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Village Structure Hierarchy -->
            <div class="max-w-6xl mx-auto">
                <!-- Level 1: Kepala Desa -->
                <div class="structure-level level-1 mb-16 gemini-element" data-delay="400">
                    <div class="flex justify-center">
                        <div class="structure-node node-kepala-desa group gemini-node" data-node-delay="0">
                            <div class="node-content">
                                <div class="node-avatar">
                                    <div class="avatar-ring"></div>
                                    <div class="avatar-inner">
                                        <span class="text-2xl">👑</span>
                                    </div>
                                    <!-- 3D Stats Badge -->
                                    <div class="node-stats-3d">
                                        <span class="stats-badge">1 Desa</span>
                                    </div>
                                </div>
                                <div class="node-info">
                                    <h3 class="node-title">Kepala Desa</h3>
                                    <p class="node-subtitle">Pemimpin Tertinggi</p>
                                    <div class="node-stats">
                                        <span class="stat-item">2,847 Jiwa</span>
                                    </div>
                                </div>
                            </div>
                            <div class="node-connections">
                                <div class="connection-line vertical"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Level 2: RW (Rukun Warga) -->
                <div class="structure-level level-2 mb-16 gemini-element" data-delay="600">
                    <div class="flex flex-col md:flex-row justify-center md:space-x-8 space-y-4 md:space-y-0">
                        <div class="structure-node node-rw group gemini-node" data-node-delay="0">
                            <div class="node-content">
                                <div class="node-avatar">
                                    <div class="avatar-ring rw-ring"></div>
                                    <div class="avatar-inner">
                                        <span class="text-xl">🏘️</span>
                                    </div>
                                    <div class="node-stats-3d">
                                        <span class="stats-badge">5 RT</span>
                                    </div>
                                </div>
                                <div class="node-info">
                                    <h3 class="node-title">RW 01</h3>
                                    <p class="node-subtitle">Rukun Warga</p>
                                    <div class="node-stats">
                                        <span class="stat-item">945 Jiwa</span>
                                    </div>
                                </div>
                            </div>
                            <div class="node-connections">
                                <div class="connection-line vertical"></div>
                            </div>
                        </div>
                        
                        <div class="structure-node node-rw group gemini-node" data-node-delay="100">
                            <div class="node-content">
                                <div class="node-avatar">
                                    <div class="avatar-ring rw-ring"></div>
                                    <div class="avatar-inner">
                                        <span class="text-xl">🏘️</span>
                                    </div>
                                    <div class="node-stats-3d">
                                        <span class="stats-badge">4 RT</span>
                                    </div>
                                </div>
                                <div class="node-info">
                                    <h3 class="node-title">RW 02</h3>
                                    <p class="node-subtitle">Rukun Warga</p>
                                    <div class="node-stats">
                                        <span class="stat-item">876 Jiwa</span>
                                    </div>
                                </div>
                            </div>
                            <div class="node-connections">
                                <div class="connection-line vertical"></div>
                            </div>
                        </div>

                        <div class="structure-node node-rw group gemini-node" data-node-delay="200">
                            <div class="node-content">
                                <div class="node-avatar">
                                    <div class="avatar-ring rw-ring"></div>
                                    <div class="avatar-inner">
                                        <span class="text-xl">🏘️</span>
                                    </div>
                                    <div class="node-stats-3d">
                                        <span class="stats-badge">6 RT</span>
                                    </div>
                                </div>
                                <div class="node-info">
                                    <h3 class="node-title">RW 03</h3>
                                    <p class="node-subtitle">Rukun Warga</p>
                                    <div class="node-stats">
                                        <span class="stat-item">1,026 Jiwa</span>
                                    </div>
                                </div>
                            </div>
                            <div class="node-connections">
                                <div class="connection-line vertical"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Level 3: RT (Rukun Tetangga) -->
                <div class="structure-level level-3 mb-16 gemini-element" data-delay="800">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-6 max-w-5xl mx-auto">
                        @php
                            $rtData = [
                                ['name' => 'RT 01', 'kk' => 25, 'jiwa' => 89],
                                ['name' => 'RT 02', 'kk' => 30, 'jiwa' => 112],
                                ['name' => 'RT 03', 'kk' => 28, 'jiwa' => 98],
                                ['name' => 'RT 04', 'kk' => 22, 'jiwa' => 76],
                                ['name' => 'RT 05', 'kk' => 35, 'jiwa' => 124]
                            ];
                        @endphp
                        
                        @foreach($rtData as $index => $rt)
                        <div class="structure-node node-rt group gemini-node" data-node-delay="{{ $index * 100 }}">
                            <div class="node-content">
                                <div class="node-avatar">
                                    <div class="avatar-ring rt-ring"></div>
                                    <div class="avatar-inner">
                                        <span class="text-lg">🏠</span>
                                    </div>
                                    <div class="node-stats-3d">
                                        <span class="stats-badge">{{ $rt['kk'] }} KK</span>
                                    </div>
                                </div>
                                <div class="node-info">
                                    <h3 class="node-title">{{ $rt['name'] }}</h3>
                                    <p class="node-subtitle">Rukun Tetangga</p>
                                    <div class="node-stats">
                                        <span class="stat-item">{{ $rt['jiwa'] }} Jiwa</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Level 4: Koordinator Bidang -->
                <div class="structure-level level-4 mb-16 gemini-element" data-delay="1000">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 max-w-6xl mx-auto">
                        @php
                            $koordinatorData = [
                                ['title' => 'Koordinator', 'subtitle' => 'Bidang Ekonomi', 'stat' => '156 UMKM', 'icon' => '💼'],
                                ['title' => 'Koordinator', 'subtitle' => 'Bidang Pendidikan', 'stat' => '12 Sekolah', 'icon' => '📚'],
                                ['title' => 'Koordinator', 'subtitle' => 'Bidang Kesehatan', 'stat' => '8 Posyandu', 'icon' => '🏥'],
                                ['title' => 'Koordinator', 'subtitle' => 'Bidang Lingkungan', 'stat' => '23 Program', 'icon' => '🌱']
                            ];
                        @endphp
                        
                        @foreach($koordinatorData as $index => $koordinator)
                        <div class="structure-node node-koordinator group gemini-node" data-node-delay="{{ $index * 100 }}">
                            <div class="node-content">
                                <div class="node-avatar">
                                    <div class="avatar-ring koordinator-ring"></div>
                                    <div class="avatar-inner">
                                        <span class="text-lg">{{ $koordinator['icon'] }}</span>
                                    </div>
                                    <div class="node-stats-3d">
                                        <span class="stats-badge">{{ explode(' ', $koordinator['stat'])[0] }}</span>
                                    </div>
                                </div>
                                <div class="node-info">
                                    <h3 class="node-title">{{ $koordinator['title'] }}</h3>
                                    <p class="node-subtitle">{{ $koordinator['subtitle'] }}</p>
                                    <div class="node-stats">
                                        <span class="stat-item">{{ $koordinator['stat'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Level 5: Tim Digital -->
                <div class="structure-level level-5 gemini-element" data-delay="1200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                        @php
                            $digitalData = [
                                ['title' => 'Tim IT', 'subtitle' => 'Teknologi Informasi', 'stat' => '15 Sistem', 'icon' => '💻'],
                                ['title' => 'Tim Media', 'subtitle' => 'Sosial & Komunikasi', 'stat' => '8 Platform', 'icon' => '📱'],
                                ['title' => 'Tim Data', 'subtitle' => 'Analytics & Monitoring', 'stat' => '24/7 Monitor', 'icon' => '📊']
                            ];
                        @endphp
                        
                        @foreach($digitalData as $index => $digital)
                        <div class="structure-node node-digital group gemini-node" data-node-delay="{{ $index * 100 }}">
                            <div class="node-content">
                                <div class="node-avatar">
                                    <div class="avatar-ring digital-ring"></div>
                                    <div class="avatar-inner">
                                        <span class="text-lg">{{ $digital['icon'] }}</span>
                                    </div>
                                    <div class="node-stats-3d">
                                        <span class="stats-badge">{{ explode(' ', $digital['stat'])[0] }}</span>
                                    </div>
                                </div>
                                <div class="node-info">
                                    <h3 class="node-title">{{ $digital['title'] }}</h3>
                                    <p class="node-subtitle">{{ $digital['subtitle'] }}</p>
                                    <div class="node-stats">
                                        <span class="stat-item">{{ $digital['stat'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Seamless Section Background */
    .seamless-section-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }

    .dark .seamless-section-bg {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
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

    /* 3D Floating Elements */
    .floating-cube {
        position: absolute;
        width: 40px;
        height: 40px;
        background: linear-gradient(45deg, #3b82f6, #8b5cf6);
        border-radius: 8px;
        animation: float3D 8s ease-in-out infinite;
        opacity: 0.1;
    }

    .floating-sphere {
        position: absolute;
        width: 30px;
        height: 30px;
        background: radial-gradient(circle, #10b981, #06b6d4);
        border-radius: 50%;
        animation: float3D 10s ease-in-out infinite reverse;
        opacity: 0.15;
    }

    .cube-1 { top: 10%; left: 10%; animation-delay: 0s; }
    .cube-2 { top: 60%; right: 15%; animation-delay: 2s; }
    .cube-3 { bottom: 20%; left: 20%; animation-delay: 4s; }
    .sphere-1 { top: 30%; right: 10%; animation-delay: 1s; }
    .sphere-2 { bottom: 40%; right: 30%; animation-delay: 3s; }

    @keyframes float3D {
        0%, 100% { 
            transform: translateY(0px) rotateX(0deg) rotateY(0deg); 
        }
        25% { 
            transform: translateY(-20px) rotateX(90deg) rotateY(90deg); 
        }
        50% { 
            transform: translateY(-10px) rotateX(180deg) rotateY(180deg); 
        }
        75% { 
            transform: translateY(-15px) rotateX(270deg) rotateY(270deg); 
        }
    }

    /* 3D Statistics Cards */
    .stats-3d-card {
        perspective: 1000px;
        height: 280px;
    }

    .stats-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        transform-style: preserve-3d;
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .stats-3d-card:hover .stats-card-inner {
        transform: rotateY(10deg) rotateX(5deg) translateZ(20px);
    }

    .stats-3d-bg {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        box-shadow: 
            0 20px 60px rgba(0, 0, 0, 0.1),
            0 0 0 1px rgba(255, 255, 255, 0.1) inset;
        transition: all 0.4s ease;
    }

    .dark .stats-3d-bg {
        background: rgba(0, 0, 0, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 
            0 20px 60px rgba(0, 0, 0, 0.3),
            0 0 0 1px rgba(255, 255, 255, 0.05) inset;
    }

    .stats-content {
        position: relative;
        z-index: 2;
        padding: 2rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    /* 3D Icon Container */
    .stats-icon-container {
        perspective: 200px;
        margin-bottom: 1.5rem;
    }

    .stats-icon-3d {
        position: relative;
        width: 80px;
        height: 80px;
        transform-style: preserve-3d;
        animation: iconRotate 6s linear infinite;
    }

    .icon-face {
        position: absolute;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 51, 234, 0.1));
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .icon-front { transform: translateZ(40px); }
    .icon-back { transform: translateZ(-40px) rotateY(180deg); }
    .icon-right { transform: rotateY(90deg) translateZ(40px); }
    .icon-left { transform: rotateY(-90deg) translateZ(40px); }
    .icon-top { transform: rotateX(90deg) translateZ(40px); }
    .icon-bottom { transform: rotateX(-90deg) translateZ(40px); }

    @keyframes iconRotate {
        0% { transform: rotateX(0deg) rotateY(0deg); }
        25% { transform: rotateX(90deg) rotateY(90deg); }
        50% { transform: rotateX(180deg) rotateY(180deg); }
        75% { transform: rotateX(270deg) rotateY(270deg); }
        100% { transform: rotateX(360deg) rotateY(360deg); }
    }

    /* Stats Value */
    .stats-value-container {
        margin-bottom: 1.5rem;
    }

    .stats-value {
        font-size: 2.5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
        text-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
    }

    .stats-label {
        font-size: 1rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .dark .stats-label {
        color: #9ca3af;
    }

    /* 3D Progress Bar */
    .stats-progress-3d {
        width: 100%;
        perspective: 100px;
    }

    .progress-track {
        width: 100%;
        height: 8px;
        background: rgba(0, 0, 0, 0.1);
        border-radius: 4px;
        overflow: hidden;
        transform: rotateX(45deg);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #06b6d4);
        border-radius: 4px;
        width: 0%;
        transition: width 2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
    }

    /* Holographic Effect */
    .holographic-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(45deg, 
            transparent 30%, 
            rgba(255, 255, 255, 0.1) 50%, 
            transparent 70%);
        border-radius: 24px;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .stats-3d-card:hover .holographic-overlay {
        opacity: 1;
        animation: holographicSweep 2s ease-in-out infinite;
    }

    @keyframes holographicSweep {
        0% { transform: translateX(-100%) skewX(-15deg); }
        100% { transform: translateX(200%) skewX(-15deg); }
    }

    /* Color Variants */
    .stats-3d-card[data-color="blue"] .stats-value {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .stats-3d-card[data-color="green"] .stats-value {
        background: linear-gradient(135deg, #10b981, #047857);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .stats-3d-card[data-color="purple"] .stats-value {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .stats-3d-card[data-color="orange"] .stats-value {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .stats-3d-card[data-color="cyan"] .stats-value {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .stats-3d-card[data-color="pink"] .stats-value {
        background: linear-gradient(135deg, #ec4899, #be185d);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Professional Gemini Effects */
    .gemini-element {
        opacity: 0;
        transform: translateY(60px) scale(0.95);
        transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        will-change: transform, opacity;
    }

    .gemini-element.visible {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .gemini-node {
        opacity: 0;
        transform: translateY(40px) scale(0.9);
        transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        will-change: transform, opacity;
    }

    .gemini-node.visible {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    /* Structure Nodes */
    .structure-node {
        position: relative;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .node-content {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 1.5rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 
            0 10px 40px rgba(0, 0, 0, 0.08),
            0 0 0 1px rgba(255, 255, 255, 0.1) inset;
        transition: all 0.4s ease;
    }

    .dark .node-content {
        background: rgba(0, 0, 0, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 
            0 10px 40px rgba(0, 0, 0, 0.3),
            0 0 0 1px rgba(255, 255, 255, 0.05) inset;
    }

    .structure-node:hover .node-content {
        transform: translateY(-8px);
        box-shadow: 
            0 20px 60px rgba(0, 0, 0, 0.12),
            0 0 0 1px rgba(255, 255, 255, 0.2) inset;
    }

    .dark .structure-node:hover .node-content {
        box-shadow: 
            0 20px 60px rgba(0, 0, 0, 0.4),
            0 0 0 1px rgba(255, 255, 255, 0.1) inset;
    }

    /* Node Avatars */
    .node-avatar {
        position: relative;
        width: 80px;
        height: 80px;
        margin: 0 auto 1rem;
    }

    .avatar-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: conic-gradient(from 0deg, #3b82f6, #8b5cf6, #06b6d4, #3b82f6);
        padding: 3px;
        animation: rotate 4s linear infinite;
    }

    .rw-ring {
        background: conic-gradient(from 0deg, #10b981, #34d399, #6ee7b7, #10b981);
    }

    .rt-ring {
        background: conic-gradient(from 0deg, #f59e0b, #fbbf24, #fcd34d, #f59e0b);
    }

    .koordinator-ring {
        background: conic-gradient(from 0deg, #8b5cf6, #a78bfa, #c4b5fd, #8b5cf6);
    }

    .digital-ring {
        background: conic-gradient(from 0deg, #ef4444, #f87171, #fca5a5, #ef4444);
    }

    .avatar-inner {
        background: white;
        border-radius: 50%;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
    }

    .dark .avatar-inner {
        background: #1f2937;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* 3D Stats Badge */
    .node-stats-3d {
        position: absolute;
        top: -8px;
        right: -8px;
        z-index: 10;
    }

    .stats-badge {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        transform: translateZ(10px);
        animation: badgePulse 2s ease-in-out infinite;
    }

    @keyframes badgePulse {
        0%, 100% { transform: translateZ(10px) scale(1); }
        50% { transform: translateZ(15px) scale(1.05); }
    }

    /* Node Info */
    .node-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .dark .node-title {
        color: #f9fafb;
    }

    .node-subtitle {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.75rem;
    }

    .dark .node-subtitle {
        color: #9ca3af;
    }

    .node-stats {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
    }

    .stat-item {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .dark .stat-item {
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
    }

    /* Connection Lines */
    .node-connections {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        z-index: -1;
    }

    .connection-line {
        background: linear-gradient(to bottom, #3b82f6, transparent);
        opacity: 0.3;
    }

    .connection-line.vertical {
        width: 2px;
        height: 60px;
    }

    .dark .connection-line {
        background: linear-gradient(to bottom, #60a5fa, transparent);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .stats-3d-card {
            height: 240px;
        }
        
        .stats-content {
            padding: 1.5rem;
        }
        
        .stats-icon-3d {
            width: 60px;
            height: 60px;
        }
        
        .icon-face {
            width: 60px;
            height: 60px;
        }
        
        .stats-value {
            font-size: 2rem;
        }
        
        .node-avatar {
            width: 60px;
            height: 60px;
        }

        .node-content {
            padding: 1rem;
        }

        .node-title {
            font-size: 1rem;
        }

        .node-subtitle {
            font-size: 0.75rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Professional Gemini Scroll Animation
    const geminiElements = document.querySelectorAll('.gemini-element');
    const geminiNodes = document.querySelectorAll('.gemini-node');
    
    // Intersection Observer for main elements
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                const delay = parseInt(element.dataset.delay) || 0;
                
                setTimeout(() => {
                    element.classList.add('visible');
                    
                    // Animate child nodes with staggered timing
                    const childNodes = element.querySelectorAll('.gemini-node');
                    childNodes.forEach((node, index) => {
                        const nodeDelay = parseInt(node.dataset.nodeDelay) || 0;
                        setTimeout(() => {
                            node.classList.add('visible');
                        }, nodeDelay);
                    });
                    
                }, delay);
                
                // Unobserve after animation
                observer.unobserve(element);
            }
        });
    }, observerOptions);
    
    // Observe all gemini elements
    geminiElements.forEach(element => {
        observer.observe(element);
    });
    
    // Counter Animation for Stats
    function animateCounter(element, target) {
        const isPercentage = target.includes('%');
        const numericTarget = parseInt(target.replace(/[^\d]/g, ''));
        let current = 0;
        const increment = numericTarget / 60; // 60 frames for smooth animation
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= numericTarget) {
                current = numericTarget;
                clearInterval(timer);
            }
            
            const displayValue = Math.floor(current);
            element.textContent = isPercentage ? displayValue + '%' : displayValue.toLocaleString();
        }, 16); // ~60fps
    }
    
    // Progress Bar Animation
    function animateProgressBar(element, targetProgress) {
        setTimeout(() => {
            element.style.width = targetProgress + '%';
        }, 500);
    }
    
    // Initialize stats animations when visible
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const card = entry.target;
                const valueElement = card.querySelector('.stats-value');
                const progressElement = card.querySelector('.progress-fill');
                
                if (valueElement) {
                    const target = valueElement.dataset.target;
                    animateCounter(valueElement, target);
                }
                
                if (progressElement) {
                    const progress = progressElement.dataset.progress;
                    animateProgressBar(progressElement, progress);
                }
                
                statsObserver.unobserve(card);
            }
        });
    }, { threshold: 0.5 });
    
    // Observe stats cards
    document.querySelectorAll('.stats-3d-card').forEach(card => {
        statsObserver.observe(card);
    });
    
    // Enhanced hover interactions
    function initNodeInteractions() {
        const nodes = document.querySelectorAll('.structure-node');
        
        nodes.forEach(node => {
            node.addEventListener('mouseenter', function() {
                // Subtle scale effect
                this.style.transform = 'scale(1.02)';
                
                // Add glow effect
                const content = this.querySelector('.node-content');
                content.style.boxShadow = `
                    0 20px 60px rgba(59, 130, 246, 0.15),
                    0 0 0 1px rgba(255, 255, 255, 0.2) inset,
                    0 0 40px rgba(59, 130, 246, 0.1)
                `;
            });
            
            node.addEventListener('mouseleave', function() {
                this.style.transform = '';
                
                const content = this.querySelector('.node-content');
                content.style.boxShadow = '';
            });
            
            // Click animation
            node.addEventListener('click', function() {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
    }
    
    // 3D Stats Card Interactions
    function init3DStatsInteractions() {
        const statsCards = document.querySelectorAll('.stats-3d-card');
        
        statsCards.forEach(card => {
            card.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;
                
                const cardInner = this.querySelector('.stats-card-inner');
                cardInner.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(20px)`;
            });
            
            card.addEventListener('mouseleave', function() {
                const cardInner = this.querySelector('.stats-card-inner');
                cardInner.style.transform = '';
            });
        });
    }
    
    // Initialize interactions
    initNodeInteractions();
    init3DStatsInteractions();
    
    console.log('Professional Village Structure with 3D Stats initialized');
});
</script>