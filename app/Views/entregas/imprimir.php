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
        <div style="font-size: 10px;">RUC: 10727174040 | Av. Luis Massaro 791 | Tel: +51 991157028</div>
        <div style="font-size: 9px; color: #666;">Impreso: <?= date('d/m/Y H:i') ?></div>
    </div>

    <!-- Título del documento -->
    <div class="document-title">
        DETALLE DE ENTREGA #<?= $entrega['identregable'] ?>
    </div>

    <!-- Información Cliente y Servicio en 2 columnas -->
    <div style="display: flex; gap: 8px;">
        <div style="flex: 1;">
            <div class="section">
                <h3 class="section-header">CLIENTE</h3>
                <div class="section-body">
                    <div class="info-row">
                        <div class="info-label">Nombre:</div>
                        <div class="info-value"><?= isset($entrega['nombre_cliente']) ? $entrega['nombre_cliente'] : 'No disponible' ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Doc:</div>
                        <div class="info-value"><?php if(isset($entrega['tipodoc']) && !empty($entrega['tipodoc']) && isset($entrega['numerodoc']) && !empty($entrega['numerodoc'])): ?><?= $entrega['tipodoc'] ?>: <?= $entrega['numerodoc'] ?><?php else: ?>No disponible<?php endif; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tel:</div>
                        <div class="info-value"><?= isset($entrega['telprincipal']) && !empty($entrega['telprincipal']) ? $entrega['telprincipal'] : 'No disponible' ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div style="flex: 1;">
            <div class="section">
                <h3 class="section-header">SERVICIO</h3>
                <div class="section-body">
                    <div class="info-row">
                        <div class="info-label">Servicio:</div>
                        <div class="info-value"><?= isset($entrega['servicio']) && !empty($entrega['servicio']) ? $entrega['servicio'] : 'No disponible' ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Fecha:</div>
                        <div class="info-value"><?= isset($entrega['fechahoraservicio']) && !empty($entrega['fechahoraservicio']) ? date('d/m/Y H:i', strtotime($entrega['fechahoraservicio'])) : 'No disponible' ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Precio:</div>
                        <div class="info-value"><?= isset($entrega['precio']) ? 'S/ ' . number_format($entrega['precio'], 2) : 'No disponible' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Entrega -->
    <div class="section">
        <h3 class="section-header">ENTREGA</h3>
        <div class="section-body">
            <div class="info-row">
                <div class="info-label">Responsable:</div>
                <div class="info-value"><?= isset($entrega['nombre_entrega']) && !empty($entrega['nombre_entrega']) && $entrega['nombre_entrega'] !== 'Sin nombre' ? $entrega['nombre_entrega'] . ' ' . $entrega['apellido_entrega'] : session()->get('usuario_nombre') ?? 'No disponible' ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha:</div>
                <div class="info-value"><?= isset($entrega['fechahoraentrega']) && !empty($entrega['fechahoraentrega']) ? date('d/m/Y H:i', strtotime($entrega['fechahoraentrega'])) : 'No disponible' ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Formato:</div>
                <div class="info-value"><?= isset($entrega['observaciones']) && !empty($entrega['observaciones']) ? $entrega['observaciones'] : 'No disponible' ?></div>
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
    
    <!-- Sección de firmas -->
    <div class="signatures">
        <div style="display: flex; gap: 15px;">
            <div style="flex: 1; text-align: center;">
                <strong style="font-size: 9px;">FIRMA RESPONSABLE</strong>
                <div class="signature-box"></div>
                <div style="font-size: 8px; color: #666; margin-top: 2px;">
                    <?= isset($entrega['nombre_entrega']) && !empty($entrega['nombre_entrega']) && $entrega['nombre_entrega'] !== 'Sin nombre' ? 
                        $entrega['nombre_entrega'] . ' ' . $entrega['apellido_entrega'] : 
                        session()->get('usuario_nombre') ?? 'Responsable' ?>
                </div>
            </div>
            <div style="flex: 1; text-align: center;">
                <strong style="font-size: 9px;">FIRMA CLIENTE</strong>
                <div class="signature-box"></div>
                <div style="font-size: 8px; color: #666; margin-top: 2px;">
                    <?= isset($entrega['nombre_cliente']) ? $entrega['nombre_cliente'] : 'Cliente' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p style="margin: 2px 0;"><strong>ISHUME PRODUCTORA</strong> - Sistema de Gestión de Entregas</p>
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
