<?php
// Esta vista se carga dinámicamente en el panel de resultados
?>

<!-- Header del reporte con animación -->
<div class="report-header mb-4 animate-slide-in">
    <div class="row align-items-center">
        <div class="col-md-6">
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
        <div class="col-md-6 text-right">
            <!-- Selector de vista -->
            <div class="btn-group mr-2" role="group">
                <button type="button" class="btn btn-outline-light btn-sm vista-btn active" data-vista="tabla">
                    <i class="fas fa-table"></i> Tabla
                </button>
                <button type="button" class="btn btn-outline-light btn-sm vista-btn" data-vista="graficos">
                    <i class="fas fa-chart-pie"></i> Gráficos
                </button>
                <button type="button" class="btn btn-outline-light btn-sm vista-btn" data-vista="tarjetas">
                    <i class="fas fa-th-large"></i> Tarjetas
                </button>
            </div>
            <!-- Botones de acción -->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-light btn-sm" onclick="verDetalle()">
                    <i class="fas fa-eye"></i>
                </button>
                <button type="button" class="btn btn-light btn-sm" onclick="exportarReporte()">
                    <i class="fas fa-download"></i>
                </button>
                <button type="button" class="btn btn-light btn-sm" onclick="imprimirReporte()">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas principales con animaciones -->
<?php if (isset($datos['estadisticas'])): ?>
<div class="row mb-4">
    <?php 
    $stats = $datos['estadisticas'];
    $colores = ['success', 'primary', 'info', 'warning', 'danger', 'secondary'];
    $iconos = ['fas fa-chart-bar', 'fas fa-dollar-sign', 'fas fa-users', 'fas fa-clock', 'fas fa-percentage', 'fas fa-trophy'];
    $contador = 0;
    
    foreach ($stats as $key => $value): 
        if (is_numeric($value) && $contador < 6):
            $color = $colores[$contador % count($colores)];
            $icono = $iconos[$contador % count($iconos)];
    ?>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3 animate-fade-in" style="animation-delay: <?= $contador * 0.1 ?>s;">
        <div class="card card-stats card-round stats-hover">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-5">
                        <div class="icon-big text-center icon-<?= $color ?> pulse-icon">
                            <i class="<?= $icono ?>"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category"><?= ucfirst(str_replace('_', ' ', $key)) ?></p>
                            <h4 class="card-title counter" data-target="<?= is_float($value) ? number_format($value, 2, '.', '') : $value ?>">0</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
            $contador++;
        endif;
    endforeach; ?>
</div>
<?php endif; ?>

<!-- Estadísticas por técnico (solo para reporte de equipos) -->
<?php if (isset($datos['estadisticas_por_tecnico'])): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-users-cog mr-2"></i>
                    Estadísticas por Técnico
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($datos['estadisticas_por_tecnico'] as $tecnico => $stats): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <h6 class="font-weight-bold"><?= $tecnico ?></h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <small class="text-muted">Total</small>
                                        <div class="h5 mb-0"><?= $stats['total_equipos'] ?></div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">Completados</small>
                                        <div class="h5 mb-0 text-success"><?= $stats['completados'] ?></div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">En Proceso</small>
                                        <div class="h5 mb-0 text-warning"><?= $stats['en_proceso'] ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Vista de Gráficos Interactivos -->
<div class="vista-graficos" style="display: none;">
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card card-round animate-fade-in">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line mr-2"></i>
                            Visualización Principal
                        </h5>
                        <div class="ml-auto">
                            <select class="form-control form-control-sm" id="tipo-grafico-principal">
                                <option value="bar">Barras</option>
                                <option value="line">Líneas</option>
                                <option value="pie">Circular</option>
                                <option value="doughnut">Dona</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="grafico-principal" style="height: 350px;"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card card-round animate-fade-in" style="animation-delay: 0.1s;">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie mr-2"></i>
                        Distribución
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="grafico-distribucion" style="height: 350px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-round animate-fade-in" style="animation-delay: 0.2s;">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area mr-2"></i>
                        Tendencias y Comparativos
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="grafico-tendencias" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vista de Tarjetas -->
<div class="vista-tarjetas" style="display: none;">
    <div class="row mb-4" id="contenedor-tarjetas">
        <!-- Las tarjetas se generarán dinámicamente aquí -->
    </div>
</div>

