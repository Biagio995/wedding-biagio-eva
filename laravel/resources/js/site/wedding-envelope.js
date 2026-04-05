const STORAGE_KEY = 'wedding_envelope_seen';

function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function initWeddingEnvelope() {
    const root = document.getElementById('wedding-envelope');
    /** Initial state uses the `hidden` attribute until we decide to show — do not bail on hidden. */
    if (!root) {
        return;
    }

    const params = new URLSearchParams(window.location.search);
    if (params.has('noenvelope')) {
        root.setAttribute('hidden', '');
        return;
    }

    try {
        if (window.sessionStorage.getItem(STORAGE_KEY) === '1') {
            root.setAttribute('hidden', '');
            return;
        }
    } catch {
        /* sessionStorage may be blocked */
    }

    root.removeAttribute('hidden');
    document.body.classList.add('wedding-envelope-active');

    const scene = root.querySelector('[data-wedding-envelope-scene]');
    const btn = root.querySelector('[data-wedding-envelope-open]');

    window.requestAnimationFrame(() => {
        btn?.focus({ preventScroll: true });
    });

    let opened = false;

    const finish = () => {
        document.body.classList.remove('wedding-envelope-active');
        root.classList.add('is-leaving');
        const removeAfter = prefersReducedMotion() ? 220 : 650;
        window.setTimeout(() => {
            root.setAttribute('hidden', '');
            try {
                window.sessionStorage.setItem(STORAGE_KEY, '1');
            } catch {
                /* ignore */
            }
        }, removeAfter);
    };

    const open = () => {
        if (opened) {
            return;
        }
        opened = true;
        if (btn) {
            btn.setAttribute('disabled', '');
        }
        if (scene) {
            scene.style.pointerEvents = 'none';
        }
        root.classList.add('is-open');
        const delay = prefersReducedMotion() ? 280 : 1450;
        window.setTimeout(finish, delay);
    };

    scene?.addEventListener('click', (e) => {
        e.preventDefault();
        open();
    });
    btn?.addEventListener('click', (e) => {
        e.preventDefault();
        open();
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWeddingEnvelope);
} else {
    initWeddingEnvelope();
}
