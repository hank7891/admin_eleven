@php($currentMember = session(MEMBER_AUTH_SESSION))
<header class="fe-header" role="banner">
    <div class="fe-header-inner">
        <div class="fe-header-left">
            <button
                type="button"
                class="fe-icon-pill fe-header-menu-btn"
                aria-label="開啟導覽選單"
                aria-expanded="false"
                aria-controls="frontend-mobile-menu"
                data-mobile-menu-toggle
            >
                <span class="material-symbols-outlined" aria-hidden="true">menu</span>
            </button>

            <a href="{{ url('/') }}" class="fe-brand" aria-label="Aura and Heirloom · 回首頁">
                Aura &amp; Heirloom
            </a>
        </div>

        <nav class="fe-nav" aria-label="前台主導覽">
            @foreach ($navItems ?? [] as $item)
                <a
                    href="{{ $item['url'] }}"
                    class="{{ !empty($item['is_active']) ? 'is-active text-primary' : '' }}"
                    @if(!empty($item['is_active'])) aria-current="page" @endif
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="fe-header-actions">
            @if (!empty($currentMember))
                <a href="{{ url('member/profile') }}" class="fe-link-pill" aria-label="會員專區">
                    <span class="material-symbols-outlined" aria-hidden="true">person</span>
                    <span>{{ $currentMember['name'] ?? '會員' }}</span>
                </a>
                <form method="POST" action="{{ url('member/logout') }}" class="fe-logout-form">
                    @csrf
                    <button type="submit" class="fe-icon-pill" aria-label="會員登出">
                        <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                    </button>
                </form>
            @else
                <a href="{{ url('member/login') }}" class="fe-link-pill">
                    <span class="material-symbols-outlined" aria-hidden="true">person</span>
                    <span>會員入口</span>
                </a>
            @endif
        </div>
    </div>

    <div id="frontend-mobile-menu" class="fe-mobile-menu is-hidden" data-mobile-menu>
        <nav class="fe-mobile-nav" aria-label="前台手機導覽">
            @foreach ($navItems ?? [] as $item)
                <a href="{{ $item['url'] }}" class="{{ !empty($item['is_active']) ? 'is-active' : '' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</header>
