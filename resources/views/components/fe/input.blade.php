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
])

@php
    $inputId = $id ?? 'fe-input-' . $name;
    $hasError = !empty($error);
@endphp

<div class="fe-form-field">
    @if ($label)
        <label for="{{ $inputId }}" class="fe-form-label">
            {{ $label }}
            @if ($required)<span class="fe-form-required" aria-hidden="true">*</span>@endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $inputId }}"
        name="{{ $name }}"
        value="{{ $value }}"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($required) required aria-required="true" @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
        {{ $attributes->merge(['class' => 'fe-input' . ($hasError ? ' has-error' : '')]) }}
    >

    @if ($hint && !$hasError)
        <p class="fe-form-hint">{{ $hint }}</p>
    @endif
    @if ($hasError)
        <p id="{{ $inputId }}-error" class="fe-form-error" role="alert">{{ $error }}</p>
    @endif
</div>
