<?= $header ?>
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Detalles del Pago #<?= $pago['idpagos'] ?></h4>
                        <div class="ml-auto">
                            <a href="<?= base_url('/controlpagos') ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button class="btn btn-sm btn-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h4 class="card-title">Información del Pago</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">ID del Pago:</th>
                                                <td>#<?= $pago['idpagos'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>Contrato:</th>
                                                <td>Contrato #<?= $pago['idcontrato'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>Fecha y Hora:</th>
                                                <td><?= date('d/m/Y H:i', strtotime($pago['fechahora'])) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Saldo Anterior:</th>
                                                <td class="font-weight-bold">S/ <?= number_format($pago['saldo'], 2) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Amortización:</th>
                                                <td class="text-success font-weight-bold">S/
                                                    <?= number_format($pago['amortizacion'], 2) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Nueva Deuda:</th>
                                                <td
                                                    class="<?= $pago['deuda'] > 0 ? 'text-warning' : 'text-success' ?> font-weight-bold">
                                                    S/ <?= number_format($pago['deuda'], 2) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Tipo de Pago:</th>
                                                <td><span class="badge badge-info"><?= $tipo_pago['tipopago'] ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Número de Transacción:</th>
                                                <td><?= !empty($pago['numtransaccion']) ? $pago['numtransaccion'] : 'N/A' ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Registrado por:</th>
                                                <td>
                                                    <?= !empty($pago['nombres']) ? 
                                                        $pago['nombres'] . ' ' . $pago['apellidos'] . ' (' . $pago['nombreusuario'] . ')' : 
                                                        'Usuario #' . $pago['idusuario'] ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h4 class="card-title">Información del Contrato</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">Contrato:</th>
                                                <td>#<?= $info_contrato['idcontrato'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>Cliente:</th>
                                                <td>
                                                    <?= !empty($info_contrato['nombres']) ?
                                                        $info_contrato['nombres'] . ' ' . $info_contrato['apellidos'] :
                                                        $info_contrato['razonsocial'] ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Monto Total:</th>
                                                <td class="font-weight-bold">S/
                                                    <?= number_format($info_contrato['monto_total'], 2) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Estado Actual:</th>
                                                <td>
                                                    <?php if ($pago['deuda'] == 0): ?>
                                                        <span class="badge badge-success">PAGADO COMPLETAMENTE</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">PENDIENTE DE PAGO</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Pagado hasta ahora:</th>
                                                <td class="text-success font-weight-bold">
                                                    S/
                                                    <?= number_format($info_contrato['monto_total'] - $pago['deuda'], 2) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Porcentaje Completado:</th>
                                                <td>
                                                    <?php
                                                    $porcentaje = $info_contrato['monto_total'] > 0 ?
                                                        (($info_contrato['monto_total'] - $pago['deuda']) / $info_contrato['monto_total'] * 100) : 0;
                                                    ?>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar <?= $porcentaje == 100 ? 'bg-success' : ($porcentaje >= 50 ? 'bg-warning' : 'bg-danger') ?>"
                                                            role="progressbar" style="width: <?= $porcentaje ?>%;"
                                                            aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0"
                                                            aria-valuemax="100">
                                                            <?= number_format($porcentaje, 1) ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-primary mt-4">
                                <div class="card-header">
                                    <h4 class="card-title">Comprobante de Pago</h4>
                                </div>
                                <div class="card-body text-center">
                                    <?php if (!empty($pago['comprobante'])): ?>
                                        <?php
                                        $extension = pathinfo($pago['comprobante'], PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                        ?>
                                        
                                        <?php if ($isImage): ?>
                                            <img src="<?= base_url('uploads/comprobantes/' . $pago['comprobante']) ?>"
                                                alt="Comprobante de pago" class="img-fluid img-thumbnail mb-3"
                                                style="max-height: 300px;">
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                <i class="fas fa-file-pdf fa-3x mb-2"></i>
                                                <p>Archivo PDF - Comprobante de pago</p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <a href="<?= base_url('/controlpagos/descargarComprobante/' . $pago['idpagos']) ?>"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-download"></i> Descargar Comprobante
                                        </a>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            No se ha subido comprobante para este pago.
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
    @media print {
        .card-header .btn, .card-header .ml-auto {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .badge {
            border: 1px solid #000;
            color: #000;
            background: transparent !important;
        }
        
        .text-success {
            color: #000 !important;
            font-weight: bold;
        }
        
        .text-warning {
            color: #000 !important;
            font-weight: bold;
        }
        
        .progress-bar {
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }
    }
</style>

<?= $footer ?>