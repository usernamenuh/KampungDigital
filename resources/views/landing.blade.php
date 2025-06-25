<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kampung Digital - Transformasi Digital Indonesia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* FORCE WHITE BACKGROUND IN LIGHT MODE */
        html {
            scroll-behavior: smooth;
            background-color: #ffffff !important;
        }
        
        body {
            background-color: #ffffff !important;
            transition: background-color 0.3s ease, color 0.3s ease;
            padding-top: 0;
        }
        
        /* Dark mode backgrounds */
        .dark html {
            background-color: #0f172a !important;
        }
        
        .dark body {
            background-color: #0f172a !important;
        }
        
        /* Force all sections to have proper backgrounds */
        section {
            background-color: transparent;
        }
        
        .dark section {
            background-color: transparent;
        }
        
        /* Override any conflicting backgrounds */
        .seamless-section-bg {
            background-color: #ffffff !important;
            transition: background-color 0.3s ease;
        }
        
        .dark .seamless-section-bg {
            background-color: #0f172a !important;
        }
        
        /* Active nav link styling */
        .nav-link.active {
            color: #3b82f6 !important;
            background: rgba(59, 130, 246, 0.1);
        }
        
        /* Ensure main content has proper background */
        main {
            background-color: transparent;
        }
        
        /* Fix any gradient overlays that might cause dark backgrounds */
        .bg-gradient-to-br {
            background: transparent !important;
        }
        
        .dark .bg-gradient-to-br {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
        }
        
        /* Ensure hero section is white in light mode */
        #home {
            background-color: #ffffff !important;
        }
        
        .dark #home {
            background-color: #0f172a !important;
        }
        
        /* Ensure features section is white in light mode */
        #features {
            background-color: #ffffff !important;
        }
        
        .dark #features {
            background-color: #0f172a !important;
        }
        
        /* Text colors for light mode */
        body {
            color: #111827;
        }
        
        .dark body {
            color: #f9fafb;
        }
        
        /* Remove any conflicting background gradients in light mode */
        .premium-orb {
            opacity: 0.3;
        }
        
        .dark .premium-orb {
            opacity: 0.7;
        }
        
        /* Ensure grid background is subtle in light mode */
        .seamless-grid {
            opacity: 0.1;
        }
        
        .dark .seamless-grid {
            opacity: 0.2;
        }
    </style>
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-300">
    
    @php
        // Navigation configuration
        $navItems = [
            ['name' => 'Home', 'link' => '#home'],
            ['name' => 'Fitur', 'link' => '#features'],
            ['name' => 'News', 'link' => '#news'],
            ['name' => 'Struktur', 'link' => '#village-structure'],
            ['name' => 'FAQ', 'link' => '#faq'],
        ];

        $logo = [
            'text' => '', // Removed text
            'icon' => 'KD', // Keep as fallback
            'image' => '', // Ready for image URL
            'href' => '#home'
        ];

        $dropdownItems = [
            [
                'name' => 'Login',
                'href' => '#', // Ganti dengan route login kamu
                'target' => '_self',
                'icon' => '<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3m0 0l4-4m-4 4l4 4m13-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            ],
            [
                'name' => 'Register',
                'href' => '#', // Ganti dengan route register kamu
                'target' => '_self',
                'icon' => '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>'
            ]
        ];

        $socialLinks = [
            [
                'name' => 'Facebook',
                'href' => 'https://facebook.com/groups/programmerhandal',
                'target' => '_blank',
                'icon' => '<svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'
            ],
            [
                'name' => 'Discord',
                'href' => 'https://discord.com',
                'target' => '_blank',
                'icon' => '<svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419-.0189 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1568 2.4189Z"/></svg>'
            ]
        ];

        // Features data
        $features = [
            [
                'icon' => '🏘️',
                'title' => 'Manajemen Desa Digital',
                'description' => 'Sistem informasi terintegrasi untuk administrasi dan pelayanan desa yang efisien dan transparan',
                'gradient' => 'from-blue-500 to-indigo-600',
                'benefits' => [
                    'Administrasi paperless',
                    'Pelayanan online 24/7',
                    'Transparansi anggaran',
                    'Monitoring real-time'
                ]
            ],
            [
                'icon' => '💰',
                'title' => 'E-Commerce & Fintech',
                'description' => 'Platform jual beli online terintegrasi dengan sistem pembayaran digital untuk UMKM lokal',
                'gradient' => 'from-green-500 to-emerald-600',
                'benefits' => [
                    'Marketplace terintegrasi',
                    'Payment gateway',
                    'Inventory management',
                    'Analytics penjualan'
                ]
            ],
            [
                'icon' => '📚',
                'title' => 'Edukasi & Pelatihan',
                'description' => 'Program pembelajaran digital dan pelatihan keterampilan untuk meningkatkan SDM desa',
                'gradient' => 'from-purple-500 to-pink-600',
                'benefits' => [
                    'E-learning platform',
                    'Sertifikasi digital',
                    'Mentoring online',
                    'Skill assessment'
                ]
            ]
        ];

        // News data - Updated for BentoGrid layout
        $news = [
            [
                'title' => 'Kampung Digital Raih Inovasi Terbaik 2024',
                'excerpt' => 'Program Kampung Digital berhasil meraih penghargaan sebagai inovasi terbaik dalam bidang transformasi digital desa dari Kementerian Desa PDTT.',
                'image' => asset('assets/inovasi.jpg'),
                'category' => 'Penghargaan',
                'category_color' => 'blue',
                'date' => '15 Des 2024',
                'author' => 'Tim Redaksi',
                'author_initials' => 'TR',
                'author_gradient' => 'from-blue-500 to-indigo-600',
                'read_time' => '3 min read',
                'link' => '#news-1',
                'icon' => '🏆'
            ],
            [
                'title' => '500 Desa Baru Bergabung Program Digitalisasi',
                'excerpt' => 'Antusiasme tinggi dari berbagai daerah membuat program Kampung Digital terus berkembang dengan 500 desa baru yang bergabung bulan ini.',
                'image' => asset('assets/desa.jpg'),
                'category' => 'Ekspansi',
                'category_color' => 'green',
                'date' => '12 Des 2024',
                'author' => 'Sarah Wijaya',
                'author_initials' => 'SW',
                'author_gradient' => 'from-green-500 to-emerald-600',
                'read_time' => '4 min read',
                'link' => '#news-2',
                'icon' => '🌱'
            ],
            [
                'title' => 'Pelatihan Digital Marketing untuk UMKM Desa',
                'excerpt' => 'Program pelatihan digital marketing khusus UMKM desa telah dimulai dengan target 1000 peserta dari seluruh Indonesia.',
                'image' => asset('assets/peletihan.jpg'),
                'category' => 'Pelatihan',
                'category_color' => 'purple',
                'date' => '10 Des 2024',
                'author' => 'Ahmad Fauzi',
                'author_initials' => 'AF',
                'author_gradient' => 'from-purple-500 to-pink-600',
                'read_time' => '5 min read',
                'link' => '#news-3',
                'icon' => '📚'
            ],
            [
                'title' => 'Kolaborasi dengan Bank Indonesia untuk Fintech Desa',
                'excerpt' => 'Kerjasama strategis dengan Bank Indonesia membuka peluang pengembangan layanan fintech yang lebih komprehensif untuk desa. Program ini akan menghadirkan solusi pembayaran digital yang mudah diakses oleh seluruh masyarakat desa.',
                'image' => asset('assets/bank.jpg'),
                'category' => 'Kerjasama',
                'category_color' => 'orange',
                'date' => '8 Des 2024',
                'author' => 'Rina Sari',
                'author_initials' => 'RS',
                'author_gradient' => 'from-orange-500 to-red-600',
                'read_time' => '6 min read',
                'link' => '#news-4',
                'icon' => '🤝'
            ],
            [
                'title' => 'Implementasi AI untuk Smart Village Analytics',
                'excerpt' => 'Teknologi AI mulai diimplementasikan untuk memberikan insights yang lebih mendalam tentang perkembangan dan kebutuhan desa.',
                'image' => asset('assets/Untitled.webp'),
                'category' => 'Teknologi',
                'category_color' => 'cyan',
                'date' => '5 Des 2024',
                'author' => 'Dr. Budi Santoso',
                'author_initials' => 'BS',
                'author_gradient' => 'from-cyan-500 to-blue-600',
                'read_time' => '7 min read',
                'link' => '#news-5',
                'icon' => '🤖'
            ],
            [
                   'title' => 'Digitalisasi Sekolah Agama di Desa',
                'excerpt' => 'Program digitalisasi untuk sekolah agama, menghadirkan pembelajaran daring, administrasi modern, dan akses literasi digital bagi santri dan guru di desa.',
                'image' => asset('assets/sa.webp'), 
                'category' => 'Pendidikan',
                'category_color' => 'yellow',
                'date' => '3 Des 2024',
                'author' => 'Ust. Ahmad Syafi\'i',
                'author_initials' => 'AS',
                'author_gradient' => 'from-yellow-500 to-amber-600',
                'read_time' => '4 min read',
                'link' => '#news-6',
                'icon' => '🕌'
            ],
            [
                'title' => 'Program Beasiswa Digital untuk Pemuda Desa Berprestasi',
                'excerpt' => 'Kampung Digital meluncurkan program beasiswa untuk pemuda desa yang ingin mengembangkan kemampuan teknologi dan digital marketing. Program ini akan memberikan kesempatan belajar gratis selama 6 bulan dengan mentor berpengalaman.',
                'image' =>  asset('assets/prestasi.jpg'),   
                'category' => 'Beasiswa',
                'category_color' => 'indigo',
                'date' => '1 Des 2024',
                'author' => 'Prof. Dewi Sartika',
                'author_initials' => 'DS',
                'author_gradient' => 'from-indigo-500 to-purple-600',
                'read_time' => '5 min read',
                'link' => '#news-7',
                'icon' => '🎓'
            ]
        ];

        // FAQ data
        $faqs = [
            [
                'question' => 'Bagaimana cara memulai menggunakan Kampung Digital?',
                'answer' => 'Anda dapat memulai dengan mendaftar melalui website kami, kemudian tim kami akan membantu proses onboarding.'
            ],
            [
                'question' => 'Apakah ada biaya berlangganan?',
                'answer' => 'Kami menyediakan paket gratis untuk fitur dasar, dan paket premium dengan harga terjangkau.'
            ],
            [
                'question' => 'Bagaimana dengan dukungan teknis?',
                'answer' => 'Tim support kami siap membantu 24/7 melalui berbagai channel komunikasi.'
            ]
        ];

        // Add this after the existing PHP variables
        $sectionSpacing = [
            'hero_to_features' => 'pt-8',
            'features_to_news' => 'pt-12',
            'news_to_structure' => 'pt-12',
            'structure_to_faq' => 'pt-12'
        ];
    @endphp

    <!-- Navbar with more bottom spacing -->
    <x-main-navbar 
        :navItems="$navItems"
        :logo="$logo"
        :dropdownItems="$dropdownItems"
        :socialLinks="$socialLinks"
        :showModeToggle="true"
        :scrollEffect="true"
        :sticky="false"
    />

    <!-- Main Content -->
    <main class="space-y-0">
        <!-- Hero Section -->
        <x-hero-section />

        <!-- Features Section -->
        <div class="">
            <x-features-section :features="$features" />
        </div>

        <!-- News Section -->
        <div class="">
            <x-news-section :news="$news" />
        </div>

        <!-- Village Structure Section -->
        <div class="">
            <x-village-structure-section />
        </div>

        <!-- FAQ Section -->
        <div class="">
            <x-faq-section :faqs="$faqs" />
        </div>
    </main>

    <!-- Footer -->
    <x-footer-section />

</body>
</html>
