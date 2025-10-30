<?= $header; ?>

<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- Panel izquierdo - Lista de conversaciones -->
        <div class="col-md-4 col-lg-3 p-0 border-end bg-white">
            <div class="d-flex flex-column h-100">
                <!-- Header del usuario -->
                <div class="p-4 border-bottom bg-light">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3 bg-primary">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold"><?= session('usuario_nombre') ?? 'Usuario' ?></h6>
                            <small class="text-muted"><?= session('usuario_email') ?? 'usuario@sistema.com' ?></small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= base_url('mensajeria/configuracion') ?>">
                                    <i class="fas fa-cog me-2"></i>Configuración
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_url('mensajeria/enviar') ?>">
                                    <i class="fas fa-plus me-2"></i>Nuevo Mensaje
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Barra de búsqueda -->
                <div class="p-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" 
                               placeholder="Buscar conversaciones..." id="buscarConversaciones">
                    </div>
                </div>

                <!-- Lista de conversaciones -->
                <div class="flex-grow-1 overflow-auto scrollbar-custom panel-conversaciones" id="listaConversaciones">
                    <div class="text-center p-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="text-muted mt-2">Cargando conversaciones...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel derecho - Chat -->
        <div class="col-md-8 col-lg-9 p-0">
            <div class="d-flex flex-column h-100 bg-light">
                <!-- Header del chat -->
                <div class="p-3 border-bottom bg-white" id="headerChat" style="display: none;">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary btn-sm me-3 d-md-none" id="btnToggleSidebar">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="avatar-circle me-3 bg-success">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold" id="nombreDestinatario">Selecciona una conversación</h6>
                            <small class="text-muted" id="estadoDestinatario"></small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" id="btnVerPerfil">
                                    <i class="fas fa-user me-2"></i>Ver Perfil
                                </a></li>
                                <li><a class="dropdown-item" href="#" id="btnEliminarConversacion">
                                    <i class="fas fa-trash me-2"></i>Eliminar Conversación
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Área de mensajes -->
                <div class="flex-grow-1 overflow-auto p-3 scrollbar-custom panel-mensajes" id="areaMensajes">
                    <div class="text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                        </div>
                        <h4 class="text-muted mb-3">Selecciona una conversación</h4>
                        <p class="text-muted mb-4">Elige una conversación de la lista para comenzar a chatear</p>
                        <a href="<?= base_url('mensajeria/enviar') ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Nuevo Mensaje
                        </a>
                    </div>
                </div>

                <!-- Área de escritura -->
                <div class="p-3 border-top bg-white area-escritura" id="areaEscritura" style="display: none;">
                    <form id="formEnviarMensaje" enctype="multipart/form-data">
                        <input type="hidden" id="destinatario_id" name="destinatario_id">
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" id="btnAdjuntar">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <input type="file" id="archivoAdjunto" name="archivo" class="d-none" />
                            <input type="text" class="form-control" id="mensajeTexto" 
                                   placeholder="Escribe tu mensaje..." autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary" id="btnEmoji">
                                <i class="fas fa-smile"></i>
                            </button>
                            <button type="submit" class="btn btn-primary" id="btnEnviar">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overlay para móviles -->
<div class="mobile-overlay" id="mobileOverlay"></div>

<!-- Sonido de notificación -->
<audio id="sndMensaje" preload="auto">
    <source src="<?= base_url('assets/img/kaiadmin/notify.mp3') ?>" type="audio/mpeg">
    <source src="<?= base_url('assets/img/kaiadmin/notify.ogg') ?>" type="audio/ogg">
  </audio>



<script>
let conversaciones = [];
let conversacionActual = null;
let timeoutBusqueda = null;
let contadorMensajesPorChat = {}; // para detectar nuevos mensajes

