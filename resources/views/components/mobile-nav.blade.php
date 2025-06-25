@props([
    'navItems' => [],
    'logo' => [
        'text' => 'Kampung Digital',
        'icon' => 'KD',
        'href' => '#home'
    ],
    'socialLinks' => [],
    'showModeToggle' => true
])

<div class="mobile-nav md:hidden">
    <!-- Mobile Header - Responsive -->
    <div class="mobile-nav-header flex items-center justify-between px-1">
        <!-- Mobile Logo - Image Ready -->
        <div class="navbar-logo">
            <a href="{{ $logo['href'] }}" class="flex items-center group">
                @if(!empty($logo['image']))
                    <!-- Image logo for mobile -->
                    <img src="{{ $logo['image'] }}" alt="Logo" class="h-8 w-auto object-contain transition-all duration-300 group-hover:scale-105">
                @else
                    <!-- Fallback icon logo -->
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md group-hover:shadow-lg transition-all duration-300 group-hover:scale-105">
                        <span class="text-white font-bold text-sm">{{ $logo['icon'] ?? 'L' }}</span>
                    </div>
                @endif
            </a>
        </div>

        <!-- Mobile Menu Toggle - Responsive -->
        <button id="mobile-menu-toggle" 
                class="p-2 rounded-lg bg-gray-100/50 dark:bg-gray-800/50 hover:bg-gray-200/70 dark:hover:bg-gray-700/70 transition-all duration-300 hover:scale-105 group backdrop-blur-sm">
            <!-- Hamburger Icon -->
            <svg class="w-5 h-5 text-gray-700 dark:text-gray-300 hamburger-icon group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <!-- Close Icon -->
            <svg class="w-5 h-5 text-gray-700 dark:text-gray-300 close-icon hidden group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Mobile Menu - Compact -->
    <div id="mobile-menu" 
         class="mobile-nav-menu bg-white/95 dark:bg-gray-900/95 border-t border-gray-200/50 dark:border-gray-700/50 opacity-0 invisible transform -translate-y-4 transition-all duration-300 backdrop-blur-md rounded-b-lg">
        <div class="px-4 py-4 space-y-1">
            <!-- Navigation Items -->
            <div class="space-y-1 mb-4">
                @foreach($navItems as $index => $item)
                    <a href="{{ $item['link'] }}" 
                       class="block text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 font-medium py-2 px-3 rounded-lg transition-all duration-200 group text-sm"
                       onclick="handleAnchorClick(event, '{{ $item['link'] }}')"
                       style="animation-delay: {{ $index * 0.1 }}s">
                        <span class="group-hover:translate-x-2 transition-transform duration-200 inline-block">{{ $item['name'] }}</span>
                    </a>
                @endforeach
            </div>
            
            <!-- Divider -->
            <div class="border-t border-gray-200/50 dark:border-gray-700/50 my-4"></div>
            
            <!-- Mobile Actions - Compact -->
            <div class="space-y-3">
                <!-- Social Links -->
                @if(!empty($socialLinks))
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($socialLinks as $social)
                            <a href="{{ $social['href'] }}" 
                               class="flex items-center justify-center gap-2 px-3 py-2 border border-gray-300/50 dark:border-gray-600/50 rounded-lg bg-white/50 dark:bg-gray-800/50 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50/70 dark:hover:bg-gray-700/70 hover:border-blue-300/70 dark:hover:border-blue-500/70 transition-all duration-200 group backdrop-blur-sm"
                               target="{{ $social['target'] ?? '_self' }}">
                                <div class="w-3 h-3 flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    {!! $social['icon'] !!}
                                </div>
                                <span class="group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $social['name'] }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
                
                <!-- Mode Toggle -->
                @if($showModeToggle)
                    <div class="flex justify-center">
                        <button id="mobile-mode-toggle" 
                                class="flex items-center justify-center gap-2 px-4 py-2 border border-gray-300/50 dark:border-gray-600/50 rounded-lg bg-white/50 dark:bg-gray-800/50 hover:bg-gray-50/70 dark:hover:bg-gray-700/70 hover:border-blue-300/70 dark:hover:border-blue-500/70 transition-all duration-200 group backdrop-blur-sm">
                            <!-- Light Mode Icon -->
                            <svg class="w-3 h-3 text-gray-700 dark:text-gray-300 dark:hidden group-hover:text-blue-600 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            <!-- Dark Mode Icon -->
                            <svg class="w-3 h-3 text-gray-300 hidden dark:block group-hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs font-medium group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                <span class="dark:hidden">Dark Mode</span>
                                <span class="hidden dark:inline">Light Mode</span>
                            </span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    /* Mobile Menu Animations */
    .mobile-nav-menu.show {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
    }

    .mobile-nav-menu.show a {
        animation: slideInLeft 0.3s ease-out forwards;
        opacity: 0;
        transform: translateX(-20px);
    }

    @keyframes slideInLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Mobile responsive adjustments */
    @media (max-width: 480px) {
        .mobile-nav-header {
            padding: 0 0.5rem;
        }
        
        .navbar-logo img {
            max-height: 24px;
        }
        
        .navbar-logo .w-8 {
            width: 1.75rem;
            height: 1.75rem;
        }
    }
</style>
