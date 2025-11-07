const KanbanValidation = {
    validateTransition(estadoActual, nuevoEstado) {
        if (estadoActual === nuevoEstado) {
            return { valido: true, mensaje: 'Sin cambios' };
        }

        if (this.isCompletado(estadoActual)) {
            return this.errorCompletado();
        }

        if (this.skipsPendiente(estadoActual, nuevoEstado)) {
            return this.errorSkipPendiente();
        }

        if (this.skipsEnProceso(estadoActual, nuevoEstado)) {
            return this.errorSkipEnProceso();
        }

        if (this.goesBackward(estadoActual, nuevoEstado)) {
            return this.errorGoBackward();
        }

        return { valido: true, mensaje: 'Transición válida' };
    },

    isCompletado(estado) {
        return estado === 'Completado';
    },

    skipsPendiente(actual, nuevo) {
        return actual === 'Programado' && (nuevo === 'En Proceso' || nuevo === 'Completado');
    },

    skipsEnProceso(actual, nuevo) {
        return actual === 'Pendiente' && nuevo === 'Completado';
    },

    goesBackward(actual, nuevo) {
        if (actual === 'En Proceso' && (nuevo === 'Pendiente' || nuevo === 'Programado')) {
            return true;
        }
        if (actual === 'Pendiente' && nuevo === 'Programado') {
            return true;
        }
        return false;
    },

    errorCompletado() {
        return {
            valido: false,
            mensaje: 'Este servicio ya está completo y no puede modificarse'
        };
    },

    errorSkipPendiente() {
        return {
            valido: false,
            mensaje: 'Debe pasar primero por Pendiente antes de iniciar el proceso'
        };
    },

    errorSkipEnProceso() {
        return {
            valido: false,
            mensaje: 'Debe iniciar el proceso antes de completar el servicio'
        };
    },

    errorGoBackward() {
        return {
            valido: false,
            mensaje: 'Este servicio no puede retroceder de estado'
        };
    }
};
