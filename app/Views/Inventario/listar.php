<?= $header ?>

<!-- Contenido Principal del Inventario -->
<div class="container-fluid py-4">
    <!-- Header con estadísticas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">
                    <i class="fas fa-boxes text-primary me-2"></i>
                    Inventario de Equipos Audiovisuales
                </h2>
                <a href="/inventario/crear" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Agregar Equipo
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Equipos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $estadisticas['total'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Disponibles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $estadisticas['total_disponible'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Categorías
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($categorias) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Marcas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($marcas) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h6>
        </div>
        <div class="card-body">
            <form id="filtrosForm" class="row g-3">
                <div class="col-md-3">
                    <label for="filtroCategoria" class="form-label">Categoría</label>
                    <select class="form-select" id="filtroCategoria" name="categoria">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria['idCateEquipo'] ?>">
                                <?= esc($categoria['nomCate']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroMarca" class="form-label">Marca</label>
                    <select class="form-select" id="filtroMarca" name="marca">
                        <option value="">Todas las marcas</option>
                        <?php foreach ($marcas as $marca): ?>
                            <option value="<?= $marca['idMarca'] ?>">
                                <?= esc($marca['nomMarca']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroEstado" class="form-label">Estado</label>
                    <select class="form-select" id="filtroEstado" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="Nuevo">Nuevo</option>
                        <option value="EnUso">En Uso</option>
                        <option value="EnMantenimiento">En Mantenimiento</option>
                        <option value="Dañado">Dañado</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroModelo" class="form-label">Modelo</label>
                    <input type="text" class="form-control" id="filtroModelo" name="modelo" placeholder="Buscar por modelo...">
                </div>
            </form>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="button" class="btn btn-primary" onclick="aplicarFiltros()">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                    <button type="button" class="btn btn-secondary ms-2" onclick="limpiarFiltros()">
                        <i class="fas fa-times me-2"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de Equipos -->
    <div class="row" id="equiposContainer">
        <?php if (empty($equipos)): ?>
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay equipos en el inventario</h5>
                        <p class="text-muted">Comienza agregando tu primer equipo al inventario.</p>
                        <a href="/inventario/crear" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Agregar Primer Equipo
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($equipos as $equipo): ?>
                <div class="col-xl-4 col-lg-6 col-md-6 mb-4 equipo-card" 
                     data-categoria="<?= $equipo['idCateEquipo'] ?>" 
                     data-marca="<?= $equipo['idMarca'] ?>" 
                     data-estado="<?= $equipo['estado'] ?>">
                    <div class="card shadow h-100">
                        <!-- Imagen del equipo -->
                        <div class="card-img-top-container" style="height: 200px; overflow: hidden;">
                            <?php if (!empty($equipo['imgEquipo'])): ?>
                                <img src="/<?= esc($equipo['imgEquipo']) ?>" 
                                     class="card-img-top" 
                                     alt="<?= esc($equipo['modelo']) ?>"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                    <i class="fas fa-camera fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <!-- Badge de estado -->
                            <div class="mb-2">
                                <?php
                                $badgeClass = match($equipo['estado']) {
                                    'Nuevo' => 'bg-success',
                                    'EnUso' => 'bg-primary',
                                    'EnMantenimiento' => 'bg-warning',
                                    'Dañado' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= esc($equipo['estado']) ?>
                                </span>
                            </div>

                            <!-- Información principal -->
                            <h5 class="card-title text-truncate" title="<?= esc($equipo['modelo']) ?>">
                                <?= esc($equipo['modelo']) ?>
                            </h5>
                            
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-layer-group me-1"></i>
                                    <?= esc($equipo['nomCate']) ?>
                                </small><br>
                                <small class="text-muted">
                                    <i class="fas fa-tag me-1"></i>
                                    <?= esc($equipo['nomMarca']) ?>
                                </small>
                            </p>

                            <?php if (!empty($equipo['descripcion'])): ?>
                                <p class="card-text text-muted small">
                                    <?= esc(substr($equipo['descripcion'], 0, 100)) ?>
                                    <?= strlen($equipo['descripcion']) > 100 ? '...' : '' ?>
                                </p>
                            <?php endif; ?>

                            <!-- Cantidad disponible -->
                            <div class="mb-2">
                                <span class="badge bg-info">
                                    <i class="fas fa-cubes me-1"></i>
                                    Disponible: <?= $equipo['cantDisponible'] ?>
                                </span>
                            </div>

                            <!-- Botones de acción -->
                            <div class="mt-auto">
                                <div class="btn-group w-100" role="group">
                                    <button type="button" 
                                            class="btn btn-outline-info btn-sm" 
                                            onclick="verDetalles(<?= $equipo['idEquipo'] ?>)"
                                            title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="/inventario/editar/<?= $equipo['idEquipo'] ?>" 
                                       class="btn btn-outline-warning btn-sm"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm" 
                                            onclick="confirmarEliminacion(<?= $equipo['idEquipo'] ?>, '<?= esc($equipo['modelo']) ?>')"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Footer con información adicional -->
                        <div class="card-footer bg-transparent">
                            <small class="text-muted">
                                <?php if (!empty($equipo['sku'])): ?>
                                    <i class="fas fa-barcode me-1"></i>SKU: <?= esc($equipo['sku']) ?>
                                <?php endif; ?>
                                <?php if (!empty($equipo['fechaCompra'])): ?>
                                    <br><i class="fas fa-calendar me-1"></i>
                                    Compra: <?= date('d/m/Y', strtotime($equipo['fechaCompra'])) ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Paginación (si es necesaria) -->
    <div class="row">
        <div class="col-12">
            <nav aria-label="Paginación del inventario">
                <!-- Aquí se puede agregar paginación si es necesaria -->
            </nav>
        </div>
    </div>
</div>

<!-- Modal para ver detalles del equipo -->
<div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetallesLabel">
                    <i class="fas fa-info-circle me-2"></i>Detalles del Equipo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetallesContent">
                <!-- Contenido cargado dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts JavaScript -->
<script>
// Función para aplicar filtros
function aplicarFiltros() {
    const categoria = document.getElementById('filtroCategoria').value;
    const marca = document.getElementById('filtroMarca').value;
    const estado = document.getElementById('filtroEstado').value;
    const modelo = document.getElementById('filtroModelo').value.toLowerCase();

    const cards = document.querySelectorAll('.equipo-card');
    
    cards.forEach(card => {
        let mostrar = true;

        // Filtro por categoría
        if (categoria && card.dataset.categoria !== categoria) {
            mostrar = false;
        }

        // Filtro por marca
        if (marca && card.dataset.marca !== marca) {
            mostrar = false;
        }

        // Filtro por estado
        if (estado && card.dataset.estado !== estado) {
            mostrar = false;
        }

        // Filtro por modelo
        if (modelo) {
            const tituloEquipo = card.querySelector('.card-title').textContent.toLowerCase();
            if (!tituloEquipo.includes(modelo)) {
                mostrar = false;
            }
        }

        card.style.display = mostrar ? 'block' : 'none';
    });

    // Mostrar mensaje si no hay resultados
    const equiposVisibles = document.querySelectorAll('.equipo-card[style="display: block"], .equipo-card:not([style*="display: none"])').length;
    
    if (equiposVisibles === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Sin resultados',
            text: 'No se encontraron equipos que coincidan con los filtros aplicados.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }
}

// Función para limpiar filtros
function limpiarFiltros() {
    document.getElementById('filtrosForm').reset();
    
    const cards = document.querySelectorAll('.equipo-card');
    cards.forEach(card => {
        card.style.display = 'block';
    });

    Swal.fire({
        icon: 'success',
        title: 'Filtros limpiados',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    });
}

// Función para ver detalles del equipo
function verDetalles(equipoId) {
    fetch(`/inventario/ver/${equipoId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const equipo = data.equipo;
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            ${equipo.imgEquipo ? 
                                `<img src="/${equipo.imgEquipo}" class="img-fluid rounded mb-3" alt="${equipo.modelo}">` :
                                `<div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                                    <i class="fas fa-camera fa-3x text-muted"></i>
                                </div>`
                            }
                        </div>
                        <div class="col-md-6">
                            <h5>${equipo.modelo}</h5>
                            <p><strong>Categoría:</strong> ${equipo.nomCate}</p>
                            <p><strong>Marca:</strong> ${equipo.nomMarca}</p>
                            <p><strong>Estado:</strong> <span class="badge bg-primary">${equipo.estado}</span></p>
                            <p><strong>Cantidad Disponible:</strong> ${equipo.cantDisponible}</p>
                            ${equipo.sku ? `<p><strong>SKU:</strong> ${equipo.sku}</p>` : ''}
                            ${equipo.numSerie ? `<p><strong>Número de Serie:</strong> ${equipo.numSerie}</p>` : ''}
                            ${equipo.fechaCompra ? `<p><strong>Fecha de Compra:</strong> ${new Date(equipo.fechaCompra).toLocaleDateString()}</p>` : ''}
                            ${equipo.fechaUso ? `<p><strong>Fecha de Uso:</strong> ${new Date(equipo.fechaUso).toLocaleDateString()}</p>` : ''}
                        </div>
                    </div>
                    ${equipo.descripcion ? `
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6>Descripción:</h6>
                                <p>${equipo.descripcion}</p>
                            </div>
                        </div>
                    ` : ''}
                    ${equipo.caracteristica ? `
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6>Características:</h6>
                                <p>${equipo.caracteristica}</p>
                            </div>
                        </div>
                    ` : ''}
                `;
                
                document.getElementById('modalDetallesContent').innerHTML = content;
                const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
                modal.show();
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
                text: 'Error al cargar los detalles del equipo'
            });
        });
}

// Función para confirmar eliminación
function confirmarEliminacion(equipoId, modelo) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas eliminar el equipo "${modelo}" del inventario?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarEquipo(equipoId);
        }
    });
}

// Función para eliminar equipo
function eliminarEquipo(equipoId) {
    fetch(`/inventario/eliminar/${equipoId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
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
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            
            // Recargar la página para actualizar la lista
            setTimeout(() => {
                window.location.reload();
            }, 1500);
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

// Mostrar mensajes flash con SweetAlert
<?php if (session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: '<?= session()->getFlashdata('success') ?>',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000
    });
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= session()->getFlashdata('error') ?>'
    });
<?php endif; ?>

// Aplicar filtros al presionar Enter en el campo de modelo
document.getElementById('filtroModelo').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        aplicarFiltros();
    }
});
</script>

<?= $footer ?>