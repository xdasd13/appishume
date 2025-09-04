<?= $header ?>
<div class="page-inner">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Registrar Nuevo Pago</div>
                </div>
                <form method="POST" action="<?= base_url('/controlpagos/guardar') ?>" id="formPago">
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
                                    <select class="form-control" id="idtipopago" name="idtipopago" required>
                                        <option value="">Seleccionar Tipo</option>
                                        <option value="1">Efectivo</option>
                                        <option value="2">Transferencia Bancaria</option>
                                        <option value="3">Tarjeta de Crédito</option>
                                        <option value="4">Cheque</option>
                                        <option value="5">Yape/Plin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
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
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-circle"></i> Guardar Pago
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
        
        // Calcular saldos cuando cambia el contrato o la amortización
        $('#idcontrato, #amortizacion').on('change keyup', function() {
            calcularSaldos();
        });
        
        function calcularSaldos() {
            const contratoId = $('#idcontrato').val();
            const amortizacion = parseFloat($('#amortizacion').val()) || 0;
            
            if (contratoId) {
                // Aquí harías una llamada AJAX para obtener el saldo actual del contrato
                // Por ahora usamos un valor estático para demostración
                const saldoActual = 2800.00; // Este valor vendría del servidor
                const nuevaDeuda = saldoActual - amortizacion;
                
                $('#saldo_actual').val(saldoActual.toFixed(2));
                $('#nueva_deuda').val(nuevaDeuda.toFixed(2));
                
                // Cambiar color según el estado de la deuda
                if (nuevaDeuda < 0) {
                    $('#nueva_deuda').addClass('text-danger').removeClass('text-success');
                } else if (nuevaDeuda === 0) {
                    $('#nueva_deuda').addClass('text-success').removeClass('text-danger');
                } else {
                    $('#nueva_deuda').removeClass('text-danger text-success');
                }
            } else {
                $('#saldo_actual').val('');
                $('#nueva_deuda').val('');
            }
        }
        
        // Validación del formulario
        $('#formPago').on('submit', function(e) {
            const amortizacion = parseFloat($('#amortizacion').val()) || 0;
            const nuevaDeuda = parseFloat($('#nueva_deuda').val()) || 0;
            
            if (amortizacion <= 0) {
                e.preventDefault();
                alert('La amortización debe ser mayor a cero.');
                return false;
            }
            
            if (nuevaDeuda < 0) {
                e.preventDefault();
                alert('La amortización no puede ser mayor al saldo actual.');
                return false;
            }
        });
    });
</script>

<style>
    .select2-container--bootstrap .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: 0.375rem 0.75rem;
    }
    .text-success {
        color: #28a745 !important;
        font-weight: bold;
    }
    .text-danger {
        color: #dc3545 !important;
        font-weight: bold;
    }
    .text-warning {
        color: #ffc107 !important;
        font-weight: bold;
    }
</style>

<?= $footer ?>