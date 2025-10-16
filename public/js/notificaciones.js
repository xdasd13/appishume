/**
 * Sistema de Notificaciones Instantáneas - ISHUME
 * Maneja notificaciones en tiempo real usando polling y Web Notifications API
 */

class NotificacionesManager {
    constructor() {
        this.pollingInterval = 10000; // 10 segundos
        this.pollingTimer = null;
        this.notificationSound = null;
        this.isPolling = false;
        this.lastNotificationCount = 0;
        this.lastMessageCount = 0;
        
        this.init();
    }

    init() {
        // Solicitar permisos para notificaciones del navegador
        this.requestNotificationPermission();
        
        // Cargar sonido de notificación
        this.loadNotificationSound();
        
        // Iniciar polling
        this.startPolling();
        
        // Configurar eventos de visibilidad de página
        this.setupVisibilityEvents();
        
        console.log('Sistema de notificaciones iniciado');
    }

    requestNotificationPermission() {
        if ('Notification' in window) {
            if (Notification.permission === 'default') {
                Notification.requestPermission().then(permission => {
                    console.log('Permiso de notificaciones:', permission);
                });
            }
        }
    }

    loadNotificationSound() {
        // Crear un sonido de notificación usando Web Audio API
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            this.audioContext = audioContext;
        } catch (e) {
            console.warn('Web Audio API no soportada');
        }
    }

    playNotificationSound() {
        if (!this.audioContext) return;
        
        try {
            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(this.audioContext.destination);
            
            oscillator.frequency.setValueAtTime(800, this.audioContext.currentTime);
            oscillator.frequency.setValueAtTime(600, this.audioContext.currentTime + 0.1);
            
            gainNode.gain.setValueAtTime(0.3, this.audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.2);
            
            oscillator.start(this.audioContext.currentTime);
            oscillator.stop(this.audioContext.currentTime + 0.2);
        } catch (e) {
            console.warn('Error al reproducir sonido de notificación');
        }
    }

    startPolling() {
        if (this.isPolling) return;
        
        this.isPolling = true;
        this.pollingTimer = setInterval(() => {
            this.checkForUpdates();
        }, this.pollingInterval);
        
        // Verificar inmediatamente
        this.checkForUpdates();
    }

    stopPolling() {
        if (this.pollingTimer) {
            clearInterval(this.pollingTimer);
            this.pollingTimer = null;
        }
        this.isPolling = false;
    }

    async checkForUpdates() {
        try {
            // Verificar si la página está visible
            if (document.hidden) {
                return;
            }

            // Obtener contadores de notificaciones y mensajes
            const [notificationsResponse, messagesResponse] = await Promise.all([
                fetch('/mensajeria/getNotificacionesNoLeidas'),
                fetch('/mensajeria/getMensajesNoLeidos')
            ]);

            const notificationsData = await notificationsResponse.json();
            const messagesData = await messagesResponse.json();

            if (notificationsData.success) {
                this.handleNotificationUpdates(notificationsData.data);
            }

            if (messagesData.success) {
                this.handleMessageUpdates(messagesData.data);
            }

        } catch (error) {
            console.error('Error al verificar actualizaciones:', error);
        }
    }

    handleNotificationUpdates(data) {
        const currentCount = data.total;
        
        if (currentCount > this.lastNotificationCount) {
            // Hay nuevas notificaciones
            const newCount = currentCount - this.lastNotificationCount;
            this.showNotificationAlert('notificaciones', newCount, data);
        }
        
        this.lastNotificationCount = currentCount;
        this.updateNotificationBadge(data);
    }

    handleMessageUpdates(data) {
        const currentCount = data.total;
        
        if (currentCount > this.lastMessageCount) {
            // Hay nuevos mensajes
            const newCount = currentCount - this.lastMessageCount;
            this.showNotificationAlert('mensajes', newCount, data);
        }
        
        this.lastMessageCount = currentCount;
        this.updateMessageBadge(data);
    }

    showNotificationAlert(tipo, cantidad, data) {
        // Reproducir sonido
        this.playNotificationSound();
        
        // Mostrar notificación del navegador
        this.showBrowserNotification(tipo, cantidad, data);
        
        // Mostrar toast notification
        this.showToastNotification(tipo, cantidad, data);
        
        // Actualizar badges
        this.updateBadges(tipo, data);
    }

    showBrowserNotification(tipo, cantidad, data) {
        if (Notification.permission !== 'granted') return;
        
        let title, body, icon;
        
        if (tipo === 'notificaciones') {
            title = 'Nueva Notificación';
            body = `Tienes ${cantidad} nueva(s) notificación(es)`;
            icon = '/assets/img/notification-icon.png';
        } else if (tipo === 'mensajes') {
            title = 'Nuevo Mensaje';
            body = `Tienes ${cantidad} nuevo(s) mensaje(s)`;
            icon = '/assets/img/message-icon.png';
        }
        
        const notification = new Notification(title, {
            body: body,
            icon: icon,
            badge: icon,
            tag: tipo,
            requireInteraction: false
        });
        
        notification.onclick = () => {
            window.focus();
            if (tipo === 'mensajes') {
                window.location.href = '/mensajeria';
            } else {
                // Abrir dropdown de notificaciones
                $('#dropdown-notificaciones').dropdown('show');
            }
            notification.close();
        };
        
        // Cerrar automáticamente después de 5 segundos
        setTimeout(() => {
            notification.close();
        }, 5000);
    }

    showToastNotification(tipo, cantidad, data) {
        const toastContainer = this.getOrCreateToastContainer();
        
        let title, message, iconClass, bgClass;
        
        if (tipo === 'notificaciones') {
            title = 'Nueva Notificación';
            message = `Tienes ${cantidad} nueva(s) notificación(es)`;
            iconClass = 'fas fa-bell';
            bgClass = 'bg-info';
        } else if (tipo === 'mensajes') {
            title = 'Nuevo Mensaje';
            message = `Tienes ${cantidad} nuevo(s) mensaje(s)`;
            iconClass = 'fas fa-envelope';
            bgClass = 'bg-primary';
        }
        
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header ${bgClass} text-white">
                    <i class="${iconClass} me-2"></i>
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        
        toast.show();
        
        // Remover el elemento después de que se oculte
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    getOrCreateToastContainer() {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        return container;
    }

    updateNotificationBadge(data) {
        const badge = document.getElementById('badge-notificaciones');
        if (badge) {
            if (data.total > 0) {
                badge.textContent = data.total;
                badge.style.display = 'inline-block';
                
                // Agregar animación de pulso
                badge.classList.add('pulse-animation');
                setTimeout(() => {
                    badge.classList.remove('pulse-animation');
                }, 1000);
            } else {
                badge.style.display = 'none';
            }
        }
    }

    updateMessageBadge(data) {
        const badge = document.getElementById('badge-mensajes');
        const badgeSidebar = document.getElementById('badge-mensajes-sidebar');
        
        if (badge) {
            if (data.total > 0) {
                badge.textContent = data.total;
                badge.style.display = 'inline-block';
                
                // Agregar animación de pulso
                badge.classList.add('pulse-animation');
                setTimeout(() => {
                    badge.classList.remove('pulse-animation');
                }, 1000);
            } else {
                badge.style.display = 'none';
            }
        }
        
        // Actualizar también el badge del sidebar
        if (badgeSidebar) {
            if (data.total > 0) {
                badgeSidebar.textContent = data.total;
                badgeSidebar.style.display = 'inline-block';
            } else {
                badgeSidebar.style.display = 'none';
            }
        }
    }

    updateBadges(tipo, data) {
        if (tipo === 'notificaciones') {
            this.updateNotificationBadge(data);
        } else if (tipo === 'mensajes') {
            this.updateMessageBadge(data);
        }
    }

    setupVisibilityEvents() {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                // Página oculta - reducir frecuencia de polling
                this.stopPolling();
                this.startPolling(); // Reiniciar con intervalo normal
            } else {
                // Página visible - verificar inmediatamente
                this.checkForUpdates();
            }
        });
    }

    // Método público para forzar verificación
    forceCheck() {
        this.checkForUpdates();
    }

    // Método público para detener el sistema
    destroy() {
        this.stopPolling();
        console.log('Sistema de notificaciones detenido');
    }
}

// Inicializar el sistema cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Solo inicializar si estamos en una página que necesita notificaciones
    if (typeof window.notificacionesManager === 'undefined') {
        window.notificacionesManager = new NotificacionesManager();
    }
});

// CSS para animaciones
const style = document.createElement('style');
style.textContent = `
    .pulse-animation {
        animation: pulse 1s ease-in-out;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    
    .toast-container {
        z-index: 9999;
    }
    
    .badge {
        transition: all 0.3s ease;
    }
`;
document.head.appendChild(style);
