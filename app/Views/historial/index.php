<?= $header ?>

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <!-- Título -->
                        <div>
                            <h3 class="mb-0 text-dark fw-bold">
                                <i class="fas fa-history text-warning"></i>
                                Historial de Actividades
                            </h3>
                            <p class="text-muted mb-0 mt-1">Registro de cambios en el tablero Kanban</p>
                        </div>
                        
                        <!-- Buscador -->
                        <div class="d-flex gap-2 mt-3 mt-md-0">
                            <select id="filtroUsuario" class="form-select" style="min-width: 250px;">
                                <option value="todos">Todos los usuarios</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario->idusuario ?>" <?= $filtro_usuario == $usuario->idusuario ? 'selected' : '' ?>>
                                        <?= esc($usuario->nombre_completo) ?> (<?= $usuario->total_cambios ?> cambios)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn btn-warning" onclick="buscarHistorial()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Historial -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <!-- Loading -->
                    <div id="loading" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Buscando actividades...</p>
                    </div>
                    
                    <!-- Tabla -->
                    <div id="tabla-container" class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">Fecha</th>
                                    <th width="8%">Hora</th>
                                    <th width="10%">Día</th>
                                    <th width="15%">Usuario</th>
                                    <th width="57%">Cambio Realizado</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-body">
                                <?php if (empty($historial)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No se encontraron actividades</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($historial as $item): ?>
                                        <tr>
                                            <!-- Fecha -->
                                            <td class="align-middle">
                                                <span class="text-dark fw-medium">
                                                    <?= date('d/m/Y', strtotime($item->fecha)) ?>
                                                </span>
                                            </td>
                                            
                                            <!-- Hora -->
                                            <td class="align-middle">
                                                <span class="badge bg-secondary">
                                                    <?= date('H:i:s', strtotime($item->fecha)) ?>
                                                </span>
                                            </td>
                                            
                                            <!-- Día -->
                                            <td class="align-middle">
                                                <span class="text-muted">
                                                    <?= obtenerNombreDia($item->fecha) ?>
                                                </span>
                                            </td>
                                            
                                            <!-- Usuario -->
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2">
                                                        <?= strtoupper(substr($item->usuario_nombre, 0, 1)) ?>
                                                    </div>
                                                    <span class="fw-medium text-dark">
                                                        <?= esc($item->usuario_nombre) ?>
                                                    </span>
                                                </div>
                                            </td>
                                            
                                            <!-- Cambio Realizado -->
                                            <td class="align-middle">
                                                <?= generarTextoAccion($item) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos CSS -->
<style>
/* Avatar circular */
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

/* Tabla responsiva */
.table-responsive {
    max-height: 600px;
    overflow-y: auto;
}

/* Hover en filas */
.table-hover tbody tr:hover {
    background-color: #fff8f0;
    cursor: pointer;
}

/* Badges de estado */
.badge-estado {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-pendiente {
    background-color: #fff3cd;
    color: #856404;
}

.badge-proceso {
    background-color: #cfe2ff;
    color: #084298;
}

.badge-completado {
    background-color: #d1e7dd;
    color: #0f5132;
}

/* Scrollbar personalizado */
.table-responsive::-webkit-scrollbar {
    width: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #ffc107;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #ff9800;
}

/* Responsive */
@media (max-width: 768px) {
    .table thead {
        display: none;
    }
    
    .table tbody tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    
    .table tbody td {
        display: block;
        text-align: right;
        padding: 10px 15px;
        border: none;
    }
    
    .table tbody td:before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
        color: #6c757d;
    }
}
</style>

<!-- JavaScript -->
<script>
/**
 * Buscar historial con filtro de usuario
 */
function buscarHistorial() {
    const loadingElement = document.getElementById('loading');
    const tablaContainer = document.getElementById('tabla-container');
    const filtroUsuario = document.getElementById('filtroUsuario').value;
    
    // Mostrar loading
    loadingElement.style.display = 'block';
    tablaContainer.style.opacity = '0.5';
    
    // Hacer petición AJAX
    fetch('<?= base_url('historial/buscar') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            usuario: filtroUsuario,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        loadingElement.style.display = 'none';
        tablaContainer.style.opacity = '1';
        
        if (data.success) {
            actualizarTabla(data.historial);
        } else {
            mostrarError(data.mensaje);
        }
    })
    .catch(error => {
        loadingElement.style.display = 'none';
        tablaContainer.style.opacity = '1';
        console.error('Error:', error);
        mostrarError('Error de conexión');
    });
}

