 <aside class="sidebar" id="sidebar">
            <!-- Enhanced 3D Logo -->
            <div class="sidebar-logo">
                <i class="bi bi-globe-asia-australia" style="color: #3b82f6;"></i>
            </div>

            <!-- Enhanced Navigation Icons with Labels -->
            <div class="nav-icons">
                <a href="{{ url('/dashboard') }}" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-speedometer2" style="color: #3b82f6;"></i>
                    </div>
                    <span class="nav-label">Dashboard</span>
                    <div class="nav-tooltip">Dashboard</div>
                </a>

                <a href="#" class="nav-item">
                    <div class="nav-icon">
                        <i class="bi bi-people" style="color: #10b981;"></i>
                    </div>
                    <span class="nav-label">Penduduk</span>
                    <div class="nav-tooltip">Data Penduduk</div>
                </a>

                <a href="#" class="nav-item">
                    <div class="nav-icon">
                        <i class="bi bi-houses" style="color: #f59e42;"></i>
                    </div>
                    <span class="nav-label">RT & RW</span>
                    <div class="nav-tooltip">Manajemen RT & RW</div>
                </a>

                <a href="#" class="nav-item">
                    <div class="nav-icon">
                        <i class="bi bi-shop" style="color: #fbbf24;"></i>
                    </div>
                    <span class="nav-label">UMKM</span>
                    <div class="nav-tooltip">Data UMKM</div>
                </a>

                <a href="#" class="nav-item">
                    <div class="nav-icon">
                        <i class="bi bi-camera" style="color: #ef4444;"></i>
                    </div>
                    <span class="nav-label">Wisata</span>
                    <div class="nav-tooltip">Tempat Wisata</div>
                </a>

                <a href="#" class="nav-item">
                    <div class="nav-icon">
                        <i class="bi bi-newspaper" style="color: #6366f1;"></i>
                    </div>
                    <span class="nav-label">Berita</span>
                    <div class="nav-tooltip">Berita & Artikel</div>
                </a>

                <a href="#" class="nav-item">
                    <div class="nav-icon">
                        <i class="bi bi-award" style="color: #eab308;"></i>
                    </div>
                    <span class="nav-label">Program</span>
                    <div class="nav-tooltip">Program Kampung</div>
                </a>

                <a href="#" class="nav-item">
                    <div class="nav-icon">
                        <i class="bi bi-hammer" style="color: #6d28d9;"></i>
                    </div>
                    <span class="nav-label">Pembangunan</span>
                    <div class="nav-tooltip">Proyek Pembangunan</div>
                </a>

                <a href="#" class="nav-item">
                    <div class="nav-icon">
                        <i class="bi bi-wallet2" style="color: #14b8a6;"></i>
                    </div>
                    <span class="nav-label">Keuangan</span>
                    <div class="nav-tooltip">Manajemen Keuangan</div>
                </a>

                <a href="#" class="nav-item">
                    <div class="nav-icon">
                        <i class="bi bi-graph-up" style="color: #0ea5e9;"></i>
                    </div>
                    <span class="nav-label">Laporan</span>
                    <div class="nav-tooltip">Laporan & Statistik</div>
                </a>

                <a href="#" class="nav-item">
                    <div class="nav-icon">
                        <i class="bi bi-calendar-event" style="color: #f472b6;"></i>
                    </div>
                    <span class="nav-label">Agenda</span>
                    <div class="nav-tooltip">Agenda & Kegiatan</div>
                </a>

                <a href="#" class="nav-item">
                    <div class="nav-icon">
                        <i class="bi bi-chat-dots" style="color: #f87171;"></i>
                    </div>
                    <span class="nav-label">Pesan</span>
                    <div class="nav-tooltip">Pesan & Komunikasi</div>
                </a>

                <a href="#" class="nav-item">
                    <div class="nav-icon">
                        <i class="bi bi-file-text" style="color: #64748b;"></i>
                    </div>
                    <span class="nav-label">Dokumen</span>
                    <div class="nav-tooltip">Dokumen & Arsip</div>
                </a>
            </div>

            <!-- Enhanced Settings with Label -->
            <a href="#" class="settings-item">
                <div class="settings-icon">
                    <i class="bi bi-gear" style="color: #a3e635;"></i>
                </div>
                <span class="nav-label">Pengaturan</span>
                <div class="settings-tooltip">Pengaturan Sistem</div>
            </a>
        </aside>