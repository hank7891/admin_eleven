@props([
    'icon' => 'inbox',
    'title' => '尚無資料',
    'description' => null,
])

<div class="admin-empty-state">
    <span class="material-symbols-outlined admin-empty-icon" aria-hidden="true">{{ $icon }}</span>
    <h3 class="admin-empty-title">{{ $title }}</h3>
    @if ($description)
        <p class="admin-empty-desc">{{ $description }}</p>
    @endif
    @if (isset($actions))
        <div class="admin-empty-actions">{{ $actions }}</div>
    @endif
</div>
