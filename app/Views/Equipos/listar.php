<?= $header ?>
<div class="container mt-4">
    <!-- Encabezado y Alertas -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-primary"><i class="fas fa-users-cog me-2"></i><?= $titulo ?></h2>
            
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tarjeta de Información del Servicio -->
    <?php if (isset($servicio)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Servicio 2</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-primary"><i class="fas fa-concierge-bell me-2"></i>Servicio:</strong>
                                <p class="mb-0"><?= $servicio->servicio ?></p>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary"><i class="fas fa-user me-2"></i>Cliente:</strong>
                                <p class="mb-0"><?= !empty($servicio->razonsocial) ? $servicio->razonsocial : $servicio->nombres . ' ' . $servicio->apellidos ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-primary"><i class="fas fa-calendar-day me-2"></i>Fecha del Evento:</strong>
                                <p class="mb-0"><?= date('d/m/Y', strtotime($servicio->fechaevento)) ?></p>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary"><i class="fas fa-star me-2"></i>Tipo de Evento:</strong>
                                <p class="mb-0"><?= $servicio->tipo_evento ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= base_url('equipos/asignar/'.$servicio->idserviciocontratado) ?>" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Asignar Nuevo Equipo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tarjeta de Información del Usuario -->
    <?php if (isset($usuario)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-user-tie me-2"></i>Información del Usuario</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <strong class="text-info"><i class="fas fa-id-card me-2"></i>Nombre:</strong>
                                <p class="mb-0"><?= $usuario->nombres . ' ' . $usuario->apellidos ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <strong class="text-info"><i class="fas fa-user me-2"></i>Usuario:</strong>
                                <p class="mb-0"><?= $usuario->nombreusuario ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <strong class="text-info"><i class="fas fa-briefcase me-2"></i>Cargo:</strong>
                                <p class="mb-0"><?= $usuario->cargo ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Sección de Equipos Asignados -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-list-check me-2"></i>Lista de Equipos Asignados</h4>
                        <a href="<?= base_url('equipos') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-eye me-2"></i>Ver Todos los Equipos
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($equipos)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                    <th><i class="fas fa-concierge-bell me-1"></i>Servicio</th>
                                    <th><i class="fas fa-user me-1"></i>Usuario Asignado</th>
                                    <th><i class="fas fa-file-alt me-1"></i>Descripción</th>
                                    <th><i class="fas fa-tasks me-1"></i>Estado</th>
                                    <th><i class="fas fa-cogs me-1"></i>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($equipos as $equipo): ?>
                                <tr>
                                    <td><strong>#<?= $equipo->idequipo ?></strong></td>
                                    <td><?= $equipo->servicio ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <?= isset($equipo->nombres) ? $equipo->nombres . ' ' . $equipo->apellidos : $equipo->nombreusuario ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;" title="<?= $equipo->descripcion ?>">
                                            <?= $equipo->descripcion ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = [
                                            'Completado' => 'bg-success',
                                            'En Proceso' => 'bg-warning',
                                            'Pendiente' => 'bg-secondary',
                                            'Programado' => 'bg-info'
                                        ];
                                        $icon_class = [
                                            'Completado' => 'fa-check-circle',
                                            'En Proceso' => 'fa-spinner',
                                            'Pendiente' => 'fa-clock',
                                            'Programado' => 'fa-calendar-check'
                                        ];
                                        ?>
                                        <span class="badge <?= $badge_class[$equipo->estadoservicio] ?>">
                                            <i class="fas <?= $icon_class[$equipo->estadoservicio] ?> me-1"></i>
                                            <?= $equipo->estadoservicio ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('servicios/'.$equipo->idserviciocontratado) ?>" class="btn btn-info btn-sm" title="Ver Detalle del Servicio">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('equipos/editar/'.$equipo->idequipo) ?>" class="btn btn-warning btn-sm" title="Editar Asignación">
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
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay equipos asignados</h5>
                        <p class="text-muted">No se encontraron equipos asignados para esta consulta.</p>
                        <?php if (isset($servicio)): ?>
                        <a href="<?= base_url('equipos/asignar/'.$servicio->idserviciocontratado) ?>" class="btn btn-primary mt-2">
                            <i class="fas fa-plus-circle me-2"></i>Asignar Primer Equipo
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para tooltips -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>

<!-- Estilos adicionales -->
<style>
.card {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}
.table th {
    border-top: none;
    font-weight: 600;
    padding: 10px 15px;
    text-align: left;
}
.btn-group .btn {
    border-radius: 5px;
    margin-right: 5px;
}
.badge {
    font-size: 0.85em;
    padding: 0.5em 0.8em;
}

.custom-template .custom-content {
    padding: 20px 15px;
    max-height: calc(100vh - 90px); /* Esto mantiene el contenido dentro de la ventana */
    overflow-y: auto; /* Habilitar scroll vertical */
}

</style>
<?= $footer ?>