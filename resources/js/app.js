import './components/crm-toast';
import './components/crm-dialog';
import './components/crm-form-validate';
import './components/crm-form-ajax';
import './components/crm-html-ajax';
import './components/crm-kanban';
import { commitFragmentBatch, transitionView } from './view-transitions.js';

document.addEventListener('DOMContentLoaded', () => {
    initThemeToggle();
    initSidebarToggle();
    initKeyboardShortcuts();
    initFragmentFilterButtons();
    initPipelineViewToggle();
    initActivityTypeSelector();
    initCompanyOptions();
    initDealStageSelection();
});

function initDealStageSelection() {
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-dialog-open="create-deal-dialog"]');
        if (!trigger) return;

        const stageSelect = document.querySelector('#create-deal-dialog select[name="stage"]');
        if (!stageSelect) return;

        const defaultStage =
            stageSelect.querySelector('option[selected]')?.value ||
            stageSelect.options[0]?.value;
        const requestedStage = trigger.getAttribute('data-deal-stage');
        const hasRequestedStage = Array.from(stageSelect.options).some((option) => option.value === requestedStage);
        const stage = hasRequestedStage ? requestedStage : defaultStage;

        if (stage) stageSelect.value = stage;
    }, true);
}

function initCompanyOptions() {
    window.addEventListener('companies:updated', (event) => {
        const company = event.detail?.company;
        if (!company?.id || !company?.name) return;

        document.querySelectorAll('select[name="company_name"]').forEach((select) => {
            if (!Array.from(select.options).some((option) => option.value === String(company.id))) {
                select.add(new Option(company.name, String(company.id)));
            }
        });
    });
}

function initThemeToggle() {
    const root = document.documentElement;
    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');

    const updateEffectiveTheme = () => {
        const mode = root.dataset.theme || 'system';
        root.dataset.effectiveTheme = mode === 'system' ? (systemTheme.matches ? 'dark' : 'light') : mode;
        updateThemeButtons(mode);
    };

    systemTheme.addEventListener('change', () => {
        if (root.dataset.theme === 'system') updateEffectiveTheme();
    });

    document.addEventListener('click', (event) => {
        const themeBtn = event.target.closest('[data-theme-set]');
        if (!themeBtn) return;

        const mode = themeBtn.getAttribute('data-theme-set');
        if (mode === 'system' || mode === 'light' || mode === 'dark') {
            root.setAttribute('data-theme', mode);
            savePreference('aura-theme', mode);
            updateEffectiveTheme();
        }
    });

    updateEffectiveTheme();
}

function readPreference(key) {
    try {
        return window.localStorage.getItem(key);
    } catch (error) {
        console.warn('No se pudo leer la preferencia.', error);
        return null;
    }
}

function savePreference(key, value) {
    try {
        window.localStorage.setItem(key, value);
    } catch (error) {
        console.warn('No se pudo guardar la preferencia.', error);
    }
}

function updateThemeButtons(activeTheme) {
    document.querySelectorAll('[data-theme-set]').forEach((btn) => {
        const isCurrent = btn.getAttribute('data-theme-set') === activeTheme;
        btn.setAttribute('aria-pressed', isCurrent ? 'true' : 'false');
        btn.classList.toggle('is-active', isCurrent);
    });
}

function initSidebarToggle() {
    const sidebar = document.querySelector('[data-crm-sidebar]');
    if (!sidebar) return;

    const mobileToggle = document.querySelector('[data-sidebar-mobile-toggle]');
    const collapseButton = sidebar.querySelector('[data-sidebar-collapse]');
    const mobileViewport = window.matchMedia('(max-width: 63.99rem)');
    let collapsed = readPreference('aura-sidebar-collapsed') === 'true';

    const setMobileOpen = (open, returnFocus = false) => {
        if (!open && returnFocus && sidebar.contains(document.activeElement)) {
            mobileToggle?.focus();
        }
        sidebar.setAttribute('data-mobile-open', String(open));
        sidebar.inert = mobileViewport.matches && !open;
        mobileToggle?.setAttribute('aria-expanded', String(open));
        if (open) sidebar.querySelector('a[href]')?.focus();
    };

    const syncViewport = () => {
        sidebar.setAttribute('data-collapsed', String(!mobileViewport.matches && collapsed));
        collapseButton?.setAttribute('aria-expanded', String(!collapsed));
        setMobileOpen(false, true);
    };

    mobileViewport.addEventListener('change', syncViewport);
    syncViewport();

    document.addEventListener('click', (event) => {
        const collapseBtn = event.target.closest('[data-sidebar-collapse]');
        if (collapseBtn) {
            collapsed = !collapsed;
            sidebar.setAttribute('data-collapsed', String(collapsed));
            collapseBtn.setAttribute('aria-expanded', String(!collapsed));
            savePreference('aura-sidebar-collapsed', String(collapsed));
            return;
        }

        const mobileBtn = event.target.closest('[data-sidebar-mobile-toggle]');
        if (mobileBtn) {
            setMobileOpen(sidebar.getAttribute('data-mobile-open') !== 'true', true);
        } else if (mobileViewport.matches && sidebar.getAttribute('data-mobile-open') === 'true' &&
            (event.target.closest('[data-sidebar-mobile-close]') || !sidebar.contains(event.target))) {
            setMobileOpen(false, true);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && mobileViewport.matches &&
            sidebar.getAttribute('data-mobile-open') === 'true' && !document.querySelector('dialog:modal')) {
            setMobileOpen(false, true);
        }
    });
}

