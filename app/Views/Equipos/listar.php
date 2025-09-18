<?= $header ?>
<div class="container mt-4">
    <!-- Encabezado y Alertas -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-primary"><i class="fas fa-users-cog me-2"></i><?= $titulo ?></h2>
            
            <!-- Las alertas ahora se muestran con SweetAlert -->
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
                                <p class="mb-0"><?= $servicio->servicio ?></p>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary"><i class="fas fa-user me-2"></i>Cliente:</strong>
                                <p class="mb-0"><?= !empty($servicio->razonsocial) ? $servicio->razonsocial : $servicio->nombres . ' ' . $servicio->apellidos ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-primary"><i class="fas fa-calendar-day me-2"></i>Fecha del Evento:</strong>
                                <p class="mb-0"><?= date('d/m/Y', strtotime($servicio->fechaevento)) ?></p>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary"><i class="fas fa-star me-2"></i>Tipo de Evento:</strong>
                                <p class="mb-0"><?= $servicio->tipo_evento ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= base_url('equipos/asignar/'.$servicio->idserviciocontratado) ?>" class="btn btn-primary">
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

    <!-- Tablero Kanban de Equipos -->
    <div class="row">
        <div class="col-12">
            <div class="kanban-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-dark"><i class="fas fa-columns me-2 text-orange"></i>Tablero de Equipos Asignados</h4>
                    <a href="<?= base_url('equipos') ?>" class="btn btn-outline-orange">
                        <i class="fas fa-eye me-2"></i>Ver Todos los Equipos
                    </a>
                </div>
            </div>
            
            <?php if (!empty($equipos)): ?>
            <!-- Tablero Kanban -->
            <div class="kanban-board">
                <div class="row g-3 kanban-columns-container">
                    <!-- Columna Pendiente -->
                    <div class="col-lg-4 col-md-6 col-sm-12 kanban-column-wrapper">
                        <div class="kanban-column">
                            <div class="kanban-column-header bg-warning">
                                <h5 class="text-center text-white mb-0">
                                    <i class="fas fa-clock me-2"></i>Pendiente
                                </h5>
                            </div>
                            <div class="kanban-column-body" id="pendiente">
                                <?php 
                                $pendientes = array_filter($equipos, function($equipo) {
                                    return $equipo->estadoservicio === 'Pendiente' || $equipo->estadoservicio === 'Programado';
                                });
                                ?>
                                <?php foreach ($pendientes as $equipo): ?>
                                <div class="kanban-card" data-id="<?= $equipo->idequipo ?>" data-status="<?= $equipo->estadoservicio ?>">
                                    <div class="card-header-kanban">
                                        <span class="badge badge-pendiente">
                                            <i class="fas fa-clock me-1"></i><?= $equipo->estadoservicio ?>
                                        </span>
                                        <span class="kanban-card-id">#<?= $equipo->idequipo ?></span>
                                    </div>
                                    <div class="card-body-kanban">
                                        <div class="info-field">
                                            <strong class="text-orange">Servicio:</strong>
                                            <span><?= $equipo->servicio ?></span>
                                        </div>
                                        <div class="info-field">
                                            <strong class="text-orange">Usuario:</strong>
                                            <span>
                                                <?php 
                                                $nombreCompleto = isset($equipo->nombres) ? $equipo->nombres . ' ' . $equipo->apellidos : $equipo->nombreusuario;
                                                $nombres = explode(' ', $nombreCompleto);
                                                echo count($nombres) > 1 ? $nombres[0] . ' ' . substr($nombres[1], 0, 1) . '.' : $nombres[0];
                                                ?>
                                            </span>
                                        </div>
                                        <?php if (isset($servicio)): ?>
                                        <div class="info-field">
                                            <strong class="text-orange">Cliente:</strong>
                                            <span>
                                                <?php 
                                                $cliente = !empty($servicio->razonsocial) ? $servicio->razonsocial : $servicio->nombres . ' ' . $servicio->apellidos;
                                                echo strlen($cliente) > 20 ? substr($cliente, 0, 20) . '...' : $cliente;
                                                ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="info-field">
                                            <strong class="text-orange">Descripción:</strong>
                                            <span class="description-text">
                                                <?= strlen($equipo->descripcion) > 50 ? substr($equipo->descripcion, 0, 50) . '...' : $equipo->descripcion ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-actions">
                                        <a href="<?= base_url('servicios/'.$equipo->idserviciocontratado) ?>" class="btn btn-sm btn-outline-orange" title="Ver Detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('equipos/editar/'.$equipo->idequipo) ?>" class="btn btn-sm btn-orange" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php if (empty($pendientes)): ?>
                                <div class="kanban-empty-state">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">No hay equipos pendientes</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Columna En Proceso -->
                    <div class="col-lg-4 col-md-6 col-sm-12 kanban-column-wrapper">
                        <div class="kanban-column">
                            <div class="kanban-column-header bg-info">
                                <h5 class="text-center text-white mb-0">
                                    <i class="fas fa-spinner me-2"></i>En Proceso
                                </h5>
                            </div>
                            <div class="kanban-column-body" id="en-proceso">
                                <?php 
                                $enProceso = array_filter($equipos, function($equipo) {
                                    return $equipo->estadoservicio === 'En Proceso';
                                });
                                ?>
                                <?php foreach ($enProceso as $equipo): ?>
                                <div class="kanban-card" data-id="<?= $equipo->idequipo ?>" data-status="En Proceso">
                                    <div class="card-header-kanban">
                                        <span class="badge badge-en-proceso">
                                            <i class="fas fa-spinner me-1"></i>En Proceso
                                        </span>
                                        <span class="kanban-card-id">#<?= $equipo->idequipo ?></span>
                                    </div>
                                    <div class="card-body-kanban">
                                        <div class="info-field">
                                            <strong class="text-orange">Servicio:</strong>
                                            <span><?= $equipo->servicio ?></span>
                                        </div>
                                        <div class="info-field">
                                            <strong class="text-orange">Usuario:</strong>
                                            <span>
                                                <?php 
                                                $nombreCompleto = isset($equipo->nombres) ? $equipo->nombres . ' ' . $equipo->apellidos : $equipo->nombreusuario;
                                                $nombres = explode(' ', $nombreCompleto);
                                                echo count($nombres) > 1 ? $nombres[0] . ' ' . substr($nombres[1], 0, 1) . '.' : $nombres[0];
                                                ?>
                                            </span>
                                        </div>
                                        <?php if (isset($servicio)): ?>
                                        <div class="info-field">
                                            <strong class="text-orange">Cliente:</strong>
                                            <span>
                                                <?php 
                                                $cliente = !empty($servicio->razonsocial) ? $servicio->razonsocial : $servicio->nombres . ' ' . $servicio->apellidos;
                                                echo strlen($cliente) > 20 ? substr($cliente, 0, 20) . '...' : $cliente;
                                                ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="info-field">
                                            <strong class="text-orange">Descripción:</strong>
                                            <span class="description-text">
                                                <?= strlen($equipo->descripcion) > 50 ? substr($equipo->descripcion, 0, 50) . '...' : $equipo->descripcion ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-actions">
                                        <a href="<?= base_url('servicios/'.$equipo->idserviciocontratado) ?>" class="btn btn-sm btn-outline-orange" title="Ver Detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('equipos/editar/'.$equipo->idequipo) ?>" class="btn btn-sm btn-orange" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php if (empty($enProceso)): ?>
                                <div class="kanban-empty-state">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">No hay equipos en proceso</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Completo -->
                    <div class="col-lg-4 col-md-6 col-sm-12 kanban-column-wrapper">
                        <div class="kanban-column">
                            <div class="kanban-column-header bg-success">
                                <h5 class="text-center text-white mb-0">
                                    <i class="fas fa-check-circle me-2"></i>Completo
                                </h5>
                            </div>
                            <div class="kanban-column-body" id="completo">
                                <?php 
                                $completados = array_filter($equipos, function($equipo) {
                                    return $equipo->estadoservicio === 'Completado';
                                });
                                ?>
                                <?php foreach ($completados as $equipo): ?>
                                <div class="kanban-card" data-id="<?= $equipo->idequipo ?>" data-status="Completado">
                                    <div class="card-header-kanban">
                                        <span class="badge badge-completo">
                                            <i class="fas fa-check-circle me-1"></i>Completado
                                        </span>
                                        <span class="kanban-card-id">#<?= $equipo->idequipo ?></span>
                                    </div>
                                    <div class="card-body-kanban">
                                        <div class="info-field">
                                            <strong class="text-orange">Servicio:</strong>
                                            <span><?= $equipo->servicio ?></span>
                                        </div>
                                        <div class="info-field">
                                            <strong class="text-orange">Usuario:</strong>
                                            <span>
                                                <?php 
                                                $nombreCompleto = isset($equipo->nombres) ? $equipo->nombres . ' ' . $equipo->apellidos : $equipo->nombreusuario;
                                                $nombres = explode(' ', $nombreCompleto);
                                                echo count($nombres) > 1 ? $nombres[0] . ' ' . substr($nombres[1], 0, 1) . '.' : $nombres[0];
                                                ?>
                                            </span>
                                        </div>
                                        <?php if (isset($servicio)): ?>
                                        <div class="info-field">
                                            <strong class="text-orange">Cliente:</strong>
                                            <span>
                                                <?php 
                                                $cliente = !empty($servicio->razonsocial) ? $servicio->razonsocial : $servicio->nombres . ' ' . $servicio->apellidos;
                                                echo strlen($cliente) > 20 ? substr($cliente, 0, 20) . '...' : $cliente;
                                                ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="info-field">
                                            <strong class="text-orange">Descripción:</strong>
                                            <span class="description-text">
                                                <?= strlen($equipo->descripcion) > 50 ? substr($equipo->descripcion, 0, 50) . '...' : $equipo->descripcion ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-actions">
                                        <a href="<?= base_url('servicios/'.$equipo->idserviciocontratado) ?>" class="btn btn-sm btn-outline-orange" title="Ver Detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('equipos/editar/'.$equipo->idequipo) ?>" class="btn btn-sm btn-orange" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php if (empty($completados)): ?>
                                <div class="kanban-empty-state">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">No hay equipos completados</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay equipos asignados</h5>
                <p class="text-muted">No se encontraron equipos asignados para esta consulta.</p>
                <?php if (isset($servicio)): ?>
                <a href="<?= base_url('equipos/asignar/'.$servicio->idserviciocontratado) ?>" class="btn btn-orange mt-2">
                    <i class="fas fa-plus-circle me-2"></i>Asignar Primer Equipo
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Scripts del Tablero Kanban -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar notificaciones de flash messages con SweetAlert
    <?php if (session()->getFlashdata('success')): ?>
        showNotification('<?= addslashes(session()->getFlashdata('success')) ?>', 'success');
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        showNotification('<?= addslashes(session()->getFlashdata('error')) ?>', 'error');
    <?php endif; ?>

    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Funcionalidad Drag and Drop
    initializeDragAndDrop();
    
    // Ajustar altura de columnas
    adjustColumnHeights();
    
    // Ajustar en redimensionamiento
    window.addEventListener('resize', adjustColumnHeights);
});

