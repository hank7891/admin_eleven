export const setupMobileMenu = () => {
    const toggleButton = document.querySelector('[data-mobile-menu-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');

    if (!toggleButton || !menu) {
        return;
    }

    const syncMenu = (isOpen) => {
        menu.classList.toggle('is-open', isOpen);
        menu.classList.toggle('is-hidden', !isOpen);
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
