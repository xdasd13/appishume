<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="<?= base_url('assets/css/entregas-imprimir.css') ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrega #<?= $entrega['identregable'] ?> - ISHUME PRODUCTORA</title>
    
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
