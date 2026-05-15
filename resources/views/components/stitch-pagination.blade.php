@props(['paginator', 'filters' => []])

@if ($paginator->hasPages())
    <nav class="admin-pagination" role="navigation" aria-label="分頁">
        <span class="admin-pagination-info">
            顯示 {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} 筆，共 {{ $paginator->total() }} 筆
        </span>

        <div class="admin-page-btns">
            {{-- 上一頁 --}}
            @if ($paginator->onFirstPage())
                <span class="admin-page-btn is-disabled" aria-disabled="true">
                    <span class="material-symbols-outlined" aria-hidden="true">chevron_left</span>
                </span>
            @else
                <a href="{{ $paginator->appends($filters)->previousPageUrl() }}" class="admin-page-btn" rel="prev" aria-label="上一頁">
                    <span class="material-symbols-outlined" aria-hidden="true">chevron_left</span>
                </a>
            @endif

            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $start = max(1, $currentPage - 2);
                $end = min($lastPage, $currentPage + 2);
                if ($end - $start < 4) {
                    if ($start === 1) {
                        $end = min($lastPage, $start + 4);
                    } else {
                        $start = max(1, $end - 4);
                    }
                }
            @endphp

            @if ($start > 1)
                <a href="{{ $paginator->appends($filters)->url(1) }}" class="admin-page-btn">1</a>
                @if ($start > 2)
                    <span class="admin-page-ellipsis" aria-hidden="true">...</span>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $currentPage)
                    <span class="admin-page-btn is-active" aria-current="page">{{ $i }}</span>
                @else
                    <a href="{{ $paginator->appends($filters)->url($i) }}" class="admin-page-btn" aria-label="第 {{ $i }} 頁">{{ $i }}</a>
                @endif
            @endfor

            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <span class="admin-page-ellipsis" aria-hidden="true">...</span>
                @endif
                <a href="{{ $paginator->appends($filters)->url($lastPage) }}" class="admin-page-btn">{{ $lastPage }}</a>
            @endif

            {{-- 下一頁 --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->appends($filters)->nextPageUrl() }}" class="admin-page-btn" rel="next" aria-label="下一頁">
                    <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                </a>
            @else
                <span class="admin-page-btn is-disabled" aria-disabled="true">
                    <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                </span>
            @endif
        </div>
    </nav>
@endif