<!-- Tabla de datos -->
<div class="vista-tabla">
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-table mr-2"></i>
                Datos Detallados
                <span class="badge badge-primary ml-2"><?= count($datos['datos']) ?> registros</span>
            </h5>
            <div class="ml-auto">
                <input type="text" class="form-control form-control-sm" id="busqueda-tabla" placeholder="🔍 Buscar en la tabla...">
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($datos['datos'])): ?>
        <div class="text-center py-4">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No hay datos disponibles</h5>
            <p class="text-muted">Intenta ajustar los filtros para obtener resultados.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tabla-reporte">
                <thead class="thead-dark">
                    <tr>
                        <?php 
                        // Generar encabezados dinámicamente
                        $primerRegistro = $datos['datos'][0];
                        foreach (array_keys($primerRegistro) as $campo): 
                            $label = ucfirst(str_replace('_', ' ', $campo));
                            // Mapear campos a labels más amigables
                            $labels = [
                                'idcontrato' => 'ID Contrato',
                                'cliente' => 'Cliente',
                                'cliente_completo' => 'Cliente Completo',
                                'tipo_evento' => 'Tipo Evento',
                                'fechaevento' => 'Fecha Evento',
                                'monto_total' => 'Monto Total',
                                'deuda_actual' => 'Deuda Actual',
                                'total_pagado' => 'Total Pagado',
                                'estado_pago' => 'Estado Pago',
                                'identregable' => 'ID Entrega',
                                'fechahoraentrega' => 'Fecha Entrega',
                                'fecha_real_entrega' => 'Fecha Real',
                                'estado' => 'Estado',
                                'observaciones' => 'Observaciones',
                                'servicio' => 'Servicio',
                                'direccion' => 'Dirección',
                                'estado_visual' => 'Estado Visual',
                                'idequipo' => 'ID Equipo',
                                'descripcion' => 'Descripción',
                                'estadoservicio' => 'Estado Servicio',
                                'fecha_asignacion' => 'Fecha Asignación',
                                'tecnico' => 'Técnico',
                                'cargo' => 'Cargo',
                                'fechahoraservicio' => 'Fecha Servicio',
                                'idcliente' => 'ID Cliente',
                                'tipo_cliente' => 'Tipo Cliente',
                                'documento' => 'Documento',
                                'telefono' => 'Teléfono',
                                'total_contratos' => 'Total Contratos',
                                'monto_total_contratado' => 'Monto Total',
                                'contratos_pagados' => 'Contratos Pagados',
                                'contratos_con_deuda' => 'Con Deuda',
                                'idserviciocontratado' => 'ID Servicio',
                                'estado_proyecto' => 'Estado Proyecto',
                                'responsable' => 'Responsable',
                                'cargo_responsable' => 'Cargo',
                                'progreso_porcentaje' => 'Progreso %',
                                'precioregular' => 'Precio Regular',
                                'precio_contratado' => 'Precio Contratado',
                                'diferencia_precio' => 'Diferencia',
                                'margen_porcentaje' => 'Margen %',
                                'cantidad_contratada' => 'Cantidad',
                                'ingresos_totales' => 'Ingresos',
                                'costo_base_total' => 'Costo Base',
                                'ganancia_total' => 'Ganancia',
                                'clientes_unicos' => 'Clientes Únicos'
                            ];
                            $label = $labels[$campo] ?? $label;
                        ?>
                        <th><?= $label ?></th>
                        <?php endforeach; ?>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos['datos'] as $registro): ?>
                    <tr>
                        <?php foreach ($registro as $campo => $valor): ?>
                        <td>
                            <?php 
                            // Formatear valores según el tipo de campo
                            if (strpos($campo, 'fecha') !== false && $valor): 
                                echo date('d/m/Y', strtotime($valor));
                            elseif (strpos($campo, 'monto') !== false || strpos($campo, 'precio') !== false || strpos($campo, 'ganancia') !== false || strpos($campo, 'ingreso') !== false || strpos($campo, 'costo') !== false || strpos($campo, 'deuda') !== false || strpos($campo, 'total') !== false):
                                echo 'S/ ' . number_format($valor, 2);
                            elseif ($campo === 'estado_pago'):
                                $badgeClass = $valor === 'Pagado' ? 'badge-success' : 'badge-warning';
                                echo '<span class="badge ' . $badgeClass . '">' . $valor . '</span>';
                            elseif ($campo === 'estado' || $campo === 'estado_visual' || $campo === 'estadoservicio' || $campo === 'estado_proyecto'):
                                $badgeClass = 'badge-secondary';
                                if ($valor === 'completada' || $valor === 'Completado' || $valor === 'Entregado') $badgeClass = 'badge-success';
                                elseif ($valor === 'pendiente' || $valor === 'Pendiente') $badgeClass = 'badge-warning';
                                elseif ($valor === 'En Proceso') $badgeClass = 'badge-info';
                                echo '<span class="badge ' . $badgeClass . '">' . $valor . '</span>';
                            elseif ($campo === 'progreso_porcentaje'):
                                $colorClass = $valor >= 80 ? 'success' : ($valor >= 50 ? 'warning' : 'danger');
                                echo '<div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-' . $colorClass . '" role="progressbar" style="width: ' . $valor . '%" aria-valuenow="' . $valor . '" aria-valuemin="0" aria-valuemax="100">
                                            ' . $valor . '%
                                        </div>
                                      </div>';
                            elseif ($campo === 'margen_porcentaje'):
                                $colorClass = $valor >= 0 ? 'text-success' : 'text-danger';
                                echo '<span class="' . $colorClass . '">' . $valor . '%</span>';
                            elseif (strpos($campo, 'telefono') !== false && $valor):
                                echo '<a href="tel:' . $valor . '">' . $valor . '</a>';
                            else:
                                echo htmlspecialchars($valor);
                            endif;
                            ?>
                        </td>
                        <?php endforeach; ?>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="verDetalleRegistro('<?= json_encode($registro) ?>')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>
