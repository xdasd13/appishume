<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ControlPagos-index.css') ?>">
<div class="page-inner">
    <!-- Notificaciones con SweetAlert2 -->
    <?php if (session()->getFlashdata('success')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '<?= addslashes(session()->getFlashdata('success')) ?>',
                    confirmButtonColor: '#28a745',
                    timer: 4000,
                    timerProgressBar: true
                });
            });
        </script>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '<?= addslashes(session()->getFlashdata('error')) ?>',
                    confirmButtonColor: '#dc3545'
                });
            });
        </script>
    <?php endif; ?>

    <!-- Tarjetas de resumen con mejoras visuales -->
    <div class="row">
        <div class="col-md-3">
            <div class="card card-stats card-round card-3d animate__animated animate__fadeInLeft card-hover">
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
            <div class="card card-stats card-round card-3d animate__animated animate__fadeInLeft animate__delay-1s card-hover">
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
            <div class="card card-stats card-round card-3d animate__animated animate__fadeInRight animate__delay-1s card-hover">
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
            <div class="card card-stats card-round card-3d animate__animated animate__fadeInRight card-hover">
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
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card-3d animate__animated animate__fadeInUp">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Contratos y Entregas</h4>
                        <a href="<?= base_url('controlpagos/crear') ?>" class="btn btn-primary btn-round ml-auto">
                            <i class="fas fa-plus mr-2"></i>Nuevo Pago
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros originales funcionando -->
                    <form method="get" action="<?= base_url('/controlpagos') ?>" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filtro_contrato">Filtrar por Contrato</label>
                                    <select class="form-control select2" id="filtro_contrato" name="filtro_contrato">
                                        <option value="">Todos los contratos</option>
                                        <?php if (!empty($contratos)): ?>
                                            <?php foreach ($contratos as $contrato): ?>
                                                <option value="<?= $contrato['idcontrato'] ?>" <?= ($filtro_contrato == $contrato['idcontrato']) ? 'selected' : '' ?>>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filtro_estado">Filtrar por Estado</label>
                                    <select class="form-control select2" id="filtro_estado" name="filtro_estado">
                                        <option value="">Todos los estados</option>
                                        <option value="completo" <?= ($filtro_estado == 'completo') ? 'selected' : '' ?>>Pago Completo</option>
                                        <option value="pendiente" <?= ($filtro_estado == 'pendiente') ? 'selected' : '' ?>>Pago Pendiente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filtro_fecha">Filtrar por Mes/Año</label>
                                    <input type="month" class="form-control" id="filtro_fecha" name="filtro_fecha" value="<?= $filtro_fecha ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label style="opacity: 0;">Aplicar Filtros</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-sm btn-hover">Aplicar Filtros</button>
                                        <a href="<?= base_url('/controlpagos') ?>" class="btn btn-secondary btn-sm btn-hover">Limpiar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="pagos-table" class="display table table-striped table-hover table-hover-3d">
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
                                        <tr class="animate__animated animate__fadeIn row-hover">
                                            <td>
                                                <span class="badge badge-dark badge-3d" style="background-color: #2c3e50 !important; color: white !important;">
                                                    #<?= $pago['idpagos'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('/controlpagos/por-contrato/' . $pago['idcontrato']) ?>" 
                                                   class="btn btn-sm btn-outline-primary btn-hover">
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
                                                <span class="badge badge-<?= $pago['saldo'] > 0 ? 'danger' : 'success' ?> badge-3d">
                                                    <?= number_format($pago['saldo'], 2) ?>
                                                </span>
                                            </td>
                                            <td class="text-success">
                                                <span class="badge badge-success badge-3d">
                                                    <?= number_format($pago['amortizacion'], 2) ?>
                                                </span>
                                            </td>
                                            <td class="<?= $pago['deuda'] > 0 ? 'text-warning' : 'text-success' ?>">
                                                <span class="badge badge-<?= $pago['deuda'] > 0 ? 'warning' : 'success' ?> badge-3d">
                                                    <?= number_format($pago['deuda'], 2) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info badge-3d"><?= $pago['tipopago'] ?></span>
                                            </td>
                                            <td><?= $pago['nombreusuario'] ?? 'N/A' ?></td>
                                            <td>
                                                <?php if (!empty($pago['comprobante'])): ?>
                                                    <a href="<?= base_url('/controlpagos/descargarComprobante/' . $pago['idpagos']) ?>" 
                                                       class="btn btn-sm btn-outline-info btn-hover" title="Descargar comprobante">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary badge-3d">Sin comprobante</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="form-button-action">
                                                    <a href="<?= base_url('/controlpagos/ver/' . $pago['idpagos']) ?>" 
                                                       class="btn btn-link btn-primary btn-lg btn-action btn-hover" 
                                                       data-toggle="tooltip" 
                                                       title="Ver detalles">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('/controlpagos/generarVoucher/' . $pago['idpagos']) ?>" 
                                                       class="btn btn-link btn-info btn-lg btn-action btn-hover" 
                                                       data-toggle="tooltip" 
                                                       title="Generar voucher"
                                                       target="_blank">
                                                        <i class="fas fa-receipt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="11" class="text-center">No se encontraron registros de pagos</td>
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

<?= $footer ?>

<!-- Estilos CSS para mejoras visuales -->


<!-- Scripts para la funcionalidad de la página -->
<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#pagos-table').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
        },
        "order": [[0, "desc"]],
        "responsive": true,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "dom": '<"top"lf>rt<"bottom"ip><"clear">'
    });

    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Inicializar select2
    $('.select2').select2({
        theme: "bootstrap"
    });

    // Agregar confirmación SweetAlert para descargas de comprobantes
    $('a[href*="descargarComprobante"]').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        
        Swal.fire({
            title: 'Descargar Comprobante',
            text: '¿Deseas descargar el comprobante de pago?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, descargar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar notificación de descarga iniciada
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                
                Toast.fire({
                    icon: 'info',
                    title: 'Descarga iniciada'
                });
                
                // Proceder con la descarga
                window.open(url, '_blank');
            }
        });
    });

    // Agregar confirmación SweetAlert para generar vouchers
    $('a[href*="generarVoucher"]').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        
        Swal.fire({
            title: 'Generar Voucher',
            text: '¿Deseas generar el voucher de pago?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Sí, generar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar notificación de generación
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                
                Toast.fire({
                    icon: 'success',
                    title: 'Generando voucher...'
                });
                
                // Abrir voucher en nueva ventana
                window.open(url, '_blank');
            }
        });
    });
});
</script>