$(document).ready(function() {
    cargarConversaciones();
    iniciarHeartbeat();
    
    // Configurar eventos
    $('#buscarConversaciones').on('input', function() {
        const termino = $(this).val().toLowerCase();
        filtrarConversaciones(termino);
    });
    
    $('#formEnviarMensaje').on('submit', function(e) {
        e.preventDefault();
        enviarMensaje();
    });

    // Adjuntos
    $('#btnAdjuntar').on('click', function(){ $('#archivoAdjunto').click(); });
    
    // Typing indicator
    let typingTimeout;
    $('#mensajeTexto').on('input', function(){
        if (!conversacionActual) return;
        $.post('<?= base_url('mensajeria/typingStart') ?>/' + conversacionActual);
        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(function(){
            $.post('<?= base_url('mensajeria/typingStop') ?>/' + conversacionActual);
        }, 2000);
    });
    
    $('#mensajeTexto').on('keypress', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            enviarMensaje();
        }
    });
    
    // Toggle sidebar en móviles
    $('#btnToggleSidebar').on('click', function() {
        $('.col-md-4').toggleClass('show');
        $('#mobileOverlay').toggleClass('show');
    });
    
    // Cerrar sidebar al hacer clic en overlay
    $('#mobileOverlay').on('click', function() {
        $('.col-md-4').removeClass('show');
        $('#mobileOverlay').removeClass('show');
    });
    
    // Cerrar sidebar al hacer clic fuera en móviles
    $(document).on('click', function(e) {
        if ($(window).width() <= 768) {
            if (!$(e.target).closest('.col-md-4, #btnToggleSidebar').length) {
                $('.col-md-4').removeClass('show');
                $('#mobileOverlay').removeClass('show');
            }
        }
    });
});

function cargarConversaciones() {
    $.get('<?= base_url('mensajeria/getConversaciones') ?>')
        .done(function(response) {
            if (response.success) {
                conversaciones = response.data;
                mostrarConversaciones(conversaciones);
            } else {
                mostrarError('Error al cargar conversaciones: ' + response.message);
            }
        })
        .fail(function() {
            mostrarError('Error de conexión al cargar conversaciones');
        });
}