<!-- Fin vista-tabla -->

<!-- Script para incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<!-- Scripts para reportes interactivos -->
<script>
// Datos del reporte para gráficos
const datosReporte = <?= json_encode($datos['datos'] ?? []) ?>;
const estadisticasReporte = <?= json_encode($datos['estadisticas'] ?? []) ?>;
const tipoReporte = '<?= $tipo_reporte ?>';

// Variables globales para gráficos
let graficoPrincipal = null;
let graficoDistribucion = null;
let graficoTendencias = null;
let tablaReporte = null;

$(document).ready(function() {
    // Animación de contadores
    animarContadores();
    
    // Inicializar DataTable si hay datos
    <?php if (!empty($datos['datos'])): ?>
    tablaReporte = $('#tabla-reporte').DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        "responsive": true,
        "pageLength": 25,
        "order": [[0, "desc"]],
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6">>rtip'
    });
    
    // Búsqueda personalizada
    $('#busqueda-tabla').on('keyup', function() {
        tablaReporte.search(this.value).draw();
    });
    <?php endif; ?>
    
    // Inicializar gráficos
    inicializarGraficos();
    
    // Manejar cambio de vista
    $('.vista-btn').on('click', function() {
        const vista = $(this).data('vista');
        cambiarVista(vista);
        
        // Actualizar botones activos
        $('.vista-btn').removeClass('active');
        $(this).addClass('active');
    });
    
    // Cambio de tipo de gráfico principal
    $('#tipo-grafico-principal').on('change', function() {
        actualizarGraficoPrincipal($(this).val());
    });
    
    // Generar tarjetas si es necesario
    generarTarjetas();
});

// ==================== FUNCIONES DE ANIMACIÓN ====================
function animarContadores() {
    $('.counter').each(function() {
        const $this = $(this);
        const target = parseFloat($this.data('target'));
        const isDecimal = target % 1 !== 0;
        
        $({count: 0}).animate({count: target}, {
            duration: 2000,
            easing: 'swing',
            step: function() {
                $this.text(isDecimal ? this.count.toFixed(2) : Math.ceil(this.count).toLocaleString());
            },
            complete: function() {
                $this.text(isDecimal ? target.toFixed(2) : target.toLocaleString());
            }
        });
    });
}

// ==================== FUNCIONES DE VISTA ====================
function cambiarVista(vista) {
    // Ocultar todas las vistas
    $('.vista-tabla, .vista-graficos, .vista-tarjetas').hide();
    
    // Mostrar vista seleccionada con animación
    $('.vista-' + vista).fadeIn(400);
    
    // Si se cambia a gráficos, redibujar
    if (vista === 'graficos') {
        setTimeout(() => {
            if (graficoPrincipal) graficoPrincipal.resize();
            if (graficoDistribucion) graficoDistribucion.resize();
            if (graficoTendencias) graficoTendencias.resize();
        }, 100);
    }
}

// ==================== FUNCIONES DE GRÁFICOS ====================
function inicializarGraficos() {
    if (datosReporte.length === 0) return;
    
    // Preparar datos según el tipo de reporte
    const datosGraficos = prepararDatosGraficos();
    
    // Gráfico Principal
    crearGraficoPrincipal(datosGraficos);
    
    // Gráfico de Distribución
    crearGraficoDistribucion(datosGraficos);
    
    // Gráfico de Tendencias
    crearGraficoTendencias(datosGraficos);
}

