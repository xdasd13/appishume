<?= $header ?>
<!-- Bootstrap CSS ya se carga en el header -->
<link rel="stylesheet" href="<?= base_url('assets/css/usuarios-listar.css') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!--  HEADER CORREGIDO -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-id-card me-2" aria-hidden="true"></i>Gestión de Credenciales
                    </h1>
                    <small class="text-muted"><?= htmlspecialchars($subtitulo ?? '') ?></small>
                </div>
                
                <div class="d-flex gap-2">
                    <!--  FILTROS DE ESTADO -->
                    <div class="btn-group" role="group" aria-label="Filtrar usuarios por estado">
                        <a href="<?= base_url('usuarios?estado=activos') ?>" 
                           class="btn btn-sm <?= ($filtro_actual ?? '') === 'activos' ? 'btn-primary' : 'btn-outline-primary' ?>"
                           aria-label="Ver usuarios activos">
                            <i class="fas fa-user-check me-1" aria-hidden="true"></i> Activos
                        </a>
                        <a href="<?= base_url('usuarios?estado=0') ?>" 
                           class="btn btn-sm <?= ($filtro_actual ?? '') === '0' ? 'btn-warning' : 'btn-outline-warning' ?>"
                           aria-label="Ver usuarios desactivados">
                            <i class="fas fa-user-times me-1" aria-hidden="true"></i> Desactivados
                        </a>
                        <a href="<?= base_url('usuarios?estado=todos') ?>" 
                           class="btn btn-sm <?= ($filtro_actual ?? '') === 'todos' ? 'btn-info' : 'btn-outline-info' ?>"
                           aria-label="Ver todos los usuarios">
                            <i class="fas fa-users me-1" aria-hidden="true"></i> Todos
                        </a>
                    </div>
                    
                    <!--  DROPDOWN CORREGIDO - AHORA FUNCIONAL -->
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" 
                                id="dropdownMenuButton" data-bs-toggle="dropdown" 
                                aria-expanded="false" aria-haspopup="true"
                                aria-label="Opciones para crear nuevo usuario">
                            <i class="fas fa-plus me-1" aria-hidden="true"></i> Nuevo
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('usuarios/crear/existente') ?>">
                                    <i class="fas fa-user me-2" aria-hidden="true"></i> Crear Credenciales
                                    <small class="d-block text-muted">Para personal existente</small>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('usuarios/crear/nuevo') ?>">
                                    <i class="fas fa-user-plus me-2" aria-hidden="true"></i> Crear Nuevo Personal
                                    <small class="d-block text-muted">Registrar nueva persona</small>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!--  ALERTAS -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite">
                    <i class="fas fa-check-circle me-2" aria-hidden="true"></i>
                    <?= htmlspecialchars(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar mensaje"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="assertive">
                    <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                    <?= htmlspecialchars(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar mensaje"></button>
                </div>
            <?php endif; ?>

            <!-- TARJETA PRINCIPAL -->
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 m-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-1" aria-hidden="true"></i> 
                        <?= htmlspecialchars($subtitulo ?? 'Lista de Usuarios') ?>
                    </h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($usuarios)): ?>
                        <div class="usuarios-grid" role="list" aria-label="Lista de usuarios">
                            <?php foreach ($usuarios as $usuario): ?>
                                <div class="card card-credential" role="listitem">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge <?= $usuario->tipo_usuario === 'admin' ? 'badge-admin' : 'badge-trabajador' ?>">
                                                <?= htmlspecialchars(ucfirst($usuario->tipo_usuario)) ?>
                                            </span>
                                            <span class="badge <?= $usuario->estado == 1 ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $usuario->estado == 1 ? 'Activo' : 'Desactivado' ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="user-header">
                                            <div class="user-avatar" aria-hidden="true">
                                                <?= htmlspecialchars(strtoupper(substr($usuario->nombres, 0, 1) . substr($usuario->apellidos, 0, 1))) ?>
                                            </div>
                                            <div class="user-info">
                                                <div class="user-name"><?= htmlspecialchars($usuario->nombres . ' ' . $usuario->apellidos) ?></div>
                                                <div class="user-role"><?= htmlspecialchars($usuario->cargo) ?></div>
                                            </div>
                                        </div>
                                        
                                        <div class="user-details">
                                            <div class="detail-item">
                                                <i class="fas fa-user" aria-hidden="true"></i>
                                                <strong>Usuario:</strong> 
                                                <span><?= htmlspecialchars($usuario->nombreusuario) ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                                <strong>Email:</strong> 
                                                <span><?= htmlspecialchars($usuario->email) ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-id-card" aria-hidden="true"></i>
                                                <strong><?= htmlspecialchars($usuario->tipodoc) ?>:</strong> 
                                                <span><?= htmlspecialchars($usuario->numerodoc) ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-phone" aria-hidden="true"></i>
                                                <strong>Tel:</strong> 
                                                <span><?= htmlspecialchars($usuario->telprincipal) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="action-buttons-container">
                                            <?php if ($usuario->estado == 1): ?>
                                                <!-- USUARIO ACTIVO -->
                                                <div class="btn-group-actions">
                                                    <a href="<?= base_url('usuarios/editar/' . $usuario->idusuario) ?>" 
                                                       class="btn btn-outline-primary"
                                                       aria-label="Editar usuario <?= htmlspecialchars($usuario->nombres) ?>">
                                                        <i class="fas fa-edit me-1" aria-hidden="true"></i> Editar
                                                    </a>
                                                    <button onclick="confirmarEliminacion(<?= $usuario->idusuario ?>, '<?= htmlspecialchars(addslashes($usuario->nombres)) ?>')" 
                                                            class="btn btn-outline-danger"
                                                            aria-label="Desactivar usuario <?= htmlspecialchars($usuario->nombres) ?>">
                                                        <i class="fas fa-user-times me-1" aria-hidden="true"></i> Desactivar
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <!-- USUARIO DESACTIVADO -->
                                                <div class="mb-2">
                                                    <button onclick="confirmarReactivacion(<?= $usuario->idusuario ?>, '<?= htmlspecialchars(addslashes($usuario->nombres)) ?>')" 
                                                            class="btn btn-success w-100 mb-1"
                                                            aria-label="Reactivar usuario <?= htmlspecialchars($usuario->nombres) ?>"
                                                            data-bs-toggle="tooltip" 
                                                            title="Restaurar el acceso del usuario">
                                                        <i class="fas fa-user-check me-1" aria-hidden="true"></i> Reactivar
                                                    </button>
                                                </div>
                                                
                                                <!-- ZONA DE ACCIONES PELIGROSAS -->
                                                <div class="danger-zone">
                                                    <span class="danger-label">ACCIÓN IRREVERSIBLE</span>
                                                    <button onclick="confirmarEliminacionPermanente(<?= $usuario->idusuario ?>, '<?= htmlspecialchars(addslashes($usuario->nombres)) ?>')" 
                                                            class="btn btn-danger-destructive"
                                                            aria-label="Eliminar permanentemente usuario <?= htmlspecialchars($usuario->nombres) ?>"
                                                            data-bs-toggle="tooltip" 
                                                            title="Eliminar permanentemente el usuario y todos sus datos">
                                                        <i class="fas fa-trash-alt me-1" aria-hidden="true"></i> Eliminar Permanentemente
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!--  ESTADO VACÍO -->
                        <div class="text-center py-5" aria-live="polite">
                            <i class="fas fa-users fa-3x text-muted mb-3" aria-hidden="true"></i>
                            <?php if (($filtro_actual ?? '') === '0'): ?>
                                <h5 class="text-muted">No hay usuarios desactivados</h5>
                                <p class="text-muted">Todos los usuarios están activos</p>
                                <a href="<?= base_url('usuarios?estado=activos') ?>" class="btn btn-primary">
                                    <i class="fas fa-user-check me-1" aria-hidden="true"></i> Ver Usuarios Activos
                                </a>
                            <?php elseif (($filtro_actual ?? '') === 'todos'): ?>
                                <h5 class="text-muted">No hay credenciales registradas</h5>
                                <p class="text-muted">El sistema no tiene usuarios registrados</p>
                                <a href="<?= base_url('usuarios/crear/existente') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-1" aria-hidden="true"></i> Crear Primera Credencial
                                </a>
                            <?php else: ?>
                                <h5 class="text-muted">No hay usuarios activos</h5>
                                <p class="text-muted">Comienza creando la primera credencial</p>
                                <a href="<?= base_url('usuarios/crear/existente') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-1" aria-hidden="true"></i> Crear Credencial
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  SCRIPTS -->
<!-- Bootstrap JS ya se carga en el footer -->
<!-- SweetAlert2 ya se carga en el footer -->

<script>
    //  FUNCIONALIDAD DEL DROPDOWN ASEGURADA
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar dropdowns de Bootstrap
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });

        // Mejorar navegación por teclado
        const buttons = document.querySelectorAll('.btn-group-actions .btn');
        buttons.forEach(button => {
            button.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
    // Manejo de errores
    window.addEventListener('error', function(e) {
        console.error('Error JavaScript:', e.error);
    });

    // Función de eliminación
    async function confirmarEliminacion(id, nombre) {
        try {
            const result = await Swal.fire({
                title: '¿Desacivar Credenciales?',
                html: `
                    <p>Está a punto de desactivar las credenciales de:</p>
                    <p><strong>${nombre}</strong></p>
                    <p class="text-danger"><small><i class="fas fa-exclamation-triangle"></i> Esta acción desactivará el acceso del usuario al sistema.</small></p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> Sí, desactivar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                reverseButtons: true,
                focusCancel: true
            });

            if (result.isConfirmed) {
                await eliminarUsuario(id, nombre);
            }
        } catch (error) {
            console.error('Error en confirmarEliminacion:', error);
            await Swal.fire({
                title: 'Error',
                text: 'Error al procesar la solicitud',
                icon: 'error',
                confirmButtonColor: '#4e73df'
            });
        }
    }

    // Función de confirmación de reactivación
    async function confirmarReactivacion(id, nombre) {
        try {
            const result = await Swal.fire({
                title: '¿Reactivar Credenciales?',
                html: `
                    <p>Está a punto de reactivar las credenciales de:</p>
                    <p><strong>${nombre}</strong></p>
                    <p class="text-success"><small><i class="fas fa-check-circle"></i> El usuario podrá acceder al sistema nuevamente.</small></p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check"></i> Sí, reactivar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                reverseButtons: true,
                focusCancel: true
            });

            if (result.isConfirmed) {
                await reactivarUsuario(id, nombre);
            }
        } catch (error) {
            console.error('Error en confirmarReactivacion:', error);
            await Swal.fire({
                title: 'Error',
                text: 'Error al procesar la solicitud',
                icon: 'error',
                confirmButtonColor: '#4e73df'
            });
        }
    }

    // Función para eliminar usuario
    async function eliminarUsuario(id, nombre) {
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
                throw new Error(data.message || 'Error desconocido');
            }
        } catch (error) {
            console.error('Error:', error);
            await Swal.fire({
                title: 'Error',
                text: error.message || 'Error al desactivar las credenciales del usuario',
                icon: 'error',
                confirmButtonColor: '#4e73df'
            });
        }
    }

    // Función para reactivar usuario
    async function reactivarUsuario(id, nombre) {
        Swal.fire({
            title: 'Reactivando...',
            text: 'Activando credenciales del usuario',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch('<?= base_url('usuarios/reactivar/') ?>' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                await Swal.fire({
                    title: '¡Reactivado!',
                    text: `Las credenciales de ${nombre} han sido reactivadas exitosamente.`,
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    timer: 2000,
                    timerProgressBar: true
                });
                location.reload();
            } else {
                throw new Error(data.message || 'Error desconocido');
            }
        } catch (error) {
            console.error('Error:', error);
            await Swal.fire({
                title: 'Error',
                text: error.message || 'Error al reactivar las credenciales del usuario',
                icon: 'error',
                confirmButtonColor: '#4e73df'
            });
        }
    }
    // Función de confirmación de eliminación permanente
async function confirmarEliminacionPermanente(id, nombre) {
    try {
        const result = await Swal.fire({
            title: '¡ATENCIÓN! Eliminación Permanente',
            html: `
                <div class="text-start">
                    <p><strong>Está a punto de eliminar PERMANENTEMENTE a:</strong></p>
                    <p class="text-center"><strong class="text-danger">${nombre}</strong></p>
                    <hr>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Esta acción NO se puede deshacer</strong>
                    </div>
                    <ul class="text-danger">
                        <li>Se eliminará el usuario de la base de datos</li>
                        <li>Se perderán todos sus datos</li>
                        <li>No podrá recuperarse posteriormente</li>
                    </ul>
                    <p class="text-muted"><small>Solo use esta opción si está completamente seguro.</small></p>
                </div>
            `,
            icon: 'warning',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            denyButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar permanentemente',
            denyButtonText: '<i class="fas fa-user-check"></i> Mejor reactivar',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            reverseButtons: true,
            focusCancel: true,
            width: '600px'
        });

        if (result.isConfirmed) {
            await eliminarUsuarioPermanente(id, nombre);
        } else if (result.isDenied) {
            await reactivarUsuario(id, nombre);
        }
    } catch (error) {
        console.error('Error en confirmarEliminacionPermanente:', error);
        await Swal.fire({
            title: 'Error',
            text: 'Error al procesar la solicitud',
            icon: 'error',
            confirmButtonColor: '#4e73df'
        });
    }
}

// Función para eliminar usuario permanentemente
async function eliminarUsuarioPermanente(id, nombre) {
    Swal.fire({
        title: 'Eliminando...',
        text: 'Eliminando usuario permanentemente de la base de datos',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await fetch('<?= base_url('usuarios/eliminar-permanente/') ?>' + id, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                title: '¡Eliminado!',
                text: `${nombre} ha sido eliminado permanentemente del sistema.`,
                icon: 'success',
                confirmButtonColor: '#4e73df',
                timer: 3000,
                timerProgressBar: true
            });
            
            // Recargar la página para actualizar la lista
            window.location.reload();
        } else {
            throw new Error(data.message || 'Error desconocido');
        }
    } catch (error) {
        console.error('Error:', error);
        await Swal.fire({
            title: 'Error',
            text: error.message || 'Error al eliminar el usuario permanentemente',
            icon: 'error',
            confirmButtonColor: '#4e73df'
        });
    }
}
</script>
<?= $footer ?>