function mostrarConversaciones(conversaciones) {
    const container = $('#listaConversaciones');
    
    if (conversaciones.length === 0) {
        container.html(`
            <div class="text-center p-4">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-3">No tienes conversaciones</h5>
                <p class="text-muted mb-4">Comienza una nueva conversación</p>
                <a href="<?= base_url('mensajeria/enviar') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Mensaje
                </a>
            </div>
        `);
        return;
    }
    
    let html = '';
    conversaciones.forEach(function(conv) {
        const ultimoMensaje = conv.ultimo_mensaje || 'Sin mensajes';
        const horaUltimoMensaje = conv.fecha_ultimo_mensaje ? 
            new Date(conv.fecha_ultimo_mensaje).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'}) : '';
        
        // Truncar mensaje largo
        const mensajeTruncado = ultimoMensaje.length > 50 ? 
            ultimoMensaje.substring(0, 50) + '...' : ultimoMensaje;
        
        html += `
            <div class="conversacion-item" data-conversacion-id="${conv.usuario_id}" onclick="abrirConversacion(${conv.usuario_id})">
                <div class="d-flex align-items-center">
                    <div class="conversacion-avatar me-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="mb-1 fw-bold">${conv.nombre_completo}</h6>
                            <small class="text-muted">${horaUltimoMensaje}</small>
                        </div>
                        <p class="mb-0 text-muted small">${mensajeTruncado}</p>
                    </div>
                    ${conv.mensajes_no_leidos > 0 ? `<span class="badge bg-danger">${conv.mensajes_no_leidos}</span>` : ''}
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function filtrarConversaciones(termino) {
    clearTimeout(timeoutBusqueda);
    timeoutBusqueda = setTimeout(() => {
        const conversacionesFiltradas = conversaciones.filter(conv => 
            conv.nombre_completo.toLowerCase().includes(termino)
        );
        mostrarConversaciones(conversacionesFiltradas);
    }, 300);
}

function abrirConversacion(usuarioId) {
    conversacionActual = usuarioId;
    
    // Actualizar UI
    $('.conversacion-item').removeClass('active');
    $(`.conversacion-item[data-conversacion-id="${usuarioId}"]`).addClass('active');
    
    // Ocultar sidebar en móviles
    $('.col-md-4').removeClass('show');
    $('#mobileOverlay').removeClass('show');
    
    // Mostrar área de chat
    $('#headerChat').show();
    $('#areaEscritura').show();
    $('#destinatario_id').val(usuarioId);
    
    // Cargar mensajes de la conversación
    cargarMensajesConversacion(usuarioId);
    
    // Actualizar header
    const conversacion = conversaciones.find(c => c.usuario_id == usuarioId);
    if (conversacion) {
        $('#nombreDestinatario').text(conversacion.nombre_completo);
        actualizarPresence();
    }
}

function cargarMensajesConversacion(usuarioId) {
    $('#areaMensajes').html(`
        <div class="text-center p-3">
            <i class="fas fa-spinner fa-spin text-primary"></i>
            <span class="ms-2 text-muted">Cargando mensajes...</span>
        </div>
    `);
    
    $.get(`<?= base_url('mensajeria/getMensajesConversacion') ?>/${usuarioId}`)
        .done(function(response) {
            if (response.success) {
                mostrarMensajes(response.data);
                // Consultar typing status al mostrar
                consultarTyping();
            } else {
                mostrarError('Error al cargar mensajes: ' + response.message);
            }
        })
        .fail(function() {
            mostrarError('Error de conexión al cargar mensajes');
        });
}

function iniciarHeartbeat(){
    setInterval(function(){ $.post('<?= base_url('mensajeria/heartbeat') ?>'); }, 20000);
}

function actualizarPresence(){
    if (!conversacionActual) return;
    $.get('<?= base_url('mensajeria/getPresence') ?>/' + conversacionActual)
        .done(function(r){
            if (r.success) {
                $('#estadoDestinatario').html(r.online ?
                    '<i class="fas fa-circle text-success" style="font-size:8px;"></i> En línea' :
                    '<i class="fas fa-circle text-muted" style="font-size:8px;"></i> Desconectado');
            }
        });
}

function consultarTyping(){
    if (!conversacionActual) return;
    $.get('<?= base_url('mensajeria/typingStatus') ?>/' + conversacionActual)
        .done(function(r){
            if (r.success) {
                if (r.typing) {
                    $('#estadoDestinatario').html('<i class="fas fa-pencil-alt text-success" style="font-size:8px;"></i> escribiendo…');
                } else {
                    actualizarPresence();
                }
            }
        });
}

function mostrarMensajes(mensajes) {
    const container = $('#areaMensajes');
    const usuarioActual = <?= session()->get('usuario_id') ?>;
    
    if (mensajes.length === 0) {
        container.html(`
            <div class="text-center p-5">
                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-3">No hay mensajes</h5>
                <p class="text-muted">Envía el primer mensaje para comenzar</p>
            </div>
        `);
        return;
    }
    
    let html = '';
    let fechaAnterior = '';
    
    mensajes.forEach(function(mensaje) {
        const fechaMensaje = new Date(mensaje.fecha_envio).toLocaleDateString('es-ES');
        const horaMensaje = new Date(mensaje.fecha_envio).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
        
        // Mostrar fecha si es diferente a la anterior
        if (fechaMensaje !== fechaAnterior) {
            html += `<div class="fecha-separador"><span>${fechaMensaje}</span></div>`;
            fechaAnterior = fechaMensaje;
        }
        
        const esPropio = mensaje.remitente_id == usuarioActual;
        const claseBubble = esPropio ? 'mensaje-propio' : 'mensaje-recibido';
        
        // Badge de tipo de mensaje
        let tipoBadge = '';
        if (mensaje.tipo !== 'normal') {
            const tipoClass = mensaje.tipo === 'importante' ? 'badge-importante' : 'badge-urgente';
            const tipoTexto = mensaje.tipo === 'importante' ? 'Importante' : 'Urgente';
            tipoBadge = `<span class="badge-tipo ${tipoClass}">${tipoTexto}</span>`;
        }
        
        // Checks de estado estilo WhatsApp
        let checks = '';
        if (esPropio) {
            const st = (mensaje.status || '').toLowerCase();
            if (st === 'read') {
                checks = '<span class="ms-1 text-primary">✔✔</span>';
            } else if (st === 'delivered') {
                checks = '<span class="ms-1 text-secondary">✔✔</span>';
            } else {
                checks = '<span class="ms-1 text-secondary">✔</span>';
            }
        }

        // Contenido con soporte básico de adjunto
        let contenidoHTML = mensaje.contenido;
        const matchAdj = contenidoHTML.match(/\[archivo\]\s+(https?:[^\s]+)/i);
        if (matchAdj) {
            const url = matchAdj[1];
            const isImg = /(\.png|\.jpg|\.jpeg|\.gif|\.webp)$/i.test(url);
            const link = `<a href="${url}" target="_blank" rel="noopener">Ver archivo</a>`;
            contenidoHTML = contenidoHTML.replace(matchAdj[0], isImg ? `<div class=\"mb-1\"><img src=\"${url}\" alt=\"adjunto\" style=\"max-width:220px;border-radius:6px\"/></div>${link}` : link);
        }

        html += `
            <div class="d-flex ${esPropio ? 'justify-content-end' : 'justify-content-start'} mb-2">
                <div class="mensaje-bubble ${claseBubble}">
                    ${tipoBadge}
                    <div>${contenidoHTML}</div>
                    <div class="mensaje-hora">${horaMensaje} ${checks}</div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
    
    // Scroll al final
    container.scrollTop(container[0].scrollHeight);

    // Sonido si hay nuevos mensajes recibidos
    const actual = mensajes.length;
    const prev = contadorMensajesPorChat[conversacionActual] || 0;
    if (actual > prev) {
        const ultimo = mensajes[mensajes.length - 1];
        if (ultimo && String(ultimo.remitente_id) !== String(usuarioActual)) {
            const snd = document.getElementById('sndMensaje');
            if (snd && snd.play) { snd.play().catch(()=>{}); }
        }
    }
    contadorMensajesPorChat[conversacionActual] = actual;
}

function enviarMensaje() {
    const texto = $('#mensajeTexto').val().trim();
    const destinatarioId = $('#destinatario_id').val();
    
    if (!texto || !destinatarioId) {
        return;
    }
    
    const $btnEnviar = $('#btnEnviar');
    const iconoOriginal = $btnEnviar.html();
    
    $btnEnviar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
    
    // Obtener el token CSRF
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    const fd = new FormData(document.getElementById('formEnviarMensaje'));
    fd.set('destinatario_id', destinatarioId);
    fd.set('asunto', 'Mensaje directo');
    fd.set('contenido', texto);
    fd.set('tipo', 'normal');

    $.ajax({
        url: '<?= base_url('mensajeria/procesarEnvio') ?>',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        data: fd,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.success) {
                $('#mensajeTexto').val('');
                $('#archivoAdjunto').val('');
                // Recargar mensajes con delay corto para ver el nuevo como 'sent'
                cargarMensajesConversacion(destinatarioId);
                // Actualizar lista de conversaciones
                cargarConversaciones();
            } else {
                mostrarError(response.message || 'Error al enviar mensaje');
            }
        },
        error: function() {
            mostrarError('Error de conexión');
        },
        complete: function() {
            $btnEnviar.prop('disabled', false).html(iconoOriginal);
        }
    });
}

function mostrarError(mensaje) {
    Swal.fire({
        title: 'Error',
        text: mensaje,
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc3545'
    });
}

// Auto-refresh rápido cuando hay conversación abierta, lento cuando no
setInterval(function() {
    if (conversacionActual) {
        cargarMensajesConversacion(conversacionActual);
        cargarConversaciones();
        actualizarPresence();
        consultarTyping();
    }
}, 3000);

setInterval(function() {
    if (!conversacionActual) {
        cargarConversaciones();
    }
}, 15000);
</script>

<?= $footer ?>