<x-ui.layout title="Empresas y Cuentas Comerciales" active="companies" breadcrumb="Empresas y Cuentas">
    <section class="crm-page-header">
        <div class="crm-page-header__title-group">
            <h1 class="crm-page-header__title">Empresas y Cuentas Comerciales</h1>
            <p class="crm-page-header__subtitle">
                Directorio B2B, métricas financieras por cuenta y cronograma de interlocutores clave
            </p>
        </div>

        <div class="crm-page-header__controls">
            <div class="crm-segmented" role="group" aria-label="Modo de visualización de cuentas">
                <a
                    href="{{ route('companies.index', array_merge(request()->query(), ['view' => 'split'])) }}"
                    class="crm-segmented__btn {{ $filters['view'] !== 'table' ? 'is-active' : '' }}"
                    @if($filters['view'] !== 'table') aria-current="true" @endif
                    data-fragment-nav="{{ route('companies.index', array_merge(request()->query(), ['view' => 'split'])) }}"
                    data-target-fragments="companies-list"
                >
                    <span class="material-symbols-outlined crm-icon crm-icon--sm" aria-hidden="true">splitscreen</span>
                    <span>Vista Dividida</span>
                </a>
                <a
                    href="{{ route('companies.index', array_merge(request()->query(), ['view' => 'table'])) }}"
                    class="crm-segmented__btn {{ $filters['view'] === 'table' ? 'is-active' : '' }}"
                    @if($filters['view'] === 'table') aria-current="true" @endif
                    data-fragment-nav="{{ route('companies.index', array_merge(request()->query(), ['view' => 'table'])) }}"
                    data-target-fragments="companies-list"
                >
                    <span class="material-symbols-outlined crm-icon crm-icon--sm" aria-hidden="true">table_rows</span>
                    <span>Tabla</span>
                </a>
            </div>

            <x-ui.button variant="primary" icon="add_business" data-dialog-open="create-company-dialog">
                Crear Empresa
            </x-ui.button>
        </div>
    </section>

    <crm-html-ajax
        url="{{ route('companies.index', request()->query()) }}"
        fragment="companies-list"
        listen-events="companies:updated,activities:updated"
    >
        @fragment('companies-list')
            <search>
                <form action="{{ route('companies.index') }}" method="GET" class="crm-toolbar" data-ajax-filter>
                    <input type="hidden" name="view" value="{{ $filters['view'] }}">
                    <div class="crm-toolbar__filters">
                        <div class="crm-row crm-row--nowrap">
                            <strong>Filtros:</strong>
                        </div>

                        <label for="filter-sector" class="sr-only">Sector</label>
                        <select id="filter-sector" name="sector" class="crm-field__control crm-filter-select">
                            <option value="">Sector: Todos</option>
                            <option value="SaaS" @selected($filters['sector'] === 'SaaS')>Sector: SaaS</option>
                            <option value="FinTech" @selected($filters['sector'] === 'FinTech')>Sector: FinTech</option>
                            <option value="Logística" @selected($filters['sector'] === 'Logística')>Sector: Logística</option>
                        </select>

                        <label for="filter-status" class="sr-only">Estado</label>
                        <select id="filter-status" name="status" class="crm-field__control crm-filter-select">
                            <option value="">Estado: Todos</option>
                            <option value="Activa" @selected($filters['status'] === 'Activa')>Estado: Activa</option>
                            <option value="Prospecto" @selected($filters['status'] === 'Prospecto')>Estado: Prospecto</option>
                        </select>

                        <label for="filter-owner" class="sr-only">Responsable</label>
                        <select id="filter-owner" name="owner" class="crm-field__control crm-filter-select">
                            <option value="">Responsable: Todos</option>
                            @foreach($crmUsers as $id => $name)
                                <option value="{{ $id }}" @selected($filters['owner'] === (string) $id)>{{ $name }}</option>
                            @endforeach
                        </select>

                        <span class="crm-card__subtitle">
                            Mostrando {{ count($companies) }} de {{ $totalCompanies }} cuentas
                        </span>
                    </div>

                    <div class="crm-toolbar__actions">
                        <x-ui.button type="submit" variant="secondary" size="sm" icon="search">Aplicar</x-ui.button>
                        @if(($filters['sector'] !== '' || $filters['status'] !== '' || $filters['owner'] !== '') && count($companies) > 0)
                            <x-ui.button href="{{ route('companies.index') }}" variant="ghost" size="sm" icon="restart_alt">Limpiar</x-ui.button>
                        @endif
                    </div>
                </form>
            </search>

            @if($filters['view'] === 'table' || !$activeCompany)
                <div class="crm-section-gap">
                    <x-ui.table :empty="count($companies) === 0" emptyTitle="No se encontraron empresas" emptyDescription="Prueba ajustando los filtros o registra una nueva cuenta comercial.">
                        <x-slot:emptyAction>
                            @if($totalCompanies === 0)
                                <x-ui.button variant="primary" icon="add_business" data-dialog-open="create-company-dialog">Crear Empresa</x-ui.button>
                            @else
                                <x-ui.button href="{{ route('companies.index') }}" variant="secondary" icon="restart_alt">Limpiar filtros</x-ui.button>
                            @endif
                        </x-slot:emptyAction>
                        <x-slot:head>
                            <tr>
                                <th>Empresa</th>
                                <th>Sector</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                                <th class="is-numeric">Valor en Deals</th>
                                <th class="is-numeric">MRR Actual</th>
                                <th>Responsable</th>
                            </tr>
                        </x-slot:head>

                        @foreach($companies as $company)
                            <tr>
                                <td>
                                    <a
                                        href="{{ route('companies.index', ['company_id' => $company['id'], 'view' => 'split']) }}"
                                        class="crm-table-cell-link"
                                    >
                                        <span class="crm-col">
                                            <span>{{ $company['name'] }}</span>
                                            <span class="crm-card__subtitle">{{ $company['tax_id'] }}</span>
                                        </span>
                                    </a>
                                </td>
                                <td>{{ $company['industry'] }}</td>
                                <td>{{ $company['location'] }}</td>
                                <td><x-ui.badge :variant="$company['status_variant']">{{ $company['status'] }}</x-ui.badge></td>
                                <td class="is-numeric"><strong>${{ number_format($company['deals_total'], 2) }} USD</strong></td>
                                <td class="is-numeric">${{ number_format($company['mrr'], 2) }}/mes</td>
                                <td>{{ $company['owner'] }}</td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                </div>
            @else
                <div class="crm-split-layout crm-split-layout--5-7 crm-section-gap">
                    <div class="crm-stack">
                        <x-ui.card>
                            <div class="crm-company-hero">
                                <div class="crm-row crm-row--nowrap">
                                    <span class="crm-avatar crm-avatar--lg" aria-hidden="true">{{ $activeCompany['initial'] }}</span>
                                    <div class="crm-col">
                                        <h2 class="crm-card__title">{{ $activeCompany['name'] }}</h2>
                                        <div class="crm-row crm-card__subtitle">
                                            <span class="tabular-nums">{{ $activeCompany['tax_id'] }}</span>
                                            @if($activeCompany['tax_id'] && $activeCompany['tier'])
                                                <span aria-hidden="true">•</span>
                                            @endif
                                            <span>{{ $activeCompany['tier'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <x-ui.badge :variant="$activeCompany['status_variant']">{{ $activeCompany['status'] }}</x-ui.badge>
                            </div>

                            <div class="crm-meta-grid">
                                <div class="crm-meta-grid__item">
                                    <span class="crm-meta-grid__label">Industria</span>
                                    <span class="crm-meta-grid__value">
                                        <span>{{ $activeCompany['industry'] }}</span>
                                    </span>
                                </div>
                                <div class="crm-meta-grid__item">
                                    <span class="crm-meta-grid__label">Ubicación</span>
                                    <span class="crm-meta-grid__value">
                                        <span>{{ $activeCompany['location'] }}</span>
                                    </span>
                                </div>
                                <div class="crm-meta-grid__item">
                                    <span class="crm-meta-grid__label">Sitio Web</span>
                                    @if($activeCompany['website'])
                                        <a href="{{ \Illuminate\Support\Str::startsWith($activeCompany['website'], ['http://', 'https://']) ? $activeCompany['website'] : 'https://' . $activeCompany['website'] }}" target="_blank" rel="noopener" class="crm-meta-grid__value crm-icon--primary">
                                            <span class="material-symbols-outlined crm-icon crm-icon--xs" aria-hidden="true">link</span>
                                            <span class="truncate">{{ $activeCompany['website'] }}</span>
                                        </a>
                                    @else
                                        <span class="crm-meta-grid__value">Sin sitio web</span>
                                    @endif
                                </div>
                                <div class="crm-meta-grid__item">
                                    <span class="crm-meta-grid__label">Teléfono Directo</span>
                                    <span class="crm-meta-grid__value tabular-nums">
                                        <span>{{ $activeCompany['phone'] }}</span>
                                    </span>
                                </div>
                            </div>

                            <div class="crm-row crm-row--end">
                                <x-ui.button variant="secondary" icon="playlist_add" data-dialog-open="quick-action-dialog">
                                    Actividad
                                </x-ui.button>
                            </div>
                        </x-ui.card>

                        <x-ui.card title="Métricas Financieras de Cuenta" variant="metric">
                            <div class="crm-kpi-box">
                                <span class="crm-meta-grid__label">Valor Total en Deals</span>
                                <span class="crm-metric-inline">${{ number_format($activeCompany['deals_total'], 2) }}</span>
                                <span class="crm-card__subtitle">{{ $activeCompany['deals_count'] }} oportunidades abiertas</span>
                            </div>

                            <div class="crm-kpi-box">
                                <span class="crm-meta-grid__label">MRR Recurrente Actual</span>
                                <span class="crm-metric-inline">${{ number_format($activeCompany['mrr'], 2) }} /mes</span>
                                <span class="crm-card__subtitle">{{ $activeCompany['mrr_plan'] }}</span>
                            </div>

                            <x-slot:footer>
                                <span class="crm-card__subtitle">Progreso del Pipeline Activo</span>
                                <strong class="crm-icon--primary tabular-nums">{{ $activeCompany['weighted_prob'] }}% probabilidad ponderada</strong>
                            </x-slot:footer>
                        </x-ui.card>

                        <x-ui.card title="Contactos Vinculados">
                            <x-slot:headerActions>
                                <x-ui.badge variant="primary" :dot="false">{{ count($linkedContacts) }}</x-ui.badge>
                            </x-slot:headerActions>

                            @forelse($linkedContacts as $contact)
                                <div class="crm-contact-mini">
                                    <div class="crm-col">
                                        <strong>{{ $contact['name'] }}</strong>
                                        <span class="crm-card__subtitle">{{ $contact['role'] }}</span>
                                    </div>
                                    <div class="crm-row crm-row--nowrap">
                                        @if($contact['role_tag'])
                                            <x-ui.badge variant="emerald" :dot="false">{{ $contact['role_tag'] }}</x-ui.badge>
                                        @endif
                                        <a href="mailto:{{ $contact['email'] }}" class="crm-btn crm-btn--ghost crm-btn--icon" aria-label="Enviar correo a {{ $contact['name'] }}">
                                            <span class="material-symbols-outlined crm-icon crm-icon--sm" aria-hidden="true">mail</span>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <p class="crm-card__subtitle">Sin contactos asociados directamente todavía.</p>
                            @endforelse
                        </x-ui.card>
                    </div>

                    <div class="crm-stack">
                        <div class="crm-tabs">
                            <div class="crm-tabs__nav" role="group" aria-label="Cuentas activas">
                                @foreach(array_slice($companies, 0, 4) as $tabCompany)
                                    <a
                                        href="{{ route('companies.index', ['company_id' => $tabCompany['id'], 'view' => 'split']) }}"
                                        class="crm-tabs__tab {{ $tabCompany['id'] === $activeCompany['id'] ? 'is-active' : '' }}"
                                        @if($tabCompany['id'] === $activeCompany['id']) aria-current="true" @endif
                                        data-fragment-nav="{{ route('companies.index', ['company_id' => $tabCompany['id'], 'view' => 'split']) }}"
                                        data-target-fragments="companies-list"
                                    >
                                        <span>{{ $tabCompany['name'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <x-ui.card title="Registrar Interacción o Nota Interna" icon="edit_note">
                            <crm-form-validate>
                                <crm-form-ajax event-keys="activities:updated,companies:updated" reset-on-success>
                                    <form action="{{ route('activities.store') }}" method="POST" class="crm-stack">
                                        @csrf
                                        <input type="hidden" name="type" value="nota">
                                        <input type="hidden" name="company_name" value="{{ $activeCompany['id'] }}">
                                        <x-ui.form-field
                                            name="subject"
                                            label="Resumen de la interacción"
                                            placeholder="Ej. Validación de SLA Enterprise con dirección de operaciones"
                                            required
                                        />
                                        <x-ui.form-field
                                            name="notes"
                                            label="Nota interna para el equipo"
                                            type="textarea"
                                            rows="3"
                                            placeholder="Escribe una nota rápida o registra un acuerdo con {{ $activeCompany['name'] }}..."
                                        />
                                        <div class="crm-row crm-row--between">
                                            <span class="crm-card__subtitle">Visible según la asignación del responsable</span>
                                            <x-ui.button type="submit" variant="primary" icon="save">Guardar Nota</x-ui.button>
                                        </div>
                                    </form>
                                </crm-form-ajax>
                            </crm-form-validate>
                        </x-ui.card>

                        <x-ui.card title="Cronograma de Actividades Recientes" icon="dynamic_feed">
                            <div class="crm-timeline">
                                @forelse($companyActivities as $act)
                                    <div class="crm-timeline__item">
                                        <div class="crm-timeline__node crm-timeline__node--{{ $act['completed'] ? 'emerald' : 'primary' }}" aria-hidden="true">
                                            <span class="crm-timeline__node-dot"></span>
                                        </div>
                                        <div class="crm-timeline__body">
                                            <div class="crm-timeline__header">
                                                <span class="crm-timeline__actor">{{ $act['subject'] }}</span>
                                                <span class="crm-timeline__time">{{ $act['schedule_label'] }}</span>
                                            </div>
                                            <p class="crm-card__subtitle">{{ $act['notes'] ?: 'Seguimiento registrado por ' . $act['owner'] }}</p>
                                            <div class="crm-row">
                                                <x-ui.badge variant="neutral" :dot="false">{{ ucfirst($act['type']) }}</x-ui.badge>
                                                @if($act['contact_name'])
                                                    <span class="crm-card__subtitle">Interlocutor: {{ $act['contact_name'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="crm-card__subtitle">Sin actividades vinculadas todavía.</p>
                                @endforelse
                            </div>
                        </x-ui.card>
                    </div>
                </div>
            @endif
        @endfragment
    </crm-html-ajax>

    <x-ui.dialog
        id="create-company-dialog"
        title="Crear Nueva Empresa"
        subtitle="Registra una cuenta comercial B2B en el directorio de Aura CRM"
        icon="add_business"
        variant="drawer"
    >
        <crm-form-validate>
            <crm-form-ajax event-keys="companies:updated,kpi:updated" reset-on-success>
                <form action="{{ route('companies.store') }}" method="POST" class="crm-stack">
                    @csrf
                    <x-ui.form-field
                        name="name"
                        label="Razón Social / Nombre de Empresa"
                        placeholder="Ej. Vertex Cloud Iberia S.L."
                        required
                        autofocus
                    />
                    <div class="crm-form-row-2">
                        <x-ui.form-field
                            name="tax_id"
                            label="NIF / Tax ID"
                            placeholder="Ej. B-91234567"
                        />
                        <x-ui.form-field
                            name="sector"
                            label="Sector"
                            type="select"
                            value="SaaS"
                            :options="[
                                'SaaS' => 'SaaS / Software',
                                'FinTech' => 'FinTech / Banca',
                                'Logística' => 'Logística / Industria',
                            ]"
                            required
                        />
                    </div>
                    <div class="crm-form-row-2">
                        <x-ui.form-field
                            name="status"
                            label="Estado Comercial"
                            type="select"
                            value="Activa"
                            :options="[
                                'Activa - Tier 1' => 'Activa - Tier 1',
                                'Activa' => 'Activa',
                                'Prospecto' => 'Prospecto',
                            ]"
                            required
                        />
                        <x-ui.form-field
                            name="location"
                            label="Ubicación"
                            placeholder="Madrid, España"
                            required
                        />
                    </div>
                    <div class="crm-form-row-2">
                        <x-ui.form-field
                            name="website"
                            label="Sitio Web"
                            placeholder="empresa.io"
                        />
                        <x-ui.form-field
                            name="phone"
                            label="Teléfono Directo"
                            type="tel"
                            placeholder="+34 910 000 000"
                        />
                    </div>
                    <div class="crm-form-row-2">
                        <x-ui.form-field
                            name="owner"
                            label="Responsable"
                            type="select"
                            :value="auth()->id()"
                            :options="$crmUsers"
                            required
                        />
                        <x-ui.form-field
                            name="mrr"
                            label="MRR Estimado"
                            type="number"
                            prefix="USD $"
                            value="0"
                            min="0"
                            step="0.01"
                        />
                    </div>
                    <div class="crm-row crm-row--end crm-form-actions">
                        <x-ui.button type="button" variant="secondary" data-dialog-close>Cancelar</x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="check">Guardar Empresa</x-ui.button>
                    </div>
                </form>
            </crm-form-ajax>
        </crm-form-validate>
    </x-ui.dialog>
</x-ui.layout>
