<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="<?= base_url('assets/css/ControlPagos-voucher.css') ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher de Pago - #<?= $pago['idpagos'] ?></title>
    
</head>
<body>
    <div class="voucher-container">
        <div class="voucher-header">
            <div class="watermark">VOUCHER</div>
            <?php if (!empty($empresa['logo'])): ?>
                <img src="<?= base_url('uploads/logo/' . $empresa['logo']) ?>" alt="Logo" class="logo">
            <?php endif; ?>
            <h1>COMPROBANTE DE PAGO</h1>
            <p>N° <?= str_pad($pago['idpagos'], 6, '0', STR_PAD_LEFT) ?></p>
        </div>
        
        <div class="voucher-body">
            <div class="voucher-section">
                <h3>Información del Pago</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Número de Pago</div>
                        <div class="info-value">#<?= $pago['idpagos'] ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Fecha y Hora</div>
                        <div class="info-value"><?= date('d/m/Y H:i:s', strtotime($pago['fechahora'])) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Contrato</div>
                        <div class="info-value">#<?= $pago['idcontrato'] ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tipo de Pago</div>
                        <div class="info-value"><?= $tipo_pago['tipopago'] ?? 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Transacción/Referencia</div>
                        <div class="info-value"><?= !empty($pago['numtransaccion']) ? $pago['numtransaccion'] : 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Registrado por</div>
                        <div class="info-value"><?= $pago['nombreusuario'] ?? 'N/A' ?></div>
                    </div>
                </div>
            </div>
            
            <div class="voucher-section">
                <h3>Detalles Financieros</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Monto Total del Contrato</div>
                        <div class="info-value amount-highlight amount-positive">
                            S/ <?= number_format($info_contrato['monto_total'], 2) ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Saldo Anterior</div>
                        <div class="info-value amount-highlight amount-negative">
                            S/ <?= number_format($pago['saldo'], 2) ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Amortización</div>
                        <div class="info-value amount-highlight amount-positive">
                            S/ <?= number_format($pago['amortizacion'], 2) ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nuevo Saldo</div>
                        <div class="info-value amount-highlight <?= $pago['deuda'] > 0 ? 'amount-warning' : 'amount-positive' ?>">
                            S/ <?= number_format($pago['deuda'], 2) ?>
                            <?php if ($pago['deuda'] == 0): ?>
                                <br><small>¡CONTRATO PAGADO EN SU TOTALIDAD!</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="voucher-section">
                <h3>Información del Cliente</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Cliente</div>
                        <div class="info-value">
                            <?php if (!empty($info_contrato['nombres'])): ?>
                                <?= $info_contrato['nombres'] . ' ' . $info_contrato['apellidos'] ?>
                            <?php else: ?>
                                <?= $info_contrato['razonsocial'] ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Documento</div>
                        <div class="info-value">
                            <?php if (!empty($info_contrato['nrodocumento'])): ?>
                                <?= $info_contrato['nrodocumento'] ?>
                            <?php else: ?>
                                <?= $info_contrato['ruc'] ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= $info_contrato['email'] ?? 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Teléfono</div>
                        <div class="info-value"><?= $info_contrato['telefono'] ?? 'N/A' ?></div>
                    </div>
                </div>
            </div>
            
            <div class="voucher-section">
                <h3>Información de la Empresa</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Empresa</div>
                        <div class="info-value"><?= $empresa['razonsocial'] ?? 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">RUC</div>
                        <div class="info-value"><?= $empresa['ruc'] ?? 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Dirección</div>
                        <div class="info-value"><?= $empresa['direccion'] ?? 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Teléfono</div>
                        <div class="info-value"><?= $empresa['telefono'] ?? 'N/A' ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="voucher-footer">
            <p>Este documento es un comprobante de pago generado automáticamente el <?= date('d/m/Y H:i:s') ?></p>
            
            <div class="signature-area">
                <div class="signature-line"></div>
                <p>Firma y Sello Autorizado</p>
            </div>
        </div>
    </div>
    
    <button class="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimir Voucher
    </button>
    
    <script>
        // Auto-print cuando se carga la página (opcional)
        window.onload = function() {
            // Descomenta la siguiente línea si quieres que se imprima automáticamente
            // window.print();
        };
    </script>
</body>
</html>