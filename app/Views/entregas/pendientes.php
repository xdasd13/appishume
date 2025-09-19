<?= $header; ?>

<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title"><i class="fas fa-clock mr-2"></i>Entregas Pendientes</h4>
                            <div>
                                <a href="<?= base_url('entregas') ?>" class="btn btn-primary btn-round ml-2">
                                    <i class="fas fa-list mr-2"></i>Todas las Entregas
                                </a>
                                <a href="<?= base_url('entregas/crear') ?>" class="btn btn-success btn-round ml-2">
                                    <i class="fas fa-plus mr-2"></i>Nueva Entrega
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
                        
                        <?php if (!empty($entregas)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Nota:</strong> Estas son las entregas pendientes que requieren seguimiento. Las entregas vencidas se muestran primero.
                            </div>
                            
                            <div class="table-responsive">
                                <table id="entregas-pendientes-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Cliente</th>
                                            <th>Servicio</th>
                                            <th>Fecha Entrega</th>
                                            <th>Días Restantes</th>
                                            <th>Estado</th>
                                            <th>Responsable</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($entregas as $entrega): ?>
                                            <tr>
                                                <td><?= $entrega['identregable'] ?></td>
                                                <td>
                                                    <div class="font-weight-bold"><?= $entrega['nombre_cliente'] ?> <?= $entrega['apellido_cliente'] ?></div>
                                                    <small class="text-muted"><?= date('d/m/Y', strtotime($entrega['fechahoraservicio'])) ?></small>
                                                </td>
                                                <td><?= $entrega['servicio'] ?></td>
                                                <td>
                                                    <div class="font-weight-bold"><?= date('d/m/Y', strtotime($entrega['fechahoraentrega'])) ?></div>
                                                    <small class="text-muted"><?= date('H:i', strtotime($entrega['fechahoraentrega'])) ?></small>
                                                </td>
                                                <td>
                                                    <?php if ($entrega['dias_restantes'] < 0): ?>
                                                        <span class="text-danger font-weight-bold"><?= $entrega['dias_restantes'] ?> días</span>
                                                        <small class="d-block text-danger">VENCIDA</small>
                                                    <?php elseif ($entrega['dias_restantes'] == 0): ?>
                                                        <span class="text-warning font-weight-bold">HOY</span>
                                                    <?php else: ?>
                                                        <span class="text-success font-weight-bold"><?= $entrega['dias_restantes'] ?> días</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($entrega['estado_entrega'] == 'vencida'): ?>
                                                        <span class="badge badge-danger">Vencida</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">Pendiente</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= $entrega['nombre_entrega'] ?> <?= $entrega['apellido_entrega'] ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="<?= base_url('entregas/ver/' . $entrega['identregable']) ?>" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Ver detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= base_url('entregas/editar/' . $entrega['identregable']) ?>" class="btn btn-sm btn-warning" data-toggle="tooltip" title="Editar">
                                                            <i class="fas fa-edit"></i>
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
                                <div class="avatar avatar-xl bg-success-light text-success rounded-circle mx-auto mb-3">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <h4>¡No hay entregas pendientes!</h4>
                                <p class="text-muted">Todas las entregas han sido completadas o aún no se han programado</p>
                                <a href="<?= base_url('entregas/crear') ?>" class="btn btn-success mt-3">
                                    <i class="fas fa-plus mr-2"></i>Programar Nueva Entrega
                                </a>
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
    // Inicializar DataTable
    $('#entregas-pendientes-table').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
        },
        "pageLength": 10,
        "order": [[4, "asc"]],  // Ordenar por días restantes, mostrando primero las vencidas
        "responsive": true
    });
    
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

<style>
.avatar {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.avatar-xl {
    width: 80px;
    height: 80px;
    font-size: 2rem;
}
.bg-success-light {
    background-color: rgba(29, 201, 183, 0.2);
}
</style>

<?= $footer; ?>