const HistorialSearch = {
    init() {
        this.bindEvents();
    },

    bindEvents() {
        const btnBuscar = document.getElementById('btnBuscar');
        if (btnBuscar) {
            btnBuscar.addEventListener('click', () => this.buscar());
        }
    },

    async buscar() {
        const filtroUsuario = document.getElementById('filtroUsuario').value;
        
        this.showLoading();
        
        try {
            const data = await this.fetchHistorial(filtroUsuario);
            
            if (data.success) {
                HistorialUI.updateTable(data.historial);
            } else {
                HistorialUI.showError(data.mensaje);
            }
        } catch (error) {
            console.error('Error:', error);
            HistorialUI.showError('Error de conexión');
        } finally {
            this.hideLoading();
        }
    },

    async fetchHistorial(usuario) {
        const response = await fetch(BASE_URL + 'historial/buscar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                usuario: usuario,
                [CSRF_TOKEN]: CSRF_HASH
            })
        });

        return await response.json();
    },

    showLoading() {
        const loadingElement = document.getElementById('loading');
        const tablaContainer = document.getElementById('tabla-container');
        
        if (loadingElement) loadingElement.style.display = 'block';
        if (tablaContainer) tablaContainer.style.opacity = '0.5';
    },

    hideLoading() {
        const loadingElement = document.getElementById('loading');
        const tablaContainer = document.getElementById('tabla-container');
        
        if (loadingElement) loadingElement.style.display = 'none';
        if (tablaContainer) tablaContainer.style.opacity = '1';
    }
};