function adjustColumnHeights() {
    const columns = document.querySelectorAll('.kanban-column-body');
    let maxHeight = 0;
    
    // Reiniciar alturas
    columns.forEach(col => {
        col.style.minHeight = 'auto';
    });
    
    // Encontrar la altura máxima
    columns.forEach(col => {
        if (col.scrollHeight > maxHeight) {
            maxHeight = col.scrollHeight;
        }
    });
    
    // Aplicar altura máxima a todas las columnas
    columns.forEach(col => {
        col.style.minHeight = maxHeight + 'px';
    });
}

function initializeDragAndDrop() {
    const cards = document.querySelectorAll('.kanban-card');
    const columns = document.querySelectorAll('.kanban-column-body');

    // Hacer las tarjetas arrastrables
    cards.forEach(card => {
        card.draggable = true;
        
        card.addEventListener('dragstart', function(e) {
            this.classList.add('dragging');
            e.dataTransfer.setData('text/plain', this.dataset.id);
            e.dataTransfer.setData('application/json', JSON.stringify({
                id: this.dataset.id,
                status: this.dataset.status
            }));
        });

        card.addEventListener('dragend', function(e) {
            this.classList.remove('dragging');
            cards.forEach(c => c.classList.remove('drag-over'));
        });
    });

    // Configurar las columnas como zonas de drop
    columns.forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        column.addEventListener('dragleave', function(e) {
            if (!this.contains(e.relatedTarget)) {
                this.classList.remove('drag-over');
            }
        });

        column.addEventListener('drop', async function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            const cardData = JSON.parse(e.dataTransfer.getData('application/json'));
            const cardElement = document.querySelector(`[data-id="${cardData.id}"]`);
            
            if (cardElement && this !== cardElement.parentNode) {
                // Determinar el nuevo estado basado en la columna
                let newStatus = '';
                let statusText = '';
                let statusIcon = '';
                
                switch(this.id) {
                    case 'en-proceso':
                        newStatus = 'En Proceso';
                        statusText = 'en proceso';
                        statusIcon = 'info';
                        break;
                    case 'pendiente':
                        newStatus = 'Pendiente';
                        statusText = 'pendiente';
                        statusIcon = 'warning';
                        break;
                    case 'completo':
                        newStatus = 'Completado';
                        statusText = 'completado';
                        statusIcon = 'success';
                        break;
                }

                // VALIDACIONES DEL LADO CLIENTE
                const estadoActual = cardData.status;
                const validacion = validarTransicionEstado(estadoActual, newStatus);
                
                if (!validacion.valido) {
                    // Mostrar error de validación
                    Swal.fire({
                        title: 'Movimiento no permitido',
                        text: validacion.mensaje,
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#ff6b35'
                    });
                    return;
                }

                // Confirmar el cambio de estado con SweetAlert
                const result = await Swal.fire({
                    title: 'Cambiar Estado del Equipo',
                    text: `¿Deseas cambiar el estado del equipo #${cardData.id} a "${statusText}"?`,
                    icon: statusIcon,
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#ff6b35',
                    cancelButtonColor: '#6c757d'
                });

                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Actualizando estado...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Llamada AJAX para actualizar el estado en la base de datos
                    try {
                        const success = await updateEquipmentStatus(cardData.id, newStatus);
                        
                        if (success) {
                            // Actualizar la tarjeta visualmente
                            updateCardStatus(cardElement, newStatus);
                            
                            // Mover la tarjeta a la nueva columna
                            this.appendChild(cardElement);
                            
                            // Actualizar estados vacíos
                            updateEmptyStates();
                            
                            // Cerrar loading y mostrar éxito
                            Swal.close();
                            showNotification(`Estado cambiado a "${statusText}" correctamente`, 'success');
                        } else {
                            // Error al actualizar
                            Swal.close();
                            showNotification('Error al actualizar el estado', 'error');
                        }
                    } catch (error) {
                        Swal.close();
                        showNotification('Error de conexión', 'error');
                        console.error('Error:', error);
                    }
                } else {
                    // Si se cancela, devolver la tarjeta a su posición original
                    cardElement.style.transform = '';
                    cardElement.style.opacity = '';
                }
            }
        });
    });
}

