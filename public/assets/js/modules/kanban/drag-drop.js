const KanbanDragDrop = {
    draggedCard: null,

    init() {
        this.bindEvents();
    },

    bindEvents() {
        const cards = document.querySelectorAll('.kanban-card');
        const columns = document.querySelectorAll('.kanban-column-body');

        cards.forEach(card => {
            card.addEventListener('dragstart', (e) => this.handleDragStart(e));
            card.addEventListener('dragend', (e) => this.handleDragEnd(e));
        });

        columns.forEach(column => {
            column.addEventListener('dragover', (e) => this.handleDragOver(e));
            column.addEventListener('drop', (e) => this.handleDrop(e));
            column.addEventListener('dragenter', (e) => this.handleDragEnter(e));
            column.addEventListener('dragleave', (e) => this.handleDragLeave(e));
        });
    },

    handleDragStart(e) {
        this.draggedCard = e.target;
        e.target.classList.add('dragging');

        e.dataTransfer.setData('text/plain', JSON.stringify({
            id: e.target.dataset.id,
            currentStatus: e.target.dataset.status
        }));
    },

    handleDragEnd(e) {
        e.target.classList.remove('dragging');
        this.clearDragStates();
        this.draggedCard = null;
    },

    handleDragOver(e) {
        e.preventDefault();
        e.currentTarget.classList.add('drag-over');
    },

    handleDragEnter(e) {
        e.preventDefault();
    },

    handleDragLeave(e) {
        if (!e.currentTarget.contains(e.relatedTarget)) {
            e.currentTarget.classList.remove('drag-over');
        }
    },

    async handleDrop(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over');

        if (!this.draggedCard) {
            console.error('draggedCard es null en handleDrop');
            return;
        }

        const currentCard = this.draggedCard;
        const targetColumn = e.currentTarget;
        const cardData = JSON.parse(e.dataTransfer.getData('text/plain'));
        const newStatus = targetColumn.dataset.estado;
        const currentStatus = cardData.currentStatus;

        if (currentStatus === newStatus) return;

        // Validación adicional: Si es trabajador, verificar que la tarjeta le pertenece
        if (typeof ES_TRABAJADOR !== 'undefined' && ES_TRABAJADOR && typeof USUARIO_ACTUAL_ID !== 'undefined') {
            const cardUsuarioId = parseInt(currentCard.dataset.usuarioId);
            if (cardUsuarioId !== USUARIO_ACTUAL_ID) {
                Swal.fire({
                    title: 'Acceso Denegado',
                    text: 'Solo puedes modificar tus propias asignaciones',
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }
        }

        const validation = KanbanValidation.validateTransition(currentStatus, newStatus);
        if (!validation.valido) {
            this.showValidationError(validation.mensaje);
            return;
        }

        const confirmed = await this.confirmStateChange(cardData.id, currentStatus, newStatus);
        if (!confirmed) return;

        const success = await this.updateCardStatus(cardData.id, newStatus);
        if (success) {
            this.moveCardToColumn(currentCard, targetColumn, newStatus);
            KanbanUI.showNotification(`Estado cambiado a "${newStatus}" correctamente`, 'success');
        } else {
            KanbanUI.showNotification('Error al actualizar el estado', 'error');
        }
    },

    clearDragStates() {
        document.querySelectorAll('.kanban-column-body').forEach(col => {
            col.classList.remove('drag-over');
        });
    },

    showValidationError(mensaje) {
        Swal.fire({
            title: 'Movimiento no permitido',
            text: mensaje,
            icon: 'warning',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#000000ff'
        });
    },

    async confirmStateChange(cardId, currentStatus, newStatus) {
        const result = await Swal.fire({
            title: 'Cambiar Estado',
            text: `¿Cambiar de "${currentStatus}" a "${newStatus}"?`,
            icon: KanbanUI.getStatusIcon(newStatus),
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#000000ff'
        });

        return result.isConfirmed;
    },

    async updateCardStatus(cardId, newStatus) {
        try {
            Swal.fire({
                title: 'Actualizando...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            const endpoint = (typeof ENDPOINT_ACTUALIZAR_ESTADO !== 'undefined' && ENDPOINT_ACTUALIZAR_ESTADO)
                ? ENDPOINT_ACTUALIZAR_ESTADO
                : (BASE_URL.endsWith('/') ? BASE_URL : BASE_URL + '/') + 'equipos/actualizar-estado';

            console.debug('[Kanban] POST actualizar-estado →', endpoint, { id: cardId, estado: newStatus });

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id: cardId, estado: newStatus })
            });

            if (!response.ok) {
                console.error('[Kanban] Error HTTP en actualizar-estado', {
                    endpoint,
                    status: response.status,
                    statusText: response.statusText
                });
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            Swal.close();

            if (!data.success && data.message) {
                KanbanUI.showNotification(data.message, 'error');
            }

            return data.success;

        } catch (error) {
            Swal.close();
            console.error('[Kanban] Excepción en updateCardStatus:', error);
            KanbanUI.showNotification('Error de conexión', 'error');
            return false;
        }
    },

    moveCardToColumn(card, newColumn, newStatus) {
        if (!card || !newColumn || !newStatus) {
            console.error('moveCardToColumn: Parámetros inválidos');
            return;
        }

        card.dataset.status = newStatus;
        this.updateCardBadge(card, newStatus);
        newColumn.appendChild(card);
        KanbanUI.updateColumnCounters();
        KanbanSearch.updateEmptyStates();
    },

    updateCardBadge(card, newStatus) {
        const badge = card.querySelector('.badge');
        if (badge) {
            badge.className = `badge bg-${KanbanUI.getStatusColor(newStatus)} text-white`;
            badge.innerHTML = `<i class="${KanbanUI.getStatusFontAwesome(newStatus)} me-1"></i>${newStatus}`;
        }
    }
};