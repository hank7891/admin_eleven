@php
    # 大頭照處理
    $avatarExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $avatarPath = $user['avatar'] ?? '';
    $avatarExt = strtolower(pathinfo($avatarPath, PATHINFO_EXTENSION));
    $hasAvatar = !empty($avatarPath) && in_array($avatarExt, $avatarExtensions);

    # 目前角色
    $currentRole = session(ADMIN_ROLE_SESSION);
    $roles = $user['roles'] ?? [];
@endphp

<aside class="fixed left-0 top-0 h-screen w-64 bg-inverse-surface flex flex-col z-50 overflow-y-auto">
    {{-- 品牌 Logo --}}
    <div class="flex items-center gap-3 px-5 py-5 mb-2">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
            <span class="material-symbols-outlined text-[22px]">rocket_launch</span>
        </div>
        <div>
            <h1 class="text-[1rem] font-bold text-inverse-on-surface leading-tight font-headline">YoYoAdmin</h1>
            <p class="text-[0.625rem] text-inverse-on-surface/50 uppercase tracking-[0.15em] font-semibold">管理後台</p>
        </div>
    </div>

    {{-- 使用者面板 --}}
    <div class="px-4 pb-4 mb-2 border-b border-inverse-on-surface/10">
        <div class="flex items-center gap-3 px-2">
            @if($hasAvatar)
                <img src="{{ asset('storage/' . $avatarPath) }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover border-2 border-inverse-on-surface/20">
            @else
                <div class="w-9 h-9 rounded-full bg-inverse-on-surface/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-inverse-on-surface/60 text-[18px]">person</span>
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-[0.8125rem] font-semibold text-inverse-on-surface truncate">{{ $user['name'] ?? '-' }}</p>
                @if(!empty($currentRole))
                    @if(count($roles) > 1)
                        <a href="{{ url('admin/select-role') }}" class="text-[0.6875rem] text-inverse-on-surface/50 hover:text-[#b9c3ff] transition-colors flex items-center gap-1 no-underline">
                            {{ $currentRole['name'] }}
                            <span class="material-symbols-outlined text-[12px]">sync</span>
                        </a>
                    @else
                        <p class="text-[0.6875rem] text-inverse-on-surface/50">{{ $currentRole['name'] }}</p>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- 導覽選單 --}}
    <nav class="flex-1 px-3 space-y-1 overflow-y-auto">
        @foreach($menu as $item)
            @if($item['have_item'])
                {{-- 群組選單 --}}
                <div x-data="{ open: {{ $item['item_open'] ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="open = !open" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-inverse-on-surface/70 hover:bg-inverse-on-surface/5 transition-all duration-200 group">
                        <i class="{{ $item['item_icon'] ?? 'fas fa-folder' }} w-5 text-center text-inverse-on-surface/40 group-hover:text-[#b9c3ff] transition-colors"></i>
                        <span class="flex-1 text-left text-[0.8125rem] font-medium">{{ $item['item_name'] }}</span>
                        <span class="material-symbols-outlined text-[18px] text-inverse-on-surface/30 transition-transform duration-200" :class="open ? 'rotate-90' : ''">chevron_right</span>
                    </button>
                    <div x-show="open" x-collapse class="mt-1 ml-4 space-y-0.5">
                        @foreach($item['details'] as $detail)
                            <a href="{{ asset($detail['url'] ?? '#') }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 no-underline
                                   {{ $detail['is_open']
                                       ? 'bg-gradient-to-r from-[#667eea] to-[#764ba2] text-white shadow-md shadow-indigo-500/20'
                                       : 'text-inverse-on-surface/60 hover:text-inverse-on-surface hover:bg-inverse-on-surface/5' }}">
                                <i class="{{ $detail['icon'] ?? 'far fa-circle' }} w-4 text-center text-[0.75rem]"></i>
                                <span class="text-[0.8125rem] font-medium">{{ $detail['name'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- 單項選單 --}}
                @foreach($item['details'] as $detail)
                    <a href="{{ asset($detail['url'] ?? '#') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 no-underline
                           {{ $detail['is_open']
                               ? 'bg-gradient-to-r from-[#667eea] to-[#764ba2] text-white shadow-md shadow-indigo-500/20'
                               : 'text-inverse-on-surface/70 hover:text-inverse-on-surface hover:bg-inverse-on-surface/5' }}">
                        <i class="{{ $detail['icon'] ?? 'far fa-circle' }} w-5 text-center text-[0.875rem]"></i>
                        <span class="text-[0.8125rem] font-medium">{{ $detail['name'] }}</span>
                    </a>
                @endforeach
            @endif
        @endforeach
    </nav>

    {{-- 登出 --}}
    <div class="px-3 py-4 border-t border-inverse-on-surface/10 mt-auto">
        <a href="{{ url('admin/logout') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-inverse-on-surface/50 hover:text-red-400 hover:bg-red-500/10 transition-all duration-200 no-underline">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span class="text-[0.8125rem] font-medium">登出</span>
        </a>
    </div>
</aside>

{{-- Alpine.js（側邊欄摺疊動畫） --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
