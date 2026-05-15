const AUTO_DISMISS_MS = 6000;
const FADE_MS = 260;

const dismiss = (item) => {
    if (!item || item.dataset.dismissed === '1') return;
    item.dataset.dismissed = '1';
    item.classList.add('is-leaving');
    window.setTimeout(() => {
        item.parentNode?.removeChild(item);
    }, FADE_MS);
};

export const setupFlashMessage = () => {
    const items = document.querySelectorAll('[data-frontend-flash-item]');
    if (!items.length) return;

    items.forEach((item) => {
        item.addEventListener('click', () => dismiss(item));
        item.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                dismiss(item);
            }
        });
        window.setTimeout(() => dismiss(item), AUTO_DISMISS_MS);
    });
};
