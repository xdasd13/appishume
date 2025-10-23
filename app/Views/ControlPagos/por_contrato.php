<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ControlPagos-por_contrato.css') ?>">
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-3d animate__animated animate__fadeIn">
                <div class="card-header">
                    <div class="d-flex align-items-center flex-column flex-md-row">
                        <h4 class="card-title mb-2 mb-md-0">Pagos del Contrato #<?= $contrato['idcontrato'] ?></h4>
                        <div class="ml-md-auto d-flex flex-wrap gap-2">
                            <a href="<?= base_url('/controlpagos/crear?contrato=' . $contrato['idcontrato']) ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> Registrar Pago
                            </a>
                            <a href="<?= base_url('/controlpagos') ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Volver al Listado
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Resumen del contrato mejorado -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card card-info bg-gradient-info text-white">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h4 class="mb-2">Resumen del Contrato #<?= $contrato['idcontrato'] ?></h4>
                                            <div class="row">
                                                <div class="col-sm-6 col-md-3 mb-2">
                                                    <strong>Cliente:</strong><br>
                                                    <?= !empty($contrato['nombres']) ? 
                                                        $contrato['nombres'] . ' ' . $contrato['apellidos'] : 
                                                        $contrato['razonsocial'] ?>
                                                </div>
                                                <div class="col-sm-6 col-md-3 mb-2">
                                                    <strong>Monto Total:</strong><br>
                                                    S/ <?= number_format($contrato['monto_total'], 2) ?>
                                                </div>
                                                <div class="col-sm-6 col-md-3 mb-2">
                                                    <strong>Total Pagado:</strong><br>
                                                    S/ <?= number_format($total_pagado, 2) ?>
                                                </div>
                                                <div class="col-sm-6 col-md-3 mb-2">
                                                    <strong>Deuda Actual:</strong><br>
                                                    <span class="badge badge-<?= $deuda_actual > 0 ? 'warning' : 'success' ?>">
                                                        S/ <?= number_format($deuda_actual, 2) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="status-indicator">
                                                <h5 class="mb-1">Estado</h5>
                                                <span class="badge badge-<?= $deuda_actual > 0 ? 'warning' : 'success' ?> badge-lg p-2">
                                                    <?= $deuda_actual > 0 ? 'PENDIENTE' : 'PAGADO COMPLETO' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Barra de progreso mejorada -->
                                    <div class="mt-3">
                                        <?php 
                                            $porcentaje = $contrato['monto_total'] > 0 ? 
                                                (($total_pagado / $contrato['monto_total']) * 100) : 0;
                                            $porcentaje = min($porcentaje, 100);
                                        ?>
                                        <div class="d-flex justify-content-between mb-1">
                                            <small>Progreso de pago</small>
                                            <small><?= number_format($porcentaje, 1) ?>%</small>
                                        </div>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated 
                                                        <?= $porcentaje == 100 ? 'bg-success' : 'bg-info' ?>" 
                                                 role="progressbar" 
                                                 style="width: <?= $porcentaje ?>%;" 
                                                 aria-valuenow="<?= $porcentaje ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <?= number_format($porcentaje, 1) ?>%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de pagos responsive -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Historial de Pagos</h4>
                                    <span class="badge badge-primary"><?= count($pagos) ?> pagos</span>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($pagos) && is_array($pagos)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead class="thead-dark">
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
                                                        <tr class="<?= $pago['deuda'] == 0 ? 'table-success' : '' ?>">
                                                            <td>
                                                                <strong>#<?= $pago['idpagos'] ?></strong>
                                                            </td>
                                                            <td><?= date('d/m/Y H:i', strtotime($pago['fechahora'])) ?></td>
                                                            <td class="text-danger font-weight-bold">
                                                                S/ <?= number_format($pago['saldo'], 2) ?>
                                                            </td>
                                                            <td class="text-success font-weight-bold">
                                                                S/ <?= number_format($pago['amortizacion'], 2) ?>
                                                            </td>
                                                            <td class="<?= $pago['deuda'] > 0 ? 'text-warning' : 'text-success' ?> font-weight-bold">
                                                                S/ <?= number_format($pago['deuda'], 2) ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-info"><?= $pago['tipopago'] ?></span>
                                                            </td>
                                                            <td><?= $pago['nombreusuario'] ?? 'N/A' ?></td>
                                                            <td>
                                                                <?php if (!empty($pago['comprobante'])): ?>
                                                                    <a href="<?= base_url('/controlpagos/descargarComprobante/' . $pago['idpagos']) ?>" 
                                                                       class="btn btn-sm btn-outline-info" 
                                                                       title="Descargar comprobante"
                                                                       data-toggle="tooltip">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="badge badge-secondary">Sin comprobante</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm" role="group">
                                                                    <a href="<?= base_url('/controlpagos/ver/' . $pago['idpagos']) ?>" 
                                                                       class="btn btn-primary" 
                                                                       data-toggle="tooltip" 
                                                                       title="Ver detalles">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                    <a href="<?= base_url('/controlpagos/generarVoucher/' . $pago['idpagos']) ?>" 
                                                                       class="btn btn-info" 
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
                                        <div class="text-center py-5">
                                            <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                                            <h5 class="text-muted">No se han registrado pagos para este contrato</h5>
                                            <p class="text-muted mb-4">Puede registrar el primer pago haciendo clic en el botón "Registrar Pago"</p>
                                            <a href="<?= base_url('/controlpagos/crear?contrato=' . $contrato['idcontrato']) ?>" 
                                               class="btn btn-primary btn-lg">
                                                <i class="fas fa-plus mr-2"></i> Registrar Primer Pago
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas de pagos (solo si hay pagos) -->
                    <?php if (!empty($pagos)): ?>
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card card-3d">
                                <div class="card-header">
                                    <h4 class="card-title">Evolución de Pagos</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="pagosChart" width="400" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-3d">
                                <div class="card-header">
                                    <h4 class="card-title">Distribución por Tipo de Pago</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="tipoPagoChart" width="400" height="250"></canvas>
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
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();

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
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Deuda (S/)',
                    data: deudas,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Evolución de Pagos y Deuda',
                    font: {
                        size: 16
                    }
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'S/ ' + value.toLocaleString('es-PE');
                        }
                    }
                }
            }
        }
    });
    
    // Gráfico de tipos de pago
    var ctxTipoPago = document.getElementById('tipoPagoChart').getContext('2d');
    var tipoPagoChart = new Chart(ctxTipoPago, {
        type: 'doughnut',
        data: {
            labels: Object.keys(tiposPago),
            datasets: [{
                data: Object.values(tiposPago),
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)',
                    'rgb(201, 203, 207)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                title: {
                    display: true,
                    text: 'Distribución por Tipo de Pago',
                    font: {
                        size: 16
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            var value = context.raw || 0;
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = Math.round((value / total) * 100);
                            return label + ': S/ ' + value.toLocaleString('es-PE') + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
});
</script>
<?php endif; ?>

