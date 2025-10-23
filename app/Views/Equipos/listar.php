<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/Equipos-listar.css') ?>">

<!-- Cargar helper de estados -->
<?php helper('estado'); ?>

<div class="container-fluid mt-4">
    <!-- Encabezado con estadísticas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="text-primary mb-0">
                    <i class="fas fa-users-cog me-2"></i><?= $titulo ?>
                </h2>
                
                <!-- Estadísticas rápidas -->
                <?php if (isset($estadisticas)): ?>
                <div class="d-flex gap-3">
                    <div class="badge bg-warning fs-6 px-3 py-2">
                        <i class="fas fa-clock me-1"></i>
                        Pendientes: <?= $estadisticas['Pendiente'] ?? 0 ?>
                    </div>
                    <div class="badge bg-info fs-6 px-3 py-2">
                        <i class="fas fa-spinner me-1"></i>
                        En Proceso: <?= $estadisticas['En Proceso'] ?? 0 ?>
                    </div>
                    <div class="badge bg-success fs-6 px-3 py-2">
                        <i class="fas fa-check-circle me-1"></i>
                        Completados: <?= $estadisticas['Completado'] ?? 0 ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tarjeta de Información del Servicio -->
    <?php if (isset($servicio)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Servicio</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-primary"><i class="fas fa-concierge-bell me-2"></i>Servicio:</strong>
                                <p class="mb-0"><?= is_array($servicio) ? $servicio['servicio'] : $servicio->servicio ?></p>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary"><i class="fas fa-user me-2"></i>Cliente:</strong>
                                <p class="mb-0"><?php 
                                    if (is_array($servicio)) {
                                        if (!empty($servicio['razonsocial'])) {
                                            echo $servicio['razonsocial'];
                                        } elseif (isset($servicio['nombres']) && isset($servicio['apellidos'])) {
                                            echo $servicio['nombres'] . ' ' . $servicio['apellidos'];
                                        } elseif (isset($servicio['cliente_nombre'])) {
                                            echo $servicio['cliente_nombre'];
                                        } else {
                                            echo 'Cliente no especificado';
                                        }
                                    } else {
                                        if (!empty($servicio->razonsocial)) {
                                            echo $servicio->razonsocial;
                                        } elseif (isset($servicio->nombres) && isset($servicio->apellidos)) {
                                            echo $servicio->nombres . ' ' . $servicio->apellidos;
                                        } elseif (isset($servicio->cliente_nombre)) {
                                            echo $servicio->cliente_nombre;
                                        } else {
                                            echo 'Cliente no especificado';
                                        }
                                    }
                                ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-primary"><i class="fas fa-calendar-day me-2"></i>Fecha del Evento:</strong>
                                <p class="mb-0"><?php 
                                    if (is_array($servicio)) {
                                        $fecha = isset($servicio['fechaevento']) ? $servicio['fechaevento'] : (isset($servicio['fechahoraservicio']) ? $servicio['fechahoraservicio'] : null);
                                    } else {
                                        $fecha = isset($servicio->fechaevento) ? $servicio->fechaevento : (isset($servicio->fechahoraservicio) ? $servicio->fechahoraservicio : null);
                                    }
                                    echo $fecha ? date('d/m/Y', strtotime($fecha)) : 'Fecha no especificada';
                                ?></p>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary"><i class="fas fa-star me-2"></i>Tipo de Evento:</strong>
                                <p class="mb-0"><?php 
                                    if (is_array($servicio)) {
                                        echo isset($servicio['tipo_evento']) ? $servicio['tipo_evento'] : 'Tipo no especificado';
                                    } else {
                                        echo isset($servicio->tipo_evento) ? $servicio->tipo_evento : 'Tipo no especificado';
                                    }
                                ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= base_url('equipos/asignar/'.(is_array($servicio) ? $servicio['idserviciocontratado'] : $servicio->idserviciocontratado)) ?>" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Asignar Nuevo Equipo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tarjeta de Información del Usuario -->
    <?php if (isset($usuario)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-user-tie me-2"></i>Información del Usuario</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <strong class="text-info"><i class="fas fa-id-card me-2"></i>Nombre:</strong>
                                <p class="mb-0"><?= $usuario->nombres . ' ' . $usuario->apellidos ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <strong class="text-info"><i class="fas fa-user me-2"></i>Usuario:</strong>
                                <p class="mb-0"><?= $usuario->nombreusuario ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <strong class="text-info"><i class="fas fa-briefcase me-2"></i>Cargo:</strong>
                                <p class="mb-0"><?= $usuario->cargo ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Acceso al Historial (Solo Administradores) -->
    <?php if (session()->get('tipo_usuario') === 'admin'): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Panel de Administrador:</strong> Accede al historial completo del sistema para análisis detallado
                </div>
                <a href="<?= base_url('historial') ?>" class="btn btn-primary">
                    <i class="fas fa-history me-1"></i>Ver Historial Completo
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tablero Kanban Refactorizado -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 text-dark">
                    <i class="fas fa-columns me-2 text-primary"></i>Tablero Kanban
                </h4>
                <?php if (isset($servicio)): ?>
                <a href="<?= base_url('equipos/asignar/'.$servicio['idserviciocontratado']) ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Asignar Técnico
                </a>
                <?php endif; ?>
            </div>
            
            <?php 
            // Usar datos agrupados del modelo o crear estructura por defecto
            $equiposKanban = $equiposKanban ?? [
                'Pendiente' => [],
                'En Proceso' => [],
                'Completado' => []
            ];
            ?>
            
            <?php if (!empty(array_filter($equiposKanban))): ?>
            <!-- Tablero Kanban Simplificado -->
            <div class="kanban-board">
                <div class="row g-4">
                    <?php 
                    // Configuración de columnas KISS
                    $columnas = [
                        'Pendiente' => ['color' => 'warning', 'icono' => 'fas fa-clock'],
                        'En Proceso' => ['color' => 'info', 'icono' => 'fas fa-spinner'],
                        'Completado' => ['color' => 'success', 'icono' => 'fas fa-check-circle']
                    ];
                    ?>
                    
                    <?php foreach ($columnas as $estado => $config): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="kanban-column">
                            <!-- Header de columna -->
                            <div class="kanban-column-header bg-<?= $config['color'] ?>">
                                <h5 class="text-center text-white mb-0">
                                    <i class="<?= $config['icono'] ?> me-2"></i><?= $estado ?>
                                    <span class="badge bg-light text-dark ms-2">
                                        <?= count($equiposKanban[$estado]) ?>
                                    </span>
                                </h5>
                            </div>
                            
                            <!-- Cuerpo de columna -->
                            <div class="kanban-column-body" id="<?= strtolower(str_replace(' ', '-', $estado)) ?>" 
                                 data-estado="<?= $estado ?>">
                                
                                <?php if (!empty($equiposKanban[$estado])): ?>
                                    <?php if (isset($agrupar_por_cliente) && $agrupar_por_cliente): ?>
                                        <!-- Modo agrupado por cliente -->
                                        <?php foreach ($equiposKanban[$estado] as $clienteData): ?>
                                            <?= renderClienteCard($clienteData, $estado) ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Modo individual (sin agrupar) -->
                                        <?php foreach ($equiposKanban[$estado] as $equipo): ?>
                                            <?= renderEquipoCard($equipo, $estado) ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="kanban-empty-state">
                                        <i class="fas fa-inbox fa-2x mb-2 text-muted"></i>
                                        <p class="text-muted mb-0">No hay equipos en <?= strtolower($estado) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <!-- Estado vacío -->
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay equipos asignados</h5>
                <p class="text-muted">Comienza asignando técnicos a los servicios contratados.</p>
                <?php if (isset($servicio)): ?>
                <a href="<?= base_url('equipos/asignar/'.$servicio['idserviciocontratado']) ?>" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-2"></i>Asignar Primer Técnico
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Función PHP para renderizar tarjetas (componente reutilizable) -->
<?php
function renderEquipoCard(array $equipo, string $estado): string {
    // Mapeo local de colores e iconos (KISS: simple y directo)
    $colores = [
        'Pendiente' => 'warning',
        'En Proceso' => 'info', 
        'Completado' => 'success'
    ];
    
    $iconos = [
        'Pendiente' => 'fas fa-clock',
        'En Proceso' => 'fas fa-spinner',
        'Completado' => 'fas fa-check-circle'
    ];
    
    $colorEstado = $colores[$estado] ?? 'secondary';
    $iconoEstado = $iconos[$estado] ?? 'fas fa-question-circle';
    
    // Truncar descripción a 2 líneas (aproximadamente 80 caracteres)
    $descripcionCorta = strlen($equipo['descripcion']) > 80 
        ? substr($equipo['descripcion'], 0, 80) . '...' 
        : $equipo['descripcion'];
    
    // Nombre corto del técnico
    $nombreCorto = !empty($equipo['nombre_completo']) 
        ? explode(' ', $equipo['nombre_completo'])[0] . ' ' . substr(explode(' ', $equipo['nombre_completo'])[1] ?? '', 0, 1) . '.'
        : $equipo['nombreusuario'];
    
    ob_start();
    ?>
    <div class="kanban-card" data-id="<?= $equipo['idequipo'] ?>" data-status="<?= $equipo['estadoservicio'] ?>" draggable="true">
        <!-- Header de tarjeta -->
        <div class="card-header-kanban">
            <span class="badge bg-<?= $colorEstado ?> text-white">
                <i class="<?= $iconoEstado ?> me-1"></i><?= $estado ?>
            </span>
            <span class="kanban-card-id text-muted">#<?= $equipo['idequipo'] ?></span>
        </div>
        
        <!-- Cuerpo de tarjeta -->
        <div class="card-body-kanban">
            <div class="mb-2">
                <strong class="text-primary d-block"><?= esc($equipo['servicio']) ?></strong>
                <small class="text-muted">
                    <i class="fas fa-user me-1"></i><?= esc($nombreCorto) ?>
                    <span class="ms-2">
                        <i class="fas fa-briefcase me-1"></i><?= esc($equipo['cargo']) ?>
                    </span>
                </small>
            </div>
            
            <div class="description-container mb-3">
                <p class="description-text mb-0" title="<?= esc($equipo['descripcion']) ?>">
                    <?= esc($descripcionCorta) ?>
                </p>
                <?php if (strlen($equipo['descripcion']) > 80): ?>
                <button class="btn btn-link btn-sm p-0 text-decoration-none" onclick="toggleDescription(this)">
                    <small>Ver más</small>
                </button>
                <?php endif; ?>
            </div>
            
            <div class="text-muted small mb-2">
                <i class="fas fa-calendar me-1"></i>
                <?= date('d/m/Y H:i', strtotime($equipo['fechahoraservicio'])) ?>
            </div>
        </div>
        
        <!-- Acciones de tarjeta -->
        <div class="card-actions">
            <a href="<?= base_url('equipos/editar/'.$equipo['idequipo']) ?>" 
               class="btn btn-sm btn-outline-primary" 
               title="Editar asignación" 
               data-bs-toggle="tooltip">
                <i class="fas fa-edit"></i>
            </a>
            <a href="<?= base_url('servicios/'.$equipo['idserviciocontratado']) ?>" 
               class="btn btn-sm btn-outline-info" 
               title="Ver servicio completo" 
               data-bs-toggle="tooltip">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Función para renderizar tarjeta de cliente con múltiples servicios
 */
function renderClienteCard(array $clienteData, string $estado): string {
    // Mapeo local de colores e iconos
    $colores = [
        'Pendiente' => 'warning',
        'En Proceso' => 'info', 
        'Completado' => 'success'
    ];
    
    $iconos = [
        'Pendiente' => 'fas fa-clock',
        'En Proceso' => 'fas fa-spinner',
        'Completado' => 'fas fa-check-circle'
    ];
    
    $colorEstado = $colores[$estado] ?? 'secondary';
    $iconoEstado = $iconos[$estado] ?? 'fas fa-question-circle';
    
    ob_start();
    ?>
    <div class="kanban-card kanban-card-cliente" data-cliente-id="<?= $clienteData['idcliente'] ?>" data-status="<?= $estado ?>">
        <!-- Header de tarjeta de cliente -->
        <div class="card-header-kanban" style="background: linear-gradient(135deg, #FF9900, #F57C00); color: white; padding: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="font-size: 1.1rem; display: block;">
                        <i class="fas fa-user-circle me-2"></i><?= esc($clienteData['cliente_nombre']) ?>
                    </strong>
                    <small style="opacity: 0.9;">
                        <i class="fas fa-phone me-1"></i><?= esc($clienteData['cliente_telefono'] ?? 'Sin teléfono') ?>
                    </small>
                </div>
                <span class="badge bg-light text-dark" style="font-size: 0.85rem;">
                    <?= $clienteData['total_equipos'] ?> Servicio<?= $clienteData['total_equipos'] > 1 ? 's' : '' ?>
                </span>
            </div>
        </div>
        
        <!-- Cuerpo con lista de servicios -->
        <div class="card-body-kanban" style="padding: 10px;">
            <?php foreach ($clienteData['equipos'] as $index => $equipo): ?>
                <?php
                // Nombre corto del técnico
                $nombreCorto = !empty($equipo['nombre_completo']) 
                    ? explode(' ', $equipo['nombre_completo'])[0] . ' ' . substr(explode(' ', $equipo['nombre_completo'])[1] ?? '', 0, 1) . '.'
                    : $equipo['nombreusuario'];
                ?>
                <div class="servicio-item-kanban" style="
                    margin-bottom: 8px; 
                    padding: 10px; 
                    background: #f8f9fa; 
                    border-radius: 8px; 
                    border-left: 4px solid 
                    <?php 
                        if ($estado == 'Completado') echo '#27AE60';
                        elseif ($estado == 'En Proceso') echo '#E67E22';
                        else echo '#FF9900';
                    ?>;
                    transition: all 0.2s ease;
                " 
                onmouseover="this.style.background='#e9ecef'; this.style.transform='translateX(3px)';"
                onmouseout="this.style.background='#f8f9fa'; this.style.transform='translateX(0)';">
                    
                    <!-- Servicio y técnico -->
                    <div style="margin-bottom: 6px;">
                        <strong style="color: #2c3e50; font-size: 0.95rem; display: block;">
                            <i class="fas fa-briefcase me-1" style="color: #FF9900;"></i>
                            <?= esc($equipo['servicio']) ?>
                        </strong>
                        <small style="color: #7f8c8d; display: block; margin-top: 3px;">
                            <i class="fas fa-user me-1"></i><?= esc($nombreCorto) ?>
                            <span style="margin-left: 8px;">
                                <i class="fas fa-id-badge me-1"></i><?= esc($equipo['cargo']) ?>
                            </span>
                        </small>
                    </div>
                    
                    <!-- Fecha y ubicación -->
                    <div style="font-size: 0.85rem; color: #6c757d;">
                        <div style="margin-bottom: 3px;">
                            <i class="fas fa-calendar me-1"></i>
                            <?= date('d/m/Y H:i', strtotime($equipo['fechahoraservicio'])) ?>
                        </div>
                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?= esc($equipo['direccion']) ?>
                        </div>
                    </div>
                    
                    <!-- Acciones rápidas -->
                    <div style="margin-top: 8px; display: flex; gap: 6px;">
                        <a href="<?= base_url('equipos/editar/'.$equipo['idequipo']) ?>" 
                           class="btn btn-sm btn-outline-primary" 
                           style="font-size: 0.75rem; padding: 3px 8px;"
                           title="Editar asignación">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= base_url('servicios/'.$equipo['idserviciocontratado']) ?>" 
                           class="btn btn-sm btn-outline-info" 
                           style="font-size: 0.75rem; padding: 3px 8px;"
                           title="Ver servicio">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Footer con acción para ver cliente -->
        <div style="padding: 10px; border-top: 1px solid #e9ecef; background: #f8f9fa;">
            <a href="<?= base_url('clientes/ver/' . $clienteData['idcliente']) ?>" 
               class="btn btn-sm btn-outline-primary w-100" 
               style="font-size: 0.85rem;">
                <i class="fas fa-user-circle me-1"></i>Ver Detalles del Cliente
            </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>

<!-- CSS Personalizado para Kanban -->


<!-- JavaScript Refactorizado para Kanban -->
<script>
// Variables globales
let draggedCard = null;

document.addEventListener('DOMContentLoaded', function() {
    // Mostrar notificaciones flash con SweetAlert
    showFlashMessages();
    
    // Inicializar funcionalidades
    initializeTooltips();
    initializeDragAndDrop();
    
    console.log('Kanban Board inicializado correctamente');
});

/**
 * Mostrar mensajes flash con SweetAlert
 * KISS: función simple y reutilizable
 */
function showFlashMessages() {
    <?php if (session()->getFlashdata('success')): ?>
        showNotification('<?= addslashes(session()->getFlashdata('success')) ?>', 'success');
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        showNotification('<?= addslashes(session()->getFlashdata('error')) ?>', 'error');
    <?php endif; ?>
}

/**
 * Mostrar notificación con SweetAlert
 * KISS: configuración consistente
 */
function showNotification(message, type) {
    const config = {
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        title: message
    };
    
    switch(type) {
        case 'success':
            config.icon = 'success';
            config.background = '#d4edda';
            break;
        case 'error':
            config.icon = 'error';
            config.background = '#f8d7da';
            break;
        case 'info':
            config.icon = 'info';
            config.background = '#d1ecf1';
            break;
    }
    
    Swal.fire(config);
}

/**
 * Inicializar tooltips de Bootstrap
 */
function initializeTooltips() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(el => new bootstrap.Tooltip(el));
}

/**
 * Inicializar Drag and Drop
 * KISS: eventos simples y claros
 */
function initializeDragAndDrop() {
    const cards = document.querySelectorAll('.kanban-card');
    const columns = document.querySelectorAll('.kanban-column-body');
    
    // Configurar tarjetas como draggables
    cards.forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
    });
    
    // Configurar columnas como drop zones
    columns.forEach(column => {
        column.addEventListener('dragover', handleDragOver);
        column.addEventListener('drop', handleDrop);
        column.addEventListener('dragenter', handleDragEnter);
        column.addEventListener('dragleave', handleDragLeave);
    });
}

/**
 * Manejar inicio de arrastre
 */
function handleDragStart(e) {
    draggedCard = this;
    this.classList.add('dragging');
    
    // Guardar datos para el drop
    e.dataTransfer.setData('text/plain', JSON.stringify({
        id: this.dataset.id,
        currentStatus: this.dataset.status
    }));
}

/**
 * Manejar fin de arrastre
 */
function handleDragEnd(e) {
    this.classList.remove('dragging');
    
    // Limpiar estados visuales
    document.querySelectorAll('.kanban-column-body').forEach(col => {
        col.classList.remove('drag-over');
    });
    
    draggedCard = null;
}

/**
 * Manejar dragover
 */
function handleDragOver(e) {
    e.preventDefault();
    this.classList.add('drag-over');
}

/**
 * Manejar dragenter
 */
function handleDragEnter(e) {
    e.preventDefault();
}

/**
 * Manejar dragleave
 */
function handleDragLeave(e) {
    if (!this.contains(e.relatedTarget)) {
        this.classList.remove('drag-over');
    }
}

/**
 * Manejar drop - Lógica principal del Kanban
 */
async function handleDrop(e) {
    e.preventDefault();
    this.classList.remove('drag-over');
    
    console.log('handleDrop - draggedCard:', draggedCard);
    
    if (!draggedCard) {
        console.error('draggedCard es null en handleDrop');
        return;
    }
    
    // Guardar referencias antes de operaciones asíncronas
    const currentCard = draggedCard;
    const targetColumn = this;
    
    console.log('Referencias guardadas - currentCard:', currentCard, 'targetColumn:', targetColumn);
    
    const cardData = JSON.parse(e.dataTransfer.getData('text/plain'));
    const newStatus = this.dataset.estado;
    const currentStatus = cardData.currentStatus;
    
    // Si no hay cambio de estado, no hacer nada
    if (currentStatus === newStatus) return;
    
    // Validar transición usando helper
    const validation = validarTransicionEstado(currentStatus, newStatus);
    
    if (!validation.valido) {
        Swal.fire({
            title: 'Movimiento no permitido',
            text: validation.mensaje,
            icon: 'warning',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#6c757d'
        });
        return;
    }
    
    // Confirmar cambio de estado
    const confirmed = await confirmStateChange(cardData.id, currentStatus, newStatus);
    if (!confirmed) return;
    
    // Actualizar estado en servidor
    const success = await updateCardStatus(cardData.id, newStatus);
    
    if (success) {
        // Mover tarjeta visualmente usando las referencias guardadas
        moveCardToColumn(currentCard, targetColumn, newStatus);
        showNotification(`Estado cambiado a "${newStatus}" correctamente`, 'success');
    } else {
        showNotification('Error al actualizar el estado', 'error');
    }
}

/**
 * Confirmar cambio de estado con SweetAlert
 */
async function confirmStateChange(cardId, currentStatus, newStatus) {
    const result = await Swal.fire({
        title: 'Cambiar Estado',
        text: `¿Cambiar de "${currentStatus}" a "${newStatus}"?`,
        icon: getStatusIcon(newStatus),
        showCancelButton: true,
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d'
    });
    
    return result.isConfirmed;
}

/**
 * Actualizar estado en servidor via AJAX
 */
async function updateCardStatus(cardId, newStatus) {
    try {
        // Mostrar loading
        Swal.fire({
            title: 'Actualizando...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch('<?= base_url("equipos/actualizar-estado") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id: cardId,
                estado: newStatus
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        Swal.close();
        
        if (!data.success && data.message) {
            showNotification(data.message, 'error');
        }
        
        return data.success;
        
    } catch (error) {
        Swal.close();
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
        return false;
    }
}

/**
 * Mover tarjeta a nueva columna visualmente
 */
function moveCardToColumn(card, newColumn, newStatus) {
    // Validar parámetros
    if (!card || !newColumn || !newStatus) {
        console.error('moveCardToColumn: Parámetros inválidos', {card, newColumn, newStatus});
        return;
    }
    
    console.log('Moviendo tarjeta:', card.dataset.id, 'a columna:', newColumn.dataset.estado);
    
    // Actualizar datos de la tarjeta
    card.dataset.status = newStatus;
    
    // Actualizar badge de estado
    const badge = card.querySelector('.badge');
    if (badge) {
        badge.className = `badge bg-${getStatusColor(newStatus)} text-white`;
        badge.innerHTML = `<i class="${getStatusFontAwesome(newStatus)} me-1"></i>${newStatus}`;
    }
    
    // Mover al DOM
    newColumn.appendChild(card);
    
    // Actualizar contadores
    updateColumnCounters();
    
    // Verificar estados vacíos
    updateEmptyStates();
}

/**
 * Actualizar contadores de columnas
 */
function updateColumnCounters() {
    document.querySelectorAll('.kanban-column').forEach(column => {
        const header = column.querySelector('.kanban-column-header h5');
        const body = column.querySelector('.kanban-column-body');
        const cards = body.querySelectorAll('.kanban-card').length;
        const counter = header.querySelector('.badge');
        
        if (counter) {
            counter.textContent = cards;
        }
    });
}

/**
 * Actualizar estados vacíos
 */
function updateEmptyStates() {
    document.querySelectorAll('.kanban-column-body').forEach(column => {
        const cards = column.querySelectorAll('.kanban-card');
        const emptyState = column.querySelector('.kanban-empty-state');
        
        if (cards.length === 0 && !emptyState) {
            const estado = column.dataset.estado;
            column.innerHTML = `
                <div class="kanban-empty-state">
                    <i class="fas fa-inbox fa-2x mb-2 text-muted"></i>
                    <p class="text-muted mb-0">No hay equipos en ${estado.toLowerCase()}</p>
                </div>
            `;
        } else if (cards.length > 0 && emptyState) {
            emptyState.remove();
        }
    });
}

/**
 * Funciones helper para estados
 */
function getStatusColor(status) {
    const colors = {
        'Pendiente': 'warning',
        'Programado': 'warning', 
        'En Proceso': 'info',
        'Completado': 'success'
    };
    return colors[status] || 'secondary';
}

function getStatusIcon(status) {
    const icons = {
        'Pendiente': 'warning',
        'Programado': 'warning',
        'En Proceso': 'info', 
        'Completado': 'success'
    };
    return icons[status] || 'question';
}

function getStatusFontAwesome(status) {
    const icons = {
        'Pendiente': 'fas fa-clock',
        'Programado': 'fas fa-clock',
        'En Proceso': 'fas fa-spinner', 
        'Completado': 'fas fa-check-circle'
    };
    return icons[status] || 'fas fa-question-circle';
}

/**
 * Validar transición de estado (lado cliente)
 * Usando la misma lógica del helper PHP
 */
function validarTransicionEstado(estadoActual, nuevoEstado) {
    // Regla 1: Completado no puede regresar
    if (estadoActual === 'Completado') {
        return {
            valido: false,
            mensaje: 'Este servicio ya está completo'
        };
    }
    
    // Regla 2: No saltar directamente a Completado
    if ((estadoActual === 'Pendiente' || estadoActual === 'Programado') && nuevoEstado === 'Completado') {
        return {
            valido: false,
            mensaje: 'Este servicio aún no tiene proceso'
        };
    }
    
    // Regla 3: En Proceso no puede regresar a Pendiente
    if (estadoActual === 'En Proceso' && nuevoEstado === 'Pendiente') {
        return {
            valido: false,
            mensaje: 'Este servicio está en proceso'
        };
    }
    
    return { valido: true, mensaje: 'Transición válida' };
}

/**
 * Toggle descripción completa
 */
function toggleDescription(button) {
    const container = button.closest('.description-container');
    const text = container.querySelector('.description-text');
    const isExpanded = text.style.webkitLineClamp === 'unset';
    
    if (isExpanded) {
        text.style.webkitLineClamp = '2';
        button.innerHTML = '<small>Ver más</small>';
    } else {
        text.style.webkitLineClamp = 'unset';
        button.innerHTML = '<small>Ver menos</small>';
    }
}

// Funciones del historial removidas - ahora están en vista separada /historial
</script>

<!-- Estilos CSS para el historial -->


<?= $footer ?>