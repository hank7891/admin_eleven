@php
    $avatarExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $avatarPath = $user['avatar'] ?? '';
    $avatarExt = strtolower(pathinfo($avatarPath, PATHINFO_EXTENSION));
    $hasAvatar = !empty($avatarPath) && in_array($avatarExt, $avatarExtensions);
@endphp

<header class="sticky top-0 z-30 glass-header border-b border-outline-variant/20 flex justify-between items-center px-6 py-3 w-full">
    <div class="flex items-center gap-6">
        <h1 class="text-xl font-bold bg-gradient-to-r from-[#667eea] to-[#764ba2] bg-clip-text text-transparent font-headline">YoYoAdmin</h1>
        <div class="hidden sm:flex items-center bg-surface-container-low px-4 py-1.5 rounded-full">
            <span class="material-symbols-outlined text-[18px] text-outline mr-2">search</span>
            <input class="bg-transparent border-none focus:ring-0 text-[0.875rem] w-48 font-body placeholder:text-outline-variant" placeholder="搜尋..." type="text" disabled />
        </div>
    </div>
    <div class="flex items-center gap-4">
        {{-- 通知鈴鐺（裝飾用） --}}
        <button class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100 transition-colors text-slate-500" disabled>
            <span class="material-symbols-outlined">notifications</span>
        </button>
        <div class="h-8 w-[1px] bg-outline-variant/30 mx-2"></div>
        {{-- 使用者資訊 + 登出 --}}
        <div class="flex items-center gap-3">
            @if($hasAvatar)
                <img src="{{ asset('storage/' . $avatarPath) }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover border-2 border-white shadow-sm">
            @else
                <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center">
                    <span class="material-symbols-outlined text-outline text-[18px]">person</span>
                </div>
            @endif
            <span class="text-[0.8125rem] font-semibold text-on-surface hidden sm:inline">{{ $user['name'] ?? '' }}</span>
            <a href="{{ url('admin/logout') }}" class="flex items-center gap-1 px-2 py-1.5 text-[0.8125rem] text-outline hover:text-error hover:bg-error/5 rounded-lg transition-colors no-underline" title="登出">
                <span class="material-symbols-outlined text-[18px]">logout</span>
            </a>
        </div>
    </div>
</header>
