// Funcionalidades personalizadas para el sistema de control de pagos

$(document).ready(function() {

    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Inicializar popovers
    $('[data-toggle="popover"]').popover();
    
    // Mostrar nombre de archivo en input file
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
    
    // Confirmación para acciones importantes
    $('.confirm-action').on('click', function(e) {
        e.preventDefault();
        var message = $(this).data('confirm') || '¿Está seguro de realizar esta acción?';
        var href = $(this).attr('href');
        
        Swal.fire({
            title: 'Confirmar acción',
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'animate__animated animate__zoomIn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
    
    // Auto-ocultar alerts después de 5 segundos
    setTimeout(function() {
        $('.alert').fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 5000);
    
    // Funcionalidad para cerrar alerts manualmente
    $('.alert .close').on('click', function() {
        $(this).closest('.alert').fadeOut();
    });
    
    // Mejorar la experiencia en formularios
    $('form').on('submit', function() {
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Crear Personal y Credenciales');
    });
    
    // Funcionalidad para filtros en tiempo real
    $('.real-time-filter').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        var target = $(this).data('target');
        $(target + ' tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Animación para elementos que entran en vista
    if (typeof IntersectionObserver !== 'undefined') {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        });
        
        document.querySelectorAll('.animate-on-scroll').forEach((element) => {
            observer.observe(element);
        });
    }
    
    // Mejorar la experiencia en móviles
    if ($(window).width() < 768) {
        $('.dropdown-menu').addClass('dropdown-menu-right');
    }
});

// ==================== Notificaciones del sistema ====================
document.addEventListener('DOMContentLoaded', function() {
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationCountBadge = document.getElementById('notificationCount');
    const notificationList = document.getElementById('notificationList');
    const markAllBtn = document.getElementById('markAllNotifications');

    if (!notificationDropdown || !notificationCountBadge || !notificationList) {
        return;
    }

    const endpoints = {
        list: notificationDropdown.dataset.apiList,
        recent: notificationDropdown.dataset.apiRecent,
        count: notificationDropdown.dataset.apiCount,
        mark: notificationDropdown.dataset.apiMark,
        markAll: notificationDropdown.dataset.apiMarkAll,
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const defaultHeaders = {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };

    const updateIntervalMs = 60000; // 1 minuto
    let pollingInterval = null;

    const fetchJSON = async (url, options = {}) => {
        const config = {
            method: 'GET',
            headers: { ...defaultHeaders, ...(options.headers || {}) },
            cache: 'no-cache',
            credentials: 'same-origin',
            ...options,
        };

        if (config.method !== 'GET' && csrfToken) {
            config.headers['Content-Type'] = 'application/json';
            config.headers['X-CSRF-TOKEN'] = csrfToken;
        }

        const response = await fetch(url, config);
        if (!response.ok) {
            throw new Error('Error en la petición');
        }
        return response.json();
    };

    const formatDateTime = (dateString) => {
        if (!dateString) return '';
        try {
            return new Date(dateString).toLocaleString('es-PE', {
                dateStyle: 'short',
                timeStyle: 'short'
            });
        } catch (error) {
            return dateString;
        }
    };

    const renderNotifications = (notifications) => {
        if (!notifications || notifications.length === 0) {
            notificationList.innerHTML = '<div class="text-muted small text-center py-3">No tienes notificaciones pendientes</div>';
            return;
        }

        const items = notifications.map(notif => {
            const tipo = notif.tipo || 'sistema';
            const iconClass = {
                asignacion_proyecto: 'fas fa-briefcase',
                vencimiento_proyecto: 'fas fa-exclamation-triangle',
                mensaje: 'fas fa-envelope',
                mensaje_importante: 'fas fa-envelope-open-text',
                mensaje_urgente: 'fas fa-bolt'
            }[tipo] || 'fas fa-bell';

            const readClass = notif.leida ? 'notif-read' : 'notif-unread';
            const url = notif.url || '#';
            const notifId = notif.id;

            return `
                <a href="${url}" class="notification-item ${readClass}" data-notification-id="${notifId}" data-notification-url="${url}">
                    <div class="notif-icon"><i class="${iconClass}"></i></div>
                    <div class="notif-content">
                        <span class="block fw-semibold">${notif.titulo || 'Notificación'}</span>
                        <span class="text-muted small d-block">${notif.mensaje || ''}</span>
                        <span class="time d-block small">${formatDateTime(notif.fecha_creacion)}</span>
                    </div>
                </a>
            `;
        }).join('');

        notificationList.innerHTML = items;
    };

    const setLoadingState = () => {
        notificationList.innerHTML = '<div class="text-muted small text-center py-3"><i class="fas fa-spinner fa-spin me-2"></i>Cargando notificaciones...</div>';
    };

    const updateCount = async () => {
        try {
            const { success, data } = await fetchJSON(endpoints.count);
            if (success && data && typeof data.total === 'number') {
                if (data.total > 0) {
                    notificationCountBadge.textContent = data.total;
                    notificationCountBadge.classList.remove('d-none');
                } else {
                    notificationCountBadge.classList.add('d-none');
                }
            }
        } catch (error) {
            console.error('Error al obtener contador de notificaciones', error);
        }
    };

    const loadNotifications = async () => {
        setLoadingState();
        try {
            const { success, data } = await fetchJSON(`${endpoints.recent}?limit=5`);
            renderNotifications(success ? data : []);
        } catch (error) {
            console.error('Error al cargar notificaciones', error);
            notificationList.innerHTML = '<div class="text-danger small text-center py-3">No se pudieron cargar las notificaciones</div>';
        }
    };

    const markNotificationAsRead = async (notificationId) => {
        if (!notificationId) return false;
        try {
            const url = `${endpoints.mark}/${notificationId}`;
            const { success } = await fetchJSON(url, { method: 'POST', body: JSON.stringify({}) });
            if (success) {
                updateCount();
            }
            return success;
        } catch (error) {
            console.error('Error al marcar notificación como leída', error);
            return false;
        }
    };

    const markAllAsRead = async () => {
        try {
            const { success } = await fetchJSON(endpoints.markAll, { method: 'POST', body: JSON.stringify({}) });
            if (success) {
                updateCount();
                loadNotifications();
            }
        } catch (error) {
            console.error('Error al marcar todas las notificaciones', error);
        }
    };

    notificationList.addEventListener('click', async (event) => {
        const item = event.target.closest('[data-notification-id]');
        if (!item) {
            return;
        }
        event.preventDefault();
        const notificationId = item.dataset.notificationId;
        const targetUrl = item.dataset.notificationUrl || '#';

        const marked = await markNotificationAsRead(notificationId);
        if (marked) {
            item.classList.remove('notif-unread');
            item.classList.add('notif-read');
        }

        if (targetUrl && targetUrl !== '#') {
            window.location.href = targetUrl;
        }
    });

    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(event) {
            event.preventDefault();
            markAllAsRead();
        });
    }

    $('#notificationDropdown').on('show.bs.dropdown', function() {
        loadNotifications();
    });

    updateCount();
    pollingInterval = setInterval(updateCount, updateIntervalMs);

    window.addEventListener('beforeunload', function() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    });
});

// Funciones utilitarias
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: 'PEN'
    }).format(amount);
}

