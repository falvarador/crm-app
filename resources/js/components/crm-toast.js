class CrmToast extends HTMLElement {
    constructor() {
        super();
        this.handleToastEvent = this.handleToastEvent.bind(this);
    }

    connectedCallback() {
        if (!this.querySelector('.crm-toast-stack')) {
            const stack = document.createElement('div');
            stack.className = 'crm-toast-stack';
            stack.setAttribute('aria-live', 'polite');
            stack.setAttribute('aria-atomic', 'false');
            this.appendChild(stack);
        }

        window.addEventListener('crm:toast', this.handleToastEvent);
        window.crmToast = (detail) => {
            window.dispatchEvent(new CustomEvent('crm:toast', { detail }));
        };
    }

    disconnectedCallback() {
        window.removeEventListener('crm:toast', this.handleToastEvent);
    }

    handleToastEvent(event) {
        const detail = event.detail || {};
        this.show({
            title: detail.title || 'Notificación',
            message: detail.message || '',
            variant: detail.variant || 'info',
            duration: typeof detail.duration === 'number' ? detail.duration : 4500,
        });
    }

    show({ title, message, variant = 'info', duration = 4500 }) {
        const stack = this.querySelector('.crm-toast-stack');
        if (!stack) return;

        const iconMap = {
            success: 'check_circle',
            error: 'error',
            info: 'info',
        };

        const toast = document.createElement('div');
        toast.className = `crm-toast crm-toast--${variant}`;
        toast.setAttribute('role', 'status');

        const icon = document.createElement('span');
        icon.className = 'material-symbols-outlined';
        icon.setAttribute('aria-hidden', 'true');
        icon.textContent = iconMap[variant] || 'info';

        const body = document.createElement('div');
        body.className = 'crm-toast__body';

        const titleEl = document.createElement('span');
        titleEl.className = 'crm-toast__title';
        titleEl.textContent = title;
        body.appendChild(titleEl);

        if (message) {
            const messageEl = document.createElement('span');
            messageEl.className = 'crm-toast__message';
            messageEl.textContent = message;
            body.appendChild(messageEl);
        }

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'crm-toast__close';
        closeBtn.setAttribute('aria-label', 'Cerrar notificación');

        const closeIcon = document.createElement('span');
        closeIcon.className = 'material-symbols-outlined';
        closeIcon.setAttribute('aria-hidden', 'true');
        closeIcon.textContent = 'close';
        closeBtn.appendChild(closeIcon);

        const dismiss = () => {
            toast.remove();
        };

        closeBtn.addEventListener('click', dismiss);

        toast.appendChild(icon);
        toast.appendChild(body);
        toast.appendChild(closeBtn);
        stack.appendChild(toast);

        if (duration > 0) {
            window.setTimeout(dismiss, duration);
        }
    }
}

if (!customElements.get('crm-toast')) {
    customElements.define('crm-toast', CrmToast);
}