function updateEmptyStates() {
    const columns = document.querySelectorAll('.kanban-column-body');
    
    columns.forEach(column => {
        const emptyState = column.querySelector('.kanban-empty-state');
        const hasCards = column.querySelector('.kanban-card:not(.kanban-empty-state)');
        
        if (hasCards) {
            if (emptyState) emptyState.style.display = 'none';
        } else {
            // Mostrar estado vacío si no existe
            if (!emptyState) {
                const status = column.id;
                let text = '';
                
                switch(status) {
                    case 'pendiente':
                        text = 'No hay equipos pendientes';
                        break;
                    case 'en-proceso':
                        text = 'No hay equipos en proceso';
                        break;
                    case 'completo':
                        text = 'No hay equipos completados';
                        break;
                }
                
                const emptyHtml = `
                    <div class="kanban-empty-state">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p class="mb-0">${text}</p>
                    </div>
                `;
                
                column.innerHTML = emptyHtml;
            } else {
                emptyState.style.display = 'flex';
            }
        }
    });
}

function updateCardStatus(cardElement, newStatus) {
    const badge = cardElement.querySelector('.badge');
    const statusMap = {
        'En Proceso': {
            class: 'badge-en-proceso',
            icon: 'fa-spinner',
            text: 'En Proceso'
        },
        'Pendiente': {
            class: 'badge-pendiente',
            icon: 'fa-clock',
            text: 'Pendiente'
        },
        'Completado': {
            class: 'badge-completo',
            icon: 'fa-check-circle',
            text: 'Completado'
        }
    };

    if (badge && statusMap[newStatus]) {
        // Remover clases anteriores
        badge.className = 'badge ' + statusMap[newStatus].class;
        
        // Actualizar contenido
        badge.innerHTML = `<i class="fas ${statusMap[newStatus].icon} me-1"></i>${statusMap[newStatus].text}`;
        
        // Actualizar data-status
        cardElement.dataset.status = newStatus;
    }
}

