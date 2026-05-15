export const setupTableBulk = () => {
    const containers = document.querySelectorAll('[data-bulk-table]');
    containers.forEach(initBulkTable);
};

const initBulkTable = (container) => {
    const toggleAll = container.querySelector('[data-bulk-toggle-all]');
    const items = container.querySelectorAll('[data-bulk-item]');
    const counter = container.querySelector('[data-bulk-counter]');
    const toolbar = container.querySelector('[data-bulk-toolbar]');

    if (!items.length) {
        return;
    }

    const sync = () => {
        const total = items.length;
        const checked = Array.from(items).filter((item) => item.checked).length;

        if (toggleAll) {
            toggleAll.checked = total > 0 && checked === total;
            toggleAll.indeterminate = checked > 0 && checked < total;
        }
        if (counter) {
            counter.textContent = String(checked);
        }
        if (toolbar) {
            toolbar.classList.toggle('is-visible', checked > 0);
        }
    };

    toggleAll?.addEventListener('change', () => {
        items.forEach((item) => {
            item.checked = toggleAll.checked;
        });
        sync();
    });

    items.forEach((item) => {
        item.addEventListener('change', sync);
    });

    sync();
};