/**
 * Actualizar contenido de la tabla
 */
function actualizarTabla(historial) {
    const tablaBody = document.getElementById('tabla-body');
    
    if (historial.length === 0) {
        tablaBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No se encontraron actividades</p>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    historial.forEach(item => {
        const inicial = item.usuario.charAt(0).toUpperCase();
        html += `
            <tr>
                <td class="align-middle">
                    <span class="text-dark fw-medium">${item.fecha}</span>
                </td>
                <td class="align-middle">
                    <span class="badge bg-secondary">${item.hora}</span>
                </td>
                <td class="align-middle">
                    <span class="text-muted">${item.dia}</span>
                </td>
                <td class="align-middle">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-2">${inicial}</div>
                        <span class="fw-medium text-dark">${item.usuario}</span>
                    </div>
                </td>
                <td class="align-middle">${item.accion}</td>
            </tr>
        `;
    });
    
    tablaBody.innerHTML = html;
}

/**
 * Mostrar mensaje de error
 */
function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje,
        confirmButtonColor: '#ffc107'
    });
}
</script>

<?php
/**
 * Obtener nombre del día de la semana en español
 */
function obtenerNombreDia($fecha) {
    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $numeroDia = date('w', strtotime($fecha));
    return $dias[$numeroDia];
}

/**
 * Generar texto descriptivo del cambio realizado
 */
function generarTextoAccion($item) {
    $html = '<div class="d-flex flex-column">';
    
    switch ($item->accion) {
        case 'cambiar_estado':
            $badgeAnterior = obtenerBadgeEstado($item->estado_anterior);
            $badgeNuevo = obtenerBadgeEstado($item->estado_nuevo);
            
            $html .= '<div class="mb-1">';
            $html .= '<strong>Cambió estado:</strong> ' . esc($item->equipo_descripcion);
            $html .= '</div>';
            $html .= '<div class="d-flex align-items-center gap-2">';
            $html .= $badgeAnterior;
            $html .= '<i class="fas fa-arrow-right text-muted"></i>';
            $html .= $badgeNuevo;
            $html .= '</div>';
            $html .= '<small class="text-muted mt-1">';
            $html .= '<i class="fas fa-briefcase"></i> ' . esc($item->servicio);
            $html .= ' | <i class="fas fa-user"></i> ' . esc($item->cliente_nombre);
            $html .= '</small>';
            break;
            
        case 'crear':
            $html .= '<div class="mb-1">';
            $html .= '<strong>Creó nuevo equipo:</strong> ' . esc($item->equipo_descripcion);
            $html .= '</div>';
            $html .= '<small class="text-muted">';
            $html .= '<i class="fas fa-briefcase"></i> ' . esc($item->servicio);
            $html .= '</small>';
            break;
            
        case 'reasignar':
            $html .= '<div class="mb-1">';
            $html .= '<strong>Reasignó equipo:</strong> ' . esc($item->equipo_descripcion);
            $html .= '</div>';
            $html .= '<small class="text-muted">';
            $html .= '<i class="fas fa-briefcase"></i> ' . esc($item->servicio);
            $html .= '</small>';
            break;
            
        default:
            $html .= ucfirst($item->accion);
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Obtener badge según el estado
 */
function obtenerBadgeEstado($estado) {
    $clases = [
        'Pendiente' => 'badge-pendiente',
        'En Proceso' => 'badge-proceso',
        'Completado' => 'badge-completado',
        'Programado' => 'badge-pendiente'
    ];
    
    $clase = $clases[$estado] ?? 'badge-secondary';
    $icono = $estado === 'Completado' ? '<i class="fas fa-check-circle"></i> ' : '';
    
    return '<span class="badge-estado ' . $clase . '">' . $icono . esc($estado) . '</span>';
}
?>

<?= $footer ?>