async function updateEquipmentStatus(equipmentId, newStatus) {
    try {
        const response = await fetch('<?= base_url("equipos/actualizar-estado") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id: equipmentId,
                estado: newStatus
            })
        });

        const data = await response.json();
        
        if (data.success) {
            return true;
        } else {
            // Si hay un error específico del servidor, mostrarlo
            if (data.error) {
                showNotification(data.error, 'error');
            }
            return false;
        }
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

// Función para validar transiciones de estado del lado cliente
function validarTransicionEstado(estadoActual, nuevoEstado) {
    // Normalizar estados para comparación
    estadoActual = estadoActual.trim();
    nuevoEstado = nuevoEstado.trim();

    // Validación 1: Si el servicio está completo, no se puede mover a pendiente o en proceso
    if (estadoActual === 'Completado') {
        if (nuevoEstado === 'Pendiente' || nuevoEstado === 'En Proceso') {
            return {
                valido: false,
                mensaje: 'Este servicio ya está completo'
            };
        }
    }

    // Validación 2: Si el servicio está pendiente y se quiere mover directamente a completo
    if (estadoActual === 'Pendiente' || estadoActual === 'Programado') {
        if (nuevoEstado === 'Completado') {
            return {
                valido: false,
                mensaje: 'Este servicio aún no tiene proceso'
            };
        }
    }

    // Validación 3: Si el servicio está en proceso y se quiere mover a pendiente
    if (estadoActual === 'En Proceso') {
        if (nuevoEstado === 'Pendiente') {
            return {
                valido: false,
                mensaje: 'Este servicio está en proceso'
            };
        }
    }

    // Si llegamos aquí, la transición es válida
    return {
        valido: true,
        mensaje: 'Transición válida'
    };
}

