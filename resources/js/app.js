import './components/crm-toast';
import './components/crm-dialog';
import './components/crm-form-validate';
import './components/crm-form-ajax';
import './components/crm-html-ajax';
import './components/crm-kanban';

document.addEventListener('DOMContentLoaded', () => {
    initThemeToggle();
    initSidebarToggle();
    initKeyboardShortcuts();
    initFragmentFilterButtons();
    initPipelineViewToggle();
    initActivityTypeSelector();
});

function initThemeToggle() {
    const root = document.documentElement;
    const savedTheme = window.localStorage.getItem('aura-theme');
    if (savedTheme === 'light' || savedTheme === 'dark') {
        root.setAttribute('data-theme', savedTheme);
    }

    document.addEventListener('click', (event) => {
        const themeBtn = event.target.closest('[data-theme-set]');
        if (!themeBtn) return;

        const mode = themeBtn.getAttribute('data-theme-set');
        if (mode === 'light' || mode === 'dark') {
            root.setAttribute('data-theme', mode);
            window.localStorage.setItem('aura-theme', mode);
            updateThemeButtons(mode);
        }
    });

    updateThemeButtons(root.getAttribute('data-theme') || 'light');
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

    const savedCollapsed = window.localStorage.getItem('aura-sidebar-collapsed') === 'true';
    if (savedCollapsed) {
        sidebar.setAttribute('data-collapsed', 'true');
    }

    document.addEventListener('click', (event) => {
        const collapseBtn = event.target.closest('[data-sidebar-collapse]');
        if (collapseBtn) {
            const next = sidebar.getAttribute('data-collapsed') !== 'true';
            sidebar.setAttribute('data-collapsed', next ? 'true' : 'false');
            collapseBtn.setAttribute('aria-expanded', next ? 'false' : 'true');
            window.localStorage.setItem('aura-sidebar-collapsed', String(next));
            return;
        }

        const mobileBtn = event.target.closest('[data-sidebar-mobile-toggle]');
        if (mobileBtn) {
            const next = sidebar.getAttribute('data-mobile-open') !== 'true';
            sidebar.setAttribute('data-mobile-open', next ? 'true' : 'false');
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
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-fragment-nav]');
        if (!trigger) return;

        event.preventDefault();
        const url = trigger.getAttribute('data-fragment-nav') || trigger.getAttribute('href');
        const fragmentsRaw = trigger.getAttribute('data-target-fragments') || '';
        const fragments = fragmentsRaw
            .split(',')
            .map((item) => item.trim())
            .filter(Boolean);

        const group = trigger.closest('[role="tablist"], .crm-segmented');
        if (group) {
            group.querySelectorAll('[data-fragment-nav]').forEach((btn) => {
                btn.classList.remove('is-active');
                btn.setAttribute('aria-pressed', 'false');
            });
            trigger.classList.add('is-active');
            trigger.setAttribute('aria-pressed', 'true');
        }

        fragments.forEach((fragment) => {
            document.dispatchEvent(
                new CustomEvent('crm:fragment-query', {
                    detail: { fragment, url },
                })
            );
        });
    });
}

function initPipelineViewToggle() {
    document.addEventListener('click', (event) => {
        const viewBtn = event.target.closest('[data-pipeline-view]');
        if (!viewBtn) return;

        const targetView = viewBtn.getAttribute('data-pipeline-view');
        const kanbanPanel = document.getElementById('deals-kanban-panel');
        const listPanel = document.getElementById('deals-list-panel');
        if (!kanbanPanel || !listPanel) return;

        const showList = targetView === 'list';
        kanbanPanel.classList.toggle('is-hidden', showList);
        listPanel.classList.toggle('is-hidden', !showList);

        document.querySelectorAll('[data-pipeline-view]').forEach((btn) => {
            const active = btn.getAttribute('data-pipeline-view') === targetView;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    });
}

function initActivityTypeSelector() {
    document.addEventListener('click', (event) => {
        const pill = event.target.closest('[data-activity-type-pill]');
        if (!pill) return;

        const type = pill.getAttribute('data-activity-type-pill');
        const container = pill.closest('[data-activity-type-group]');
        const hiddenInput = document.querySelector('[data-activity-type-input]');
        if (!container || !type) return;

        container.querySelectorAll('[data-activity-type-pill]').forEach((btn) => {
            btn.classList.remove('is-active');
            btn.setAttribute('aria-pressed', 'false');
        });

        pill.classList.add('is-active');
        pill.setAttribute('aria-pressed', 'true');

        if (hiddenInput) {
            hiddenInput.value = type;
        }
    });
}
