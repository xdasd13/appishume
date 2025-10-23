<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/historial-index.css') ?>">

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-primary mb-1">
                        <i class="fas fa-history me-2"></i><?= $title ?>
                    </h2>
                    <p class="text-muted mb-0">Panel de administración del historial de actividades del sistema</p>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-success" onclick="exportarHistorial()">
                        <i class="fas fa-download me-1"></i>Exportar CSV
                    </button>
                    <button type="button" class="btn btn-warning" onclick="limpiarHistorial()">
                        <i class="fas fa-broom me-1"></i>Limpiar Antiguo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= $estadisticas['total_actividades'] ?></h4>
                            <p class="mb-0">Total Actividades</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= count($estadisticas['por_usuario']) ?></h4>
                            <p class="mb-0">Usuarios Activos</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= count($estadisticas['por_accion']) ?></h4>
                            <p class="mb-0">Tipos de Acción</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tasks fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <?php 
                            $trabajadorActivo = $reporte['trabajador_mas_activo'] ?? null;
                            $proyectosCompletados = $trabajadorActivo ? $trabajadorActivo['proyectos_completados'] : 0;
                            ?>
                            <h4 class="mb-0"><?= $proyectosCompletados ?></h4>
                            <p class="mb-0">Proyectos Completados</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-trophy fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                    </h5>
                </div>
                <div class="card-body">
                    <form id="filtrosForm" class="row g-3">
                        <div class="col-md-2">
                            <label for="tabla" class="form-label">Tabla</label>
                            <select class="form-select" id="tabla" name="tabla">
                                <option value="">Todas</option>
                                <option value="equipos" <?= $filtros['tabla'] === 'equipos' ? 'selected' : '' ?>>Equipos</option>
                                <option value="servicioscontratados" <?= $filtros['tabla'] === 'servicioscontratados' ? 'selected' : '' ?>>Servicios</option>
                                <option value="usuarios" <?= $filtros['tabla'] === 'usuarios' ? 'selected' : '' ?>>Usuarios</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="accion" class="form-label">Acción</label>
                            <select class="form-select" id="accion" name="accion">
                                <option value="">Todas</option>
                                <option value="crear" <?= $filtros['accion'] === 'crear' ? 'selected' : '' ?>>Crear</option>
                                <option value="actualizar" <?= $filtros['accion'] === 'actualizar' ? 'selected' : '' ?>>Actualizar</option>
                                <option value="cambio_estado" <?= $filtros['accion'] === 'cambio_estado' ? 'selected' : '' ?>>Cambio Estado</option>
                                <option value="asignar" <?= $filtros['accion'] === 'asignar' ? 'selected' : '' ?>>Asignar</option>
                                <option value="completar" <?= $filtros['accion'] === 'completar' ? 'selected' : '' ?>>Completar</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="usuario_id" class="form-label">Usuario</label>
                            <select class="form-select" id="usuario_id" name="usuario_id">
                                <option value="">Todos</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario['idusuario'] ?>" <?= $filtros['usuario_id'] == $usuario['idusuario'] ? 'selected' : '' ?>>
                                        <?= $usuario['nombres'] ? $usuario['nombres'] . ' ' . $usuario['apellidos'] : $usuario['nombreusuario'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="fecha_desde" class="form-label">Desde</label>
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="<?= $filtros['fecha_desde'] ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="fecha_hasta" class="form-label">Hasta</label>
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="<?= $filtros['fecha_hasta'] ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Actividades -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Historial de Actividades
                    </h5>
                    <div class="badge bg-secondary">
                        <?= count($actividades) ?> registros
                    </div>
                </div>
                <div class="card-body">
                    <div id="historial-loading" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando actividades...</p>
                    </div>
                    
                    <div id="historial-content">
                        <?php if (empty($actividades)): ?>
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle me-2"></i>
                                No se encontraron actividades con los filtros seleccionados
                            </div>
                        <?php else: ?>
                            <div class="timeline">
                                <?php foreach ($actividades as $actividad): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-marker">
                                            <i class="<?= getIconoAccion($actividad['accion']) ?> text-<?= getColorAccion($actividad['accion']) ?>"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="timeline-header">
                                                <h6 class="timeline-title mb-1"><?= esc($actividad['descripcion']) ?></h6>
                                                <div class="timeline-meta">
                                                    <small class="text-muted">
                                                        <i class="fas fa-user me-1"></i>
                                                        <?php 
                                                        $nombreUsuario = '';
                                                        if (!empty($actividad['nombres']) && !empty($actividad['apellidos'])) {
                                                            $nombres = explode(' ', $actividad['nombres']);
                                                            $apellidos = explode(' ', $actividad['apellidos']);
                                                            $nombreUsuario = $nombres[0] . ' ' . $apellidos[0] . '.';
                                                        } else {
                                                            $nombreUsuario = $actividad['nombreusuario'] ?? 'Usuario';
                                                        }
                                                        echo esc($nombreUsuario);
                                                        ?>
                                                        
                                                        <span class="mx-2">•</span>
                                                        
                                                        <i class="fas fa-table me-1"></i><?= esc($actividad['tabla_afectada']) ?>
                                                        
                                                        <span class="mx-2">•</span>
                                                        
                                                        <i class="fas fa-tag me-1"></i><?= esc($actividad['accion']) ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="timeline-date">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?= date('d/m/Y H:i:s', strtotime($actividad['created_at'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos CSS -->


<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar envío de filtros
    document.getElementById('filtrosForm').addEventListener('submit', function(e) {
        e.preventDefault();
        aplicarFiltros();
    });
});

/**
 * Aplicar filtros y recargar actividades
 */
function aplicarFiltros() {
    const formData = new FormData(document.getElementById('filtrosForm'));
    const params = new URLSearchParams(formData);
    
    // Recargar página con nuevos filtros
    window.location.href = '<?= base_url('historial') ?>?' + params.toString();
}

/**
 * Exportar historial a CSV
 */
function exportarHistorial() {
    const formData = new FormData(document.getElementById('filtrosForm'));
    const params = new URLSearchParams(formData);
    
    // Abrir enlace de descarga
    window.open('<?= base_url('historial/exportar-csv') ?>?' + params.toString(), '_blank');
}

/**
 * Limpiar historial antiguo
 */
function limpiarHistorial() {
    Swal.fire({
        title: '¿Limpiar Historial Antiguo?',
        html: `
            <div class="mb-3">
                <label for="dias-mantener" class="form-label">Días a mantener:</label>
                <input type="number" id="dias-mantener" class="form-control" value="365" min="30" max="3650">
                <small class="text-muted">Se eliminarán registros anteriores a esta cantidad de días</small>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545',
        preConfirm: () => {
            const dias = document.getElementById('dias-mantener').value;
            if (!dias || dias < 30) {
                Swal.showValidationMessage('Debe mantener al menos 30 días');
                return false;
            }
            return dias;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const dias = result.value;
            
            // Mostrar loading
            Swal.fire({
                title: 'Limpiando historial...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            
            // Hacer petición AJAX
            fetch('<?= base_url('historial/limpiar-historial') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    dias: dias,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Historial Limpiado!',
                        text: data.message,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'Error al limpiar el historial'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo conectar con el servidor'
                });
            });
        }
    });
}
</script>

<?php
// Funciones helper para iconos y colores
function getIconoAccion($accion) {
    $iconos = [
        'crear' => 'fas fa-plus-circle',
        'actualizar' => 'fas fa-edit',
        'eliminar' => 'fas fa-trash',
        'cambio_estado' => 'fas fa-exchange-alt',
        'asignar' => 'fas fa-user-plus',
        'completar' => 'fas fa-check-circle',
        'subir_archivo' => 'fas fa-upload',
        'comentario' => 'fas fa-comment'
    ];
    return $iconos[$accion] ?? 'fas fa-info-circle';
}

function getColorAccion($accion) {
    $colores = [
        'crear' => 'success',
        'actualizar' => 'info',
        'eliminar' => 'danger',
        'cambio_estado' => 'warning',
        'asignar' => 'primary',
        'completar' => 'success',
        'subir_archivo' => 'info',
        'comentario' => 'secondary'
    ];
    return $colores[$accion] ?? 'secondary';
}
?>

<?= $footer ?>
