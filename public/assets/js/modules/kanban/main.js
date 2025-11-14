// Inicialización del Tablero Kanban
document.addEventListener('DOMContentLoaded', function() {
    console.log('[Kanban] Inicializando módulos...');

    // Inicializar validación
    if (typeof KanbanValidation !== 'undefined') {
        console.log('[Kanban] ✓ KanbanValidation cargado');
    }

    // Inicializar UI
    if (typeof KanbanUI !== 'undefined') {
        KanbanUI.init();
        console.log('[Kanban] ✓ KanbanUI inicializado');
    }

    // Inicializar Drag & Drop
    if (typeof KanbanDragDrop !== 'undefined') {
        KanbanDragDrop.init();
        console.log('[Kanban] ✓ KanbanDragDrop inicializado');
    }

    // Inicializar búsqueda
    if (typeof KanbanSearch !== 'undefined') {
        KanbanSearch.init();
        console.log('[Kanban] ✓ KanbanSearch inicializado');
    }

    // Mostrar notificaciones de flash si existen
    if (window.FLASH_SUCCESS) {
        if (typeof KanbanUI !== 'undefined' && typeof KanbanUI.showNotification === 'function') {
            KanbanUI.showNotification(window.FLASH_SUCCESS, 'success');
        }
    }

    if (window.FLASH_ERROR) {
        if (typeof KanbanUI !== 'undefined' && typeof KanbanUI.showNotification === 'function') {
            KanbanUI.showNotification(window.FLASH_ERROR, 'error');
        }
    }

    console.log('[Kanban] Inicialización completada');
});
