<x-ui.layout title="Contactos y Prospectos" active="contacts" breadcrumb="Contactos y Prospectos">
    <section class="crm-page-header">
        <div class="crm-page-header__title-group">
            <div class="crm-page-header__title-row">
                <h1 class="crm-page-header__title">Contactos y Prospectos</h1>
                <crm-html-ajax
                    url="{{ route('contacts.index', request()->query()) }}"
                    fragment="contacts-kpi"
                    listen-events="contacts:updated,kpi:updated"
                >
                    @fragment('contacts-kpi')
                        <div class="crm-row">
                            <x-ui.badge variant="neutral" :dot="false">
                                <span class="tabular-nums">{{ $totalContacts }} contactos</span>
                            </x-ui.badge>
                            <x-ui.badge variant="emerald">
                                Calificados: <span class="tabular-nums">{{ $qualifiedCount }}</span>
                            </x-ui.badge>
                            <x-ui.badge variant="sky">
                                Nuevos: <span class="tabular-nums">{{ $newCount }}</span>
                            </x-ui.badge>
                        </div>
                    @endfragment
                </crm-html-ajax>
            </div>
            <p class="crm-page-header__subtitle">
                Gestiona y califica la base comercial de leads y tomadores de decisión
            </p>
        </div>

        <div class="crm-page-header__controls">
            <x-ui.button variant="secondary" icon="ios_share" href="{{ route('companies.index') }}">
                Ver Empresas
            </x-ui.button>
            <x-ui.button variant="primary" icon="add" data-dialog-open="create-contact-dialog">
                Nuevo Contacto
            </x-ui.button>
        </div>
    </section>

    <crm-html-ajax
        url="{{ route('contacts.index', request()->query()) }}"
        fragment="contacts-table"
        listen-events="contacts:updated"
    >
        @fragment('contacts-table')
            <search>
                <form action="{{ route('contacts.index') }}" method="GET" class="crm-toolbar" data-ajax-filter>
                    <div class="crm-toolbar__filters">
                        <div class="crm-search-wrap">
                            <label for="contacts-search" class="sr-only">Buscar contactos</label>
                            <div class="crm-field__control-wrap">
                                <span class="material-symbols-outlined crm-field__icon" aria-hidden="true">search</span>
                                <input
                                    id="contacts-search"
                                    type="search"
                                    name="search"
                                    value="{{ $filters['search'] }}"
                                    class="crm-field__control crm-field__control--with-icon"
                                    placeholder="Buscar por nombre, empresa o correo..."
                                >
                            </div>
                        </div>

                        <label for="contacts-status-filter" class="sr-only">Estado del lead</label>
                        <select id="contacts-status-filter" name="status" class="crm-field__control crm-filter-select">
                            <option value="">Estado: Todos</option>
                            <option value="Calificado" @selected($filters['status'] === 'Calificado')>Calificado</option>
                            <option value="Nuevo Lead" @selected($filters['status'] === 'Nuevo Lead')>Nuevo Lead</option>
                            <option value="Contactado" @selected($filters['status'] === 'Contactado')>Contactado</option>
                            <option value="Perdido" @selected($filters['status'] === 'Perdido')>Perdido</option>
                        </select>

                        <label for="contacts-owner-filter" class="sr-only">Ejecutivo asignado</label>
                        <select id="contacts-owner-filter" name="owner" class="crm-field__control crm-filter-select">
                            <option value="">Asignado: Todos</option>
                            @foreach($crmUsers as $id => $name)
                                <option value="{{ $id }}" @selected($filters['owner'] === (string) $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="crm-toolbar__actions">
                        <x-ui.button type="submit" variant="secondary" size="sm" icon="search">Filtrar</x-ui.button>
                        <x-ui.button href="{{ route('contacts.index') }}" variant="ghost" size="sm" icon="restart_alt">Limpiar</x-ui.button>
                    </div>
                </form>
            </search>

            <div class="crm-section-gap">
                <x-ui.table :empty="count($contacts) === 0" emptyTitle="Sin contactos que coincidan" emptyDescription="No se encontraron prospectos con el filtro actual.">
                    <x-slot:head>
                        <tr>
                            <th>Nombre y Cargo</th>
                            <th>Empresa Vinculada</th>
                            <th>Contacto Directo</th>
                            <th>Origen</th>
                            <th>Estado Lead</th>
                            <th class="is-actions">Acciones Rápidas</th>
                        </tr>
                    </x-slot:head>

                    @foreach($contacts as $contact)
                        <tr>
                            <td>
                                <div class="crm-row crm-row--nowrap">
                                    <span class="crm-avatar" aria-hidden="true">{{ $contact['initials'] }}</span>
                                    <div class="crm-col">
                                        <strong>{{ $contact['name'] }}</strong>
                                        <span class="crm-card__subtitle">{{ $contact['role'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($contact['company_name'])
                                <a href="{{ route('companies.index', ['company_id' => $contact['company_id']]) }}" class="crm-account-pill">
                                    <span class="material-symbols-outlined crm-icon crm-icon--xs crm-icon--primary" aria-hidden="true">domain</span>
                                    <span>{{ $contact['company_name'] }}</span>
                                </a>
                                @endif
                            </td>
                            <td>
                                <div class="crm-col">
                                    <a href="mailto:{{ $contact['email'] }}" class="crm-table-cell-link">{{ $contact['email'] }}</a>
                                    <span class="crm-card__subtitle tabular-nums">{{ $contact['phone'] }}</span>
                                </div>
                            </td>
                            <td>
                                <x-ui.badge variant="neutral" :dot="false">{{ $contact['source'] }}</x-ui.badge>
                            </td>
                            <td>
                                <x-ui.badge :variant="$contact['status_variant']">{{ $contact['status'] }}</x-ui.badge>
                            </td>
                            <td class="is-actions">
                                <div class="crm-row crm-row--end crm-row--nowrap">
                                    <x-ui.button href="{{ route('deals.index') }}" variant="ghost" size="sm">Ver oportunidades</x-ui.button>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    <x-slot:footer>
                        <span>
                            Mostrando <strong class="tabular-nums">{{ count($contacts) }}</strong> de
                            <strong class="tabular-nums">{{ $totalContacts }}</strong> contactos
                        </span>
                        <span class="crm-card__subtitle">Actualización parcial vía SSR Híbrido</span>
                    </x-slot:footer>
                </x-ui.table>
            </div>
        @endfragment
    </crm-html-ajax>

    <x-ui.dialog
        id="create-contact-dialog"
        title="Crear Nuevo Contacto"
        subtitle="Añade un prospecto o cliente a tu canal de ventas comercial"
        icon="person_add"
        variant="drawer"
    >
        <crm-form-validate>
            <crm-form-ajax event-keys="contacts:updated,kpi:updated" reset-on-success>
                <form action="{{ route('contacts.store') }}" method="POST" class="crm-stack">
                    @csrf
                    <div class="crm-form-row-2">
                        <x-ui.form-field
                            name="first_name"
                            label="Nombre"
                            placeholder="Alejandro"
                            autocomplete="given-name"
                            required
                            autofocus
                        />
                        <x-ui.form-field
                            name="last_name"
                            label="Apellido"
                            placeholder="Gómez"
                            autocomplete="family-name"
                            required
                        />
                    </div>

                    <x-ui.form-field
                        name="email"
                        label="Correo Electrónico Corporativo"
                        type="email"
                        placeholder="nombre@empresa.com"
                        hint="Formato corporativo: nombre@empresa.com"
                        validateMsg="Por favor ingresa un correo corporativo válido (@empresa.com)"
                        autocomplete="email"
                        autocorrect="off"
                        autocapitalize="none"
                        spellcheck="false"
                        required
                    />

                    <x-ui.form-field
                        name="phone"
                        label="Teléfono Directo"
                        type="tel"
                        placeholder="+34 612 345 678"
                        autocomplete="tel"
                    />

                    <x-ui.form-field
                        name="role"
                        label="Cargo o Puesto Comercial"
                        placeholder="Head of Growth"
                        required
                    />

                    <x-ui.form-field
                        name="company_name"
                        label="Empresa Asociada"
                        type="select"
                        placeholder="Selecciona una empresa"
                        :options="$companyOptions"
                        required
                    />

                    <x-ui.form-field
                        name="owner"
                        label="Responsable"
                        type="select"
                        :value="auth()->id()"
                        :options="$crmUsers"
                        required
                    />

                    <div class="crm-form-row-2">
                        <x-ui.form-field
                            name="source"
                            label="Canal de Adquisición / Origen"
                            type="select"
                            value="Web Inbound"
                            :options="[
                                'Referido Socio' => 'Recomendación / Referido Directo',
                                'Web Inbound' => 'Sitio Web Inbound',
                                'Llamada en Frío' => 'Llamada Fría / Prospección',
                                'Evento Tech Summit' => 'Evento Tecnológico / Conferencia',
                            ]"
                            required
                        />
                        <x-ui.form-field
                            name="status"
                            label="Etapa Inicial del Lead"
                            type="select"
                            value="Nuevo Lead"
                            :options="[
                                'Nuevo Lead' => 'Nuevo Lead',
                                'Calificado' => 'Calificado',
                                'Contactado' => 'Contactado',
                            ]"
                            required
                        />
                    </div>

                    <x-ui.form-field
                        name="notes"
                        label="Notas Internas de Oportunidad"
                        type="textarea"
                        rows="3"
                        placeholder="Tomador de decisión clave, contexto o requerimientos..."
                    />

                    <div class="crm-row crm-row--end crm-form-actions">
                        <x-ui.button type="button" variant="secondary" data-dialog-close>Cancelar</x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="check">Guardar Contacto</x-ui.button>
                    </div>
                </form>
            </crm-form-ajax>
        </crm-form-validate>
    </x-ui.dialog>
</x-ui.layout>
