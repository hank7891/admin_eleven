import { setupSidebar } from './modules/sidebar-nav.js';
import { setupTableBulk } from './modules/table-bulk.js';
import { setupToggleActive } from './modules/toggle-active.js';
import { setupFlashMessage } from './modules/flash-message.js';

const boot = () => {
    setupSidebar();
    setupTableBulk();
    setupToggleActive();
    setupFlashMessage();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
