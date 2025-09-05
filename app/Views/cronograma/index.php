<?= $header ?>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
<style>
    :root {
        --color-primary: #ff9800;
        --color-primary-dark: #e68900;
        --color-primary-light: #ffb74d;
        --color-dark:rgb(255, 255, 255);
        --color-surface: #2d2d2d;
        --color-surface-light: #3d3d3d;
        --color-white: #ffffff;
        --color-text-primary: #ffffff;
        --color-text-secondary: #cccccc;
        --color-text-muted: #999999;
        --color-border: #404040;
        --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.3);
        --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.4);
        --border-radius: 8px;
        --spacing-sm: 8px;
        --spacing-md: 16px;
        --spacing-lg: 24px;
        --spacing-xl: 32px;
    }

    body {
        background-color: var(--color-dark);
        color: var(--color-text-primary);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
        font-size: 14px;
        line-height: 1.5;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--spacing-lg);
    }

    /* Typography */
    h1 {
        font-size: 28px;
        font-weight: 600;
        color: var(--color-text-primary);
        margin: 0 0 var(--spacing-lg) 0;
    }

    h2 {
        font-size: 20px;
        font-weight: 600;
        color: var(--color-text-primary);
        margin: 0 0 var(--spacing-md) 0;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
    }

    .stat-card {
        background-color: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius);
        padding: var(--spacing-lg);
        box-shadow: var(--shadow-sm);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-content {
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        background-color: var(--color-primary);
        color: var(--color-white);
        border-radius: var(--border-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .stat-info h3 {
        font-size: 32px;
        font-weight: 700;
        color: var(--color-text-primary);
        margin: 0;
        line-height: 1;
    }

    .stat-info p {
        font-size: 14px;
        color: var(--color-text-secondary);
        margin: 4px 0 0 0;
        font-weight: 500;
    }

    /* Cards */
    .card {
        background-color: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        margin-bottom: var(--spacing-xl);
    }

    .card-header {
        background-color: var(--color-primary);
        color: var(--color-white);
        padding: var(--spacing-md) var(--spacing-lg);
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .card-header h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .card-header i {
        font-size: 16px;
    }

    .card-body {
        padding: var(--spacing-lg);
    }

    /* Calendar */
    .fc {
        background: transparent;
    }

    .fc-toolbar {
        background-color: var(--color-surface-light);
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius);
        padding: var(--spacing-md);
        margin-bottom: var(--spacing-md);
    }

    .fc .fc-button-primary {
        background-color: var(--color-primary);
        border-color: var(--color-primary);
        color: var(--color-white);
        font-weight: 500;
        font-size: 13px;
        padding: 6px 12px;
        border-radius: 4px;
    }

    .fc .fc-button-primary:hover:not(:disabled) {
        background-color: var(--color-primary-dark);
        border-color: var(--color-primary-dark);
    }

    .fc .fc-button-primary:focus {
        box-shadow: 0 0 0 2px rgba(255, 152, 0, 0.25);
    }

    .fc-daygrid-event {
        background-color: var(--color-primary);
        border-color: var(--color-primary);
        color: var(--color-white);
        font-size: 12px;
        border-radius: 4px;
        padding: 2px 4px;
    }

    .fc-toolbar-title {
        color: var(--color-text-primary);
        font-size: 18px;
        font-weight: 600;
    }

    .fc-col-header-cell {
        background-color: var(--color-surface-light);
    }

    .fc-day-today {
        background-color: rgba(255, 152, 0, 0.1);
    }

    /* Table */
    .table-responsive {
        overflow-x: auto;
        border-radius: var(--border-radius);
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
        background-color: transparent;
    }

    .table th {
        background-color: var(--color-primary);
        color: var(--color-white);
        font-weight: 600;
        font-size: 13px;
        padding: 12px 16px;
        text-align: left;
        border: none;
    }

    .table th:first-child {
        border-radius: var(--border-radius) 0 0 0;
    }

    .table th:last-child {
        border-radius: 0 var(--border-radius) 0 0;
    }

    .table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--color-border);
        color: var(--color-text-primary);
        font-size: 13px;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: var(--color-surface-light);
    }

    .table tbody tr:last-child td:first-child {
        border-radius: 0 0 0 var(--border-radius);
    }

    .table tbody tr:last-child td:last-child {
        border-radius: 0 0 var(--border-radius) 0;
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 4px;
        line-height: 1;
    }

    .badge-programado {
        background-color: #2196f3;
        color: var(--color-white);
    }

    .badge-en-proceso {
        background-color: var(--color-primary);
        color: var(--color-white);
    }

    .badge-completado {
        background-color: #4caf50;
        color: var(--color-white);
    }

    .badge-pendiente {
        background-color: #757575;
        color: var(--color-white);
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-sm);
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        border-radius: 4px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        line-height: 1;
    }

    .btn-orange {
        background-color: var(--color-primary);
        color: var(--color-white);
        border-color: var(--color-primary);
    }

    .btn-orange:hover {
        background-color: var(--color-primary-dark);
        border-color: var(--color-primary-dark);
        color: var(--color-white);
        text-decoration: none;
    }

    .btn-outline-white {
        background-color: transparent;
        color: var(--color-text-secondary);
        border-color: var(--color-border);
    }

    .btn-outline-white:hover {
        background-color: var(--color-surface-light);
        color: var(--color-text-primary);
        text-decoration: none;
    }

    .btn + .btn {
        margin-left: var(--spacing-sm);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            padding: var(--spacing-md);
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: var(--spacing-md);
        }

        h1 {
            font-size: 24px;
        }

        .card-header h2 {
            font-size: 16px;
        }

        .card-body {
            padding: var(--spacing-md);
        }

        .table th,
        .table td {
            padding: 8px 12px;
            font-size: 12px;
        }

        .btn {
            font-size: 11px;
            padding: 4px 8px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            font-size: 20px;
        }

        .stat-info h3 {
            font-size: 28px;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            gap: var(--spacing-sm);
        }

        .stat-content {
            flex-direction: column;
            text-align: center;
            gap: var(--spacing-sm);
        }

        .btn {
            display: block;
            text-align: center;
            margin-bottom: 4px;
        }

        .btn + .btn {
            margin-left: 0;
        }
    }

    h1{
        color: #1a1a1a;
    }
