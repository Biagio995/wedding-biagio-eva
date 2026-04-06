/**
 * Fade-in on scroll for elements with .reveal-on-scroll (adds .is-visible when in view).
 */
let scrollRevealObserver = null;

function revealAll(elements) {
    elements.forEach((el) => {
        el.classList.add('is-visible');
    });
}

function initScrollReveal() {
    if (scrollRevealObserver) {
        scrollRevealObserver.disconnect();
        scrollRevealObserver = null;
    }

    const elements = document.querySelectorAll('.reveal-on-scroll');
    if (elements.length === 0) {
        return;
    }

    if (
        typeof window === 'undefined' ||
        !('IntersectionObserver' in window)
    ) {
        revealAll(elements);
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        revealAll(elements);
        return;
    }

    scrollRevealObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    scrollRevealObserver?.unobserve(entry.target);
                }
            });
        },
        {
            root: null,
            rootMargin: '0px 0px -10% 0px',
            threshold: 0.08,
        },
    );

    elements.forEach((el) => {
        scrollRevealObserver?.observe(el);
    });
}

document.addEventListener('turbo:load', initScrollReveal);

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrollReveal);
} else {
    initScrollReveal();
}
