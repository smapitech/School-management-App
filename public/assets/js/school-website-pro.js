(() => {
    const button = document.querySelector('[data-menu-button]');
    const menu = document.querySelector('[data-menu]');
    if (button && menu) {
        button.addEventListener('click', () => {
            const open = button.getAttribute('aria-expanded') === 'true';
            button.setAttribute('aria-expanded', String(!open));
            menu.classList.toggle('is-open', !open);
        });
        menu.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
            button.setAttribute('aria-expanded', 'false');
            menu.classList.remove('is-open');
        }));
    }

    const header = document.querySelector('[data-site-header]');
    if (header) {
        const update = () => header.classList.toggle('is-scrolled', window.scrollY > 12);
        update();
        window.addEventListener('scroll', update, { passive: true });
    }

    const slider = document.querySelector('[data-hero-slider]');
    if (!slider) return;
    const slides = [...slider.querySelectorAll('[data-slide]')];
    const dots = [...slider.querySelectorAll('[data-slider-dot]')];
    const previous = slider.querySelector('[data-slider-prev]');
    const next = slider.querySelector('[data-slider-next]');
    if (slides.length < 2) return;

    let current = 0;
    let timer;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const show = (index, restart = true) => {
        current = (index + slides.length) % slides.length;
        slides.forEach((slide, slideIndex) => {
            const active = slideIndex === current;
            slide.classList.toggle('is-active', active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });
        dots.forEach((dot, dotIndex) => {
            const active = dotIndex === current;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        if (restart) start();
    };

    const start = () => {
        window.clearInterval(timer);
        if (!reducedMotion) timer = window.setInterval(() => show(current + 1, false), 6500);
    };

    previous?.addEventListener('click', () => show(current - 1));
    next?.addEventListener('click', () => show(current + 1));
    dots.forEach((dot, index) => dot.addEventListener('click', () => show(index)));
    slider.addEventListener('mouseenter', () => window.clearInterval(timer));
    slider.addEventListener('mouseleave', start);
    slider.addEventListener('focusin', () => window.clearInterval(timer));
    slider.addEventListener('focusout', start);
    document.addEventListener('visibilitychange', () => document.hidden ? window.clearInterval(timer) : start());
    start();
})();
