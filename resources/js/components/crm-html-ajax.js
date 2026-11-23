import { transitionView } from '../view-transitions.js';

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
            const request = this.reload(event.detail.url, {
                deferRender: Boolean(event.detail.deferRender),
                animate: true,
            });
            event.detail.requests?.push(request);
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
        this.reload(nextUrl, { animate: true });
    }

    async reload(overrideUrl = null, { deferRender = false, animate = false } = {}) {
        const url = overrideUrl || this.getAttribute('url') || window.location.href;
        const fragment = this.getAttribute('fragment') || '';

        if (this.abortController) {
            this.abortController.abort();
        }
        const controller = new AbortController();
        this.abortController = controller;

        this.setAttribute('aria-busy', 'true');
        let deferred = false;

        const finish = () => {
            if (this.abortController === controller) {
                this.removeAttribute('aria-busy');
                this.abortController = null;
            }
        };

        try {
            const response = await fetch(url, {
                method: 'GET',
                signal: controller.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Fragment-Request': 'true',
                    ...(fragment ? { 'X-Fragment-Name': fragment } : {}),
                    Accept: 'text/html',
                },
            });

            if (this.abortController !== controller || controller.signal.aborted) return;
            if (response.redirected) {
                window.location.assign(response.url);
                return;
            }

            if (!response.ok) {
                throw new Error('No se pudo actualizar el fragmento.');
            }

            const html = await response.text();
            if (this.abortController !== controller || controller.signal.aborted) return;
            const commit = () => {
                if (this.abortController !== controller || controller.signal.aborted) return;
                this.innerHTML = html;
                this.setAttribute('url', url);
                this.dispatchEvent(new CustomEvent('crm:fragment-loaded', {
                    bubbles: true,
                    detail: { fragment, url },
                }));
                finish();
            };

            if (deferRender) {
                deferred = true;
                return { commit, discard: finish, isCurrent: () => this.abortController === controller && !controller.signal.aborted };
            }
            if (animate) {
                await transitionView(commit);
            } else {
                commit();
            }
        } catch (error) {
            if (this.abortController === controller && !controller.signal.aborted && error.name !== 'AbortError') {
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
            if (!deferred) finish();
        }
    }
}

if (!customElements.get('crm-html-ajax')) {
    customElements.define('crm-html-ajax', CrmHtmlAjax);
}
