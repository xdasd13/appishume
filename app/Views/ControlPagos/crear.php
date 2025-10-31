[file name]: crear.php
[file content begin]
<?php
// Función helper para obtener fecha y hora de Perú
function getPeruDateTime() {
    date_default_timezone_set('America/Lima');
    $peruDateTime = date('Y-m-d H:i:s');
    date_default_timezone_set('America/Lima');
    return $peruDateTime;
}

function getPeruDateTimeFormatted() {
    date_default_timezone_set('America/Lima');
    $peruDateTime = date('d/m/Y H:i');
    date_default_timezone_set('America/Lima');
    return $peruDateTime;
    
}
?>

<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ControlPagos-crear.css') ?>">
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-3d animate__animated animate__fadeIn">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Registrar Nuevo Pago</h4>
                        <a href="<?= base_url('/controlpagos') ?>" class="btn btn-secondary btn-round ml-auto">
                            <i class="fas fa-arrow-left mr-2"></i> Volver al Listado
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('/controlpagos/guardar') ?>" method="post" enctype="multipart/form-data" id="pagoForm">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="idcontrato">Contrato *</label>
                                    <select class="form-control" id="idcontrato" name="idcontrato" required>
                                        <option value="">Seleccione un contrato</option>
                                        <?php if (!empty($contratos)): ?>
                                            <?php foreach ($contratos as $contrato): ?>
                                                <option value="<?= $contrato['idcontrato'] ?>" 
                                                    <?= (isset($contrato_seleccionado) && $contrato_seleccionado == $contrato['idcontrato']) ? 'selected' : '' ?>>
                                                    Contrato #<?= $contrato['idcontrato'] ?> - 
                                                    <?= !empty($contrato['nombres']) ? 
                                                        $contrato['nombres'] . ' ' . $contrato['apellidos'] : 
                                                        $contrato['razonsocial'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <small class="form-text text-muted">Seleccione el contrato al que se aplicará el pago</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="idtipopago">Tipo de Pago *</label>
                                    <select class="form-control" id="idtipopago" name="idtipopago" required>
                                        <option value="">Seleccione tipo de pago</option>
                                        <?php if (!empty($tipospago)): ?>
                                            <?php foreach ($tipospago as $tipo): ?>
                                                <option value="<?= $tipo['idtipopago'] ?>"><?= $tipo['tipopago'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="monto_total">Monto Total del Contrato</label>
                                    <input type="text" class="form-control" id="monto_total" readonly 
                                        value="<?= (isset($info_contrato_precargada) && $info_contrato_precargada) ? 'S/ ' . number_format($info_contrato_precargada['monto_total'], 2) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="saldo_actual">Saldo Actual</label>
                                    <input type="text" class="form-control" id="saldo_actual" readonly 
                                        value="<?= (isset($info_contrato_precargada) && $info_contrato_precargada) ? 'S/ ' . number_format($info_contrato_precargada['saldo_actual'], 2) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="amortizacion">Amortización (S/) *</label>
                                    <input type="number" class="form-control" id="amortizacion" name="amortizacion" 
                                           step="0.01" min="0.01" required>
                                    <small class="form-text text-muted">Ingrese el monto a pagar</small>
                                </div>
                            </div>
                        </div>

                        <!-- Campos condicionales para tipo de pago -->
                        <div class="row" id="campos-transferencia">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numtransaccion">Número de Transacción/Referencia *</label>
                                    <input type="text" class="form-control" id="numtransaccion" name="numtransaccion">
                                    <small class="form-text text-muted">Ingrese el número de transacción bancaria o referencia de pago</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="comprobante">Comprobante de Pago *</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="comprobante" name="comprobante" 
                                               accept=".png,.jpg,.jpeg,.pdf">
                                        <label class="custom-file-label" for="comprobante">Seleccionar archivo</label>
                                    </div>
                                    <small class="form-text text-muted">Formatos permitidos: PNG, JPG, JPEG, PDF (Máx. 2MB)</small>
                                    
                                    <!-- Previsualización del comprobante -->
                                    <div id="preview-container" class="mt-3" style="display: none;">
                                        <h6>Vista previa del comprobante:</h6>
                                        <div id="preview-content" class="border p-3 rounded">
                                            <!-- Aquí se mostrará la previsualización -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos para identificar al pagador -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dni_pagador">DNI del Pagador *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="dni_pagador" name="dni_pagador" 
                                               maxlength="8" pattern="[0-9]{8}" required 
                                               placeholder="Ingrese 8 dígitos">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" id="btnValidarDni">
                                                <i class="fas fa-search mr-1"></i> Validar
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Ingrese el DNI de la persona que realiza el pago</small>
                                    <div id="dni-feedback" class="mt-2"></div>
                                    <div id="privacy-notice-pagador" class="alert alert-info mt-2" style="display: none;">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <small>Los datos fueron obtenidos de RENIEC y están protegidos por la Ley de Protección de Datos Personales</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre_pagador">Nombre Completo del Pagador *</label>
                                    <input type="text" class="form-control" id="nombre_pagador" name="nombre_pagador" 
                                           required readonly style="background-color: #f8f9fa;">
                                    <small class="form-text text-muted">Se completará automáticamente al validar el DNI</small>
                                    <input type="hidden" id="nombre_pagador_hidden" name="nombre_pagador_hidden">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fechahora">Fecha y Hora del Pago *</label>
                                    <input type="text" class="form-control" id="fechahora" name="fechahora" 
                                           value="<?= getPeruDateTimeFormatted() ?>" readonly style="background-color: #f8f9fa;">
                                    <input type="hidden" name="fechahora_hidden" value="<?= getPeruDateTime() ?>">
                                    <small class="form-text text-muted">Hora de Perú (UTC-5)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen del pago mejorado -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div id="resumen-pago" class="card card-info" style="display: none;">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0"><i class="fas fa-calculator mr-2"></i>Resumen del Pago</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <p><strong>Monto Total:</strong><br>
                                                <span class="h5 text-primary">S/ <span id="resumen-monto">0.00</span></span></p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Saldo Actual:</strong><br>
                                                <span class="h5 text-warning">S/ <span id="resumen-saldo">0.00</span></span></p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Amortización:</strong><br>
                                                <span class="h5 text-success">S/ <span id="resumen-amortizacion">0.00</span></span></p>
                                            </div>
                                            <div class="col-md-3">
                                                <p><strong>Nuevo Saldo:</strong><br>
                                                <span class="h5" id="resumen-nuevo-saldo-text">S/ <span id="resumen-nuevo-saldo">0.00</span></span></p>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <div id="alerta-completo" class="alert alert-success" style="display: none;">
                                                    <i class="fas fa-check-circle mr-2"></i>¡El contrato quedará completamente pagado!
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Importante:</strong> Todos los campos marcados con (*) son obligatorios. 
                                    Verifique que toda la información sea correcta antes de registrar el pago.
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success btn-lg btn-modern mr-3">
                                    <i class="fas fa-check-circle mr-2"></i> Registrar Pago
                                </button>
                                <a href="<?= base_url('/controlpagos') ?>" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-times mr-2"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer ?>

<!-- Scripts para la funcionalidad del formulario -->
<script>
$(document).ready(function() {
    // Validación de DNI del pagador
    let dniValidationInProgress = false;
    let lastValidatedDni = '';

    // Validar DNI al hacer clic en el botón
    $('#btnValidarDni').on('click', function() {
        const dni = $('#dni_pagador').val().trim();
        validateDniPagador(dni);
    });

    // Validar DNI al presionar Enter en el campo
    $('#dni_pagador').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const dni = $(this).val().trim();
            validateDniPagador(dni);
        }
    });

    // Función para validar DNI del pagador
    function validateDniPagador(dni) {
        // Validación básica
        if (!dni) {
            Swal.fire({
                icon: 'warning',
                title: 'DNI requerido',
                text: 'Por favor, ingrese el DNI del pagador',
                timer: 3000,
                showConfirmButton: false
            });
            $('#dni_pagador').focus();
            return;
        }

        // Validar formato
        if (!/^\d{8}$/.test(dni)) {
            Swal.fire({
                icon: 'error',
                title: 'Formato inválido',
                text: 'El DNI debe tener exactamente 8 dígitos numéricos',
                timer: 3000,
                showConfirmButton: false
            });
            $('#dni_pagador').removeClass('is-valid').addClass('is-invalid');
            return;
        }

        // Si ya se validó este DNI, no validar de nuevo
        if (lastValidatedDni === dni) {
            return;
        }

        if (dniValidationInProgress) {
            return;
        }

        dniValidationInProgress = true;
        const $btn = $('#btnValidarDni');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Validando...');

        $.ajax({
            url: '<?= base_url('/controlpagos/validarDniPagador') ?>',
            type: 'POST',
            data: {
                dni: dni,
                csrf_test_name: $('input[name="csrf_test_name"]').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // DNI válido
                    $('#dni_pagador').removeClass('is-invalid').addClass('is-valid');
                    
                    // Completar nombre
                    const nombreCompleto = response.data.nombres + ' ' + response.data.apellidos_completos;
                    $('#nombre_pagador').val(nombreCompleto).removeClass('is-invalid').addClass('is-valid');
                    $('#nombre_pagador_hidden').val(nombreCompleto);
                    
                    // Mostrar aviso de privacidad
                    $('#privacy-notice-pagador').show();
                    
                    // Mostrar mensaje de éxito
                    $('#dni-feedback').html(
                        '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-check-circle mr-2"></i>DNI válido: ' + nombreCompleto +
                        '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
                        '</div>'
                    );
                    
                    lastValidatedDni = dni;
                } else {
                    // DNI inválido
                    $('#dni_pagador').removeClass('is-valid').addClass('is-invalid');
                    $('#nombre_pagador').val('').removeClass('is-valid');
                    $('#nombre_pagador_hidden').val('');
                    $('#privacy-notice-pagador').hide();
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'DNI no encontrado',
                        text: response.message || 'El DNI no fue encontrado en RENIEC',
                        confirmButtonColor: '#dc3545'
                    });
                    
                    $('#dni-feedback').html(
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-times-circle mr-2"></i>' + (response.message || 'DNI no válido') +
                        '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
                        '</div>'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servicio de validación de DNI',
                    confirmButtonColor: '#dc3545'
                });
                $('#dni_pagador').removeClass('is-valid').addClass('is-invalid');
                $('#dni-feedback').html(
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    '<i class="fas fa-exclamation-triangle mr-2"></i>Error de conexión' +
                    '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
                    '</div>'
                );
            },
            complete: function() {
                dniValidationInProgress = false;
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    }
    // Función para mostrar/ocultar campos según tipo de pago
    function toggleCamposTipoPago() {
        var tipoPago = $('#idtipopago option:selected').text().toLowerCase();
        var camposTransferencia = $('#campos-transferencia');
        
        if (tipoPago.includes('efectivo')) {
            camposTransferencia.hide();
            $('#numtransaccion').removeAttr('required');
            $('#comprobante').removeAttr('required');
        } else {
            camposTransferencia.show();
            $('#numtransaccion').attr('required', 'required');
            $('#comprobante').attr('required', 'required');
        }
    }

    // Inicializar estado de campos
    toggleCamposTipoPago();

    // Escuchar cambios en el tipo de pago
    $('#idtipopago').on('change', function() {
        toggleCamposTipoPago();
    });

    // Si hay un contrato pre-seleccionado, cargar su información automáticamente
    <?php if (isset($contrato_seleccionado) && $contrato_seleccionado): ?>
        console.log('Cargando información del contrato pre-seleccionado: <?= $contrato_seleccionado ?>');
        
        // Si ya tenemos información precargada, usarla
        <?php if (isset($info_contrato_precargada) && $info_contrato_precargada): ?>
            console.log('Usando información precargada del contrato');
            $('#amortizacion').attr('max', <?= $info_contrato_precargada['saldo_actual'] ?>);
            updateResumen();
        <?php else: ?>
            // Si no hay información precargada, hacer petición AJAX
            $('#idcontrato').trigger('change');
        <?php endif; ?>
    <?php endif; ?>

    // Actualizar nombre del archivo en el input file
    $('#comprobante').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
        
        // Mostrar previsualización
        previewFile(this);
    });

    // Previsualización de archivos
    function previewFile(input) {
        var previewContainer = $('#preview-container');
        var previewContent = $('#preview-content');
        
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var reader = new FileReader();
            
            previewContainer.show();
            previewContent.html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Cargando vista previa...</div>');
            
            reader.onload = function(e) {
                if (file.type.includes('image')) {
                    previewContent.html('<img src="' + e.target.result + '" class="img-fluid" style="max-height: 300px;" alt="Vista previa del comprobante">');
                } else if (file.type === 'application/pdf') {
                    previewContent.html(`
                        <div class="text-center">
                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                            <p><strong>Archivo PDF:</strong> ${file.name}</p>
                            <p>Tamaño: ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                            <a href="${e.target.result}" target="_blank" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-external-link-alt mr-1"></i> Ver PDF
                            </a>
                        </div>
                    `);
                } else {
                    previewContent.html('<p class="text-muted">No se puede previsualizar este tipo de archivo</p>');
                }
            };
            
            reader.readAsDataURL(file);
        } else {
            previewContainer.hide();
        }
    }

    // Cargar información del contrato cuando se selecciona
    $('#idcontrato').on('change', function() {
        var idcontrato = $(this).val();
        
        if (idcontrato) {
            $('#monto_total, #saldo_actual').val('Cargando...');
            
            $.get('<?= base_url('/controlpagos/infoContrato/') ?>' + idcontrato)
            .done(function(data) {
                if (data.error) {
                    $('#monto_total, #saldo_actual').val('Error: ' + data.error);
                    console.error('Error del servidor:', data.error);
                } else {
                    $('#monto_total').val('S/ ' + parseFloat(data.monto_total || 0).toFixed(2));
                    $('#saldo_actual').val('S/ ' + parseFloat(data.saldo_actual || 0).toFixed(2));
                    $('#amortizacion').attr('max', data.saldo_actual || 0);
                    updateResumen();
                    console.log('Datos cargados:', data);
                }
            })
            .fail(function(xhr, status, error) {
                $('#monto_total, #saldo_actual').val('Error al cargar');
                console.error('Error AJAX:', status, error);
                console.error('Respuesta:', xhr.responseText);
            });
        } else {
            $('#monto_total, #saldo_actual').val('');
            $('#amortizacion').removeAttr('max');
            $('#resumen-pago').hide();
        }
    });
    
    // Cargar información si ya hay un contrato seleccionado
    $(document).ready(function() {
        if ($('#idcontrato').val()) {
            $('#idcontrato').trigger('change');
        }
    });

    // Función para actualizar el resumen
    function updateResumen() {
        var amortizacion = parseFloat($('#amortizacion').val()) || 0;
        var saldoActual = parseFloat($('#saldo_actual').val().replace('S/ ', '')) || 0;
        var montoTotal = parseFloat($('#monto_total').val().replace('S/ ', '')) || 0;
        
        if (saldoActual > 0) {
            $('#resumen-monto').text(montoTotal.toFixed(2));
            $('#resumen-saldo').text(saldoActual.toFixed(2));
            $('#resumen-amortizacion').text(amortizacion.toFixed(2));
            
            var nuevoSaldo = saldoActual - amortizacion;
            $('#resumen-nuevo-saldo').text(nuevoSaldo.toFixed(2));
            
            // Cambiar color según el nuevo saldo
            var nuevoSaldoElement = $('#resumen-nuevo-saldo-text');
            nuevoSaldoElement.removeClass('text-success text-warning text-danger');
            
            if (nuevoSaldo === 0) {
                nuevoSaldoElement.addClass('text-success');
                $('#alerta-completo').show();
            } else if (nuevoSaldo > 0) {
                nuevoSaldoElement.addClass('text-warning');
                $('#alerta-completo').hide();
            } else {
                nuevoSaldoElement.addClass('text-danger');
                $('#alerta-completo').hide();
            }
            
            $('#resumen-pago').show();
        }
    }

    // Validar amortización en tiempo real
    $('#amortizacion').on('input', function() {
        var amortizacion = parseFloat($(this).val()) || 0;
        var saldoActual = parseFloat($('#saldo_actual').val().replace('S/ ', '')) || 0;
        
        if (amortizacion > saldoActual) {
            $(this).addClass('is-invalid');
            $('#resumen-pago').hide();
            Swal.fire({
                icon: 'warning',
                title: 'Monto excedido',
                text: 'La amortización no puede ser mayor al saldo actual',
                timer: 3000,
                showConfirmButton: false
            });
        } else if (amortizacion <= 0) {
            $(this).addClass('is-invalid');
            $('#resumen-pago').hide();
        } else {
            $(this).removeClass('is-invalid');
            updateResumen();
        }
    });

    // Validar formulario antes de enviar
    $('#pagoForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validar campos obligatorios
        var contrato = $('#idcontrato').val();
        var tipoPago = $('#idtipopago').val();
        var amortizacion = parseFloat($('#amortizacion').val()) || 0;
        var fechaHora = $('#fechahora').val();
        var tipoPagoTexto = $('#idtipopago option:selected').text().toLowerCase();
        var dniPagador = $('#dni_pagador').val().trim();
        var nombrePagador = $('#nombre_pagador').val().trim();
        
        // Validar campos condicionales
        var numTransaccion = $('#numtransaccion').val();
        var comprobante = $('#comprobante')[0].files[0];
        
        var errores = [];
        
        if (!contrato) errores.push('Seleccione un contrato');
        if (!tipoPago) errores.push('Seleccione un tipo de pago');
        if (amortizacion <= 0) errores.push('Ingrese un monto de amortización válido (mayor a 0)');
        if (!fechaHora) errores.push('Seleccione fecha y hora');
        if (!dniPagador || dniPagador.length !== 8) errores.push('Debe validar el DNI del pagador (8 dígitos)');
        if (!nombrePagador) errores.push('Debe validar el DNI del pagador para obtener su nombre');
        
        // Validar campos específicos según tipo de pago
        if (!tipoPagoTexto.includes('efectivo')) {
            if (!numTransaccion) errores.push('Ingrese el número de transacción/referencia');
            if (!comprobante) errores.push('Seleccione un comprobante de pago');
        }
        
        if (errores.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Campos incompletos',
                html: 'Por favor, complete todos los campos obligatorios:<br><br>' +
                      '<ul class="text-left">' +
                      errores.map(error => '<li>' + error + '</li>').join('') +
                      '</ul>',
                confirmButtonColor: '#dc3545'
            });
            return false;
        }
        
        var saldoActual = parseFloat($('#saldo_actual').val().replace('S/ ', '')) || 0;
        
        if (amortizacion > saldoActual) {
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'La amortización no puede ser mayor al saldo actual del contrato'
            });
            return false;
        }
        
        if (amortizacion <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'La amortización debe ser mayor a cero'
            });
            return false;
        }
        
        // Guardar referencia del formulario
        var $form = $('#pagoForm');
        
        // Mostrar confirmación
        Swal.fire({
            title: '¿Confirmar registro de pago?',
            html: '<div class="text-left">' +
                  '<p><strong>Contrato:</strong> ' + $('#idcontrato option:selected').text() + '</p>' +
                  '<p><strong>Tipo de Pago:</strong> ' + $('#idtipopago option:selected').text() + '</p>' +
                  '<p><strong>Amortización:</strong> S/ ' + amortizacion.toFixed(2) + '</p>' +
                  '<p><strong>Nuevo Saldo:</strong> S/ ' + (saldoActual - amortizacion).toFixed(2) + '</p>' +
                  '<p><strong>Fecha/Hora:</strong> ' + fechaHora + '</p>' +
                  '<p><strong>DNI Pagador:</strong> ' + dniPagador + '</p>' +
                  '<p><strong>Nombre Pagador:</strong> ' + nombrePagador + '</p>' +
                  (!tipoPagoTexto.includes('efectivo') ? '<p><strong>Transacción:</strong> ' + numTransaccion + '</p>' : '') +
                  '</div>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, registrar pago',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar indicador de carga
                Swal.fire({
                    title: 'Procesando...',
                    html: 'Registrando el pago, por favor espere.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Crear un formulario temporal y enviarlo
                var formData = new FormData($form[0]);
                
                // Asegurar que el token CSRF se incluya
                var csrfToken = $('input[name="csrf_test_name"]').val();
                if (csrfToken) {
                    formData.append('csrf_test_name', csrfToken);
                }
                
                // Log para debugging
                console.log('Enviando formulario a:', $form.attr('action'));
                console.log('CSRF Token:', csrfToken);
                console.log('FormData contiene:', Array.from(formData.keys()));
                
                // Enviar el formulario mediante AJAX
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    beforeSend: function(xhr) {
                        console.log('Enviando petición AJAX...');
                        // Asegurar que el header se establezca
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    },
                    success: function(response) {
                        // Verificar si es JSON
                        try {
                            var jsonResponse = typeof response === 'string' ? JSON.parse(response) : response;
                            
                            if (jsonResponse.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: jsonResponse.message || 'El pago se registró correctamente',
                                    confirmButtonColor: '#28a745',
                                    timer: 2000,
                                    timerProgressBar: true
                                }).then(() => {
                                    window.location.href = jsonResponse.redirect || '<?= base_url('/controlpagos') ?>';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: jsonResponse.message || 'Error al registrar el pago',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        } catch(e) {
                            // Si no es JSON, probablemente es HTML (redirect)
                            if (response.includes('<!DOCTYPE') || response.includes('<html')) {
                                window.location.href = '<?= base_url('/controlpagos') ?>';
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: 'El pago se registró correctamente',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    window.location.href = '<?= base_url('/controlpagos') ?>';
                                });
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        console.error('Status:', xhr.status);
                        console.error('Response:', xhr.responseText);
                        
                        var errorMsg = 'Error al registrar el pago.';
                        
                        // Intentar extraer el mensaje de error de la respuesta JSON
                        if (xhr.responseText) {
                            try {
                                var jsonResponse = JSON.parse(xhr.responseText);
                                if (jsonResponse.message) {
                                    errorMsg = jsonResponse.message;
                                }
                            } catch(e) {
                                // Si no es JSON, intentar extraer de HTML
                                try {
                                    var parser = new DOMParser();
                                    var doc = parser.parseFromString(xhr.responseText, 'text/html');
                                    var errorElement = doc.querySelector('.alert-danger, .error, [role="alert"]');
                                    if (errorElement) {
                                        errorMsg = errorElement.textContent.trim();
                                    }
                                } catch(e2) {
                                    // Si no se puede parsear, usar el mensaje por defecto
                                    if (xhr.status === 500) {
                                        errorMsg = 'Error interno del servidor. Por favor, verifique los logs del servidor.';
                                    } else if (xhr.status === 422) {
                                        errorMsg = 'Error de validación. Por favor, verifique todos los campos.';
                                    } else {
                                        errorMsg = 'Error al comunicarse con el servidor. Código: ' + xhr.status;
                                    }
                                }
                            }
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMsg,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    });
});
</script>

