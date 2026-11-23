<x-ui.layout title="Dashboard Ejecutivo" active="dashboard" breadcrumb="Dashboard Ejecutivo">
    <section class="crm-page-header">
        <div class="crm-page-header__title-group">
            <div class="crm-page-header__eyebrow">
                <x-ui.badge variant="emerald">Operaciones en Vivo</x-ui.badge>
            </div>
            <div class="crm-page-header__title-row">
                <h1 class="crm-page-header__title">Dashboard Ejecutivo</h1>
            </div>
            <p class="crm-page-header__subtitle">
                Resumen de rendimiento comercial y seguimiento en tiempo real
            </p>
        </div>

        <div class="crm-page-header__controls">
            <div class="crm-segmented" role="group" aria-label="Periodo de análisis">
                <button
                    type="button"
                    class="crm-segmented__btn {{ $period === '30d' ? 'is-active' : '' }}"
                    aria-pressed="{{ $period === '30d' ? 'true' : 'false' }}"
                    data-fragment-nav="{{ route('dashboard', ['period' => '30d']) }}"
                    data-target-fragments="dashboard-kpis,dashboard-closing-deals"
                >
                    Últimos 30 días
                </button>
                <button
                    type="button"
                    class="crm-segmented__btn {{ $period === 'month' ? 'is-active' : '' }}"
                    aria-pressed="{{ $period === 'month' ? 'true' : 'false' }}"
                    data-fragment-nav="{{ route('dashboard', ['period' => 'month']) }}"
                    data-target-fragments="dashboard-kpis,dashboard-closing-deals"
                >
                    Este mes
                </button>
                <button
                    type="button"
                    class="crm-segmented__btn {{ $period === 'quarter' ? 'is-active' : '' }}"
                    aria-pressed="{{ $period === 'quarter' ? 'true' : 'false' }}"
                    data-fragment-nav="{{ route('dashboard', ['period' => 'quarter']) }}"
                    data-target-fragments="dashboard-kpis,dashboard-closing-deals"
                >
                    Trimestre actual
                </button>
            </div>

            <x-ui.button variant="secondary" icon="download" href="{{ route('deals.index') }}">
                Ver Pipeline
            </x-ui.button>
            <x-ui.button variant="primary" icon="add_circle" data-dialog-open="dashboard-deal-dialog">
                Nueva Oportunidad
            </x-ui.button>
        </div>
    </section>

    <crm-html-ajax
        url="{{ route('dashboard', ['period' => $period]) }}"
        fragment="dashboard-kpis"
        listen-events="kpi:updated,deals:updated,contacts:updated,activities:updated"
    >
        @fragment('dashboard-kpis')
            <section class="crm-grid-kpis" aria-label="Indicadores clave de rendimiento">
                <x-ui.stat-card
                    label="Pipeline abierto creado en el período"
                    :value="'$' . number_format($openValue, 2)"
                    unit="USD"
                    icon="account_balance_wallet"
                />

                <x-ui.stat-card
                    label="Conversión de oportunidades del período"
                    :value="$conversionRate . '%'"
                    unit="promedio"
                    icon="trending_up"
                    iconVariant="tertiary"
                />

                <x-ui.stat-card
                    label="Contactos creados en el período"
                    :value="(string) $leadsCount"
                    unit="leads"
                    icon="group_add"
                />

                <x-ui.stat-card
                    label="Tareas Pendientes Hoy"
                    :value="(string) $pendingTasksCount"
                    unit="agendadas"
                    icon="notification_important"
                    iconVariant="error"
                    metaText="Resolver"
                    metaHref="#tareas-hoy"
                />
            </section>
        @endfragment
    </crm-html-ajax>

    <div class="crm-split-layout crm-split-layout--8-4">
        <div id="tareas-hoy">
            <crm-html-ajax
                url="{{ route('dashboard') }}"
                fragment="dashboard-tasks"
                listen-events="activities:updated"
            >
                @fragment('dashboard-tasks')
                    <x-ui.card
                        title="Tareas Pendientes para Hoy"
                        subtitle="Prioridades comerciales del equipo de ventas"
                        icon="check_circle"
                    >
                        <x-slot:headerActions>
                            <div class="crm-col crm-col--end">
                                <span class="tabular-nums crm-label-strong">
                                    {{ $completedTasksCount }} de {{ $totalTasksCount }} completadas
                                </span>
                                @php
                                    $progressPct = $totalTasksCount > 0 ? (int) round(($completedTasksCount / $totalTasksCount) * 100) : 0;
                                @endphp
                                <div class="crm-progress crm-progress--md" role="progressbar" aria-valuenow="{{ $progressPct }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="crm-progress__bar" style="inline-size: {{ $progressPct }}%;"></div>
                                </div>
                            </div>
                            <x-ui.button variant="ghost" size="sm" icon="add" data-dialog-open="quick-action-dialog" aria-label="Nueva tarea">
                                Agregar
                            </x-ui.button>
                        </x-slot:headerActions>

                        <div class="crm-task-list">
                            @forelse($tasks as $task)
                                <div class="crm-task-item {{ $task['completed'] ? 'crm-task-item--done' : '' }}">
                                    <div class="crm-task-item__main">
                                        <crm-form-ajax event-keys="activities:updated,kpi:updated">
                                            <form action="{{ route('activities.toggle', $task['id']) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="crm-btn crm-btn--ghost crm-btn--icon"
                                                    aria-label="{{ $task['completed'] ? 'Marcar como pendiente' : 'Marcar como completada' }}"
                                                >
                                                    <span class="material-symbols-outlined crm-icon {{ $task['completed'] ? 'crm-icon--tertiary' : 'crm-icon--muted' }}" aria-hidden="true">
                                                        {{ $task['completed'] ? 'check_circle' : 'radio_button_unchecked' }}
                                                    </span>
                                                </button>
                                            </form>
                                        </crm-form-ajax>

                                        <div class="crm-task-item__content">
                                            <div class="crm-task-item__title-row">
                                                <span class="crm-task-item__title">{{ $task['subject'] }}</span>
                                                <x-ui.badge :variant="$task['completed'] ? 'neutral' : ($task['priority'] === 'Alta Prioridad' ? 'coral' : 'sky')" :dot="false">
                                                    {{ $task['schedule_label'] }}
                                                </x-ui.badge>
                                                <x-ui.badge :variant="$task['priority'] === 'Alta Prioridad' ? 'coral' : 'neutral'" :dot="false">
                                                    {{ $task['priority'] }}
                                                </x-ui.badge>
                                            </div>
                                            <div class="crm-task-item__meta">
                                                <span class="crm-row crm-row--nowrap">
                                                    <span class="material-symbols-outlined crm-icon crm-icon--xs crm-icon--muted" aria-hidden="true">domain</span>
                                                    <span>{{ $task['company_name'] }}</span>
                                                </span>
                                                <span aria-hidden="true">·</span>
                                                <span class="crm-row crm-row--nowrap">
                                                    <span class="material-symbols-outlined crm-icon crm-icon--xs crm-icon--muted" aria-hidden="true">person</span>
                                                    <span>{{ $task['contact_name'] }}</span>
                                                </span>
                                                @if(!empty($task['amount_label']))
                                                    <span aria-hidden="true">·</span>
                                                    <strong class="tabular-nums crm-icon--tertiary">{{ $task['amount_label'] }}</strong>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if(!$task['completed'])
                                        <crm-form-ajax event-keys="activities:updated,kpi:updated">
                                            <form action="{{ route('activities.toggle', $task['id']) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.button type="submit" variant="secondary" size="sm">Completar</x-ui.button>
                                            </form>
                                        </crm-form-ajax>
                                    @else
                                        <span class="material-symbols-outlined crm-icon crm-icon--tertiary" aria-label="Completada">done_all</span>
                                    @endif
                                </div>
                            @empty
                                <p class="crm-card__subtitle">No hay tareas programadas para hoy.</p>
                                <x-ui.button variant="secondary" icon="add" data-dialog-open="quick-action-dialog">Agregar actividad</x-ui.button>
                            @endforelse
                        </div>
                    </x-ui.card>
                @endfragment
            </crm-html-ajax>
        </div>

        <div>
            <crm-html-ajax
                url="{{ route('dashboard') }}"
                fragment="dashboard-feed"
                listen-events="activities:updated,deals:updated"
            >
                @fragment('dashboard-feed')
                    <x-ui.card title="Flujo de Actividad" icon="history">
                        <x-slot:headerActions>
                            <a href="{{ route('activities.index') }}" class="crm-stat-card__link">Ver todas</a>
                        </x-slot:headerActions>

                        <div class="crm-timeline">
                            @forelse($recentFeed as $item)
                                <div class="crm-timeline__item">
                                    <div class="crm-timeline__node crm-timeline__node--{{ $item['tone'] }}" aria-hidden="true">
                                        <span class="crm-timeline__node-dot"></span>
                                    </div>
                                    <div class="crm-timeline__body">
                                        <div class="crm-timeline__header">
                                            <span class="crm-timeline__actor">{{ $item['actor'] }}</span>
                                            <span class="crm-timeline__time">{{ $item['time'] }}</span>
                                        </div>
                                        <p class="crm-card__subtitle">{{ $item['summary'] }}</p>
                                        @if($item['meta'])
                                            <x-ui.badge variant="neutral" :dot="false">{{ $item['meta'] }}</x-ui.badge>
                                        @endif
                                        @if($item['quote'])
                                            <blockquote class="crm-timeline__quote">{{ $item['quote'] }}</blockquote>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="crm-card__subtitle">Todavía no hay actividad registrada.</p>
                            @endforelse
                        </div>
                    </x-ui.card>
                @endfragment
            </crm-html-ajax>
        </div>
    </div>

    <crm-html-ajax
        url="{{ route('dashboard', ['period' => $period]) }}"
        fragment="dashboard-closing-deals"
        listen-events="deals:updated,kpi:updated"
    >
        @fragment('dashboard-closing-deals')
            <x-ui.card
                title="Oportunidades Próximas a Cerrar"
                :subtitle="count($closingDeals) . ' tratos clave proyectados con cierre en curso'"
                icon="verified"
            >
                <x-slot:headerActions>
                    <span class="crm-card__subtitle">Total proyectado:</span>
                    <span class="crm-metric-inline">${{ number_format($closingTotal, 2) }} USD</span>
                </x-slot:headerActions>

                <x-ui.table :empty="count($closingDeals) === 0" emptyTitle="Sin tratos próximos a cerrar" emptyDescription="Agrega nuevas oportunidades para proyectar cierres.">
                    <x-slot:head>
                        <tr>
                            <th>Oportunidad</th>
                            <th>Empresa</th>
                            <th class="is-numeric">Valor</th>
                            <th>Probabilidad</th>
                            <th>Etapa</th>
                            <th class="is-numeric">Fecha Estimada</th>
                            <th>Responsable</th>
                        </tr>
                    </x-slot:head>

                    @foreach($closingDeals as $deal)
                        @php
                            $stageInfo = $stages[$deal['stage']] ?? ['label' => ucfirst($deal['stage']), 'color' => 'neutral'];
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('deals.index') }}" class="crm-table-cell-link">
                                    <span>{{ $deal['title'] }}</span>
                                    <span class="material-symbols-outlined crm-icon crm-icon--xs crm-icon--muted" aria-hidden="true">arrow_forward</span>
                                </a>
                            </td>
                            <td>
                                <span class="crm-account-pill">
                                    <span class="crm-avatar crm-avatar--sm crm-avatar--secondary" aria-hidden="true">{{ mb_substr($deal['company_name'], 0, 1) }}</span>
                                    <span>{{ $deal['company_name'] }}</span>
                                </span>
                            </td>
                            <td class="is-numeric"><strong>${{ number_format($deal['amount'], 2) }}</strong></td>
                            <td>
                                <div class="crm-row crm-row--nowrap">
                                    <div class="crm-progress crm-progress--sm">
                                        <div class="crm-progress__bar crm-progress__bar--emerald" style="inline-size: {{ $deal['probability'] }}%;"></div>
                                    </div>
                                    <span class="tabular-nums">{{ $deal['probability'] }}%</span>
                                </div>
                            </td>
                            <td>
                                <x-ui.badge :variant="$stageInfo['color']">{{ $stageInfo['label'] }}</x-ui.badge>
                            </td>
                            <td class="is-numeric">{{ $deal['close_date'] }}</td>
                            <td>
                                <div class="crm-row crm-row--nowrap">
                                    <span class="crm-avatar crm-avatar--sm" aria-hidden="true">{{ $deal['owner'] }}</span>
                                    <span>{{ $deal['owner_name'] }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    <x-slot:footer>
                        <span>Mostrando {{ count($closingDeals) }} oportunidades clave</span>
                        <a href="{{ route('deals.index') }}" class="crm-stat-card__link">Abrir tablero Kanban completo →</a>
                    </x-slot:footer>
                </x-ui.table>
            </x-ui.card>
        @endfragment
    </crm-html-ajax>

    <x-ui.dialog
        id="dashboard-deal-dialog"
        title="Nueva Oportunidad Comercial"
        subtitle="Añade un trato al pipeline de ventas desde el resumen ejecutivo"
        icon="add_circle"
        variant="drawer"
    >
        <crm-form-validate>
            <crm-form-ajax event-keys="deals:updated,kpi:updated" reset-on-success>
                <form action="{{ route('deals.store') }}" method="POST" class="crm-stack">
                    @csrf
                    <x-ui.form-field
                        name="title"
                        label="Nombre de la Oportunidad"
                        placeholder="Ej. Expansión Cloud Enterprise"
                        required
                        autofocus
                    />
                    <x-ui.form-field
                        name="company_name"
                        label="Empresa Asociada"
                        type="select"
                        placeholder="Selecciona una empresa"
                        :options="$crmCompanies"
                        required
                    />
                    <div class="crm-form-row-2">
                        <x-ui.form-field
                            name="amount"
                            label="Valor Estimado"
                            type="number"
                            prefix="USD $"
                            min="100"
                            step="0.01"
                            required
                        />
                        <x-ui.form-field
                            name="stage"
                            label="Etapa Inicial"
                            type="select"
                            value="propuesta"
                            :options="[
                                'prospecto' => 'Prospecto',
                                'propuesta' => 'Propuesta Enviada',
                                'negociacion' => 'Negociación',
                            ]"
                            required
                        />
                    </div>
                    <div class="crm-form-row-2">
                        <x-ui.form-field
                            name="close_date"
                            label="Fecha Estimada de Cierre"
                            type="date"
                            required
                        />
                        <x-ui.form-field
                            name="owner_name"
                            label="Ejecutivo Responsable"
                            type="select"
                            :value="auth()->id()"
                            :options="$crmUsers"
                            required
                        />
                    </div>
                    <div class="crm-row crm-row--end crm-form-actions">
                        <x-ui.button type="button" variant="secondary" data-dialog-close>Cancelar</x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="check">Guardar Oportunidad</x-ui.button>
                    </div>
                </form>
            </crm-form-ajax>
        </crm-form-validate>
    </x-ui.dialog>
</x-ui.layout>
