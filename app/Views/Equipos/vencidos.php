<?= $header ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 vencidos-header">
        <div>
            <h2 class="mb-0">
                <span class="alert-icon"></span><?= esc($titulo ?? 'Proyectos Vencidos') ?>
            </h2>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('equipos/vencidos') ?>" class="btn btn-refresh">
                <i class="fas fa-sync-alt me-1"></i>Actualizar
            </a>
            <a href="<?= base_url('equipos') ?>" class="btn btn-return">
                <i class="fas fa-arrow-left me-1"></i>Volver al tablero
            </a>
        </div>
    </div>

    <div class="card vencidos-table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table vencidos-table mb-0">
                    <thead>
                        <tr>
                            <th><i class="far fa-calendar-alt"></i>Fecha</th>
                            <th><i class="far fa-user"></i>Cliente</th>
                            <th><i class="fas fa-briefcase"></i>Servicio</th>
                            <th><i class="fas fa-map-marker-alt"></i>Lugar</th>
                            <th><i class="fas fa-info-circle"></i>Estado</th>
                            <th><i class="fas fa-user-cog"></i>Técnico asignado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($equipos)): ?>
                            <?php foreach ($equipos as $equipo): ?>
                                <?php
                                    $fechaServicio = $equipo['fechahoraservicio'] ?? null;
                                    $direccion = $equipo['direccion'] ?? $equipo['lugar'] ?? 'Sin dirección registrada';
                                ?>
                                <tr>
                                    <td data-label="Fecha" class="fecha-cell">
                                        <?= $fechaServicio ? date('d/m/Y', strtotime($fechaServicio)) : 'Sin fecha' ?>
                                        <span><?= $fechaServicio ? date('H:i', strtotime($fechaServicio)) : '' ?></span>
                                    </td>
                                    <td data-label="Cliente" class="cliente-cell">
                                        <span class="client-name"><?= esc($equipo['cliente_nombre'] ?? 'Cliente no especificado') ?></span>
                                        <?php if (!empty($equipo['cliente_telefono'])): ?>
                                            <span class="client-meta">
                                                <i class="fas fa-phone"></i><?= esc($equipo['cliente_telefono']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Servicio" class="servicio-cell">
                                        <span class="truncate-2" title="<?= esc($equipo['servicio'] ?? '') ?>">
                                            <?= esc($equipo['servicio'] ?? 'Servicio no definido') ?>
                                        </span>
                                        <span class="text-muted small">
                                            <?= esc($equipo['tipoevento'] ?? '') ?>
                                        </span>
                                    </td>
                                    <td data-label="Lugar" class="lugar-cell">
                                        <span class="truncate-2" title="<?= esc($direccion) ?>">
                                            <?= esc($direccion) ?>
                                        </span>
                                    </td>
                                    <td data-label="Estado">
                                        <span class="status-pill">
                                            <i class="fas fa-clock"></i> Vencido
                                        </span>
                                    </td>
                                    <td data-label="Técnico" class="tecnico-cell">
                                        <strong><?= esc($equipo['nombre_completo'] ?? 'Sin asignar') ?></strong>
                                        <?php if (!empty($equipo['cargo'])): ?>
                                            <span class="text-muted d-block small"><?= esc($equipo['cargo']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>
                                    <p class="mb-0">No hay proyectos vencidos registrados.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $footer ?>