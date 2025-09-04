<?= $header; ?>

<div class="container">
    <div class="page-inner">
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

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title"><i class="fas fa-list-ol mr-2"></i>Entregas Registradas</h4>
                            <div class="d-flex align-items-center">
                                <a href="<?= base_url('/entregas/crear') ?>" class="btn btn-primary btn-round">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Nueva Entrega
                                </a>
                                <a href="<?= base_url('/entregas/pendientes') ?>"
                                    class="btn btn-danger btn-round mr-2 pulse-animation">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Ver Pendientes
                                    <span class="badge badge-light badge-pill ml-2">
                                        <?= $cantidad_pendientes ?? '' ?>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="entregas-table" class="display table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>Fecha Entrega</th>
                                        <th>Ubicación</th>
                                        <th>Responsable</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($entregas)): ?>
                                        <?php foreach ($entregas as $entrega): ?>
                                            <tr>
                                                <td><span class="badge badge-secondary">#<?= $entrega['identregable'] ?></span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <div class="font-weight-bold">
                                                                <?= $entrega['nombre_cliente'] . ' ' . $entrega['apellido_cliente'] ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 200px;"
                                                        title="<?= $entrega['servicio'] ?>">
                                                        <?= $entrega['servicio'] ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-nowrap">
                                                        <i class="far fa-calendar-alt mr-1 text-primary"></i>
                                                        <?= date('d/m/Y', strtotime($entrega['fechahoraentrega'])) ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="far fa-clock mr-1"></i>
                                                        <?= date('H:i', strtotime($entrega['fechahoraentrega'])) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 150px;"
                                                        title="<?= $entrega['direccion'] ?>">
                                                        <i class="fas fa-map-marker-alt mr-1 text-danger"></i>
                                                        <?= $entrega['direccion'] ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if (!empty($entrega['nombre_entrega'])): ?>
                                                        <span class="badge badge-info">
                                                            <i class="fas fa-user mr-1"></i>
                                                            <?= $entrega['nombre_entrega'] . ' ' . $entrega['apellido_entrega'] ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">No asignado</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($entrega['estado'] == 'completada') {
                                                        echo '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Completada</span>';
                                                    } else {
                                                        $fechaEntrega = strtotime($entrega['fechahoraentrega']);
                                                        $hoy = strtotime('now');
                                                        if ($fechaEntrega > $hoy) {
                                                            echo '<span class="badge badge-warning"><i class="fas fa-clock mr-1"></i>Pendiente</span>';
                                                        } else {
                                                            echo '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle mr-1"></i>Vencida</span>';
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="<?= base_url('/entregas/ver/' . $entrega['identregable']) ?>"
                                                            class="btn btn-sm btn-icon btn-info" data-toggle="tooltip"
                                                            title="Ver detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if ($entrega['estado'] != 'completada'): ?>
                                                            <a href="<?= base_url('/entregas/editar/' . $entrega['identregable']) ?>"
                                                                class="btn btn-sm btn-icon btn-primary" data-toggle="tooltip"
                                                                title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-icon btn-secondary" disabled
                                                                data-toggle="tooltip" title="Entrega completada - No editable">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <?php if ($entrega['estado'] != 'completada'): ?>
                                                            <a href="<?= base_url('/entregas/eliminar/' . $entrega['identregable']) ?>"
                                                                class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip"
                                                                title="Eliminar"
                                                                onclick="return confirm('¿Está seguro de eliminar esta entrega?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-icon btn-secondary" disabled
                                                                data-toggle="tooltip" title="Entrega completada - No eliminable">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                                    <h4 class="text-muted">No hay entregas registradas</h4>
                                                    <p class="text-muted">Comienza registrando tu primera entrega</p>
                                                    <a href="<?= base_url('/entregas/crear') ?>"
                                                        class="btn btn-primary mt-2">
                                                        <i class="fa fa-plus mr-2"></i>Crear primera entrega
                                                    </a>
                                                </div>
                                            </td>
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

<script>
    $(document).ready(function () {
        $('#entregas-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
            },
            "pageLength": 10,
            "order": [
                [3, "desc"]
            ],
            "responsive": true,
            "dom": '<"top"<"dataTables__top"lf>><"dataTables__container"t><"bottom"<"dataTables__bottom"ip>>',
            "columnDefs": [{
                "responsivePriority": 1,
                "targets": 0
            },
            {
                "responsivePriority": 2,
                "targets": -1
            }
            ]
        });
    });
</script>

<style>
    .empty-state {
        padding: 2rem;
        text-align: center;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        margin: 0 2px;
    }

    .dataTables__top {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .dataTables__bottom {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
    }

    @media (max-width: 768px) {
        .dataTables__top {
            flex-direction: column;
        }

        .dataTables__bottom {
            flex-direction: column;
            align-items: center;
        }

        /* Estilos responsivos para el header */
        .card-header .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .card-header .btn {
            margin-top: 10px;
            margin-left: 0 !important;
            align-self: flex-end;
        }
    }
</style>

<?= $footer; ?>