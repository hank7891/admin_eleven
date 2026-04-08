const HERO_INTERVAL = 6500;

const setupAlertBanner = () => {
    const banner = document.getElementById('frontend-alert-banner');
    const closeButton = document.querySelector('[data-alert-close]');

    if (!banner || !closeButton) {
        return;
    }

    closeButton.addEventListener('click', () => {
        banner.classList.add('is-hidden');
        closeButton.setAttribute('aria-expanded', 'false');
    });
};

const setupMobileMenu = () => {
    const toggleButton = document.querySelector('[data-mobile-menu-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');

    if (!toggleButton || !menu) {
        return;
    }

    const syncMenu = (isOpen) => {
        menu.classList.toggle('is-open', isOpen);
        menu.classList.toggle('hidden', !isOpen);
        toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    };

    syncMenu(false);

    toggleButton.addEventListener('click', () => {
        const isOpen = toggleButton.getAttribute('aria-expanded') === 'true';
        syncMenu(!isOpen);
    });

    menu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => syncMenu(false));
    });
};

const setupHeroCarousel = () => {
    const images = Array.from(document.querySelectorAll('[data-hero-image]'));
    const dots = Array.from(document.querySelectorAll('[data-hero-dot]'));
    const nextButton = document.querySelector('[data-hero-next]');
    const prevButton = document.querySelector('[data-hero-prev]');
    const eyebrow = document.getElementById('heroEyebrow');
    const title = document.getElementById('heroTitle');
    const description = document.getElementById('heroDescription');
    const primaryCta = document.getElementById('heroPrimaryCta');
    const secondaryCta = document.getElementById('heroSecondaryCta');

    if (!images.length || !dots.length || !eyebrow || !title || !description || !primaryCta || !secondaryCta) {
        return;
    }

    const slides = images.map((image) => {
        const slideIndex = image.dataset.slideIndex;
        const dot = dots.find((item) => item.dataset.slideIndex === slideIndex);

        return {
            image,
            dot,
            eyebrow: image.dataset.eyebrow,
            title: image.dataset.title,
            description: image.dataset.description,
            primaryLabel: image.dataset.primaryLabel,
            primaryUrl: image.dataset.primaryUrl,
            secondaryLabel: image.dataset.secondaryLabel,
            secondaryUrl: image.dataset.secondaryUrl,
        };
    });

    let currentIndex = 0;
    let timer = null;
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const render = (nextIndex) => {
        currentIndex = (nextIndex + slides.length) % slides.length;

        slides.forEach((slide, index) => {
            const isActive = index === currentIndex;
            slide.image.classList.toggle('is-active', isActive);
            slide.dot?.classList.toggle('is-active', isActive);
            slide.dot?.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        const currentSlide = slides[currentIndex];
        eyebrow.textContent = currentSlide.eyebrow || '';
        title.textContent = currentSlide.title || '';
        description.textContent = currentSlide.description || '';
        primaryCta.textContent = currentSlide.primaryLabel || 'Explore';
        primaryCta.setAttribute('href', currentSlide.primaryUrl || '#');
        secondaryCta.textContent = currentSlide.secondaryLabel || 'Learn more';
        secondaryCta.setAttribute('href', currentSlide.secondaryUrl || '#');
    };

    const restartTimer = () => {
        if (prefersReducedMotion) {
            return;
        }

        if (timer) {
            window.clearInterval(timer);
        }

        timer = window.setInterval(() => {
            render(currentIndex + 1);
        }, HERO_INTERVAL);
    };

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            render(index);
            restartTimer();
        });
    });

    nextButton?.addEventListener('click', () => {
        render(currentIndex + 1);
        restartTimer();
    });

    prevButton?.addEventListener('click', () => {
        render(currentIndex - 1);
        restartTimer();
    });

    render(0);
    restartTimer();
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-hero-image]').forEach((image) => {
        const index = Number(image.dataset.slideIndex);
        const slide = window.__FRONTEND_HERO_SLIDES__?.[index];

        if (!slide) {
            return;
        }

        image.dataset.eyebrow = slide.eyebrow;
        image.dataset.title = slide.title;
        image.dataset.description = slide.description;
        image.dataset.primaryLabel = slide.primary_cta?.label || '';
        image.dataset.primaryUrl = slide.primary_cta?.url || '#';
        image.dataset.secondaryLabel = slide.secondary_cta?.label || '';
        image.dataset.secondaryUrl = slide.secondary_cta?.url || '#';
    });

    setupAlertBanner();
    setupMobileMenu();
    setupHeroCarousel();
});

