@extends('layouts.admin-guest')

@section('title-suffix', ' · 系統通知')

@section('content')
<div class="admin-login-body">
    <div class="admin-login-wrap admin-notice-wrap">
        <main class="admin-login-card admin-notice-card">
            @php
                $icon = match ($notice['type'] ?? '') {
                    'error' => 'error',
                    'warning' => 'warning',
                    'success' => 'check_circle',
                    default => 'info',
                };
                $tone = match ($notice['type'] ?? '') {
                    'error' => 'admin-notice-icon-danger',
                    'warning' => 'admin-notice-icon-warning',
                    'success' => 'admin-notice-icon-success',
                    default => 'admin-notice-icon-info',
                };
            @endphp
            <span class="material-symbols-outlined admin-notice-icon {{ $tone }}" aria-hidden="true">{{ $icon }}</span>
            <h2 class="admin-notice-title">{{ $notice['title'] }}</h2>
            <p class="admin-notice-message">{{ $notice['message'] }}</p>
            <a href="{{ url('admin/login') }}" class="admin-btn admin-btn-primary admin-notice-action">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                <span>返回登入頁</span>
            </a>
        </main>

        <footer class="admin-login-foot">
            &copy; {{ date('Y') }} YoYoAdmin
        </footer>
    </div>
</div>
@endsection
