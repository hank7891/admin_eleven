export const setupProductGallery = () => {
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
        if (!imageUrl) return;
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
            thumbs.forEach((item) => item.classList.remove('is-active'));
            thumb.classList.add('is-active');
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
        if (event.key !== 'Tab') return;
        const focusable = dialog.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (!focusable.length) return;
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
