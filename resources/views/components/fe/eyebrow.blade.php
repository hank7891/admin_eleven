@props(['as' => 'span'])

<{{ $as }} {{ $attributes->merge(['class' => 'fe-eyebrow']) }}>
    {{ $slot }}
</{{ $as }}>
