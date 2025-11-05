<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/cronograma-proyectos.css') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


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
                                <div class="detail-value" style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                                    <span style="flex: 1;"><?= esc($proyecto['direccion_principal']) ?></span>
                                    <button class="btn-ver-ruta" onclick="mostrarRuta('<?= esc($proyecto['direccion_principal']) ?>', '<?= esc($proyecto['cliente']) ?>')">
                                        <i class="fas fa-route"></i> Ver Ruta
                                    </button>
                                </div>
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

<!-- Modal para mostrar el mapa con la ruta -->
<div id="modalMapa" class="modal-mapa" style="display: none;">
    <div class="modal-mapa-content">
        <div class="modal-mapa-header">
            <h3 id="modalMapaTitulo">
                <i class="fas fa-route"></i> Ruta al Evento
            </h3>
            <button class="modal-mapa-close" onclick="cerrarModalMapa()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-mapa-body">
            <div id="mapaRuta" style="width: 100%; height: 500px; border-radius: 8px;"></div>
            <div id="infoRuta" class="info-ruta" style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px; display: none;">
                <div style="display: flex; justify-content: space-around; text-align: center;">
                    <div>
                        <i class="fas fa-road" style="color: #FF9900; font-size: 1.5rem;"></i>
                        <p style="margin: 5px 0 0 0; font-weight: bold;" id="distanciaRuta">-</p>
                        <small style="color: #7f8c8d;">Distancia</small>
                    </div>
                    <div>
                        <i class="fas fa-clock" style="color: #FF9900; font-size: 1.5rem;"></i>
                        <p style="margin: 5px 0 0 0; font-weight: bold;" id="tiempoRuta">-</p>
                        <small style="color: #7f8c8d;">Tiempo estimado</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para el modal del mapa */
.modal-mapa {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

.modal-mapa-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 900px;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
}

.modal-mapa-header {
    background: linear-gradient(135deg, #FF9900 0%, #F57C00 100%);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-mapa-header h3 {
    margin: 0;
    font-size: 1.3rem;
}

.modal-mapa-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.modal-mapa-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.modal-mapa-body {
    padding: 20px;
}

/* Botón Ver Ruta */
.btn-ver-ruta {
    background: linear-gradient(135deg, #FF9900 0%, #F57C00 100%);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.btn-ver-ruta:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 153, 0, 0.4);
}

.btn-ver-ruta i {
    font-size: 0.9rem;
}

/* Animaciones */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Estilos para el mapa Leaflet */
.leaflet-popup-content-wrapper {
    border-radius: 8px;
}

.leaflet-popup-content {
    margin: 15px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
</style>

<script>
let mapaRuta = null;
let marcadorOrigen = null;
let marcadorDestino = null;
let rutaPolyline = null;

// Función para mostrar la ruta en el modal
async function mostrarRuta(direccionDestino, nombreCliente) {
    // Mostrar el modal
    document.getElementById('modalMapa').style.display = 'flex';
    document.getElementById('modalMapaTitulo').innerHTML = `<i class="fas fa-route"></i> Ruta al Evento - ${nombreCliente}`;
    
    // Esperar un momento para que el modal se renderice
    setTimeout(async () => {
        // Inicializar el mapa si no existe
        if (!mapaRuta) {
            mapaRuta = L.map('mapaRuta').setView([-12.0464, -77.0428], 13); // Lima, Perú por defecto
            
            // Agregar capa de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(mapaRuta);
        } else {
            mapaRuta.invalidateSize();
        }
        
        // Obtener ubicación actual del usuario
        if (navigator.geolocation) {
            Swal.fire({
                title: 'Obteniendo tu ubicación...',
                html: 'Por favor, permite el acceso a tu ubicación',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const origenLat = position.coords.latitude;
                    const origenLng = position.coords.longitude;
                    
                    // Geocodificar la dirección de destino
                    try {
                        const destinoCoords = await geocodificarDireccion(direccionDestino);
                        
                        if (destinoCoords) {
                            await trazarRuta(origenLat, origenLng, destinoCoords.lat, destinoCoords.lng, direccionDestino);
                            Swal.close();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo encontrar la ubicación del evento. Verifica la dirección.',
                                confirmButtonColor: '#FF9900'
                            });
                        }
                    } catch (error) {
                        console.error('Error al geocodificar:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al buscar la ubicación.',
                            confirmButtonColor: '#FF9900'
                        });
                    }
                },
                (error) => {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ubicación no disponible',
                        text: 'No se pudo obtener tu ubicación. Mostrando ubicación de ejemplo.',
                        confirmButtonColor: '#FF9900'
                    });
                    
                    // Usar ubicación de ejemplo (Lima, Perú)
                    geocodificarDireccion(direccionDestino).then(destinoCoords => {
                        if (destinoCoords) {
                            // Mostrar solo el destino sin ruta
                            mostrarSoloDestino(destinoCoords.lat, destinoCoords.lng, direccionDestino);
                        }
                    });
                }
            );
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Geolocalización no soportada',
                text: 'Tu navegador no soporta geolocalización.',
                confirmButtonColor: '#FF9900'
            });
        }
    }, 100);
}

