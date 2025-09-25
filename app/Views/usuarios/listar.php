<?= $header ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<style>
    /* RESET Y ESTILOS BASE */
    .container-fluid.py-4 {
        margin: 0 !important;
        padding: 1.5rem !important;
    }
    
    /*  CONTENEDOR PRINCIPAL MEJORADO */
    .usuarios-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        padding: 1rem 0;
        margin: 0;
        width: 100%;
    }
    
    /*  TARJETA BASE CON TEXTO MÁS GRANDE */
    .usuarios-grid .card-credential {
        background: #ffffff;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease-in-out;
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 380px; /* Aumentado para texto más grande */
        width: 100%;
        position: relative;
        overflow: hidden;
    }
    
    /*  HOVER */
    .usuarios-grid .card-credential:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-color: #4e73df;
    }
    
    /*  HEADER */
    .usuarios-grid .card-credential .card-header {
        background: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        padding: 12px 16px;
        border-radius: 8px 8px 0 0;
        flex-shrink: 0;
    }
    
    /* BODY CON PADDING MEJORADO */
    .usuarios-grid .card-credential .card-body {
        padding: 20px !important; /* Aumentado */
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    /*  FOOTER */
    .usuarios-grid .card-credential .card-footer {
        background: #f8f9fc;
        border-top: 1px solid #e3e6f0;
        padding: 16px !important; /* Aumentado */
        border-radius: 0 0 8px 8px;
        margin-top: auto;
        flex-shrink: 0;
    }
    
    /*  USUARIO INFO */
    .usuarios-grid .card-credential .user-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px; /* Aumentado */
        width: 100%;
    }
    
    /* AVATAR MÁS GRANDE */
    .usuarios-grid .card-credential .user-avatar {
        width: 50px; /* Aumentado */
        height: 50px; /* Aumentado */
        background: linear-gradient(135deg,rgb(221, 107, 0) 0%,rgb(240, 156, 0) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 16px; /* Aumentado */
        margin-right: 12px;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    
    /*  HOVER AVATAR */
    .usuarios-grid .card-credential:hover .user-avatar {
        transform: scale(1.05);
    }
    
    /*  USER INFO */
    .usuarios-grid .card-credential .user-info {
        flex: 1;
        min-width: 0;
        overflow: hidden;
    }
    
    /*  USER NAME MÁS GRANDE */
    .usuarios-grid .card-credential .user-name {
        font-size: 16px !important; /* Aumentado significativamente */
        font-weight: 600;
        color: #2d3748;
        margin: 0;
        line-height: 1.4;
        word-wrap: break-word;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /*  USER ROLE MÁS GRANDE */
    .usuarios-grid .card-credential .user-role {
        font-size: 14px !important; /* Aumentado */
        color: #718096;
        margin: 4px 0 0 0; /* Aumentado */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /*  DETALLES */
    .usuarios-grid .card-credential .user-details {
        flex: 1;
        margin-top: 12px; /* Aumentado */
    }
    
    /*  DETAIL ITEM MÁS GRANDE */
    .usuarios-grid .card-credential .detail-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 10px; /* Aumentado */
        font-size: 14px !important; /* Aumentado significativamente */
        line-height: 1.5;
        width: 100%;
    }
    
    .usuarios-grid .card-credential .detail-item:last-child {
        margin-bottom: 0;
    }
    
    /*  ICONOS MÁS GRANDES */
    .usuarios-grid .card-credential .detail-item i {
        width: 18px; /* Aumentado */
        color: #a0aec0;
        margin-right: 8px; /* Aumentado */
        margin-top: 2px;
        flex-shrink: 0;
        text-align: center;
        font-size: 14px; /* Aumentado */
    }
    
    /*  STRONG */
    .usuarios-grid .card-credential .detail-item strong {
        color: #4a5568;
        margin-right: 6px; /* Aumentado */
        font-weight: 600;
        font-size: 14px; /* Aumentado */
    }
    
    /* SPAN */
    .usuarios-grid .card-credential .detail-item span {
        color: #2d3748;
        word-break: break-all;
        flex: 1;
        overflow: hidden;
        font-size: 14px; /* Aumentado */
    }
    
    /*  BADGES MÁS GRANDES */
    .badge-admin {
        background: #e53e3e !important;
        color: white !important;
        font-size: 12px !important; /* Aumentado */
    }
    
    .badge-trabajador {
        background: #3182ce !important;
        color: white !important;
        font-size: 12px !important; /* Aumentado */
    }
    
    .usuarios-grid .card-credential .badge {
        font-size: 12px !important; /* Aumentado */
        font-weight: 500;
        padding: 6px 10px !important; /* Aumentado */
        border-radius: 6px;
        border: none;
    }
    
    /*  BOTONES MÁS GRANDES */
    .usuarios-grid .card-credential .btn-group-actions {
        display: flex;
        gap: 8px; /* Aumentado */
        width: 100%;
    }
    
    .usuarios-grid .card-credential .btn-group-actions .btn {
        flex: 1;
        font-size: 14px !important; /* Aumentado significativamente */
        padding: 10px 12px !important; /* Aumentado */
        border-radius: 6px;
        font-weight: 500;
        text-align: center;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    
    /*  DROPDOWN FUNCIONAL */
    .dropdown-menu {
        border: 1px solid #e3e6f0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-radius: 8px;
        padding: 8px 0;
    }
    
    .dropdown-item {
        padding: 10px 16px;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .dropdown-toggle::after {
        margin-left: 8px;
    }
    
    /* ✅ ESTILOS PARA FOCUS MEJORADOS */
    .btn-group .btn:focus,
    .dropdown-toggle:focus,
    .btn-group-actions .btn:focus,
    .dropdown-item:focus {
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        outline: none;
    }
    
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
    
    /* ✅ RESPONSIVIDAD MEJORADA */
    @media (max-width: 576px) {
        .container-fluid.py-4 {
            padding: 1rem !important;
        }
        
        .usuarios-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .usuarios-grid .card-credential {
            min-height: 360px;
        }
        
        .usuarios-grid .card-credential .btn-group-actions {
            flex-direction: column;
            gap: 6px;
        }
        
        .usuarios-grid .card-credential .user-name {
            font-size: 15px !important;
        }
        
        .usuarios-grid .card-credential .user-role {
            font-size: 13px !important;
        }
        
        .usuarios-grid .card-credential .detail-item {
            font-size: 13px !important;
        }
        
        .usuarios-grid .card-credential .btn-group-actions .btn {
            font-size: 13px !important;
            padding: 8px 10px !important;
        }
        
        /* ✅ HEADER RESPONSIVE */
        .d-flex.justify-content-between.align-items-center.mb-4 {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start !important;
        }
        
        .d-flex.gap-2 {
            width: 100%;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .btn-group {
            order: 2;
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn-group .btn {
            flex: 1;
            font-size: 12px;
        }
        
        .dropdown {
            order: 1;
        }
    }
    
    @media (min-width: 577px) and (max-width: 768px) {
        .usuarios-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
        
        .usuarios-grid .card-credential .user-name {
            font-size: 15px !important;
        }
        
        .usuarios-grid .card-credential .detail-item {
            font-size: 13px !important;
        }
    }
    
    @media (min-width: 769px) and (max-width: 1024px) {
        .usuarios-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
    }
    
    @media (min-width: 1025px) and (max-width: 1399px) {
        .usuarios-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
    }
    
    @media (min-width: 1400px) {
        .usuarios-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }
        
        /* ✅ TEXTO AÚN MÁS GRANDE EN PANTALLAS GRANDES */
        .usuarios-grid .card-credential .user-name {
            font-size: 17px !important;
        }
        
        .usuarios-grid .card-credential .user-role {
            font-size: 15px !important;
        }
        
        .usuarios-grid .card-credential .detail-item {
            font-size: 15px !important;
        }
        
        .usuarios-grid .card-credential .btn-group-actions .btn {
            font-size: 15px !important;
            padding: 12px 14px !important;
        }
    }
</style>

<!--  INCLUIR FONT AWESOME PARA LOS ICONOS -->
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
                                        <div class="btn-group-actions">
                                            <a href="<?= base_url('usuarios/editar/' . $usuario->idusuario) ?>" 
                                               class="btn btn-outline-primary"
                                               aria-label="Editar usuario <?= htmlspecialchars($usuario->nombres) ?>">
                                                <i class="fas fa-edit me-1" aria-hidden="true"></i> Editar
                                            </a>
                                            <?php if ($usuario->estado == 1): ?>
                                                <button onclick="confirmarEliminacion(<?= $usuario->idusuario ?>, '<?= htmlspecialchars(addslashes($usuario->nombres)) ?>')" 
                                                        class="btn btn-outline-danger"
                                                        aria-label="Desactivar usuario <?= htmlspecialchars($usuario->nombres) ?>">
                                                    <i class="fas fa-user-times me-1" aria-hidden="true"></i> Desactivar
                                                </button>
                                            <?php else: ?>
                                                <button onclick="confirmarReactivacion(<?= $usuario->idusuario ?>, '<?= htmlspecialchars(addslashes($usuario->nombres)) ?>')" 
                                                        class="btn btn-outline-success"
                                                        aria-label="Reactivar usuario <?= htmlspecialchars($usuario->nombres) ?>">
                                                    <i class="fas fa-user-check me-1" aria-hidden="true"></i> Reactivar
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- ✅ ESTADO VACÍO -->
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

<!-- ✅ SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ✅  FUNCIONALIDAD DEL DROPDOWN ASEGURADA
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

    // ✅ MANEJO DE ERRORES
    window.addEventListener('error', function(e) {
        console.error('Error JavaScript:', e.error);
    });

    // ✅ FUNCIÓN DE ELIMINACIÓN
    async function confirmarEliminacion(id, nombre) {
        try {
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

    // ✅ FUNCIÓN DE REACTIVACIÓN
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

    // ✅ FUNCIÓN PARA ELIMINAR USUARIO
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

    // ✅ FUNCIÓN PARA REACTIVAR USUARIO
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
</script>

<?= $footer ?>