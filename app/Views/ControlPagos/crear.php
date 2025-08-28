<?= $header ?>
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Nuevo Pago</h4>
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
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Nuevo Pago</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Registrar Nuevo Pago</div>
                </div>
                <form method="POST" action="<?= base_url('/controlpagos/guardar') ?>">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="idcontrato">Contrato</label>
                                    <select class="form-control" id="idcontrato" name="idcontrato" required>
                                        <option value="">Seleccionar Contrato</option>
                                        <!-- Aquí deberías cargar los contratos disponibles desde la base de datos -->
                                        <option value="1">Contrato #1 - Carlos García</option>
                                        <option value="2">Contrato #2 - María Rodríguez</option>
                                        <!-- Agrega más opciones según tus datos -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="idtipopago">Tipo de Pago</label>
                                    <select class="form-control" id="idtipopago" name="idtipopago" required>
                                        <option value="">Seleccionar Tipo</option>
                                        <option value="1">Efectivo</option>
                                        <option value="2">Transferencia Bancaria</option>
                                        <option value="3">Tarjeta de Crédito</option>
                                        <option value="4">Cheque</option>
                                        <option value="5">Yape/Plin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="saldo">Saldo</label>
                                    <input type="number" step="0.01" class="form-control" id="saldo" name="saldo" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="amortizacion">Amortización</label>
                                    <input type="number" step="0.01" class="form-control" id="amortizacion" name="amortizacion" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="deuda">Deuda</label>
                                    <input type="number" step="0.01" class="form-control" id="deuda" name="deuda" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numtransaccion">Número de Transacción</label>
                                    <input type="text" class="form-control" id="numtransaccion" name="numtransaccion" placeholder="Ej: TXN20240120001">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fechahora">Fecha y Hora</label>
                                    <input type="datetime-local" class="form-control" id="fechahora" name="fechahora" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-success">Guardar Pago</button>
                        <a href="<?= base_url('/controlpagos') ?>" class="btn btn-danger">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $footer ?>