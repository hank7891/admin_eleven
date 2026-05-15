@props([
    'title' => null,
    'pad' => true,
])

<div {{ $attributes->merge(['class' => 'admin-card' . ($pad ? ' admin-card-pad' : '')]) }}>
    @if ($title || isset($head))
        <div class="admin-section-head">
            @if ($title)<h3 class="admin-section-title">{{ $title }}</h3>@endif
            {{ $head ?? '' }}
        </div>
    @endif
    {{ $slot }}
</div>
