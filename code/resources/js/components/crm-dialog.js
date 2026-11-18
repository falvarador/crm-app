class CrmDialog extends HTMLElement {
    constructor() {
        super();
        this.handleDocumentClick = this.handleDocumentClick.bind(this);
        this.handleDialogClose = this.handleDialogClose.bind(this);
        this.pressedOutside = false;
    }

    connectedCallback() {
        this.dialog = this.querySelector('dialog');
        if (!this.dialog) return;

        document.addEventListener('click', this.handleDocumentClick);
        this.dialog.addEventListener('close', this.handleDialogClose);

        // Fallback for browsers without native closedby="any" support
        if (!('closedBy' in HTMLDialogElement.prototype) && this.dialog.getAttribute('closedby') === 'any') {
            const isOutside = (event) => {
                const rect = this.dialog.getBoundingClientRect();
                return (
                    event.clientX < rect.left ||
                    event.clientX > rect.right ||
                    event.clientY < rect.top ||
                    event.clientY > rect.bottom
                );
            };

            this.dialog.addEventListener('pointerdown', (event) => {
                this.pressedOutside = isOutside(event);
            });

            this.dialog.addEventListener('pointerup', (event) => {
                if (this.pressedOutside && isOutside(event)) {
                    this.close();
                }
                this.pressedOutside = false;
            });
        }
    }

    disconnectedCallback() {
        document.removeEventListener('click', this.handleDocumentClick);
        if (this.dialog) {
            this.dialog.removeEventListener('close', this.handleDialogClose);
        }
    }

    handleDocumentClick(event) {
        if (!this.dialog) return;

        const openTrigger = event.target.closest('[data-dialog-open], [command="show-modal"]');
        if (openTrigger) {
            const targetId =
                openTrigger.getAttribute('data-dialog-open') ||
                openTrigger.getAttribute('commandfor');

            if (targetId && targetId === this.dialog.id) {
                event.preventDefault();
                this.open();
                return;
            }
        }

        const closeTrigger = event.target.closest('[data-dialog-close], [command="close"]');
        if (closeTrigger && this.contains(closeTrigger)) {
            event.preventDefault();
            this.close();
        }
    }

    handleDialogClose() {
        if (this.dialog) {
            this.dialog.returnValue = '';
        }
    }

    open() {
        if (this.dialog && !this.dialog.open) {
            this.dialog.showModal();
        }
    }

    close() {
        if (this.dialog && this.dialog.open) {
            this.dialog.close();
        }
    }
}

if (!customElements.get('crm-dialog')) {
    customElements.define('crm-dialog', CrmDialog);
}
