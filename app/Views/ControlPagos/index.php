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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filtro_contrato">Filtrar por Contrato</label>
                                <select class="form-control select2" id="filtro_contrato">
                                    <option value="">Todos los contratos</option>
                                    <?php if (!empty($contratos)): ?>
                                        <?php foreach ($contratos as $contrato): ?>
                                            <option value="<?= $contrato['idcontrato'] ?>">
                                                Contrato #<?= $contrato['idcontrato'] ?> - 
                                                <?= !empty($contrato['nombres']) ? 
                                                    $contrato['nombres'] . ' ' . $contrato['apellidos'] : 
                                                    $contrato['razonsocial'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filtro_estado">Filtrar por Estado</label>
                                <select class="form-control select2" id="filtro_estado">
                                    <option value="">Todos los estados</option>
                                    <option value="completo">Pago Completo</option>
                                    <option value="pendiente">Pago Pendiente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filtro_fecha">Filtrar por Fecha</label>
                                <input type="month" class="form-control" id="filtro_fecha">
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
                                    <th>Fecha/Hora</th>
                                    <th>Saldo (S/)</th>
                                    <th>Amortización (S/)</th>
                                    <th>Deuda (S/)</th>
                                    <th>Tipo Pago</th>
                                    <th>Usuario</th>
                                    <th>Comprobante</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pagos) && is_array($pagos)): ?>
                                    <?php foreach ($pagos as $pago): ?>
                                        <tr>
                                            <td><?= $pago['idpagos'] ?></td>
                                            <td>
                                                <a href="<?= base_url('/controlpagos/por-contrato/' . $pago['idcontrato']) ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    Contrato #<?= $pago['idcontrato'] ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?= !empty($pago['nombres']) ? 
                                                    $pago['nombres'] . ' ' . $pago['apellidos'] : 
                                                    $pago['razonsocial'] ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($pago['fechahora'])) ?></td>
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
                                            <td><?= $pago['nombreusuario'] ?? 'N/A' ?></td>
                                            <td>
                                                <?php if (!empty($pago['comprobante'])): ?>
                                                    <span class="badge badge-success">Sí</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('/controlpagos/ver/' . $pago['idpagos']) ?>" 
                                                       class="btn btn-sm btn-info btn-detalle" title="Ver detalles">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <?php if (!empty($pago['comprobante'])): ?>
                                                        <a href="<?= base_url('/controlpagos/descargarComprobante/' . $pago['idpagos']) ?>" 
                                                           class="btn btn-sm btn-secondary" title="Descargar comprobante">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
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
        $('#filtro_contrato, #filtro_estado, #filtro_fecha').on('change', function() {
            table.draw();
        });
        
        // Configurar filtros para DataTable
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var contrato = $('#filtro_contrato').val();
                var estado = $('#filtro_estado').val();
                var fecha = $('#filtro_fecha').val();
                
                // Filtrar por contrato
                if (contrato !== '') {
                    var contratoData = data[1].match(/Contrato #(\d+)/);
                    if (!contratoData || contratoData[1] !== contrato) {
                        return false;
                    }
                }
                
                // Filtrar por estado
                if (estado !== '') {
                    var deuda = parseFloat(data[6].replace(/[^\d.,]/g, '').replace(',', ''));
                    if (estado === 'completo' && deuda !== 0) {
                        return false;
                    }
                    if (estado === 'pendiente' && deuda === 0) {
                        return false;
                    }
                }
                
                // Filtrar por fecha
                if (fecha) {
                    var fechaPago = data[3].split(' ')[0].split('/').reverse().join('-');
                    var fechaFiltro = fecha + '-01';
                    
                    var fechaPagoObj = new Date(fechaPago);
                    var fechaFiltroObj = new Date(fechaFiltro);
                    
                    if (fechaPagoObj.getMonth() !== fechaFiltroObj.getMonth() || 
                        fechaPagoObj.getFullYear() !== fechaFiltroObj.getFullYear()) {
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