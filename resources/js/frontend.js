const HERO_INTERVAL = 5000;
const HERO_SWIPE_THRESHOLD = 50;
const HERO_FALLBACK_IMAGE = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(`
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 1500" preserveAspectRatio="none">
  <defs>
    <linearGradient id="heroFallbackGradient" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#dde1ff" />
      <stop offset="55%" stop-color="#f7ecdf" />
      <stop offset="100%" stop-color="#eadffd" />
    </linearGradient>
  </defs>
  <rect width="1200" height="1500" fill="url(#heroFallbackGradient)" />
</svg>` )}`;

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
    const carousel = document.querySelector('[data-hero-carousel]');
    const images = Array.from(document.querySelectorAll('[data-hero-image]'));
    const dots = Array.from(document.querySelectorAll('[data-hero-dot]'));
    const nextButton = document.querySelector('[data-hero-next]');
    const prevButton = document.querySelector('[data-hero-prev]');
    const liveRegion = document.getElementById('heroLiveRegion');
    const slideLink = document.getElementById('heroSlideLink');

    if (!carousel || !images.length || !dots.length) {
        return;
    }

    const slides = images.map((image) => {
        const slideIndex = image.dataset.slideIndex;
        const dot = dots.find((item) => item.dataset.slideIndex === slideIndex);

        return {
            image,
            dot,
            imageAlt: image.alt,
            targetUrl: image.dataset.targetUrl,
        };
    });

    let currentIndex = 0;
    let timer = null;
    let isPaused = false;
    let touchStartX = 0;
    let touchStartY = 0;
    let hasRenderedOnce = false;
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const syncSlideLink = (slide) => {
        if (!slideLink) {
            return;
        }

        const targetUrl = (slide?.targetUrl || '').trim();
        const hasUrl = targetUrl.length > 0;
        const isExternal = hasUrl && /^https?:\/\//i.test(targetUrl);

        slideLink.setAttribute('href', hasUrl ? targetUrl : '#');
        slideLink.classList.toggle('hero-slide-link-disabled', !hasUrl);
        slideLink.dataset.linkDisabled = hasUrl ? '0' : '1';

        if (isExternal) {
            slideLink.setAttribute('target', '_blank');
            slideLink.setAttribute('rel', 'noopener noreferrer');
        } else {
            slideLink.removeAttribute('target');
            slideLink.removeAttribute('rel');
        }
    };

    const updateLiveRegion = (index) => {
        if (!liveRegion) {
            return;
        }

        liveRegion.textContent = `第 ${index + 1} 張，共 ${slides.length} 張：${slides[index]?.imageAlt || ''}`;
    };

    const stopTimer = () => {
        if (timer) {
            window.clearInterval(timer);
            timer = null;
        }
    };

    const startTimer = () => {
        if (prefersReducedMotion || isPaused || slides.length <= 1) {
            return;
        }

        timer = window.setInterval(() => {
            render(currentIndex + 1);
        }, HERO_INTERVAL);
    };

    const render = (nextIndex, options = {}) => {
        const { announce = true, immediate = false } = options;

        currentIndex = (nextIndex + slides.length) % slides.length;

        slides.forEach((slide, index) => {
            const isActive = index === currentIndex;
            slide.image.classList.toggle('is-active', isActive);
            slide.dot?.classList.toggle('is-active', isActive);
            slide.dot?.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        const currentSlide = slides[currentIndex];
        syncSlideLink(currentSlide);

        if (announce) {
            updateLiveRegion(currentIndex);
        }

        hasRenderedOnce = true;
    };

    const restartTimer = () => {
        stopTimer();
        startTimer();
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

    carousel.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowRight') {
            event.preventDefault();
            render(currentIndex + 1);
            restartTimer();
        }

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            render(currentIndex - 1);
            restartTimer();
        }
    });

    carousel.addEventListener('mouseenter', () => {
        isPaused = true;
        stopTimer();
    });

    carousel.addEventListener('mouseleave', () => {
        isPaused = false;
        startTimer();
    });

    carousel.addEventListener('focusin', () => {
        isPaused = true;
        stopTimer();
    });

    carousel.addEventListener('focusout', (event) => {
        if (carousel.contains(event.relatedTarget)) {
            return;
        }

        isPaused = false;
        startTimer();
    });

    carousel.addEventListener('touchstart', (event) => {
        const touch = event.changedTouches?.[0];

        if (!touch) {
            return;
        }

        touchStartX = touch.clientX;
        touchStartY = touch.clientY;
    }, { passive: true });

    carousel.addEventListener('touchend', (event) => {
        const touch = event.changedTouches?.[0];

        if (!touch || slides.length <= 1) {
            return;
        }

        const deltaX = touch.clientX - touchStartX;
        const deltaY = touch.clientY - touchStartY;

        if (Math.abs(deltaX) <= HERO_SWIPE_THRESHOLD || Math.abs(deltaX) <= Math.abs(deltaY)) {
            return;
        }

        if (deltaX < 0) {
            render(currentIndex + 1);
        } else {
            render(currentIndex - 1);
        }

        restartTimer();
    }, { passive: true });

    images.forEach((image) => {
        image.addEventListener('error', () => {
            if (image.dataset.fallbackApplied === 'true') {
                return;
            }

            image.dataset.fallbackApplied = 'true';
            image.src = HERO_FALLBACK_IMAGE;
            image.classList.add('is-fallback');
            image.alt = image.dataset.fallbackAlt || '首頁輪播預設圖片';
        }, { once: true });
    });

    render(0, { announce: false, immediate: true });
    restartTimer();
};

const setupProductGallery = () => {
    const mainImage = document.querySelector('[data-main-image]');
    const thumbs = Array.from(document.querySelectorAll('[data-thumb]'));
    const dialog = document.getElementById('productImageDialog');
    const dialogImage = document.getElementById('productDialogImage');
    const openButton = document.querySelector('[data-open-product-image-dialog]');
    const closeButton = document.querySelector('[data-close-product-image-dialog]');

    if (!mainImage) {
        return;
    }

    const syncMainImage = (imageUrl, imageAlt) => {
        if (!imageUrl) {
            return;
        }

        mainImage.setAttribute('src', imageUrl);
        mainImage.setAttribute('alt', imageAlt || mainImage.getAttribute('alt') || '商品圖片');

        if (dialogImage) {
            dialogImage.setAttribute('src', imageUrl);
            dialogImage.setAttribute('alt', imageAlt || mainImage.getAttribute('alt') || '商品圖片');
        }
    };

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            syncMainImage(thumb.dataset.imageUrl || '', thumb.dataset.imageAlt || '');

            thumbs.forEach((item) => item.classList.remove('border-primary', 'ring-2', 'ring-primary/30'));
            thumb.classList.add('border-primary', 'ring-2', 'ring-primary/30');
        });
    });

    if (!dialog || typeof dialog.showModal !== 'function') {
        openButton?.addEventListener('click', () => {
            const imageUrl = mainImage.getAttribute('src') || '';
            if (imageUrl) {
                window.open(imageUrl, '_blank', 'noopener');
            }
        });
        return;
    }

    const restoreFocusToTrigger = () => {
        openButton?.focus();
    };

    openButton?.addEventListener('click', () => {
        syncMainImage(mainImage.getAttribute('src') || '', mainImage.getAttribute('alt') || '');
        dialog.showModal();
    });

    closeButton?.addEventListener('click', () => {
        dialog.close();
    });

    dialog.addEventListener('close', restoreFocusToTrigger);

    dialog.addEventListener('keydown', (event) => {
        if (event.key !== 'Tab') {
            return;
        }

        const focusable = dialog.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (!focusable.length) {
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    dialog.addEventListener('click', (event) => {
        const bounds = dialog.getBoundingClientRect();
        const isOutside = event.clientX < bounds.left
            || event.clientX > bounds.right
            || event.clientY < bounds.top
            || event.clientY > bounds.bottom;

        if (isOutside) {
            dialog.close();
        }
    });
};

const setupProductTagChips = () => {
    const checkboxes = Array.from(document.querySelectorAll('[data-tag-checkbox]'));

    if (!checkboxes.length) {
        return;
    }

    const syncChipState = (checkbox) => {
        const chip = checkbox.closest('[data-tag-chip]');
        if (!chip) {
            return;
        }

        chip.classList.toggle('border-primary', checkbox.checked);
        chip.classList.toggle('bg-primary/10', checkbox.checked);
        chip.classList.toggle('text-primary', checkbox.checked);

        chip.classList.toggle('border-outline-variant/60', !checkbox.checked);
        chip.classList.toggle('bg-surface-container-lowest', !checkbox.checked);
        chip.classList.toggle('text-on-surface/75', !checkbox.checked);
    };

    checkboxes.forEach((checkbox) => {
        syncChipState(checkbox);
        checkbox.addEventListener('change', () => syncChipState(checkbox));
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-hero-image]').forEach((image) => {
        const index = Number(image.dataset.slideIndex);
        const slide = window.__FRONTEND_HERO_SLIDES__?.[index];

        if (!slide) {
            return;
        }

        image.dataset.targetUrl = slide.target_url || '';
    });

    setupAlertBanner();
    setupMobileMenu();
    setupHeroCarousel();
    setupProductGallery();
    setupProductTagChips();
});

