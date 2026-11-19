@props([
    'empty' => false,
    'emptyTitle' => 'Sin registros disponibles',
    'emptyDescription' => 'No se encontraron resultados con los criterios seleccionados.',
    'emptyIcon' => 'inbox',
    'dense' => false,
])

<div {{ $attributes->merge(['class' => 'crm-table-wrapper']) }}>
    @if($empty)
        <div class="crm-table-empty">
            <div class="crm-table-empty__icon" aria-hidden="true">
                <span class="material-symbols-outlined">{{ $emptyIcon }}</span>
            </div>
            <h3 class="crm-table-empty__title">{{ $emptyTitle }}</h3>
            <p class="crm-table-empty__desc">{{ $emptyDescription }}</p>
            @isset($emptyAction)
                <div>{{ $emptyAction }}</div>
            @endisset
        </div>
    @else
        <div class="crm-table-wrapper__scroll">
            <table class="crm-table {{ $dense ? 'crm-table--dense' : '' }}">
                @isset($head)
                    <thead>
                        {{ $head }}
                    </thead>
                @endisset
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    @endif

    @isset($footer)
        <div class="crm-table-footer">
            {{ $footer }}
        </div>
    @endisset
</div>
