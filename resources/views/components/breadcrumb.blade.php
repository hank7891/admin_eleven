@props(['items'])

<nav class="flex items-center gap-2 text-[0.75rem] text-outline-variant mb-1 uppercase tracking-widest font-semibold">
    @foreach ($items as $index => $item)
        @if ($index > 0)
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        @endif

        @if (isset($item['url']) && $index < count($items) - 1)
            <a href="{{ asset($item['url']) }}" class="hover:text-primary transition-colors no-underline text-outline-variant">{{ $item['label'] }}</a>
        @elseif ($index === count($items) - 1)
            <span class="text-primary">{{ $item['label'] }}</span>
        @else
            <span>{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
