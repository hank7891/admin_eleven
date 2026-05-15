import { postJson } from '../../shared/csrf.js';

export const setupToggleActive = () => {
    const buttons = document.querySelectorAll('[data-toggle-active-url]');

    buttons.forEach((btn) => {
        btn.addEventListener('click', async (event) => {
            event.preventDefault();
            if (btn.dataset.busy === '1') {
                return;
            }
            const url = btn.dataset.toggleActiveUrl;
            if (!url) return;

            btn.dataset.busy = '1';
            try {
                const result = await postJson(url, {});
                if (result?.status === 0 || result?.success === true) {
                    // 後端回應成功，重新整理頁面取得最新狀態
                    window.location.reload();
                } else {
                    const msg = result?.msg || result?.message || '操作失敗，請稍後重試';
                    window.alert(msg);
                }
            } catch (err) {
                window.alert('操作失敗：' + (err?.message || '網路錯誤'));
            } finally {
                btn.dataset.busy = '0';
            }
        });
    });
};
