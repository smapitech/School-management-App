(() => {
    const ready = (fn) => {
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn, { once: true });
        else fn();
    };

    ready(() => {
        const button = document.querySelector('[data-menu-button]');
        const menu = document.querySelector('[data-menu]');

        if (button && menu) {
            const setOpen = (open) => {
                button.setAttribute('aria-expanded', open ? 'true' : 'false');
                menu.classList.toggle('is-open', open);
                document.body.classList.toggle('website-menu-open', open);
            };

            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                setOpen(button.getAttribute('aria-expanded') !== 'true');
            });

            menu.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => setOpen(false));
            });

            document.addEventListener('click', (event) => {
                if (button.getAttribute('aria-expanded') === 'true' && !menu.contains(event.target) && !button.contains(event.target)) {
                    setOpen(false);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') setOpen(false);
            });
        }

        document.querySelectorAll('a[href]').forEach((link) => {
            const href = link.getAttribute('href') || '';
            if (href !== '#' && href.trim() !== '') return;
            link.setAttribute('role', 'button');
            link.addEventListener('click', (event) => event.preventDefault());
        });

        const slider = document.querySelector('[data-hero-slider]');
        if (slider) {
            let startX = 0;
            let startY = 0;
            const next = slider.querySelector('[data-slider-next]');
            const prev = slider.querySelector('[data-slider-prev]');

            slider.addEventListener('touchstart', (event) => {
                const touch = event.changedTouches[0];
                startX = touch.clientX;
                startY = touch.clientY;
            }, { passive: true });

            slider.addEventListener('touchend', (event) => {
                const touch = event.changedTouches[0];
                const diffX = touch.clientX - startX;
                const diffY = touch.clientY - startY;
                if (Math.abs(diffX) < 45 || Math.abs(diffX) < Math.abs(diffY)) return;
                if (diffX < 0) next?.click();
                else prev?.click();
            }, { passive: true });
        }
    });
})();