function showLoading(message = 'Cargando...') {
    Swal.fire({
        title: message,
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
        customClass: {
            popup: 'animate__animated animate__zoomIn'
        }
    });
}

function hideLoading() {
    Swal.close();
}

function showToast(message, type = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    
    Toast.fire({
        icon: type,
        title: message,
        customClass: {
            popup: 'animate__animated animate__zoomIn'
        }
    });
}

// Validación de montos en tiempo real
function validatePaymentAmount(inputElement, maxAmount) {
    const value = parseFloat(inputElement.val());

    if (isNaN(value)) {
        showToast('Por favor ingrese un monto válido', 'error');
        inputElement.addClass('is-invalid');
        return false;
    }
    
    if (value <= 0) {
        showToast('El monto debe ser mayor a cero', 'error');
        inputElement.addClass('is-invalid');
        return false;
    }
    
    if (value > maxAmount) {
        showToast('El monto excede el saldo disponible', 'error');
        inputElement.addClass('is-invalid');
        return false;
    }
    
    inputElement.removeClass('is-invalid');
    return true;
}

// Efectos de animación para transiciones de página
function animatePageTransition() {
    $('body').addClass('animate__animated animate__fadeIn');
}

// Inicializar animaciones cuando se carga la página
$(window).on('load', function() {
    animatePageTransition();
    
    // Animación para elementos con delay
    $('.card-3d').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });
});

// Funciones globales para mejorar la UX
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Confirmaciones para acciones importantes
    const confirmarAccion = (elemento, mensaje) => {
        elemento.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: mensaje,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    };
    
    // Aplicar a todos los enlaces de eliminación
    document.querySelectorAll('a[data-confirm]').forEach(enlace => {
        confirmarAccion(enlace, enlace.getAttribute('data-confirm'));
    });
    
    // Mejoras para formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const botones = this.querySelectorAll('button[type="submit"]');
            botones.forEach(boton => {
                boton.disabled = true;
                boton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
            });
        });
    });
    
    // Animaciones para elementos al hacer scroll
    const animarAlScroll = () => {
        const elementos = document.querySelectorAll('.animate-on-scroll');
        elementos.forEach(elemento => {
            const posicion = elemento.getBoundingClientRect().top;
            const alturaVentana = window.innerHeight;
            
            if (posicion < alturaVentana - 100) {
                elemento.classList.add('animate__animated', 'animate__fadeInUp');
            }
        });
    };
    
    window.addEventListener('scroll', animarAlScroll);
    animarAlScroll(); // Ejecutar una vez al cargar
});

// Función para formatear montos monetarios
function formatoMoneda(monto) {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: 'PEN'
    }).format(monto);
}

// Función para mostrar notificaciones Toast
function mostrarToast(mensaje, tipo = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    Toast.fire({
        icon: tipo,
        title: mensaje
    });
}