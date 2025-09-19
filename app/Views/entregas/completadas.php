<?= $header; ?>

<div class="container">
    <div class="page-inner">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title">Todas las Entregas Registradas</h4>
                            <div>
                                <a href="<?= base_url('entregas') ?>" class="btn btn-primary btn-round">
                                    <i class="fas fa-arrow-left mr-2"></i>Volver a Contratos
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (session('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <?= session('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if (session('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-2"></i>
                                <?= session('success') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Información completa:</strong> Aquí encontrará todas las entregas registradas con sus comprobantes, fechas y responsables para responder rápidamente a reclamos.
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="entregas-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>Fecha Entrega</th>
                                        <th>Responsable</th>
                                        <th>Estado</th>
                                        <th>Comprobante</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($entregas as $e): ?>
                                    <tr>
                                        <td><?= $e['identregable'] ?></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-bold"><?= $e['nombre_cliente'] ?> <?= $e['apellido_cliente'] ?></span>
                                                <span class="text-muted small">Contrato #<?= $e['idcontrato'] ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-bold"><?= $e['servicio'] ?? 'No disponible' ?></span>
                                                <span class="text-muted small">
                                                    <?= isset($e['fechahoraservicio']) ? date('d/m/Y', strtotime($e['fechahoraservicio'])) : 'No disponible' ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-bold"><?= date('d/m/Y', strtotime($e['fechahoraentrega'])) ?></span>
                                                <span class="text-muted small"><?= date('h:i A', strtotime($e['fechahoraentrega'])) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-primary text-white rounded-circle mr-2">
                                                    <?= substr($e['nombre_entrega'], 0, 1) ?>
                                                </div>
                                                <div>
                                                    <?= $e['nombre_entrega'] ?> <?= $e['apellido_entrega'] ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($e['estado'] == 'completada'): ?>
                                                <span class="badge badge-success">Completada</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Pendiente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(isset($e['comprobante_entrega']) && $e['comprobante_entrega']): ?>
                                                <button type="button" class="btn btn-sm btn-danger ver-pdf" 
                                                        data-toggle="modal" data-target="#pdfModal" 
                                                        data-pdf="<?= base_url('uploads/comprobantes_entrega/' . $e['comprobante_entrega']) ?>">
                                                    <i class="fas fa-file-pdf mr-1"></i> Ver PDF
                                                </button>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Sin comprobante</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?= base_url('entregas/ver/' . $e['identregable']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Ver Detalle
                                                </a>
                                                <a href="<?= base_url('entregas/imprimir/' . $e['identregable']) ?>" class="btn btn-sm btn-info" target="_blank">
                                                    <i class="fas fa-print"></i> Imprimir
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver PDF -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="pdfModalLabel"><i class="fas fa-file-pdf mr-2"></i>Comprobante de Entrega</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div style="height: 70vh;">
                    <iframe id="pdfViewer" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <a id="downloadPdf" href="#" class="btn btn-success" download>
                    <i class="fas fa-download mr-2"></i>Descargar
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#entregas-table').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
        },
        "order": [[0, "desc"]], // Ordenar por ID de entregable (descendente)
        "responsive": true
    });
    
    // Manejar la visualización de PDF
    $('.ver-pdf').on('click', function() {
        var pdfUrl = $(this).data('pdf');
        $('#pdfViewer').attr('src', pdfUrl);
        $('#downloadPdf').attr('href', pdfUrl);
    });
});
</script>

<style>
.avatar {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.table td {
    vertical-align: middle;
}
</style>

<?= $footer; ?>