function prepararDatosGraficos() {
    const labels = [];
    const valores = [];
    const valores2 = [];
    
    // Limitar a los primeros 10 registros para mejor visualización
    const datosLimitados = datosReporte.slice(0, 10);
    
    datosLimitados.forEach((dato, index) => {
        // Determinar qué campos usar según el tipo de reporte
        if (tipoReporte === 'financiero') {
            labels.push(dato.cliente || `Registro ${index + 1}`);
            valores.push(parseFloat(dato.monto_total) || 0);
            valores2.push(parseFloat(dato.deuda_actual) || 0);
        } else if (tipoReporte === 'entregas') {
            labels.push(dato.cliente || `Registro ${index + 1}`);
            valores.push(dato.estado_visual === 'Entregado' ? 1 : 0);
        } else if (tipoReporte === 'equipos') {
            labels.push(dato.tecnico || `Registro ${index + 1}`);
            valores.push(1); // Contar equipos
        } else if (tipoReporte === 'clientes') {
            labels.push(dato.cliente || `Registro ${index + 1}`);
            valores.push(parseInt(dato.total_contratos) || 0);
            valores2.push(parseFloat(dato.monto_total_contratado) || 0);
        } else if (tipoReporte === 'rentabilidad') {
            labels.push(dato.servicio || `Registro ${index + 1}`);
            valores.push(parseFloat(dato.ganancia_total) || 0);
            valores2.push(parseFloat(dato.margen_porcentaje) || 0);
        } else {
            labels.push(`Registro ${index + 1}`);
            valores.push(index + 1);
        }
    });
    
    return { labels, valores, valores2 };
}

function crearGraficoPrincipal(datos) {
    const ctx = document.getElementById('grafico-principal');
    if (!ctx) return;
    
    graficoPrincipal = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: datos.labels,
            datasets: [{
                label: obtenerLabelDataset1(),
                data: datos.valores,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                borderRadius: 8,
                barThickness: 'flex'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: { size: 12, family: 'Arial' },
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });
}

function crearGraficoDistribucion(datos) {
    const ctx = document.getElementById('grafico-distribucion');
    if (!ctx) return;
    
    // Preparar datos para gráfico circular
    const colores = generarColores(datos.labels.length);
    
    graficoDistribucion = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: datos.labels,
            datasets: [{
                data: datos.valores,
                backgroundColor: colores,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 11 },
                        padding: 10,
                        boxWidth: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    cornerRadius: 8
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1500
            }
        }
    });
}

