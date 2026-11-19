@props([
    'id',
    'title',
    'subtitle' => null,
    'icon' => null,
    'variant' => 'modal',
    'closedby' => 'any',
])

<crm-dialog id="{{ $id }}-wrapper">
    <dialog
        id="{{ $id }}"
        {{ $attributes->merge(['class' => "crm-dialog crm-dialog--{$variant}"]) }}
        aria-labelledby="{{ $id }}-title"
        closedby="{{ $closedby }}"
    >
        <header class="crm-dialog__header">
            <div class="crm-dialog__title-wrap">
                <div class="crm-dialog__title-row">
                    @if($icon)
                        <span class="material-symbols-outlined crm-icon crm-icon--lg crm-icon--primary" aria-hidden="true">{{ $icon }}</span>
                    @endif
                    <h2 id="{{ $id }}-title" class="crm-dialog__title">{{ $title }}</h2>
                </div>
                @if($subtitle)
                    <p class="crm-dialog__subtitle">{{ $subtitle }}</p>
                @endif
            </div>

            <button
                type="button"
                class="crm-btn crm-btn--ghost crm-btn--icon"
                data-dialog-close
                aria-label="Cerrar diálogo"
            >
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
        </header>

        <div class="crm-dialog__body">
            {{ $slot }}
        </div>

        @isset($footer)
            <footer class="crm-dialog__footer">
                {{ $footer }}
            </footer>
        @endisset
    </dialog>
</crm-dialog>
