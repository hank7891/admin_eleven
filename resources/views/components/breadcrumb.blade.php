@props(['items'])

<nav class="admin-crumbs" aria-label="麵包屑">
    @foreach ($items as $index => $item)
        @if ($index > 0)
            <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
        @endif

        @if (isset($item['url']) && $index < count($items) - 1)
            <a href="{{ asset($item['url']) }}">{{ $item['label'] }}</a>
        @else
            <span @if ($index === count($items) - 1) aria-current="page" @endif>{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
