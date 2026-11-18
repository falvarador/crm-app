@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'hint' => null,
    'icon' => null,
    'prefix' => null,
    'validateMsg' => null,
    'options' => [],
    'rows' => 3,
    'id' => null,
])

@php
    $fieldId = $id ?? 'field-' . str_replace(['.', '[', ']'], '-', $name) . '-' . substr(md5($name . $label), 0, 6);
    $errorId = "{$fieldId}-error";
    $hintId = $hint ? "{$fieldId}-hint" : null;

    $controlClasses = ['crm-field__control'];
    if ($icon) {
        $controlClasses[] = 'crm-field__control--with-icon';
    }
    if ($prefix) {
        $controlClasses[] = 'crm-field__control--with-prefix';
    }
    if ($type === 'textarea') {
        $controlClasses[] = 'crm-field__control--textarea';
    }
@endphp

<div class="crm-field">
    <div class="crm-field__label-row">
        <label for="{{ $fieldId }}" class="crm-field__label">
            {{ $label }}
            @if($required)
                <span aria-hidden="true">*</span>
            @endif
        </label>
        @if($required)
            <span class="crm-field__req">Requerido</span>
        @endif
    </div>

    @if($hint)
        <span id="{{ $hintId }}" class="crm-field__hint">{{ $hint }}</span>
    @endif

    <div class="crm-field__control-wrap">
        @if($icon)
            <span class="material-symbols-outlined crm-field__icon" aria-hidden="true">{{ $icon }}</span>
        @elseif($prefix)
            <span class="crm-field__prefix" aria-hidden="true">{{ $prefix }}</span>
        @endif

        @if($type === 'select')
            <select
                id="{{ $fieldId }}"
                name="{{ $name }}"
                data-error="{{ $errorId }}"
                @if($hintId) aria-describedby="{{ $hintId }}" @endif
                @if($required) required @endif
                @if($validateMsg) validate-msg="{{ $validateMsg }}" @endif
                {{ $attributes->merge(['class' => implode(' ', $controlClasses)]) }}
            >
                @if($placeholder)
                    <option value="">{{ $placeholder }}</option>
                @endif
                @foreach($options as $optValue => $optLabel)
                    <option value="{{ $optValue }}" @selected((string) $value === (string) $optValue)>{{ $optLabel }}</option>
                @endforeach
            </select>
        @elseif($type === 'textarea')
            <textarea
                id="{{ $fieldId }}"
                name="{{ $name }}"
                rows="{{ $rows }}"
                placeholder="{{ $placeholder }}"
                data-error="{{ $errorId }}"
                @if($hintId) aria-describedby="{{ $hintId }}" @endif
                @if($required) required @endif
                @if($validateMsg) validate-msg="{{ $validateMsg }}" @endif
                {{ $attributes->merge(['class' => implode(' ', $controlClasses)]) }}
            >{{ $value }}</textarea>
        @else
            <input
                id="{{ $fieldId }}"
                type="{{ $type }}"
                name="{{ $name }}"
                value="{{ $value }}"
                placeholder="{{ $placeholder }}"
                data-error="{{ $errorId }}"
                @if($hintId) aria-describedby="{{ $hintId }}" @endif
                @if($required) required @endif
                @if($validateMsg) validate-msg="{{ $validateMsg }}" @endif
                {{ $attributes->merge(['class' => implode(' ', $controlClasses)]) }}
            >
        @endif
    </div>

    <div id="{{ $errorId }}" class="crm-field__error" data-error-for="{{ $name }}"></div>
</div>
