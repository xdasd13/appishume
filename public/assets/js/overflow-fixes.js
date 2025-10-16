/* ========================================
   OVERFLOW DETECTION & FIX SCRIPT
   ======================================== */

$(document).ready(function() {
    // Función para detectar y corregir overflow
    function fixOverflow() {
        // Detectar elementos que se salen horizontalmente
        $('*').each(function() {
            const element = $(this);
            const elementWidth = element.outerWidth();
            const parentWidth = element.parent().width();
            
            // Si el elemento es más ancho que su contenedor
            if (elementWidth > parentWidth && parentWidth > 0) {
                // Aplicar fixes específicos
                if (element.is('table')) {
                    element.addClass('table-responsive');
                } else if (element.is('img')) {
                    element.css({
                        'max-width': '100%',
                        'height': 'auto'
                    });
                } else if (element.is('.card, .card-body')) {
                    element.css('overflow-x', 'hidden');
                } else {
                    element.css('max-width', '100%');
                }
            }
        });
        
        // Detectar scroll horizontal en el body
        if (document.body.scrollWidth > window.innerWidth) {
            $('body').css('overflow-x', 'hidden');
        }
        
        // Detectar scroll horizontal en contenedores principales
        $('.container-fluid, .main-panel, .page-inner').each(function() {
            if (this.scrollWidth > this.clientWidth) {
                $(this).css('overflow-x', 'hidden');
            }
        });
    }
    
    // Ejecutar al cargar
    fixOverflow();
    
    // Ejecutar al redimensionar ventana
    $(window).on('resize', function() {
        setTimeout(fixOverflow, 100);
    });
    
    // Ejecutar después de cargar contenido dinámico
    $(document).on('DOMNodeInserted', function() {
        setTimeout(fixOverflow, 50);
    });
    
    // Prevenir zoom horizontal
    $(window).on('scroll', function() {
        if (window.scrollX !== 0) {
            window.scrollTo(0, window.scrollY);
        }
    });
    
    // Fix específico para mensajería
    if (window.location.pathname.includes('mensajeria')) {
        $('.mensajeria-container').css({
            'overflow-x': 'hidden',
            'max-width': '100vw',
            'width': '100%'
        });
    }
    
    // Fix específico para reportes
    if (window.location.pathname.includes('reportes')) {
        $('.reportes-container').css({
            'overflow-x': 'hidden',
            'max-width': '100%'
        });
    }
    
    // Log para debugging
    console.log('Overflow fixes aplicados correctamente');
});
