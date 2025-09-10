<?= $header ?>
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Pagos del Contrato #<?= $contrato['idcontrato'] ?></h4>
                        <a href="<?= base_url('/controlpagos') ?>" class="btn btn-secondary btn-round ml-auto">
                            <i class="fas fa-arrow-left mr-2"></i> Volver al Listado
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Resumen del contrato -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card card-info bg-info-gradient">
                                <div class="card-body">
                                    <h4 class="mb-3">Resumen del Contrato</h4>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Contrato #:</strong> <?= $contrato['idcontrato'] ?></p>
                                            <p><strong>Cliente:</strong> 
                                                <?= !empty($contrato['nombres']) ? 
                                                    $contrato['nombres'] . ' ' . $contrato['apellidos'] : 
                                                    $contrato['razonsocial'] ?>
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Monto Total:</strong> S/ <?= number_format($contrato['monto_total'], 2) ?></p>
                                            <p><strong>Total Pagado:</strong> S/ <?= number_format($total_pagado, 2) ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Deuda Actual:</strong> 
                                                <span class="badge badge-<?= $deuda_actual > 0 ? 'warning' : 'success' ?>">
                                                    S/ <?= number_format($deuda_actual, 2) ?>
                                                </span>
                                            </p>
                                            <p><strong>Estado:</strong> 
                                                <span class="badge badge-<?= $deuda_actual > 0 ? 'warning' : 'success' ?>">
                                                    <?= $deuda_actual > 0 ? 'PENDIENTE' : 'PAGADO COMPLETO' ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Barra de progreso -->
                                    <div class="progress" style="height: 20px; margin-top: 15px;">
                                        <?php 
                                            $porcentaje = $contrato['monto_total'] > 0 ? 
                                                (($total_pagado / $contrato['monto_total']) * 100) : 0;
                                        ?>
                                        <div class="progress-bar progress-bar-striped 
                                                    <?= $porcentaje == 100 ? 'bg-success' : 'bg-info' ?>" 
                                             role="progressbar" 
                                             style="width: <?= $porcentaje ?>%;" 
                                             aria-valuenow="<?= $porcentaje ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <?= number_format($porcentaje, 2) ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de pagos -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Historial de Pagos</h4>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($pagos) && is_array($pagos)): ?>
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
                                                        <th>Comprobante</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($pagos as $pago): ?>
                                                        <tr>
                                                            <td><?= $pago['idpagos'] ?></td>
                                                            <td><?= date('d/m/Y H:i', strtotime($pago['fechahora'])) ?></td>
                                                            <td><?= number_format($pago['saldo'], 2) ?></td>
                                                            <td class="text-success"><?= number_format($pago['amortizacion'], 2) ?></td>
                                                            <td class="<?= $pago['deuda'] > 0 ? 'text-warning' : 'text-success' ?>">
                                                                <?= number_format($pago['deuda'], 2) ?>
                                                            </td>
                                                            <td><?= $pago['tipopago'] ?></td>
                                                            <td><?= $pago['nombreusuario'] ?? 'N/A' ?></td>
                                                            <td>
                                                                <?php if (!empty($pago['comprobante'])): ?>
                                                                    <a href="<?= base_url('/controlpagos/descargarComprobante/' . $pago['idpagos']) ?>" 
                                                                       class="btn btn-sm btn-outline-info" title="Descargar comprobante">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="badge badge-secondary">Sin comprobante</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <div class="form-button-action">
                                                                    <a href="<?= base_url('/controlpagos/ver/' . $pago['idpagos']) ?>" 
                                                                       class="btn btn-link btn-primary btn-lg" 
                                                                       data-toggle="tooltip" 
                                                                       title="Ver detalles">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                    <a href="<?= base_url('/controlpagos/generarVoucher/' . $pago['idpagos']) ?>" 
                                                                       class="btn btn-link btn-info btn-lg" 
                                                                       data-toggle="tooltip" 
                                                                       title="Generar voucher"
                                                                       target="_blank">
                                                                        <i class="fas fa-receipt"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                                            <h5>No se han registrado pagos para este contrato</h5>
                                            <p>Puede registrar el primer pago haciendo clic en el botón "Registrar Pago"</p>
                                            <a href="<?= base_url('/controlpagos/crear') ?>" class="btn btn-primary mt-3">
                                                <i class="fas fa-plus mr-2"></i> Registrar Primer Pago
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas de pagos -->
                    <?php if (!empty($pagos)): ?>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Resumen de Pagos</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="pagosChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Distribución por Tipo de Pago</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="tipoPagoChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer ?>

<?php if (!empty($pagos)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Datos para los gráficos
    var fechas = <?= json_encode(array_map(function($pago) {
        return date('d/m/Y', strtotime($pago['fechahora']));
    }, $pagos)) ?>;
    
    var amortizaciones = <?= json_encode(array_map(function($pago) {
        return $pago['amortizacion'];
    }, $pagos)) ?>;
    
    var deudas = <?= json_encode(array_map(function($pago) {
        return $pago['deuda'];
    }, $pagos)) ?>;
    
    // Agrupar por tipo de pago
    var tiposPago = {};
    <?php foreach ($pagos as $pago): ?>
        if (!tiposPago['<?= $pago['tipopago'] ?>']) {
            tiposPago['<?= $pago['tipopago'] ?>'] = 0;
        }
        tiposPago['<?= $pago['tipopago'] ?>'] += <?= $pago['amortizacion'] ?>;
    <?php endforeach; ?>
    
    // Gráfico de evolución de pagos
    var ctxPagos = document.getElementById('pagosChart').getContext('2d');
    var pagosChart = new Chart(ctxPagos, {
        type: 'line',
        data: {
            labels: fechas,
            datasets: [
                {
                    label: 'Amortización (S/)',
                    data: amortizaciones,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.1
                },
                {
                    label: 'Deuda (S/)',
                    data: deudas,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Evolución de Pagos y Deuda'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Gráfico de tipos de pago
    var ctxTipoPago = document.getElementById('tipoPagoChart').getContext('2d');
    var tipoPagoChart = new Chart(ctxTipoPago, {
        type: 'pie',
        data: {
            labels: Object.keys(tiposPago),
            datasets: [{
                data: Object.values(tiposPago),
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                title: {
                    display: true,
                    text: 'Distribución por Tipo de Pago'
                }
            }
        }
    });
});
</script>
<?php endif; ?>