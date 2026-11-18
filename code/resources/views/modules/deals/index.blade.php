<x-ui.layout title="Pipeline de Oportunidades" active="deals" breadcrumb="Pipeline de Oportunidades">
    <section class="crm-page-header">
        <div class="crm-page-header__title-group">
            <div class="crm-page-header__title-row">
                <h1 class="crm-page-header__title">Pipeline de Ventas</h1>
                <x-ui.badge variant="primary" :dot="false">Q2 • Q3</x-ui.badge>
            </div>
            <p class="crm-page-header__subtitle">
                Flujo comercial interactivo y seguimiento de oportunidades activas
            </p>
        </div>

        <div class="crm-page-header__controls">
            <div class="crm-segmented" role="tablist" aria-label="Cambiar vista del pipeline">
                <button type="button" role="tab" class="crm-segmented__btn is-active" aria-selected="true" data-pipeline-view="kanban">
                    <span class="material-symbols-outlined crm-icon crm-icon--sm" aria-hidden="true">view_kanban</span>
                    <span>Tablero Kanban</span>
                </button>
                <button type="button" role="tab" class="crm-segmented__btn" aria-selected="false" data-pipeline-view="list">
                    <span class="material-symbols-outlined crm-icon crm-icon--sm" aria-hidden="true">format_list_bulleted</span>
                    <span>Vista Lista</span>
                </button>
            </div>

            <x-ui.button variant="primary" icon="add" kbd="N" data-dialog-open="create-deal-dialog">
                Nueva Oportunidad
            </x-ui.button>
        </div>
    </section>

    <div class="crm-toolbar">
        <search>
            <form action="{{ route('deals.index') }}" method="GET" class="crm-toolbar__filters" data-ajax-filter>
                <label for="deals-owner-filter" class="sr-only">Propietario</label>
                <select id="deals-owner-filter" name="owner" class="crm-field__control crm-filter-select">
                    <option value="">Propietario: Todos</option>
                    <option value="Sofía Méndez" @selected($filters['owner'] === 'Sofía Méndez')>Sofía Méndez</option>
                    <option value="Carlos Vega" @selected($filters['owner'] === 'Carlos Vega')>Carlos Vega</option>
                    <option value="Diego Cruz" @selected($filters['owner'] === 'Diego Cruz')>Diego Cruz</option>
                </select>
                <x-ui.button type="submit" variant="secondary" size="sm" icon="filter_alt">Filtrar</x-ui.button>
                @if($filters['owner'] !== '' || $filters['stage'] !== '')
                    <x-ui.button href="{{ route('deals.index') }}" variant="ghost" size="sm" icon="restart_alt">Limpiar</x-ui.button>
                @endif
            </form>
        </search>

        <crm-html-ajax
            url="{{ route('deals.index', request()->query()) }}"
            fragment="deals-kpi"
            listen-events="deals:updated,kpi:updated"
        >
            @fragment('deals-kpi')
                <div class="crm-row">
                    <x-ui.badge variant="primary">
                        <span>Pipeline Ponderado:</span>
                        <strong class="tabular-nums">${{ number_format($weightedValue) }} USD</strong>
                        <span>/ {{ $totalDealsCount }} oportunidades</span>
                    </x-ui.badge>
                </div>
            @endfragment
        </crm-html-ajax>
    </div>

    <crm-html-ajax
        url="{{ route('deals.index', request()->query()) }}"
        fragment="deals-board"
        listen-events="deals:updated"
    >
        @fragment('deals-board')
            <div id="deals-kanban-panel">
                <crm-kanban class="crm-kanban" endpoint-template="/deals/{id}/stage" event-keys="deals:updated,kpi:updated">
                    <div class="crm-kanban__board">
                        @foreach($stages as $stageKey => $stageMeta)
                            @php
                                $stageDeals = $dealsByStage[$stageKey] ?? [];
                                $stageTotal = $stageTotals[$stageKey] ?? 0;
                                $colModifiers = [];
                                if ($stageKey === 'negociacion') {
                                    $colModifiers[] = 'crm-kanban__col--active';
                                } elseif ($stageKey === 'perdida') {
                                    $colModifiers[] = 'crm-kanban__col--muted';
                                }
                            @endphp

                            <section class="crm-kanban__col {{ implode(' ', $colModifiers) }}" data-stage="{{ $stageKey }}" aria-label="Etapa {{ $stageMeta['label'] }}">
                                <header class="crm-kanban__col-header">
                                    <div class="crm-kanban__col-title-group">
                                        <span class="crm-kanban__col-dot crm-kanban__col-dot--{{ $stageMeta['color'] }}" aria-hidden="true"></span>
                                        <h2 class="crm-kanban__col-title">{{ $stageMeta['label'] }}</h2>
                                        <x-ui.badge :variant="$stageMeta['color']" :dot="false">
                                            <span class="tabular-nums" data-stage-count>{{ count($stageDeals) }} deals</span>
                                        </x-ui.badge>
                                    </div>
                                    <button type="button" class="crm-btn crm-btn--ghost crm-btn--icon" data-dialog-open="create-deal-dialog" aria-label="Añadir oportunidad en {{ $stageMeta['label'] }}">
                                        <span class="material-symbols-outlined crm-icon crm-icon--sm" aria-hidden="true">add</span>
                                    </button>
                                </header>

                                <div class="crm-kanban__col-subtotal-row">
                                    <span class="crm-kanban__col-subtotal-label">Subtotal</span>
                                    <span class="crm-kanban__col-subtotal-val tabular-nums" data-stage-subtotal>${{ number_format($stageTotal) }} USD</span>
                                </div>

                                <div class="crm-kanban__cards" data-kanban-cards>
                                    @foreach($stageDeals as $deal)
                                        <article
                                            class="crm-deal-card"
                                            draggable="true"
                                            data-deal-id="{{ $deal['id'] }}"
                                            data-current-stage="{{ $deal['stage'] }}"
                                            data-amount="{{ $deal['amount'] }}"
                                        >
                                            @if(!empty($deal['badge']) && $deal['stage'] === 'propuesta')
                                                <div>
                                                    <x-ui.badge :variant="$deal['badge_variant'] ?? 'coral'" icon="bolt">{{ $deal['badge'] }}</x-ui.badge>
                                                </div>
                                            @endif

                                            <div class="crm-deal-card__top">
                                                <span class="crm-deal-card__title {{ $deal['stage'] === 'perdida' ? 'crm-deal-card__title--lost' : '' }}">
                                                    {{ $deal['title'] }}
                                                </span>
                                                <span class="material-symbols-outlined crm-icon crm-icon--sm crm-deal-card__drag-handle" aria-hidden="true">drag_indicator</span>
                                            </div>

                                            <div class="crm-deal-card__company">
                                                <span class="material-symbols-outlined crm-icon crm-icon--xs crm-icon--muted" aria-hidden="true">domain</span>
                                                <span class="truncate">{{ $deal['company_name'] }}</span>
                                            </div>

                                            @if(!empty($deal['alert']))
                                                <x-ui.badge variant="coral" icon="warning">{{ $deal['alert'] }}</x-ui.badge>
                                            @elseif(!empty($deal['lost_reason']))
                                                <x-ui.badge variant="neutral" icon="info">{{ $deal['lost_reason'] }}</x-ui.badge>
                                            @endif

                                            <div class="crm-deal-card__metrics">
                                                <span class="crm-deal-card__amount tabular-nums">${{ number_format($deal['amount']) }}</span>
                                                <x-ui.badge :variant="$stageMeta['color']">
                                                    <span class="tabular-nums">{{ $deal['probability'] }}% prob.</span>
                                                </x-ui.badge>
                                            </div>

                                            <footer class="crm-deal-card__footer">
                                                <div class="crm-row crm-row--nowrap">
                                                    <span class="material-symbols-outlined crm-icon crm-icon--xs" aria-hidden="true">
                                                        {{ $deal['stage'] === 'ganada' ? 'done_all' : ($deal['stage'] === 'perdida' ? 'event_busy' : 'event') }}
                                                    </span>
                                                    <span class="tabular-nums">{{ $deal['close_date'] }}</span>
                                                </div>

                                                <div class="crm-row crm-row--nowrap">
                                                    <label for="stage-select-{{ $deal['id'] }}" class="sr-only">Mover {{ $deal['title'] }} a etapa</label>
                                                    <select
                                                        id="stage-select-{{ $deal['id'] }}"
                                                        class="crm-deal-card__stage-select"
                                                        data-stage-select
                                                        title="Cambiar etapa"
                                                    >
                                                        @foreach($stages as $optKey => $optMeta)
                                                            <option value="{{ $optKey }}" @selected($deal['stage'] === $optKey)>{{ $optMeta['label'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="crm-avatar crm-avatar--sm" aria-hidden="true">{{ $deal['owner'] }}</span>
                                                </div>
                                            </footer>
                                        </article>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>
                </crm-kanban>
            </div>

            <div id="deals-list-panel" class="is-hidden">
                <x-ui.table :empty="count($deals) === 0" emptyTitle="Sin oportunidades" emptyDescription="No hay oportunidades para mostrar en este filtro.">
                    <x-slot:head>
                        <tr>
                            <th>Oportunidad</th>
                            <th>Empresa</th>
                            <th>Etapa</th>
                            <th class="is-numeric">Valor Estimado</th>
                            <th class="is-numeric">Probabilidad</th>
                            <th class="is-numeric">Fecha de Cierre</th>
                            <th>Propietario</th>
                        </tr>
                    </x-slot:head>

                    @foreach($deals as $deal)
                        @php
                            $stageInfo = $stages[$deal['stage']] ?? ['label' => ucfirst($deal['stage']), 'color' => 'neutral'];
                        @endphp
                        <tr>
                            <td><strong>{{ $deal['title'] }}</strong></td>
                            <td>{{ $deal['company_name'] }}</td>
                            <td><x-ui.badge :variant="$stageInfo['color']">{{ $stageInfo['label'] }}</x-ui.badge></td>
                            <td class="is-numeric"><strong>${{ number_format($deal['amount']) }} USD</strong></td>
                            <td class="is-numeric">{{ $deal['probability'] }}%</td>
                            <td class="is-numeric">{{ $deal['close_date'] }}</td>
                            <td>
                                <div class="crm-row crm-row--nowrap">
                                    <span class="crm-avatar crm-avatar--sm" aria-hidden="true">{{ $deal['owner'] }}</span>
                                    <span>{{ $deal['owner_name'] }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            </div>
        @endfragment
    </crm-html-ajax>

    <x-ui.dialog
        id="create-deal-dialog"
        title="Nueva Oportunidad en el Pipeline"
        subtitle="Crea un nuevo trato y asígnalo a una etapa del embudo comercial"
        icon="view_kanban"
        variant="drawer"
    >
        <crm-form-validate>
            <crm-form-ajax event-keys="deals:updated,kpi:updated" reset-on-success>
                <form action="{{ route('deals.store') }}" method="POST" class="crm-stack">
                    @csrf
                    <x-ui.form-field
                        name="title"
                        label="Título de la Oportunidad"
                        placeholder="Ej. Implementación Plataforma CRM"
                        required
                        autofocus
                    />
                    <x-ui.form-field
                        name="company_name"
                        label="Empresa Cliente"
                        type="select"
                        :options="$companyOptions"
                        required
                    />
                    <div class="crm-form-row-2">
                        <x-ui.form-field
                            name="amount"
                            label="Valor Estimado"
                            type="number"
                            prefix="USD $"
                            value="32000"
                            min="100"
                            required
                        />
                        <x-ui.form-field
                            name="stage"
                            label="Etapa del Embudo"
                            type="select"
                            value="prospecto"
                            :options="[
                                'prospecto' => 'Prospecto',
                                'propuesta' => 'Propuesta Enviada',
                                'negociacion' => 'Negociación',
                                'ganada' => 'Ganada',
                                'perdida' => 'Perdida',
                            ]"
                            required
                        />
                    </div>
                    <div class="crm-form-row-2">
                        <x-ui.form-field
                            name="close_date"
                            label="Fecha Estimada de Cierre"
                            value="30 Jun 2025"
                            required
                        />
                        <x-ui.form-field
                            name="owner_name"
                            label="Propietario"
                            type="select"
                            value="Sofía Méndez"
                            :options="[
                                'Sofía Méndez' => 'Sofía Méndez',
                                'Carlos Vega' => 'Carlos Vega',
                                'Diego Cruz' => 'Diego Cruz',
                            ]"
                            required
                        />
                    </div>
                    <div class="crm-row crm-row--end crm-form-actions">
                        <x-ui.button type="button" variant="secondary" data-dialog-close>Cancelar</x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="check">Crear Oportunidad</x-ui.button>
                    </div>
                </form>
            </crm-form-ajax>
        </crm-form-validate>
    </x-ui.dialog>
</x-ui.layout>
