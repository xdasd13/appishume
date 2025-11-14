<?= $header ?>

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <!-- Título -->
                        <div>
                            <h3 class="mb-0 text-dark fw-bold" style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #1f2937;">
                                <i class="fas fa-history" style="color: #eb8f06ff; margin-right: 8px;"></i>
                                Historial de Actividades
                            </h3>
                            <p class="text-muted mb-0 mt-1" style="font-family: 'Poppins', sans-serif; color: #6b7280;">Registro de cambios en el tablero Kanban</p>
                        </div>

                        <!-- Buscador -->
                        <div class="d-flex gap-2 mt-3 mt-md-0">
                            <select id="filtroUsuario" class="form-select" style="min-width: 250px; font-family: 'Poppins', sans-serif; border: 1px solid #e5e7eb; border-radius: 8px;">
                                <option value="todos">Todos los usuarios</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario->idusuario ?>" <?= $filtro_usuario == $usuario->idusuario ? 'selected' : '' ?>>
                                        <?= esc($usuario->nombre_completo) ?> (<?= $usuario->total_cambios ?> cambios)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn" style="background-color: #eb8f06ff; color: white; font-family: 'Poppins', sans-serif; font-weight: 600; border: none; border-radius: 8px;" onclick="buscarHistorial()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tabla de Historial -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <!-- Loading -->
                    <div id="loading" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Buscando actividades...</p>
                    </div>

                    <!-- Tabla -->
                    <div id="tabla-container" class="table-responsive">
                        <table class="table mb-0" style="font-family: 'Poppins', sans-serif;">
                            <thead style="background-color: #000000ff; border-bottom: 2px solid #ff6b35;">
                                <tr>
                                    <th width="10%" style="font-weight: 700; color: #ffffff; padding: 12px 15px;">Fecha</th>
                                    <th width="8%" style="font-weight: 700; color: #ffffff; padding: 12px 15px;">Hora</th>
                                    <th width="10%" style="font-weight: 700; color: #ffffff; padding: 12px 15px;">Día</th>
                                    <th width="15%" style="font-weight: 700; color: #ffffff; padding: 12px 15px;">Usuario</th>
                                    <th width="57%" style="font-weight: 700; color: #ffffff; padding: 12px 15px;">Cambio Realizado</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-body">
                                <?php if (empty($historial)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5" style="border: none; color: #000000ff; padding: 2rem 1rem;">
                                            <i class="fas fa-inbox fa-3x mb-3" style="color: #d1d5db;"></i>
                                            <p class="mb-0">No se encontraron actividades</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($historial as $item): ?>
                                        <tr style="border-bottom: 1px solid #f3f4f6; background-color: #ffffff;">
                                            <!-- Fecha -->
                                            <td class="align-middle" style="padding: 12px 15px; color: #1f2937; font-weight: 500; font-family: 'Poppins', sans-serif;">
                                                <span>
                                                    <?= date('d/m/Y', strtotime($item->fecha)) ?>
                                                </span>
                                            </td>

                                            <!-- Hora -->
                                            <td class="align-middle" style="padding: 12px 15px;">
                                                <span class="badge" style="background-color: #fd8700ff; color: white; font-family: 'Poppins', sans-serif; font-weight: 600; padding: 4px 10px;">
                                                    <?= date('H:i:s', strtotime($item->fecha)) ?>
                                                </span>
                                            </td>

                                            <!-- Día -->
                                            <td class="align-middle" style="padding: 12px 15px; color: #000000ff; font-family: 'Poppins', sans-serif;">
                                                <span>
                                                    <?= obtenerNombreDia($item->fecha) ?>
                                                </span>
                                            </td>

                                            <!-- Usuario -->
                                            <td class="align-middle" style="padding: 12px 15px;">
                                                <div class="d-flex align-items-center">
                                                    <span style="font-weight: 500; color: #1f2937; font-family: 'Poppins', sans-serif;">
                                                        <?= esc($item->usuario_nombre) ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- Cambio Realizado -->
                                            <td class="align-middle" style="padding: 12px 15px; color: #374151; font-family: 'Poppins', sans-serif;">
                                                <?= generarTextoAccion($item) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos CSS -->
<style>
    /* Tabla responsiva */
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
    }


    /* Badges de estado */
    .badge-estado {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        font-family: 'Poppins', sans-serif;
    }

    .badge-pendiente {
        background-color: #fef08a;
        color: #854d0e;
    }

    .badge-proceso {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .badge-completado {
        background-color: #d1fae5;
        color: #065f46;
    }

    /* Scrollbar personalizado */
    .table-responsive::-webkit-scrollbar {
        width: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #0055ffff;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #7c3aed;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #6d28d9;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .table thead {
            display: none;
        }

        .table tbody tr {
            display: block;
            margin-bottom: 15px;
            border: 1px solid #ff0000ff;
            border-radius: 8px;
        }

        .table tbody td {
            display: block;
            text-align: right;
            padding: 10px 15px;
            border: none;
        }

        .table tbody td:before {
            content: attr(data-label);
            float: left;
            font-weight: bold;
            color: #6c757d;
        }
    }
