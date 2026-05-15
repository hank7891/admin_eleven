@extends('layouts.admin-guest')

@section('title-suffix', ' · 登入')

@section('content')
@php
    $serverMessages = session(ADMIN_MESSAGE_SESSION, []);
    session()->forget(ADMIN_MESSAGE_SESSION);
@endphp

<div class="admin-login-body">
    <div class="admin-login-wrap">
        <h1 class="admin-login-brand">YoYoAdmin</h1>

        <main class="admin-login-card" id="admin-content" tabindex="-1">
            <h2>歡迎回來</h2>
            <p class="admin-page-sub">請登入您的帳號</p>

            @if ($errors->any())
                <div class="admin-form-error" role="alert">
                    <span class="material-symbols-outlined" aria-hidden="true">error</span>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            @if (is_array($serverMessages) && !empty($serverMessages))
                @foreach ($serverMessages as $message)
                    @php
                        $type = $message['type'] ?? 'danger';
                        $toneClass = match ($type) {
                            'success' => 'admin-form-success',
                            'warning' => 'admin-form-warning',
                            default => 'admin-form-error',
                        };
                        $icon = match ($type) {
                            'success' => 'check_circle',
                            'warning' => 'warning',
                            default => 'error',
                        };
                    @endphp
                    <div class="{{ $toneClass }}" role="alert">
                        <span class="material-symbols-outlined" aria-hidden="true">{{ $icon }}</span>
                        <p>{{ $message['message'] ?? '' }}</p>
                    </div>
                @endforeach
            @endif

            <form action="{{ url('admin/login') }}" method="POST" class="admin-stack admin-login-form" novalidate>
                @csrf

                <div class="admin-field">
                    <label class="admin-label" for="login-account">帳號</label>
                    <div class="admin-input-icon">
                        <span class="material-symbols-outlined" aria-hidden="true">person</span>
                        <input
                            id="login-account"
                            class="admin-input"
                            type="text"
                            name="account"
                            value="{{ old('account') }}"
                            placeholder="請輸入使用者名稱"
                            autocomplete="username"
                            required
                            aria-required="true"
                        >
                    </div>
                </div>

                <div class="admin-field">
                    <label class="admin-label" for="login-password">密碼</label>
                    <div class="admin-input-icon">
                        <span class="material-symbols-outlined" aria-hidden="true">lock</span>
                        <input
                            id="login-password"
                            class="admin-input"
                            type="password"
                            name="password"
                            placeholder="請輸入您的密碼"
                            autocomplete="current-password"
                            required
                            aria-required="true"
                        >
                    </div>
                </div>

                <label class="admin-login-remember">
                    <input type="checkbox" name="remember" value="1" class="admin-checkbox">
                    <span>記住我</span>
                </label>

                <button type="submit" class="admin-btn admin-btn-primary admin-login-submit">
                    <span>登入</span>
                    <span class="material-symbols-outlined" aria-hidden="true">login</span>
                </button>
            </form>
        </main>

        <footer class="admin-login-foot">
            &copy; {{ date('Y') }} YoYoAdmin. All rights reserved.
        </footer>
    </div>
</div>
@endsection
