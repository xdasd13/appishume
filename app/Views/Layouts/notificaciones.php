<!-- Sistema de Notificaciones para Header -->
<div class="navbar-nav ms-auto">
    <!-- Notificaciones de Mensajes -->
    <div class="nav-item dropdown me-3">
        <a class="nav-link dropdown-toggle position-relative" href="#" id="dropdown-mensajes" 
           role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-envelope"></i>
            <span id="badge-mensajes" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                  style="display: none; font-size: 0.7em;">0</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-mensajes" style="min-width: 300px;">
            <li><h6 class="dropdown-header">Mensajes</h6></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <div id="mensajes-dropdown-content">
                    <div class="text-center p-3">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p class="mb-0 text-muted">Cargando mensajes...</p>
                    </div>
                </div>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-center" href="<?= base_url('mensajeria') ?>">
                    <i class="fas fa-comments me-1"></i> Ver todos los mensajes
                </a>
            </li>
        </ul>
    </div>

    <!-- Notificaciones del Sistema -->
    <div class="nav-item dropdown me-3">
        <a class="nav-link dropdown-toggle position-relative" href="#" id="dropdown-notificaciones" 
           role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-bell"></i>
            <span id="badge-notificaciones" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                  style="display: none; font-size: 0.7em;">0</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-notificaciones" style="min-width: 350px;">
            <li><h6 class="dropdown-header">Notificaciones</h6></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <div id="notificaciones-dropdown-content">
                    <div class="text-center p-3">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p class="mb-0 text-muted">Cargando notificaciones...</p>
                    </div>
                </div>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <div class="d-flex justify-content-between px-3 py-2">
                    <a href="#" class="btn btn-sm btn-outline-primary" onclick="marcarTodasNotificacionesLeidas()">
                        <i class="fas fa-check-double me-1"></i> Marcar todas como leídas
                    </a>
                    <a href="<?= base_url('mensajeria/configuracion') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-cog me-1"></i> Configurar
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>



<script>
// Funciones para manejar el dropdown de notificaciones
$(document).ready(function() {
    // Cargar notificaciones cuando se abre el dropdown
    $('#dropdown-notificaciones').on('show.bs.dropdown', function() {
        cargarNotificacionesRecientes();
    });
    
    // Cargar mensajes cuando se abre el dropdown
    $('#dropdown-mensajes').on('show.bs.dropdown', function() {
        cargarMensajesRecientes();
    });
});

function cargarNotificacionesRecientes() {
    $.get('<?= base_url('mensajeria/getNotificacionesRecientes') ?>', { limit: 5 })
        .done(function(response) {
            if (response.success) {
                mostrarNotificaciones(response.data);
            } else {
                mostrarErrorNotificaciones('Error al cargar notificaciones');
            }
        })
        .fail(function() {
            mostrarErrorNotificaciones('Error de conexión');
        });
}

function cargarMensajesRecientes() {
    // Esta función se implementaría para cargar mensajes recientes
    // Por ahora mostramos un mensaje de placeholder
    $('#mensajes-dropdown-content').html(`
        <div class="text-center p-3">
            <i class="fas fa-envelope fa-2x text-muted mb-2"></i>
            <p class="mb-0 text-muted">No hay mensajes recientes</p>
            <a href="<?= base_url('mensajeria') ?>" class="btn btn-sm btn-primary mt-2">
                <i class="fas fa-comments me-1"></i> Ir a Mensajería
            </a>
        </div>
    `);
}

function mostrarNotificaciones(notificaciones) {
    const container = $('#notificaciones-dropdown-content');
    
    if (notificaciones.length === 0) {
        container.html(`
            <div class="text-center p-3">
                <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No hay notificaciones</p>
            </div>
        `);
        return;
    }
    
    let html = '';
    notificaciones.forEach(function(notificacion) {
        const iconClass = getIconClass(notificacion.tipo);
        const isUnread = !notificacion.leida;
        const unreadClass = isUnread ? 'unread' : '';
        
        html += `
            <div class="notification-item ${unreadClass}" onclick="marcarNotificacionLeida(${notificacion.id})">
                <div class="d-flex align-items-start">
                    <div class="notification-icon ${notificacion.tipo}">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notificacion.titulo}</div>
                        <div class="notification-message">${notificacion.mensaje}</div>
                        <div class="notification-time">${notificacion.tiempo_transcurrido}</div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function mostrarErrorNotificaciones(mensaje) {
    $('#notificaciones-dropdown-content').html(`
        <div class="text-center p-3">
            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
            <p class="mb-0 text-muted">${mensaje}</p>
        </div>
    `);
}

function getIconClass(tipo) {
    switch(tipo) {
        case 'mensaje':
            return 'fas fa-envelope';
        case 'mensaje_importante':
            return 'fas fa-exclamation-triangle';
        case 'mensaje_urgente':
            return 'fas fa-exclamation-circle';
        case 'sistema':
            return 'fas fa-cog';
        default:
            return 'fas fa-bell';
    }
}

function marcarNotificacionLeida(notificacionId) {
    $.ajax({
        url: '<?= base_url('mensajeria/marcarNotificacionLeida') ?>',
        method: 'POST',
        data: {
            notificacion_id: notificacionId,
            <?= csrf_token_name() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                // Remover la clase unread
                $(`[onclick="marcarNotificacionLeida(${notificacionId})"]`).removeClass('unread');
                
                // Actualizar contador
                if (window.notificacionesManager) {
                    window.notificacionesManager.forceCheck();
                }
            }
        }
    });
}

function marcarTodasNotificacionesLeidas() {
    $.ajax({
        url: '<?= base_url('mensajeria/marcarTodasNotificacionesLeidas') ?>',
        method: 'POST',
        data: {
            <?= csrf_token_name() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                // Remover todas las clases unread
                $('.notification-item').removeClass('unread');
                
                // Actualizar contador
                if (window.notificacionesManager) {
                    window.notificacionesManager.forceCheck();
                }
                
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: 'Éxito',
                    text: 'Todas las notificaciones han sido marcadas como leídas',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }
    });
}

// Función para actualizar contadores desde el sistema de notificaciones
function actualizarContadoresHeader(notificaciones, mensajes) {
    // Actualizar badge de notificaciones
    const badgeNotificaciones = $('#badge-notificaciones');
    if (notificaciones.total > 0) {
        badgeNotificaciones.text(notificaciones.total).show();
    } else {
        badgeNotificaciones.hide();
    }
    
    // Actualizar badge de mensajes
    const badgeMensajes = $('#badge-mensajes');
    if (mensajes.total > 0) {
        badgeMensajes.text(mensajes.total).show();
    } else {
        badgeMensajes.hide();
    }
}
</script>
