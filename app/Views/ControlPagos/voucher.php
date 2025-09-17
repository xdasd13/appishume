<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher de Pago - #<?= $pago['idpagos'] ?></title>
    <style>
        /* Estilos para el voucher profesional */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .voucher-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .voucher-header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .voucher-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        
        .voucher-header p {
            margin: 5px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .voucher-body {
            padding: 30px;
        }
        
        .voucher-section {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .voucher-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .voucher-section h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
            border-left: 4px solid #3498db;
            padding-left: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .info-item {
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .info-value {
            color: #333;
            font-size: 15px;
        }
        
        .amount-highlight {
            font-size: 18px;
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 5px;
        }
        
        .amount-positive {
            background-color: #d4edda;
            color: #155724;
        }
        
        .amount-negative {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .amount-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .voucher-footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #eee;
        }
        
        .signature-area {
            margin-top: 40px;
            border-top: 1px dashed #ccc;
            padding-top: 20px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 250px;
            margin: 0 auto;
            padding-top: 30px;
        }
        
        .print-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
            font-weight: bold;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .print-button:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }
        
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        
        .watermark {
            position: absolute;
            opacity: 0.03;
            font-size: 120px;
            font-weight: bold;
            color: #000;
            transform: rotate(-45deg);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            pointer-events: none;
        }
        
        @media print {
            body {
                background: white;
            }
            
            .voucher-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }
            
            .print-button {
                display: none;
            }
            
            .voucher-header {
                background: #2c3e50 !important;
                color: black !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
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