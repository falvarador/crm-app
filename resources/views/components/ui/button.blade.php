@props([
    'variant' => 'secondary',
    'size' => 'md',
    'type' => 'button',
    'icon' => null,
    'kbd' => null,
    'href' => null,
])

@php
    $classes = "crm-btn crm-btn--{$variant} crm-btn--{$size}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <span class="material-symbols-outlined crm-icon crm-icon--sm" aria-hidden="true">{{ $icon }}</span>
        @endif
        <span>{{ $slot }}</span>
        @if($kbd)
            <kbd class="crm-btn__kbd">{{ $kbd }}</kbd>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <span class="material-symbols-outlined crm-icon crm-icon--sm" aria-hidden="true">{{ $icon }}</span>
        @endif
        <span>{{ $slot }}</span>
        @if($kbd)
            <kbd class="crm-btn__kbd">{{ $kbd }}</kbd>
        @endif
    </button>
@endif