// Función para geocodificar una dirección usando Nominatim (OpenStreetMap)
async function geocodificarDireccion(direccion) {
    try {
        // Agregar "Lima, Perú" si no está en la dirección para mejorar resultados
        const direccionCompleta = direccion.includes('Lima') ? direccion : `${direccion}, Lima, Perú`;
        
        const response = await fetch(
            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccionCompleta)}&limit=1`
        );
        const data = await response.json();
        
        if (data && data.length > 0) {
            return {
                lat: parseFloat(data[0].lat),
                lng: parseFloat(data[0].lon)
            };
        }
        return null;
    } catch (error) {
        console.error('Error en geocodificación:', error);
        return null;
    }
}

// Función para trazar la ruta usando OSRM
async function trazarRuta(origenLat, origenLng, destinoLat, destinoLng, direccionDestino) {
    try {
        // Limpiar marcadores y rutas anteriores
        if (marcadorOrigen) mapaRuta.removeLayer(marcadorOrigen);
        if (marcadorDestino) mapaRuta.removeLayer(marcadorDestino);
        if (rutaPolyline) mapaRuta.removeLayer(rutaPolyline);
        
        // Obtener la ruta desde OSRM
        const response = await fetch(
            `https://router.project-osrm.org/route/v1/driving/${origenLng},${origenLat};${destinoLng},${destinoLat}?overview=full&geometries=geojson`
        );
        const data = await response.json();
        
        if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
            const route = data.routes[0];
            const coordinates = route.geometry.coordinates;
            
            // Convertir coordenadas de [lng, lat] a [lat, lng] para Leaflet
            const latlngs = coordinates.map(coord => [coord[1], coord[0]]);
            
            // Dibujar la ruta
            rutaPolyline = L.polyline(latlngs, {
                color: '#FF9900',
                weight: 5,
                opacity: 0.7
            }).addTo(mapaRuta);
            
            // Agregar marcador de origen
            marcadorOrigen = L.marker([origenLat, origenLng], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background: #27AE60; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-user"></i></div>',
                    iconSize: [30, 30]
                })
            }).addTo(mapaRuta)
              .bindPopup('<b>Tu ubicación</b><br>Punto de partida');
            
            // Agregar marcador de destino
            marcadorDestino = L.marker([destinoLat, destinoLng], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background: #E74C3C; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-map-marker-alt"></i></div>',
                    iconSize: [35, 35]
                })
            }).addTo(mapaRuta)
              .bindPopup(`<b>Ubicación del Evento</b><br>${direccionDestino}`);
            
            // Ajustar el mapa para mostrar toda la ruta
            mapaRuta.fitBounds(rutaPolyline.getBounds(), { padding: [50, 50] });
            
            // Mostrar información de la ruta
            const distanciaKm = (route.distance / 1000).toFixed(2);
            const tiempoMin = Math.round(route.duration / 60);
            
            document.getElementById('distanciaRuta').textContent = `${distanciaKm} km`;
            document.getElementById('tiempoRuta').textContent = `${tiempoMin} min`;
            document.getElementById('infoRuta').style.display = 'block';
            
        } else {
            throw new Error('No se pudo calcular la ruta');
        }
    } catch (error) {
        console.error('Error al trazar ruta:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error al calcular ruta',
            text: 'No se pudo calcular la ruta. Mostrando solo la ubicación del evento.',
            confirmButtonColor: '#FF9900'
        });
        
        // Mostrar solo el destino
        mostrarSoloDestino(destinoLat, destinoLng, direccionDestino);
    }
}

// Función para mostrar solo el destino (sin ruta)
function mostrarSoloDestino(lat, lng, direccion) {
    // Limpiar marcadores anteriores
    if (marcadorOrigen) mapaRuta.removeLayer(marcadorOrigen);
    if (marcadorDestino) mapaRuta.removeLayer(marcadorDestino);
    if (rutaPolyline) mapaRuta.removeLayer(rutaPolyline);
    
    // Agregar marcador del destino
    marcadorDestino = L.marker([lat, lng], {
        icon: L.divIcon({
            className: 'custom-marker',
            html: '<div style="background: #E74C3C; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-map-marker-alt"></i></div>',
            iconSize: [35, 35]
        })
    }).addTo(mapaRuta)
      .bindPopup(`<b>Ubicación del Evento</b><br>${direccion}`)
      .openPopup();
    
    // Centrar el mapa en el destino
    mapaRuta.setView([lat, lng], 15);
    
    // Ocultar información de ruta
    document.getElementById('infoRuta').style.display = 'none';
}

// Función para cerrar el modal
function cerrarModalMapa() {
    document.getElementById('modalMapa').style.display = 'none';
}

// Cerrar modal al hacer clic fuera de él
document.addEventListener('click', function(event) {
    const modal = document.getElementById('modalMapa');
    if (event.target === modal) {
        cerrarModalMapa();
    }
});
</script>
<?= $footer ?>