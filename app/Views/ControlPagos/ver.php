<?= $header ?>
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Detalles del Pago #<?= $pago['idpagos'] ?></h4>
                        <a href="<?= base_url('/controlpagos') ?>" class="btn btn-secondary btn-round ml-auto">
                            <i class="fas fa-arrow-left mr-2"></i> Volver al Listado
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary bg-primary-gradient">
                                <div class="card-body">
                                    <h4 class="mb-3">Información del Pago</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>ID Pago:</strong> <?= $pago['idpagos'] ?></p>
                                            <p><strong>Fecha y Hora:</strong> <?= date('d/m/Y H:i', strtotime($pago['fechahora'])) ?></p>
                                            <p><strong>Saldo Anterior:</strong> S/ <?= number_format($pago['saldo'], 2) ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Amortización:</strong> S/ <?= number_format($pago['amortizacion'], 2) ?></p>
                                            <p><strong>Nueva Deuda:</strong> S/ <?= number_format($pago['deuda'], 2) ?></p>
                                            <p><strong>Tipo de Pago:</strong> <?= $tipo_pago['tipopago'] ?></p>
                                        </div>
                                    </div>
                                    <?php if (!empty($pago['numtransaccion'])): ?>
                                        <p><strong>Número de Transacción:</strong> <?= $pago['numtransaccion'] ?></p>
                                    <?php endif; ?>
                                    <p><strong>Registrado por:</strong> <?= $pago['nombreusuario'] ?? 'N/A' ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-secondary bg-secondary-gradient">
                                <div class="card-body">
                                    <h4 class="mb-3">Información del Contrato</h4>
                                    <p><strong>Contrato #:</strong> <?= $info_contrato['idcontrato'] ?></p>
                                    <p><strong>Cliente:</strong> 
                                        <?= !empty($info_contrato['nombres']) ? 
                                            $info_contrato['nombres'] . ' ' . $info_contrato['apellidos'] : 
                                            $info_contrato['razonsocial'] ?>
                                    </p>
                                    <p><strong>Monto Total del Contrato:</strong> S/ <?= number_format($info_contrato['monto_total'], 2) ?></p>
                                    <p><strong>Total Pagado:</strong> 
                                        <?php 
                                            $total_pagado = $info_contrato['monto_total'] - $pago['deuda'];
                                            echo 'S/ ' . number_format($total_pagado, 2);
                                        ?>
                                    </p>
                                    <p><strong>Estado:</strong> 
                                        <span class="badge badge-<?= $pago['deuda'] == 0 ? 'success' : 'warning' ?>">
                                            <?= $pago['deuda'] == 0 ? 'PAGADO COMPLETO' : 'PENDIENTE' ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comprobante -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Comprobante de Pago</h4>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($pago['comprobante'])): ?>
                                        <div class="text-center">
                                            <?php 
                                                $extension = pathinfo($pago['comprobante'], PATHINFO_EXTENSION);
                                                if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])): 
                                            ?>
                                                <img src="<?= base_url('uploads/comprobantes/' . $pago['comprobante']) ?>" 
                                                     class="img-fluid rounded" 
                                                     style="max-height: 400px;" 
                                                     alt="Comprobante de pago">
                                            <?php else: ?>
                                                <div class="alert alert-info">
                                                    <i class="fas fa-file-pdf fa-3x mb-3"></i>
                                                    <p>Comprobante en formato PDF</p>
                                                </div>
                                            <?php endif; ?>
                                            <div class="mt-3">
                                                <a href="<?= base_url('/controlpagos/descargarComprobante/' . $pago['idpagos']) ?>" 
                                                   class="btn btn-primary">
                                                    <i class="fas fa-download mr-2"></i> Descargar Comprobante
                                                </a>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning text-center">
                                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                            <p>No se ha subido comprobante para este pago</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de pagos del contrato -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Historial de Pagos del Contrato</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID Pago</th>
                                                    <th>Fecha/Hora</th>
                                                    <th>Saldo (S/)</th>
                                                    <th>Amortización (S/)</th>
                                                    <th>Deuda (S/)</th>
                                                    <th>Tipo Pago</th>
                                                    <th>Usuario</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($historial_pagos)): ?>
                                                    <?php foreach ($historial_pagos as $historial): ?>
                                                        <tr class="<?= $historial['idpagos'] == $pago['idpagos'] ? 'table-active' : '' ?>">
                                                            <td><?= $historial['idpagos'] ?></td>
                                                            <td><?= date('d/m/Y H:i', strtotime($historial['fechahora'])) ?></td>
                                                            <td><?= number_format($historial['saldo'], 2) ?></td>
                                                            <td class="text-success"><?= number_format($historial['amortizacion'], 2) ?></td>
                                                            <td class="<?= $historial['deuda'] > 0 ? 'text-warning' : 'text-success' ?>">
                                                                <?= number_format($historial['deuda'], 2) ?>
                                                            </td>
                                                            <td><?= $historial['tipopago'] ?></td>
                                                            <td><?= $historial['nombreusuario'] ?? 'N/A' ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center">No hay historial de pagos</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <a href="<?= base_url('/controlpagos/generarVoucher/' . $pago['idpagos']) ?>" 
                               class="btn btn-info btn-animate" target="_blank">
                                <span class="btn-label">
                                    <i class="fas fa-receipt"></i>
                                </span>
                                Generar Voucher
                            </a>
                            <a href="<?= base_url('/controlpagos/por-contrato/' . $pago['idcontrato']) ?>" 
                               class="btn btn-primary">
                                <i class="fas fa-list mr-2"></i> Ver Todos los Pagos del Contrato
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $footer ?>