@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => [],
])

<div class="admin-page-head">
    <div class="admin-page-head-text">
        @if (!empty($breadcrumbs))
            <x-breadcrumb :items="$breadcrumbs" />
        @endif
        <h2 class="admin-h1">{{ $title }}</h2>
        @if ($subtitle)
            <p class="admin-page-sub">{{ $subtitle }}</p>
        @endif
    </div>
    @if (isset($actions))
        <div class="admin-page-head-actions">
            {{ $actions }}
        </div>
    @endif
</div>
