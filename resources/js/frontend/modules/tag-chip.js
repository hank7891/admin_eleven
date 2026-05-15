export const setupProductTagChips = () => {
    const checkboxes = Array.from(document.querySelectorAll('[data-tag-checkbox]'));
    if (!checkboxes.length) {
        return;
    }

    const syncChipState = (checkbox) => {
        const chip = checkbox.closest('[data-tag-chip]');
        if (!chip) return;
        chip.classList.toggle('is-active', checkbox.checked);
        chip.setAttribute('aria-pressed', checkbox.checked ? 'true' : 'false');
    };

    checkboxes.forEach((checkbox) => {
        syncChipState(checkbox);
        checkbox.addEventListener('change', () => syncChipState(checkbox));
    });
};
