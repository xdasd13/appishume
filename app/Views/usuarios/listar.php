<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <style>
        .card-credential {
            transition: transform 0.2s;
            border: 1px solid #dee2e6;
            border-radius: 10px;
        }
        .card-credential:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .badge-admin {
            background-color: #dc3545;
        }
        .badge-trabajador {
            background-color: #0d6efd;
        }
        .user-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg,rgb(255, 136, 0),rgb(224, 126, 60));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?= $header ?>
    
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <!-- CORRECCIÓN: El dropdown debe estar dentro del div de encabezado -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-id-card me-2"></i>Gestión de Credenciales
                    </h1>
                    
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" 
                                id="dropdownMenuButton" data-bs-toggle="dropdown" 
                                aria-expanded="false" aria-haspopup="true">
                            <i class="fas fa-plus me-1"></i> Nuevo
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('usuarios/crear/existente') ?>">
                                    <i class="fas fa-user me-2"></i> Crear Credenciales
                                    <small class="d-block text-muted">Para personal existente</small>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('usuarios/crear/nuevo') ?>">
                                    <i class="fas fa-user-plus me-2"></i> Crear Nuevo Personal
                                    <small class="d-block text-muted">Registrar nueva persona</small>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Fin de la corrección -->

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-users me-1"></i> Lista de Credenciales Activas
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($usuarios)): ?>
                            <div class="row">
                                <?php foreach ($usuarios as $usuario): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card card-credential h-100">
                                            <div class="card-header bg-light py-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge <?= $usuario->tipo_usuario === 'admin' ? 'badge-admin' : 'badge-trabajador' ?>">
                                                        <?= ucfirst($usuario->tipo_usuario) ?>
                                                    </span>
                                                    <span class="badge bg-success">Activo</span>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="user-avatar me-3">
                                                        <?= strtoupper(substr($usuario->nombres, 0, 1) . substr($usuario->apellidos, 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0"><?= $usuario->nombres . ' ' . $usuario->apellidos ?></h6>
                                                        <small class="text-muted"><?= $usuario->cargo ?></small>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-user me-1"></i> Usuario: <?= $usuario->nombreusuario ?>
                                                    </small>
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-envelope me-1"></i> Email: <?= $usuario->email ?>
                                                    </small>
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-id-card me-1"></i> <?= $usuario->tipodoc ?>: <?= $usuario->numerodoc ?>
                                                    </small>
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-phone me-1"></i> Tel: <?= $usuario->telprincipal ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-white">
                                                <div class="d-flex justify-content-between">
                                                    <a href="<?= base_url('usuarios/editar/' . $usuario->idusuario) ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                    <button onclick="confirmarEliminacion(<?= $usuario->idusuario ?>, '<?= $usuario->nombres ?>')" 
                                                            class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay credenciales registradas</h5>
                                <p class="text-muted">Comienza creando la primera credencial</p>
                                <!-- Actualizar este enlace para que vaya a alguna de las opciones -->
                                <a href="<?= base_url('usuarios/crear/existente') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Crear Credencial
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar las credenciales de <strong id="userName"></strong>?</p>
                    <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <?= $footer ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        async function confirmarEliminacion(id, nombre) {
            const result = await Swal.fire({
                title: '¿Eliminar Credenciales?',
                html: `
                    <p>Está a punto de eliminar las credenciales de:</p>
                    <p><strong>${nombre}</strong></p>
                    <p class="text-danger"><small><i class="fas fa-exclamation-triangle"></i> Esta acción desactivará el acceso del usuario al sistema.</small></p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                reverseButtons: true,
                focusCancel: true
            });

            if (result.isConfirmed) {
                await eliminarUsuario(id, nombre);
            }
        }

        async function eliminarUsuario(id, nombre) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                text: 'Desactivando credenciales del usuario',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch('<?= base_url('usuarios/eliminar/') ?>' + id, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    await Swal.fire({
                        title: '¡Eliminado!',
                        text: `Las credenciales de ${nombre} han sido desactivadas exitosamente.`,
                        icon: 'success',
                        confirmButtonColor: '#4e73df',
                        timer: 2000,
                        timerProgressBar: true
                    });
                    location.reload();
                } else {
                    await Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#4e73df'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                await Swal.fire({
                    title: 'Error',
                    text: 'Error al eliminar las credenciales del usuario',
                    icon: 'error',
                    confirmButtonColor: '#4e73df'
                });
            }
        }
    </script>
</body>
</html>