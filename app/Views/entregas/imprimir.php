<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrega #<?= $entrega['identregable'] ?> - APISHUME EVENTOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header-company {
            border-bottom: 3px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        
        .document-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin: 20px 0;
            text-align: center;
            border: 2px solid #007bff;
            padding: 10px;
            background-color: #f8f9fa;
        }
        
        .info-section {
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        
        .section-header {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            font-weight: bold;
            margin: 0;
        }
        
        .section-body {
            padding: 15px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
            flex-shrink: 0;
        }
        
        .info-value {
            flex: 1;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .status-completed {
            background-color: #28a745;
            color: white;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }
        
        .signature-section {
            margin-top: 40px;
            border-top: 2px solid #007bff;
            padding-top: 20px;
        }
        
        .signature-box {
            border: 1px solid #dee2e6;
            height: 80px;
            margin-top: 10px;
        }
        
        .footer-info {
            margin-top: 30px;
            font-size: 10px;
            color: #6c757d;
            text-align: center;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header de la empresa -->
        <div class="header-company">
            <div class="row">
                <div class="col-md-8">
                    <div class="company-name">APISHUME EVENTOS</div>
                    <div class="text-muted">
                        <strong>RUC:</strong> 20123456789<br>
                        <strong>Dirección:</strong> Av. Principal 123, Lima, Perú<br>
                        <strong>Teléfono:</strong> +51 1 234 5678 | <strong>Email:</strong> info@apishume.com
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <div class="text-muted">
                        <strong>Fecha de impresión:</strong><br>
                        <?= date('d/m/Y H:i:s') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Título del documento -->
        <div class="document-title">
            <i class="fas fa-truck-loading"></i> DETALLE DE ENTREGA #<?= $entrega['identregable'] ?>
        </div>

        <!-- Información del Cliente -->
        <div class="info-section">
            <h5 class="section-header">
                <i class="fas fa-user-circle"></i> INFORMACIÓN DEL CLIENTE
            </h5>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">Nombre:</span>
                            <span class="info-value"><?= isset($entrega['nombre_cliente']) ? $entrega['nombre_cliente'] : 'No disponible' ?> <?= isset($entrega['apellido_cliente']) ? $entrega['apellido_cliente'] : '' ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Documento:</span>
                            <span class="info-value">
                                <?php if(isset($entrega['tipodoc']) && !empty($entrega['tipodoc']) && isset($entrega['numerodoc']) && !empty($entrega['numerodoc'])): ?>
                                    <?= $entrega['tipodoc'] ?>: <?= $entrega['numerodoc'] ?>
                                <?php else: ?>
                                    No disponible
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">Teléfono:</span>
                            <span class="info-value"><?= isset($entrega['telprincipal']) && !empty($entrega['telprincipal']) ? $entrega['telprincipal'] : 'No disponible' ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Dirección:</span>
                            <span class="info-value"><?= isset($entrega['direccion']) && !empty($entrega['direccion']) ? $entrega['direccion'] : 'No disponible' ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Servicio -->
        <div class="info-section">
            <h5 class="section-header">
                <i class="fas fa-briefcase"></i> INFORMACIÓN DEL SERVICIO
            </h5>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">Servicio:</span>
                            <span class="info-value"><?= isset($entrega['servicio']) && !empty($entrega['servicio']) ? $entrega['servicio'] : 'No disponible' ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Descripción:</span>
                            <span class="info-value"><?= isset($entrega['descripcion_servicio']) && !empty($entrega['descripcion_servicio']) ? $entrega['descripcion_servicio'] : 'No disponible' ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">Fecha Servicio:</span>
                            <span class="info-value"><?= isset($entrega['fechahoraservicio']) && !empty($entrega['fechahoraservicio']) ? date('d/m/Y H:i', strtotime($entrega['fechahoraservicio'])) : 'No disponible' ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Cantidad:</span>
                            <span class="info-value"><?= isset($entrega['cantidad']) ? $entrega['cantidad'] : 'No disponible' ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Precio:</span>
                            <span class="info-value"><?= isset($entrega['precio']) ? 'S/ ' . number_format($entrega['precio'], 2) : 'No disponible' ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Entrega -->
        <div class="info-section">
            <h5 class="section-header">
                <i class="fas fa-truck"></i> INFORMACIÓN DE ENTREGA
            </h5>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">Responsable:</span>
                            <span class="info-value">
                                <?= isset($entrega['nombre_entrega']) && !empty($entrega['nombre_entrega']) && $entrega['nombre_entrega'] !== 'Sin nombre' ? 
                                    $entrega['nombre_entrega'] . ' ' . $entrega['apellido_entrega'] : 
                                    session()->get('usuario_nombre') ?? 'No disponible' ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Documento Responsable:</span>
                            <span class="info-value">
                                <?= isset($entrega['numerodoc_entrega']) && !empty($entrega['numerodoc_entrega']) && $entrega['numerodoc_entrega'] !== 'No disponible' ? 
                                    'DNI: ' . $entrega['numerodoc_entrega'] : 'No disponible' ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <span class="info-label">Fecha/Hora Entrega:</span>
                            <span class="info-value"><?= isset($entrega['fechahoraentrega']) && !empty($entrega['fechahoraentrega']) ? date('d/m/Y H:i', strtotime($entrega['fechahoraentrega'])) : 'No disponible' ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Estado:</span>
                            <span class="info-value">
                                <span class="status-badge <?= (isset($entrega['estado']) && $entrega['estado'] == 'completada') ? 'status-completed' : 'status-pending' ?>">
                                    <?= isset($entrega['estado_visual']) ? $entrega['estado_visual'] : (isset($entrega['estado']) && $entrega['estado'] == 'completada' ? '✅ ENTREGADO' : '⏳ EN POSTPRODUCCIÓN') ?>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="info-row">
                    <span class="info-label">Formato Entrega:</span>
                    <span class="info-value"><?= isset($entrega['observaciones']) && !empty($entrega['observaciones']) ? $entrega['observaciones'] : 'No disponible' ?></span>
                </div>
                <?php if(isset($entrega['estado']) && $entrega['estado'] == 'completada' && isset($entrega['fecha_real_entrega']) && !empty($entrega['fecha_real_entrega'])): ?>
                <div class="info-row">
                    <span class="info-label">Fecha Real Entrega:</span>
                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($entrega['fecha_real_entrega'])) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="info-section">
            <h5 class="section-header">
                <i class="fas fa-info-circle"></i> INFORMACIÓN ADICIONAL
            </h5>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-row">
                            <span class="info-label">Contrato:</span>
                            <span class="info-value">#<?= isset($entrega['idcontrato']) && !empty($entrega['idcontrato']) ? $entrega['idcontrato'] : 'N/A' ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-row">
                            <span class="info-label">Cotización:</span>
                            <span class="info-value">#<?= isset($entrega['idcotizacion']) && !empty($entrega['idcotizacion']) ? $entrega['idcotizacion'] : 'N/A' ?></span>
                        </div>
                    </div>
                    <?php if(isset($entrega['dias_postproduccion'])): ?>
                    <div class="col-md-4">
                        <div class="info-row">
                            <span class="info-label">Días Postproducción:</span>
                            <span class="info-value"><?= $entrega['dias_postproduccion'] ?> días</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sección de firmas -->
        <div class="signature-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="text-center">
                        <strong>FIRMA DEL RESPONSABLE DE ENTREGA</strong>
                        <div class="signature-box"></div>
                        <small class="text-muted"><?= isset($entrega['nombre_entrega']) && !empty($entrega['nombre_entrega']) && $entrega['nombre_entrega'] !== 'Sin nombre' ? 
                            $entrega['nombre_entrega'] . ' ' . $entrega['apellido_entrega'] : 
                            session()->get('usuario_nombre') ?? 'Responsable' ?></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-center">
                        <strong>FIRMA DEL CLIENTE</strong>
                        <div class="signature-box"></div>
                        <small class="text-muted"><?= isset($entrega['nombre_cliente']) ? $entrega['nombre_cliente'] : 'Cliente' ?> <?= isset($entrega['apellido_cliente']) ? $entrega['apellido_cliente'] : '' ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-info">
            <p><strong>APISHUME EVENTOS</strong> - Sistema de Gestión de Entregas</p>
            <p>Este documento fue generado automáticamente el <?= date('d/m/Y H:i:s') ?></p>
        </div>

        <!-- Botones de control (no se imprimen) -->
        <div class="no-print mt-4">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cerrar
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-imprimir al cargar la página
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
