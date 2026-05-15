/**
 * Member auth (login / register) 客戶端驗證
 * 對應 Blade：Frontend/member/login.blade.php、Frontend/member/register.blade.php
 *
 * 觸發條件：表單帶 data-login-form 或 data-register-form
 * Alert 區塊：data-login-client-alert / data-register-client-alert
 */

const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

const showAlert = (alertEl, textEl, message) => {
    if (!alertEl || !textEl) return;
    textEl.textContent = message;
    alertEl.classList.remove('is-hidden');
};

const hideAlert = (alertEl) => {
    if (!alertEl) return;
    alertEl.classList.add('is-hidden');
};

const setupLoginForm = () => {
    const form = document.querySelector('[data-login-form]');
    if (!form) return;

    const clientAlert = document.querySelector('[data-login-client-alert]');
    const clientAlertText = document.querySelector('[data-login-client-alert-text]');

    form.addEventListener('submit', (event) => {
        hideAlert(clientAlert);

        const email = (form.email?.value || '').trim();
        const password = form.password?.value || '';

        if (email === '') {
            event.preventDefault();
            showAlert(clientAlert, clientAlertText, '請輸入電子信箱。');
            form.email?.focus();
            return;
        }

        if (!EMAIL_PATTERN.test(email)) {
            event.preventDefault();
            showAlert(clientAlert, clientAlertText, '電子信箱格式錯誤，請重新輸入。');
            form.email?.focus();
            return;
        }

        if (password === '') {
            event.preventDefault();
            showAlert(clientAlert, clientAlertText, '請輸入密碼。');
            form.password?.focus();
        }
    });
};

const setupRegisterForm = () => {
    const form = document.querySelector('[data-register-form]');
    if (!form) return;

    const clientAlert = document.querySelector('[data-register-client-alert]');
    const clientAlertText = document.querySelector('[data-register-client-alert-text]');

    form.addEventListener('submit', (event) => {
        hideAlert(clientAlert);

        const email = (form.email?.value || '').trim();
        const name = (form.name?.value || '').trim();
        const password = form.password?.value || '';
        const passwordConfirmation = form.password_confirmation?.value || '';

        if (email === '') {
            event.preventDefault();
            showAlert(clientAlert, clientAlertText, '請輸入電子信箱。');
            form.email?.focus();
            return;
        }

        if (!EMAIL_PATTERN.test(email)) {
            event.preventDefault();
            showAlert(clientAlert, clientAlertText, '電子信箱格式錯誤，請重新輸入。');
            form.email?.focus();
            return;
        }

        if (name === '') {
            event.preventDefault();
            showAlert(clientAlert, clientAlertText, '請輸入姓名。');
            form.name?.focus();
            return;
        }

        if (password.length < 8) {
            event.preventDefault();
            showAlert(clientAlert, clientAlertText, '密碼至少需要 8 個字元。');
            form.password?.focus();
            return;
        }

        if (password !== passwordConfirmation) {
            event.preventDefault();
            showAlert(clientAlert, clientAlertText, '兩次密碼輸入不一致。');
            form.password_confirmation?.focus();
        }
    });
};

export const setupAuthValidation = () => {
    setupLoginForm();
    setupRegisterForm();
};
