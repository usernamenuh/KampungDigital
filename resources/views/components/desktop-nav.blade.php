@props([
    'navItems' => [],
    'logo' => [
        'text' => '',
        'icon' => '',
        'image' => '',
        'href' => '#home'
    ],
    'dropdownItems' => [],
    'showModeToggle' => true
])

<div class="hidden md:flex items-center justify-between w-full max-w-7xl mx-auto px-4">
    <!-- Logo Section - Fixed width for consistent spacing -->
    <div class="navbar-logo w-48 flex-shrink-0">
        <a href="{{ $logo['href'] }}" class="flex items-center group">
            @if(!empty($logo['image']))
                <!-- Image logo -->
                <img src="{{ $logo['image'] }}" alt="Logo" class="h-10 w-auto object-contain transition-all duration-300 group-hover:scale-105">
            @else
                <!-- Fallback icon logo -->
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md group-hover:shadow-lg transition-all duration-300 group-hover:scale-105">
                    <span class="text-white font-bold text-base">{{ $logo['icon'] ?? 'L' }}</span>
                </div>
            @endif
        </a>
    </div>

    <!-- Centered Navigation Items -->
    <div class="flex-1 flex justify-center items-center">
        <div class="nav-items flex items-center justify-center space-x-6 px-4 py-2">
            @foreach($navItems as $item)
                <a href="{{ $item['link'] }}"
                   class="nav-link px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium rounded-xl transition-all duration-300 relative overflow-hidden group text-base position-relative"
                   onclick="handleAnchorClick(event, '{{ $item['link'] }}')">
                    <span class="relative z-10 transition-all duration-300 group-hover:scale-105">{{ $item['name'] }}</span>

                    <!-- Animated background gradient -->
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-900/20 dark:via-indigo-900/20 dark:to-purple-900/20 rounded-xl scale-0 group-hover:scale-100 transition-all duration-500 origin-center opacity-0 group-hover:opacity-100"></div>

                    <!-- Shimmer effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent dark:via-white/10 rounded-xl translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 ease-out"></div>

                    <!-- Bottom border indicator -->
                    <div class="absolute bottom-0 left-1/2 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 group-hover:w-full group-hover:left-0 transition-all duration-400 ease-out rounded-full"></div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Right Side Actions - Fixed width for consistent spacing -->
    <div class="flex items-center justify-end gap-2 w-48 flex-shrink-0">
        <!-- Mode Toggle -->
        @if($showModeToggle)
            <button id="desktop-mode-toggle"
                    class="p-2 rounded-lg bg-gray-100/50 dark:bg-gray-800/50 hover:bg-gray-200/70 dark:hover:bg-gray-700/70 transition-all duration-300 hover:scale-105 group backdrop-blur-sm">
                <!-- Light Mode Icon -->
                <svg class="w-4 h-4 text-gray-700 dark:text-gray-300 dark:hidden group-hover:text-blue-600 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <!-- Dark Mode Icon -->
                <svg class="w-4 h-4 text-gray-300 hidden dark:block group-hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                </svg>
            </button>
        @endif

        <!-- Login Button with Hover Border Gradient -->
        @if(!empty($dropdownItems))
            <div class="relative">
                <button id="desktop-dropdown-toggle"
                        class="hover-border-gradient-btn relative z-20 px-4 py-2 rounded-full bg-black/20 dark:bg-white/20 text-white dark:text-black font-medium transition-all duration-500 overflow-hidden group">
                    <span class="relative z-10 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        Get-Start
                    </span>
                </button>

                <!-- Dropdown Content -->
                <div id="desktop-dropdown-content"
                     class="absolute right-0 mt-2 w-52 bg-white/95 dark:bg-gray-800/95 rounded-xl shadow-lg border border-gray-200/50 dark:border-gray-700/50 opacity-0 invisible transform scale-95 transition-all duration-200 origin-top-right backdrop-blur-md">
                    <div class="py-2">
                        <a href="{{ route('login') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-700/50 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 group">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                            </div>
                            <span class="group-hover:translate-x-1 transition-transform duration-200">Login</span>
                        </a>
                        <a href="{{ route('register') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100/50 dark:hover:bg-gray-700/50 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 group">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <!-- User Plus Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2" />
                                    <circle cx="9" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 8v6m3-3h-6" />
                                </svg>
                            </div>
                            <span class="group-hover:translate-x-1 transition-transform duration-200">Register</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Enhanced Navigation Item Hover Effects */
    .nav-link {
        position: relative;
        overflow: hidden;
        transform-style: preserve-3d;
    }

    /* Pulse effect on hover */
    .nav-link:hover {
        animation: navPulse 0.6s ease-out;
    }

    @keyframes navPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1.02); }
    }

    /* Glowing text effect */
    .nav-link:hover span {
        text-shadow: 0 0 8px rgba(59, 130, 246, 0.3);
        filter: brightness(1.1);
    }

    /* Enhanced shimmer animation */
    .nav-link:hover .absolute:nth-child(3) {
        animation: shimmer 1.5s ease-in-out infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%) skewX(-15deg); }
        100% { transform: translateX(200%) skewX(-15deg); }
    }

    /* Hover Border Gradient Button */
    .hover-border-gradient-btn {
        position: relative;
        background: linear-gradient(45deg, #000000, #1a1a1a);
        border: 1px solid transparent;
        background-clip: padding-box;
    }

    .hover-border-gradient-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: -1;
        margin: -1px;
        border-radius: inherit;
        background: linear-gradient(45deg,
            rgba(255, 255, 255, 0.1) 0%,
            rgba(50, 117, 248, 0.3) 25%,
            rgba(255, 255, 255, 0.1) 50%,
            rgba(50, 117, 248, 0.3) 75%,
            rgba(255, 255, 255, 0.1) 100%
        );
        background-size: 400% 400%;
        animation: gradientRotate 3s ease infinite;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .hover-border-gradient-btn:hover::before {
        opacity: 1;
    }

    .hover-border-gradient-btn:hover {
        background: linear-gradient(45deg, #0a0a0a, #2a2a2a);
        transform: scale(1.02);
    }

    /* Dark mode adjustments */
    .dark .hover-border-gradient-btn {
        background: linear-gradient(45deg, #ffffff, #f0f0f0);
        color: #000000;
    }

    .dark .hover-border-gradient-btn:hover {
        background: linear-gradient(45deg, #f5f5f5, #e0e0e0);
    }

    .dark .hover-border-gradient-btn::before {
        background: linear-gradient(45deg,
            rgba(0, 0, 0, 0.1) 0%,
            rgba(50, 117, 248, 0.4) 25%,
            rgba(0, 0, 0, 0.1) 50%,
            rgba(50, 117, 248, 0.4) 75%,
            rgba(0, 0, 0, 0.1) 100%
        );
    }

    @keyframes gradientRotate {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }

    /* Dropdown Animation */
    .dropdown-show {
        opacity: 1 !important;
        visibility: visible !important;
        transform: scale(1) !important;
    }

    /* Responsive logo sizing */
    .navbar-logo img {
        max-height: 40px;
        height: auto;
        width: auto;
    }

    /* Responsive Navigation Items */
    @media (max-width: 1200px) {
        .nav-items {
            space-x-4;
        }

        .nav-link {
            px-3 py-2;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 1024px) {
        .navbar-logo {
            width: auto;
            min-width: 120px;
        }

        .nav-items {
            space-x-3;
        }

        .nav-link {
            px-2.5 py-1.5;
            font-size: 0.85rem;
        }

        .w-48 {
            width: auto;
            min-width: 100px;
        }
    }

    @media (max-width: 768px) {
        .navbar-logo img {
            max-height: 32px;
        }

        .nav-items {
            space-x-2;
        }

        .nav-link {
            px-2 py-1.5;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 640px) {
        .navbar-logo img {
            max-height: 28px;
        }

        .nav-link {
            px-1.5 py-1;
            font-size: 0.75rem;
        }
    }

    /* Ensure centered navigation on all screen sizes */
    @media (min-width: 768px) {
        .nav-items {
            justify-content: center;
        }
    }

    /* Performance optimizations */
    .nav-link,
    .nav-link span,
    .nav-link .absolute {
        will-change: transform, opacity, background-color;
        backface-visibility: hidden;
    }
</style>
