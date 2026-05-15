@props([
    'label' => null,
    'name',
    'options' => [],
    'value' => '',
    'required' => false,
    'placeholder' => null,
    'hint' => null,
    'error' => null,
    'id' => null,
])

@php
    $selectId = $id ?? 'admin-select-' . $name;
    $hasError = !empty($error);
@endphp

<div class="admin-field {{ $hasError ? 'has-error' : '' }}">
    @if ($label)
        <label for="{{ $selectId }}" class="admin-label">
            {{ $label }}
            @if ($required)<span class="admin-required" aria-hidden="true">*</span>@endif
        </label>
    @endif

    <select
        id="{{ $selectId }}"
        name="{{ $name }}"
        @if ($required) required aria-required="true" @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $selectId }}-error" @endif
        {{ $attributes->merge(['class' => 'admin-select']) }}
    >
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected((string) $optValue === (string) $value)>{{ $optLabel }}</option>
        @endforeach
    </select>

    @if ($hint && !$hasError)
        <p class="admin-help">{{ $hint }}</p>
    @endif
    @if ($hasError)
        <p id="{{ $selectId }}-error" class="admin-help is-error" role="alert">{{ $error }}</p>
    @endif
</div>
