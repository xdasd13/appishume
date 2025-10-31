<?= $header ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
                    </h5>
                </div>
                <div class="card-body">
                    <form id="formEnviarMensaje">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="destinatario_id" class="form-label">Destinatario *</label>
                                <select class="form-select" id="destinatario_id" name="destinatario_id" required>
                                    <option value="">Seleccione un destinatario</option>
                                    <?php 
                                    // Obtener usuarios del sistema
                                    $usuarioModel = new \App\Models\UsuarioModel();
                                    $usuarios = $usuarioModel->getUsuariosCompletos();
                                    $usuarioActual = session()->get('usuario_id');
                                    
                                    foreach ($usuarios as $usuario): 
                                        if ($usuario->idusuario != $usuarioActual): // No incluir al usuario actual
                                    ?>
                                        <option value="<?= $usuario->idusuario ?>">
                                            <?= esc($usuario->nombres . ' ' . $usuario->apellidos) ?> 
                                            (<?= esc($usuario->nombreusuario) ?>)
                                        </option>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </select>
                                <div class="invalid-feedback">Por favor seleccione un destinatario.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tipo" class="form-label">Tipo de Mensaje *</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="normal">Normal</option>
                                    <option value="importante">Importante</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                                <div class="invalid-feedback">Por favor seleccione un tipo de mensaje.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="asunto" class="form-label">Asunto *</label>
                            <input type="text" class="form-control" id="asunto" name="asunto" 
                                   placeholder="Ingrese el asunto del mensaje" required>
                            <div class="invalid-feedback">El asunto es obligatorio.</div>
                        </div>

                        <div class="mb-3">
                            <label for="contenido" class="form-label">Contenido *</label>
                            <textarea class="form-control" id="contenido" name="contenido" 
                                      rows="5" placeholder="Escriba su mensaje aquí..." required></textarea>
                            <div class="invalid-feedback">El contenido es obligatorio.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('mensajeria') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnEnviar" disabled>
                                <i class="fas fa-paper-plane"></i> Enviar Mensaje
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Configurar envío del formulario
    $('#formEnviarMensaje').on('submit', function(e) {
        e.preventDefault();
        if (validarFormulario()) {
            enviarMensaje();
        }
    });
    
    // Validar campos individualmente cuando cambian (no marcar todo en rojo)
    $('#destinatario_id').on('change', function() {
        // Solo validar el campo que cambió
        validarCampo($(this));
        // Verificar si todos los campos están completos para habilitar botón
        verificarFormularioCompleto();
    });
    
    $('#tipo').on('change', function() {
        validarCampo($(this));
        verificarFormularioCompleto();
    });
    
    $('#asunto, #contenido').on('blur', function() {
        // Validar solo cuando el usuario termina de escribir (pierde el foco)
        validarCampo($(this));
        verificarFormularioCompleto();
    });
    
    // Limpiar validación cuando el usuario empiece a escribir
    $('#asunto, #contenido').on('input', function() {
        // Solo limpiar el error, no mostrar validación positiva aún
        $(this).removeClass('is-invalid');
        // Verificar estado del formulario mientras escribe
        verificarFormularioCompleto();
    });
    
    // Función para verificar si el formulario está completo sin validar todo
    function verificarFormularioCompleto() {
        const destinatarioId = $('#destinatario_id').val();
        const asunto = $('#asunto').val().trim();
        const contenido = $('#contenido').val().trim();
        const tipo = $('#tipo').val();
        
        // Solo habilitar si todos los campos tienen valor (sin restricción de caracteres)
        const completo = destinatarioId && asunto && asunto.trim().length > 0 && contenido && contenido.trim().length > 0 && tipo;
        $('#btnEnviar').prop('disabled', !completo);
    }
});

function validarCampo($campo) {
    const campoId = $campo.attr('id');
    const valor = $campo.val() ? $campo.val().trim() : '';
    
    // Limpiar clases previas
    $campo.removeClass('is-invalid is-valid');
    
    // Validaciones específicas por campo
    if (!valor) {
        $campo.addClass('is-invalid');
        return false;
    } else {
        // Sin validaciones de longitud mínima - solo que no esté vacío
        $campo.addClass('is-valid');
        return true;
    }
}

function validarFormulario() {
    const destinatarioId = $('#destinatario_id').val();
    const asunto = $('#asunto').val().trim();
    const contenido = $('#contenido').val().trim();
    const tipo = $('#tipo').val();
    
    let esValido = true;
    
    // Validar destinatario
    if (!destinatarioId) {
        $('#destinatario_id').removeClass('is-valid').addClass('is-invalid');
        esValido = false;
    } else {
        $('#destinatario_id').removeClass('is-invalid').addClass('is-valid');
    }
    
    // Validar asunto (sin restricción de caracteres mínimos)
    if (!asunto || asunto.trim().length === 0) {
        $('#asunto').removeClass('is-valid').addClass('is-invalid');
        esValido = false;
    } else {
        $('#asunto').removeClass('is-invalid').addClass('is-valid');
    }
    
    // Validar contenido (sin restricción de caracteres mínimos)
    if (!contenido || contenido.trim().length === 0) {
        $('#contenido').removeClass('is-valid').addClass('is-invalid');
        esValido = false;
    } else {
        $('#contenido').removeClass('is-invalid').addClass(' почемуvalid');
    }
    
    // Validar tipo
    if (!tipo) {
        $('#tipo').removeClass('is-valid').addClass('is-invalid');
        esValido = false;
    } else {
        $('#tipo').removeClass('is-invalid').addClass('is-valid');
    }
    
    // Habilitar/deshabilitar botón
    $('#btnEnviar').prop('disabled', !esValido);
    
    return esValido;
}

function enviarMensaje() {
    if (!validarFormulario()) {
        return;
    }
    
    const $btnEnviar = $('#btnEnviar');
    const textoOriginal = $btnEnviar.html();
    
    // Obtener datos del formulario
    const destinatarioId = $('#destinatario_id').val();
    const asunto = $('#asunto').val().trim();
    const contenido = $('#contenido').val().trim();
    const tipo = $('#tipo').val();
    
    // Mostrar estado de carga
    $btnEnviar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
    
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
            destinatario_id: destinatarioId,
            asunto: asunto,
            contenido: contenido,
            tipo: tipo
        },
        success: function(response) {
            if (response.success) {
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: '¡Mensaje Enviado!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Redirigir a la conversación
                    window.location.href = '<?= base_url('mensajeria/conversacion') ?>/' + destinatarioId;
                });
            } else {
                // Mostrar errores
                let mensajeError = response.message || 'Error al enviar el mensaje';
                if (response.errors) {
                    mensajeError += '<br><br>';
                    Object.values(response.errors).forEach(error => {
                        mensajeError += '• ' + error + '<br>';
                    });
                }
                
                Swal.fire({
                    title: 'Error',
                    html: mensajeError,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function(xhr, status, error) {
            let mensajeError = 'Error de conexión. Intente nuevamente.';
            
            if (xhr.status === 403) {
                mensajeError = 'Acceso denegado. Por favor, verifique su sesión e intente nuevamente.';
            } else if (xhr.status === 401) {
                mensajeError = 'Sesión expirada. Por favor, inicie sesión nuevamente.';
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
            $btnEnviar.prop('disabled', false).html(textoOriginal);
        }
    });
}
</script>

<?= $footer ?>