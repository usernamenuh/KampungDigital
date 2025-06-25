@props([
    'logo' => [
        'text' => 'Kampung Digital',
        'icon' => 'KD'
    ],
    'copyright' => '© 2024 Kampung Digital. Semua hak dilindungi undang-undang.'
])

<style>
    /* Background Options - Choose one */
    
    /* Option 1: Solid Dark Background */
    .footer-bg-solid {
        background: #1e293b;
    }
    
    /* Option 2: Light Background */
    .footer-bg-light {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }
    
    /* Option 3: Blue Gradient Background */
    .footer-bg-blue {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
    }
    
    /* Option 4: Purple Gradient Background */
    .footer-bg-purple {
        background: linear-gradient(135deg, #581c87 0%, #7c3aed 50%, #a855f7 100%);
    }
    
    /* Option 5: Green Gradient Background */
    .footer-bg-green {
        background: linear-gradient(135deg, #065f46 0%, #059669 50%, #10b981 100%);
    }
    
    /* Option 6: Minimal White Background */
    .footer-bg-white {
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
    }

    /* Current Dark Gradient (Original) */
    .footer-bg-dark {
        background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
        position: relative;
        overflow: hidden;
    }

    .footer-bg-dark::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(120, 119, 198, 0.1) 0%, transparent 50%);
        animation: float 25s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-3px); }
    }

    /* Glass card effects */
    .glass-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.2s ease;
        border-radius: 16px;
    }

    .glass-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Light theme adjustments */
    .footer-bg-light .glass-card,
    .footer-bg-white .glass-card {
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(0, 0, 0, 0.1);
        color: #1e293b;
    }

    .footer-bg-light .text-white,
    .footer-bg-white .text-white {
        color: #1e293b !important;
    }

    .footer-bg-light .text-slate-300,
    .footer-bg-light .text-slate-400,
    .footer-bg-white .text-slate-300,
    .footer-bg-white .text-slate-400 {
        color: #64748b !important;
    }

    .social-icon {
        transition: all 0.15s ease;
        backdrop-filter: blur(8px);
    }

    .social-icon:hover {
        transform: translateY(-1px);
        opacity: 0.8;
    }

    .link-hover {
        transition: all 0.15s ease;
        position: relative;
        padding: 2px 0;
    }

    .link-hover::before {
        content: '';
        position: absolute;
        left: 0;
        bottom: 1px;
        width: 0;
        height: 1px;
        background: currentColor;
        transition: width 0.15s ease;
        opacity: 0.5;
    }

    .link-hover:hover::before {
        width: 20px;
    }

    .link-hover:hover {
        transform: translateX(2px);
        opacity: 0.9;
    }

    .contact-item {
        transition: all 0.15s ease;
        padding: 2px 0;
    }

    .contact-item:hover {
        transform: translateX(2px);
        opacity: 0.9;
    }

    .contact-icon {
        transition: all 0.15s ease;
    }

    .contact-item:hover .contact-icon {
        opacity: 0.9;
    }

    .animate-pulse-slow {
        animation: pulse 4s ease-in-out infinite;
    }

    .section-title {
        position: relative;
        padding-bottom: 8px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 20px;
        height: 1px;
        background: currentColor;
        opacity: 0.3;
    }

    .icon {
        width: 16px;
        height: 16px;
        display: inline-block;
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        .glass-card {
            padding: 1rem;
        }

        .social-icon {
            width: 36px;
            height: 36px;
        }

        .contact-icon {
            width: 36px;
            height: 36px;
        }
    }
</style>

