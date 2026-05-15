@extends('layouts.frontend')

@section('fe-active', '')

@section('content')
    <section class="fe-auth-wrap">
        <div class="fe-auth-card">
            <div class="fe-auth-head">
                <span class="fe-eyebrow is-muted">Create an Account</span>
                <h1 class="fe-auth-title">加入會員</h1>
                <p class="fe-body fe-auth-sub">建立 Aura &amp; Heirloom 會員帳號，開啟更靠近品牌日常的入口。</p>
            </div>

            @if ($errors->any())
                <div class="fe-form-error fe-auth-alert" role="alert" data-register-server-alert>
                    <span class="material-symbols-outlined" aria-hidden="true">error</span>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <div class="fe-form-error fe-auth-alert is-hidden" role="alert" data-register-client-alert>
                <span class="material-symbols-outlined" aria-hidden="true">error</span>
                <p data-register-client-alert-text></p>
            </div>

            <form method="POST" action="{{ url('member/register') }}" class="fe-auth-form" novalidate data-register-form>
                @csrf

                <div class="fe-form-field">
                    <label class="fe-form-label" for="email">電子信箱</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        required
                        aria-required="true"
                        maxlength="255"
                        value="{{ old('email', $formData['email'] ?? '') }}"
                        placeholder="your@email.com"
                        autocomplete="email"
                        class="fe-input"
                    >
                </div>

                <div class="fe-form-field">
                    <label class="fe-form-label" for="name">姓名</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        required
                        aria-required="true"
                        maxlength="100"
                        value="{{ old('name', $formData['name'] ?? '') }}"
                        placeholder="請輸入您的姓名"
                        autocomplete="name"
                        class="fe-input"
                    >
                </div>

                <div class="fe-form-field">
                    <label class="fe-form-label" for="password">密碼</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        aria-required="true"
                        minlength="8"
                        placeholder="至少 8 個字元"
                        autocomplete="new-password"
                        class="fe-input"
                    >
                    <p class="fe-form-hint">密碼至少需要 8 個字元，儲存失敗時需重新輸入密碼</p>
                </div>

                <div class="fe-form-field">
                    <label class="fe-form-label" for="password_confirmation">確認密碼</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        aria-required="true"
                        minlength="8"
                        placeholder="請再次輸入密碼"
                        autocomplete="new-password"
                        class="fe-input"
                    >
                </div>

                <button type="submit" class="fe-btn fe-btn-primary fe-auth-submit">建立會員帳號</button>
            </form>

            <p class="fe-auth-foot">
                <a href="{{ url('member/login') }}">已經有帳號？立即登入</a>
            </p>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.querySelector('[data-register-form]');
            if (!form) {
                return;
            }

            const clientAlert = document.querySelector('[data-register-client-alert]');
            const clientAlertText = document.querySelector('[data-register-client-alert-text]');

            function showClientAlert(message) {
                if (!clientAlert || !clientAlertText) {
                    return;
                }
                clientAlertText.textContent = message;
                clientAlert.classList.remove('is-hidden');
            }

            function hideClientAlert() {
                if (!clientAlert) {
                    return;
                }
                clientAlert.classList.add('is-hidden');
            }

            form.addEventListener('submit', function (event) {
                hideClientAlert();

                const email = (form.email?.value || '').trim();
                const name = (form.name?.value || '').trim();
                const password = form.password?.value || '';
                const passwordConfirmation = form.password_confirmation?.value || '';

                if (email === '') {
                    event.preventDefault();
                    showClientAlert('請輸入電子信箱。');
                    form.email?.focus();
                    return;
                }

                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    event.preventDefault();
                    showClientAlert('電子信箱格式錯誤，請重新輸入。');
                    form.email?.focus();
                    return;
                }

                if (name === '') {
                    event.preventDefault();
                    showClientAlert('請輸入姓名。');
                    form.name?.focus();
                    return;
                }

                if (password.length < 8) {
                    event.preventDefault();
                    showClientAlert('密碼至少需要 8 個字元。');
                    form.password?.focus();
                    return;
                }

                if (password !== passwordConfirmation) {
                    event.preventDefault();
                    showClientAlert('兩次密碼輸入不一致。');
                    form.password_confirmation?.focus();
                }
            });
        })();
    </script>
@endpush
