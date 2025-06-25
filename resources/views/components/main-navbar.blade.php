@props([
    'navItems' => [],
    'logo' => [
        'text' => '',
        'icon' => '',
        'image' => '',
        'href' => '#home'
    ],
    'dropdownItems' => [],
    'socialLinks' => [],
    'showModeToggle' => true,
    'scrollEffect' => true,
    'sticky' => false
])

<nav id="main-navbar" class="fixed top-5 left-0 right-0 z-50 navbar-scroll-effect {{ $scrollEffect ? 'scroll-enabled' : '' }}">
    <div class="navbar-container">
        <!-- Desktop Navigation -->
        <x-desktop-nav 
            :navItems="$navItems"
            :logo="$logo"
            :dropdownItems="$dropdownItems"
            :showModeToggle="$showModeToggle"
        />

        <!-- Mobile Navigation -->
        <x-mobile-nav 
            :navItems="$navItems"
            :logo="$logo"
            :socialLinks="$socialLinks"
            :showModeToggle="$showModeToggle"
        />
    </div>
</nav>

<style>
    /* Initial Navbar State - Much more space below */
    #main-navbar {
        background: transparent;
        backdrop-filter: none;
        border: none;
        box-shadow: none;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        transform: translateZ(0);
    }

    .navbar-container {
        max-width: 95%;
        margin: 0 auto;
        padding: 0 1.5rem;
        border-radius: 0;
        background: transparent;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        will-change: transform, max-width, background-color, border-radius;
    }

    /* Floating Effect on Scroll - More space when scrolled */
    .scroll-enabled.navbar-scrolled {
        background: transparent !important;
        transform: translateY(8px) translateZ(0);
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* Container Floating State - Much Tighter */
    .scroll-enabled.navbar-scrolled .navbar-container {
        max-width: 65%;
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(8px) saturate(120%);
        -webkit-backdrop-filter: blur(8px) saturate(120%);
        border-radius: 50px;
        padding: 0.20rem 0.25rem;
        box-shadow: 
            0 8px 32px -8px rgba(0, 0, 0, 0.08),
            0 4px 16px -4px rgba(0, 0, 0, 0.04),
            0 0 0 1px rgba(255, 255, 255, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        border: none;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* Dark Mode Floating State */
    .dark .scroll-enabled.navbar-scrolled .navbar-container {
        background: rgba(17, 24, 39, 0.1) !important;
        border: none;
        box-shadow: 
            0 8px 32px -8px rgba(0, 0, 0, 0.15),
            0 4px 16px -4px rgba(0, 0, 0, 0.08),
            0 0 0 1px rgba(255, 255, 255, 0.02),
            inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }

    /* Enhanced Hover Effects */
    .navbar-scrolled .navbar-container:hover {
        transform: translateY(-2px) scale(1.005);
        box-shadow: 
            0 15px 50px -8px rgba(0, 0, 0, 0.15),
            0 8px 30px -4px rgba(0, 0, 0, 0.08),
            0 0 0 1px rgba(255, 255, 255, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.5);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Smooth Text Color Transitions */
    .navbar-scrolled .navbar-logo-text,
    .navbar-scrolled .nav-link {
        color: #1f2937 !important;
        transition: color 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .dark .scroll-enabled.navbar-scrolled .navbar-logo-text,
    .dark .scroll-enabled.navbar-scrolled .nav-link {
        color: #f9fafb !important;
    }

    /* Enhanced Navigation Item Hover Effects */
    .nav-link {
        position: relative;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    /* Animated background on hover */
    .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, 
            transparent, 
            rgba(59, 130, 246, 0.1), 
            rgba(59, 130, 246, 0.15),
            rgba(59, 130, 246, 0.1), 
            transparent
        );
        transition: left 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1;
    }

    .nav-link:hover::before {
        left: 100%;
    }

    /* Glowing border effect */
    .nav-link::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 12px;
        padding: 1px;
        background: linear-gradient(45deg, 
            transparent, 
            rgba(59, 130, 246, 0.3), 
            rgba(147, 51, 234, 0.3), 
            rgba(59, 130, 246, 0.3), 
            transparent
        );
        background-size: 300% 300%;
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask-composite: xor;
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        opacity: 0;
        animation: borderGlow 2s linear infinite;
        transition: opacity 0.3s ease;
    }

    .nav-link:hover::after {
        opacity: 1;
    }

    @keyframes borderGlow {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    /* Enhanced hover state */
    .nav-link:hover {
        background: rgba(59, 130, 246, 0.08);
        color: #2563eb !important;
        transform: translateY(-2px) scale(1.05);
        box-shadow: 
            0 8px 25px rgba(59, 130, 246, 0.15),
            0 4px 12px rgba(59, 130, 246, 0.1);
    }

    .dark .nav-link:hover {
        background: rgba(59, 130, 246, 0.12);
        color: #60a5fa !important;
    }

    /* Active state */
    .nav-link.active {
        background: rgba(59, 130, 246, 0.12);
        color: #1d4ed8 !important;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
        transform: scale(1.02);
    }

    .dark .nav-link.active {
        background: rgba(59, 130, 246, 0.15);
        color: #93c5fd !important;
    }

    /* Logo Scaling Animation */
    .navbar-scrolled .navbar-logo a {
        transform: scale(0.95);
        transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .navbar-scrolled .navbar-logo a:hover {
        transform: scale(0.98);
    }

    /* Button Enhancements */
    .navbar-scrolled #desktop-mode-toggle,
    .navbar-scrolled #desktop-dropdown-toggle {
        background: rgba(255, 255, 255, 0.7);
        border-color: rgba(0, 0, 0, 0.06);
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dark .scroll-enabled.navbar-scrolled #desktop-mode-toggle,
    .dark .scroll-enabled.navbar-scrolled #desktop-dropdown-toggle {
        background: rgba(17, 24, 39, 0.7);
        border-color: rgba(255, 255, 255, 0.06);
    }

    /* Login Button Styles */
    #desktop-dropdown-toggle.hover-border-gradient-btn {
        background: #fff !important;
        color: #111827 !important;
        border: 1.5px solid #e5e7eb !important;
        border-radius: 14px !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    }

    #desktop-dropdown-toggle.hover-border-gradient-btn:hover,
    #desktop-dropdown-toggle.hover-border-gradient-btn:focus {
        background: #f3f4f6 !important;
        color: #111827 !important;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08) !important;
    }

    .dark #desktop-dropdown-toggle.hover-border-gradient-btn {
        background: #37306B !important;
        color: #ffffff !important;
        border: 1.5px solid #f0f0f0 !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.16) !important;
    }

    .dark #desktop-dropdown-toggle.hover-border-gradient-btn:hover,
    .dark #desktop-dropdown-toggle.hover-border-gradient-btn:focus {
        background: #ffffff !important;
        color: #000000 !important;
    }

    /* Fixed Responsive Breakpoints */
    @media (max-width: 1200px) {
        .navbar-container {
            max-width: 96%;
            padding: 0 1.25rem;
        }
        
        .scroll-enabled.navbar-scrolled .navbar-container {
            max-width: 70%;
            padding: 0.75rem 1rem;
        }
    }

    @media (max-width: 1024px) {
        .navbar-container {
            max-width: 97%;
            padding: 0 1rem;
        }
        
        .scroll-enabled.navbar-scrolled .navbar-container {
            max-width: 75%;
            border-radius: 28px;
            padding: 0.75rem 1rem;
        }
    }

    @media (max-width: 768px) {
        .navbar-container {
            max-width: 98%;
            padding: 0 0.75rem;
        }
        
        .scroll-enabled.navbar-scrolled .navbar-container {
            max-width: 85%;
            border-radius: 24px;
            padding: 0.5rem 0.75rem;
        }
    }

    @media (max-width: 640px) {
        .navbar-container,
        .scroll-enabled.navbar-scrolled .navbar-container {
            max-width: 100vw !important;
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            border-radius: 10px !important;
            background: transparent !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            transition: none !important;
        }
        #main-navbar {
            left: 0 !important;
            right: 0 !important;
            top: 0.5rem !important;
            background: transparent !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
        }
    }

    @media (max-width: 480px) {
        .scroll-enabled.navbar-scrolled .navbar-container {
            max-width: 95%;
            border-radius: 12px;
            padding: 0.4rem 0.6rem;
        }
    }

    /* Smooth Entrance Animation */
    .navbar-scroll-effect {
        animation: navbarEntrance 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    @keyframes navbarEntrance {
        0% {
            opacity: 0;
            transform: translateY(-30px) scale(0.95);
        }
        60% {
            opacity: 0.8;
            transform: translateY(5px) scale(1.02);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Scroll Progress Indicator */
    .navbar-scrolled::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.6), transparent);
        border-radius: 0 0 1px 1px;
        animation: scrollIndicator 0.8s ease-out 0.2s forwards;
    }

    @keyframes scrollIndicator {
        0% {
            width: 0;
            opacity: 0;
        }
        100% {
            width: 60px;
            opacity: 1;
        }
    }

    /* Performance Optimizations */
    .navbar-container,
    .navbar-logo a,
    .nav-link,
    #desktop-mode-toggle,
    #desktop-dropdown-toggle {
        will-change: transform, background-color, box-shadow;
        backface-visibility: hidden;
        perspective: 1000px;
    }

    /* Enhanced Focus States */
    .nav-link:focus,
    #desktop-mode-toggle:focus,
    #desktop-dropdown-toggle:focus {
        outline: 2px solid rgba(59, 130, 246, 0.5);
        outline-offset: 3px;
        border-radius: 8px;
        transition: outline 0.2s ease;
    }

    /* Micro-interactions */
    .navbar-scrolled .nav-items {
        transform: translateZ(0);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let isScrolling = false;
        const scrollThreshold = 30;
        
        function handleScroll() {
            if (!isScrolling) {
                window.requestAnimationFrame(updateNavbar);
                isScrolling = true;
            }
        }
        
        function updateNavbar() {
            const navbar = document.getElementById('main-navbar');
            const currentScrollY = window.scrollY;
            
            if (currentScrollY > scrollThreshold) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
            
            isScrolling = false;
        }
        
        // Smooth anchor scrolling
        window.handleAnchorClick = function(e, href) {
            e.preventDefault();
            
            if (href.startsWith('#')) {
                const target = document.querySelector(href);
                if (target) {
                    const offset = 120;
                    const targetPosition = target.offsetTop - offset;
                    
                    window.scrollTo({
                        top: Math.max(0, targetPosition),
                        behavior: 'smooth'
                    });
                }
            }
        };
        
        // Mobile menu functionality
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function() {
                const mobileMenu = document.getElementById('mobile-menu');
                const hamburgerIcon = document.querySelector('.hamburger-icon');
                const closeIcon = document.querySelector('.close-icon');
                
                if (mobileMenu.classList.contains('show')) {
                    mobileMenu.classList.remove('show');
                    hamburgerIcon?.classList.remove('hidden');
                    closeIcon?.classList.add('hidden');
                } else {
                    mobileMenu.classList.add('show');
                    hamburgerIcon?.classList.add('hidden');
                    closeIcon?.classList.remove('hidden');
                }
            });
        }
        
        // Dropdown functionality
        const desktopDropdownToggle = document.getElementById('desktop-dropdown-toggle');
        if (desktopDropdownToggle) {
            desktopDropdownToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                const dropdownContent = document.getElementById('desktop-dropdown-content');
                dropdownContent?.classList.toggle('dropdown-show');
            });
        }
        
        // Dark mode toggle
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }
        
        document.getElementById('desktop-mode-toggle')?.addEventListener('click', toggleDarkMode);
        document.getElementById('mobile-mode-toggle')?.addEventListener('click', toggleDarkMode);
        
        // Initialize dark mode
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
        
        // Attach scroll listener
        window.addEventListener('scroll', handleScroll, { passive: true });
        updateNavbar();
    });
</script>