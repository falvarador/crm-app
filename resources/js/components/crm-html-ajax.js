class CrmHtmlAjax extends HTMLElement {
    constructor() {
        super();
        this.handleRefreshEvent = this.handleRefreshEvent.bind(this);
        this.handleFilterInteraction = this.handleFilterInteraction.bind(this);
        this.handleDocumentFilter = this.handleDocumentFilter.bind(this);
        this.abortController = null;
    }

    connectedCallback() {
        this.boundEvents = this.getListenEvents();
        this.boundEvents.forEach((eventName) => {
            window.addEventListener(eventName, this.handleRefreshEvent);
        });

        this.addEventListener('change', this.handleFilterInteraction);
        this.addEventListener('submit', this.handleFilterInteraction);
        document.addEventListener('crm:fragment-query', this.handleDocumentFilter);
    }

    disconnectedCallback() {
        (this.boundEvents || []).forEach((eventName) => {
            window.removeEventListener(eventName, this.handleRefreshEvent);
        });
        this.removeEventListener('change', this.handleFilterInteraction);
        this.removeEventListener('submit', this.handleFilterInteraction);
        document.removeEventListener('crm:fragment-query', this.handleDocumentFilter);

        if (this.abortController) {
            this.abortController.abort();
        }
    }

    getListenEvents() {
        const raw = this.getAttribute('listen-events') || this.getAttribute('event-keys') || '';
        return raw
            .split(',')
            .map((evt) => evt.trim())
            .filter(Boolean);
    }

    handleRefreshEvent(event) {
        const customUrl = event.detail?.url || null;
        this.reload(customUrl);
    }

    handleDocumentFilter(event) {
        const targetFragment = event.detail?.fragment;
        if (targetFragment && targetFragment !== this.getAttribute('fragment')) {
            return;
        }
        if (event.detail?.url) {
            this.setAttribute('url', event.detail.url);
            this.reload(event.detail.url);
        }
    }

    handleFilterInteraction(event) {
        const form = event.target.closest('form[data-ajax-filter]');
        if (!form || !this.contains(form)) return;

        if (event.type === 'submit') {
            event.preventDefault();
        }

        const params = new URLSearchParams(new FormData(form));
        const baseUrl = (form.getAttribute('action') || this.getAttribute('url') || window.location.pathname).split('?')[0];
        const nextUrl = `${baseUrl}?${params.toString()}`;
        this.setAttribute('url', nextUrl);
        this.reload(nextUrl);
    }

    async reload(overrideUrl = null) {
        const url = overrideUrl || this.getAttribute('url') || window.location.href;
        const fragment = this.getAttribute('fragment') || '';

        if (this.abortController) {
            this.abortController.abort();
        }
        this.abortController = new AbortController();

        this.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(url, {
                method: 'GET',
                signal: this.abortController.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Fragment-Request': 'true',
                    ...(fragment ? { 'X-Fragment-Name': fragment } : {}),
                    Accept: 'text/html',
                },
            });

            if (response.redirected) {
                window.location.assign(response.url);
                return;
            }

            if (!response.ok) {
                throw new Error('No se pudo actualizar el fragmento.');
            }

            const html = await response.text();
            this.innerHTML = html;
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.dispatchEvent(
                    new CustomEvent('crm:toast', {
                        detail: {
                            title: 'Error de actualización',
                            message: error.message || 'Error al recargar los datos.',
                            variant: 'error',
                        },
                    })
                );
            }
        } finally {
            this.removeAttribute('aria-busy');
        }
    }
}

if (!customElements.get('crm-html-ajax')) {
    customElements.define('crm-html-ajax', CrmHtmlAjax);
}
