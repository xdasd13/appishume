<?php
// Esta vista se carga dinámicamente en el panel de resultados
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
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="verDetalle()">
                    <i class="fas fa-eye mr-1"></i>
                    Ver Detalle
                </button>
                <button type="button" class="btn btn-outline-success btn-sm" onclick="exportarReporte()">
                    <i class="fas fa-download mr-1"></i>
                    Exportar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas principales -->
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
    <div class="col-md-2 mb-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-5">
                        <div class="icon-big text-center icon-<?= $color ?>">
                            <i class="<?= $icono ?>"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category"><?= ucfirst(str_replace('_', ' ', $key)) ?></p>
                            <h4 class="card-title"><?= is_float($value) ? number_format($value, 2) : number_format($value) ?></h4>
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

<!-- Tabla de datos -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-table mr-2"></i>
            Datos Detallados
            <span class="badge badge-primary ml-2"><?= count($datos['datos']) ?> registros</span>
        </h5>
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

<!-- Scripts para la tabla -->
<script>
$(document).ready(function() {
    // Inicializar DataTable si hay datos
    <?php if (!empty($datos['datos'])): ?>
    $('#tabla-reporte').DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
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
        "order": [[0, "desc"]]
    });
    <?php endif; ?>
});

// Funciones globales
function verDetalle() {
    // Implementar vista detallada del reporte completo
    Swal.fire({
        title: 'Vista Detallada',
        html: 'Esta funcionalidad mostrará el reporte completo con más detalles y gráficos interactivos.',
        icon: 'info',
        confirmButtonText: 'Entendido'
    });
}

function exportarReporte() {
    // Implementar exportación
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

<!-- Estilos adicionales -->
<style>
.border-left-primary {
    border-left: 4px solid #007bff !important;
}

.card-stats .icon-big {
    font-size: 2rem;
}

.table th {
    background-color: #343a40;
    color: white;
    font-weight: 600;
    border: none;
}

.table td {
    vertical-align: middle;
}

.progress {
    border-radius: 10px;
}

.badge {
    font-size: 0.75em;
}

.report-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
}

.report-header h4 {
    color: white;
}

.report-header p {
    color: rgba(255,255,255,0.9);
}

.report-header small {
    color: rgba(255,255,255,0.8);
}

@media (max-width: 768px) {
    .report-header {
        padding: 1rem;
    }
    
    .report-header .col-md-4 {
        margin-top: 1rem;
        text-align: left !important;
    }
}
</style>
