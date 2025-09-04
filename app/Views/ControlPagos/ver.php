<?= $header ?>
<div class="page-inner">
    

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Detalles del Pago #<?= $pago['idpagos'] ?></h4>
                        <a href="<?= base_url('/controlpagos') ?>" class="btn btn-secondary btn-round ml-auto">
                            <i class="fa fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($pago)): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ID de Pago</label>
                                <p class="form-control-static"><?= $pago['idpagos'] ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contrato</label>
                                <p class="form-control-static">Contrato #<?= $pago['idcontrato'] ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Saldo (S/)</label>
                                <p class="form-control-static <?= $pago['saldo'] > 0 ? 'text-danger' : 'text-success' ?>">
                                    <?= number_format($pago['saldo'], 2) ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Amortización (S/)</label>
                                <p class="form-control-static text-success">
                                    <?= number_format($pago['amortizacion'], 2) ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Deuda (S/)</label>
                                <p class="form-control-static <?= $pago['deuda'] > 0 ? 'text-warning' : 'text-success' ?>">
                                    <?= number_format($pago['deuda'], 2) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo de Pago</label>
                                <p class="form-control-static">
                                    <?php 
                                        switch($pago['idtipopago']){
                                            case 1: echo "Efectivo"; break;
                                            case 2: echo "Transferencia Bancaria"; break;
                                            case 3: echo "Tarjeta de Crédito"; break;
                                            case 4: echo "Cheque"; break;
                                            case 5: echo "Yape/Plin"; break;
                                            default: echo "No especificado";
                                        }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Número de Transacción</label>
                                <p class="form-control-static"><?= $pago['numtransaccion'] ?? 'N/A' ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha y Hora</label>
                                <p class="form-control-static"><?= date('d/m/Y H:i', strtotime($pago['fechahora'])) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Registrado por</label>
                                <p class="form-control-static">Usuario #<?= $pago['idusuario'] ?></p>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        No se encontró información del pago.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $footer ?>