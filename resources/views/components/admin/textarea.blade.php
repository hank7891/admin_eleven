@props([
    'label' => null,
    'name',
    'value' => '',
    'rows' => 4,
    'required' => false,
    'placeholder' => '',
    'hint' => null,
    'error' => null,
    'id' => null,
])

@php
    $textareaId = $id ?? 'admin-textarea-' . $name;
    $hasError = !empty($error);
@endphp

<div class="admin-field {{ $hasError ? 'has-error' : '' }}">
    @if ($label)
        <label for="{{ $textareaId }}" class="admin-label">
            {{ $label }}
            @if ($required)<span class="admin-required" aria-hidden="true">*</span>@endif
        </label>
    @endif

    <textarea
        id="{{ $textareaId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($required) required aria-required="true" @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $textareaId }}-error" @endif
        {{ $attributes->merge(['class' => 'admin-textarea']) }}
    >{{ $value }}</textarea>

    @if ($hint && !$hasError)
        <p class="admin-help">{{ $hint }}</p>
    @endif
    @if ($hasError)
        <p id="{{ $textareaId }}-error" class="admin-help is-error" role="alert">{{ $error }}</p>
    @endif
</div>
