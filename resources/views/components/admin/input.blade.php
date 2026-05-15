@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'placeholder' => '',
    'hint' => null,
    'error' => null,
    'id' => null,
    'icon' => null,
])

@php
    $inputId = $id ?? 'admin-input-' . $name;
    $hasError = !empty($error);
@endphp

<div class="admin-field {{ $hasError ? 'has-error' : '' }}">
    @if ($label)
        <label for="{{ $inputId }}" class="admin-label">
            {{ $label }}
            @if ($required)<span class="admin-required" aria-hidden="true">*</span>@endif
        </label>
    @endif

    @if ($icon)
        <div class="admin-input-wrap">
            <span class="material-symbols-outlined admin-input-leading-icon" aria-hidden="true">{{ $icon }}</span>
            <input
                type="{{ $type }}"
                id="{{ $inputId }}"
                name="{{ $name }}"
                value="{{ $value }}"
                @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                @if ($required) required aria-required="true" @endif
                @if ($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
                {{ $attributes->merge(['class' => 'admin-input admin-input-with-icon']) }}
            >
        </div>
    @else
        <input
            type="{{ $type }}"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ $value }}"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required aria-required="true" @endif
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
            {{ $attributes->merge(['class' => 'admin-input']) }}
        >
    @endif

    @if ($hint && !$hasError)
        <p class="admin-help">{{ $hint }}</p>
    @endif
    @if ($hasError)
        <p id="{{ $inputId }}-error" class="admin-help is-error" role="alert">{{ $error }}</p>
    @endif
</div>
