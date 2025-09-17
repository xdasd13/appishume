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
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
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