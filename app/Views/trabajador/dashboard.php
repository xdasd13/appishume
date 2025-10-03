<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Mi Panel - ISHUME' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            z-index: 1000;
        }
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .task-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
            border-left: 4px solid #28a745;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-pendiente { background-color: #ffc107; color: #000; }
        .status-proceso { background-color: #17a2b8; color: #fff; }
        .status-completado { background-color: #28a745; color: #fff; }
        .btn-update {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3 text-center border-bottom">
            <h4><i class="fas fa-music me-2"></i>ISHUME</h4>
            <small>Panel Trabajador</small>
        </div>
        
        <div class="p-3 border-bottom">
            <div><strong><?= $usuario ?></strong></div>
            <small class="text-light">Trabajador</small>
        </div>
        
        <nav class="mt-3">
            <a href="<?= base_url('trabajador/dashboard') ?>" class="nav-link active">
                <i class="fas fa-tachometer-alt me-2"></i> Mi Panel
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-tasks me-2"></i> Mis Asignaciones
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-chart-line me-2"></i> Mi Progreso
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-calendar me-2"></i> Mi Cronograma
            </a>
        </nav>
        
        <div class="position-absolute bottom-0 w-100 p-3">
            <a href="<?= base_url('auth/logout') ?>" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mi Panel de Trabajo</h2>
            <span class="badge bg-info">Acceso Limitado</span>
        </div>
        
        <!-- Resumen de Tareas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="task-card text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4><?= count(array_filter($equipos ?? [], fn($e) => $e->estado === 'Pendiente')) ?></h4>
                    <div class="text-muted">Tareas Pendientes</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="task-card text-center">
                    <i class="fas fa-play-circle fa-2x text-info mb-2"></i>
                    <h4><?= count(array_filter($equipos ?? [], fn($e) => $e->estado === 'En Proceso')) ?></h4>
                    <div class="text-muted">En Proceso</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="task-card text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4><?= count(array_filter($equipos ?? [], fn($e) => $e->estado === 'Completado')) ?></h4>
                    <div class="text-muted">Completadas</div>
                </div>
            </div>
        </div>
        
        <!-- Mis Asignaciones -->
        <div class="task-card">
            <h5 class="mb-3"><i class="fas fa-tools me-2"></i>Mis Equipos Asignados</h5>
            
            <?php if (!empty($equipos)): ?>
                <div class="row">
                    <?php foreach ($equipos as $equipo): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title"><?= $equipo->equipo ?></h6>
                                    <span class="status-badge status-<?= strtolower(str_replace(' ', '', $equipo->estado)) ?>">
                                        <?= $equipo->estado ?>
                                    </span>
                                </div>
                                
                                <p class="card-text text-muted small">
                                    <strong>Servicio:</strong> <?= $equipo->servicio ?><br>
                                    <strong>Cliente:</strong> <?= $equipo->cliente ?><br>
                                    <strong>Fecha:</strong> <?= date('d/m/Y', strtotime($equipo->fechaservicio)) ?>
                                </p>
                                
                                <div class="d-flex gap-2">
                                    <?php if ($equipo->estado !== 'Completado'): ?>
                                        <button class="btn btn-sm btn-update" onclick="actualizarEstado(<?= $equipo->idequipo ?>, 'En Proceso')">
                                            <i class="fas fa-play me-1"></i>Iniciar
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="actualizarEstado(<?= $equipo->idequipo ?>, 'Completado')">
                                            <i class="fas fa-check me-1"></i>Completar
                                        </button>
                                    <?php else: ?>
                                        <span class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>Completado
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No tienes equipos asignados</h5>
                    <p class="text-muted">Cuando se te asignen equipos, aparecerán aquí.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Información Adicional -->
        <div class="task-card">
            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Información</h5>
            <div class="alert alert-info">
                <strong>Instrucciones:</strong>
                <ul class="mb-0 mt-2">
                    <li>Puedes actualizar el estado de tus equipos asignados</li>
                    <li>Marca como "En Proceso" cuando comiences a trabajar</li>
                    <li>Marca como "Completado" cuando termines la tarea</li>
                    <li>Si tienes dudas, contacta a tu supervisor</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Mostrar mensajes flash de error
        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Acceso Denegado',
                text: '<?= addslashes(session()->getFlashdata('error')) ?>',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#d33',
                timer: 5000,
                timerProgressBar: true
            });
        <?php endif; ?>

        // Mostrar mensajes flash de éxito
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '<?= addslashes(session()->getFlashdata('success')) ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#28a745',
                timer: 3000,
                timerProgressBar: true
            });
        <?php endif; ?>

        function actualizarEstado(equipoId, nuevoEstado) {
            Swal.fire({
                title: '¿Confirmar cambio?',
                text: `¿Deseas cambiar el estado a "${nuevoEstado}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar petición AJAX
                    fetch('<?= base_url('auth/actualizar-estado') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `equipo_id=${equipoId}&estado=${encodeURIComponent(nuevoEstado)}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Actualizado!',
                                text: data.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Ocurrió un error al actualizar el estado', 'error');
                    });
                }
            });
        }
    </script>
</body>
</html>
