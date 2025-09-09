<?= $header ?>
<div class="page-inner">
    

    <!-- Mostrar mensajes de éxito/error -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x mr-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">¡Éxito!</h5>
                    <p class="mb-0"><?= session()->getFlashdata('success') ?></p>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-2x mr-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">¡Error!</h5>
                    <p class="mb-0"><?= session()->getFlashdata('error') ?></p>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Tarjetas de resumen -->
    <div class="row">
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Total Pagado</p>
                                <h4 class="card-title">S/ <?= number_format($estadisticas['total_pagado'], 2) ?></h4>
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
                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Deuda Total</p>
                                <h4 class="card-title">S/ <?= number_format($estadisticas['deuda_total'], 2) ?></h4>
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
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-receipt"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Pagos Registrados</p>
                                <h4 class="card-title"><?= $estadisticas['pagos_count'] ?></h4>
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
                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fas fa-handshake"></i>
                            </div>
                        </div>
                        <div class="col-7 col-stats">
                            <div class="numbers">
                                <p class="card-category">Contratos Pagados</p>
                                <h4 class="card-title"><?= $estadisticas['contratos_pagados'] ?> / <?= $estadisticas['contratos_pagados'] + $estadisticas['contratos_con_deuda'] ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel principal de pagos -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Registros de Pagos</h4>
                        <a href="<?= base_url('/controlpagos/crear') ?>" class="btn btn-primary btn-round ml-auto btn-animate">
                            <span class="btn-label">
                                <i class="fa fa-plus"></i>
                            </span>
                            Nuevo Pago
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filtro_contrato">Filtrar por Contrato</label>
                                <select class="form-control select2" id="filtro_contrato">
                                    <option value="">Todos los contratos</option>
                                    <?php 
                                    $contratosUnicos = [];
                                    foreach ($pagos as $pago) {
                                        if (!in_array($pago['idcontrato'], $contratosUnicos)) {
                                            $contratosUnicos[] = $pago['idcontrato'];
                                            echo '<option value="'.$pago['idcontrato'].'">Contrato #'.$pago['idcontrato'].'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filtro_estado">Filtrar por Estado</label>
                                <select class="form-control select2" id="filtro_estado">
                                    <option value="">Todos los estados</option>
                                    <option value="completo">Pago Completo</option>
                                    <option value="pendiente">Pago Pendiente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filtro_fecha_desde">Fecha Desde</label>
                                <input type="date" class="form-control" id="filtro_fecha_desde">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filtro_fecha_hasta">Fecha Hasta</label>
                                <input type="date" class="form-control" id="filtro_fecha_hasta">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="pagos-table" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Contrato</th>
                                    <th>Cliente</th>
                                    <th>Saldo (S/)</th>
                                    <th>Amortización (S/)</th>
                                    <th>Deuda (S/)</th>
                                    <th>Tipo Pago</th>
                                    <th>Transacción</th>
                                    <th>Fecha/Hora</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pagos) && is_array($pagos)): ?>
                                    <?php foreach ($pagos as $pago): ?>
                                        <tr>
                                            <td><?= $pago['idpagos'] ?></td>
                                            <td>Contrato #<?= $pago['idcontrato'] ?></td>
                                            <td>
                                                <?= !empty($pago['nombres']) ? 
                                                    $pago['nombres'] . ' ' . $pago['apellidos'] : 
                                                    $pago['razonsocial'] ?>
                                            </td>
                                            <td class="<?= $pago['saldo'] > 0 ? 'text-danger' : 'text-success' ?>">
                                                <span class="badge badge-<?= $pago['saldo'] > 0 ? 'danger' : 'success' ?>">
                                                    <?= number_format($pago['saldo'], 2) ?>
                                                </span>
                                            </td>
                                            <td class="text-success">
                                                <span class="badge badge-success">
                                                    <?= number_format($pago['amortizacion'], 2) ?>
                                                </span>
                                            </td>
                                            <td class="<?= $pago['deuda'] > 0 ? 'text-warning' : 'text-success' ?>">
                                                <span class="badge badge-<?= $pago['deuda'] > 0 ? 'warning' : 'success' ?>">
                                                    <?= number_format($pago['deuda'], 2) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><?= $pago['tipopago'] ?></span>
                                            </td>
                                            <td><?= $pago['numtransaccion'] ?? 'N/A' ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($pago['fechahora'])) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('/controlpagos/ver/' . $pago['idpagos']) ?>" 
                                                       class="btn btn-sm btn-info btn-detalle" title="Ver detalles">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-sm btn-secondary btn-print" title="Imprimir recibo" data-id="<?= $pago['idpagos'] ?>">
                                                        <i class="fa fa-print"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <i class="fas fa-receipt fa-3x text-muted mb-2"></i>
                                            <p class="text-muted">No hay registros de pagos</p>
                                            <a href="<?= base_url('/controlpagos/crear') ?>" class="btn btn-primary mt-2">
                                                <i class="fa fa-plus"></i> Registrar primer pago
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inicializar DataTable con más opciones
        var table = $('#pagos-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
            },
            "pageLength": 10,
            "order": [[0, "desc"]],
            "responsive": true,
            "dom": '<"top"<"row"<"col-md-6"l><"col-md-6"f>>>rt<"bottom"<"row"<"col-md-6"i><"col-md-6"p>>><"clear">',
            "initComplete": function() {
                // Aplicar filtros personalizados
                this.api().columns().every(function() {
                    var column = this;
                    // Solo aplicar a columnas específicas si es necesario
                });
            }
        });
        
        // Aplicar filtros personalizados
        $('#filtro_contrato, #filtro_estado, #filtro_fecha_desde, #filtro_fecha_hasta').on('change', function() {
            table.draw();
        });
        
        // Configurar filtros para DataTable
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var contrato = $('#filtro_contrato').val();
                var estado = $('#filtro_estado').val();
                var fechaDesde = $('#filtro_fecha_desde').val();
                var fechaHasta = $('#filtro_fecha_hasta').val();
                
                // Filtrar por contrato
                if (contrato !== '' && data[1] !== 'Contrato #'+contrato) {
                    return false;
                }
                
                // Filtrar por estado
                if (estado !== '') {
                    if (estado === 'completo' && parseFloat(data[5]) !== 0) {
                        return false;
                    }
                    if (estado === 'pendiente' && parseFloat(data[5]) === 0) {
                        return false;
                    }
                }
                
                // Filtrar por fecha
                if (fechaDesde || fechaHasta) {
                    var fechaPago = data[8].split(' ')[0].split('/').reverse().join('-');
                    
                    if (fechaDesde && fechaPago < fechaDesde) {
                        return false;
                    }
                    
                    if (fechaHasta && fechaPago > fechaHasta) {
                        return false;
                    }
                }
                
                return true;
            }
        );
        
        // Animación para botones de detalles
        $('.btn-detalle').on('mouseenter', function() {
            $(this).transition({ scale: 1.1 });
        }).on('mouseleave', function() {
            $(this).transition({ scale: 1 });
        });
        
        // Función para imprimir recibo
        $('.btn-print').on('click', function(e) {
            e.preventDefault();
            var idPago = $(this).data('id');
            window.open('<?= base_url('/controlpagos/recibo/') ?>' + idPago, '_blank');
        });
    });
</script>

<style>
    .card-stats .icon-big {
        font-size: 2.5rem;
        padding: 15px;
        border-radius: 50%;
    }
    
    .bubble-shadow-small {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .btn-animate {
        transition: all 0.3s ease;
    }
    
    .btn-animate:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 14px rgba(0,0,0,0.1);
    }
    
    .badge {
        font-size: 0.85em;
        padding: 0.5em 0.8em;
    }
    
    #pagos-table tbody tr {
        transition: all 0.3s ease;
    }
    
    #pagos-table tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
</style>

<?= $footer ?>