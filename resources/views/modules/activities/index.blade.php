<x-ui.layout title="Actividades y Seguimiento" active="activities" breadcrumb="Actividades y Tareas">
    <section class="crm-page-header">
        <div class="crm-page-header__title-group">
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
                        <strong class="tabular-nums">{{ $overdueCount }} Vencidas</strong>
                    </x-ui.badge>
                    <x-ui.badge variant="emerald" icon="check_circle">
                        <strong class="tabular-nums">{{ $totalCompleted }} Hechas</strong>
                    </x-ui.badge>
                </div>
            @endfragment
        </crm-html-ajax>
    </section>

    <div class="crm-section-gap">
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
                    <form id="activity-create-form" action="{{ route('activities.store') }}" method="POST" class="crm-stack">
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
                                placeholder="Selecciona una empresa"
                                :options="$crmCompanies"
                                required
                            />
                            <x-ui.form-field
                                name="contact_name"
                                label="Contacto de la empresa"
                                type="select"
                                placeholder="Sin contacto"
                                :options="$crmContacts"
                            />
                        </div>

                        <div class="crm-form-row-2">
                            <x-ui.form-field
                                name="schedule_label"
                                label="Fecha y hora programada"
                                type="datetime-local"
                                icon="schedule"
                                required
                            />
                            <x-ui.form-field
                                name="owner"
                                label="Responsable del seguimiento"
                                type="select"
                                :value="auth()->id()"
                                :options="$crmUsers"
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
    </div>

    <div class="crm-stack">
        <div class="crm-segmented" role="group" aria-label="Filtrar actividades">
            <button
                type="button"
                class="crm-segmented__btn {{ $filter === 'all' ? 'is-active' : '' }}"
                aria-pressed="{{ $filter === 'all' ? 'true' : 'false' }}"
                data-fragment-nav="{{ route('activities.index', ['filter' => 'all']) }}"
                data-target-fragments="activities-feed"
            >
                Todas las actividades
            </button>
            <button
                type="button"
                class="crm-segmented__btn {{ $filter === 'pending' ? 'is-active' : '' }}"
                aria-pressed="{{ $filter === 'pending' ? 'true' : 'false' }}"
                data-fragment-nav="{{ route('activities.index', ['filter' => 'pending']) }}"
                data-target-fragments="activities-feed"
            >
                Tareas Pendientes
            </button>
            <button
                type="button"
                class="crm-segmented__btn {{ $filter === 'completed' ? 'is-active' : '' }}"
                aria-pressed="{{ $filter === 'completed' ? 'true' : 'false' }}"
                data-fragment-nav="{{ route('activities.index', ['filter' => 'completed']) }}"
                data-target-fragments="activities-feed"
            >
                Completadas
            </button>
            <button
                type="button"
                class="crm-segmented__btn {{ $filter === 'llamada' ? 'is-active' : '' }}"
                aria-pressed="{{ $filter === 'llamada' ? 'true' : 'false' }}"
                data-fragment-nav="{{ route('activities.index', ['filter' => 'llamada']) }}"
                data-target-fragments="activities-feed"
            >
                Llamadas
            </button>
            <button
                type="button"
                class="crm-segmented__btn {{ $filter === 'reunion' ? 'is-active' : '' }}"
                aria-pressed="{{ $filter === 'reunion' ? 'true' : 'false' }}"
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
                                <x-ui.button href="#activity-create-form" variant="secondary" icon="add">Registrar actividad</x-ui.button>
                            </div>
                        </x-ui.card>
                    @endif
                </div>
            @endfragment
        </crm-html-ajax>
    </div>
</x-ui.layout>
