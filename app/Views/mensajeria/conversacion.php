<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/mensajeria-conversacion.css') ?>">

<div class="container-fluid">
    <div class="row">
        <!-- Información del contacto -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold text-primary">
                                <?= $contacto->nombres . ' ' . $contacto->apellidos ?>
                            </h6>
                            <small class="text-muted"><?= $contacto->nombreusuario ?> - <?= $contacto->cargo_nombre ?? 'Sin cargo' ?></small>
                        </div>
                    </div>
                    <div>
                        <a href="<?= base_url('mensajeria') ?>" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button class="btn btn-primary btn-sm" onclick="mostrarFormularioRespuesta()">
                            <i class="fas fa-reply"></i> Responder
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Área de mensajes -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-comments"></i> Conversación
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div id="mensajes-container" class="mensajes-container">
                        <?php if (empty($mensajes)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay mensajes en esta conversación</p>
                                <button class="btn btn-primary" onclick="mostrarFormularioRespuesta()">
                                    <i class="fas fa-plus"></i> Enviar primer mensaje
                                </button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($mensajes as $mensaje): ?>
                                <div class="mensaje-item <?= $mensaje['direccion'] ?>" data-mensaje-id="<?= $mensaje['id'] ?>">
                                    <div class="mensaje-bubble">
                                        <div class="mensaje-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?= $mensaje['remitente_nombre'] ?></strong>
                                                    <small class="text-muted ms-2">
                                                        <?= date('d/m/Y H:i', strtotime($mensaje['fecha_envio'])) ?>
                                                    </small>
                                                </div>
                                                <div class="mensaje-tipo">
                                                    <?php if ($mensaje['tipo'] === 'urgente'): ?>
                                                        <span class="badge bg-danger">🚨 Urgente</span>
                                                    <?php elseif ($mensaje['tipo'] === 'importante'): ?>
                                                        <span class="badge bg-warning">⚠️ Importante</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <h6 class="mensaje-asunto"><?= htmlspecialchars($mensaje['asunto']) ?></h6>
                                        </div>
                                        <div class="mensaje-contenido">
                                            <?= nl2br(htmlspecialchars($mensaje['contenido'])) ?>
                                        </div>
                                        <div class="mensaje-actions">
                                            <?php if ($mensaje['direccion'] === 'recibido'): ?>
                                                <button class="btn btn-sm btn-outline-primary" onclick="responderMensaje(<?= $mensaje['id'] ?>)">
                                                    <i class="fas fa-reply"></i> Responder
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-outline-danger" onclick="eliminarMensaje(<?= $mensaje['id'] ?>)">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de respuesta (oculto por defecto) -->
        <div class="col-12" id="formulario-respuesta" style="display: none;">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-reply"></i> Responder Mensaje
                    </h6>
                </div>
                <div class="card-body">
                    <form id="formResponderMensaje">
                        <input type="hidden" id="mensaje_respuesta_id" name="mensaje_respuesta_id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo_respuesta" class="form-label">Tipo de Mensaje *</label>
                                <select class="form-select" id="tipo_respuesta" name="tipo_respuesta" required>
                                    <option value="normal">💬 Normal</option>
                                    <option value="importante">⚠️ Importante</option>
                                    <option value="urgente">🚨 Urgente</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="asunto_respuesta" class="form-label">Asunto *</label>
                            <input type="text" class="form-control" id="asunto_respuesta" name="asunto_respuesta" 
                                   placeholder="Ingrese el asunto del mensaje..." required>
                        </div>

                        <div class="mb-3">
                            <label for="contenido_respuesta" class="form-label">Contenido *</label>
                            <textarea class="form-control" id="contenido_respuesta" name="contenido_respuesta" 
                                      rows="4" placeholder="Escriba su respuesta aquí..." required></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="ocultarFormularioRespuesta()">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" id="btnResponder">
                                <i class="fas fa-paper-plane"></i> Enviar Respuesta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
const contactoId = <?= $contacto->idusuario ?>;

$(document).ready(function() {
    // Scroll al final de los mensajes
    scrollToBottom();
    
    // Configurar formulario de respuesta
    $('#formResponderMensaje').on('submit', function(e) {
        e.preventDefault();
        enviarRespuesta();
    });
    
    // Auto-resize del textarea
    $('#contenido_respuesta').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});

function scrollToBottom() {
    const container = document.getElementById('mensajes-container');
    container.scrollTop = container.scrollHeight;
}

function mostrarFormularioRespuesta() {
    $('#formulario-respuesta').slideDown();
    $('#asunto_respuesta').focus();
}

function ocultarFormularioRespuesta() {
    $('#formulario-respuesta').slideUp();
    $('#formResponderMensaje')[0].reset();
}

function responderMensaje(mensajeId) {
    $('#mensaje_respuesta_id').val(mensajeId);
    mostrarFormularioRespuesta();
    
    // Pre-rellenar asunto con "Re: "
    const mensajeOriginal = $(`[data-mensaje-id="${mensajeId}"]`);
    const asuntoOriginal = mensajeOriginal.find('.mensaje-asunto').text();
    $('#asunto_respuesta').val('Re: ' + asuntoOriginal);
}

function enviarRespuesta() {
    const $btnResponder = $('#btnResponder');
    const textoOriginal = $btnResponder.html();
    
    // Validar campos
    const asunto = $('#asunto_respuesta').val().trim();
    const contenido = $('#contenido_respuesta').val().trim();
    const tipo = $('#tipo_respuesta').val();
    
    if (!asunto || asunto.trim().length === 0) {
        Swal.fire('Error', 'El asunto no puede estar vacío', 'error');
        return;
    }
    
    if (!contenido || contenido.trim().length === 0) {
        Swal.fire('Error', 'El contenido es obligatorio', 'error');
        return;
    }
    
    // Mostrar estado de carga
    $btnResponder.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
    
    // Obtener el token CSRF del meta tag
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    // Enviar datos
    $.ajax({
        url: '<?= base_url('mensajeria/procesarEnvio') ?>',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            destinatario_id: contactoId,
            asunto: asunto,
            contenido: contenido,
            tipo: tipo
        },
        success: function(response) {
            if (response.success) {
                // Recargar la página para mostrar el nuevo mensaje
                location.reload();
            } else {
                Swal.fire({
                    title: 'Error',
                    text: response.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function(xhr, status, error) {
            let mensajeError = 'Error al enviar la respuesta. Intente nuevamente.';
            
            if (xhr.status === 403) {
                mensajeError = 'Acceso denegado. Por favor, verifique su sesión e intente nuevamente.';
            } else if (xhr.status === 401) {
                mensajeError = 'Sesión expirada. Por favor, inicie sesión nuevamente.';
                window.location.href = '<?= base_url('login') ?>';
                return;
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                mensajeError = xhr.responseJSON.message;
            }
            
            Swal.fire({
                title: 'Error',
                text: mensajeError,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        },
        complete: function() {
            $btnResponder.prop('disabled', false).html(textoOriginal);
        }
    });
}

function eliminarMensaje(mensajeId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Deseas eliminar este mensaje?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Obtener el token CSRF del meta tag
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            
            $.ajax({
                url: '<?= base_url('mensajeria/eliminarMensaje') ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    mensaje_id: mensajeId
                },
                success: function(response) {
                    if (response.success) {
                        $(`[data-mensaje-id="${mensajeId}"]`).fadeOut(300, function() {
                            $(this).remove();
                        });
                        Swal.fire('Eliminado', response.message, 'success');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al eliminar el mensaje', 'error');
                }
            });
        }
    });
}

// Actualizar contadores cada 30 segundos
setInterval(function() {
    actualizarContadores();
}, 30000);

function actualizarContadores() {
    // Actualizar contador de mensajes
    $.get('<?= base_url('mensajeria/getMensajesNoLeidos') ?>', function(response) {
        if (response.success) {
            if (response.data.total > 0) {
                $('#badge-mensajes').text(response.data.total).show();
            } else {
                $('#badge-mensajes').hide();
            }
        }
    });

    // Actualizar contador de notificaciones
    $.get('<?= base_url('mensajeria/getNotificacionesNoLeidas') ?>', function(response) {
        if (response.success) {
            if (response.data.total > 0) {
                $('#badge-notificaciones').text(response.data.total).show();
            } else {
                $('#badge-notificaciones').hide();
            }
        }
    });
}
</script>

<?= $footer ?>
