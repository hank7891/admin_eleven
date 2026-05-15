@props([
    'name',
    'value' => '1',
    'checked' => false,
    'label' => null,
    'id' => null,
])

@php
    $checkboxId = $id ?? 'admin-checkbox-' . $name . '-' . $value;
@endphp

<label for="{{ $checkboxId }}" class="admin-checkbox-row">
    <input
        type="checkbox"
        id="{{ $checkboxId }}"
        name="{{ $name }}"
        value="{{ $value }}"
        @checked($checked)
        {{ $attributes->merge(['class' => 'admin-checkbox']) }}
    >
    @if ($label || $slot->isNotEmpty())
        <span class="admin-checkbox-label">{{ $label ?? $slot }}</span>
    @endif
</label>
