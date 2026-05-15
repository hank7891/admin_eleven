@extends('layouts.frontend')

@section('fe-active', '')

@section('content')
    <section class="fe-auth-wrap">
        <div class="fe-auth-card">
            <div class="fe-auth-head">
                <span class="fe-eyebrow is-muted">Member Sign In</span>
                <h1 class="fe-auth-title">會員登入</h1>
                <p class="fe-body fe-auth-sub">輸入帳密進入會員專區，繼續日常的探索。</p>
            </div>

            @if ($errors->any())
                <div class="fe-form-error fe-auth-alert" role="alert" data-login-server-alert>
                    <span class="material-symbols-outlined" aria-hidden="true">error</span>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <div class="fe-form-error fe-auth-alert is-hidden" role="alert" data-login-client-alert>
                <span class="material-symbols-outlined" aria-hidden="true">error</span>
                <p data-login-client-alert-text></p>
            </div>

            <form method="POST" action="{{ url('member/login') }}" class="fe-auth-form" novalidate data-login-form>
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
                        autocomplete="username"
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
                        placeholder="請輸入密碼"
                        autocomplete="current-password"
                        class="fe-input"
                    >
                    <p class="fe-form-hint">儲存失敗時需重新輸入密碼</p>
                </div>

                <button type="submit" class="fe-btn fe-btn-primary fe-auth-submit">會員登入</button>
            </form>

            <p class="fe-auth-foot">
                <a href="{{ url('member/register') }}">尚未註冊？立即建立會員帳號</a>
            </p>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.querySelector('[data-login-form]');
            if (!form) {
                return;
            }

            const clientAlert = document.querySelector('[data-login-client-alert]');
            const clientAlertText = document.querySelector('[data-login-client-alert-text]');

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
                const password = form.password?.value || '';

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

                if (password === '') {
                    event.preventDefault();
                    showClientAlert('請輸入密碼。');
                    form.password?.focus();
                }
            });
        })();
    </script>
@endpush
