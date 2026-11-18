class CrmFormValidate extends HTMLElement {
    constructor() {
        super();
        this.dirtyState = new WeakMap();
        this.supportsUserInvalid = typeof CSS !== 'undefined' && CSS.supports('selector(:user-invalid)');

        this.handleBlur = this.handleBlur.bind(this);
        this.handleInput = this.handleInput.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleReset = this.handleReset.bind(this);
        this.handleServerErrors = this.handleServerErrors.bind(this);
    }

    connectedCallback() {
        this.form = this.querySelector('form');
        if (!this.form) return;

        this.form.noValidate = true;

        this.getControls().forEach((control) => {
            if (!control.hasAttribute('aria-invalid')) {
                control.setAttribute('aria-invalid', 'false');
            }
        });

        this.form.addEventListener('blur', this.handleBlur, true);
        this.form.addEventListener('input', this.handleInput);
        this.form.addEventListener('change', this.handleChange);
        this.form.addEventListener('submit', this.handleSubmit, true);
        this.form.addEventListener('reset', this.handleReset);
        this.form.addEventListener('crm:form-errors', this.handleServerErrors);
    }

    disconnectedCallback() {
        if (!this.form) return;
        this.form.removeEventListener('blur', this.handleBlur, true);
        this.form.removeEventListener('input', this.handleInput);
        this.form.removeEventListener('change', this.handleChange);
        this.form.removeEventListener('submit', this.handleSubmit, true);
        this.form.removeEventListener('reset', this.handleReset);
        this.form.removeEventListener('crm:form-errors', this.handleServerErrors);
    }

    getControls() {
        return Array.from(this.form.querySelectorAll('input, select, textarea')).filter(
            (el) => el.type !== 'hidden' && el.type !== 'submit' && el.type !== 'button'
        );
    }

    getErrorBox(control) {
        if (!control.name) return null;
        return this.form.querySelector(`[data-error-for="${CSS.escape(control.name)}"]`);
    }

    linkError(control, showError) {
        const errorBox = this.getErrorBox(control);
        const errorId = control.dataset.error || errorBox?.id;
        if (!errorId) return;

        const currentIds = (control.getAttribute('aria-describedby') || '')
            .split(/\s+/)
            .filter((id) => id && id !== errorId);

        if (showError) {
            currentIds.push(errorId);
        }

        if (currentIds.length > 0) {
            control.setAttribute('aria-describedby', currentIds.join(' '));
        } else {
            control.removeAttribute('aria-describedby');
        }
    }

    renderError(control, message) {
        const errorBox = this.getErrorBox(control);
        if (message) {
            control.setAttribute('aria-invalid', 'true');
            if (!this.supportsUserInvalid) {
                control.classList.add('user-invalid-fallback');
                control.classList.remove('user-valid-fallback');
            }
            if (errorBox) {
                errorBox.replaceChildren();
                const icon = document.createElement('span');
                icon.className = 'material-symbols-outlined';
                icon.setAttribute('aria-hidden', 'true');
                icon.style.fontSize = '14px';
                icon.textContent = 'error';

                const text = document.createElement('span');
                text.textContent = message;

                errorBox.appendChild(icon);
                errorBox.appendChild(text);
            }
            this.linkError(control, true);
        } else {
            control.setAttribute('aria-invalid', 'false');
            if (!this.supportsUserInvalid) {
                control.classList.remove('user-invalid-fallback');
                control.classList.add('user-valid-fallback');
            }
            if (errorBox) {
                errorBox.replaceChildren();
            }
            this.linkError(control, false);
        }
    }

    validateControl(control, force = false) {
        if (!control.checkValidity) return true;

        const state = this.dirtyState.get(control) || { interacted: false, blurred: false };
        if (!force && !state.interacted && !state.blurred) {
            return control.validity.valid;
        }

        control.setCustomValidity('');

        if (!control.validity.valid) {
            const customMsg = control.getAttribute('validate-msg');
            const message = customMsg || control.validationMessage || 'Campo inválido.';
            this.renderError(control, message);
            return false;
        }

        this.renderError(control, '');
        return true;
    }

    handleBlur(event) {
        const control = event.target;
        if (!control.matches('input, select, textarea')) return;

        const state = this.dirtyState.get(control) || { interacted: false, blurred: false };
        state.blurred = true;
        this.dirtyState.set(control, state);

        if (state.interacted || control.value.length > 0) {
            this.validateControl(control, true);
        }
    }

    handleInput(event) {
        const control = event.target;
        if (!control.matches('input, select, textarea')) return;

        const state = this.dirtyState.get(control) || { interacted: false, blurred: false };
        state.interacted = true;
        this.dirtyState.set(control, state);

        if (control.getAttribute('aria-invalid') === 'true' || state.blurred) {
            this.validateControl(control, true);
        }
    }

    handleChange(event) {
        const control = event.target;
        if (!control.matches('input, select, textarea')) return;

        const state = this.dirtyState.get(control) || { interacted: false, blurred: false };
        state.interacted = true;
        state.blurred = true;
        this.dirtyState.set(control, state);

        this.validateControl(control, true);
    }

    handleSubmit(event) {
        let firstInvalid = null;

        for (const control of this.getControls()) {
            this.dirtyState.set(control, { interacted: true, blurred: true });
            const isValid = this.validateControl(control, true);
            if (!isValid && !firstInvalid) {
                firstInvalid = control;
            }
        }

        if (firstInvalid) {
            event.preventDefault();
            event.stopImmediatePropagation();
            firstInvalid.focus();
        }
    }

    handleServerErrors(event) {
        const errors = event.detail?.errors || {};
        let firstInvalid = null;

        for (const [field, messages] of Object.entries(errors)) {
            const control = this.form.querySelector(`[name="${CSS.escape(field)}"]`);
            const msg = Array.isArray(messages) ? messages[0] : String(messages);

            if (control) {
                this.dirtyState.set(control, { interacted: true, blurred: true });
                this.renderError(control, msg);
                if (!firstInvalid) {
                    firstInvalid = control;
                }
            } else {
                const errorBox = this.form.querySelector(`[data-error-for="${CSS.escape(field)}"]`);
                if (errorBox) {
                    errorBox.textContent = msg;
                }
            }
        }

        if (firstInvalid) {
            firstInvalid.focus();
        }
    }

    handleReset() {
        window.setTimeout(() => {
            this.getControls().forEach((control) => {
                this.dirtyState.delete(control);
                control.setCustomValidity('');
                control.setAttribute('aria-invalid', 'false');
                control.classList.remove('user-invalid-fallback', 'user-valid-fallback');
                const errorBox = this.getErrorBox(control);
                if (errorBox) {
                    errorBox.replaceChildren();
                }
                this.linkError(control, false);
            });
        });
    }
}

if (!customElements.get('crm-form-validate')) {
    customElements.define('crm-form-validate', CrmFormValidate);
}
