const KanbanSearch = {
    init() {
        this.bindEvents();
    },

    bindEvents() {
        const searchInput = document.getElementById('searchInput');
        const filterTecnico = document.getElementById('filterTecnico');
        const filterVencimiento = document.getElementById('filterVencimiento');
        const btnReset = document.getElementById('btnResetFilters');

        if (searchInput) searchInput.addEventListener('input', () => this.applyFilters());
        if (filterTecnico) filterTecnico.addEventListener('change', () => this.applyFilters());
        if (filterVencimiento) filterVencimiento.addEventListener('change', () => this.applyFilters());
        if (btnReset) btnReset.addEventListener('click', () => this.resetFilters());
    },

    applyFilters() {
        const filters = this.getFilterValues();
        const cards = document.querySelectorAll('.kanban-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const shouldShow = this.shouldShowCard(card, filters);
            card.style.display = shouldShow ? '' : 'none';
            if (shouldShow) visibleCount++;
        });

        this.updateSearchResults(visibleCount, cards.length);
        this.updateColumnCounters();
        this.updateEmptyStates();
    },

    getFilterValues() {
        return {
            searchTerm: document.getElementById('searchInput').value.toLowerCase(),
            tecnicoId: document.getElementById('filterTecnico').value,
            vencimiento: document.getElementById('filterVencimiento').value
        };
    },

    shouldShowCard(card, filters) {
        if (!this.matchesSearchTerm(card, filters.searchTerm)) return false;
        if (!this.matchesTecnico(card, filters.tecnicoId)) return false;
        if (!this.matchesVencimiento(card, filters.vencimiento)) return false;
        return true;
    },

    matchesSearchTerm(card, searchTerm) {
        if (!searchTerm) return true;

        const cliente = card.dataset.cliente || '';
        const servicio = card.dataset.servicio || '';
        const tecnico = card.dataset.tecnico || '';
        const searchableText = `${cliente} ${servicio} ${tecnico}`;

        return searchableText.includes(searchTerm);
    },

    matchesTecnico(card, tecnicoId) {
        if (!tecnicoId) return true;
        return card.dataset.usuarioId === tecnicoId;
    },

    matchesVencimiento(card, vencimiento) {
        if (!vencimiento) return true;

        const fechaCard = new Date(card.dataset.fecha);
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        switch (vencimiento) {
            case 'hoy':
                return this.isToday(fechaCard, hoy);
            case 'semana':
                return this.isThisWeek(fechaCard, hoy);
            case 'mes':
                return this.isThisMonth(fechaCard, hoy);
            case 'vencidos':
                return fechaCard < hoy;
            default:
                return true;
        }
    },

    isToday(fecha, hoy) {
        const manana = new Date(hoy);
        manana.setDate(manana.getDate() + 1);
        return fecha >= hoy && fecha < manana;
    },

    isThisWeek(fecha, hoy) {
        const finSemana = new Date(hoy);
        finSemana.setDate(finSemana.getDate() + 7);
        return fecha >= hoy && fecha <= finSemana;
    },

    isThisMonth(fecha, hoy) {
        const finMes = new Date(hoy);
        finMes.setMonth(finMes.getMonth() + 1);
        return fecha >= hoy && fecha <= finMes;
    },

    resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterTecnico').value = '';
        document.getElementById('filterVencimiento').value = '';

        document.querySelectorAll('.kanban-card').forEach(card => {
            card.style.display = '';
        });

        document.getElementById('searchResults').classList.add('d-none');
        this.updateColumnCounters();
        this.updateEmptyStates();
    },

    updateColumnCounters() {
        document.querySelectorAll('.kanban-column').forEach(column => {
            const body = column.querySelector('.kanban-column-body');
            const header = column.querySelector('.kanban-column-header h5');
            const counter = header.querySelector('.badge');
            
            if (counter && body) {
                const visibleCards = Array.from(body.querySelectorAll('.kanban-card'))
                    .filter(card => card.style.display !== 'none');
                counter.textContent = visibleCards.length;
            }
        });
    },

    updateSearchResults(visibleCount, totalCount) {
        const resultsDiv = document.getElementById('searchResults');
        const resultsCount = document.getElementById('resultsCount');

        if (visibleCount < totalCount) {
            resultsCount.textContent = visibleCount;
            resultsDiv.classList.remove('d-none');
        } else {
            resultsDiv.classList.add('d-none');
        }
    },

    updateEmptyStates() {
        document.querySelectorAll('.kanban-column-body').forEach(column => {
            const visibleCards = Array.from(column.querySelectorAll('.kanban-card'))
                .filter(card => card.style.display !== 'none');
            const emptyState = column.querySelector('.kanban-empty-state');

            if (visibleCards.length === 0 && !emptyState) {
                this.showEmptyState(column);
            } else if (visibleCards.length > 0 && emptyState) {
                emptyState.remove();
            }
        });
    },

    showEmptyState(column) {
        const estado = column.dataset.estado;
        const emptyStateDiv = document.createElement('div');
        emptyStateDiv.className = 'kanban-empty-state';
        emptyStateDiv.innerHTML = `
            <i class="fas fa-inbox fa-2x mb-2 text-muted"></i>
            <p class="text-muted mb-0">No hay equipos en ${estado.toLowerCase()}</p>
        `;
        column.appendChild(emptyStateDiv);
    }
};
