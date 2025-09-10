<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher de Pago - <?= $pago['idpagos'] ?></title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            background-color: #fff;
        }
        .voucher-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 10px;
            background-color: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .company-info {
            text-align: center;
        }
        .voucher-title {
            color: #007bff;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #f8f9fa;
            padding: 8px 15px;
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .payment-details {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .detail-row {
            display: flex;
            margin-bottom: 8px;
        }
        .detail-label {
            font-weight: bold;
            width: 200px;
        }
        .amount-highlight {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .signature-area {
            margin-top: 60px;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
            text-align: center;
        }
        .print-button {
            text-align: center;
            margin: 20px 0;
        }
        @media print {
            .print-button {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .voucher-container {
                border: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimir Voucher
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Cerrar
        </button>
    </div>

    <div class="voucher-container">
        <!-- Encabezado -->
        <div class="header">
            <div class="company-info">
                <h1><?= $empresa['razonsocial'] ?? 'EMPRESA' ?></h1>
                <p>RUC: <?= $empresa['ruc'] ?? '00000000000' ?></p>
                <p><?= $empresa['direccion'] ?? 'Dirección no especificada' ?></p>
                <p>Teléfono: <?= $empresa['telefono'] ?? 'N/A' ?> | Email: <?= $empresa['email'] ?? 'N/A' ?></p>
            </div>
        </div>

        <!-- Título del voucher -->
        <div class="voucher-title text-center">
            COMPROBANTE DE PAGO
        </div>

        <!-- Información del pago -->
        <div class="section">
            <div class="section-title">INFORMACIÓN DEL PAGO</div>
            <div class="payment-details">
                <div class="detail-row">
                    <div class="detail-label">Número de Voucher:</div>
                    <div><?= $pago['idpagos'] ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Fecha y Hora:</div>
                    <div><?= date('d/m/Y H:i', strtotime($pago['fechahora'])) ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Tipo de Pago:</div>
                    <div><?= $tipo_pago['tipopago'] ?></div>
                </div>
                <?php if (!empty($pago['numtransaccion'])): ?>
                <div class="detail-row">
                    <div class="detail-label">Número de Transacción:</div>
                    <div><?= $pago['numtransaccion'] ?></div>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                    <div class="detail-label">Registrado por:</div>
                    <div><?= $pago['nombreusuario'] ?? 'N/A' ?></div>
                </div>
            </div>
        </div>

        <!-- Montos -->
        <div class="section">
            <div class="section-title">DETALLES DE MONTO</div>
            <div class="payment-details">
                <div class="detail-row">
                    <div class="detail-label">Saldo Anterior:</div>
                    <div>S/ <?= number_format($pago['saldo'], 2) ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Amortización:</div>
                    <div class="amount-highlight">S/ <?= number_format($pago['amortizacion'], 2) ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Nuevo Saldo:</div>
                    <div class="<?= $pago['deuda'] > 0 ? 'text-warning' : 'text-success' ?>">
                        S/ <?= number_format($pago['deuda'], 2) ?>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Estado:</div>
                    <div>
                        <strong class="<?= $pago['deuda'] > 0 ? 'text-warning' : 'text-success' ?>">
                            <?= $pago['deuda'] > 0 ? 'PENDIENTE' : 'PAGADO COMPLETO' ?>
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del contrato -->
        <div class="section">
            <div class="section-title">INFORMACIÓN DEL CONTRATO</div>
            <div class="payment-details">
                <div class="detail-row">
                    <div class="detail-label">Número de Contrato:</div>
                    <div><?= $info_contrato['idcontrato'] ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Cliente:</div>
                    <div>
                        <?= !empty($info_contrato['nombres']) ? 
                            $info_contrato['nombres'] . ' ' . $info_contrato['apellidos'] : 
                            $info_contrato['razonsocial'] ?>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Monto Total del Contrato:</div>
                    <div>S/ <?= number_format($info_contrato['monto_total'], 2) ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Total Pagado:</div>
                    <div>
                        <?php 
                            $total_pagado = $info_contrato['monto_total'] - $pago['deuda'];
                            echo 'S/ ' . number_format($total_pagado, 2);
                        ?>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Porcentaje Pagado:</div>
                    <div>
                        <?php 
                            $porcentaje = $info_contrato['monto_total'] > 0 ? 
                                (($total_pagado / $info_contrato['monto_total']) * 100) : 0;
                            echo number_format($porcentaje, 2) . '%';
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Observaciones -->
        <div class="section">
            <div class="section-title">OBSERVACIONES</div>
            <div class="payment-details">
                <p>Este comprobante certifica que se ha recibido el pago correspondiente al contrato mencionado.</p>
                <p>Para cualquier consulta, por favor contactar con nuestra área de administración.</p>
            </div>
        </div>

        <!-- Firmas -->
        <div class="signature-area">
            <div style="float: left; width: 45%; text-align: center;">
                <p>_________________________</p>
                <p>Firma del Cliente</p>
            </div>
            <div style="float: right; width: 45%; text-align: center;">
                <p>_________________________</p>
                <p>Firma del Representante</p>
            </div>
            <div style="clear: both;"></div>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <p>Documento generado el: <?= date('d/m/Y H:i:s') ?></p>
            <p>Este es un documento oficial de <?= $empresa['razonsocial'] ?? 'la Empresa' ?></p>
        </div>
    </div>

    <script>
        // Auto-imprimir al cargar la página (opcional)
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>
</html>