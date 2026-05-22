/**
 * 後台批次操作確認 modal（product/list bulk 上/下架）
 *
 * 既有 inline script 搬移 + 注入 shared/focus-trap.js
 * 對應元素：bulkStatusTrigger / bulkConfirmModal / bulkStatusForm / bulkStatusSelect
 */

import { createFocusTrap } from '../../shared/focus-trap.js';

export const setupBulkConfirm = () => {
    const trigger = document.getElementById('bulkStatusTrigger');
    const modal = document.getElementById('bulkConfirmModal');
    const message = document.getElementById('bulkConfirmMessage');
    const cancelBtn = document.getElementById('bulkConfirmCancel');
    const okBtn = document.getElementById('bulkConfirmOk');
    const form = document.getElementById('bulkStatusForm');
    const select = document.getElementById('bulkStatusSelect');
    const backdrop = modal?.querySelector('[data-modal-close]');

    if (!trigger || !modal || !form || !select) {
        return;
    }

    const trap = createFocusTrap(modal, {
        onEscape: () => {
            if (!modal.hasAttribute('hidden')) {
                closeModal();
            }
        },
    });

    const openModal = () => {
        modal.removeAttribute('hidden');
        trap.activate();
    };

    const closeModal = () => {
        trap.deactivate();
        modal.setAttribute('hidden', '');
    };

    trigger.addEventListener('click', () => {
        const checked = form.querySelectorAll('[data-bulk-item]:checked');
        if (checked.length === 0) return;
        const action = select.options[select.selectedIndex].text;
        if (message) {
            message.textContent = `即將對 ${checked.length} 件商品執行「${action}」，確定要繼續嗎？`;
        }
        openModal();
    });

    cancelBtn?.addEventListener('click', closeModal);
    backdrop?.addEventListener('click', closeModal);
    okBtn?.addEventListener('click', () => {
        form.submit();
    });
};
