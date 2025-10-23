<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/historial-index.css') ?>">

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="activity-header">
                <div class="activity-title">
                    <h2 class="mb-1">REGISTRO DE ACTIVIDAD</h2>
                    <div class="activity-divider"></div>
                </div>
                <div class="activity-controls">
                    <div class="filter-group">
                        <select id="filtroFecha" class="form-select me-2" onchange="aplicarFiltros()">
                            <option value="hoy" <?= $filtro_fecha === 'hoy' ? 'selected' : '' ?>>Hoy</option>
                            <option value="ayer" <?= $filtro_fecha === 'ayer' ? 'selected' : '' ?>>Ayer</option>
                            <option value="semana" <?= $filtro_fecha === 'semana' ? 'selected' : '' ?>>Esta semana</option>
                            <option value="mes" <?= $filtro_fecha === 'mes' ? 'selected' : '' ?>>Este mes</option>
                        </select>
                        
                        <select id="filtroUsuario" class="form-select me-2" onchange="aplicarFiltros()">
                            <option value="todos">Todos los usuarios</option>
                            <?php foreach ($usuarios_activos as $usuario): ?>
                                <option value="<?= $usuario->idusuario ?>" <?= $filtro_usuario == $usuario->idusuario ? 'selected' : '' ?>>
                                    <?= esc($usuario->nombre_completo) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <button type="button" class="btn-refresh" onclick="actualizarHistorial()">
                            <i class="fas fa-sync-alt"></i> Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="row">
        <div class="col-12">
            <div class="activity-container">
                <div id="historial-loading" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando actividades...</p>
                </div>
                
                <div id="historial-content">
                    <?php if (empty($historial)): ?>
                        <div class="no-activity">
                            <i class="fas fa-info-circle"></i>
                            <p>No se encontraron actividades para el período seleccionado</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($historial as $item): ?>
                            <div class="activity-item">
                                <div class="activity-time">
                                    <?= date('H:i', strtotime($item->fecha)) ?>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-user">
                                        <?= esc($item->usuario_nombre) ?>
                                    </div>
                                    <div class="activity-action">
                                        <?php if ($item->accion === 'cambiar_estado'): ?>
                                            movió "<?= esc($item->equipo_descripcion) ?>"
                                            <div class="state-change">
                                                <span class="state-from"><?= esc($item->estado_anterior) ?></span>
                                                <i class="fas fa-arrow-right"></i>
                                                <span class="state-to <?= $item->estado_nuevo === 'Completado' ? 'completed' : '' ?>">
                                                    <?= esc($item->estado_nuevo) ?>
                                                    <?= $item->estado_nuevo === 'Completado' ? ' ✓' : '' ?>
                                                </span>
                                            </div>
                                        <?php elseif ($item->accion === 'crear'): ?>
                                            creó nuevo equipo "<?= esc($item->equipo_descripcion) ?>"
                                        <?php elseif ($item->accion === 'reasignar'): ?>
                                            reasignó "<?= esc($item->equipo_descripcion) ?>"
                                        <?php endif; ?>
                                    </div>
                                    <div class="activity-service">
                                        <?= obtenerIconoCategoria($item->categoria) ?> Servicio: <?= esc($item->servicio) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos CSS -->
<<<<<<< HEAD
<style>
/* Paleta de colores de la empresa */
:root {
    --primary-orange: #ff6b35;
    --light-orange: #ff8c5a;
    --dark-orange: #e55a2b;
    --white: #ffffff;
    --light-gray: #f8f9fa;
    --medium-gray: #6c757d;
    --dark-gray: #343a40;
    --border-color: #e9ecef;
}

