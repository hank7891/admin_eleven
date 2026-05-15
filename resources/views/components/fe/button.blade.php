@props([
    'variant' => 'primary',
    'as' => 'button',
    'href' => null,
    'icon' => null,
    'iconLeft' => null,
    'type' => 'button',
])

@php
    $tag = $as === 'a' ? 'a' : 'button';
    $classes = 'fe-btn fe-btn-' . $variant;
@endphp

@if ($tag === 'a')
    <a {{ $attributes->merge(['class' => $classes, 'href' => $href ?? '#']) }}>
        @if ($iconLeft)
            <span class="material-symbols-outlined" aria-hidden="true">{{ $iconLeft }}</span>
        @endif
        {{ $slot }}
        @if ($icon)
            <span class="material-symbols-outlined" aria-hidden="true">{{ $icon }}</span>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($iconLeft)
            <span class="material-symbols-outlined" aria-hidden="true">{{ $iconLeft }}</span>
        @endif
        {{ $slot }}
        @if ($icon)
            <span class="material-symbols-outlined" aria-hidden="true">{{ $icon }}</span>
        @endif
    </button>
@endif
