<?= $header; ?>

<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <!-- Tarjeta principal con mejor diseño -->
                <div class="card card-border-top-primary">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title mb-0"><i class="fas fa-info-circle mr-2 text-primary"></i>Información de la Entrega</h4>
                            <div class="d-flex">
                                <a href="<?= base_url('/entregas') ?>" class="btn btn-light btn-sm mr-2">
                                    <i class="fas fa-arrow-left mr-1"></i>Volver al Listado
                                </a>
                                <?php if (($entrega['estado'] ?? 'pendiente') != 'completada'): ?>
                                    <a href="<?= base_url('/entregas/editar/' . $entrega['identregable']) ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit mr-1"></i>Editar Entrega
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled title="Entrega completada - No editable">
                                        <i class="fas fa-lock mr-1"></i>Editar Entrega
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Primera fila de información -->
                        <div class="row mb-4">
                            <!-- Información del CLIENTE (a quien se entrega) -->
                            <div class="col-md-6 mb-4">
                                <div class="card card-info h-100">
                                    <div class="card-header bg-primary text-white py-2">
                                        <h5 class="card-title mb-0"><i class="fas fa-user-tie mr-2"></i>Cliente - Receptor de la Entrega</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px;">
                                                <?= substr($entrega['nombre_cliente'], 0, 1) . substr($entrega['apellido_cliente'], 0, 1) ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?= $entrega['nombre_cliente'] . ' ' . $entrega['apellido_cliente'] ?></h6>
                                                <small class="text-muted">Cliente receptor</small>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Documento</small>
                                                <p class="mb-2 font-weight-semibold"><?= $entrega['tipodoc'] ?>: <?= $entrega['numerodoc'] ?></p>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Teléfono</small>
                                                <p class="mb-2 font-weight-semibold"><?= $entrega['telprincipal'] ?></p>
                                            </div>
                                        </div>
                                        <small class="text-muted">Dirección de entrega</small>
                                        <p class="mb-0"><?= $entrega['direccion'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del servicio -->
                            <div class="col-md-6 mb-4">
                                <div class="card card-success h-100">
                                    <div class="card-header bg-success text-white py-2">
                                        <h5 class="card-title mb-0"><i class="fas fa-briefcase mr-2"></i>Información del Servicio</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="text-success mb-2"><?= $entrega['servicio'] ?></h6>
                                        <p class="text-muted mb-3 small"><?= $entrega['descripcion_servicio'] ?></p>
                                        <div class="row">
                                            <div class="col-4">
                                                <small class="text-muted">Cantidad</small>
                                                <p class="mb-2 font-weight-bold"><?= $entrega['cantidad'] ?></p>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted">Precio Unitario</small>
                                                <p class="mb-2 font-weight-bold">S/ <?= number_format($entrega['precio'], 2) ?></p>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted">Total</small>
                                                <p class="mb-2 font-weight-bold text-success">S/ <?= number_format($entrega['cantidad'] * $entrega['precio'], 2) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Segunda fila de información -->
                        <div class="row mb-4">
                            <!-- Detalles de entrega -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-white py-2">
                                        <h5 class="card-title mb-0"><i class="fas fa-calendar-check mr-2 text-primary"></i>Detalles de Entrega</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">
                                            <div class="list-group-item d-flex align-items-center px-0">
                                                <div class="icon-circle bg-primary text-white mr-3">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </div>
                                                <div class="flex-fill">
                                                    <h6 class="mb-1">Fecha y Hora de Entrega</h6>
                                                    <p class="text-muted mb-0"><?= date('d/m/Y H:i', strtotime($entrega['fechahoraentrega'])) ?></p>
                                                </div>
                                            </div>
                                            <div class="list-group-item d-flex align-items-center px-0">
                                                <div class="icon-circle bg-success text-white mr-3">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </div>
                                                <div class="flex-fill">
                                                    <h6 class="mb-1">Ubicación del Servicio</h6>
                                                    <p class="text-muted mb-0"><?= $entrega['direccion_servicio'] ?></p>
                                                </div>
                                            </div>
                                            <div class="list-group-item d-flex align-items-center px-0">
                                                <div class="icon-circle bg-info text-white mr-3">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <div class="flex-fill">
                                                    <h6 class="mb-1">Fecha del Servicio Realizado</h6>
                                                    <p class="text-muted mb-0"><?= date('d/m/Y H:i', strtotime($entrega['fechahoraservicio'])) ?></p>
                                                </div>
                                            </div>
                                            <div class="list-group-item d-flex align-items-center px-0">
                                                <div class="icon-circle bg-warning text-white mr-3">
                                                    <i class="fas fa-history"></i>
                                                </div>
                                                <div class="flex-fill">
                                                    <h6 class="mb-1">Tiempo de Postproducción</h6>
                                                    <p class="text-muted mb-0">
                                                        <?php
                                                        $diasPostproduccion = $entrega['dias_postproduccion'] ?? 
                                                            round((strtotime($entrega['fechahoraentrega']) - strtotime($entrega['fechahoraservicio'])) / (60 * 60 * 24));
                                                        echo $diasPostproduccion . ' día' . ($diasPostproduccion != 1 ? 's' : '');
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Responsable de entrega (empleado que entregará) -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-white py-2">
                                        <h5 class="card-title mb-0"><i class="fas fa-user-tie mr-2 text-info"></i>Empleado que Realizará la Entrega</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($entrega['nombre_entrega'])): ?>
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px;">
                                                    <?= substr($entrega['nombre_entrega'], 0, 1) . substr($entrega['apellido_entrega'], 0, 1) ?>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?= $entrega['nombre_entrega'] . ' ' . $entrega['apellido_entrega'] ?></h6>
                                                    <small class="text-muted">Empleado responsable</small>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">Documento</small>
                                                    <p class="mb-2 font-weight-semibold"><?= $entrega['tipodoc_entrega'] ?>: <?= $entrega['numerodoc_entrega'] ?></p>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Tipo de documento</small>
                                                    <p class="mb-2 font-weight-semibold"><?= $entrega['tipodoc_entrega'] ?></p>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <small class="text-muted">Rol en la entrega</small>
                                                <p class="mb-0 badge badge-primary">Responsable de entrega física</p>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-4">
                                                <i class="fas fa-user-slash fa-2x text-muted mb-3"></i>
                                                <h6 class="text-muted mb-2">No se asignó responsable de entrega</h6>
                                                <p class="text-muted small">Esta entrega no tiene un empleado asignado para la entrega física</p>
                                                <a href="<?= base_url('/entregas/editar/' . $entrega['identregable']) ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-user-plus mr-1"></i>Asignar responsable
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tercera fila - Formato de entrega y observaciones -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-white py-2">
                                        <h5 class="card-title mb-0"><i class="fas fa-box-open mr-2 text-warning"></i>Formato y Detalles de la Entrega</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($entrega['observaciones'])): ?>
                                            <div class="alert alert-info">
                                                <h6 class="alert-heading"><i class="fas fa-info-circle mr-2"></i>Formato de entrega:</h6>
                                                <p class="mb-0"><?= nl2br(htmlspecialchars($entrega['observaciones'])) ?></p>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                No se especificó el formato de entrega. Ej: USB físico, link digital, cuadros, etc.
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                                    <div class="icon-circle bg-secondary text-white mr-3">
                                                        <i class="fas fa-file-contract"></i>
                                                    </div>
                                                    <div>
                                                        <span class="d-block text-muted small">ID de Cotización</span>
                                                        <span class="font-weight-bold"><?= $entrega['idcotizacion'] ?? 'N/A' ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                                    <div class="icon-circle bg-primary text-white mr-3">
                                                        <i class="fas fa-handshake"></i>
                                                    </div>
                                                    <div>
                                                        <span class="d-block text-muted small">ID de Contrato</span>
                                                        <span class="font-weight-bold"><?= $entrega['idcontrato'] ?? 'N/A' ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                                    <div class="icon-circle bg-success text-white mr-3">
                                                        <i class="fas fa-truck-loading"></i>
                                                    </div>
                                                    <div>
                                                        <span class="d-block text-muted small">ID de Entrega</span>
                                                        <span class="font-weight-bold">#<?= $entrega['identregable'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estado de la entrega -->
                        <div class="card mb-0">
                            <div class="card-header bg-white py-2">
                                <h5 class="card-title mb-0"><i class="fas fa-tasks mr-2 text-secondary"></i>Estado de la Entrega</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                // CORRECCIÓN: Usar el campo 'estado' de la base de datos en lugar de calcularlo
                                $estado = $entrega['estado'] ?? 'pendiente';
                                $badgeClass = 'warning';
                                $icon = 'clock';
                                $mensaje = '';
                                
                                if ($estado == 'completada') {
                                    $badgeClass = 'success';
                                    $icon = 'check-circle';
                                    $fechaEntrega = strtotime($entrega['fecha_real_entrega'] ?? $entrega['fechahoraentrega']);
                                    $hoy = time();
                                    $diasTranscurridos = round(($hoy - $fechaEntrega) / (60 * 60 * 24));
                                    $mensaje = "Entregado el: <strong>" . date('d/m/Y H:i', $fechaEntrega) . "</strong>";
                                    $mensaje .= "<br>Hace: <strong>" . $diasTranscurridos . " día" . ($diasTranscurridos != 1 ? 's' : '') . "</strong>";
                                } else {
                                    $fechaEntrega = strtotime($entrega['fechahoraentrega']);
                                    $hoy = time();
                                    
                                    if ($fechaEntrega < $hoy) {
                                        $badgeClass = 'danger';
                                        $icon = 'exclamation-triangle';
                                        $estado = 'VENCIDA';
                                        $diasVencida = round(($hoy - $fechaEntrega) / (60 * 60 * 24));
                                        $mensaje = "Fecha programada: <strong>" . date('d/m/Y H:i', $fechaEntrega) . "</strong>";
                                        $mensaje .= "<br>Vencida hace: <strong>" . $diasVencida . " día" . ($diasVencida != 1 ? 's' : '') . "</strong>";
                                    } else {
                                        $diasRestantes = round(($fechaEntrega - $hoy) / (60 * 60 * 24));
                                        $mensaje = "Programada para: <strong>" . date('d/m/Y H:i', $fechaEntrega) . "</strong>";
                                        $mensaje .= "<br>Tiempo restante: <strong>" . $diasRestantes . " día" . ($diasRestantes != 1 ? 's' : '') . "</strong>";
                                    }
                                }
                                ?>
                                
                                <div class="text-center">
                                    <span class="badge badge-<?= $badgeClass ?> badge-lg p-3">
                                        <i class="fas fa-<?= $icon ?> mr-2"></i>
                                        <?= strtoupper($estado) ?>
                                    </span>
                                    
                                    <p class="text-muted mt-2 mb-0">
                                        <?= $mensaje ?>
                                    </p>
                                    
                                    <?php if ($estado == 'completada' && !empty($entrega['fecha_real_entrega']) && $entrega['fecha_real_entrega'] != $entrega['fechahoraentrega']): ?>
                                        <p class="text-info small mt-2">
                                            <i class="fas fa-info-circle"></i> 
                                            La entrega se realizó el <?= date('d/m/Y H:i', strtotime($entrega['fecha_real_entrega'])) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-border-top-primary {
        border-top: 4px solid #4e73df !important;
    }
    .card-info {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    .avatar {
        font-weight: bold;
        font-size: 1rem;
    }
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .font-weight-semibold {
        font-weight: 600;
    }
    .list-group-flush .list-group-item {
        border: none;
        padding: 0.75rem 0;
    }
    .bg-light {
        background-color: #f8f9fc !important;
    }
    .badge-lg {
        font-size: 1.1rem;
        padding: 0.5rem 1rem;
    }
</style>

<?= $footer; ?>