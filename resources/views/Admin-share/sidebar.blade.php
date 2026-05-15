@php
    $avatarExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $avatarPath = $user['avatar'] ?? '';
    $avatarExt = strtolower(pathinfo($avatarPath, PATHINFO_EXTENSION));
    $hasAvatar = !empty($avatarPath) && in_array($avatarExt, $avatarExtensions);

    $currentRole = session(ADMIN_ROLE_SESSION);
    $roles = $user['roles'] ?? [];
@endphp

<aside class="admin-sidebar" data-admin-sidebar aria-label="管理後台導覽">
    <div class="admin-brand">
        <div class="admin-brand-mark">
            <span class="material-symbols-outlined" aria-hidden="true">rocket_launch</span>
        </div>
        <div>
            <h1 class="admin-brand-name">YoYoAdmin</h1>
            <p class="admin-brand-sub">管理後台</p>
        </div>
    </div>

    <div class="admin-user-panel">
        <div class="admin-user-row">
            @if($hasAvatar)
                <img src="{{ asset('storage/' . $avatarPath) }}" alt="使用者頭像" class="admin-avatar admin-avatar-img">
            @else
                <div class="admin-avatar" aria-hidden="true">
                    <span class="material-symbols-outlined">person</span>
                </div>
            @endif
            <div class="admin-user-info">
                <div class="admin-user-name">{{ $user['name'] ?? '-' }}</div>
                @if(!empty($currentRole))
                    @if(count($roles) > 1)
                        <a class="admin-user-role" href="{{ url('admin/select-role') }}">
                            {{ $currentRole['name'] }}
                            <span class="material-symbols-outlined" aria-hidden="true">sync</span>
                        </a>
                    @else
                        <span class="admin-user-role">{{ $currentRole['name'] }}</span>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <nav class="admin-nav" aria-label="後台主導覽">
        @foreach(($menu ?? []) as $item)
            @if(!empty($item['have_item']))
                <div class="admin-nav-group {{ !empty($item['item_open']) ? 'is-open' : '' }}" data-admin-nav-group>
                    <button type="button"
                        class="admin-nav-toggle"
                        data-admin-nav-toggle
                        aria-expanded="{{ !empty($item['item_open']) ? 'true' : 'false' }}"
                    >
                        <i class="{{ $item['item_icon'] ?? 'fa-solid fa-folder' }} admin-nav-icon" aria-hidden="true"></i>
                        <span class="admin-nav-label">{{ $item['item_name'] }}</span>
                        <span class="material-symbols-outlined admin-nav-chev" aria-hidden="true">chevron_right</span>
                    </button>
                    <div class="admin-nav-sub">
                        @foreach($item['details'] as $detail)
                            <a href="{{ asset($detail['url'] ?? '#') }}"
                               class="admin-nav-link {{ !empty($detail['is_open']) ? 'is-active' : '' }}"
                               @if(!empty($detail['is_open'])) aria-current="page" @endif
                            >
                                <i class="{{ $detail['icon'] ?? 'fa-regular fa-circle' }} admin-nav-icon" aria-hidden="true"></i>
                                <span class="admin-nav-label">{{ $detail['name'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                @foreach($item['details'] as $detail)
                    <a href="{{ asset($detail['url'] ?? '#') }}"
                       class="admin-nav-link {{ !empty($detail['is_open']) ? 'is-active' : '' }}"
                       @if(!empty($detail['is_open'])) aria-current="page" @endif
                    >
                        <i class="{{ $detail['icon'] ?? 'fa-regular fa-circle' }} admin-nav-icon" aria-hidden="true"></i>
                        <span class="admin-nav-label">{{ $detail['name'] }}</span>
                    </a>
                @endforeach
            @endif
        @endforeach
    </nav>

    <div class="admin-sidebar-foot">
        <a href="{{ url('admin/logout') }}" class="admin-nav-link admin-nav-logout">
            <span class="material-symbols-outlined admin-nav-icon" aria-hidden="true">logout</span>
            <span class="admin-nav-label">登出</span>
        </a>
    </div>
</aside>
