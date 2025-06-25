@props([
    'title' => 'FAQ',
    'subtitle' => 'Pertanyaan yang sering diajukan tentang Kampung Digital',
    'faqs' => []
])

<style>
    /* Seamless Section Background */
    .seamless-section-bg {
        background-color: #ffffff !important;
        transition: background-color 0.3s ease;
    }
    
    .dark .seamless-section-bg {
        background-color: #0f172a !important;
    }

    /* FAQ Item Styling - Smaller in light mode */
    .faq-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(248, 250, 252, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.6);
    }

    .dark .faq-item {
        background: rgba(30, 41, 59, 0.9);
        border: 1px solid rgba(71, 85, 105, 0.3);
    }

    /* Smaller padding and text in light mode */
    .faq-question {
        padding: 1rem 1.25rem; /* Smaller padding in light mode */
        transition: all 0.3s ease;
    }

    .faq-question h3 {
        font-size: 0.95rem; /* Smaller text in light mode */
        font-weight: 600;
    }

    .dark .faq-question {
        padding: 1.5rem; /* Larger padding in dark mode */
    }
    
    .dark .faq-question h3 {
        font-size: 1.125rem; /* Larger text in dark mode */
        font-weight: 600;
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out, padding 0.3s ease-out;
    }

    .faq-answer.active {
        max-height: 200px;
    }

    .faq-answer-content {
        padding: 0 1.25rem 1rem 1.25rem; /* Smaller padding in light mode */
        border-top: 1px solid rgba(226, 232, 240, 0.6);
        margin-top: 0.75rem;
        padding-top: 0.75rem;
    }

    .dark .faq-answer-content {
        padding: 0 1.5rem 1.5rem 1.5rem; /* Larger padding in dark mode */
        border-top: 1px solid rgba(71, 85, 105, 0.3);
        margin-top: 1rem;
        padding-top: 1rem;
    }

    .faq-answer-text {
        font-size: 0.875rem; /* Smaller text in light mode */
        line-height: 1.5;
        color: #64748b;
    }

    .dark .faq-answer-text {
        font-size: 0.95rem; /* Larger text in dark mode */
        line-height: 1.6;
        color: #cbd5e1;
    }

    .faq-question:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        background: rgba(241, 245, 249, 0.9);
    }

    .dark .faq-question:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        background: rgba(30, 41, 59, 0.95);
    }

    .chevron-icon {
        transition: transform 0.3s ease;
        width: 1.125rem; /* Smaller icon in light mode */
        height: 1.125rem;
    }

    .dark .chevron-icon {
        width: 1.25rem; /* Larger icon in dark mode */
        height: 1.25rem;
    }

    .chevron-icon.rotate {
        transform: rotate(180deg);
    }

    .faq-header-icon {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        animation: pulse 3s ease-in-out infinite;
        width: 3.5rem; /* Smaller header icon in light mode */
        height: 3.5rem;
    }

    .dark .faq-header-icon {
        width: 4rem; /* Larger header icon in dark mode */
        height: 4rem;
    }

    .faq-header-icon svg {
        width: 1.75rem; /* Smaller SVG in light mode */
        height: 1.75rem;
    }

    .dark .faq-header-icon svg {
        width: 2rem; /* Larger SVG in dark mode */
        height: 2rem;
    }

    /* Header text sizing */
    .faq-title {
        font-size: 2.5rem; /* Smaller title in light mode */
        margin-bottom: 1rem;
    }

    .faq-subtitle {
        font-size: 1.125rem; /* Smaller subtitle in light mode */
    }

    .dark .faq-title {
        font-size: 3rem; /* Larger title in dark mode */
        margin-bottom: 1.5rem;
    }
    
    .dark .faq-subtitle {
        font-size: 1.25rem; /* Larger subtitle in dark mode */
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    /* Compact spacing in light mode */
    .faq-container {
        gap: 0.75rem; /* Smaller gap in light mode */
    }

    .dark .faq-container {
        gap: 1rem; /* Larger gap in dark mode */
    }

    /* Section spacing */
    #faq {
        padding-top: 4rem; /* 16 in Tailwind */
        padding-bottom: 4rem;
    }

    .dark #faq {
        padding-top: 5rem; /* 20 in Tailwind */
        padding-bottom: 5rem;
    }
</style>

<section id="faq" class="seamless-section-bg transition-colors duration-300">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center faq-header-icon rounded-full mb-4">
                <svg class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="faq-title font-bold text-gray-900 dark:text-white">
                {{ $title }}
            </h2>
            <p class="faq-subtitle text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed">
                {{ $subtitle }}
            </p>
        </div>

        <!-- FAQ Items -->
        <div class="max-w-3xl mx-auto faq-container flex flex-col">
            @forelse($faqs as $index => $faq)
                <div class="faq-item rounded-xl shadow-sm hover:shadow-md overflow-hidden transition-all duration-300">
                    <button
                        class="faq-question w-full text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-300"
                        onclick="toggleFAQ({{ $index }})"
                        aria-expanded="false"
                        aria-controls="answer-{{ $index }}"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="text-gray-900 dark:text-white pr-3 leading-relaxed">
                                {{ $faq['question'] }}
                            </h3>
                            <div class="flex-shrink-0">
                                <svg id="icon-{{ $index }}" class="text-blue-500 chevron-icon transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </button>
                    <div id="answer-{{ $index }}" class="faq-answer" aria-labelledby="question-{{ $index }}">
                        <div class="faq-answer-content">
                            <p class="faq-answer-text">
                                {{ $faq['answer'] }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum ada FAQ</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">FAQ akan ditampilkan di sini setelah ditambahkan.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<script>
function toggleFAQ(index) {
    const answer = document.getElementById(`answer-${index}`);
    const icon = document.getElementById(`icon-${index}`);
    const button = answer.previousElementSibling;

    // Close all other FAQs
    document.querySelectorAll('.faq-answer').forEach((item, i) => {
        if (i !== index) {
            item.classList.remove('active');
            const otherIcon = document.getElementById(`icon-${i}`);
            const otherButton = item.previousElementSibling;
            if (otherIcon) {
                otherIcon.classList.remove('rotate');
            }
            if (otherButton) {
                otherButton.setAttribute('aria-expanded', 'false');
            }
        }
    });

    // Toggle current FAQ
    const isActive = answer.classList.contains('active');

    if (isActive) {
        answer.classList.remove('active');
        icon.classList.remove('rotate');
        button.setAttribute('aria-expanded', 'false');
    } else {
        answer.classList.add('active');
        icon.classList.add('rotate');
        button.setAttribute('aria-expanded', 'true');
    }
}

// Initialize FAQ animations on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add staggered fade-in animation
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(15px)';

        setTimeout(() => {
            item.classList.add('fade-in-up');
        }, index * 80);
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            // Close all open FAQs
            document.querySelectorAll('.faq-answer.active').forEach((answer, index) => {
                answer.classList.remove('active');
                const icon = document.getElementById(`icon-${index}`);
                const button = answer.previousElementSibling;
                if (icon) icon.classList.remove('rotate');
                if (button) button.setAttribute('aria-expanded', 'false');
            });
        }
    });

    // Smooth scroll to FAQ when linked
    if (window.location.hash === '#faq') {
        document.getElementById('faq').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
});
</script>