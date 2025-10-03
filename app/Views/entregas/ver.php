<?= $header; ?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">
                <i class="fas fa-truck-loading mr-2"></i>Detalle de Entrega
            </h4>
            <ul class="breadcrumbs">
                <li class="nav-home">
                    <a href="<?= base_url('dashboard') ?>">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="fas fa-angle-right"></i>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('entregas') ?>">Entregas</a>
                </li>
                <li class="separator">
                    <i class="fas fa-angle-right"></i>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('entregas/completadas') ?>">Completadas</a>
                </li>
                <li class="separator">
                    <i class="fas fa-angle-right"></i>
                </li>
                <li class="nav-item">
                    <span>Entrega #<?= $entrega['identregable'] ?></span>
                </li>
            </ul>
        </div>

        <?php if (session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                <?= session('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= session('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Información de Entrega #<?= $entrega['identregable'] ?></h4>
                            <span class="ml-3 badge <?= $entrega['estado'] == 'completada' ? 'badge-success' : 'badge-warning' ?>">
                                <?= isset($entrega['estado_visual']) ? $entrega['estado_visual'] : ($entrega['estado'] == 'completada' ? '✅ ENTREGADO' : '⏳ EN POSTPRODUCCIÓN') ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="text-primary">
                                    <i class="fas fa-user-circle mr-2"></i>Cliente
                                </h5>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th class="bg-light">Nombre</th>
                                        <td><?= isset($entrega['nombre_cliente']) ? $entrega['nombre_cliente'] : 'No disponible' ?> <?= isset($entrega['apellido_cliente']) ? $entrega['apellido_cliente'] : '' ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Documento</th>
                                        <td>
                                            <?php if(isset($entrega['tipodoc']) && !empty($entrega['tipodoc']) && isset($entrega['numerodoc']) && !empty($entrega['numerodoc'])): ?>
                                                <?= $entrega['tipodoc'] ?>: <?= $entrega['numerodoc'] ?>
                                            <?php else: ?>
                                                No disponible
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Teléfono</th>
                                        <td><?= isset($entrega['telprincipal']) && !empty($entrega['telprincipal']) ? $entrega['telprincipal'] : 'No disponible' ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Dirección</th>
                                        <td><?= isset($entrega['direccion']) && !empty($entrega['direccion']) ? $entrega['direccion'] : 'No disponible' ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-info">
                                    <i class="fas fa-briefcase mr-2"></i>Servicio
                                </h5>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th class="bg-light">Servicio</th>
                                        <td><?= isset($entrega['servicio']) && !empty($entrega['servicio']) ? $entrega['servicio'] : 'No disponible' ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Descripción</th>
                                        <td><?= isset($entrega['descripcion_servicio']) && !empty($entrega['descripcion_servicio']) ? $entrega['descripcion_servicio'] : 'No disponible' ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Fecha Servicio</th>
                                        <td><?= isset($entrega['fechahoraservicio']) && !empty($entrega['fechahoraservicio']) ? date('d/m/Y H:i', strtotime($entrega['fechahoraservicio'])) : 'No disponible' ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Cantidad</th>
                                        <td><?= isset($entrega['cantidad']) ? $entrega['cantidad'] : 'No disponible' ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Precio</th>
                                        <td><?= isset($entrega['precio']) ? 'S/ ' . number_format($entrega['precio'], 2) : 'No disponible' ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-success">
                                    <i class="fas fa-truck mr-2"></i>Información de Entrega
                                </h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th class="bg-light" width="25%">Responsable</th>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-primary text-white rounded-circle mr-2">
                                                    <?= isset($entrega['nombre_entrega']) && !empty($entrega['nombre_entrega']) && $entrega['nombre_entrega'] !== 'Sin nombre' ? substr($entrega['nombre_entrega'], 0, 1) : '?' ?>
                                                </div>
                                                <div>
                                                    <?= isset($entrega['nombre_entrega']) && !empty($entrega['nombre_entrega']) && $entrega['nombre_entrega'] !== 'Sin nombre' ? 
                                                        $entrega['nombre_entrega'] . ' ' . $entrega['apellido_entrega'] : 
                                                        session()->get('usuario_nombre') ?? 'No disponible' ?>
                                                    <div class="text-muted small">
                                                        <?= isset($entrega['numerodoc_entrega']) && !empty($entrega['numerodoc_entrega']) && $entrega['numerodoc_entrega'] !== 'No disponible' ? 
                                                            'DNI: ' . $entrega['numerodoc_entrega'] : '' ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Fecha/Hora Entrega</th>
                                        <td>
                                            <span class="font-weight-bold">
                                                <?= isset($entrega['fechahoraentrega']) && !empty($entrega['fechahoraentrega']) ? date('d/m/Y H:i', strtotime($entrega['fechahoraentrega'])) : 'No disponible' ?>
                                            </span>
                                            <?php if(isset($entrega['estado']) && $entrega['estado'] == 'completada' && isset($entrega['fecha_real_entrega']) && !empty($entrega['fecha_real_entrega'])): ?>
                                                <div class="text-muted small">
                                                    Entregado: <?= date('d/m/Y H:i', strtotime($entrega['fecha_real_entrega'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Formato Entrega</th>
                                        <td><?= isset($entrega['observaciones']) && !empty($entrega['observaciones']) ? $entrega['observaciones'] : 'No disponible' ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Estado</th>
                                        <td>
                                            <span class="badge <?= (isset($entrega['estado']) && $entrega['estado'] == 'completada') ? 'badge-success' : 'badge-warning' ?> p-2">
                                                <?= isset($entrega['estado_visual']) ? $entrega['estado_visual'] : (isset($entrega['estado']) && $entrega['estado'] == 'completada' ? '✅ ENTREGADO' : '⏳ EN POSTPRODUCCIÓN') ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Controles -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= base_url('entregas/historial') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>Volver
                            </a>
                            <div>
                                <button type="button" class="btn btn-info" onclick="imprimirEntrega()">
                                    <i class="fas fa-print mr-2"></i>Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-file-pdf mr-2"></i>Comprobante de Entrega
                        </h4>
                    </div>
                    <div class="card-body p-0">
                        <?php if(isset($entrega['comprobante_entrega']) && !empty($entrega['comprobante_entrega'])): ?>
                            <div style="height: 500px;">
                                <iframe src="<?= base_url('uploads/comprobantes_entrega/' . $entrega['comprobante_entrega']) ?>" 
                                        style="width: 100%; height: 100%; border: none;"></iframe>
                            </div>
                            <div class="p-3">
                                <a href="<?= base_url('uploads/comprobantes_entrega/' . $entrega['comprobante_entrega']) ?>" 
                                   class="btn btn-block btn-success" download>
                                    <i class="fas fa-download mr-2"></i>Descargar comprobante
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="p-4 text-center">
                                <i class="fas fa-file-pdf text-danger fa-4x mb-3"></i>
                                <p>No hay comprobante de entrega disponible.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-info-circle mr-2"></i>Información Adicional
                        </h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Contrato
                                <span class="badge badge-primary">#<?= isset($entrega['idcontrato']) && !empty($entrega['idcontrato']) ? $entrega['idcontrato'] : 'N/A' ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Cotización
                                <span class="badge badge-info">#<?= isset($entrega['idcotizacion']) && !empty($entrega['idcotizacion']) ? $entrega['idcotizacion'] : 'N/A' ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Días de Postproducción
                                <span class="badge badge-info"><?= isset($entrega['dias_postproduccion']) ? abs($entrega['dias_postproduccion']) : '0' ?> días</span>
                            </li>
                        </ul>

                        <?php if(isset($entrega['estado']) && $entrega['estado'] == 'completada'): ?>
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-check-circle mr-2"></i>
                                <strong>Entrega Completada</strong><br>
                                Esta entrega fue completada satisfactoriamente.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Entrega Pendiente</strong><br>
                                Esta entrega aún está pendiente de completar.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para imprimir -->
<div class="modal fade" id="imprimirModal" tabindex="-1" aria-labelledby="imprimirModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="imprimirModalLabel">
                    <i class="fas fa-print mr-2"></i>Imprimir Detalle de Entrega #<?= $entrega['identregable'] ?>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="contenido-imprimir" style="padding: 20px; background: white;">
                    <!-- Header de la empresa -->
                    <div style="border-bottom: 3px solid #007bff; padding-bottom: 15px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-size: 24px; font-weight: bold; color: #007bff;">APISHUME EVENTOS</div>
                                <div style="color: #6c757d; font-size: 12px;">
                                    <strong>RUC:</strong> 20123456789<br>
                                    <strong>Dirección:</strong> Av. Principal 123, Lima, Perú<br>
                                    <strong>Teléfono:</strong> +51 1 234 5678 | <strong>Email:</strong> info@apishume.com
                                </div>
                            </div>
                            <div style="text-align: right; color: #6c757d; font-size: 12px;">
                                <strong>Fecha de impresión:</strong><br>
                                <?= date('d/m/Y H:i:s') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Título del documento -->
                    <div style="font-size: 20px; font-weight: bold; color: #333; margin: 20px 0; text-align: center; border: 2px solid #007bff; padding: 10px; background-color: #f8f9fa;">
                        <i class="fas fa-truck-loading"></i> DETALLE DE ENTREGA #<?= $entrega['identregable'] ?>
                    </div>

                    <!-- Información del Cliente -->
                    <div style="margin-bottom: 20px; border: 1px solid #dee2e6; border-radius: 5px;">
                        <h5 style="background-color: #007bff; color: white; padding: 8px 15px; font-weight: bold; margin: 0; border-radius: 5px 5px 0 0;">
                            <i class="fas fa-user-circle"></i> INFORMACIÓN DEL CLIENTE
                        </h5>
                        <div style="padding: 15px;">
                            <div style="display: flex; gap: 30px;">
                                <div style="flex: 1;">
                                    <div style="margin-bottom: 8px;"><strong>Nombre:</strong> <?= isset($entrega['nombre_cliente']) ? $entrega['nombre_cliente'] : 'No disponible' ?> <?= isset($entrega['apellido_cliente']) ? $entrega['apellido_cliente'] : '' ?></div>
                                    <div style="margin-bottom: 8px;"><strong>Documento:</strong> 
                                        <?php if(isset($entrega['tipodoc']) && !empty($entrega['tipodoc']) && isset($entrega['numerodoc']) && !empty($entrega['numerodoc'])): ?>
                                            <?= $entrega['tipodoc'] ?>: <?= $entrega['numerodoc'] ?>
                                        <?php else: ?>
                                            No disponible
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="flex: 1;">
                                    <div style="margin-bottom: 8px;"><strong>Teléfono:</strong> <?= isset($entrega['telprincipal']) && !empty($entrega['telprincipal']) ? $entrega['telprincipal'] : 'No disponible' ?></div>
                                    <div style="margin-bottom: 8px;"><strong>Dirección:</strong> <?= isset($entrega['direccion']) && !empty($entrega['direccion']) ? $entrega['direccion'] : 'No disponible' ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Servicio -->
                    <div style="margin-bottom: 20px; border: 1px solid #dee2e6; border-radius: 5px;">
                        <h5 style="background-color: #007bff; color: white; padding: 8px 15px; font-weight: bold; margin: 0; border-radius: 5px 5px 0 0;">
                            <i class="fas fa-briefcase"></i> INFORMACIÓN DEL SERVICIO
                        </h5>
                        <div style="padding: 15px;">
                            <div style="display: flex; gap: 30px;">
                                <div style="flex: 1;">
                                    <div style="margin-bottom: 8px;"><strong>Servicio:</strong> <?= isset($entrega['servicio']) && !empty($entrega['servicio']) ? $entrega['servicio'] : 'No disponible' ?></div>
                                    <div style="margin-bottom: 8px;"><strong>Descripción:</strong> <?= isset($entrega['descripcion_servicio']) && !empty($entrega['descripcion_servicio']) ? $entrega['descripcion_servicio'] : 'No disponible' ?></div>
                                </div>
                                <div style="flex: 1;">
                                    <div style="margin-bottom: 8px;"><strong>Fecha Servicio:</strong> <?= isset($entrega['fechahoraservicio']) && !empty($entrega['fechahoraservicio']) ? date('d/m/Y H:i', strtotime($entrega['fechahoraservicio'])) : 'No disponible' ?></div>
                                    <div style="margin-bottom: 8px;"><strong>Cantidad:</strong> <?= isset($entrega['cantidad']) ? $entrega['cantidad'] : 'No disponible' ?></div>
                                    <div style="margin-bottom: 8px;"><strong>Precio:</strong> <?= isset($entrega['precio']) ? 'S/ ' . number_format($entrega['precio'], 2) : 'No disponible' ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Entrega -->
                    <div style="margin-bottom: 20px; border: 1px solid #dee2e6; border-radius: 5px;">
                        <h5 style="background-color: #007bff; color: white; padding: 8px 15px; font-weight: bold; margin: 0; border-radius: 5px 5px 0 0;">
                            <i class="fas fa-truck"></i> INFORMACIÓN DE ENTREGA
                        </h5>
                        <div style="padding: 15px;">
                            <div style="display: flex; gap: 30px;">
                                <div style="flex: 1;">
                                    <div style="margin-bottom: 8px;"><strong>Responsable:</strong> 
                                        <?= isset($entrega['nombre_entrega']) && !empty($entrega['nombre_entrega']) && $entrega['nombre_entrega'] !== 'Sin nombre' ? 
                                            $entrega['nombre_entrega'] . ' ' . $entrega['apellido_entrega'] : 
                                            session()->get('usuario_nombre') ?? 'No disponible' ?>
                                    </div>
                                    <div style="margin-bottom: 8px;"><strong>Documento Responsable:</strong> 
                                        <?= isset($entrega['numerodoc_entrega']) && !empty($entrega['numerodoc_entrega']) && $entrega['numerodoc_entrega'] !== 'No disponible' ? 
                                            'DNI: ' . $entrega['numerodoc_entrega'] : 'No disponible' ?>
                                    </div>
                                </div>
                                <div style="flex: 1;">
                                    <div style="margin-bottom: 8px;"><strong>Fecha/Hora Entrega:</strong> <?= isset($entrega['fechahoraentrega']) && !empty($entrega['fechahoraentrega']) ? date('d/m/Y H:i', strtotime($entrega['fechahoraentrega'])) : 'No disponible' ?></div>
                                    <div style="margin-bottom: 8px;"><strong>Estado:</strong> 
                                        <span style="padding: 5px 10px; border-radius: 15px; font-weight: bold; font-size: 11px; <?= (isset($entrega['estado']) && $entrega['estado'] == 'completada') ? 'background-color: #28a745; color: white;' : 'background-color: #ffc107; color: #212529;' ?>">
                                            <?= isset($entrega['estado_visual']) ? $entrega['estado_visual'] : (isset($entrega['estado']) && $entrega['estado'] == 'completada' ? '✅ ENTREGADO' : '⏳ EN POSTPRODUCCIÓN') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-bottom: 8px;"><strong>Formato Entrega:</strong> <?= isset($entrega['observaciones']) && !empty($entrega['observaciones']) ? $entrega['observaciones'] : 'No disponible' ?></div>
                            <?php if(isset($entrega['estado']) && $entrega['estado'] == 'completada' && isset($entrega['fecha_real_entrega']) && !empty($entrega['fecha_real_entrega'])): ?>
                            <div style="margin-bottom: 8px;"><strong>Fecha Real Entrega:</strong> <?= date('d/m/Y H:i', strtotime($entrega['fecha_real_entrega'])) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div style="margin-bottom: 20px; border: 1px solid #dee2e6; border-radius: 5px;">
                        <h5 style="background-color: #007bff; color: white; padding: 8px 15px; font-weight: bold; margin: 0; border-radius: 5px 5px 0 0;">
                            <i class="fas fa-info-circle"></i> INFORMACIÓN ADICIONAL
                        </h5>
                        <div style="padding: 15px;">
                            <div style="display: flex; gap: 30px;">
                                <div style="flex: 1;">
                                    <div style="margin-bottom: 8px;"><strong>Contrato:</strong> #<?= isset($entrega['idcontrato']) && !empty($entrega['idcontrato']) ? $entrega['idcontrato'] : 'N/A' ?></div>
                                </div>
                                <div style="flex: 1;">
                                    <div style="margin-bottom: 8px;"><strong>Cotización:</strong> #<?= isset($entrega['idcotizacion']) && !empty($entrega['idcotizacion']) ? $entrega['idcotizacion'] : 'N/A' ?></div>
                                </div>
                                <div style="flex: 1;">
                                    <div style="margin-bottom: 8px;"><strong>Días Postproducción:</strong> <?= isset($entrega['dias_postproduccion']) ? $entrega['dias_postproduccion'] : '0' ?> días</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de firmas -->
                    <div style="margin-top: 40px; border-top: 2px solid #007bff; padding-top: 20px;">
                        <div style="display: flex; gap: 50px;">
                            <div style="flex: 1; text-align: center;">
                                <strong>FIRMA DEL RESPONSABLE DE ENTREGA</strong>
                                <div style="border: 1px solid #dee2e6; height: 80px; margin-top: 10px;"></div>
                                <small style="color: #6c757d;">
                                    <?= isset($entrega['nombre_entrega']) && !empty($entrega['nombre_entrega']) && $entrega['nombre_entrega'] !== 'Sin nombre' ? 
                                        $entrega['nombre_entrega'] . ' ' . $entrega['apellido_entrega'] : 
                                        session()->get('usuario_nombre') ?? 'Responsable' ?>
                                </small>
                            </div>
                            <div style="flex: 1; text-align: center;">
                                <strong>FIRMA DEL CLIENTE</strong>
                                <div style="border: 1px solid #dee2e6; height: 80px; margin-top: 10px;"></div>
                                <small style="color: #6c757d;"><?= isset($entrega['nombre_cliente']) ? $entrega['nombre_cliente'] : 'Cliente' ?> <?= isset($entrega['apellido_cliente']) ? $entrega['apellido_cliente'] : '' ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div style="margin-top: 30px; font-size: 10px; color: #6c757d; text-align: center; border-top: 1px solid #dee2e6; padding-top: 10px;">
                        <p><strong>APISHUME EVENTOS</strong> - Sistema de Gestión de Entregas</p>
                        <p>Este documento fue generado automáticamente el <?= date('d/m/Y H:i:s') ?></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Cerrar
                </button>
                <button type="button" class="btn btn-primary" onclick="imprimirDirecto()">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
</style>

<script>
function imprimirEntrega() {
    // Crear ventana de impresión
    var ventana = window.open('', '_blank', 'width=800,height=600');
    
    // Contenido HTML para imprimir
    var contenido = '<!DOCTYPE html><html><head><title>Entrega #<?= $entrega["identregable"] ?> - APISHUME EVENTOS</title><style>body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; margin: 20px; color: #333; } .header { text-align: center; border-bottom: 3px solid #007bff; padding-bottom: 20px; margin-bottom: 30px; } .company-name { font-size: 28px; font-weight: bold; color: #007bff; margin-bottom: 10px; } .document-title { font-size: 24px; font-weight: bold; color: #333; margin: 20px 0; border: 2px solid #007bff; padding: 15px; background-color: #f8f9fa; } .section { margin-bottom: 25px; border: 1px solid #dee2e6; border-radius: 5px; } .section-header { background-color: #007bff; color: white; padding: 10px 15px; font-weight: bold; margin: 0; } .section-body { padding: 20px; } .info-row { display: flex; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 8px; } .info-label { font-weight: bold; width: 200px; flex-shrink: 0; } .info-value { flex: 1; } .status-badge { padding: 8px 15px; border-radius: 20px; font-weight: bold; font-size: 12px; } .status-completed { background-color: #28a745; color: white; } .status-pending { background-color: #ffc107; color: #212529; } .signatures { margin-top: 40px; border-top: 2px solid #007bff; padding-top: 30px; } .signature-box { border: 1px solid #dee2e6; height: 100px; margin: 10px 0; } .footer { margin-top: 30px; font-size: 12px; color: #6c757d; text-align: center; border-top: 1px solid #dee2e6; padding-top: 15px; } @media print { body { margin: 0; padding: 15px; } .no-print { display: none !important; } }</style></head><body><div class="header"><div class="company-name">APISHUME EVENTOS</div><div>RUC: 20123456789 | Av. Principal 123, Lima, Perú</div><div>Teléfono: +51 1 234 5678 | Email: info@apishume.com</div><div style="margin-top: 15px; font-size: 12px; color: #666;">Fecha de impresión: <?= date("d/m/Y H:i:s") ?></div></div><div class="document-title">DETALLE DE ENTREGA #<?= $entrega["identregable"] ?></div><div class="section"><h3 class="section-header">INFORMACIÓN DEL CLIENTE</h3><div class="section-body"><div class="info-row"><div class="info-label">Nombre:</div><div class="info-value"><?= isset($entrega["nombre_cliente"]) ? $entrega["nombre_cliente"] : "No disponible" ?> <?= isset($entrega["apellido_cliente"]) ? $entrega["apellido_cliente"] : "" ?></div></div><div class="info-row"><div class="info-label">Documento:</div><div class="info-value"><?php if(isset($entrega["tipodoc"]) && !empty($entrega["tipodoc"]) && isset($entrega["numerodoc"]) && !empty($entrega["numerodoc"])): ?><?= $entrega["tipodoc"] ?>: <?= $entrega["numerodoc"] ?><?php else: ?>No disponible<?php endif; ?></div></div><div class="info-row"><div class="info-label">Teléfono:</div><div class="info-value"><?= isset($entrega["telprincipal"]) && !empty($entrega["telprincipal"]) ? $entrega["telprincipal"] : "No disponible" ?></div></div><div class="info-row"><div class="info-label">Dirección:</div><div class="info-value"><?= isset($entrega["direccion"]) && !empty($entrega["direccion"]) ? $entrega["direccion"] : "No disponible" ?></div></div></div></div><div class="section"><h3 class="section-header">INFORMACIÓN DEL SERVICIO</h3><div class="section-body"><div class="info-row"><div class="info-label">Servicio:</div><div class="info-value"><?= isset($entrega["servicio"]) && !empty($entrega["servicio"]) ? $entrega["servicio"] : "No disponible" ?></div></div><div class="info-row"><div class="info-label">Descripción:</div><div class="info-value"><?= isset($entrega["descripcion_servicio"]) && !empty($entrega["descripcion_servicio"]) ? $entrega["descripcion_servicio"] : "No disponible" ?></div></div><div class="info-row"><div class="info-label">Fecha Servicio:</div><div class="info-value"><?= isset($entrega["fechahoraservicio"]) && !empty($entrega["fechahoraservicio"]) ? date("d/m/Y H:i", strtotime($entrega["fechahoraservicio"])) : "No disponible" ?></div></div><div class="info-row"><div class="info-label">Cantidad:</div><div class="info-value"><?= isset($entrega["cantidad"]) ? $entrega["cantidad"] : "No disponible" ?></div></div><div class="info-row"><div class="info-label">Precio:</div><div class="info-value"><?= isset($entrega["precio"]) ? "S/ " . number_format($entrega["precio"], 2) : "No disponible" ?></div></div></div></div><div class="section"><h3 class="section-header">INFORMACIÓN DE ENTREGA</h3><div class="section-body"><div class="info-row"><div class="info-label">Responsable:</div><div class="info-value"><?= isset($entrega["nombre_entrega"]) && !empty($entrega["nombre_entrega"]) && $entrega["nombre_entrega"] !== "Sin nombre" ? $entrega["nombre_entrega"] . " " . $entrega["apellido_entrega"] : session()->get("usuario_nombre") ?? "No disponible" ?></div></div><div class="info-row"><div class="info-label">Fecha/Hora Entrega:</div><div class="info-value"><?= isset($entrega["fechahoraentrega"]) && !empty($entrega["fechahoraentrega"]) ? date("d/m/Y H:i", strtotime($entrega["fechahoraentrega"])) : "No disponible" ?></div></div><div class="info-row"><div class="info-label">Formato Entrega:</div><div class="info-value"><?= isset($entrega["observaciones"]) && !empty($entrega["observaciones"]) ? $entrega["observaciones"] : "No disponible" ?></div></div><div class="info-row"><div class="info-label">Estado:</div><div class="info-value"><span class="status-badge <?= (isset($entrega["estado"]) && $entrega["estado"] == "completada") ? "status-completed" : "status-pending" ?>"><?= isset($entrega["estado_visual"]) ? $entrega["estado_visual"] : (isset($entrega["estado"]) && $entrega["estado"] == "completada" ? "✅ ENTREGADO" : "⏳ EN POSTPRODUCCIÓN") ?></span></div></div></div></div><div class="section"><h3 class="section-header">INFORMACIÓN ADICIONAL</h3><div class="section-body"><div class="info-row"><div class="info-label">Contrato:</div><div class="info-value">#<?= isset($entrega["idcontrato"]) && !empty($entrega["idcontrato"]) ? $entrega["idcontrato"] : "N/A" ?></div></div><div class="info-row"><div class="info-label">Cotización:</div><div class="info-value">#<?= isset($entrega["idcotizacion"]) && !empty($entrega["idcotizacion"]) ? $entrega["idcotizacion"] : "N/A" ?></div></div><?php if(isset($entrega["dias_postproduccion"])): ?><div class="info-row"><div class="info-label">Días Postproducción:</div><div class="info-value"><?= $entrega["dias_postproduccion"] ?> días</div></div><?php endif; ?></div></div><div class="signatures"><div style="display: flex; gap: 50px;"><div style="flex: 1; text-align: center;"><strong>FIRMA DEL RESPONSABLE DE ENTREGA</strong><div class="signature-box"></div><div style="font-size: 12px; color: #666; margin-top: 5px;"><?= isset($entrega["nombre_entrega"]) && !empty($entrega["nombre_entrega"]) && $entrega["nombre_entrega"] !== "Sin nombre" ? $entrega["nombre_entrega"] . " " . $entrega["apellido_entrega"] : session()->get("usuario_nombre") ?? "Responsable" ?></div></div><div style="flex: 1; text-align: center;"><strong>FIRMA DEL CLIENTE</strong><div class="signature-box"></div><div style="font-size: 12px; color: #666; margin-top: 5px;"><?= isset($entrega["nombre_cliente"]) ? $entrega["nombre_cliente"] : "Cliente" ?> <?= isset($entrega["apellido_cliente"]) ? $entrega["apellido_cliente"] : "" ?></div></div></div></div><div class="footer"><p><strong>APISHUME EVENTOS</strong> - Sistema de Gestión de Entregas</p><p>Este documento fue generado automáticamente el <?= date("d/m/Y H:i:s") ?></p></div><div class="no-print" style="text-align: center; margin-top: 30px;"><button onclick="window.print()" style="padding: 15px 30px; background: #007bff; color: white; border: none; border-radius: 5px; font-size: 16px; margin-right: 10px;">🖨️ Imprimir</button><button onclick="window.close()" style="padding: 15px 30px; background: #6c757d; color: white; border: none; border-radius: 5px; font-size: 16px;">✕ Cerrar</button></div></body></html>';
    
    ventana.document.write(contenido);
    ventana.document.close();
    
    // Auto-imprimir después de cargar
    setTimeout(function() {
        ventana.print();
    }, 1000);
}
</script>

<?= $footer; ?>