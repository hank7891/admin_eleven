import { setupAlertBanner } from './modules/alert-banner.js';
import { setupMobileMenu } from './modules/mobile-menu.js';
import { setupHeroCarousel } from './modules/carousel.js';
import { setupProductGallery } from './modules/product-gallery.js';
import { setupProductTagChips } from './modules/tag-chip.js';
import { setupFlashMessage } from './modules/flash-message.js';
import { setupAuthValidation } from './modules/auth-validation.js';

const boot = () => {
    setupAlertBanner();
    setupMobileMenu();
    setupHeroCarousel();
    setupProductGallery();
    setupProductTagChips();
    setupFlashMessage();
    setupAuthValidation();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
