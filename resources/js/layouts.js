// Global chart instances
let monthlyChart = null;
let genderChart = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing...');

    // Theme toggle functionality
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const currentTheme = localStorage.getItem('theme') || 'light';

    // Set initial theme
    document.documentElement.setAttribute('data-theme', currentTheme);
    updateThemeIcon(currentTheme);

    themeToggle.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    });

    function updateThemeIcon(theme) {
        if (theme === 'dark') {
            themeIcon.className = 'bi bi-moon';
        } else {
            themeIcon.className = 'bi bi-sun';
        }
    }

    // ENHANCED Profile dropdown functionality with smooth animations
    const profileTrigger = document.getElementById('profileTrigger');
    const profileDropdown = document.getElementById('profileDropdown');
    const dropdownMenu = document.getElementById('dropdownMenu');

    if (profileTrigger && dropdownMenu) {
        let isAnimating = false;

        profileTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (isAnimating) return; // Prevent multiple clicks during animation

            console.log('Profile clicked');

            const isActive = dropdownMenu.classList.contains('show');

            if (isActive) {
                closeDropdown();
            } else {
                openDropdown();
            }
        });

        function openDropdown() {
            isAnimating = true;

            // Reset any previous animations
            dropdownMenu.classList.remove('closing');

            // Add active states
            profileDropdown.classList.add('active');
            dropdownMenu.classList.add('show');

            // Reset item animations
            const items = dropdownMenu.querySelectorAll('.dropdown-item, .dropdown-divider');
            items.forEach(item => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
            });

            console.log('Dropdown opened with animation');

            // Allow interactions after animation completes
            setTimeout(() => {
                isAnimating = false;
            }, 600);
        }

        function closeDropdown() {
            isAnimating = true;

            // Add closing animation
            dropdownMenu.classList.add('closing');

            setTimeout(() => {
                dropdownMenu.classList.remove('show', 'closing');
                profileDropdown.classList.remove('active');
                isAnimating = false;
                console.log('Dropdown closed with animation');
            }, 300);
        }

        // Enhanced outside click detection
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && !isAnimating) {
                if (dropdownMenu.classList.contains('show')) {
                    closeDropdown();
                    console.log('Dropdown closed by outside click');
                }
            }
        });

        // Prevent dropdown from closing when clicking inside
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Add keyboard support
        profileTrigger.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                profileTrigger.click();
            }
            if (e.key === 'Escape') {
                if (dropdownMenu.classList.contains('show')) {
                    closeDropdown();
                }
            }
        });

        // Enhanced hover effects
        profileTrigger.addEventListener('mouseenter', function() {
            if (!dropdownMenu.classList.contains('show')) {
                profileTrigger.style.transform = 'translateY(-1px)';
            }
        });

        profileTrigger.addEventListener('mouseleave', function() {
            if (!dropdownMenu.classList.contains('show')) {
                profileTrigger.style.transform = 'translateY(0)';
            }
        });
    }

    // Mobile menu functionality
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebar');
    const mobileOverlay = document.getElementById('mobileOverlay');

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
            mobileOverlay.classList.toggle('active');
        });
    }

    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            mobileOverlay.classList.remove('active');
        });
    }

    // Initialize charts
    setTimeout(() => {
        initializeCharts();
    }, 500);
});

function initializeCharts() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded');
        return;
    }

    // Destroy existing charts
    if (monthlyChart) {
        monthlyChart.destroy();
        monthlyChart = null;
    }
    if (genderChart) {
        genderChart.destroy();
        genderChart = null;
    }

    // Monthly Statistics Chart
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        try {
            monthlyChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Penduduk',
                        data: [2400, 2405, 2410, 2415, 2420, 2425, 2428, 2430, 2432, 2435, 2438, 2440],
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#8b5cf6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }, {
                        label: 'UMKM',
                        data: [35, 37, 39, 41, 42, 43, 44, 45, 46, 47, 48, 50],
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#06b6d4',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--text-secondary'),
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--border-color')
                            },
                            ticks: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--text-secondary'),
                                font: {
                                    size: 9
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--border-color')
                            },
                            ticks: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--text-secondary'),
                                font: {
                                    size: 9
                                }
                            }
                        }
                    }
                }
            });
            console.log('Monthly chart created successfully');
        } catch (error) {
            console.error('Error creating monthly chart:', error);
        }
    }

    // Gender Distribution Chart
    const genderCtx = document.getElementById('genderChart');
    if (genderCtx) {
        try {
            genderChart = new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        data: [1250, 1180],
                        backgroundColor: ['#8b5cf6', '#06b6d4'],
                        borderWidth: 0,
                        cutout: '70%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: getComputedStyle(document.documentElement).getPropertyValue('--text-secondary'),
                                usePointStyle: true,
                                padding: 12,
                                font: {
                                    size: 9
                                }
                            }
                        }
                    }
                }
            });
            console.log('Gender chart created successfully');
        } catch (error) {
            console.error('Error creating gender chart:', error);
        }
    }
}