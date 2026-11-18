@props([
    'title' => 'Aura CRM',
    'active' => 'dashboard',
    'breadcrumb' => 'Espacio de Trabajo',
])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Aura CRM</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="crm-shell">
        <aside class="crm-sidebar" data-crm-sidebar data-collapsed="false" aria-label="Navegación principal">
            <div class="crm-sidebar__top">
                <div class="crm-sidebar__brand-row">
                    <button type="button" class="crm-sidebar__workspace-btn" data-dialog-open="command-palette-dialog">
                        <span class="crm-sidebar__logo-mark" aria-hidden="true">A</span>
                        <span class="crm-sidebar__workspace-meta">
                            <span class="crm-sidebar__workspace-name">Aura Workspace</span>
                            <span class="crm-sidebar__workspace-domain">aura.attio.io</span>
                        </span>
                    </button>
                    <button
                        type="button"
                        class="crm-sidebar__collapse-btn"
                        data-sidebar-collapse
                        aria-expanded="true"
                        aria-label="Colapsar barra lateral"
                        title="Colapsar barra lateral"
                    >
                        <span class="material-symbols-outlined crm-icon crm-icon--md" aria-hidden="true">dock_to_left</span>
                    </button>
                </div>

                <div class="crm-sidebar__action-box">
                    <button
                        type="button"
                        class="crm-btn crm-btn--secondary crm-btn--sm crm-btn--full crm-sidebar__action-btn"
                        data-dialog-open="quick-action-dialog"
                    >
                        <span class="crm-row crm-row--nowrap crm-icon--primary">
                            <span class="material-symbols-outlined crm-icon crm-icon--sm" aria-hidden="true">add</span>
                            <span class="crm-sidebar__link-text">Nueva Acción</span>
                        </span>
                        <kbd class="crm-btn__kbd crm-sidebar__kbd">C</kbd>
                    </button>
                </div>

                <div class="crm-sidebar__section-label">Espacios</div>

                <nav class="crm-sidebar__nav">
                    <a href="{{ route('dashboard') }}" class="crm-sidebar__link" @if($active === 'dashboard') aria-current="page" @endif title="Dashboard">
                        <span class="material-symbols-outlined crm-icon crm-icon--md" aria-hidden="true">grid_view</span>
                        <span class="crm-sidebar__link-text">Dashboard</span>
                    </a>
                    <a href="{{ route('companies.index') }}" class="crm-sidebar__link" @if($active === 'companies') aria-current="page" @endif title="Empresas">
                        <span class="material-symbols-outlined crm-icon crm-icon--md" aria-hidden="true">domain</span>
                        <span class="crm-sidebar__link-text">Empresas</span>
                    </a>
                    <a href="{{ route('contacts.index') }}" class="crm-sidebar__link" @if($active === 'contacts') aria-current="page" @endif title="Contactos">
                        <span class="material-symbols-outlined crm-icon crm-icon--md" aria-hidden="true">contacts</span>
                        <span class="crm-sidebar__link-text">Contactos</span>
                    </a>
                    <a href="{{ route('deals.index') }}" class="crm-sidebar__link" @if($active === 'deals') aria-current="page" @endif title="Oportunidades">
                        <span class="material-symbols-outlined crm-icon crm-icon--md" aria-hidden="true">view_kanban</span>
                        <span class="crm-sidebar__link-text">Oportunidades</span>
                    </a>
                    <a href="{{ route('activities.index') }}" class="crm-sidebar__link" @if($active === 'activities') aria-current="page" @endif title="Actividades">
                        <span class="material-symbols-outlined crm-icon crm-icon--md" aria-hidden="true">check_circle</span>
                        <span class="crm-sidebar__link-text">Actividades</span>
                    </a>
                </nav>
            </div>

            <div class="crm-sidebar__footer">
                <div class="crm-sidebar__theme-row">
                    <div class="crm-segmented" role="group" aria-label="Selector de tema visual">
                        <button type="button" class="crm-segmented__btn crm-segmented__btn--compact is-active" data-theme-set="light" aria-pressed="true" aria-label="Tema claro">
                            <span class="material-symbols-outlined crm-icon crm-icon--xs" aria-hidden="true">light_mode</span>
                        </button>
                        <button type="button" class="crm-segmented__btn crm-segmented__btn--compact" data-theme-set="dark" aria-pressed="false" aria-label="Tema oscuro">
                            <span class="material-symbols-outlined crm-icon crm-icon--xs" aria-hidden="true">dark_mode</span>
                        </button>
                    </div>
                    <a href="{{ route('dashboard') }}" class="crm-btn crm-btn--ghost crm-btn--icon crm-sidebar__settings-link" aria-label="Configuración del espacio">
                        <span class="material-symbols-outlined crm-icon crm-icon--md" aria-hidden="true">settings</span>
                    </a>
                </div>

                <div class="crm-sidebar__profile">
                    <div class="crm-sidebar__profile-info">
                        <span class="crm-avatar" aria-hidden="true">SM</span>
                        <div class="crm-sidebar__profile-meta">
                            <span class="crm-sidebar__profile-name">Sofía Méndez</span>
                            <span class="crm-sidebar__profile-role">Admin / Ventas</span>
                        </div>
                    </div>
                    <span class="material-symbols-outlined crm-icon crm-icon--md crm-icon--muted crm-sidebar__profile-more" aria-hidden="true">more_vert</span>
                </div>
            </div>
        </aside>

        <div class="crm-workspace">
            <header class="crm-topbar">
                <div class="crm-topbar__left">
                    <button
                        type="button"
                        class="crm-btn crm-btn--ghost crm-btn--icon crm-topbar__mobile-trigger"
                        data-sidebar-mobile-toggle
                        aria-label="Abrir menú de navegación"
                    >
                        <span class="material-symbols-outlined" aria-hidden="true">menu</span>
                    </button>
                    <nav class="crm-topbar__breadcrumbs" aria-label="Ruta de navegación">
                        <a href="{{ route('dashboard') }}" class="crm-topbar__crumb-root">Aura CRM</a>
                        <span class="crm-topbar__crumb-sep" aria-hidden="true">/</span>
                        <span class="crm-topbar__crumb-current">{{ $breadcrumb }}</span>
                    </nav>
                </div>

                <div class="crm-topbar__actions">
                    <button type="button" class="crm-topbar__search-btn" data-dialog-open="command-palette-dialog">
                        <span class="material-symbols-outlined crm-icon crm-icon--sm crm-icon--muted" aria-hidden="true">search</span>
                        <span class="crm-topbar__search-text">Buscar contactos, empresas...</span>
                        <kbd class="crm-btn__kbd">⌘K</kbd>
                    </button>

                    <div class="crm-topbar__icon-group">
                        <a href="{{ route('activities.index') }}" class="crm-btn crm-btn--ghost crm-btn--icon" aria-label="Actividades pendientes" title="Actividades pendientes">
                            <span class="material-symbols-outlined" aria-hidden="true">notifications</span>
                        </a>
                        <button type="button" class="crm-btn crm-btn--ghost crm-btn--icon" data-dialog-open="command-palette-dialog" aria-label="Atajos y ayuda" title="Atajos rápidos">
                            <span class="material-symbols-outlined" aria-hidden="true">help</span>
                        </button>
                    </div>
                </div>
            </header>

            <main class="crm-main">
                <div class="crm-page">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <x-ui.dialog
        id="quick-action-dialog"
        title="Nueva Acción Rápida"
        subtitle="Registra un seguimiento comercial inmediato desde cualquier módulo"
        icon="bolt"
        variant="modal"
    >
        <crm-form-validate>
            <crm-form-ajax event-keys="activities:updated,kpi:updated" reset-on-success>
                <form action="{{ route('activities.store') }}" method="POST" class="crm-stack">
                    @csrf
                    <x-ui.form-field
                        name="type"
                        label="Tipo de Actividad"
                        type="select"
                        value="llamada"
                        :options="[
                            'llamada' => 'Llamada comercial',
                            'reunion' => 'Reunión / Demo',
                            'correo' => 'Correo de seguimiento',
                            'tarea' => 'Tarea interna',
                            'nota' => 'Nota rápida',
                        ]"
                        required
                    />
                    <x-ui.form-field
                        name="subject"
                        label="Asunto"
                        placeholder="Ej. Llamada de seguimiento con prospecto"
                        required
                        autofocus
                    />
                    <div class="crm-form-row-2">
                        <x-ui.form-field
                            name="company_name"
                            label="Empresa"
                            placeholder="Ej. NovaTech Solutions"
                            required
                        />
                        <x-ui.form-field
                            name="schedule_label"
                            label="Horario programado"
                            value="Hoy, 16:30"
                            required
                        />
                    </div>
                    <x-ui.form-field
                        name="notes"
                        label="Notas"
                        type="textarea"
                        placeholder="Acuerdos, próximos pasos o contexto..."
                        rows="2"
                    />
                    <div class="crm-row crm-row--end">
                        <x-ui.button type="button" variant="secondary" data-dialog-close>Cancelar</x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="check">Guardar Acción</x-ui.button>
                    </div>
                </form>
            </crm-form-ajax>
        </crm-form-validate>
    </x-ui.dialog>

    <x-ui.dialog
        id="command-palette-dialog"
        title="Navegación Rápida"
        subtitle="Salta entre módulos o busca registros en Aura CRM"
        icon="search"
        variant="modal"
    >
        <search>
            <form action="{{ route('contacts.index') }}" method="GET" class="crm-stack">
                <x-ui.form-field
                    name="search"
                    label="Buscar contacto, empresa o palabra clave"
                    type="search"
                    icon="search"
                    placeholder="Escribe el nombre de un contacto o empresa y presiona Enter..."
                    autofocus
                />
                <div class="crm-row">
                    <x-ui.button href="{{ route('dashboard') }}" variant="secondary" size="sm" icon="grid_view">Dashboard</x-ui.button>
                    <x-ui.button href="{{ route('companies.index') }}" variant="secondary" size="sm" icon="domain">Empresas</x-ui.button>
                    <x-ui.button href="{{ route('contacts.index') }}" variant="secondary" size="sm" icon="contacts">Contactos</x-ui.button>
                    <x-ui.button href="{{ route('deals.index') }}" variant="secondary" size="sm" icon="view_kanban">Oportunidades</x-ui.button>
                    <x-ui.button href="{{ route('activities.index') }}" variant="secondary" size="sm" icon="check_circle">Actividades</x-ui.button>
                </div>
                <div class="crm-row crm-row--end">
                    <x-ui.button type="button" variant="ghost" data-dialog-close>Cerrar (ESC)</x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="search">Buscar Contactos</x-ui.button>
                </div>
            </form>
        </search>
    </x-ui.dialog>

    <crm-toast id="crm-toaster"></crm-toast>
</body>
</html>
