[file name]: crear.php
[file content begin]
<?php
// Función helper para obtener fecha y hora de Perú
function getPeruDateTime() {
    $originalTimezone = date_default_timezone_get();
    date_default_timezone_set('America/Lima');
    $peruDateTime = date('Y-m-d H:i:s');
    date_default_timezone_set($originalTimezone);
    return $peruDateTime;
}

function getPeruDateTimeFormatted() {
    $originalTimezone = date_default_timezone_get();
    date_default_timezone_set('America/Lima');
    $peruDateTime = date('d/m/Y H:i');
    date_default_timezone_set($originalTimezone);
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
        
        // Validar campos condicionales
        var numTransaccion = $('#numtransaccion').val();
        var comprobante = $('#comprobante')[0].files[0];
        
        var errores = [];
        
        if (!contrato) errores.push('Seleccione un contrato');
        if (!tipoPago) errores.push('Seleccione un tipo de pago');
        if (amortizacion <= 0) errores.push('Ingrese un monto de amortización válido (mayor a 0)');
        if (!fechaHora) errores.push('Seleccione fecha y hora');
        
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
        
        // Mostrar confirmación
        Swal.fire({
            title: '¿Confirmar registro de pago?',
            html: '<div class="text-left">' +
                  '<p><strong>Contrato:</strong> ' + $('#idcontrato option:selected').text() + '</p>' +
                  '<p><strong>Tipo de Pago:</strong> ' + $('#idtipopago option:selected').text() + '</p>' +
                  '<p><strong>Amortización:</strong> S/ ' + amortizacion.toFixed(2) + '</p>' +
                  '<p><strong>Nuevo Saldo:</strong> S/ ' + (saldoActual - amortizacion).toFixed(2) + '</p>' +
                  '<p><strong>Fecha/Hora:</strong> ' + fechaHora + '</p>' +
                  (!tipoPagoTexto.includes('efectivo') ? '<p><strong>Transacción:</strong> ' + numTransaccion + '</p>' : '') +
                  '</div>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, registrar pago',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    // Enviar formulario
                    $(this).unbind('submit').submit();
                    resolve();
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar notificación de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Procesando',
                    text: 'El pago se está registrando...',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });
});
</script>

