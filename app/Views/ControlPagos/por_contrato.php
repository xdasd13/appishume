<?= $header ?>
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Pagos del Contrato #<?= $contrato['idcontrato'] ?></h4>
                        <div class="ml-auto">
                            <a href="<?= base_url('/controlpagos') ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Información del contrato -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h4 class="card-title">Información del Contrato</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Cliente:</label>
                                                <p class="form-control-static font-weight-bold">
                                                    <?= !empty($contrato['nombres']) ? 
                                                        $contrato['nombres'] . ' ' . $contrato['apellidos'] : 
                                                        $contrato['razonsocial'] ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Monto Total:</label>
                                                <p class="form-control-static font-weight-bold">S/ <?= number_format($contrato['monto_total'], 2) ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Estado:</label>
                                                <p class="form-control-static">
                                                    <?php if ($deuda_actual == 0): ?>
                                                        <span class="badge badge-success">PAGADO COMPLETAMENTE</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">PENDIENTE DE PAGO</span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Total Pagado:</label>
                                                <p class="form-control-static text-success font-weight-bold">S/ <?= number_format($total_pagado, 2) ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Deuda Actual:</label>
                                                <p class="form-control-static <?= $deuda_actual > 0 ? 'text-warning' : 'text-success' ?> font-weight-bold">
                                                    S/ <?= number_format($deuda_actual, 2) ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Porcentaje Completado:</label>
                                                <div class="progress" style="height: 25px;">
                                                    <?php
                                                    $porcentaje = $contrato['monto_total'] > 0 ? 
                                                        ($total_pagado / $contrato['monto_total'] * 100) : 0;
                                                    $clase_progress = $porcentaje == 100 ? 'bg-success' : 
                                                                     ($porcentaje >= 50 ? 'bg-warning' : 'bg-danger');
                                                    ?>
                                                    <div class="progress-bar <?= $clase_progress ?>" 
                                                         role="progressbar" style="width: <?= $porcentaje ?>%;" 
                                                         aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <?= number_format($porcentaje, 1) ?>%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de pagos -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Historial de Pagos</h4>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($pagos)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Fecha</th>
                                                        <th>Saldo Anterior</th>
                                                        <th>Amortización</th>
                                                        <th>Nueva Deuda</th>
                                                        <th>Tipo Pago</th>
                                                        <th>Usuario</th>
                                                        <th>Comprobante</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($pagos as $pago): ?>
                                                        <tr>
                                                            <td>#<?= $pago['idpagos'] ?></td>
                                                            <td><?= date('d/m/Y H:i', strtotime($pago['fechahora'])) ?></td>
                                                            <td class="font-weight-bold">S/ <?= number_format($pago['saldo'], 2) ?></td>
                                                            <td class="text-success font-weight-bold">S/ <?= number_format($pago['amortizacion'], 2) ?></td>
                                                            <td class="<?= $pago['deuda'] > 0 ? 'text-warning' : 'text-success' ?> font-weight-bold">
                                                                S/ <?= number_format($pago['deuda'], 2) ?>
                                                            </td>
                                                            <td><span class="badge badge-info"><?= $pago['tipopago'] ?></span></td>
                                                            <td><?= $pago['nombreusuario'] ?? 'N/A' ?></td>
                                                            <td>
                                                                <?php if (!empty($pago['comprobante'])): ?>
                                                                    <span class="badge badge-success">Sí</span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-warning">No</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="<?= base_url('/controlpagos/ver/' . $pago['idpagos']) ?>" 
                                                                   class="btn btn-sm btn-info" title="Ver detalles">
                                                                    <i class="fa fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> No se han registrado pagos para este contrato.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .badge {
        font-size: 0.85em;
        padding: 0.5em 0.8em;
    }
</style>

<?= $footer ?>