function showNotification(message, type = 'info') {
    // Usar SweetAlert Toast para notificaciones elegantes
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });

    Toast.fire({
        icon: type === 'success' ? 'success' : type === 'error' ? 'error' : 'info',
        title: message
    });
}

// Función para animar las tarjetas al cargar
function animateCards() {
    const cards = document.querySelectorAll('.kanban-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Ejecutar animación al cargar
setTimeout(animateCards, 100);
</script>

<!-- CSS Mejorado del Tablero Kanban -->
<style>
/* Variables CSS */
:root {
    --orange-primary: #ff6b35;
    --orange-secondary: #ff8c42;
    --orange-light: #ffb366;
    --orange-dark: #e55a2b;
    --orange-subtle: #fff4f0;
    --gray-50: #fafafa;
    --gray-100: #f5f5f5;
    --gray-200: #eeeeee;
    --gray-300: #e0e0e0;
    --gray-400: #bdbdbd;
    --gray-500: #9e9e9e;
    --gray-600: #757575;
    --gray-700: #616161;
    --gray-800: #424242;
    --gray-900: #212121;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
    --transition-fast: 0.15s ease-in-out;
    --transition-normal: 0.3s ease-in-out;
}

/* Estilos de colores naranja */
.text-orange {
    color: var(--orange-primary) !important;
}

.bg-orange {
    background-color: var(--orange-primary) !important;
}

.btn-orange {
    background-color: var(--orange-primary);
    border-color: var(--orange-primary);
    color: white;
    font-weight: 500;
    transition: all var(--transition-fast);
    box-shadow: var(--shadow-sm);
}

.btn-orange:hover {
    background-color: var(--orange-dark);
    border-color: var(--orange-dark);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-outline-orange {
    border-color: var(--orange-primary);
    color: var(--orange-primary);
    font-weight: 500;
    transition: all var(--transition-fast);
}

.btn-outline-orange:hover {
    background-color: var(--orange-primary);
    border-color: var(--orange-primary);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Layout principal del tablero Kanban */
.kanban-board {
    background: linear-gradient(135deg, var(--gray-50) 0%, white 100%);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    width: 100%;
    overflow-x: auto;
}

.kanban-columns-container {
    display: flex;
    flex-wrap: nowrap;
    min-width: min-content;
}

.kanban-column-wrapper {
    flex: 1;
    min-width: 320px;
    max-width: 400px;
}

/* Columnas del tablero */
.kanban-column {
    background-color: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: all var(--transition-normal);
}

.kanban-column:hover {
    box-shadow: var(--shadow-xl);
    transform: translateY(-2px);
}

.kanban-column-header {
    padding: 1rem;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    position: relative;
    overflow: hidden;
}

.kanban-column-header h5 {
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: 0.025em;
    margin: 0;
    color: white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.kanban-column-body {
    padding: 1rem;
    flex: 1;
    overflow-y: auto;
    background-color: var(--gray-50);
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    min-height: 400px;
}

/* Tarjetas del tablero */
.kanban-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-normal);
    cursor: grab;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 180px;
}

.kanban-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
    border-color: var(--orange-light);
}

.kanban-card:active {
    cursor: grabbing;
}

.card-header-kanban {
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-50);
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-height: 32px;
}

.kanban-card-id {
    font-size: 0.7rem;
    color: var(--gray-500);
    font-weight: 500;
}

.card-body-kanban {
    padding: 0.75rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    overflow: hidden;
}

.card-actions {
    padding: 0.5rem 0.75rem;
    border-top: 1px solid var(--gray-200);
    background: var(--gray-50);
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    min-height: 40px;
    align-items: center;
}

/* Información dentro de las tarjetas */
.info-field {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    font-size: 0.875rem;
    line-height: 1.3;
}

.info-field strong {
    font-weight: 600;
    font-size: 0.75rem;
    color: var(--orange-primary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 0;
}

.info-field span {
    color: var(--gray-700);
    font-weight: 400;
    line-height: 1.2;
    font-size: 0.875rem;
    word-break: break-word;
}

.description-text {
    font-style: italic;
    color: var(--gray-600);
    font-size: 0.8rem;
    line-height: 1.3;
}

/* Badges de estado */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius-sm);
    font-weight: 500;
    letter-spacing: 0.025em;
    box-shadow: var(--shadow-sm);
    border: none;
}