/* Header de actividad */
.activity-header {
    background: var(--white);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.activity-title h2 {
    font-size: 24px;
    font-weight: 700;
    color: var(--dark-gray);
    margin: 0;
    letter-spacing: 0.5px;
}

.activity-divider {
    height: 3px;
    background: linear-gradient(90deg, var(--primary-orange) 0%, var(--light-orange) 100%);
    border-radius: 2px;
    margin-top: 8px;
    width: 100%;
}

.activity-controls {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-top: 16px;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.form-select {
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 14px;
    background: var(--white);
    min-width: 160px;
}

.form-select:focus {
    border-color: var(--primary-orange);
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
}

.btn-refresh {
    background: var(--primary-orange);
    color: var(--white);
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-refresh:hover {
    background: var(--dark-orange);
    transform: translateY(-1px);
}

/* Contenedor de actividades */
.activity-container {
    background: var(--white);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    max-height: 600px;
    overflow-y: auto;
}

/* Items de actividad */
.activity-item {
    display: flex;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    transition: background-color 0.2s ease;
}

.activity-item:hover {
    background-color: #fef7f4;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-time {
    font-size: 14px;
    font-weight: 600;
    color: var(--medium-gray);
    min-width: 60px;
    margin-right: 20px;
    text-align: right;
}

.activity-content {
    flex: 1;
}

.activity-user {
    font-weight: 600;
    color: var(--dark-gray);
    font-size: 15px;
    margin-bottom: 4px;
}

.activity-action {
    color: var(--dark-gray);
    font-size: 14px;
    line-height: 1.4;
    margin-bottom: 8px;
}

.state-change {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 4px;
}

.state-from {
    background: #f8f9fa;
    color: var(--medium-gray);
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.state-to {
    background: var(--light-orange);
    color: var(--white);
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.state-to.completed {
    background: #28a745;
}

.state-change i {
    color: var(--medium-gray);
    font-size: 12px;
}

.activity-service {
    font-size: 13px;
    color: var(--medium-gray);
    display: flex;
    align-items: center;
    gap: 6px;
}

.activity-service i {
    color: var(--primary-orange);
}

/* Estado vacío */
.no-activity {
    text-align: center;
    padding: 60px 20px;
    color: var(--medium-gray);
}

.no-activity i {
    font-size: 48px;
    color: var(--primary-orange);
    margin-bottom: 16px;
}

.no-activity p {
    font-size: 16px;
    margin: 0;
}

/* Scrollbar personalizado */
.activity-container::-webkit-scrollbar {
    width: 6px;
}

.activity-container::-webkit-scrollbar-track {
    background: var(--light-gray);
    border-radius: 3px;
}

.activity-container::-webkit-scrollbar-thumb {
    background: var(--primary-orange);
    border-radius: 3px;
}

.activity-container::-webkit-scrollbar-thumb:hover {
    background: var(--dark-orange);
}

/* Responsive */
@media (max-width: 768px) {
    .activity-header {
        padding: 16px;
    }
    
    .activity-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-group {
        flex-direction: column;
        gap: 8px;
    }
    
    .form-select {
        min-width: auto;
    }
    
    .activity-item {
        flex-direction: column;
        padding: 16px;
    }
    
    .activity-time {
        text-align: left;
        margin-bottom: 8px;
        margin-right: 0;
    }
}
</style>
=======

>>>>>>> 7d88b39e190e242f3f4c50280724345b019eaec9

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-actualizar cada 30 segundos
    setInterval(actualizarHistorial, 30000);
});

/**
 * Aplicar filtros y recargar historial
 */
function aplicarFiltros() {
    const filtroFecha = document.getElementById('filtroFecha').value;
    const filtroUsuario = document.getElementById('filtroUsuario').value;
    
    // Construir URL con parámetros
    const params = new URLSearchParams();
    params.set('fecha', filtroFecha);
    params.set('usuario', filtroUsuario);
    
    // Recargar página con nuevos filtros
    window.location.href = '<?= base_url('historial') ?>?' + params.toString();
}

/**
 * Actualizar historial via AJAX
 */
function actualizarHistorial() {
    const loadingElement = document.getElementById('historial-loading');
    const contentElement = document.getElementById('historial-content');
    
    // Mostrar loading
    loadingElement.style.display = 'block';
    contentElement.style.opacity = '0.5';
    
    const filtroFecha = document.getElementById('filtroFecha').value;
    const filtroUsuario = document.getElementById('filtroUsuario').value;
    
    // Hacer petición AJAX
    fetch('<?= base_url('historial/obtenerHistorial') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            fecha: filtroFecha,
            usuario: filtroUsuario,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        loadingElement.style.display = 'none';
        contentElement.style.opacity = '1';
        
        if (data.success) {
            actualizarContenidoHistorial(data.historial);
        } else {
            console.error('Error al obtener historial:', data.error);
        }
    })
    .catch(error => {
        loadingElement.style.display = 'none';
        contentElement.style.opacity = '1';
        console.error('Error de conexión:', error);
    });
}

/**
 * Actualizar contenido del historial
 */
function actualizarContenidoHistorial(historial) {
    const contentElement = document.getElementById('historial-content');
    
    if (historial.length === 0) {
        contentElement.innerHTML = `
            <div class="no-activity">
                <i class="fas fa-info-circle"></i>
                <p>No se encontraron actividades para el período seleccionado</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    historial.forEach(item => {
        html += `
            <div class="activity-item">
                <div class="activity-time">${item.fecha}</div>
                <div class="activity-content">
                    <div class="activity-user">${item.usuario}</div>
                    <div class="activity-action">
                        ${generarTextoAccion(item)}
                    </div>
                    <div class="activity-service">
                        ${obtenerIconoServicio(item.categoria)} Servicio: ${item.servicio}
                    </div>
                </div>
            </div>
        `;
    });
    
    contentElement.innerHTML = html;
}

/**
 * Generar texto de acción
 */
function generarTextoAccion(item) {
    if (item.accion === 'cambiar_estado') {
        const completedClass = item.estado_nuevo === 'Completado' ? 'completed' : '';
        const checkmark = item.estado_nuevo === 'Completado' ? ' ✓' : '';
        
        return `
            movió "${item.descripcion}"
            <div class="state-change">
                <span class="state-from">${item.estado_anterior}</span>
                <i class="fas fa-arrow-right"></i>
                <span class="state-to ${completedClass}">${item.estado_nuevo}${checkmark}</span>
            </div>
        `;
    } else if (item.accion === 'crear') {
        return `creó nuevo equipo "${item.descripcion}"`;
    } else if (item.accion === 'reasignar') {
        return `reasignó "${item.descripcion}"`;
    }
    return item.accion;
}

/**
 * Obtener icono según categoría de servicio
 */
function obtenerIconoServicio(categoria) {
    const iconos = {
        'Audio y Sonido': '<i class="fas fa-volume-up"></i>',
        'Fotografía y Video': '<i class="fas fa-camera"></i>',
        'Iluminación': '<i class="fas fa-lightbulb"></i>',
        'Decoración': '<i class="fas fa-palette"></i>',
        'Catering': '<i class="fas fa-utensils"></i>'
    };
    return iconos[categoria] || '<i class="fas fa-cog"></i>';
}
</script>

<?php
/**
 * Obtener icono según categoría de servicio
 */
function obtenerIconoCategoria($categoria) {
    $iconos = [
        'Audio y Sonido' => '<i class="fas fa-volume-up"></i>',
        'Fotografía y Video' => '<i class="fas fa-camera"></i>',
        'Iluminación' => '<i class="fas fa-lightbulb"></i>',
        'Decoración' => '<i class="fas fa-palette"></i>',
        'Catering' => '<i class="fas fa-utensils"></i>'
    ];
    return $iconos[$categoria] ?? '<i class="fas fa-cog"></i>';
}
?>

<?= $footer ?>