{{-- Change the class below to use different background options --}}
{{-- Options: footer-bg-dark, footer-bg-light, footer-bg-blue, footer-bg-purple, footer-bg-green, footer-bg-white, footer-bg-solid --}}
<footer class="footer-bg-dark py-12 relative">
    <div class="container mx-auto px-4 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
            <!-- Company Info -->
            <div class="lg:col-span-1">
                <div class="glass-card p-5 rounded-2xl">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-slate-600 rounded-lg flex items-center justify-center animate-pulse-slow border border-white/20">
                            <span class="text-white font-bold text-lg">{{ $logo['icon'] }}</span>
                        </div>
                        <span class="text-xl font-bold text-white">{{ $logo['text'] }}</span>
                    </div>
                    <p class="text-slate-300 mb-4 leading-relaxed text-sm">
                        Platform digital yang menghubungkan warga desa dengan teknologi modern untuk meningkatkan kesejahteraan masyarakat.
                    </p>

                    <!-- Social Media -->
                    <div class="flex space-x-2 flex-wrap">
                        <a href="#" class="social-icon w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center text-slate-300 hover:text-white border border-white/10" title="Facebook">
                            <svg class="icon" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="social-icon w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center text-slate-300 hover:text-white border border-white/10" title="Instagram">
                            <svg class="icon" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.418-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.928.875 1.418 2.026 1.418 3.323s-.49 2.448-1.418 3.244c-.875.807-2.026 1.297-3.323 1.297zm7.83-9.405c-.49 0-.928-.175-1.297-.49-.367-.315-.49-.753-.49-1.243 0-.49.123-.928.49-1.243.369-.367.807-.49 1.297-.49s.928.123 1.297.49c.367.315.49.753.49 1.243 0 .49-.123.928-.49 1.243-.369.315-.807.49-1.297.49z"/></svg>
                        </a>
                        <a href="#" class="social-icon w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center text-slate-300 hover:text-white border border-white/10" title="LinkedIn">
                            <svg class="icon" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        <a href="#" class="social-icon w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center text-slate-300 hover:text-white border border-white/10" title="YouTube">
                            <svg class="icon" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        <a href="#" class="social-icon w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center text-slate-300 hover:text-white border border-white/10" title="TikTok">
                            <svg class="icon" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="section-title text-lg font-bold text-white mb-4 flex items-center">
                    <svg class="icon mr-2 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H6.9C4.29 7 2.2 9.09 2.2 11.7v.6c0 2.61 2.09 4.7 4.7 4.7h4v-1.9H6.9c-1.71 0-3.1-1.39-3.1-3.1V12zM8 13h8v-2H8v2zm9.1-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1v.6c0 1.71-1.39 3.1-3.1 3.1h-4V17h4c2.61 0 4.7-2.09 4.7-4.7v-.6C21.8 9.09 19.71 7 17.1 7z"/>
                    </svg>
                    Quick Links
                </h4>
                <ul class="space-y-1">
                    <li><a href="#" class="link-hover text-slate-300 hover:text-white block text-sm">Beranda</a></li>
                    <li><a href="" class="link-hover text-slate-300 hover:text-white block text-sm">Tentang Kami</a></li>
                    <li><a href="" class="link-hover text-slate-300 hover:text-white block text-sm">Layanan</a></li>
                    <li><a href="" class="link-hover text-slate-300 hover:text-white block text-sm">Berita</a></li>
                    <li><a href="" class="link-hover text-slate-300 hover:text-white block text-sm">Kontak</a></li>
                </ul>
            </div>

            <!-- Layanan -->
            <div>
                <h4 class="section-title text-lg font-bold text-white mb-4 flex items-center">
                    <svg class="icon mr-2 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    Layanan
                </h4>
                <ul class="space-y-1">
                    <li><a href="#" class="link-hover text-slate-300 hover:text-white block text-sm">E-Commerce</a></li>
                    <li><a href="#" class="link-hover text-slate-300 hover:text-white block text-sm">Edukasi Digital</a></li>
                    <li><a href="#" class="link-hover text-slate-300 hover:text-white block text-sm">Layanan Desa</a></li>
                    <li><a href="#" class="link-hover text-slate-300 hover:text-white block text-sm">Konsultasi UMKM</a></li>
                    <li><a href="#" class="link-hover text-slate-300 hover:text-white block text-sm">Komunitas</a></li>
                </ul>
            </div>

            <!-- Kontak -->
            <div>
                <h4 class="section-title text-lg font-bold text-white mb-4 flex items-center">
                    <svg class="icon mr-2 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>
                    Kontak
                </h4>
                <ul class="space-y-3">
                    <li>
                        <a href="mailto:info@kampungdigital.id" class="contact-item flex items-center text-slate-300 hover:text-white">
                            <div class="contact-icon w-8 h-8 bg-slate-600 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 border border-white/20">
                                <svg class="icon text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                </svg>
                            </div>
                            <span class="text-sm">info@kampungdigital.id</span>
                        </a>
                    </li>
                    <li>
                        <a href="tel:+6281234567890" class="contact-item flex items-center text-slate-300 hover:text-white">
                            <div class="contact-icon w-8 h-8 bg-slate-600 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 border border-white/20">
                                <svg class="icon text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                </svg>
                            </div>
                            <span class="text-sm">+62 812-3456-7890</span>
                        </a>
                    </li>
                    <li>
                        <div class="contact-item flex items-center text-slate-300">
                            <div class="contact-icon w-8 h-8 bg-slate-600 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 border border-white/20">
                                <svg class="icon text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                            </div>
                            <span class="text-sm">Jakarta, Indonesia</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-white/20 pt-6">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <p class="text-slate-400 mb-3 md:mb-0 text-sm">
                    {{ $copyright }}
                </p>
                <div class="flex items-center space-x-4 text-sm">
                    <a href="#" class="text-slate-400 hover:text-white link-hover">Kebijakan Privasi</a>
                    <a href="#" class="text-slate-400 hover:text-white link-hover">Syarat & Ketentuan</a>
                    <a href="#" class="text-slate-400 hover:text-white link-hover">Bantuan</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Subtle Floating Elements -->
    <div class="absolute top-16 left-16 w-20 h-20 bg-white/3 rounded-full animate-pulse opacity-20"></div>
    <div class="absolute bottom-16 right-16 w-16 h-16 bg-white/3 rounded-full animate-pulse opacity-20" style="animation-delay: 3s;"></div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Intersection observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -20px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Apply fade-in to footer sections
    document.querySelectorAll('footer .glass-card, footer ul, footer h4').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(10px)';
        el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        observer.observe(el);
    });

    // Stagger delay for list items
    document.querySelectorAll('footer ul li').forEach((li, index) => {
        li.style.transitionDelay = `${index * 0.02}s`;
    });
});
</script>