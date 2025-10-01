<?= $header; ?>

<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title"><i class="fas fa-file-contract mr-2"></i>Contratos y Entregas</h4>
                            <div>
                                <a href="<?= base_url('entregas/crear') ?>" class="btn btn-primary btn-round">
                                    <i class="fas fa-plus mr-2"></i>Nueva Entrega
                                </a>
                                <!-- Nuevo botón para ver entregas completadas -->
                                <a href="<?= base_url('entregas/historial') ?>" class="btn btn-success btn-round ml-2">
                                    <i class="fas fa-history mr-2"></i>Historial de Entregas
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
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
                                                <?php if ($c['total_entregas'] > 0): ?>
                                                    <span class="badge badge-info"><?= $c['total_entregas'] ?> entregas</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Sin entregas</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($c['deuda_actual'] == 0): ?>
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
</div>
<script>
    $(document).ready(function () {
        $('#contratos-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
            },
            "responsive": true
        });
    });
</script>
<?= $footer; ?>