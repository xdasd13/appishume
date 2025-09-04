<?= $header ?>
<div class="page-inner">


    <!-- Mostrar mensajes de éxito/error -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Registros de Pagos</h4>
                        <a href="<?= base_url('/controlpagos/crear') ?>" class="btn btn-primary btn-round ml-auto">
                            <i class="fa fa-plus"></i>
                            Nuevo Pago
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="basic-datatables" class="display table table-striped table-hover">
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
                                                <?= number_format($pago['saldo'], 2) ?>
                                            </td>
                                            <td class="text-success">
                                                <?= number_format($pago['amortizacion'], 2) ?>
                                            </td>
                                            <td class="<?= $pago['deuda'] > 0 ? 'text-warning' : 'text-success' ?>">
                                                <?= number_format($pago['deuda'], 2) ?>
                                            </td>
                                            <td><?= $pago['tipopago'] ?></td>
                                            <td><?= $pago['numtransaccion'] ?? 'N/A' ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($pago['fechahora'])) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('/controlpagos/ver/' . $pago['idpagos']) ?>" 
                                                       class="btn btn-sm btn-info" title="Ver detalles">
                                                        <i class="fa fa-eye"></i>
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
        $('#basic-datatables').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
            },
            "pageLength": 10,
            "order": [[0, "desc"]],
            "responsive": true,
            "dom": '<"top"lf>rt<"bottom"ip><"clear">'
        });
    });
</script>
<?= $footer ?>