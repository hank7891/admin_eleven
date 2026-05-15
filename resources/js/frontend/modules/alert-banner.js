export const setupAlertBanner = () => {
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
