<?= $header ?>
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-3d animate__animated animate__fadeIn">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Detalles del Pago #<?= $pago['idpagos'] ?></h4>
                        <div class="ml-auto">
                            <a href="<?= base_url('/controlpagos') ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-2"></i> Volver al Listado
                            </a>
                            <a href="<?= base_url('/controlpagos/generarVoucher/' . $pago['idpagos']) ?>" 
                               class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-receipt mr-2"></i> Generar Voucher
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-stats card-round card-3d">
                                <div class="card-header">
                                    <h4 class="card-title">Información del Pago</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">ID de Pago:</th>
                                            <td>#<?= $pago['idpagos'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Contrato:</th>
                                            <td>
                                                <a href="<?= base_url('/controlpagos/por-contrato/' . $pago['idcontrato']) ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    Contrato #<?= $pago['idcontrato'] ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Fecha y Hora:</th>
                                            <td><?= date('d/m/Y H:i:s', strtotime($pago['fechahora'])) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Saldo Anterior:</th>
                                            <td class="text-danger">S/ <?= number_format($pago['saldo'], 2) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Amortización:</th>
                                            <td class="text-success">S/ <?= number_format($pago['amortizacion'], 2) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Nuevo Saldo:</th>
                                            <td class="<?= $pago['deuda'] > 0 ? 'text-warning' : 'text-success' ?>">
                                                S/ <?= number_format($pago['deuda'], 2) ?>
                                                <?php if ($pago['deuda'] == 0): ?>
                                                    <span class="badge badge-success ml-2">¡PAGADO COMPLETO!</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tipo de Pago:</th>
                                            <td><?= $tipo_pago['tipopago'] ?? 'N/A' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Número de Transacción:</th>
                                            <td><?= !empty($pago['numtransaccion']) ? $pago['numtransaccion'] : 'N/A' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Registrado por:</th>
                                            <td><?= $pago['nombreusuario'] ?? 'N/A' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Comprobante:</th>
                                            <td>
                                                <?php if (!empty($pago['comprobante'])): ?>
                                                    <a href="<?= base_url('/controlpagos/descargarComprobante/' . $pago['idpagos']) ?>" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-download mr-2"></i> Descargar Comprobante
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Sin comprobante</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-stats card-round card-3d">
                                <div class="card-header">
                                    <h4 class="card-title">Información del Cliente</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Cliente:</th>
                                            <td>
                                                <?php if (!empty($info_contrato['nombres'])): ?>
                                                    <?= $info_contrato['nombres'] . ' ' . $info_contrato['apellidos'] ?>
                                                <?php else: ?>
                                                    <?= $info_contrato['razonsocial'] ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Documento:</th>
                                            <td>
                                                <?php if (!empty($info_contrato['nrodocumento'])): ?>
                                                    <?= $info_contrato['nrodocumento'] ?>
                                                <?php else: ?>
                                                    <?= $info_contrato['ruc'] ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td><?= $info_contrato['email'] ?? 'N/A' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Teléfono:</th>
                                            <td><?= $info_contrato['telefono'] ?? 'N/A' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Monto Total Contrato:</th>
                                            <td class="text-primary">S/ <?= number_format($info_contrato['monto_total'], 2) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Total Pagado:</th>
                                            <td class="text-success">S/ <?= number_format($info_contrato['monto_total'] - $pago['deuda'], 2) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Estado Actual:</th>
                                            <td>
                                                <?php if ($pago['deuda'] == 0): ?>
                                                    <span class="badge badge-success badge-3d">PAGADO COMPLETO</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning badge-3d">PENDIENTE: S/ <?= number_format($pago['deuda'], 2) ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de pagos del contrato -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-3d">
                                <div class="card-header">
                                    <h4 class="card-title">Historial de Pagos del Contrato #<?= $pago['idcontrato'] ?></h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover table-hover-3d">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Fecha/Hora</th>
                                                    <th>Saldo (S/)</th>
                                                    <th>Amortización (S/)</th>
                                                    <th>Deuda (S/)</th>
                                                    <th>Tipo Pago</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($historial_pagos) && is_array($historial_pagos)): ?>
                                                    <?php foreach ($historial_pagos as $historial): ?>
                                                        <tr class="<?= $historial['idpagos'] == $pago['idpagos'] ? 'table-active' : '' ?>">
                                                            <td><?= $historial['idpagos'] ?></td>
                                                            <td><?= date('d/m/Y H:i', strtotime($historial['fechahora'])) ?></td>
                                                            <td class="text-danger"><?= number_format($historial['saldo'], 2) ?></td>
                                                            <td class="text-success"><?= number_format($historial['amortizacion'], 2) ?></td>
                                                            <td class="<?= $historial['deuda'] > 0 ? 'text-warning' : 'text-success' ?>">
                                                                <?= number_format($historial['deuda'], 2) ?>
                                                            </td>
                                                            <td><?= $historial['tipopago'] ?></td>
                                                            <td>
                                                                <div class="form-button-action">
                                                                    <a href="<?= base_url('/controlpagos/ver/' . $historial['idpagos']) ?>" 
                                                                       class="btn btn-link btn-primary btn-sm" 
                                                                       data-toggle="tooltip" 
                                                                       title="Ver detalles">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                    <?php if (!empty($historial['comprobante'])): ?>
                                                                        <a href="<?= base_url('/controlpagos/descargarComprobante/' . $historial['idpagos']) ?>" 
                                                                           class="btn btn-link btn-info btn-sm" 
                                                                           data-toggle="tooltip" 
                                                                           title="Descargar comprobante">
                                                                            <i class="fas fa-download"></i>
                                                                        </a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center">No se encontraron pagos para este contrato</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer ?>

<script>
$(document).ready(function() {
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>