<?= $header ?>
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Registrar Nuevo Pago</div>
                </div>
                <form method="POST" action="<?= base_url('/controlpagos/guardar') ?>" id="formPago" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="idcontrato">Contrato *</label>
                                    <select class="form-control select2" id="idcontrato" name="idcontrato" required>
                                        <option value="">Seleccionar Contrato</option>
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
                                    <select class="form-control select2" id="idtipopago" name="idtipopago" required>
                                        <option value="">Seleccionar Tipo</option>
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
                        <div class="row" id="info-contrato" style="display: none;">
                            <div class="col-md-12">
                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h4 class="card-title">Información del Contrato</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Monto Total del Contrato:</label>
                                                    <p class="form-control-static font-weight-bold" id="info-monto-total">S/ 0.00</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Pagado hasta ahora:</label>
                                                    <p class="form-control-static" id="info-pagado">S/ 0.00</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Porcentaje Completado:</label>
                                                    <div class="progress" style="height: 20px;">
                                                        <div id="info-progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="saldo_actual">Saldo Actual (S/)</label>
                                    <input type="text" class="form-control" id="saldo_actual" readonly placeholder="0.00">
                                    <small class="form-text text-muted">Se calculará automáticamente</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="amortizacion">Amortización (S/) *</label>
                                    <input type="number" step="0.01" min="0" class="form-control" 
                                           id="amortizacion" name="amortizacion" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nueva_deuda">Nueva Deuda (S/)</label>
                                    <input type="text" class="form-control" id="nueva_deuda" readonly placeholder="0.00">
                                    <small class="form-text text-muted">Se calculará automáticamente</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numtransaccion">Número de Transacción</label>
                                    <input type="text" class="form-control" id="numtransaccion" 
                                           name="numtransaccion" placeholder="Ej: TXN20240120001">
                                    <small class="form-text text-muted">Opcional para pagos electrónicos</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fechahora">Fecha y Hora *</label>
                                    <input type="datetime-local" class="form-control" 
                                           id="fechahora" name="fechahora" required 
                                           value="<?= date('Y-m-d\TH:i') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="comprobante">Comprobante de Pago *</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="comprobante" name="comprobante" accept=".jpg,.jpeg,.png,.pdf" required>
                                        <label class="custom-file-label" for="comprobante" id="comprobante-label">Seleccionar archivo (JPG, PNG, PDF - Máx. 2MB)</label>
                                    </div>
                                    <small class="form-text text-muted">Formatos aceptados: JPG, PNG, PDF. Tamaño máximo: 2MB</small>
                                    <div id="preview-comprobante" class="mt-2" style="display: none;">
                                        <img id="preview-image" src="#" alt="Vista previa" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-success btn-animate">
                            <span class="btn-label">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            Guardar Pago
                        </button>
                        <a href="<?= base_url('/controlpagos') ?>" class="btn btn-danger">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inicializar select2
        $('.select2').select2({
            theme: 'bootstrap'
        });
        
        // Cargar información del contrato cuando se selecciona uno
        $('#idcontrato').on('change', function() {
            const contratoId = $(this).val();
            
            if (contratoId) {
                // Mostrar loading
                $('#info-contrato').hide();
                $('#saldo_actual').val('Cargando...');
                
                // Hacer petición AJAX para obtener información del contrato
                $.get('<?= base_url('/controlpagos/infoContrato/') ?>' + contratoId, function(data) {
                    const saldoActual = parseFloat(data.saldo_actual);
                    const montoTotal = parseFloat(data.monto_total);
                    const pagado = montoTotal - saldoActual;
                    const porcentaje = montoTotal > 0 ? (pagado / montoTotal * 100) : 0;
                    
                    // Actualizar campos
                    $('#saldo_actual').val(saldoActual.toFixed(2));
                    $('#amortizacion').val('');
                    $('#nueva_deuda').val(saldoActual.toFixed(2));
                    
                    // Actualizar información del contrato
                    $('#info-monto-total').text('S/ ' + montoTotal.toFixed(2));
                    $('#info-pagado').text('S/ ' + pagado.toFixed(2));
                    $('#info-progress-bar')
                        .css('width', porcentaje + '%')
                        .attr('aria-valuenow', porcentaje)
                        .text(porcentaje.toFixed(0) + '%');
                    
                    // Cambiar color de la barra de progreso según el porcentaje
                    if (porcentaje < 50) {
                        $('#info-progress-bar').removeClass('bg-success bg-warning').addClass('bg-danger');
                    } else if (porcentaje < 100) {
                        $('#info-progress-bar').removeClass('bg-success bg-danger').addClass('bg-warning');
                    } else {
                        $('#info-progress-bar').removeClass('bg-warning bg-danger').addClass('bg-success');
                    }
                    
                    // Mostrar información del contrato
                    $('#info-contrato').slideDown();
                    
                    // Cambiar color según el saldo
                    if (saldoActual > 0) {
                        $('#saldo_actual').addClass('text-danger').removeClass('text-success');
                    } else {
                        $('#saldo_actual').addClass('text-success').removeClass('text-danger');
                    }
                }).fail(function() {
                    alert('Error al cargar la información del contrato');
                });
            } else {
                $('#info-contrato').slideUp();
                $('#saldo_actual').val('');
                $('#nueva_deuda').val('');
            }
        });
        
        // Calcular nueva deuda cuando cambia la amortización
        $('#amortizacion').on('input', function() {
            const saldoActual = parseFloat($('#saldo_actual').val()) || 0;
            const amortizacion = parseFloat($(this).val()) || 0;
            
            if (amortizacion > saldoActual) {
                $(this).addClass('is-invalid');
                $('#nueva_deuda').val('ERROR: La amortización no puede ser mayor al saldo');
                $('#nueva_deuda').addClass('text-danger');
            } else {
                $(this).removeClass('is-invalid');
                const nuevaDeuda = saldoActual - amortizacion;
                $('#nueva_deuda').val(nuevaDeuda.toFixed(2));
                
                // Cambiar color según la deuda
                if (nuevaDeuda > 0) {
                    $('#nueva_deuda').addClass('text-warning').removeClass('text-success');
                } else {
                    $('#nueva_deuda').addClass('text-success').removeClass('text-warning');
                }
            }
        });
        
        // Validación del formulario
        $('#formPago').on('submit', function(e) {
            const saldoActual = parseFloat($('#saldo_actual').val()) || 0;
            const amortizacion = parseFloat($('#amortizacion').val()) || 0;
            
            if (amortizacion > saldoActual) {
                e.preventDefault();
                alert('Error: La amortización no puede ser mayor al saldo actual del contrato.');
                $('#amortizacion').focus();
            }
        });
        
        // Preview de imagen para comprobante
        $('#comprobante').on('change', function() {
            const file = this.files[0];
            const preview = $('#preview-image');
            const previewContainer = $('#preview-comprobante');
            const label = $('#comprobante-label');
            
            // Actualizar label con el nombre del archivo
            if (file) {
                label.text(file.name);
                
                // Mostrar preview solo para imágenes
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.attr('src', e.target.result);
                        previewContainer.show();
                    }
                    
                    reader.readAsDataURL(file);
                } else {
                    previewContainer.hide();
                }
            } else {
                label.text('Seleccionar archivo (JPG, PNG, PDF - Máx. 2MB)');
                previewContainer.hide();
            }
        });
    });
</script>

<style>
    .select2-container--bootstrap .select2-selection {
        height: calc(2.25rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
    }
    
    .card-secondary {
        border-left: 4px solid #6c757d;
    }
    
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        transition: width 0.5s ease;
    }
    
    .form-control:read-only {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    
    .text-success {
        color: #28a745 !important;
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .text-warning {
        color: #ffc107 !important;
    }
    
    .custom-file-label::after {
        content: "Examinar";
    }
</style>

<?= $footer ?>