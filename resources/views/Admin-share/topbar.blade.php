@php
    $avatarExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $avatarPath = $user['avatar'] ?? '';
    $avatarExt = strtolower(pathinfo($avatarPath, PATHINFO_EXTENSION));
    $hasAvatar = !empty($avatarPath) && in_array($avatarExt, $avatarExtensions);
@endphp

<header class="admin-navbar" role="banner">
    <div class="admin-navbar-left">
        <button
            type="button"
            class="admin-iconbtn admin-sidebar-toggle"
            data-admin-sidebar-toggle
            aria-label="開啟側邊選單"
            aria-expanded="false"
            aria-controls="admin-sidebar"
        >
            <span class="material-symbols-outlined" aria-hidden="true">menu</span>
        </button>

        <h1 class="admin-navbar-title">YoYoAdmin</h1>
        <div class="admin-search" role="search" aria-hidden="true">
            <span class="material-symbols-outlined" aria-hidden="true">search</span>
            <input type="text" placeholder="搜尋..." disabled>
        </div>
    </div>

    <div class="admin-navbar-right">
        <button type="button" class="admin-iconbtn" aria-label="通知（暫不可用）" disabled>
            <span class="material-symbols-outlined" aria-hidden="true">notifications</span>
        </button>
        <div class="admin-divider-v" aria-hidden="true"></div>

        @if($hasAvatar)
            <img src="{{ asset('storage/' . $avatarPath) }}" alt="使用者頭像" class="admin-avatar admin-avatar-img admin-avatar-sm">
        @else
            <div class="admin-avatar admin-avatar-sm" aria-hidden="true">
                <span class="material-symbols-outlined">person</span>
            </div>
        @endif
        <span class="admin-text-sm admin-user-name-inline">{{ $user['name'] ?? '' }}</span>

        <a href="{{ url('admin/logout') }}" class="admin-iconbtn" title="登出" aria-label="登出">
            <span class="material-symbols-outlined" aria-hidden="true">logout</span>
        </a>
    </div>
</header>
