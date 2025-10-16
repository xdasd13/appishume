<?= $header ?>

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
<style>
:root {
    --primary-color: #2c5aa0;
    --primary-light: #e8f0fe;
    --success-color: #10b981;
    --success-light: #d1fae5;
    --info-color: #0ea5e9;
    --info-light: #e0f2fe;
    --warning-color: #f59e0b;
    --warning-light: #fef3c7;
    --danger-color: #ef4444;
    --danger-light: #fee2e2;
    --gray-100: #f8f9fa;
    --gray-200: #e9ecef;
    --gray-600: #6c757d;
    --gray-800: #343a40;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
    --border-radius: 12px;
    --transition: all 0.3s ease;
}

/* Header Styles */
.header-icon-container {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-primary-gradient {
    background: linear-gradient(135deg, var(--primary-color), #4c7bd9);
}

/* Stat Cards */
.stat-card {
    border-radius: var(--border-radius);
    transition: var(--transition);
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg) !important;
}

.shadow-hover {
    box-shadow: var(--shadow-sm);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-primary-light { background-color: var(--primary-light); }
.bg-success-light { background-color: var(--success-light); }
.bg-info-light { background-color: var(--info-light); }
.bg-warning-light { background-color: var(--warning-light); }

/* Equipment Cards */
.equipment-card {
    border-radius: var(--border-radius);
    transition: var(--transition);
    overflow: hidden;
    background: white;
}

.equipment-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}

.equipment-image-container {
    position: relative;
    height: 220px;
    overflow: hidden;
    background: linear-gradient(135deg, var(--gray-100), var(--gray-200));
}

.equipment-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.equipment-card:hover .equipment-image {
    transform: scale(1.05);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, transparent 60%, rgba(0,0,0,0.3));
    opacity: 0;
    transition: var(--transition);
}

.equipment-card:hover .image-overlay {
    opacity: 1;
}

.equipment-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--gray-600);
    background: var(--gray-100);
}

/* Status Badges */
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-new {
    background: var(--success-light);
    color: var(--success-color);
    border: 1px solid var(--success-color);
}

.badge-in-use {
    background: var(--primary-light);
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
}

.badge-maintenance {
    background: var(--warning-light);
    color: var(--warning-color);
    border: 1px solid var(--warning-color);
}

.badge-damaged {
    background: var(--danger-light);
    color: var(--danger-color);
    border: 1px solid var(--danger-color);
}

.badge-other {
    background: var(--gray-200);
    color: var(--gray-600);
    border: 1px solid var(--gray-600);
}

.equipment-status-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 2;
}

/* Botones de acción visibles */
.equipment-actions-visible {
    opacity: 1;
    transform: none;
}

.equipment-actions-visible .btn-group {
    gap: 2px;
}

.equipment-actions-visible .btn {
    border-radius: 6px;
    font-size: 0.75rem;
    padding: 0.4rem 0.5rem;
    transition: all 0.3s ease;
    flex: 1;
    border: 1px solid #dee2e6;
}

.equipment-actions-visible .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.equipment-actions-visible .btn-outline-primary:hover {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.equipment-actions-visible .btn-outline-warning:hover {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
    color: white;
}

.equipment-actions-visible .btn-outline-danger:hover {
    background-color: var(--danger-color);
    border-color: var(--danger-color);
}

/* Content Styles */
.equipment-model {
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
}

.equipment-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
}

.meta-item i {
    width: 16px;
    text-align: center;
}

.equipment-description {
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Stock Indicator */
.stock-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.in-stock {
    background: var(--success-light);
    color: var(--success-color);
}

.out-of-stock {
    background: var(--danger-light);
    color: var(--danger-color);
}

/* Empty State */
.empty-state {
    padding: 3rem 1rem;
}

/* Form Controls */
.form-select, .form-control {
    border-radius: 8px;
    border: 1px solid var(--gray-200);
    transition: var(--transition);
}

.form-select:focus, .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(44, 90, 160, 0.1);
}

/* Responsive */
@media (max-width: 768px) {
    .equipment-image-container {
        height: 180px;
    }
    
    .equipment-actions-visible .btn {
        font-size: 0.7rem;
        padding: 0.3rem 0.4rem;
    }
    
    .equipment-actions-visible .btn i {
        margin-right: 0.25rem;
    }
}

/* Animation for card appearance */
.equipo-card {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Stagger animation for multiple cards */
.equipo-card:nth-child(1) { animation-delay: 0.1s; }
.equipo-card:nth-child(2) { animation-delay: 0.2s; }
.equipo-card:nth-child(3) { animation-delay: 0.3s; }
.equipo-card:nth-child(4) { animation-delay: 0.4s; }
.equipo-card:nth-child(5) { animation-delay: 0.5s; }
.equipo-card:nth-child(6) { animation-delay: 0.6s; }

/* Tooltips */
.tooltip {
    font-size: 0.75rem;
}
</style>

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