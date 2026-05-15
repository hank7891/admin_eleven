@props([
    'eyebrow' => null,
    'title' => null,
    'lead' => null,
    'align' => 'left',
])

<header class="fe-section-head fe-section-head-{{ $align }}">
    @if ($eyebrow)
        <span class="fe-eyebrow">{{ $eyebrow }}</span>
    @endif
    @if ($title)
        <h2 class="fe-h2">{{ $title }}</h2>
    @endif
    @if ($lead)
        <p class="fe-body-lg">{{ $lead }}</p>
    @endif
    {{ $slot }}
</header>
