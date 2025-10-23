<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/cronograma-proyectos.css') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />



<div class="container">
    <div class="dashboard-header">
        <div class="dashboard-title">
            <i class="fa-solid fa-clipboard-list dashboard-icon"></i>
            <h1>Proyectos Activos</h1>
        </div>
        <p class="dashboard-subtitle">Gestiona y supervisa todos tus proyectos audiovisuales en tiempo real</p>
    </div>
    
    <div class="projects-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-tasks"></i>Proyectos en Curso
                <span class="projects-count"><?= count($proyectos) ?></span>
            </h2>
            <a href="<?= base_url('proyectos') ?>" class="view-all">
                Ver todos <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="projects-grid">
            <?php if (!empty($proyectos)): ?>
                <?php foreach ($proyectos as $proyecto): ?>
                <div class="project-card">
                    <div class="project-header">
                        <span class="project-status status-activo">
                            <?= $proyecto['total_servicios'] ?> Servicio<?= $proyecto['total_servicios'] > 1 ? 's' : '' ?>
                        </span>
                        <h3 class="project-title"><?= esc($proyecto['cliente']) ?></h3>
                        <p class="project-client">
                            <i class="fas fa-phone me-1"></i><?= esc($proyecto['telefono_cliente'] ?? 'Sin teléfono') ?>
                        </p>
                    </div>
                    
                    <div class="project-body">
                        <!-- Lista de servicios contratados -->
                        <div class="project-detail">
                            <div class="detail-icon">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Servicios Contratados</div>
                                <div class="detail-value">
                                    <?php foreach ($proyecto['servicios'] as $index => $servicio): ?>
                                        <div class="servicio-item" style="margin-bottom: 8px; padding: 8px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid 
                                            <?php 
                                                if ($servicio['estado'] == 'Completado') echo '#27AE60';
                                                elseif ($servicio['estado'] == 'En Proceso') echo '#E67E22';
                                                elseif ($servicio['estado'] == 'Programado') echo '#FF9900';
                                                else echo '#7F8C8D';
                                            ?>;">
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <strong style="color: #2c3e50; font-size: 0.95rem;">
                                                    <i class="fas fa-check-circle me-1" style="font-size: 0.8rem;"></i>
                                                    <?= esc($servicio['servicio']) ?>
                                                </strong>
                                                <span class="badge" style="font-size: 0.7rem; padding: 3px 8px;
                                                    <?php 
                                                        if ($servicio['estado'] == 'Completado') echo 'background: #27AE60;';
                                                        elseif ($servicio['estado'] == 'En Proceso') echo 'background: #E67E22;';
                                                        elseif ($servicio['estado'] == 'Programado') echo 'background: #FF9900;';
                                                        else echo 'background: #7F8C8D;';
                                                    ?> color: white;">
                                                    <?= esc($servicio['estado']) ?>
                                                </span>
                                            </div>
                                            <small style="color: #7f8c8d; display: block; margin-top: 4px;">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?= date('d/m/Y H:i', strtotime($servicio['fechahoraservicio'])) ?>
                                            </small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="project-detail">
                            <div class="detail-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Fecha Más Próxima</div>
                                <div class="detail-value"><?= date('d/m/Y H:i', strtotime($proyecto['fecha_mas_proxima'])) ?></div>
                            </div>
                        </div>
                        
                        <div class="project-detail">
                            <div class="detail-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Ubicación Principal</div>
                                <div class="detail-value"><?= esc($proyecto['direccion_principal']) ?></div>
                            </div>
                        </div>
                        
                        <div class="progress-container">
                            <div class="progress-info">
                                <span class="progress-label">Progreso Promedio</span>
                                <span class="progress-percentage"><?= $proyecto['progreso_promedio'] ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $proyecto['progreso_promedio'] ?>%"></div>
                            </div>
                        </div>
                        
                        <div class="project-actions">
                            <a href="<?= base_url('clientes/ver/' . $proyecto['idcliente']) ?>" class="project-btn btn-primary">
                                <i class="fas fa-user"></i> Ver Cliente
                            </a>
                            <a href="<?= base_url('cronograma') ?>" class="project-btn btn-secondary">
                                <i class="fas fa-calendar"></i> Cronograma
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3 class="empty-text">No hay proyectos activos en este momento</h3>
                    <a href="<?= base_url('servicios/crear') ?>" class="project-btn btn-primary" style="display: inline-flex; width: auto;">
                        <i class="fas fa-plus"></i> Crear Primer Proyecto
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Las animaciones ahora se manejan completamente con CSS
    // Este script solo es para funcionalidades adicionales si se necesitan en el futuro
    console.log('Proyectos cargados correctamente');
});
</script>

<?= $footer ?>