function initKeyboardShortcuts() {
    document.addEventListener('keydown', (event) => {
        const target = event.target;
        const isTyping =
            target instanceof HTMLInputElement ||
            target instanceof HTMLTextAreaElement ||
            target instanceof HTMLSelectElement ||
            target?.isContentEditable;

        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            const dialog = document.getElementById('command-palette-dialog');
            if (dialog && !dialog.open) {
                dialog.showModal();
            }
            return;
        }

        if (!isTyping && event.key.toLowerCase() === 'c' && !event.metaKey && !event.ctrlKey) {
            const dialog = document.getElementById('quick-action-dialog');
            if (dialog && !dialog.open) {
                event.preventDefault();
                dialog.showModal();
            }
        }
    });
}

function initFragmentFilterButtons() {
    const pendingSelections = new Map();

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-fragment-nav]');
        if (!trigger || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        event.preventDefault();
        let url = trigger.getAttribute('data-fragment-nav') || trigger.getAttribute('href');
        const fragmentsRaw = trigger.getAttribute('data-target-fragments') || '';
        const fragments = fragmentsRaw
            .split(',')
            .map((item) => item.trim())
            .filter(Boolean);

        const group = trigger.closest('[role="group"], .crm-segmented');
        const target = document.querySelector(`crm-html-ajax[fragment="${CSS.escape(fragments[0] || '')}"]`);
        const destination = new URL(url, window.location.href);
        if (trigger.matches('a') && destination.searchParams.has('view') && target?.getAttribute('url')) {
            const current = new URL(target.getAttribute('url'), window.location.href);
            current.searchParams.set('view', destination.searchParams.get('view'));
            url = current.href;
        }

        const requests = [];
        const deferRender = fragments.length > 1;
        fragments.forEach((fragment) => {
            if (group) pendingSelections.set(fragment, { group, trigger, url });
            document.dispatchEvent(
                new CustomEvent('crm:fragment-query', {
                    detail: { fragment, url, deferRender, requests },
                })
            );
        });

        if (deferRender) {
            Promise.all(requests).then(async (results) => {
                await commitFragmentBatch(results, fragments.length);
                if (results.length !== fragments.length) {
                    window.dispatchEvent(new CustomEvent('crm:toast', {
                        detail: {
                            title: 'Error de actualización',
                            message: 'No se encontraron todos los fragmentos necesarios.',
                            variant: 'error',
                        },
                    }));
                }
            }).catch((error) => {
                window.dispatchEvent(new CustomEvent('crm:toast', {
                    detail: {
                        title: 'Error de actualización',
                        message: error.message || 'No se pudieron actualizar los fragmentos.',
                        variant: 'error',
                    },
                }));
            });
        }
    });

    document.addEventListener('crm:fragment-loaded', (event) => {
        const { fragment, url } = event.detail || {};
        if (fragment === 'companies-list') {
            document.querySelectorAll('a[data-fragment-nav][data-target-fragments="companies-list"]').forEach((link) => {
                const destination = new URL(link.href);
                const current = new URL(url, window.location.href);
                current.searchParams.set('view', destination.searchParams.get('view'));
                link.href = current.href;
                link.setAttribute('data-fragment-nav', current.href);
            });
        }
        const selection = pendingSelections.get(fragment);
        if (!selection || selection.url !== url) return;
        pendingSelections.delete(fragment);
        selection.group.querySelectorAll('[data-fragment-nav]').forEach((btn) => {
            const active = btn === selection.trigger;
            btn.classList.toggle('is-active', active);
            if (btn.matches('a')) {
                if (active) btn.setAttribute('aria-current', 'true');
                else btn.removeAttribute('aria-current');
            } else {
                btn.setAttribute('aria-pressed', active ? 'true' : 'false');
            }
        });
    });
}

function initPipelineViewToggle() {
    const showPipelineView = (targetView) => {
        const kanbanPanel = document.getElementById('deals-kanban-panel');
        const listPanel = document.getElementById('deals-list-panel');
        if (!kanbanPanel || !listPanel) return;

        const showList = targetView === 'list';
        kanbanPanel.classList.toggle('is-hidden', showList);
        listPanel.classList.toggle('is-hidden', !showList);

        document.querySelectorAll('[data-pipeline-view]').forEach((btn) => {
            const active = btn.getAttribute('data-pipeline-view') === targetView;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    };

    document.addEventListener('click', (event) => {
        const viewBtn = event.target.closest('[data-pipeline-view]');
        if (!viewBtn) return;

        transitionView(() => showPipelineView(viewBtn.getAttribute('data-pipeline-view')));
    });

    document.addEventListener('crm:fragment-loaded', (event) => {
        if (event.detail?.fragment !== 'deals-board') return;
        const activeView = document.querySelector('[data-pipeline-view][aria-pressed="true"]');
        if (activeView) showPipelineView(activeView.getAttribute('data-pipeline-view'));
    });
}

function initActivityTypeSelector() {
    const defaultActivityType = document.querySelector('[data-activity-type-input]')?.value;
    const updateSelection = (container, type) => {
        container.querySelectorAll('[data-activity-type-pill]').forEach((btn) => {
            const active = btn.getAttribute('data-activity-type-pill') === type;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    };

    document.addEventListener('click', (event) => {
        const pill = event.target.closest('[data-activity-type-pill]');
        if (!pill) return;

        const type = pill.getAttribute('data-activity-type-pill');
        const container = pill.closest('[data-activity-type-group]');
        const hiddenInput = document.querySelector('[data-activity-type-input]');
        if (!container || !type) return;

        updateSelection(container, type);

        if (hiddenInput) {
            hiddenInput.value = type;
        }
    });

    document.addEventListener('reset', (event) => {
        const hiddenInput = event.target.querySelector('[data-activity-type-input]');
        const container = document.querySelector('[data-activity-type-group]');
        if (!hiddenInput || !container) return;
        window.setTimeout(() => {
            hiddenInput.value = defaultActivityType;
            updateSelection(container, defaultActivityType);
        });
    });
}
