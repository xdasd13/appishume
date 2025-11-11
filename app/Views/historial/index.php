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
    <!-- Historial de Actividades -->
    <div class="row">
        <div class="col-12">
            <!-- Loading -->
            <div id="loading" class="text-center py-5" style="display: none;">
                <div class="spinner-border text-warning" role="status"></div>
                <p class="mt-3 text-muted">Buscando actividades...</p>
            </div>

            <!-- Actividades Container -->
            <div id="tabla-container">
                <?php if (empty($historial)): ?>
                    <div class="card text-center py-5">
                        <div class="card-body">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No hay actividades registradas</h4>
                            <p class="text-muted">Las actividades del sistema aparecerán aquí</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div id="tabla-body" class="activity-grid">
                        <?php foreach ($historial as $index => $item): ?>
                            <div class="activity-card-compact">
                                <div class="card-header-compact">
                                    <div class="icon-compact <?= obtenerClaseIcono($item->accion) ?>">
                                        <i class="<?= obtenerIcono($item->accion) ?>"></i>
                                    </div>
                                    <div class="user-name"><?= esc($item->usuario_nombre) ?></div>
                                </div>
                                <div class="card-meta-compact">
                                    <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($item->fecha)) ?></span>
                                    <span><i class="fas fa-clock"></i> <?= date('H:i', strtotime($item->fecha)) ?></span>
                                </div>
                                <div class="card-body-compact">
                                    <?= generarTextoAccionSimple($item) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Estilos CSS Grid Compacto -->
<style>
/* Grid de 3 columnas responsivo */
.activity-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

/* Card compacta */
.activity-card-compact {
    background: white;
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.activity-card-compact:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

/* Header compacto */
.card-header-compact {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f8f9fa;
}

.icon-compact {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: white;
    flex-shrink: 0;
}

.icon-cambiar {
    background: #FF9900;
}

.icon-crear {
    background: #2ECC71;
}

.icon-reasignar {
    background: #9B59B6;
}

.icon-default {
    background: #3498DB;
}

.user-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2c3e50;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Meta compacta */
.card-meta-compact {
    display: flex;
    gap: 12px;
    font-size: 0.8rem;
    color: #7f8c8d;
    flex-wrap: wrap;
}

.card-meta-compact span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.card-meta-compact i {
    color: #FF9900;
    font-size: 0.75rem;
}

/* Body compacto */
.card-body-compact {
    color: #34495e;
    font-size: 0.875rem;
    line-height: 1.5;
}

.card-body-compact p {
    margin: 0 0 8px 0;
}

.card-body-compact strong {
    color: #2c3e50;
    font-size: 0.85rem;
}

/* Badges de estado */
.estado-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    margin: 0 4px;
}

.badge-pendiente {
    background: #FFF3CD;
    color: #856404;
}

.badge-proceso {
    background: #D1ECF1;
    color: #0C5460;
}

.badge-completado {
    background: #D4EDDA;
    color: #155724;
}

.badge-programado {
    background: #FFE5D9;
    color: #D84315;
}

/* Detalles */
.detail-item {
    display: inline-block;
    margin-right: 10px;
    color: #7f8c8d;
    font-size: 0.8rem;
}

.detail-item i {
    color: #FF9900;
    margin-right: 4px;
    font-size: 0.75rem;
}

