/**
 * Gestión de mapas y rutas usando MapLibre GL + OpenRouteService
 */

const MapLibreRoutes = {
    map: null,
    mapLoaded: false,
    modal: null,
    userMarker: null,
    destinationMarker: null,
    routeSourceId: 'route-line',
    routeLayerId: 'route-layer',

    initModal() {
        if (document.getElementById('routeModal')) {
            this.modal = new bootstrap.Modal(document.getElementById('routeModal'));
            return;
        }

        const modalHTML = `
            <div class="modal fade" id="routeModal" tabindex="-1" aria-labelledby="routeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable modal-fullscreen-md-down">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="routeModalLabel">
                                <i class="fas fa-map-location-dot me-2"></i>Ruta hacia la Ubicación
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div id="mapContainer">
                                <div id="mapLibreMap" style="height:100%; width:100%"></div>
                            </div>
                            <div id="routeInfo" style="display:none; margin-top:20px;">
                                <div class="stats">
                                    <div class="stat-card">
                                        <strong>Distancia estimada</strong>
                                        <span id="routeDistance">-</span>
                                    </div>
                                    <div class="stat-card">
                                        <strong>Duración estimada</strong>
                                        <span id="routeDuration">-</span>
                                    </div>
                                </div>
                            </div>
                            <div id="loadingRoute" style="display:none;">
                                <div class="spinner-border text-warning" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p>Calculando ruta, por favor espera…</p>
                            </div>
                            <div id="errorRoute" class="alert alert-danger" style="display:none; margin-top:16px;">
                                <i class="fas fa-triangle-exclamation me-2"></i>
                                <span id="errorMessage"></span>
                            </div>
                            <div id="infoRoute" class="alert alert-info" style="display:none; margin-top:16px;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <a id="openGoogleMaps" class="btn btn-success" target="_blank" rel="noopener">
                                <i class="fas fa-directions me-2"></i>Abrir en Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = new bootstrap.Modal(document.getElementById('routeModal'));
    },

    openRoute(address) {
        if (!address) {
            this.showError('La dirección seleccionada no es válida.');
            return;
        }

        if (!this.modal) {
            this.initModal();
        }

        this.modal.show();
        this.setLoading(true);
        this.toggleInfo('');
        this.clearError();
        this.clearRouteInfo();

        setTimeout(() => {
            this.initializeMap();
            this.getUserLocation().finally(() => {
                this.geocodeAddress(address.trim());
            });
        }, 350);
    },

    initializeMap() {
        if (this.map) {
            this.map.resize();
            return;
        }

        const defaultLocation = window.DEFAULT_PROJECT_LOCATION || { lat: -13.42253, lng: -76.12940 };

        this.map = new maplibregl.Map({
            container: 'mapLibreMap',
            style: 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
            center: [defaultLocation.lng, defaultLocation.lat],
            zoom: 13
        });

        this.map.addControl(new maplibregl.NavigationControl(), 'top-right');

        this.map.on('load', () => {
            this.mapLoaded = true;
            if (!this.map.getSource(this.routeSourceId)) {
                this.map.addSource(this.routeSourceId, {
                    type: 'geojson',
                    data: { type: 'FeatureCollection', features: [] }
                });
            }

            if (!this.map.getLayer(this.routeLayerId)) {
                this.map.addLayer({
                    id: this.routeLayerId,
                    type: 'line',
                    source: this.routeSourceId,
                    layout: { 'line-cap': 'round', 'line-join': 'round' },
                    paint: {
                        'line-color': '#2563eb',
                        'line-width': 5,
                        'line-opacity': 0.85
                    }
                });
            }
        });
    },

    getUserLocation() {
        return new Promise((resolve) => {
            if (!('geolocation' in navigator)) {
                this.toggleInfo('Tu navegador no permite obtener la ubicación actual. Se usará la ubicación predeterminada.');
                resolve(null);
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    resolve(this.userLocation);
                },
                () => {
                    this.toggleInfo('No pudimos acceder a tu ubicación. Puedes permitir el acceso o usar el botón de Google Maps.');
                    resolve(null);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    },

    geocodeAddress(address) {
        const apiKey = window.ORS_API_KEY;
        if (!apiKey) {
            this.setLoading(false);
            this.showError('No se ha configurado la variable ORS_API_KEY en el entorno.');
            return;
        }

        const url = `https://api.openrouteservice.org/geocode/search?api_key=${apiKey}&text=${encodeURIComponent(address)}&size=1`;

        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                if (!data.features || data.features.length === 0) {
                    throw new Error('No se encontró la dirección especificada.');
                }

                const coords = data.features[0].geometry.coordinates;
                const destination = {
                    lng: coords[0],
                    lat: coords[1],
                    name: address
                };

                this.destinationLocation = destination;
                this.processRoute();
            })
            .catch((error) => {
                this.setLoading(false);
                this.showError(error.message || 'Error al buscar la dirección.');
            });
    },

    processRoute() {
        if (!this.destinationLocation) {
            this.setLoading(false);
            this.showError('No se ha seleccionado una ubicación destino.');
            return;
        }

        if (!this.userLocation) {
            const fallback = window.DEFAULT_PROJECT_LOCATION;
            if (fallback?.lat && fallback?.lng) {
                this.userLocation = { lat: fallback.lat, lng: fallback.lng };
                this.toggleInfo('Usamos la ubicación por defecto de Chincha Alta como punto de partida.');
            }
        }

        if (!this.userLocation) {
            this.setDestinationMarker(this.destinationLocation);
            this.map?.flyTo({ center: [this.destinationLocation.lng, this.destinationLocation.lat], zoom: 14 });
            this.toggleInfo('Mostramos únicamente la ubicación de destino porque no pudimos obtener un origen válido.');
            this.updateGoogleMapsLink();
            this.setLoading(false);
            return;
        }

        this.requestRoute(this.userLocation, this.destinationLocation);
    },

    requestRoute(start, end) {
        const apiKey = window.ORS_API_KEY;
        const url = 'https://api.openrouteservice.org/v2/directions/driving-car/geojson';

        const body = {
            coordinates: [
                [start.lng, start.lat],
                [end.lng, end.lat]
            ]
        };

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: apiKey
            },
            body: JSON.stringify(body)
        })
            .then((response) => response.json())
            .then((data) => {
                if (!data.features || data.features.length === 0) {
                    throw new Error('No se pudo calcular la ruta.');
                }

                const routeFeature = data.features[0];
                this.updateRouteLayer(routeFeature);
                this.setMarkers(start, end);
                this.updateRouteSummary(routeFeature.properties?.summary);
                this.updateGoogleMapsLink();
                this.fitMapToRoute(routeFeature.geometry.coordinates);
                this.setLoading(false);
                this.toggleInfo('');
            })
            .catch((error) => {
                this.setLoading(false);
                this.showError(error.message || 'Error al calcular la ruta.');
                this.setDestinationMarker(end);
                this.map?.flyTo({ center: [end.lng, end.lat], zoom: 14 });
            });
    },

    updateRouteLayer(routeFeature) {
        if (!this.mapLoaded) {
            this.map.once('load', () => this.updateRouteLayer(routeFeature));
            return;
        }

        const source = this.map.getSource(this.routeSourceId);
        if (source) {
            source.setData({ type: 'FeatureCollection', features: [routeFeature] });
        }
    },

    setMarkers(start, end) {
        this.setUserMarker(start);
        this.setDestinationMarker(end);
    },

    setUserMarker(position) {
        if (!this.map) return;
        if (this.userMarker) {
            this.userMarker.remove();
        }

        this.userMarker = new maplibregl.Marker({ color: '#f78c00ff' })
            .setLngLat([position.lng, position.lat])
            .setPopup(new maplibregl.Popup().setHTML('<strong>Tu ubicación</strong>'))
            .addTo(this.map);
    },

    setDestinationMarker(position) {
        if (!this.map) return;
        if (this.destinationMarker) {
            this.destinationMarker.remove();
        }

        this.destinationMarker = new maplibregl.Marker({ color: '#dc2626' })
            .setLngLat([position.lng, position.lat])
            .setPopup(new maplibregl.Popup().setHTML(`<strong>Destino</strong><br>${position.name || ''}`))
            .addTo(this.map);
    },

    fitMapToRoute(coordinates) {
        if (!this.map || !coordinates?.length) return;
        const bounds = new maplibregl.LngLatBounds();
        coordinates.forEach((coord) => bounds.extend(coord));
        this.map.fitBounds(bounds, { padding: 60, maxZoom: 15, linear: true });
    },

    updateRouteSummary(summary) {
        if (!summary) return;
        const distanceEl = document.getElementById('routeDistance');
        const durationEl = document.getElementById('routeDuration');
        const infoEl = document.getElementById('routeInfo');

        const distanceKm = summary.distance ? (summary.distance / 1000).toFixed(2) : '-';
        const durationMin = summary.duration ? Math.round(summary.duration / 60) : '-';

        if (distanceEl) distanceEl.textContent = `${distanceKm} km`;
        if (durationEl) durationEl.textContent = `${durationMin} min`;
        if (infoEl) infoEl.style.display = 'block';
    },

    updateGoogleMapsLink() {
        const mapsBtn = document.getElementById('openGoogleMaps');
        if (!mapsBtn) return;

        const origin = this.userLocation || window.DEFAULT_PROJECT_LOCATION;
        const destination = this.destinationLocation;

        if (!destination) {
            mapsBtn.style.display = 'none';
            return;
        }

        const originCoords = origin ? `${origin.lat},${origin.lng}` : '';
        const destCoords = `${destination.lat},${destination.lng}`;
        const baseUrl = 'https://www.google.com/maps/dir/';
        mapsBtn.href = originCoords ? `${baseUrl}${originCoords}/${destCoords}` : `${baseUrl}/${destCoords}`;
        mapsBtn.style.display = 'inline-flex';
    },

    setLoading(isLoading) {
        const loadingEl = document.getElementById('loadingRoute');
        if (loadingEl) {
            loadingEl.style.display = isLoading ? 'block' : 'none';
        }
    },

    showError(message) {
        const errorEl = document.getElementById('errorRoute');
        const errorMessage = document.getElementById('errorMessage');
        if (errorMessage) errorMessage.textContent = message;
        if (errorEl) errorEl.style.display = 'block';
    },

    clearError() {
        const errorEl = document.getElementById('errorRoute');
        if (errorEl) errorEl.style.display = 'none';
    },

    toggleInfo(message) {
        const infoEl = document.getElementById('infoRoute');
        if (!infoEl) return;
        if (!message) {
            infoEl.style.display = 'none';
            infoEl.textContent = '';
            return;
        }
        infoEl.textContent = message;
        infoEl.style.display = 'block';
    },

    clearRouteInfo() {
        const infoEl = document.getElementById('routeInfo');
        if (infoEl) infoEl.style.display = 'none';
        const distanceEl = document.getElementById('routeDistance');
        const durationEl = document.getElementById('routeDuration');
        if (distanceEl) distanceEl.textContent = '-';
        if (durationEl) durationEl.textContent = '-';
        const mapsBtn = document.getElementById('openGoogleMaps');
        if (mapsBtn) mapsBtn.style.display = 'none';
    }
};

// Inicializar al cargar el documento
window.addEventListener('DOMContentLoaded', () => {
    MapLibreRoutes.initModal();
});
