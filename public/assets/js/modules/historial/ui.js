const HistorialUI = {
    updateTable(historial) {
        const tablaBody = document.getElementById('tabla-body');

        if (historial.length === 0) {
            this.showEmptyState(tablaBody);
            return;
        }

        const html = historial.map(item => this.createTableRow(item)).join('');
        tablaBody.innerHTML = html;
    },

    showEmptyState(tablaBody) {
        tablaBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No se encontraron registros</p>
                </td>
            </tr>
        `;
    },

    createTableRow(item) {
        const inicial = item.usuario.charAt(0).toUpperCase();
        
        return `
            <tr>
                <td class="align-middle">
                    <span class="text-dark fw-medium">${item.fecha}</span>
                </td>
                <td class="align-middle">
                    <span class="badge bg-secondary">${item.hora}</span>
                </td>
                <td class="align-middle">
                    <span class="text-muted">${item.dia}</span>
                </td>
                <td class="align-middle">
                    ${this.createUserAvatar(inicial, item.usuario)}
                </td>
                <td class="align-middle">${item.accion}</td>
            </tr>
        `;
    },

    createUserAvatar(inicial, usuario) {
        return `
            <div class="d-flex align-items-center">
                <div class="avatar-circle me-2">${inicial}</div>
                <span class="text-dark">${usuario}</span>
            </div>
        `;
    },

    showError(mensaje) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje,
            confirmButtonColor: '#ffc107'
        });
    }
};
