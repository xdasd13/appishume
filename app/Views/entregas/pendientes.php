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
                            <h4 class="card-title"><i class="fas fa-clock mr-2 text-warning"></i>Entregas Pendientes de Postproducción</h4>
                            <div>
                                <a href="<?= base_url('/entregas') ?>" class="btn btn-secondary btn-round mr-2">
                                    <i class="fas fa-list mr-2"></i>Ver Todas las Entregas
                                </a>
                                <a href="<?= base_url('/entregas/crear') ?>" class="btn btn-primary btn-round">
                                    <i class="fa fa-plus mr-2"></i>
                                    Nueva Entrega
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($entregas)): ?>
                            <div class="table-responsive">
                                <table id="pendientes-table" class="display table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Servicio</th>
                                            <th>Fecha Programada</th>
                                            <th>Días Restantes</th>
                                            <th>Ubicación</th>
                                            <th>Responsable</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($entregas as $entrega): ?>
                                            <?php
                                            $fechaEntrega = strtotime($entrega['fechahoraentrega']);
                                            $hoy = strtotime('now');
                                            $diasRestantes = ceil(($fechaEntrega - $hoy) / (60 * 60 * 24));
                                            
                                            $claseDias = 'success';
                                            if ($diasRestantes <= 2) {
                                                $claseDias = 'danger';
                                            } elseif ($diasRestantes <= 5) {
                                                $claseDias = 'warning';
                                            } elseif ($diasRestantes <= 0) {
                                                $claseDias = 'secondary';
                                                $diasRestantes = 'Vencida';
                                            }
                                            ?>
                                            <tr>
                                                <td><span class="badge badge-secondary">#<?= $entrega['identregable'] ?></span></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <div class="font-weight-bold"><?= $entrega['nombre_cliente'] . ' ' . $entrega['apellido_cliente'] ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 200px;" title="<?= $entrega['servicio'] ?>">
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
                                                    <span class="badge badge-<?= $claseDias ?>">
                                                        <?= is_numeric($diasRestantes) ? $diasRestantes . ' día' . ($diasRestantes != 1 ? 's' : '') : $diasRestantes ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 150px;" title="<?= $entrega['direccion'] ?>">
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
                                                    <?php if ($diasRestantes === 'Vencida'): ?>
                                                        <span class="badge badge-danger"><i class="fas fa-exclamation-triangle mr-1"></i>Vencida</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i>En Postproducción</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="<?= base_url('/entregas/ver/' . $entrega['identregable']) ?>" class="btn btn-sm btn-icon btn-info" data-toggle="tooltip" title="Ver detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= base_url('/entregas/editar/' . $entrega['identregable']) ?>" class="btn btn-sm btn-icon btn-primary" data-toggle="tooltip" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?= base_url('/entregas/eliminar/' . $entrega['identregable']) ?>" class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta entrega?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h4 class="text-muted">¡No hay entregas pendientes!</h4>
                                    <p class="text-muted">Todas las entregas han sido completadas o no hay entregas programadas</p>
                                    <a href="<?= base_url('/entregas/crear') ?>" class="btn btn-primary mt-2">
                                        <i class="fa fa-plus mr-2"></i>Crear nueva entrega
                                    </a>
                                </div>
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
        $('#pendientes-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
            },
            "pageLength": 10,
            "order": [
                [3, "asc"] // Ordenar por fecha de entrega (más próximas primero)
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