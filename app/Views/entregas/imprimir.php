<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrega #<?= $entrega['identregable'] ?> - ISHUME PRODUCTORA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            margin: 20px;
            color: #333;
            background: white;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .document-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 20px 0;
            border: 2px solid #007bff;
            padding: 15px;
            background-color: #f8f9fa;
            text-align: center;
        }
        
        .section {
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        
        .section-header {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            margin: 0;
            border-radius: 5px 5px 0 0;
        }
        
        .section-body {
            padding: 20px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            width: 200px;
            flex-shrink: 0;
        }
        
        .info-value {
            flex: 1;
        }
        
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
        }
        
        .status-completed {
            background-color: #28a745;
            color: white;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }
        
        .signatures {
            margin-top: 40px;
            border-top: 2px solid #007bff;
            padding-top: 30px;
        }
        
        .signature-box {
            border: 1px solid #dee2e6;
            height: 100px;
            margin: 10px 0;
        }
        
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        
        .print-buttons {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
        }
        
        .print-buttons button {
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin: 0 10px;
            cursor: pointer;
        }
        
        .btn-print {
            background: #007bff;
            color: white;
        }
        
        .btn-close {
            background: #6c757d;
            color: white;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .print-buttons {
                display: none !important;
            }
            .section {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            .section-header {
                page-break-after: avoid;
                break-after: avoid;
            }
            .info-row {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            .signatures {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            .document-title {
                page-break-after: avoid;
                break-after: avoid;
            }
            .header {
                page-break-after: avoid;
                break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Header de la empresa -->
    <div class="header">
        <div class="company-name">ISHUME PRODUCTORA</div>
        <div>RUC: 10727174040 | Av. Luis Massaro 791</div>
        <div>Teléfono: +51 991157028 | Email: ishumeproductora@hotmail.com</div>
        <div style="margin-top: 15px; font-size: 12px; color: #666;">
            Fecha de impresión: <?= date('d/m/Y H:i:s') ?>
        </div>
    </div>

    <!-- Título del documento -->
    <div class="document-title">
        <i class="fas fa-truck-loading"></i> DETALLE DE ENTREGA #<?= $entrega['identregable'] ?>
    </div>
    
    <!-- Espaciado para mejor presentación -->
    <div style="margin-bottom: 20px;"></div>

    <!-- Información del Cliente -->
    <div class="section">
        <h3 class="section-header">
            <i class="fas fa-user-circle"></i> INFORMACIÓN DEL CLIENTE
        </h3>
        <div class="section-body">
            <div class="info-row">
                <div class="info-label">Nombre:</div>
                <div class="info-value">
                    <?= isset($entrega['nombre_cliente']) ? $entrega['nombre_cliente'] : 'No disponible' ?> 
                    <?= isset($entrega['apellido_cliente']) ? $entrega['apellido_cliente'] : '' ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Documento:</div>
                <div class="info-value">
                    <?php if(isset($entrega['tipodoc']) && !empty($entrega['tipodoc']) && isset($entrega['numerodoc']) && !empty($entrega['numerodoc'])): ?>
                        <?= $entrega['tipodoc'] ?>: <?= $entrega['numerodoc'] ?>
                    <?php else: ?>
                        No disponible
                    <?php endif; ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Teléfono:</div>
                <div class="info-value">
                    <?= isset($entrega['telprincipal']) && !empty($entrega['telprincipal']) ? $entrega['telprincipal'] : 'No disponible' ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Dirección:</div>
                <div class="info-value">
                    <?= isset($entrega['direccion']) && !empty($entrega['direccion']) ? $entrega['direccion'] : 'No disponible' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Servicio -->
    <div class="section">
        <h3 class="section-header">
            <i class="fas fa-briefcase"></i> INFORMACIÓN DEL SERVICIO
        </h3>
        <div class="section-body">
            <div class="info-row">
                <div class="info-label">Servicio:</div>
                <div class="info-value">
                    <?= isset($entrega['servicio']) && !empty($entrega['servicio']) ? $entrega['servicio'] : 'No disponible' ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Descripción:</div>
                <div class="info-value">
                    <?= isset($entrega['descripcion_servicio']) && !empty($entrega['descripcion_servicio']) ? $entrega['descripcion_servicio'] : 'No disponible' ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha Servicio:</div>
                <div class="info-value">
                    <?= isset($entrega['fechahoraservicio']) && !empty($entrega['fechahoraservicio']) ? date('d/m/Y H:i', strtotime($entrega['fechahoraservicio'])) : 'No disponible' ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Cantidad:</div>
                <div class="info-value">
                    <?= isset($entrega['cantidad']) ? $entrega['cantidad'] : 'No disponible' ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Precio:</div>
                <div class="info-value">
                    <?= isset($entrega['precio']) ? 'S/ ' . number_format($entrega['precio'], 2) : 'No disponible' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Entrega -->
    <div class="section">
        <h3 class="section-header">
            <i class="fas fa-truck"></i> INFORMACIÓN DE ENTREGA
        </h3>
        <div class="section-body">
            <div class="info-row">
                <div class="info-label">Responsable:</div>
                <div class="info-value">
                    <?= isset($entrega['nombre_entrega']) && !empty($entrega['nombre_entrega']) && $entrega['nombre_entrega'] !== 'Sin nombre' ? 
                        $entrega['nombre_entrega'] . ' ' . $entrega['apellido_entrega'] : 
                        session()->get('usuario_nombre') ?? 'No disponible' ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha/Hora Entrega:</div>
                <div class="info-value">
                    <?= isset($entrega['fechahoraentrega']) && !empty($entrega['fechahoraentrega']) ? date('d/m/Y H:i', strtotime($entrega['fechahoraentrega'])) : 'No disponible' ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Formato Entrega:</div>
                <div class="info-value">
                    <?= isset($entrega['observaciones']) && !empty($entrega['observaciones']) ? $entrega['observaciones'] : 'No disponible' ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Estado:</div>
                <div class="info-value">
                    <span class="status-badge <?= (isset($entrega['estado']) && $entrega['estado'] == 'completada') ? 'status-completed' : 'status-pending' ?>">
                        <?= isset($entrega['estado_visual']) ? $entrega['estado_visual'] : (isset($entrega['estado']) && $entrega['estado'] == 'completada' ? '✅ ENTREGADO' : '⏳ EN POSTPRODUCCIÓN') ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="section">
        <h3 class="section-header">
            <i class="fas fa-info-circle"></i> INFORMACIÓN ADICIONAL
        </h3>
        <div class="section-body">
            <div class="info-row">
                <div class="info-label">Contrato:</div>
                <div class="info-value">
                    #<?= isset($entrega['idcontrato']) && !empty($entrega['idcontrato']) ? $entrega['idcontrato'] : 'N/A' ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Cotización:</div>
                <div class="info-value">
                    #<?= isset($entrega['idcotizacion']) && !empty($entrega['idcotizacion']) ? $entrega['idcotizacion'] : 'N/A' ?>
                </div>
            </div>
            <?php if(isset($entrega['dias_postproduccion'])): ?>
            <div class="info-row">
                <div class="info-label">Días Postproducción:</div>
                <div class="info-value">
                    <?= abs($entrega['dias_postproduccion']) ?> días
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Espaciado adicional -->
    <div style="margin-bottom: 30px;"></div>
    
    <!-- Sección de firmas -->
    <div class="signatures">
        <div style="display: flex; gap: 50px;">
            <div style="flex: 1; text-align: center;">
                <strong>FIRMA DEL RESPONSABLE DE ENTREGA</strong>
                <div class="signature-box"></div>
                <div style="font-size: 12px; color: #666; margin-top: 5px;">
                    <?= isset($entrega['nombre_entrega']) && !empty($entrega['nombre_entrega']) && $entrega['nombre_entrega'] !== 'Sin nombre' ? 
                        $entrega['nombre_entrega'] . ' ' . $entrega['apellido_entrega'] : 
                        session()->get('usuario_nombre') ?? 'Responsable' ?>
                </div>
            </div>
            <div style="flex: 1; text-align: center;">
                <strong>FIRMA DEL CLIENTE</strong>
                <div class="signature-box"></div>
                <div style="font-size: 12px; color: #666; margin-top: 5px;">
                    <?= isset($entrega['nombre_cliente']) ? $entrega['nombre_cliente'] : 'Cliente' ?> 
                    <?= isset($entrega['apellido_cliente']) ? $entrega['apellido_cliente'] : '' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>ISHUME PRODUCTORA</strong> - Sistema de Gestión de Entregas</p>
        <p>Este documento fue generado automáticamente el <?= date('d/m/Y H:i:s') ?></p>
    </div>

    <!-- Botones de impresión -->
    <div class="print-buttons">
        <button class="btn-print" onclick="window.print()">🖨️ Imprimir</button>
        <button class="btn-close" onclick="window.close()">✕ Cerrar</button>
    </div>

    <script>
        // Auto-imprimir después de cargar
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
    </script>
</body>
</html>
