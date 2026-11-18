@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'variant' => 'default',
    'padding' => 'lg',
])

@php
    $cardClasses = ['crm-card'];
    if ($variant === 'metric') {
        $cardClasses[] = 'crm-card--metric-layout';
    } elseif ($variant !== 'default') {
        $cardClasses[] = "crm-card--{$variant}";
    }
    if ($padding !== 'lg') {
        $cardClasses[] = "crm-card--padding-{$padding}";
    }
@endphp

<div class="crm-card-container">
    <section {{ $attributes->merge(['class' => implode(' ', $cardClasses)]) }}>
        @if($title || isset($headerActions))
            <header class="crm-card__header">
                <div class="crm-card__heading">
                    @if($icon)
                        <div class="crm-card__icon-box" aria-hidden="true">
                            <span class="material-symbols-outlined crm-icon crm-icon--md">{{ $icon }}</span>
                        </div>
                    @endif
                    <div>
                        @if($title)
                            <h2 class="crm-card__title">{{ $title }}</h2>
                        @endif
                        @if($subtitle)
                            <p class="crm-card__subtitle">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>

                @isset($headerActions)
                    <div class="crm-card__actions">
                        {{ $headerActions }}
                    </div>
                @endisset
            </header>
        @endif

        <div class="crm-card__body">
            {{ $slot }}
        </div>

        @isset($footer)
            <footer class="crm-card__footer">
                {{ $footer }}
            </footer>
        @endisset
    </section>
</div>
