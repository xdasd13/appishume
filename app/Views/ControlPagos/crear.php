<?= $header ?>
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-3d">
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

                        <!-- Información del contrato seleccionado -->
                        <div class="row mb-4" id="contrato-info" style="display: none;">
                            <div class="col-md-12">
                                <div class="card card-info bg-info-gradient">
                                    <div class="card-body">
                                        <h4 class="mb-3">Información del Contrato Seleccionado</h4>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <p><strong>Monto Total del Contrato:</strong> <span id="monto-total">S/ 0.00</span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Saldo Actual:</strong> <span id="saldo-actual-display">S/ 0.00</span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Estado:</strong> <span id="estado-contrato" class="badge badge-warning">PENDIENTE</span></p>
                                            </div>
                                        </div>
                                        <div class="progress mt-3" style="height: 20px;">
                                            <div id="progress-bar" class="progress-bar progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="saldo_actual">Saldo Actual (S/)</label>
                                    <input type="text" class="form-control" id="saldo_actual" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="amortizacion">Amortización (S/) *</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="amortizacion" name="amortizacion" required>
                                    <small class="form-text text-muted">Monto a pagar en esta transacción</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nueva_deuda">Nueva Deuda (S/)</label>
                                    <input type="text" class="form-control" id="nueva_deuda" readonly>
                                    <small class="form-text text-muted">Saldo después del pago</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numtransaccion">Número de Transacción</label>
                                    <input type="text" class="form-control" id="numtransaccion" name="numtransaccion" placeholder="Opcional">
                                    <small class="form-text text-muted">Número de operación bancaria o referencia</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fechahora">Fecha y Hora *</label>
                                    <input type="datetime-local" class="form-control" id="fechahora" name="fechahora" required value="<?= date('Y-m-d\TH:i') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="comprobante">Comprobante de Pago</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="comprobante" name="comprobante" accept=".png,.jpg,.jpeg,.pdf">
                                        <label class="custom-file-label" for="comprobante">Seleccionar archivo (PNG, JPG, PDF)</label>
                                    </div>
                                    <small class="form-text text-muted">Tamaño máximo: 2MB. Formatos permitidos: PNG, JPG, JPEG, PDF</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-animate">
                                    <span class="btn-label">
                                        <i class="fas fa-save"></i>
                                    </span>
                                    Registrar Pago
                                </button>
                                <button type="reset" class="btn btn-secondary">Limpiar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer ?>

<script>
$(document).ready(function() {
    // Inicializar select2
    $('.select2').select2({
        theme: "bootstrap"
    });

    // Mostrar nombre de archivo seleccionado
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Cargar información del contrato cuando se selecciona
    $('#idcontrato').on('change', function() {
        var idcontrato = $(this).val();
        
        if (idcontrato) {
            // Mostrar sección de información del contrato
            $('#contrato-info').slideDown();
            
            // Mostrar loading
            $('#saldo_actual').val('Cargando...');
            $('#saldo-actual-display').text('Cargando...');
            $('#nueva_deuda').val('Cargando...');
            
            // Hacer petición AJAX para obtener información del contrato
            $.ajax({
                url: '<?= base_url('/controlpagos/infoContrato/') ?>' + idcontrato,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response) {
                        var saldoActual = parseFloat(response.saldo_actual);
                        var montoTotal = parseFloat(response.monto_total);
                        var porcentajePagado = ((montoTotal - saldoActual) / montoTotal) * 100;
                        
                        $('#saldo_actual').val('S/ ' + saldoActual.toFixed(2));
                        $('#saldo-actual-display').text('S/ ' + saldoActual.toFixed(2));
                        $('#monto-total').text('S/ ' + montoTotal.toFixed(2));
                        
                        // Actualizar barra de progreso
                        $('#progress-bar')
                            .css('width', porcentajePagado + '%')
                            .attr('aria-valuenow', porcentajePagado)
                            .text(porcentajePagado.toFixed(2) + '%');
                            
                        if (porcentajePagado == 100) {
                            $('#progress-bar').removeClass('bg-info').addClass('bg-success');
                            $('#estado-contrato')
                                .removeClass('badge-warning')
                                .addClass('badge-success')
                                .text('PAGADO COMPLETO');
                        } else {
                            $('#progress-bar').removeClass('bg-success').addClass('bg-info');
                            $('#estado-contrato')
                                .removeClass('badge-success')
                                .addClass('badge-warning')
                                .text('PENDIENTE');
                        }
                        
                        // Calcular nueva deuda cuando se ingrese la amortización
                        $('#amortizacion').trigger('input');
                    } else {
                        $('#saldo_actual').val('Error al cargar información');
                        $('#saldo-actual-display').text('Error al cargar información');
                    }
                },
                error: function() {
                    $('#saldo_actual').val('Error al cargar información');
                    $('#saldo-actual-display').text('Error al cargar información');
                }
            });
        } else {
            $('#contrato-info').slideUp();
            $('#saldo_actual').val('');
            $('#nueva_deuda').val('');
        }
    });

    // Calcular nueva deuda cuando cambia la amortización
    $('#amortizacion').on('input', function() {
        var saldoActualText = $('#saldo_actual').val();
        var saldoActual = parseFloat(saldoActualText.replace('S/ ', '')) || 0;
        var amortizacion = parseFloat($(this).val()) || 0;
        
        if (amortizacion > saldoActual) {
            $('#nueva_deuda').val('Error: La amortización excede el saldo');
            $('#nueva_deuda').addClass('text-danger');
            $('button[type="submit"]').prop('disabled', true);
            
            // Mostrar alerta
            Swal.fire({
                icon: 'error',
                title: 'Monto excedido',
                text: 'La amortización no puede ser mayor al saldo actual del contrato.',
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            var nuevaDeuda = saldoActual - amortizacion;
            $('#nueva_deuda').val('S/ ' + nuevaDeuda.toFixed(2));
            $('#nueva_deuda').removeClass('text-danger');
            $('button[type="submit"]').prop('disabled', false);
            
            // Mostrar mensaje de éxito si se paga completamente
            if (nuevaDeuda == 0 && amortizacion > 0) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Pago completo!',
                    text: 'Este pago completará el contrato.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        }
    });

    // Validar formulario antes de enviar
    $('#pagoForm').on('submit', function(e) {
        var saldoActualText = $('#saldo_actual').val();
        var saldoActual = parseFloat(saldoActualText.replace('S/ ', '')) || 0;
        var amortizacion = parseFloat($('#amortizacion').val()) || 0;
        
        if (amortizacion > saldoActual) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'La amortización no puede ser mayor al saldo actual del contrato.',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        if (amortizacion <= 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'La amortización debe ser mayor a cero.',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        // Mostrar loading
        Swal.fire({
            title: 'Procesando pago',
            text: 'Por favor espere...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
});
</script>