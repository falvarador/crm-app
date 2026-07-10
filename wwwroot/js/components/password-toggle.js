class PasswordToggle extends HTMLElement {
    connectedCallback() {
        this._input  = this.querySelector('input[type="password"], input[type="text"]');
        this._button = this.querySelector('button[aria-label="Show password"]');
        this._icon   = this._button?.querySelector('.bi');

        this._button?.addEventListener('click', () => this._toggle());
    }

    disconnectedCallback() {
        this._button?.removeEventListener('click', () => this._toggle());
    }

    _toggle() {
        const isShowing = this._input.type === 'text';

        this._input.type            = isShowing ? 'password' : 'text';
        this._icon.className        = `bi ${isShowing ? 'bi-eye' : 'bi-eye-slash'}`;
        this._button.setAttribute('aria-pressed', String(!isShowing));
        this._button.setAttribute('aria-label',   isShowing ? 'Show password' : 'Hide password');
    }
}

customElements.define('password-toggle', PasswordToggle);