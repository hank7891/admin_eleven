@props(['as' => 'article'])

<{{ $as }} {{ $attributes->merge(['class' => 'fe-journal-card']) }}>
    {{ $slot }}
</{{ $as }}>
