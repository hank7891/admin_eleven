/**
 * Modal focus trap — 共用 module
 *
 * 用法：
 *   const trap = createFocusTrap(modalEl, {
 *       onEscape: () => closeModal(),
 *   });
 *   trap.activate();   // 開啟 modal 前呼叫
 *   trap.deactivate(); // 關閉 modal 後呼叫
 *
 * 行為：
 *   - activate：記錄當前 activeElement、auto-focus 首個 focusable、綁 Tab 循環 + Esc keydown
 *   - deactivate：移除監聽、focus 還原到 activate 前的元素
 *   - idempotent：重複 activate / deactivate 不會多次綁監聽或重複還原
 *   - 無 focusable 元素：early return（不報錯）
 */

const DEFAULT_FOCUSABLE_SELECTOR =
    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';

export const createFocusTrap = (modalEl, opts = {}) => {
    if (!modalEl) {
        return { activate() {}, deactivate() {} };
    }

    const focusableSelector = opts.focusableSelector || DEFAULT_FOCUSABLE_SELECTOR;
    const onEscape = typeof opts.onEscape === 'function' ? opts.onEscape : null;

    let isActive = false;
    let triggerElement = null;

    const getFocusable = () => Array.from(modalEl.querySelectorAll(focusableSelector));

    const handleKeydown = (event) => {
        if (event.key === 'Escape') {
            if (onEscape) {
                onEscape();
            }
            return;
        }

        if (event.key !== 'Tab') return;

        const focusable = getFocusable();
        if (!focusable.length) return;

        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        const active = document.activeElement;

        if (event.shiftKey && active === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && active === last) {
            event.preventDefault();
            first.focus();
        }
    };

    return {
        activate() {
            // idempotent — 已啟用則不重複綁
            if (isActive) return;

            triggerElement = document.activeElement instanceof HTMLElement
                ? document.activeElement
                : null;

            modalEl.addEventListener('keydown', handleKeydown);

            // auto-focus 首個 focusable
            const focusable = getFocusable();
            if (focusable.length > 0) {
                focusable[0].focus();
            }

            isActive = true;
        },
        deactivate() {
            // idempotent — 未啟用則不重複還原
            if (!isActive) return;

            modalEl.removeEventListener('keydown', handleKeydown);

            // 還原 focus 到 activate 前的 trigger
            if (triggerElement && typeof triggerElement.focus === 'function') {
                triggerElement.focus();
            }
            triggerElement = null;

            isActive = false;
        },
    };
};
