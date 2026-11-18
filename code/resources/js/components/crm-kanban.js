class CrmKanban extends HTMLElement {
    constructor() {
        super();
        this.draggedCard = null;
        this.handleDragStart = this.handleDragStart.bind(this);
        this.handleDragEnd = this.handleDragEnd.bind(this);
        this.handleDragOver = this.handleDragOver.bind(this);
        this.handleDragLeave = this.handleDragLeave.bind(this);
        this.handleDrop = this.handleDrop.bind(this);
        this.handleStageSelect = this.handleStageSelect.bind(this);
    }

    connectedCallback() {
        this.addEventListener('dragstart', this.handleDragStart);
        this.addEventListener('dragend', this.handleDragEnd);
        this.addEventListener('dragover', this.handleDragOver);
        this.addEventListener('dragleave', this.handleDragLeave);
        this.addEventListener('drop', this.handleDrop);
        this.addEventListener('change', this.handleStageSelect);
    }

    disconnectedCallback() {
        this.removeEventListener('dragstart', this.handleDragStart);
        this.removeEventListener('dragend', this.handleDragEnd);
        this.removeEventListener('dragover', this.handleDragOver);
        this.removeEventListener('dragleave', this.handleDragLeave);
        this.removeEventListener('drop', this.handleDrop);
        this.removeEventListener('change', this.handleStageSelect);
    }

    handleDragStart(event) {
        const card = event.target.closest('[data-deal-id]');
        if (!card) return;

        this.draggedCard = card;
        card.setAttribute('data-dragging', 'true');
        if (event.dataTransfer) {
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', card.getAttribute('data-deal-id') || '');
        }
    }

    handleDragEnd(event) {
        const card = event.target.closest('[data-deal-id]');
        if (card) {
            card.removeAttribute('data-dragging');
        }
        this.querySelectorAll('[data-drop-target="true"]').forEach((col) => {
            col.removeAttribute('data-drop-target');
        });
        this.draggedCard = null;
    }

    handleDragOver(event) {
        const column = event.target.closest('[data-stage]');
        if (!column || !this.draggedCard) return;

        event.preventDefault();
        if (event.dataTransfer) {
            event.dataTransfer.dropEffect = 'move';
        }
        column.setAttribute('data-drop-target', 'true');
    }

    handleDragLeave(event) {
        const column = event.target.closest('[data-stage]');
        if (!column) return;

        if (!column.contains(event.relatedTarget)) {
            column.removeAttribute('data-drop-target');
        }
    }

    handleDrop(event) {
        const column = event.target.closest('[data-stage]');
        if (!column || !this.draggedCard) return;

        event.preventDefault();
        column.removeAttribute('data-drop-target');

        const dealId = this.draggedCard.getAttribute('data-deal-id');
        const targetStage = column.getAttribute('data-stage');
        const currentStage = this.draggedCard.getAttribute('data-current-stage');

        if (!dealId || !targetStage || targetStage === currentStage) {
            return;
        }

        this.moveCard(this.draggedCard, column, targetStage);
    }

    handleStageSelect(event) {
        const select = event.target.closest('[data-stage-select]');
        if (!select) return;

        const card = select.closest('[data-deal-id]');
        const targetStage = select.value;
        const targetColumn = this.querySelector(`[data-stage="${CSS.escape(targetStage)}"]`);
        if (!card || !targetColumn) return;

        this.moveCard(card, targetColumn, targetStage);
    }

    async moveCard(card, targetColumn, targetStage) {
        const dealId = card.getAttribute('data-deal-id');
        const previousColumn = card.closest('[data-stage]');
        const previousStage = card.getAttribute('data-current-stage');
        const cardsContainer = targetColumn.querySelector('[data-kanban-cards]');
        const previousContainer = previousColumn?.querySelector('[data-kanban-cards]');

        if (!cardsContainer || !dealId) return;

        // Optimistic UI update
        cardsContainer.prepend(card);
        card.setAttribute('data-current-stage', targetStage);
        const stageSelect = card.querySelector('[data-stage-select]');
        if (stageSelect) {
            stageSelect.value = targetStage;
        }
        this.recalculateColumns();

        const template = this.getAttribute('endpoint-template') || '/deals/{id}/stage';
        const endpoint = template.replace('{id}', encodeURIComponent(dealId));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        try {
            const response = await fetch(endpoint, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                },
                body: JSON.stringify({ stage: targetStage }),
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload.message || 'No se pudo mover la oportunidad.');
            }

            const eventKeys = (this.getAttribute('event-keys') || 'deals:updated,kpi:updated')
                .split(',')
                .map((key) => key.trim())
                .filter(Boolean);

            eventKeys.forEach((key) => {
                window.dispatchEvent(new CustomEvent(key, { detail: payload }));
            });

            window.dispatchEvent(
                new CustomEvent('crm:toast', {
                    detail: {
                        title: 'Pipeline actualizado',
                        message: payload.message || 'La etapa de la oportunidad ha sido actualizada.',
                        variant: 'success',
                    },
                })
            );
        } catch (error) {
            // Rollback on failure
            if (previousContainer && previousStage) {
                previousContainer.prepend(card);
                card.setAttribute('data-current-stage', previousStage);
                if (stageSelect) {
                    stageSelect.value = previousStage;
                }
                this.recalculateColumns();
            }

            window.dispatchEvent(
                new CustomEvent('crm:toast', {
                    detail: {
                        title: 'Error al mover tarjeta',
                        message: error.message || 'No se pudo guardar el cambio de etapa.',
                        variant: 'error',
                    },
                })
            );
        }
    }

    recalculateColumns() {
        this.querySelectorAll('[data-stage]').forEach((column) => {
            const cards = Array.from(column.querySelectorAll('[data-deal-id]'));
            const countEl = column.querySelector('[data-stage-count]');
            const subtotalEl = column.querySelector('[data-stage-subtotal]');

            if (countEl) {
                countEl.textContent = `${cards.length} deals`;
            }

            if (subtotalEl) {
                const sum = cards.reduce((acc, card) => {
                    const amount = Number(card.getAttribute('data-amount') || 0);
                    return acc + (Number.isFinite(amount) ? amount : 0);
                }, 0);
                subtotalEl.textContent = `$${sum.toLocaleString('en-US')} USD`;
            }
        });
    }
}

if (!customElements.get('crm-kanban')) {
    customElements.define('crm-kanban', CrmKanban);
}
