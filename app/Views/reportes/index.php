<?= $this->include('Layouts/header') ?>

<div class="page-inner">
    <!-- Header del módulo -->
    <div class="page-header">
        <h4 class="page-title">
            <i class="fas fa-chart-bar mr-2"></i>
            Reportes Dinámicos
        </h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="<?= base_url('welcome') ?>">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <span>Reportes</span>
            </li>
        </ul>
    </div>

    <!-- Tarjetas de estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="icon-big text-center icon-success">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Reportes Disponibles</p>
                                <h4 class="card-title"><?= count($reportes_disponibles) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="icon-big text-center icon-primary">
                                <i class="fas fa-filter"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Filtros Activos</p>
                                <h4 class="card-title" id="filtros-activos">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="icon-big text-center icon-info">
                                <i class="fas fa-download"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Exportaciones</p>
                                <h4 class="card-title">PDF/Excel</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="icon-big text-center icon-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Última Actualización</p>
                                <h4 class="card-title"><?= date('H:i') ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel principal de reportes -->
    <div class="row">
        <!-- Panel de selección de reportes -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-list mr-2"></i>
                        Seleccionar Reporte
                    </h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="tipo-reporte">Tipo de Reporte</label>
                        <select class="form-control" id="tipo-reporte" name="tipo_reporte">
                            <option value="">-- Seleccionar reporte --</option>
                            <?php foreach ($reportes_disponibles as $key => $reporte): ?>
                                <option value="<?= $key ?>" 
                                        data-categoria="<?= $reporte['categoria'] ?>"
                                        data-descripcion="<?= $reporte['descripcion'] ?>"
                                        data-filtros="<?= implode(',', $reporte['filtros']) ?>">
                                    <?= $reporte['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Descripción del reporte seleccionado -->
                    <div id="descripcion-reporte" class="alert alert-info" style="display: none;">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span id="texto-descripcion"></span>
                    </div>

                    <!-- Filtros dinámicos -->
                    <div id="panel-filtros" style="display: none;">
                        <h5 class="mb-3">
                            <i class="fas fa-filter mr-2"></i>
                            Filtros Disponibles
                        </h5>
                        <div id="filtros-dinamicos">
                            <!-- Los filtros se generarán dinámicamente aquí -->
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary btn-block" id="generar-reporte">
                                <i class="fas fa-play mr-2"></i>
                                Generar Reporte
                            </button>
                            <button type="button" class="btn btn-secondary btn-block" id="limpiar-filtros">
                                <i class="fas fa-eraser mr-2"></i>
                                Limpiar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de exportación -->
            <div class="card mt-4" id="panel-exportacion" style="display: none;">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-download mr-2"></i>
                        Exportar Reporte
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="btn btn-danger btn-block" id="exportar-pdf">
                                <i class="fas fa-file-pdf mr-2"></i>
                                PDF
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-success btn-block" id="exportar-excel">
                                <i class="fas fa-file-excel mr-2"></i>
                                Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de resultados -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">
                            <i class="fas fa-chart-line mr-2"></i>
                            Resultados del Reporte
                        </h4>
                        <div class="ml-auto">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="actualizar-reporte">
                                <i class="fas fa-sync-alt mr-1"></i>
                                Actualizar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Estado inicial -->
                    <div id="estado-inicial" class="text-center py-5">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Selecciona un reporte para comenzar</h5>
                        <p class="text-muted">Elige un tipo de reporte del panel izquierdo y configura los filtros según tus necesidades.</p>
                    </div>

                    <!-- Loading -->
                    <div id="loading-reporte" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Generando reporte...</span>
                        </div>
                        <p class="mt-3 text-muted">Generando reporte...</p>
                    </div>

                    <!-- Contenido del reporte -->
                    <div id="contenido-reporte" style="display: none;">
                        <!-- Aquí se cargará el contenido del reporte -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para vista detallada -->
    <div class="modal fade" id="modal-detalle" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye mr-2"></i>
                        Vista Detallada
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="contenido-detalle">
                        <!-- Contenido detallado -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="exportar-detalle">
                        <i class="fas fa-download mr-2"></i>
                        Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts específicos para reportes se cargan al final -->

<!-- Estilos específicos para reportes -->
<style>
.card-stats .icon-big {
    font-size: 2.5rem;
    min-height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-success { color: #28a745; }
.icon-primary { color: #007bff; }
.icon-info { color: #17a2b8; }
.icon-warning { color: #ffc107; }

#filtros-dinamicos .form-group {
    margin-bottom: 1rem;
}

#filtros-dinamicos label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #495057;
}

.btn-block {
    width: 100%;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

.modal-xl {
    max-width: 95%;
}

/* Animaciones */
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.btn {
    transition: all 0.2s ease-in-out;
}

/* Responsive */
@media (max-width: 768px) {
    .col-md-4, .col-md-8 {
        margin-bottom: 1rem;
    }
    
    .card-stats .col-5 {
        display: none;
    }
    
    .card-stats .col-7 {
        width: 100%;
    }
}
</style>

<?= $this->include('Layouts/footer') ?>

<!-- Scripts específicos para reportes - Cargados después del footer -->
<script>
$(document).ready(function() {
    let reporteActual = null;
    let filtrosActuales = {};

    // Datos de filtros base
    const filtrosBase = <?= json_encode($filtros_base) ?>;
    const reportesDisponibles = <?= json_encode($reportes_disponibles) ?>;

    // Manejar selección de reporte
    $('#tipo-reporte').on('change', function() {
        const tipoReporte = $(this).val();
        
        if (tipoReporte) {
            reporteActual = tipoReporte;
            const reporte = reportesDisponibles[tipoReporte];
            
            // Mostrar descripción
            $('#texto-descripcion').text(reporte.descripcion);
            $('#descripcion-reporte').show();
            
            // Generar filtros dinámicos
            generarFiltrosDinamicos(reporte.filtros);
            $('#panel-filtros').show();
            $('#panel-exportacion').show();
            
            // Limpiar resultados anteriores
            limpiarResultados();
        } else {
            limpiarTodo();
        }
    });

    // Función para generar filtros dinámicos
    function generarFiltrosDinamicos(filtrosRequeridos) {
        const container = $('#filtros-dinamicos');
        container.empty();
        filtrosActuales = {};

        filtrosRequeridos.forEach(function(filtroKey) {
            const filtro = filtrosBase[filtroKey];
            if (filtro) {
                const div = $('<div class="form-group"></div>');
                
                // Label
                const label = $('<label></label>').text(filtro.label);
                div.append(label);

                // Input según el tipo
                let input;
                if (filtro.tipo === 'select') {
                    input = $('<select class="form-control"></select>');
                    input.attr('name', filtroKey);
                    
                    // Opciones
                    Object.keys(filtro.opciones).forEach(function(key) {
                        const option = $('<option></option>')
                            .val(key)
                            .text(filtro.opciones[key]);
                        input.append(option);
                    });
                } else if (filtro.tipo === 'date') {
                    input = $('<input type="date" class="form-control">');
                    input.attr('name', filtroKey);
                    input.attr('placeholder', filtro.placeholder);
                } else {
                    input = $('<input type="text" class="form-control">');
                    input.attr('name', filtroKey);
                    input.attr('placeholder', filtro.placeholder);
                }

                div.append(input);
                container.append(div);

                // Manejar cambios en filtros
                input.on('change', function() {
                    filtrosActuales[filtroKey] = $(this).val();
                    actualizarContadorFiltros();
                });
            }
        });

        actualizarContadorFiltros();
    }

    // Actualizar contador de filtros activos
    function actualizarContadorFiltros() {
        const filtrosActivos = Object.values(filtrosActuales).filter(val => val && val !== 'todos' && val !== '').length;
        $('#filtros-activos').text(filtrosActivos);
    }

    // Generar reporte
    $('#generar-reporte').on('click', function() {
        if (!reporteActual) {
            Swal.fire('Error', 'Por favor selecciona un tipo de reporte', 'error');
            return;
        }

        mostrarLoading();
        
        // Recopilar filtros
        const filtros = {};
        $('#filtros-dinamicos input, #filtros-dinamicos select').each(function() {
            const valor = $(this).val();
            if (valor && valor !== 'todos') {
                filtros[$(this).attr('name')] = valor;
            }
        });

        // Enviar petición AJAX
        $.ajax({
            url: '<?= base_url("reportes/generar") ?>',
            method: 'POST',
            data: {
                tipo_reporte: reporteActual,
                filtros: filtros,
                formato: 'html',
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                $('#contenido-reporte').html(response);
                $('#contenido-reporte').show();
                $('#loading-reporte').hide();
                $('#estado-inicial').hide();
            },
            error: function(xhr) {
                $('#loading-reporte').hide();
                Swal.fire('Error', 'Error al generar el reporte', 'error');
                console.error(xhr);
            }
        });
    });

    // Limpiar filtros
    $('#limpiar-filtros').on('click', function() {
        $('#filtros-dinamicos input, #filtros-dinamicos select').val('');
        filtrosActuales = {};
        actualizarContadorFiltros();
    });

    // Actualizar reporte
    $('#actualizar-reporte').on('click', function() {
        if (reporteActual) {
            $('#generar-reporte').click();
        }
    });

    // Exportar PDF
    $('#exportar-pdf').on('click', function() {
        if (!reporteActual) {
            Swal.fire('Error', 'Por favor genera un reporte primero', 'error');
            return;
        }

        const filtros = {};
        $('#filtros-dinamicos input, #filtros-dinamicos select').each(function() {
            const valor = $(this).val();
            if (valor && valor !== 'todos') {
                filtros[$(this).attr('name')] = valor;
            }
        });

        // Crear formulario temporal para descarga
        const form = $('<form method="POST" action="<?= base_url("reportes/exportarPDF") ?>"></form>');
        form.append($('<input type="hidden" name="tipo_reporte">').val(reporteActual));
        form.append($('<input type="hidden" name="filtros">').val(JSON.stringify(filtros)));
        form.append($('<input type="hidden" name="<?= csrf_token() ?>">').val('<?= csrf_hash() ?>'));
        $('body').append(form);
        form.submit();
        form.remove();
    });

    // Exportar Excel
    $('#exportar-excel').on('click', function() {
        if (!reporteActual) {
            Swal.fire('Error', 'Por favor genera un reporte primero', 'error');
            return;
        }

        const filtros = {};
        $('#filtros-dinamicos input, #filtros-dinamicos select').each(function() {
            const valor = $(this).val();
            if (valor && valor !== 'todos') {
                filtros[$(this).attr('name')] = valor;
            }
        });

        // Crear formulario temporal para descarga
        const form = $('<form method="POST" action="<?= base_url("reportes/exportarExcel") ?>"></form>');
        form.append($('<input type="hidden" name="tipo_reporte">').val(reporteActual));
        form.append($('<input type="hidden" name="filtros">').val(JSON.stringify(filtros)));
        form.append($('<input type="hidden" name="<?= csrf_token() ?>">').val('<?= csrf_hash() ?>'));
        $('body').append(form);
        form.submit();
        form.remove();
    });

    // Funciones auxiliares
    function mostrarLoading() {
        $('#loading-reporte').show();
        $('#contenido-reporte').hide();
        $('#estado-inicial').hide();
    }

    function limpiarResultados() {
        $('#contenido-reporte').hide();
        $('#estado-inicial').show();
    }

    function limpiarTodo() {
        $('#descripcion-reporte').hide();
        $('#panel-filtros').hide();
        $('#panel-exportacion').hide();
        $('#contenido-reporte').hide();
        $('#estado-inicial').show();
        $('#filtros-activos').text('0');
        reporteActual = null;
        filtrosActuales = {};
    }

    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    console.log('Sistema de reportes dinámicos inicializado correctamente');
});
</script>
