@props([
    'variant' => 'neutral',
    'dot' => true,
    'icon' => null,
])

<span {{ $attributes->merge(['class' => "crm-badge crm-badge--{$variant}"]) }}>
    @if($icon)
        <span class="material-symbols-outlined crm-icon crm-icon--xs" aria-hidden="true">{{ $icon }}</span>
    @elseif($dot)
        <span class="crm-badge__dot" aria-hidden="true"></span>
    @endif
    <span>{{ $slot }}</span>
</span>
