<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/entregas-listar.css') ?>">
<div class="page-inner">
    <!-- Notificaciones -->
    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            <?= session('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?= session('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Tarjetas de resumen SIMPLIFICADAS -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="icon-big text-center icon-primary">
                                <i class="fas fa-file-contract"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Total Contratos</p>
                                <h4 class="card-title"><?= count($contratos) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="icon-big text-center icon-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Contratos Pagados</p>
                                <h4 class="card-title">
                                    <?= count(array_filter($contratos, function($c) { 
                                        return ($c['deuda_actual'] ?? 0) <= 0.01; 
                                    })) ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="icon-big text-center icon-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Pendientes Entrega</p>
                                <h4 class="card-title">
                                    <?= count(array_filter($contratos, function($c) { 
                                        $total = intval($c['total_servicios'] ?? 0);
                                        $completadas = intval($c['entregas_completadas'] ?? 0);
                                        return $total > $completadas;
                                    })) ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="icon-big text-center icon-info">
                                <i class="fas fa-truck-loading"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Entregas Completadas</p>
                                <h4 class="card-title">
                                    <?= array_sum(array_column($contratos, 'entregas_completadas')) ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel principal -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Contratos y Entregas</h4>
                        <a href="<?= base_url('entregas/crear') ?>" class="btn btn-primary btn-round ml-auto">
                            <i class="fas fa-plus mr-2"></i>Nueva Entrega
                        </a>
                        <a href="<?= base_url('entregas/historial') ?>" class="btn btn-success btn-round ml-2">
                            <i class="fas fa-history mr-2"></i>Historial
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="contratos-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Evento</th>
                                    <th>Fecha</th>
                                    <th>Estado Pago</th>
                                    <th>Entregas</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contratos as $c): ?>
                                    <tr>
                                        <td><?= $c['idcontrato'] ?></td>
                                        <td><?= $c['cliente_nombre'] ?></td>
                                        <td><?= $c['tipo_evento'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($c['fechaevento'])) ?></td>
                                        <td>
                                            <?php if ($c['deuda_actual'] == 0): ?>
                                                <span class="badge badge-success">Pagado</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Con deuda: S/
                                                    <?= number_format($c['deuda_actual'], 2) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($c['todos_servicios_completados']) && $c['todos_servicios_completados']): ?>
                                                <span class="badge badge-success">✅ <?= $c['estado_entregas'] ?></span>
                                            <?php elseif ($c['total_entregas'] > 0): ?>
                                                <span class="badge badge-warning">⏳ <?= $c['estado_entregas'] ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Sin entregas</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($c['todos_servicios_completados']) && $c['todos_servicios_completados']): ?>
                                                <span class="btn btn-success btn-sm disabled">
                                                    <i class="fas fa-check-circle"></i> Completado
                                                </span>
                                            <?php elseif ($c['deuda_actual'] == 0): ?>
                                                <a href="<?= base_url('entregas/crear?contrato=' . $c['idcontrato']) ?>"
                                                    class="btn btn-success btn-sm">
                                                    <i class="fas fa-truck"></i> Registrar entrega
                                                </a>
                                            <?php else: ?>
                                                <div class="btn-group-vertical" role="group">
                                                    <a href="<?= base_url('controlpagos/crear?contrato=' . $c['idcontrato']) ?>"
                                                        class="btn btn-warning btn-sm mb-1" 
                                                        title="Proceder con el pago del contrato">
                                                        <i class="fas fa-credit-card"></i> Completar pago
                                                    </a>
                                                    <small class="text-danger">
                                                        <i class="fas fa-exclamation-triangle"></i> Deuda: S/ <?= number_format($c['deuda_actual'], 2) ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $footer; ?>

<script>
    $(document).ready(function () {
        $('#contratos-table').DataTable({
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
            "responsive": true
        });

        // Sweet Alert para cuando todas las entregas ya fueron registradas
        <?php if (session('info')): ?>
            Swal.fire({
                title: '¡Todas las entregas completadas!',
                text: '<?= session('info') ?>',
                icon: 'success',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#28a745'
            });
        <?php endif; ?>
    });
</script>