</style>

<div class="container">
    <div style="display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-clipboard-list" style="font-size: 24px;"></i>
        <h1 style="margin: 0;">Cronograma de Servicios</h1>
    </div>
    <br>

    <!-- ESTADÍSTICAS RÁPIDAS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $servicios_count ?? 0 ?></h3>
                    <p>Servicios activos</p>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $equipos ?? 0 ?></h3>
                    <p>Equipos asignados</p>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $tecnicos ?? 0 ?></h3>
                    <p>Técnicos disponibles</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendario -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-calendar-alt"></i>
            <h2>Cronograma de Servicios</h2>
        </div>
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Lista de próximos servicios -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i>
            <h2>Próximos Servicios</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar-day"></i> Fecha</th>
                            <th><i class="fas fa-user"></i> Cliente</th>
                            <th><i class="fas fa-briefcase"></i> Servicio</th>
                            <th><i class="fas fa-map-marker-alt"></i> Lugar</th>
                            <th><i class="fas fa-info-circle"></i> Estado</th>
                            <th><i class="fas fa-cogs"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proximos as $servicio): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($servicio->fechahoraservicio)) ?></td>
                            <td><?= $servicio->cliente ?></td>
                            <td><?= $servicio->servicio ?></td>
                            <td><?= $servicio->direccion ?></td>
                            <td>
                                <?php
                                    $estado = strtolower($servicio->estado);
                                    $badgeClass = 'badge-programado';
                                    if ($estado == 'en proceso') $badgeClass = 'badge-en-proceso';
                                    elseif ($estado == 'completado') $badgeClass = 'badge-completado';
                                    elseif ($estado == 'pendiente') $badgeClass = 'badge-pendiente';
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst($servicio->estado) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= base_url('equipos/por-servicio/' . $servicio->idserviciocontratado) ?>" class="btn btn-orange">
                                    <i class="fas fa-users"></i> Equipos
                                </a>
                                <a href="<?= base_url('servicios/editar/' . $servicio->idserviciocontratado) ?>" class="btn btn-outline-white">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '<?= base_url('cronograma/getEventos') ?>',
        eventClick: function(info) {
            const evento = info.event.extendedProps;
            Swal.fire({
                icon: 'info',
                title: info.event.title,
                html: `<b>Fecha:</b> ${info.event.start.toLocaleString()}<br>
                       <b>Lugar:</b> ${evento.direccion}<br>
                       <b>Teléfono:</b> ${evento.telefono}`,
                confirmButtonColor: '#ff9800'
            });
        },
        dateClick: function(info) {
            Swal.fire({
                title: 'Crear servicio',
                text: `¿Deseas crear un servicio para el ${info.dateStr}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ff9800',
                cancelButtonColor: '#6C757D',
                confirmButtonText: 'Sí, crear',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('servicios/crear') ?>?fecha=' + info.dateStr;
                }
            });
        }
    });
    calendar.render();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?= $footer ?>