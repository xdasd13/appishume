<?= $header; ?>

<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title"><i class="fas fa-check-circle mr-2"></i>Contratos Pagados Sin Entregas Completas</h4>
                            <div>
                                <a href="<?= base_url('entregas') ?>" class="btn btn-secondary btn-round">
                                    <i class="fas fa-arrow-left mr-2"></i>Volver a todos los contratos
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($contratos)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>No hay contratos pagados pendientes de entregar.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="contratos-pendientes-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Cliente</th>
                                            <th>Evento</th>
                                            <th>Fecha</th>
                                            <th>Total</th>
                                            <th>Servicios</th>
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
                                            <td>S/ <?= number_format($c['monto_total'], 2) ?></td>
                                            <td><?= $c['total_servicios'] ?></td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <?php 
                                                    $porcentaje = ($c['entregas_realizadas'] / $c['total_servicios']) * 100;
                                                    ?>
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porcentaje ?>%;" 
                                                        aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100">
                                                        <?= $c['entregas_realizadas'] ?>/<?= $c['total_servicios'] ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('entregas/crear?contrato=' . $c['idcontrato']) ?>" class="btn btn-success btn-sm">
                                                    <i class="fas fa-truck"></i> Registrar entrega
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#contratos-pendientes-table').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
        },
        "responsive": true
    });
});
</script>
<?= $footer; ?>