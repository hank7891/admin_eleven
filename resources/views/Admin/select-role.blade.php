@extends('layouts.admin-guest')

@section('title-suffix', ' · 選擇角色')

@section('content')
<div class="admin-login-body">
    <div class="admin-login-wrap admin-select-role-wrap">
        <h1 class="admin-login-brand">YoYoAdmin</h1>

        <main class="admin-login-card admin-select-role-card">
            <div class="admin-select-role-head">
                <h2>選擇角色</h2>
                <p class="admin-page-sub">請選擇要使用的角色</p>
            </div>

            <div class="admin-select-role-grid">
                @foreach($roles as $role)
                    @php($isActive = ($currentRole['id'] ?? null) == $role['id'])
                    <form action="{{ url('admin/select-role') }}" method="POST" class="admin-select-role-form">
                        @csrf
                        <input type="hidden" name="role_id" value="{{ $role['id'] }}">
                        <button type="submit" class="admin-select-role-btn {{ $isActive ? 'is-active' : '' }}">
                            <div class="admin-select-role-icon" aria-hidden="true">
                                <span class="material-symbols-outlined">shield_person</span>
                            </div>
                            <span class="admin-select-role-name">{{ $role['role_name'] }}</span>
                            @if ($isActive)
                                <span class="admin-select-role-flag">目前使用中</span>
                                <span class="material-symbols-outlined admin-select-role-check" aria-hidden="true">check_circle</span>
                            @endif
                        </button>
                    </form>
                @endforeach
            </div>
        </main>

        <footer class="admin-login-foot">
            &copy; {{ date('Y') }} YoYoAdmin. All rights reserved.
        </footer>
    </div>
</div>
@endsection