</style>

<!-- JavaScript -->
<script>
    /**
     * Buscar historial con filtro de usuario
     */
    function buscarHistorial() {
        const loadingElement = document.getElementById('loading');
        const tablaContainer = document.getElementById('tabla-container');
        const filtroUsuario = document.getElementById('filtroUsuario').value;

        // Mostrar loading
        loadingElement.style.display = 'block';
        tablaContainer.style.opacity = '0.5';

        // Hacer petición AJAX
        fetch('<?= base_url('historial/buscar') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                usuario: filtroUsuario,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
        })
            .then(response => response.json())
            .then(data => {
                loadingElement.style.display = 'none';
                tablaContainer.style.opacity = '1';

                if (data.success) {
                    actualizarTabla(data.historial);
                } else {
                    mostrarError(data.mensaje);
                }
            })
            .catch(error => {
                loadingElement.style.display = 'none';
                tablaContainer.style.opacity = '1';
                console.error('Error:', error);
                mostrarError('Error de conexión');
            });
    }

    /**
     * Actualizar contenido de la tabla
     */
    function actualizarTabla(historial) {
        const tablaBody = document.getElementById('tabla-body');

        if (historial.length === 0) {
            tablaBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No se encontraron actividades</p>
                </td>
            </tr>
        `;
            return;
        }

        let html = '';
        historial.forEach(item => {
            const inicial = item.usuario.charAt(0).toUpperCase();
            html += `
            <tr>
                <td class="align-middle">
                    <span class="text-dark fw-medium">${item.fecha}</span>
                </td>
                <td class="align-middle">
                    <span class="badge bg-secondary">${item.hora}</span>
                </td>
                <td class="align-middle">
                    <span class="text-muted">${item.dia}</span>
                </td>
                <td class="align-middle">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-2">${inicial}</div>
                        <span class="fw-medium text-dark">${item.usuario}</span>
                    </div>
                </td>
                <td class="align-middle">${item.accion}</td>
            </tr>
        `;
        });

        tablaBody.innerHTML = html;
    }

    /**
     * Mostrar mensaje de error
     */
    function mostrarError(mensaje) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje,
            confirmButtonColor: '#ffc107'
        });
    }
</script>

<?php
/**
 * Obtener nombre del día de la semana en español
 */
function obtenerNombreDia($fecha)
{
    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $numeroDia = date('w', strtotime($fecha));
    return $dias[$numeroDia];
}

/**
 * Generar texto descriptivo del cambio realizado
 */
function generarTextoAccion($item)
{
    $html = '<div class="d-flex flex-column">';

    switch ($item->accion) {
        case 'cambiar_estado':
            $badgeAnterior = obtenerBadgeEstado($item->estado_anterior);
            $badgeNuevo = obtenerBadgeEstado($item->estado_nuevo);

            $html .= '<div class="mb-1">';
            $html .= '<strong>Cambió estado:</strong> ' . esc($item->equipo_descripcion);
            $html .= '</div>';
            $html .= '<div class="d-flex align-items-center gap-2">';
            $html .= $badgeAnterior;
            $html .= '<i class="fas fa-arrow-right text-muted"></i>';
            $html .= $badgeNuevo;
            $html .= '</div>';
            $html .= '<small class="text-muted mt-1">';
            $html .= '<i class="fas fa-briefcase"></i> ' . esc($item->servicio);
            $html .= ' | <i class="fas fa-user"></i> ' . esc($item->cliente_nombre);
            $html .= '</small>';
            break;

        case 'crear':
            $html .= '<div class="mb-1">';
            $html .= '<strong>Creó nuevo equipo:</strong> ' . esc($item->equipo_descripcion);
            $html .= '</div>';
            $html .= '<small class="text-muted">';
            $html .= '<i class="fas fa-briefcase"></i> ' . esc($item->servicio);
            $html .= '</small>';
            break;

        case 'reasignar':
            $html .= '<div class="mb-1">';
            $html .= '<strong>Reasignó equipo:</strong> ' . esc($item->equipo_descripcion);
            $html .= '</div>';
            $html .= '<small class="text-muted">';
            $html .= '<i class="fas fa-briefcase"></i> ' . esc($item->servicio);
            $html .= '</small>';
            break;

        default:
            $html .= ucfirst($item->accion);
    }

    $html .= '</div>';
    return $html;
}

/**
 * Obtener badge según el estado
 */
function obtenerBadgeEstado($estado)
{
    $clases = [
        'Pendiente' => 'badge-pendiente',
        'En Proceso' => 'badge-proceso',
        'Completado' => 'badge-completado',
        'Programado' => 'badge-pendiente'
    ];

    $clase = $clases[$estado] ?? 'badge-secondary';
    $icono = $estado === 'Completado' ? '<i class="fas fa-check-circle"></i> ' : '';

    return '<span class="badge-estado ' . $clase . '">' . $icono . esc($estado) . '</span>';
}
?>

<?= $footer ?>