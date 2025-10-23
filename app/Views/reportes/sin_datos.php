<?php
// Vista para mostrar cuando no hay datos en el reporte
?>

<!-- Header del reporte -->
<div class="report-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="mb-1">
                <i class="fas fa-chart-<?= $tipo_reporte === 'financiero' ? 'line' : ($tipo_reporte === 'equipos' ? 'bar' : 'pie') ?> mr-2"></i>
                <?= $metadata['nombre'] ?>
            </h4>
            <p class="text-muted mb-0"><?= $metadata['descripcion'] ?></p>
            <small class="text-muted">
                <i class="fas fa-calendar mr-1"></i>
                Generado el <?= date('d/m/Y H:i', strtotime($metadata['fecha_generacion'])) ?>
            </small>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarFiltros()">
                    <i class="fas fa-eraser mr-1"></i>
                    Limpiar Filtros
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="generarNuevoReporte()">
                    <i class="fas fa-sync-alt mr-1"></i>
                    Nuevo Reporte
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mensaje de no hay datos -->
<div class="card">
    <div class="card-body text-center py-5">
        <div class="mb-4">
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No se encontraron datos</h4>
        </div>
        
        <div class="alert alert-info text-left mx-auto" style="max-width: 600px;">
            <h6 class="alert-heading">
                <i class="fas fa-info-circle mr-2"></i>
                Información del Reporte
            </h6>
            <div class="mb-3">
                <strong>Tipo de Reporte:</strong> <?= $metadata['nombre'] ?><br>
                <strong>Categoría:</strong> <?= $metadata['categoria'] ?><br>
                <strong>Fecha de Generación:</strong> <?= date('d/m/Y H:i', strtotime($metadata['fecha_generacion'])) ?>
            </div>
            
            <?php if (!empty($filtros)): ?>
            <div class="mb-3">
                <strong>Filtros Aplicados:</strong>
                <ul class="mb-0 mt-2">
                    <?php if (!empty($filtros['fecha_desde']) && !empty($filtros['fecha_hasta'])): ?>
                        <li>Período: <?= date('d/m/Y', strtotime($filtros['fecha_desde'])) ?> - <?= date('d/m/Y', strtotime($filtros['fecha_hasta'])) ?></li>
                    <?php elseif (!empty($filtros['fecha_desde'])): ?>
                        <li>Desde: <?= date('d/m/Y', strtotime($filtros['fecha_desde'])) ?></li>
                    <?php elseif (!empty($filtros['fecha_hasta'])): ?>
                        <li>Hasta: <?= date('d/m/Y', strtotime($filtros['fecha_hasta'])) ?></li>
                    <?php endif; ?>
                    
                    <?php if (!empty($filtros['estado_entrega']) && $filtros['estado_entrega'] !== 'todos'): ?>
                        <li>Estado de Entrega: <?= ucfirst($filtros['estado_entrega']) ?></li>
                    <?php endif; ?>
                    
                    <?php if (!empty($filtros['tipo_evento']) && $filtros['tipo_evento'] !== 'todos'): ?>
                        <li>Tipo de Evento: <?= $filtros['tipo_evento'] ?></li>
                    <?php endif; ?>
                    
                    <?php if (!empty($filtros['estado_pago']) && $filtros['estado_pago'] !== 'todos'): ?>
                        <li>Estado de Pago: <?= ucfirst($filtros['estado_pago']) ?></li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>

        <div class="alert alert-warning text-left mx-auto" style="max-width: 600px;">
            <h6 class="alert-heading">
                <i class="fas fa-lightbulb mr-2"></i>
                Sugerencias
            </h6>
            <ul class="mb-0">
                <li>Verifique que el rango de fechas sea correcto</li>
                <li>Intente ampliar el período de búsqueda</li>
                <li>Revise los filtros aplicados</li>
                <li>Asegúrese de que existan registros en el sistema para el tipo de reporte seleccionado</li>
            </ul>
        </div>

        <div class="mt-4">
            <button type="button" class="btn btn-primary mr-2" onclick="limpiarFiltros()">
                <i class="fas fa-eraser mr-2"></i>
                Limpiar Filtros
            </button>
            <button type="button" class="btn btn-outline-primary" onclick="generarNuevoReporte()">
                <i class="fas fa-sync-alt mr-2"></i>
                Generar Nuevo Reporte
            </button>
        </div>
    </div>
</div>

<!-- Scripts para la vista sin datos -->
<script>
// Funciones para manejar la vista sin datos
function limpiarFiltros() {
    // Limpiar todos los filtros del formulario
    $('#filtros-dinamicos input, #filtros-dinamicos select').val('');
    $('#filtros-activos').text('0');
    
    // Mostrar mensaje de confirmación
    Swal.fire({
        title: 'Filtros Limpiados',
        text: 'Todos los filtros han sido limpiados. Puede generar un nuevo reporte.',
        icon: 'success',
        confirmButtonText: 'Entendido',
        timer: 3000,
        timerProgressBar: true
    });
}

function generarNuevoReporte() {
    // Volver al estado inicial
    $('#contenido-reporte').hide();
    $('#estado-inicial').show();
    
    // Mostrar mensaje informativo
    Swal.fire({
        title: 'Nuevo Reporte',
        text: 'Seleccione un tipo de reporte y configure los filtros según sus necesidades.',
        icon: 'info',
        confirmButtonText: 'Entendido'
    });
}

// Mostrar mensaje informativo al cargar la vista
$(document).ready(function() {
    // Opcional: mostrar un toast o notificación
    if (typeof toastr !== 'undefined') {
        toastr.info('No se encontraron datos para el reporte con los filtros aplicados', 'Sin Resultados');
    }
});
</script>

<!-- Estilos específicos para la vista sin datos -->


