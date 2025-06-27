<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Kampung Digital') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0/dist/chart.umd.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @vite(['resources/css/layouts.css', 'resources/js/layouts.js'])

</head>

<body>
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <div class="app-container">
        <!-- Reduced Size Hover-to-Expand Sidebar -->
       @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="main-content">
            <!-- Reduced Header Size -->
            <header class="header">
                <div class="header-left">
                    <!-- Mobile Menu Button -->
                    <button class="mobile-menu-btn" id="mobileMenuBtn">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="brand-title">Kampung Digital</span>
                </div>

                <!-- Centered Search Bar -->
                <div class="header-center">
                    <div class="search-container" style="width:100%;">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="Cari data penduduk, UMKM, berita, atau informasi lainnya...">
                    </div>
                </div>

                <div class="header-right">
                    <button class="header-btn">
                        <i class="bi bi-bell"></i>
                    </button>

                    <!-- Theme Toggle Button -->
                    <button class="theme-toggle" id="themeToggle">
                        <i class="bi bi-sun" id="themeIcon"></i>
                    </button>

                    @auth
                    <!-- FIXED Profile Dropdown -->
                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="profile-trigger" id="profileTrigger">
                            <div class="user-info-avatar">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="user-info-text">{{ Auth::user()->name }}</span>
                            <i class="bi bi-chevron-down dropdown-arrow"></i>
                        </div>
                        <div class="dropdown-menu" id="dropdownMenu">
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-person"></i>
                                <span>Profile Saya</span>
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-gear"></i>
                                <span>Pengaturan</span>
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-question-circle"></i>
                                <span>Bantuan</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                @auth
                    <!-- Enhanced Welcome Banner with Pattern Background -->
                    <div class="welcome-banner">
                        <div class="welcome-content">
                            <div class="welcome-icon">
                                👋
                            </div>
                            <div class="welcome-text">
                                <h2>Selamat Datang, {{ Auth::user()->name }}!</h2>
                                <p>Berikut adalah ringkasan data terkini dari sistem Kampung Digital Anda.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <!-- Enhanced Total Saldo dengan Detail -->
                        <div class="stat-card saldo-card">
                            <div class="stat-header">
                                <div class="stat-icon-3d icon-wallet">
                                    <i class="bi bi-wallet2"></i>
                                </div>
                                <span class="stat-change">+12.5%</span>
                            </div>
                            <div class="stat-value">Rp {{ number_format(15000000, 0, ',', '.') }}</div>
                            <div class="stat-label">Total Saldo</div>
                            <div class="saldo-breakdown">
                                <div class="saldo-item">
                                    <span class="saldo-label">Kas Desa:</span>
                                    <span class="saldo-amount">Rp 12.5M</span>
                                </div>
                                <div class="saldo-item">
                                    <span class="saldo-label">Dana Bantuan:</span>
                                    <span class="saldo-amount">Rp 2.5M</span>
                                </div>
                            </div>
                        </div>

                        <!-- Total Penduduk -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon-3d icon-people">
                                    <i class="bi bi-people"></i>
                                </div>
                                <span class="stat-change">+2.1%</span>
                            </div>
                            <div class="stat-value">2,430</div>
                            <div class="stat-label">Total Penduduk</div>
                            <div class="stat-sublabel">Jiwa terdaftar</div>
                        </div>

                        <!-- RT & RW -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon-3d icon-houses">
                                    <i class="bi bi-houses"></i>
                                </div>
                                <span class="stat-change warning">Stabil</span>
                            </div>
                            <div class="stat-value">30</div>
                            <div class="stat-label">RT & RW</div>
                            <div class="rt-rw-details">
                                <div class="rt-rw-item">
                                    <div class="rt-rw-count">18</div>
                                    <div class="rt-rw-label">RT</div>
                                </div>
                                <div class="rt-rw-item">
                                    <div class="rt-rw-count">12</div>
                                    <div class="rt-rw-label">RW</div>
                                </div>
                            </div>
                        </div>

                        <!-- UMKM Aktif -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon-3d icon-shop">
                                    <i class="bi bi-shop"></i>
                                </div>
                                <span class="stat-change">+8.3%</span>
                            </div>
                            <div class="stat-value">45</div>
                            <div class="stat-label">UMKM Aktif</div>
                            <div class="stat-sublabel">Usaha terdaftar</div>
                        </div>

                        <!-- Tempat Wisata -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon-3d icon-camera">
                                    <i class="bi bi-camera"></i>
                                </div>
                                <span class="stat-change">+16.7%</span>
                            </div>
                            <div class="stat-value">12</div>
                            <div class="stat-label">Tempat Wisata</div>
                            <div class="stat-sublabel">Destinasi aktif</div>
                        </div>

                        <!-- Lembaga Pendidikan -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon-3d icon-education">
                                    <i class="bi bi-mortarboard"></i>
                                </div>
                                <span class="stat-change warning">Stabil</span>
                            </div>
                            <div class="stat-value">8</div>
                            <div class="stat-label">Lembaga Pendidikan</div>
                            <div class="stat-sublabel">Sekolah & kampus</div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="charts-section">
                        <!-- Monthly Chart -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Statistik Bulanan</h3>
                                <select class="chart-select">
                                    <option>7 hari terakhir</option>
                                    <option>30 hari terakhir</option>
                                    <option>3 bulan terakhir</option>
                                </select>
                            </div>
                            <div class="chart-container">
                                <canvas id="monthlyChart"></canvas>
                            </div>
                        </div>

                        <!-- Gender Chart -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Distribusi Gender</h3>
                            </div>
                            <div class="chart-container">
                                <canvas id="genderChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="activity-card">
                        <h3 class="chart-title" style="margin-bottom: 0.875rem;">Aktivitas Terbaru</h3>

                        <div class="activity-item">
                            <div class="activity-icon icon-wallet">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Penduduk baru terdaftar</div>
                                <div class="activity-subtitle">Atas nama Budi Santoso • 5 menit yang lalu</div>
                            </div>
                            <span class="activity-badge">Baru</span>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon icon-shop">
                                <i class="bi bi-shop"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">UMKM baru didaftarkan</div>
                                <div class="activity-subtitle">Warung Makan Sederhana • 2 jam yang lalu</div>
                            </div>
                            <span class="activity-badge">UMKM</span>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon icon-camera">
                                <i class="bi bi-newspaper"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Berita baru dipublikasikan</div>
                                <div class="activity-subtitle">Program Bantuan Sosial 2024 • 1 hari yang lalu</div>
                            </div>
                            <span class="activity-badge">Berita</span>
                        </div>
                    </div>
                @else
                    @yield('content')
                @endauth
            </div>
        </main>
    </div>
</body>
</html>
