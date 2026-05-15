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

{{-- client validation 邏輯已抽離至 resources/js/frontend/modules/auth-validation.js（由 layouts.frontend 載入 frontend/index.js 啟用） --}}
