<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-outline-variant/20 shadow-sm">
    <div class="flex items-center justify-between px-6 py-3">
        <div class="flex items-center gap-4">
            {{-- 此區域可放麵包屑，由各頁面自行處理 --}}
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ url('admin/logout') }}" class="flex items-center gap-1.5 px-3 py-1.5 text-[0.8125rem] text-outline hover:text-error hover:bg-error/5 rounded-lg transition-colors no-underline" title="登出">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                <span class="hidden sm:inline font-medium">登出</span>
            </a>
        </div>
    </div>
</header>