function crearGraficoTendencias(datos) {
    const ctx = document.getElementById('grafico-tendencias');
    if (!ctx) return;
    
    const datasets = [{
        label: obtenerLabelDataset1(),
        data: datos.valores,
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointRadius: 5,
        pointHoverRadius: 7,
        pointBackgroundColor: 'rgba(75, 192, 192, 1)',
        pointBorderColor: '#fff',
        pointBorderWidth: 2
    }];
    
    // Agregar segundo dataset si hay datos
    if (datos.valores2 && datos.valores2.some(v => v > 0)) {
        datasets.push({
            label: obtenerLabelDataset2(),
            data: datos.valores2,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: 'rgba(255, 99, 132, 1)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        });
    }
    
    graficoTendencias = new Chart(ctx, {
        type: 'line',
        data: {
            labels: datos.labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });
}

function actualizarGraficoPrincipal(tipo) {
    if (!graficoPrincipal) return;
    
    graficoPrincipal.config.type = tipo;
    
    // Ajustar configuración según tipo
    if (tipo === 'pie' || tipo === 'doughnut') {
        graficoPrincipal.options.plugins.legend.position = 'bottom';
        const colores = generarColores(graficoPrincipal.data.labels.length);
        graficoPrincipal.data.datasets[0].backgroundColor = colores;
    } else {
        graficoPrincipal.options.plugins.legend.position = 'top';
        graficoPrincipal.data.datasets[0].backgroundColor = 'rgba(54, 162, 235, 0.6)';
    }
    
    graficoPrincipal.update();
}

function generarColores(cantidad) {
    const colores = [
        'rgba(255, 99, 132, 0.8)',
        'rgba(54, 162, 235, 0.8)',
        'rgba(255, 206, 86, 0.8)',
        'rgba(75, 192, 192, 0.8)',
        'rgba(153, 102, 255, 0.8)',
        'rgba(255, 159, 64, 0.8)',
        'rgba(199, 199, 199, 0.8)',
        'rgba(83, 102, 255, 0.8)',
        'rgba(255, 99, 255, 0.8)',
        'rgba(99, 255, 132, 0.8)'
    ];
    
    return colores.slice(0, cantidad);
}

function obtenerLabelDataset1() {
    const labels = {
        'financiero': 'Monto Total',
        'entregas': 'Entregas Completadas',
        'equipos': 'Equipos Asignados',
        'clientes': 'Total Contratos',
        'rentabilidad': 'Ganancia Total'
    };
    return labels[tipoReporte] || 'Valores';
}

function obtenerLabelDataset2() {
    const labels = {
        'financiero': 'Deuda Actual',
        'clientes': 'Monto Contratado',
        'rentabilidad': 'Margen %'
    };
    return labels[tipoReporte] || 'Valores 2';
}

// ==================== FUNCIONES DE TARJETAS ====================
function generarTarjetas() {
    const contenedor = $('#contenedor-tarjetas');
    contenedor.empty();
    
    if (datosReporte.length === 0) return;
    
    datosReporte.forEach((dato, index) => {
        const tarjeta = crearTarjeta(dato, index);
        contenedor.append(tarjeta);
    });
}

function crearTarjeta(dato, index) {
    let titulo, subtitulo, detalles;
    
    if (tipoReporte === 'financiero') {
        titulo = dato.cliente_completo || dato.cliente;
        subtitulo = dato.tipo_evento || 'Sin tipo';
        detalles = `
            <div class="mb-2"><strong>Monto:</strong> S/ ${parseFloat(dato.monto_total).toFixed(2)}</div>
            <div class="mb-2"><strong>Deuda:</strong> S/ ${parseFloat(dato.deuda_actual).toFixed(2)}</div>
            <div><span class="badge ${dato.estado_pago === 'Pagado' ? 'badge-success' : 'badge-warning'}">${dato.estado_pago}</span></div>
        `;
    } else if (tipoReporte === 'entregas') {
        titulo = dato.cliente;
        subtitulo = dato.servicio;
        detalles = `
            <div class="mb-2"><strong>Dirección:</strong> ${dato.direccion || 'N/A'}</div>
            <div class="mb-2"><strong>Fecha:</strong> ${formatearFecha(dato.fechahoraentrega)}</div>
            <div><span class="badge ${dato.estado_visual === 'Entregado' ? 'badge-success' : 'badge-warning'}">${dato.estado_visual}</span></div>
        `;
    } else if (tipoReporte === 'equipos') {
        titulo = dato.tecnico;
        subtitulo = dato.descripcion;
        detalles = `
            <div class="mb-2"><strong>Cliente:</strong> ${dato.cliente}</div>
            <div class="mb-2"><strong>Servicio:</strong> ${dato.servicio}</div>
            <div><span class="badge badge-info">${dato.estadoservicio}</span></div>
        `;
    } else {
        titulo = dato.cliente || `Registro ${index + 1}`;
        subtitulo = Object.keys(dato)[1] ? dato[Object.keys(dato)[1]] : '';
        detalles = '';
    }
    
    return $(`
        <div class="col-lg-4 col-md-6 mb-4 animate-fade-in" style="animation-delay: ${index * 0.05}s;">
            <div class="card card-hover h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">${titulo}</h5>
                            <p class="text-muted mb-0 small">${subtitulo}</p>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" onclick='verDetalleRegistro(${JSON.stringify(JSON.stringify(dato))})'>
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    ${detalles}
                </div>
            </div>
        </div>
    `);
}

function formatearFecha(fecha) {
    if (!fecha) return 'N/A';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-PE');
}

// ==================== FUNCIONES GLOBALES ====================
function verDetalle() {
    Swal.fire({
        title: 'Vista Detallada',
        html: 'Esta funcionalidad mostrará el reporte completo con más detalles y gráficos interactivos.',
        icon: 'info',
        confirmButtonText: 'Entendido'
    });
}

function exportarReporte() {
    Swal.fire({
        title: 'Exportar Reporte',
        html: 'Selecciona el formato de exportación:',
        showCancelButton: true,
        confirmButtonText: 'PDF',
        cancelButtonText: 'Excel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $('#exportar-pdf').click();
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            $('#exportar-excel').click();
        }
    });
}

function imprimirReporte() {
    window.print();
}

function verDetalleRegistro(registroJson) {
    const registro = JSON.parse(registroJson);
    let html = '<div class="row">';
    
    Object.keys(registro).forEach(function(key) {
        const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        html += '<div class="col-md-6 mb-2">';
        html += '<strong>' + label + ':</strong><br>';
        html += '<span class="text-muted">' + (registro[key] || 'N/A') + '</span>';
        html += '</div>';
    });
    
    html += '</div>';
    
    Swal.fire({
        title: 'Detalle del Registro',
        html: html,
        width: '60%',
        showCloseButton: true,
        showConfirmButton: false
    });
}
</script>

<!-- Estilos adicionales mejorados -->

