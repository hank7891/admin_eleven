@props(['paginator', 'filters' => []])

@if ($paginator->hasPages())
    <div class="px-6 py-6 border-t border-outline-variant/10 bg-white flex flex-col sm:flex-row items-center justify-between gap-4">
        {{-- 左側：顯示筆數資訊 --}}
        <p class="text-[0.8125rem] text-outline font-medium">
            顯示 {{ $paginator->firstItem() }} 到 {{ $paginator->lastItem() }} 筆，共 {{ $paginator->total() }} 筆資料
        </p>

        {{-- 右側：分頁按鈕 --}}
        <div class="flex items-center gap-2">
            {{-- 上一頁 --}}
            @if ($paginator->onFirstPage())
                <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low text-outline-variant/40 cursor-not-allowed">
                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                </span>
            @else
                <a href="{{ $paginator->appends($filters)->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low text-outline hover:bg-surface-container-high transition-colors no-underline">
                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                </a>
            @endif

            {{-- 頁碼 --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();

                # 計算顯示的頁碼範圍
                $start = max(1, $currentPage - 2);
                $end = min($lastPage, $currentPage + 2);

                # 確保至少顯示 5 個頁碼
                if ($end - $start < 4) {
                    if ($start === 1) {
                        $end = min($lastPage, $start + 4);
                    } else {
                        $start = max(1, $end - 4);
                    }
                }
            @endphp

            {{-- 第一頁 + 省略號 --}}
            @if ($start > 1)
                <a href="{{ $paginator->appends($filters)->url(1) }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low text-on-surface font-semibold text-[0.875rem] hover:bg-surface-container-high transition-colors no-underline">1</a>
                @if ($start > 2)
                    <span class="text-outline-variant px-1">...</span>
                @endif
            @endif

            {{-- 中間頁碼 --}}
            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $currentPage)
                    <span class="w-10 h-10 flex items-center justify-center rounded-xl btn-primary text-white font-bold text-[0.875rem]">{{ $i }}</span>
                @else
                    <a href="{{ $paginator->appends($filters)->url($i) }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low text-on-surface font-semibold text-[0.875rem] hover:bg-surface-container-high transition-colors no-underline">{{ $i }}</a>
                @endif
            @endfor

            {{-- 省略號 + 最後一頁 --}}
            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <span class="text-outline-variant px-1">...</span>
                @endif
                <a href="{{ $paginator->appends($filters)->url($lastPage) }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low text-on-surface font-semibold text-[0.875rem] hover:bg-surface-container-high transition-colors no-underline">{{ $lastPage }}</a>
            @endif

            {{-- 下一頁 --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->appends($filters)->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low text-outline hover:bg-surface-container-high transition-colors no-underline">
                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                </a>
            @else
                <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low text-outline-variant/40 cursor-not-allowed">
                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                </span>
            @endif
        </div>
    </div>
@endif