/* Responsive */
@media (max-width: 1200px) {
    .activity-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .activity-grid {
        grid-template-columns: 1fr;
    }
    
    .card-meta-compact {
        flex-direction: column;
        gap: 6px;
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

        console.log('Filtro seleccionado:', filtroUsuario); // Debug

        // Mostrar loading
        loadingElement.style.display = 'block';
        tablaContainer.style.opacity = '0.5';

        // Preparar datos del formulario
        const formData = new FormData();
        formData.append('usuario', filtroUsuario);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        // Hacer petición AJAX
        fetch('<?= base_url('historial/buscar') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
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
     * Actualizar contenido con cards compactas en grid
     */
    function actualizarTabla(historial) {
        const tablaBody = document.getElementById('tabla-body');

        if (historial.length === 0) {
            tablaBody.innerHTML = `
                <div class="card text-center py-5" style="grid-column: 1 / -1;">
                    <div class="card-body">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No se encontraron actividades</h4>
                        <p class="text-muted">Las actividades del sistema aparecerán aquí</p>
                    </div>
                </div>
            `;
            return;
        }

        let html = '';
        historial.forEach((item, index) => {
            const iconClass = getIconClass(item.accion_tipo);
            const icono = getIcono(item.accion_tipo);
            
            html += `
                <div class="activity-card-compact">
                    <div class="card-header-compact">
                        <div class="icon-compact ${iconClass}">
                            <i class="${icono}"></i>
                        </div>
                        <div class="user-name">${item.usuario}</div>
                    </div>
                    <div class="card-meta-compact">
                        <span><i class="fas fa-calendar"></i> ${item.fecha}</span>
                        <span><i class="fas fa-clock"></i> ${item.hora}</span>
                    </div>
                    <div class="card-body-compact">
                        ${item.accion}
                    </div>
                </div>
            `;
        });

        tablaBody.innerHTML = html;
    }
    
    /**
     * Obtener clase de icono según tipo de acción
     */
    function getIconClass(tipo) {
        const clases = {
            'cambiar_estado': 'icon-cambiar',
            'crear': 'icon-crear',
            'reasignar': 'icon-reasignar'
        };
        return clases[tipo] || 'icon-default';
    }
    
    /**
     * Obtener icono FontAwesome según tipo de acción
     */
    function getIcono(tipo) {
        const iconos = {
            'cambiar_estado': 'fas fa-exchange-alt',
            'crear': 'fas fa-plus-circle',
            'reasignar': 'fas fa-user-cog'
        };
        return iconos[tipo] || 'fas fa-info-circle';
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
function obtenerNombreDia($fecha)
{
    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $numeroDia = date('w', strtotime($fecha));
    return $dias[$numeroDia];
}

/**
 * Obtener clase de icono según la acción
 */
function obtenerClaseIcono($accion)
{
    $clases = [
        'cambiar_estado' => 'icon-cambiar',
        'crear' => 'icon-crear',
        'reasignar' => 'icon-reasignar'
    ];
    return $clases[$accion] ?? 'icon-default';
}

/**
 * Obtener icono FontAwesome según la acción
 */
function obtenerIcono($accion)
{
    $iconos = [
        'cambiar_estado' => 'fas fa-exchange-alt',
        'crear' => 'fas fa-plus-circle',
        'reasignar' => 'fas fa-user-cog'
    ];
    return $iconos[$accion] ?? 'fas fa-info-circle';
}

/**
 * Obtener color de avatar rotativo
 */
function obtenerColorAvatar($index)
{
    $colores = ['avatar-orange', 'avatar-purple', 'avatar-blue', 'avatar-green'];
    return $colores[$index % 4];
}

/**
 * Generar texto de acción simple y limpio
 */
function generarTextoAccionSimple($item)
{
    $html = '';

    switch ($item->accion) {
        case 'cambiar_estado':
            $badgeAnterior = obtenerBadgeEstadoSimple($item->estado_anterior);
            $badgeNuevo = obtenerBadgeEstadoSimple($item->estado_nuevo);

            $html .= '<p class="mb-2"><strong>Cambió estado:</strong> ' . esc($item->equipo_descripcion) . '</p>';
            $html .= '<div class="mb-2">';
            $html .= $badgeAnterior . ' <i class="fas fa-arrow-right mx-2"></i> ' . $badgeNuevo;
            $html .= '</div>';
            $html .= '<div>';
            $html .= '<span class="detail-item"><i class="fas fa-briefcase"></i>' . esc($item->servicio) . '</span>';
            $html .= '<span class="detail-item"><i class="fas fa-user"></i>' . esc($item->cliente_nombre) . '</span>';
            $html .= '</div>';
            break;

        case 'crear':
            $html .= '<p class="mb-2"><strong>Creó nuevo equipo:</strong> ' . esc($item->equipo_descripcion) . '</p>';
            $html .= '<span class="detail-item"><i class="fas fa-briefcase"></i>' . esc($item->servicio) . '</span>';
            break;

        case 'reasignar':
            $html .= '<p class="mb-2"><strong>Reasignó equipo:</strong> ' . esc($item->equipo_descripcion) . '</p>';
            $html .= '<span class="detail-item"><i class="fas fa-briefcase"></i>' . esc($item->servicio) . '</span>';
            break;

        default:
            $html .= '<p>' . ucfirst($item->accion) . '</p>';
    }

    return $html;
}

/**
 * Obtener badge simple según el estado
 */
function obtenerBadgeEstadoSimple($estado)
{
    $clases = [
        'Pendiente' => 'badge-pendiente',
        'En Proceso' => 'badge-proceso',
        'Completado' => 'badge-completado',
        'Programado' => 'badge-programado'
    ];

    $clase = $clases[$estado] ?? 'badge-pendiente';
    return '<span class="estado-badge ' . $clase . '">' . esc($estado) . '</span>';
}

/**
 * Obtener badge moderno según el estado
 */
function obtenerBadgeEstadoModerno($estado)
{
    $clases = [
        'Pendiente' => 'badge-pendiente',
        'En Proceso' => 'badge-proceso',
        'Completado' => 'badge-completado',
        'Programado' => 'badge-programado'
    ];

    $iconos = [
        'Pendiente' => 'fas fa-clock',
        'En Proceso' => 'fas fa-spinner',
        'Completado' => 'fas fa-check-circle',
        'Programado' => 'fas fa-calendar-check'
    ];

    $clase = $clases[$estado] ?? 'badge-pendiente';
    $icono = $iconos[$estado] ?? 'fas fa-info-circle';

    return '<span class="estado-badge ' . $clase . '"><i class="' . $icono . '"></i>' . esc($estado) . '</span>';
}

/**
 * Generar texto descriptivo del cambio realizado (versión antigua para compatibilidad)
 */
function generarTextoAccion($item)
{
    return generarTextoAccionSimple($item);
}

/**
 * Obtener badge según el estado
 */
function obtenerBadgeEstado($estado)
{
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