import { getJson } from '../../shared/csrf.js';

const ICON_BY_TYPE = {
    success: 'check_circle',
    danger: 'error',
    warning: 'warning',
    info: 'info',
};

const TONE_CLASS = {
    success: 'admin-toast-success',
    danger: 'admin-toast-danger',
    warning: 'admin-toast-warning',
    info: 'admin-toast-info',
};

const AUTO_DISMISS_MS = 4000;
const FADE_MS = 280;

const ensureArea = () => {
    let area = document.getElementById('admin-toast-area');
    if (!area) {
        area = document.createElement('div');
        area.id = 'admin-toast-area';
        area.setAttribute('role', 'status');
        area.setAttribute('aria-live', 'polite');
        document.body.appendChild(area);
    }
    return area;
};

const dismiss = (node) => {
    if (!node || node.dataset.dismissed === '1') return;
    node.dataset.dismissed = '1';
    node.classList.add('is-leaving');
    window.setTimeout(() => node.remove(), FADE_MS);
};

const renderMessage = (type, message) => {
    const area = ensureArea();
    const tone = TONE_CLASS[type] || TONE_CLASS.danger;
    const icon = ICON_BY_TYPE[type] || ICON_BY_TYPE.info;

    const node = document.createElement('div');
    node.className = `admin-toast ${tone}`;
    node.setAttribute('role', 'alert');
    node.innerHTML = `
        <span class="material-symbols-outlined admin-toast-icon" aria-hidden="true">${icon}</span>
        <span class="admin-toast-body">${escapeHtml(message)}</span>
        <button type="button" class="admin-toast-close" aria-label="關閉訊息">
            <span class="material-symbols-outlined" aria-hidden="true">close</span>
        </button>
    `;

    node.querySelector('.admin-toast-close')?.addEventListener('click', () => dismiss(node));
    area.appendChild(node);
    window.setTimeout(() => dismiss(node), AUTO_DISMISS_MS);
};

const escapeHtml = (text) =>
    String(text ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');

export const setupFlashMessage = async () => {
    const meta = document.querySelector('meta[name="admin-message-endpoint"]');
    const endpoint = meta?.getAttribute('content');
    if (!endpoint) {
        return;
    }

    try {
        const data = await getJson(endpoint);
        if (!data || data.status === 1 || !Array.isArray(data.data)) {
            return;
        }
        data.data.forEach((msg) => {
            renderMessage(msg.type || 'info', msg.message || '');
        });
    } catch (err) {
        // 後端取訊息失敗時不影響頁面渲染
    }

    // upload_error fallback（保留既有行為）
    const url = new URL(window.location.href);
    if (url.searchParams.get('upload_error') === 'too_large') {
        renderMessage('danger', '上傳檔案過大，請選擇較小檔案後重試。');
        url.searchParams.delete('upload_error');
        window.history.replaceState({}, document.title, url.pathname + (url.searchParams.toString() ? `?${url.searchParams.toString()}` : ''));
    }
};

export { renderMessage as renderAdminToast };
