@props([
    'label',
    'value',
    'unit' => null,
    'icon' => 'analytics',
    'iconVariant' => 'primary',
    'badgeText' => null,
    'badgeVariant' => 'emerald',
    'metaText' => null,
    'metaHref' => null,
])

<article {{ $attributes->merge(['class' => 'crm-stat-card']) }}>
    <div class="crm-stat-card__top">
        <div>
            <span class="crm-stat-card__label">{{ $label }}</span>
            <div class="crm-stat-card__value-row">
                <span class="crm-stat-card__value tabular-nums">{{ $value }}</span>
                @if($unit)
                    <span class="crm-stat-card__unit">{{ $unit }}</span>
                @endif
            </div>
        </div>
        <div class="crm-stat-card__icon {{ $iconVariant !== 'primary' ? "crm-stat-card__icon--{$iconVariant}" : '' }}" aria-hidden="true">
            <span class="material-symbols-outlined crm-icon crm-icon--md">{{ $icon }}</span>
        </div>
    </div>

    <div class="crm-stat-card__bottom">
        @if($badgeText)
            <x-ui.badge :variant="$badgeVariant">{{ $badgeText }}</x-ui.badge>
        @endif

        @if(isset($sparkline))
            {{ $sparkline }}
        @elseif($metaHref && $metaText)
            <a href="{{ $metaHref }}" class="crm-stat-card__link">{{ $metaText }}</a>
        @elseif($metaText)
            <span class="crm-stat-card__meta">{{ $metaText }}</span>
        @endif
    </div>
</article>
