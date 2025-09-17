<?= $header ?>
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
                                    <select class="form-control select2" id="idcontrato" name="idcontrato" required>
                                        <option value="">Seleccione un contrato</option>
                                        <?php if (!empty($contratos)): ?>
                                            <?php foreach ($contratos as $contrato): ?>
                                                <option value="<?= $contrato['idcontrato'] ?>">
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
                                    <select class="form-control select2" id="idtipopago" name="idtipopago" required>
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
                                    <input type="text" class="form-control" id="monto_total" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="saldo_actual">Saldo Actual</label>
                                    <input type="text" class="form-control" id="saldo_actual" readonly>
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numtransaccion">Número de Transacción/Referencia</label>
                                    <input type="text" class="form-control" id="numtransaccion" name="numtransaccion">
                                    <small class="form-text text-muted">Ingrese el número de transacción bancaria o referencia de pago</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fechahora">Fecha y Hora del Pago *</label>
                                    <input type="datetime-local" class="form-control" id="fechahora" name="fechahora" 
                                           value="<?= date('Y-m-d\TH:i') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="comprobante">Comprobante de Pago</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="comprobante" name="comprobante" 
                                               accept=".png,.jpg,.jpeg,.pdf">
                                        <label class="custom-file-label" for="comprobante">Seleccionar archivo</label>
                                    </div>
                                    <small class="form-text text-muted">Formatos permitidos: PNG, JPG, JPEG, PDF (Máx. 2MB)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div id="resumen-pago" class="alert alert-info" style="display: none;">
                                        <h5>Resumen del Pago</h5>
                                        <p><strong>Monto Total:</strong> S/ <span id="resumen-monto">0.00</span></p>
                                        <p><strong>Saldo Actual:</strong> S/ <span id="resumen-saldo">0.00</span></p>
                                        <p><strong>Amortización:</strong> S/ <span id="resumen-amortizacion">0.00</span></p>
                                        <p><strong>Nuevo Saldo:</strong> S/ <span id="resumen-nuevo-saldo">0.00</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg btn-animate">
                                    <span class="btn-label">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                    Registrar Pago
                                </button>
                                <a href="<?= base_url('/controlpagos') ?>" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times-circle mr-2"></i> Cancelar
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
    // Inicializar select2
    $('.select2').select2({
        theme: "bootstrap"
    });

    // Actualizar nombre del archivo en el input file
    $('#comprobante').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });

    // Cargar información del contrato cuando se selecciona
    $('#idcontrato').on('change', function() {
        var idcontrato = $(this).val();
        
        if (idcontrato) {
            // Mostrar carga
            $('#monto_total, #saldo_actual').addClass('loading');
            
            // Hacer petición AJAX
            $.get('<?= base_url('/controlpagos/infoContrato/') ?>' + idcontrato, function(data) {
                $('#monto_total').val('S/ ' + parseFloat(data.monto_total).toFixed(2));
                $('#saldo_actual').val('S/ ' + parseFloat(data.saldo_actual).toFixed(2));
                
                // Establecer máximo para amortización
                $('#amortizacion').attr('max', data.saldo_actual);
                
                // Quitar clase de carga
                $('#monto_total, #saldo_actual').removeClass('loading');
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar la información del contrato'
                });
                $('#monto_total, #saldo_actual').removeClass('loading');
            });
        } else {
            $('#monto_total, #saldo_actual').val('');
            $('#amortizacion').removeAttr('max');
        }
    });

    // Validar amortización
    $('#amortizacion').on('input', function() {
        var amortizacion = parseFloat($(this).val()) || 0;
        var saldoActual = parseFloat($('#saldo_actual').val().replace('S/ ', '')) || 0;
        
        if (amortizacion > saldoActual) {
            $(this).addClass('is-invalid');
            $('#resumen-pago').hide();
        } else {
            $(this).removeClass('is-invalid');
            
            // Actualizar resumen
            if (saldoActual > 0) {
                $('#resumen-monto').text(parseFloat($('#monto_total').val().replace('S/ ', '') || 0).toFixed(2));
                $('#resumen-saldo').text(saldoActual.toFixed(2));
                $('#resumen-amortizacion').text(amortizacion.toFixed(2));
                $('#resumen-nuevo-saldo').text((saldoActual - amortizacion).toFixed(2));
                $('#resumen-pago').show();
            }
        }
    });

    // Validar formulario antes de enviar
    $('#pagoForm').on('submit', function(e) {
        var amortizacion = parseFloat($('#amortizacion').val()) || 0;
        var saldoActual = parseFloat($('#saldo_actual').val().replace('S/ ', '')) || 0;
        
        if (amortizacion > saldoActual) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'La amortización no puede ser mayor al saldo actual del contrato'
            });
            return false;
        }
        
        if (amortizacion <= 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'La amortización debe ser mayor a cero'
            });
            return false;
        }
        
        // Mostrar confirmación
        e.preventDefault();
        Swal.fire({
            title: '¿Confirmar registro de pago?',
            html: '<div class="text-left">' +
                  '<p><strong>Contrato:</strong> ' + $('#idcontrato option:selected').text() + '</p>' +
                  '<p><strong>Amortización:</strong> S/ ' + amortizacion.toFixed(2) + '</p>' +
                  '<p><strong>Nuevo Saldo:</strong> S/ ' + (saldoActual - amortizacion).toFixed(2) + '</p>' +
                  '</div>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, registrar pago',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar notificación de procesamiento
                Swal.fire({
                    title: 'Procesando pago',
                    text: 'Por favor espere...',
                    icon: 'info',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Enviar formulario
                $(this).unbind('submit').submit();
            }
        });
    });
});
</script>

<style>
.loading {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="%23f8f9fa" d="M0 0h100v100H0z"/><path d="M73 50c0-12.7-10.3-23-23-23S27 37.3 27 50m3.9 0c0-10.5 8.5-19.1 19.1-19.1S69.1 39.5 69.1 50" stroke="%23007bff" stroke-width="8"><animateTransform attributeName="transform" attributeType="XML" type="rotate" from="0 50 50" to="360 50 50" dur="1s" repeatCount="indefinite"/></path></svg>');
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px 20px;
}
</style>