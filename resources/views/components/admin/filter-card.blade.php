@props([
    'action',
    'method' => 'GET',
    'title' => '篩選',
])

<form action="{{ $action }}" method="{{ $method }}" class="admin-card admin-card-pad admin-filter-card">
    @if (strtoupper($method) !== 'GET')
        @csrf
    @endif

    @if ($title)
        <div class="admin-section-head">
            <h3 class="admin-section-title">{{ $title }}</h3>
            @if (isset($head))<div>{{ $head }}</div>@endif
        </div>
    @endif

    <div class="admin-filter-grid">
        {{ $slot }}
    </div>

    @if (isset($actions))
        <div class="admin-filter-actions">
            {{ $actions }}
        </div>
    @else
        <div class="admin-filter-actions">
            <button type="submit" class="admin-btn admin-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">search</span>
                <span>搜尋</span>
            </button>
        </div>
    @endif
</form>
