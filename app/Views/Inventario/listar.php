<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/Inventario-listar.css') ?>">

<!-- Contenido Principal del Inventario -->
<div class="container-fluid px-4">
    <!-- Header Mejorado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <div class="header-icon-container bg-primary-gradient rounded-circle me-3">
                <i class="fas fa-boxes text-white"></i>
            </div>
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">Inventario de Equipos</h1>
                <p class="text-muted mb-0">Gestiona y organiza tu equipo audiovisual</p>
            </div>
        </div>
        <a href="/inventario/crear" class="btn btn-primary btn-lg shadow-sm">
            <i class="fas fa-plus-circle me-2"></i>
            Nuevo Equipo
        </a>
    </div>

    <!-- Tarjetas de Estadísticas Mejoradas -->
    <div class="row mb-5">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-hover">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-semibold text-primary text-uppercase mb-1">Total Equipos</div>
                            <div class="h2 fw-bold text-gray-800 mb-0"><?= $estadisticas['total'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <div class="stat-icon bg-primary-light">
                                <i class="fas fa-boxes text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-success small">
                            <i class="fas fa-caret-up me-1"></i>
                            Inventario activo
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-hover">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-semibold text-success text-uppercase mb-1">Disponibles</div>
                            <div class="h2 fw-bold text-gray-800 mb-0"><?= $estadisticas['total_disponible'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <div class="stat-icon bg-success-light">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-success small">
                            <i class="fas fa-check me-1"></i>
                            Listos para usar
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-hover">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-semibold text-info text-uppercase mb-1">Categorías</div>
                            <div class="h2 fw-bold text-gray-800 mb-0"><?= count($categorias) ?></div>
                        </div>
                        <div class="col-auto">
                            <div class="stat-icon bg-info-light">
                                <i class="fas fa-layer-group text-info"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-info small">
                            <i class="fas fa-tags me-1"></i>
                            Tipos de equipo
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-hover">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-semibold text-warning text-uppercase mb-1">Marcas</div>
                            <div class="h2 fw-bold text-gray-800 mb-0"><?= count($marcas) ?></div>
                        </div>
                        <div class="col-auto">
                            <div class="stat-icon bg-warning-light">
                                <i class="fas fa-tags text-warning"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-warning small">
                            <i class="fas fa-industry me-1"></i>
                            Fabricantes
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de Filtros Mejorado -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-sliders-h text-primary me-2"></i>
                    Filtros y Búsqueda
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="aplicarFiltros()">
                        <i class="fas fa-search me-1"></i>Buscar
                    </button>
                    <button type="button" class="btn btn-sm btn-light" onclick="limpiarFiltros()">
                        <i class="fas fa-undo me-1"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-semibold text-uppercase text-muted">Categoría</label>
                    <select class="form-select form-select-sm" id="filtroCategoria">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria['idCateEquipo'] ?>">
                                <?= esc($categoria['nomCate']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-semibold text-uppercase text-muted">Marca</label>
                    <select class="form-select form-select-sm" id="filtroMarca">
                        <option value="">Todas las marcas</option>
                        <?php foreach ($marcas as $marca): ?>
                            <option value="<?= $marca['idMarca'] ?>">
                                <?= esc($marca['nomMarca']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-semibold text-uppercase text-muted">Estado</label>
                    <select class="form-select form-select-sm" id="filtroEstado">
                        <option value="">Todos los estados</option>
                        <option value="Nuevo">Nuevo</option>
                        <option value="EnUso">En Uso</option>
                        <option value="EnMantenimiento">En Mantenimiento</option>
                        <option value="Dañado">Dañado</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-semibold text-uppercase text-muted">Buscar Modelo</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="filtroModelo" 
                               placeholder="Escribe para buscar...">
                        <button class="btn btn-outline-primary" type="button" onclick="aplicarFiltros()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de Equipos Mejorado -->
    <div class="row g-4" id="equiposContainer">
        <?php if (empty($equipos)): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-box-open fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted mb-3">No hay equipos en el inventario</h4>
                            <p class="text-muted mb-4">Comienza agregando tu primer equipo al inventario.</p>
                            <a href="/inventario/crear" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-plus me-2"></i>Agregar Primer Equipo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($equipos as $equipo): ?>
                <div class="col-xl-4 col-lg-6 col-md-6 equipo-card" 
                     data-categoria="<?= $equipo['idCateEquipo'] ?>" 
                     data-marca="<?= $equipo['idMarca'] ?>" 
                     data-estado="<?= $equipo['estado'] ?>">
                    <div class="card equipment-card border-0 shadow-sm h-100">
                        <!-- Imagen del Equipo -->
                        <div class="equipment-image-container">
                            <?php if (!empty($equipo['imgEquipo'])): ?>
                                <img src="/<?= esc($equipo['imgEquipo']) ?>" 
                                     class="equipment-image"
                                     alt="<?= esc($equipo['modelo']) ?>"
                                     loading="lazy">
                                <div class="image-overlay"></div>
                            <?php else: ?>
                                <div class="equipment-image-placeholder">
                                    <i class="fas fa-camera fa-3x"></i>
                                    <p class="mt-2 small">Sin imagen</p>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badge de Estado -->
                            <div class="equipment-status-badge">
                                <?php
                                $badgeConfig = [
                                    'Nuevo' => ['class' => 'badge-new', 'icon' => 'fas fa-star'],
                                    'EnUso' => ['class' => 'badge-in-use', 'icon' => 'fas fa-play-circle'],
                                    'EnMantenimiento' => ['class' => 'badge-maintenance', 'icon' => 'fas fa-tools'],
                                    'Dañado' => ['class' => 'badge-damaged', 'icon' => 'fas fa-exclamation-triangle'],
                                    'Otro' => ['class' => 'badge-other', 'icon' => 'fas fa-question-circle']
                                ];
                                $config = $badgeConfig[$equipo['estado']] ?? $badgeConfig['Otro'];
                                ?>
                                <span class="status-badge <?= $config['class'] ?>">
                                    <i class="<?= $config['icon'] ?> me-1"></i>
                                    <?= $equipo['estado'] ?>
                                </span>
                            </div>
                        </div>

                        <!-- Información del Equipo -->
                        <div class="card-body">
                            <h6 class="equipment-model text-truncate" title="<?= esc($equipo['modelo']) ?>">
                                <?= esc($equipo['modelo']) ?>
                            </h6>
                            
                            <div class="equipment-meta">
                                <div class="meta-item">
                                    <i class="fas fa-tag text-muted"></i>
                                    <span><?= esc($equipo['nomCate']) ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-industry text-muted"></i>
                                    <span><?= esc($equipo['nomMarca']) ?></span>
                                </div>
                                <?php if (!empty($equipo['sku'])): ?>
                                <div class="meta-item">
                                    <i class="fas fa-barcode text-muted"></i>
                                    <span class="small"><?= esc($equipo['sku']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($equipo['descripcion'])): ?>
                            <p class="equipment-description small text-muted mt-2">
                                <?= esc(substr($equipo['descripcion'], 0, 80)) ?>
                                <?= strlen($equipo['descripcion']) > 80 ? '...' : '' ?>
                            </p>
                            <?php endif; ?>
                        </div>

                        <!-- Footer con Stock y Botones -->
                        <div class="card-footer bg-transparent border-top-0 pt-0">
                            <div class="stock-section mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="stock-indicator">
                                        <span class="stock-badge <?= $equipo['cantDisponible'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                                            <i class="fas fa-box me-1"></i>
                                            <?= $equipo['cantDisponible'] ?> disponible(s)
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        <?php if (!empty($equipo['fechaCompra'])): ?>
                                            <?= date('M Y', strtotime($equipo['fechaCompra'])) ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>

                            <!-- Botones de Acción Visibles -->
                            <div class="equipment-actions-visible">
                                <div class="btn-group w-100" role="group">
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm" 
                                            onclick="verDetalles(<?= $equipo['idEquipo'] ?>)"
                                            title="Ver Detalles"
                                            data-bs-toggle="tooltip">
                                        <i class="fas fa-eye me-1"></i>Ver
                                    </button>
                                    <a href="/inventario/editar/<?= $equipo['idEquipo'] ?>" 
                                       class="btn btn-outline-warning btn-sm"
                                       title="Editar Equipo"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-edit me-1"></i>Editar
                                    </a>
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm" 
                                            onclick="eliminarEquipo(<?= $equipo['idEquipo'] ?>)"
                                            title="Eliminar Equipo"
                                            data-bs-toggle="tooltip">
                                        <i class="fas fa-trash me-1"></i>Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para Detalles -->
<div class="modal fade" id="detallesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-semibold">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Detalles del Equipo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="detallesContent">
                    <!-- Contenido cargado dinámicamente -->
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer ?>

<!-- Estilos CSS Mejorados -->


<!-- JavaScript Mejorado -->
<script>
// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});

// Aplicar filtros con animación
function aplicarFiltros() {
    const categoria = document.getElementById('filtroCategoria').value;
    const marca = document.getElementById('filtroMarca').value;
    const estado = document.getElementById('filtroEstado').value;
    const modelo = document.getElementById('filtroModelo').value.toLowerCase();

    const equipos = document.querySelectorAll('.equipo-card');
    let visibleCount = 0;
    
    equipos.forEach((equipo, index) => {
        const catMatch = !categoria || equipo.dataset.categoria === categoria;
        const marcaMatch = !marca || equipo.dataset.marca === marca;
        const estadoMatch = !estado || equipo.dataset.estado === estado;
        const modeloMatch = !modelo || 
            equipo.querySelector('.equipment-model').textContent.toLowerCase().includes(modelo);
        
        if (catMatch && marcaMatch && estadoMatch && modeloMatch) {
            equipo.style.display = 'block';
            equipo.style.animationDelay = `${(visibleCount % 6) * 0.1}s`;
            visibleCount++;
        } else {
            equipo.style.display = 'none';
        }
    });

    // Mostrar mensaje si no hay resultados
    if (visibleCount === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Sin resultados',
            text: 'No se encontraron equipos con los filtros aplicados.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }
}

function limpiarFiltros() {
    document.getElementById('filtroCategoria').value = '';
    document.getElementById('filtroMarca').value = '';
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroModelo').value = '';
    
    const equipos = document.querySelectorAll('.equipo-card');
    equipos.forEach((equipo, index) => {
        equipo.style.display = 'block';
        equipo.style.animationDelay = `${(index % 6) * 0.1}s`;
    });
}

function verDetalles(idEquipo) {
    // Mostrar loading
    const modalContent = document.getElementById('detallesContent');
    modalContent.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="text-muted">Cargando detalles del equipo...</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('detallesModal'));
    modal.show();

    fetch(`/inventario/ver/${idEquipo}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const equipo = data.equipo;
                const html = `
                    <div class="row g-0">
                        <div class="col-md-5">
                            <div class="p-4">
                                ${equipo.imgEquipo ? 
                                    `<img src="/${equipo.imgEquipo}" class="img-fluid rounded shadow-sm" alt="${equipo.modelo}">` : 
                                    `<div class="text-center py-4 bg-light rounded">
                                        <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Sin imagen disponible</p>
                                    </div>`
                                }
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="p-4">
                                <h4 class="fw-bold text-gray-800 mb-3">${equipo.modelo}</h4>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="small text-muted">Categoría</label>
                                        <p class="fw-semibold">${equipo.nomCate}</p>
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted">Marca</label>
                                        <p class="fw-semibold">${equipo.nomMarca}</p>
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted">Estado</label>
                                        <div>
                                            <span class="status-badge ${getBadgeClass(equipo.estado)}">
                                                ${equipo.estado}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted">Disponibles</label>
                                        <p class="fw-semibold">${equipo.cantDisponible} unidades</p>
                                    </div>
                                    ${equipo.sku ? `
                                    <div class="col-6">
                                        <label class="small text-muted">SKU</label>
                                        <p class="fw-semibold">${equipo.sku}</p>
                                    </div>
                                    ` : ''}
                                    ${equipo.numSerie ? `
                                    <div class="col-6">
                                        <label class="small text-muted">Número de Serie</label>
                                        <p class="fw-semibold">${equipo.numSerie}</p>
                                    </div>
                                    ` : ''}
                                    ${equipo.fechaCompra ? `
                                    <div class="col-6">
                                        <label class="small text-muted">Fecha de Compra</label>
                                        <p class="fw-semibold">${new Date(equipo.fechaCompra).toLocaleDateString()}</p>
                                    </div>
                                    ` : ''}
                                </div>
                                ${equipo.descripcion ? `
                                <div class="mt-3">
                                    <label class="small text-muted">Descripción</label>
                                    <p class="mb-0">${equipo.descripcion}</p>
                                </div>
                                ` : ''}
                                ${equipo.caracteristica ? `
                                <div class="mt-3">
                                    <label class="small text-muted">Características</label>
                                    <p class="mb-0">${equipo.caracteristica}</p>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
                modalContent.innerHTML = html;
            } else {
                modalContent.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h5 class="text-danger">Error al cargar los detalles</h5>
                        <p class="text-muted">${data.message}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalContent.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h5 class="text-danger">Error de conexión</h5>
                    <p class="text-muted">No se pudieron cargar los detalles del equipo.</p>
                </div>
            `;
        });
}

function eliminarEquipo(idEquipo) {
    Swal.fire({
        title: '¿Eliminar equipo?',
        text: "Esta acción no se puede deshacer. El equipo será eliminado permanentemente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-light'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/inventario/eliminar/${idEquipo}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al eliminar el equipo'
                });
            });
        }
    });
}

function getBadgeClass(estado) {
    const classes = {
        'Nuevo': 'badge-new',
        'EnUso': 'badge-in-use',
        'EnMantenimiento': 'badge-maintenance',
        'Dañado': 'badge-damaged',
        'Otro': 'badge-other'
    };
    return classes[estado] || 'badge-other';
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Buscar al presionar Enter
    document.getElementById('filtroModelo').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            aplicarFiltros();
        }
    });

    // Auto-aplicar filtros en selects
    document.getElementById('filtroCategoria').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroMarca').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);
});
</script>