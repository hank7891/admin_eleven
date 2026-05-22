import { setupSidebar } from './modules/sidebar-nav.js';
import { setupTableBulk } from './modules/table-bulk.js';
import { setupTableSort } from './modules/table-sort.js';
import { setupToggleActive } from './modules/toggle-active.js';
import { setupFlashMessage } from './modules/flash-message.js';
import { setupBulkConfirm } from './modules/bulk-confirm.js';

const boot = () => {
    setupSidebar();
    setupTableBulk();
    setupTableSort();
    setupToggleActive();
    setupFlashMessage();
    setupBulkConfirm();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
