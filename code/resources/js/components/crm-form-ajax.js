class CrmFormAjax extends HTMLElement {
    constructor() {
        super();
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    connectedCallback() {
        this.form = this.querySelector('form');
        if (!this.form) return;

        this.form.addEventListener('submit', this.handleSubmit);
    }

    disconnectedCallback() {
        if (this.form) {
            this.form.removeEventListener('submit', this.handleSubmit);
        }
    }

    async handleSubmit(event) {
        event.preventDefault();
        if (!this.form) return;

        if (this.form.getAttribute('aria-busy') === 'true') {
            return;
        }

        const submitButtons = Array.from(this.form.querySelectorAll('button[type="submit"], input[type="submit"]'));
        const csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
            this.form.querySelector('input[name="_token"]')?.value ||
            '';

        this.form.setAttribute('aria-busy', 'true');
        submitButtons.forEach((btn) => {
            btn.disabled = true;
        });

        try {
            const rawMethod = (this.form.getAttribute('method') || 'POST').toUpperCase();
            const formData = new FormData(this.form);
            const method = rawMethod === 'GET' ? 'GET' : 'POST';

            if (rawMethod !== 'GET' && rawMethod !== 'POST' && !formData.has('_method')) {
                formData.set('_method', rawMethod);
            }

            let targetUrl = this.form.getAttribute('action') || window.location.href;
            const fetchOptions = {
                method,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                },
            };

            if (method === 'GET') {
                const params = new URLSearchParams(formData);
                targetUrl += (targetUrl.includes('?') ? '&' : '?') + params.toString();
            } else {
                fetchOptions.body = formData;
            }

            const response = await fetch(targetUrl, fetchOptions);
            const contentType = response.headers.get('content-type') || '';
            const payload = contentType.includes('application/json') ? await response.json() : {};

            if (response.status === 422) {
                this.form.dispatchEvent(
                    new CustomEvent('crm:form-errors', {
                        bubbles: false,
                        detail: { errors: payload.errors || {} },
                    })
                );

                window.dispatchEvent(
                    new CustomEvent('crm:toast', {
                        detail: {
                            title: 'Revisa los datos ingresados',
                            message: payload.message || 'Hay campos que requieren corrección.',
                            variant: 'error',
                        },
                    })
                );
                return;
            }

            if (!response.ok) {
                throw new Error(payload.message || 'Ocurrió un error al procesar la solicitud.');
            }

            if (this.hasAttribute('reset-on-success')) {
                this.form.reset();
            }

            const shouldCloseDialog = this.getAttribute('close-dialog-on-success') !== 'false';
            if (shouldCloseDialog) {
                const parentDialog = this.closest('dialog');
                if (parentDialog?.open) {
                    parentDialog.close();
                }
            }

            const eventKeys = (this.getAttribute('event-keys') || '')
                .split(',')
                .map((key) => key.trim())
                .filter(Boolean);

            eventKeys.forEach((key) => {
                window.dispatchEvent(new CustomEvent(key, { detail: payload }));
            });

            if (payload.message) {
                window.dispatchEvent(
                    new CustomEvent('crm:toast', {
                        detail: {
                            title: 'Operación completada',
                            message: payload.message,
                            variant: 'success',
                        },
                    })
                );
            }
        } catch (error) {
            window.dispatchEvent(
                new CustomEvent('crm:toast', {
                    detail: {
                        title: 'Error de conexión',
                        message: error.message || 'No se pudo completar la operación.',
                        variant: 'error',
                    },
                })
            );
        } finally {
            this.form.removeAttribute('aria-busy');
            submitButtons.forEach((btn) => {
                btn.disabled = false;
            });
        }
    }
}

if (!customElements.get('crm-form-ajax')) {
    customElements.define('crm-form-ajax', CrmFormAjax);
}
