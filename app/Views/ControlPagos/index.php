<?= $header ?>
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Control de Pagos</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="<?= base_url('/dashboard') ?>">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Finanzas</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('/controlpagos') ?>">Control de Pagos</a>
            </li>
        </ul>
    </div>

    <!-- Mostrar mensajes de éxito/error -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
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
                    <!-- Debug: Verificar si hay datos -->
                    <?php 
                    echo "<!-- Debug: Número de pagos = " . count($pagos ?? []) . " -->";
                    ?>
                    
                    <div class="table-responsive">
                        <table id="basic-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Contrato</th>
                                    <th>Saldo</th>
                                    <th>Amortización</th>
                                    <th>Deuda</th>
                                    <th>Tipo Pago</th>
                                    <th>Transacción</th>
                                    <th>Fecha/Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pagos) && is_array($pagos)): ?>
                                    <?php foreach ($pagos as $pago): ?>
                                        <tr>
                                            <td><?= $pago['idpagos'] ?></td>
                                            <td>Contrato #<?= $pago['idcontrato'] ?></td>
                                            <td>S/ <?= number_format($pago['saldo'], 2) ?></td>
                                            <td>S/ <?= number_format($pago['amortizacion'], 2) ?></td>
                                            <td>S/ <?= number_format($pago['deuda'], 2) ?></td>
                                            <td>
                                                <?php 
                                                    switch($pago['idtipopago']){
                                                        case 1: echo "Efectivo"; break;
                                                        case 2: echo "Transferencia"; break;
                                                        case 3: echo "Tarjeta Crédito"; break;
                                                        case 4: echo "Cheque"; break;
                                                        case 5: echo "Yape/Plin"; break;
                                                        default: echo "No especificado";
                                                    }
                                                ?>
                                            </td>
                                            <td><?= $pago['numtransaccion'] ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($pago['fechahora'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay registros de pagos</td>
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
            "order": [[0, "desc"]]
        });
    });
</script>
<?= $footer ?>