.badge-en-proceso {
    background:rgb(16, 196, 241);
    color: white;
}

.badge-pendiente {
    background: #ffc107;
    color: var(--gray-800);
}

.badge-completo {
    background: #28a745;
    color: white;
}

/* Estado vacío */
.kanban-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    color: var(--gray-500);
    text-align: center;
    flex: 1;
}

/* Botones */
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    border-radius: var(--radius-sm);
    font-weight: 500;
    transition: all var(--transition-fast);
    border: none;
    box-shadow: var(--shadow-sm);
    min-width: 60px;
}

.btn-sm:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Animaciones drag and drop */
.kanban-card.dragging {
    opacity: 0.7;
    transform: rotate(3deg) scale(1.02);
    box-shadow: var(--shadow-xl);
    z-index: 1000;
}

.kanban-column-body.drag-over {
    background: linear-gradient(135deg, var(--orange-subtle) 0%, rgba(255, 107, 53, 0.1) 100%);
    border: 2px dashed var(--orange-primary);
    border-radius: var(--radius-md);
}

/* Animaciones de entrada */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.kanban-card {
    animation: slideInUp 0.3s ease-out;
}

/* Diseño responsive */
@media (max-width: 1200px) {
    .kanban-board {
        padding: 1rem;
    }
    
    .kanban-column-wrapper {
        min-width: 300px;
    }
}

@media (max-width: 992px) {
    .kanban-columns-container {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .kanban-column-wrapper {
        margin-bottom: 1rem;
    }
}

@media (max-width: 768px) {
    .kanban-board {
        padding: 0.75rem;
    }

    .kanban-column-wrapper {
        min-width: 100%;
    }

    .kanban-card {
        min-height: 160px;
    }
}

@media (max-width: 480px) {
    .kanban-board {
        padding: 0.5rem;
    }

    .kanban-column-header {
        padding: 0.75rem;
    }

    .kanban-column-header h5 {
        font-size: 0.875rem;
    }

    .kanban-card {
        min-height: 140px;
    }

    .card-actions {
        flex-direction: row;
        gap: 0.25rem;
    }

    .btn-sm {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        min-width: 50px;
    }
}

/* Scrollbar personalizado */
.kanban-column-body::-webkit-scrollbar {
    width: 6px;
}

.kanban-column-body::-webkit-scrollbar-track {
    background: var(--gray-100);
    border-radius: var(--radius-sm);
}

.kanban-column-body::-webkit-scrollbar-thumb {
    background: var(--gray-300);
    border-radius: var(--radius-sm);
}

.kanban-column-body::-webkit-scrollbar-thumb:hover {
    background: var(--orange-primary);
}

/* Mejoras en accesibilidad */
.kanban-card:focus {
    outline: 2px solid var(--orange-primary);
    outline-offset: 2px;
}

.btn:focus {
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.25);
}

/* Estilos generales mejorados */
.card {
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid var(--gray-200);
}

.container {
    max-width: 100%;
    padding: 0 15px;
}
</style>
<?= $footer ?>