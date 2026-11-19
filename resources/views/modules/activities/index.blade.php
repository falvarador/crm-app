<x-ui.layout title="Actividades y Seguimiento" active="activities" breadcrumb="Actividades y Tareas">
    <section class="crm-page-header">
        <div class="crm-page-header__title-group">
            <div class="crm-page-header__eyebrow">
                <span>Gestión de Pipeline</span>
                <span aria-hidden="true">•</span>
                <span class="crm-icon--primary">Tiempo Real</span>
            </div>
            <h1 class="crm-page-header__title">Actividades y Tareas Comerciales</h1>
            <p class="crm-page-header__subtitle">
                Registro diario de interacciones, compromisos y llamadas con prospectos clave para acelerar el ciclo de ventas.
            </p>
        </div>

        <crm-html-ajax
            url="{{ route('activities.index', request()->query()) }}"
            fragment="activities-kpi"
            listen-events="activities:updated,kpi:updated"
        >
            @fragment('activities-kpi')
                <div class="crm-row">
                    <x-ui.badge variant="primary">
                        <strong class="tabular-nums">{{ $totalPending }} Pendientes</strong>
                    </x-ui.badge>
                    <x-ui.badge variant="coral">
                        <strong class="tabular-nums">{{ $overdueCount }} Prioritarias</strong>
                    </x-ui.badge>
                    <x-ui.badge variant="emerald" icon="check_circle">
                        <strong class="tabular-nums">{{ $totalCompleted }} Hechas</strong>
                    </x-ui.badge>
                </div>
            @endfragment
        </crm-html-ajax>
    </section>

    <div class="crm-split-layout crm-split-layout--8-4">
        <x-ui.card title="Registrar Nueva Actividad" icon="add_task" variant="accent">
            <x-slot:headerActions>
                <div class="crm-segmented" data-activity-type-group role="group" aria-label="Tipo de actividad">
                    <button type="button" class="crm-segmented__btn is-active" data-activity-type-pill="llamada" aria-pressed="true">
                        <span class="material-symbols-outlined crm-icon crm-icon--xs" aria-hidden="true">call</span>
                        <span>Llamada</span>
                    </button>
                    <button type="button" class="crm-segmented__btn" data-activity-type-pill="correo" aria-pressed="false">
                        <span class="material-symbols-outlined crm-icon crm-icon--xs" aria-hidden="true">mail</span>
                        <span>Correo</span>
                    </button>
                    <button type="button" class="crm-segmented__btn" data-activity-type-pill="reunion" aria-pressed="false">
                        <span class="material-symbols-outlined crm-icon crm-icon--xs" aria-hidden="true">videocam</span>
                        <span>Reunión</span>
                    </button>
                    <button type="button" class="crm-segmented__btn" data-activity-type-pill="tarea" aria-pressed="false">
                        <span class="material-symbols-outlined crm-icon crm-icon--xs" aria-hidden="true">check_box</span>
                        <span>Tarea</span>
                    </button>
                    <button type="button" class="crm-segmented__btn" data-activity-type-pill="nota" aria-pressed="false">
                        <span class="material-symbols-outlined crm-icon crm-icon--xs" aria-hidden="true">edit_note</span>
                        <span>Nota</span>
                    </button>
                </div>
            </x-slot:headerActions>

            <crm-form-validate>
                <crm-form-ajax event-keys="activities:updated,kpi:updated" reset-on-success>
                    <form action="{{ route('activities.store') }}" method="POST" class="crm-stack">
                        @csrf
                        <input type="hidden" name="type" value="llamada" data-activity-type-input>

                        <x-ui.form-field
                            name="subject"
                            label="Asunto de la interacción"
                            placeholder="Ej. Llamada de seguimiento post-demo comercial"
                            required
                        />

                        <div class="crm-form-row-2">
                            <x-ui.form-field
                                name="company_name"
                                label="Empresa vinculada"
                                type="select"
                                :options="collect($companies)->pluck('name', 'name')->toArray()"
                                required
                            />
                            <x-ui.form-field
                                name="contact_name"
                                label="Contacto / Interlocutor"
                                placeholder="Elena Sanz"
                                value="Elena Sanz"
                            />
                        </div>

                        <div class="crm-form-row-2">
                            <x-ui.form-field
                                name="schedule_label"
                                label="Fecha y hora programada"
                                icon="schedule"
                                value="Hoy, 16:30"
                                required
                            />
                            <x-ui.form-field
                                name="owner"
                                label="Responsable del seguimiento"
                                type="select"
                                value="Sofía Méndez"
                                :options="[
                                    'Sofía Méndez' => 'Sofía Méndez (Yo)',
                                    'Carlos Vega' => 'Carlos Vega',
                                    'Diego Cruz' => 'Diego Cruz',
                                ]"
                            />
                        </div>

                        <x-ui.form-field
                            name="notes"
                            label="Notas clave y orden del día"
                            type="textarea"
                            rows="2"
                            placeholder="Añade notas contextuales previas a la reunión o el resultado..."
                        />

                        <div class="crm-row crm-row--between">
                            <label class="crm-row crm-row--nowrap crm-pointer">
                                <input type="checkbox" name="completed" value="1">
                                <span class="crm-card__subtitle">Marcar como completada inmediatamente</span>
                            </label>
                            <div class="crm-row">
                                <x-ui.button type="reset" variant="ghost">Descartar</x-ui.button>
                                <x-ui.button type="submit" variant="primary" icon="save">Guardar Actividad</x-ui.button>
                            </div>
                        </div>
                    </form>
                </crm-form-ajax>
            </crm-form-validate>
        </x-ui.card>

        <div class="crm-stack">
            <x-ui.card>
                <div class="crm-row crm-row--between">
                    <span class="crm-meta-grid__label">Prospecto en foco</span>
                    <x-ui.badge variant="sky">En Propuesta</x-ui.badge>
                </div>

                <div class="crm-row crm-row--nowrap">
                    <span class="crm-avatar crm-avatar--lg" aria-hidden="true">ES</span>
                    <div class="crm-col">
                        <strong>Elena Sanz</strong>
                        <span class="crm-card__subtitle">COO en NovaTech Solutions S.A.</span>
                    </div>
                </div>

                <div class="crm-meta-grid">
                    <div class="crm-meta-grid__item">
                        <span class="crm-meta-grid__label">Valor potencial</span>
                        <strong class="crm-metric-inline">$47,000</strong>
                    </div>
                    <div class="crm-meta-grid__item">
                        <span class="crm-meta-grid__label">Lead Score</span>
                        <strong class="crm-metric-inline crm-icon--tertiary">94/100</strong>
                    </div>
                </div>

                <div class="crm-col">
                    <div class="crm-row crm-row--between">
                        <span class="crm-card__subtitle">Cadencia de interacción</span>
                        <strong class="crm-icon--primary">Óptima (4 días)</strong>
                    </div>
                    <div class="crm-progress">
                        <div class="crm-progress__bar" style="inline-size: 80%;"></div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Rendimiento Semanal" icon="trending_up">
                <x-slot:headerActions>
                    <span class="crm-card__subtitle tabular-nums">+14% vs sem. ant.</span>
                </x-slot:headerActions>

                <svg class="crm-sparkline crm-sparkline--wide" fill="none" viewBox="0 0 160 50" aria-hidden="true">
                    <path d="M0 42 L25 35 L50 38 L75 20 L100 24 L125 10 L160 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <circle cx="160" cy="6" r="3.5" fill="currentColor" />
                </svg>

                <x-slot:footer>
                    <span>Lun</span>
                    <span>Mar</span>
                    <span>Mié</span>
                    <strong class="crm-icon--primary">Jue</strong>
                    <span>Vie</span>
                </x-slot:footer>
            </x-ui.card>
        </div>
    </div>

    <div class="crm-stack">
        <div class="crm-segmented" role="tablist" aria-label="Filtrar actividades">
            <button
                type="button"
                role="tab"
                class="crm-segmented__btn {{ $filter === 'all' ? 'is-active' : '' }}"
                aria-selected="{{ $filter === 'all' ? 'true' : 'false' }}"
                data-fragment-nav="{{ route('activities.index', ['filter' => 'all']) }}"
                data-target-fragments="activities-feed"
            >
                Todas las actividades
            </button>
            <button
                type="button"
                role="tab"
                class="crm-segmented__btn {{ $filter === 'pending' ? 'is-active' : '' }}"
                aria-selected="{{ $filter === 'pending' ? 'true' : 'false' }}"
                data-fragment-nav="{{ route('activities.index', ['filter' => 'pending']) }}"
                data-target-fragments="activities-feed"
            >
                Mis Tareas Pendientes
            </button>
            <button
                type="button"
                role="tab"
                class="crm-segmented__btn {{ $filter === 'completed' ? 'is-active' : '' }}"
                aria-selected="{{ $filter === 'completed' ? 'true' : 'false' }}"
                data-fragment-nav="{{ route('activities.index', ['filter' => 'completed']) }}"
                data-target-fragments="activities-feed"
            >
                Completadas
            </button>
            <button
                type="button"
                role="tab"
                class="crm-segmented__btn {{ $filter === 'llamada' ? 'is-active' : '' }}"
                aria-selected="{{ $filter === 'llamada' ? 'true' : 'false' }}"
                data-fragment-nav="{{ route('activities.index', ['filter' => 'llamada']) }}"
                data-target-fragments="activities-feed"
            >
                Llamadas
            </button>
            <button
                type="button"
                role="tab"
                class="crm-segmented__btn {{ $filter === 'reunion' ? 'is-active' : '' }}"
                aria-selected="{{ $filter === 'reunion' ? 'true' : 'false' }}"
                data-fragment-nav="{{ route('activities.index', ['filter' => 'reunion']) }}"
                data-target-fragments="activities-feed"
            >
                Reuniones
            </button>
        </div>

        <crm-html-ajax
            url="{{ route('activities.index', ['filter' => $filter]) }}"
            fragment="activities-feed"
            listen-events="activities:updated"
        >
            @fragment('activities-feed')
                <div class="crm-stack">
                    @if(count($pendingActivities) > 0)
                        <div class="crm-row crm-row--between">
                            <span class="crm-page-header__eyebrow crm-icon--primary">Compromisos Programados</span>
                            <span class="crm-card__subtitle tabular-nums">{{ count($pendingActivities) }} compromisos</span>
                        </div>

                        @foreach($pendingActivities as $activity)
                            @php
                                $iconName = match ($activity['type']) {
                                    'reunion' => 'videocam',
                                    'correo' => 'mail',
                                    'tarea' => 'check_box',
                                    'nota' => 'edit_note',
                                    default => 'call',
                                };
                            @endphp
                            <article class="crm-activity-row">
                                <div class="crm-activity-row__left">
                                    <crm-form-ajax event-keys="activities:updated,kpi:updated">
                                        <form action="{{ route('activities.toggle', $activity['id']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="crm-btn crm-btn--ghost crm-btn--icon" aria-label="Marcar como completada">
                                                <span class="material-symbols-outlined crm-icon crm-icon--muted" aria-hidden="true">radio_button_unchecked</span>
                                            </button>
                                        </form>
                                    </crm-form-ajax>

                                    <div class="crm-activity-row__type-icon crm-activity-row__type-icon--{{ $activity['type'] }}" aria-hidden="true">
                                        <span class="material-symbols-outlined">{{ $iconName }}</span>
                                    </div>

                                    <div class="crm-col">
                                        <div class="crm-row">
                                            <strong>{{ $activity['subject'] }}</strong>
                                            <x-ui.badge variant="primary" :dot="false">{{ $activity['schedule_label'] }}</x-ui.badge>
                                        </div>
                                        <div class="crm-row crm-card__subtitle">
                                            <span>{{ $activity['company_name'] }}</span>
                                            <span aria-hidden="true">•</span>
                                            <span>{{ $activity['contact_name'] }}</span>
                                            @if(!empty($activity['notes']))
                                                <span aria-hidden="true">•</span>
                                                <span>{{ $activity['notes'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="crm-activity-row__actions">
                                    <crm-form-ajax event-keys="activities:updated,kpi:updated">
                                        <form action="{{ route('activities.toggle', $activity['id']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <x-ui.button type="submit" variant="secondary" size="sm" icon="check">Completar</x-ui.button>
                                        </form>
                                    </crm-form-ajax>
                                </div>
                            </article>
                        @endforeach
                    @endif

                    @if(count($completedActivities) > 0)
                        <div class="crm-row crm-row--between crm-section-gap">
                            <span class="crm-page-header__eyebrow">Historial • Completadas con éxito</span>
                            <x-ui.badge variant="emerald">100% Cumplimiento</x-ui.badge>
                        </div>

                        @foreach($completedActivities as $activity)
                            <article class="crm-activity-row crm-activity-row--completed">
                                <div class="crm-activity-row__left">
                                    <crm-form-ajax event-keys="activities:updated,kpi:updated">
                                        <form action="{{ route('activities.toggle', $activity['id']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="crm-btn crm-btn--ghost crm-btn--icon" aria-label="Reabrir tarea">
                                                <span class="material-symbols-outlined crm-icon crm-icon--tertiary" aria-hidden="true">check_circle</span>
                                            </button>
                                        </form>
                                    </crm-form-ajax>

                                    <div class="crm-activity-row__type-icon crm-activity-row__type-icon--completed" aria-hidden="true">
                                        <span class="material-symbols-outlined">assignment_turned_in</span>
                                    </div>

                                    <div class="crm-col">
                                        <strong class="crm-text-strike">{{ $activity['subject'] }}</strong>
                                        <span class="crm-card__subtitle">
                                            Completada por {{ $activity['owner'] }} • {{ $activity['notes'] ?: $activity['company_name'] }}
                                        </span>
                                    </div>
                                </div>
                                <span class="crm-card__subtitle">Archivada en historial</span>
                            </article>
                        @endforeach
                    @endif

                    @if(count($pendingActivities) === 0 && count($completedActivities) === 0)
                        <x-ui.card>
                            <div class="crm-table-empty">
                                <div class="crm-table-empty__icon" aria-hidden="true">
                                    <span class="material-symbols-outlined">event_available</span>
                                </div>
                                <h3 class="crm-table-empty__title">Sin actividades en esta vista</h3>
                                <p class="crm-table-empty__desc">Registra una nueva llamada, reunión o tarea usando el panel superior.</p>
                            </div>
                        </x-ui.card>
                    @endif
                </div>
            @endfragment
        </crm-html-ajax>
    </div>
</x-ui.layout>
