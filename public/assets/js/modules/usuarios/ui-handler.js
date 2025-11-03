/**
 * Módulo de Manejo de UI
 * 
 * Gestiona toda la interacción con la interfaz de usuario:
 * - Alertas y notificaciones (SweetAlert)
 * - Estados de botones
 * - Feedback visual
 * - Toggle de visibilidad de contraseñas
 * - Mostrar/ocultar elementos
 */

const UIHandler = (function() {
    'use strict';

    const config = window.UsuariosConfig;

    /**
     * Muestra una alerta simple con SweetAlert
     * @param {string} icon - Tipo de icono (success, error, warning, info)
     * @param {string} title - Título de la alerta
     * @param {string} text - Texto de la alerta
     */
    function mostrarAlerta(icon, title, text) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            confirmButtonColor: config.SWAL.CONFIRM_BUTTON_COLOR
        });
    }

    /**
     * Muestra una alerta de confirmación
     * @param {string} title - Título
     * @param {string} text - Texto
     * @returns {Promise} - Promesa de SweetAlert
     */
    function mostrarConfirmacion(title, text) {
        return Swal.fire({
            title: title || config.MENSAJES.CONFIRMACION.TITULO,
            text: text || config.MENSAJES.CONFIRMACION.TEXTO,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: config.SWAL.CONFIRM_BUTTON_COLOR,
            cancelButtonColor: config.SWAL.CANCEL_BUTTON_COLOR,
            confirmButtonText: config.MENSAJES.CONFIRMACION.CONFIRMAR,
            cancelButtonText: config.MENSAJES.CONFIRMACION.CANCELAR
        });
    }

    /**
     * Muestra un loading con SweetAlert
     * @param {string} title - Título
     * @param {string} text - Texto
     */
    function mostrarLoading(title, text) {
        Swal.fire({
            title: title || config.MENSAJES.PROCESANDO.TITULO,
            text: text || config.MENSAJES.PROCESANDO.TEXTO,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    /**
     * Cierra el SweetAlert actual
     */
    function cerrarAlerta() {
        Swal.close();
    }

    /**
     * Muestra un toast de SweetAlert
     * @param {string} icon - Icono
     * @param {string} title - Título
     * @param {string} html - HTML del contenido
     */
    function mostrarToast(icon, title, html) {
        Swal.fire({
            icon: icon,
            title: title,
            html: html,
            timer: config.SWAL.TOAST_TIMER,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            showConfirmButton: false
        });
    }

    /**
     * Deshabilita un botón y guarda su texto original
     * @param {jQuery} $button - Botón a deshabilitar
     * @param {string} textoOriginal - Texto original del botón
     */
    function deshabilitarBoton($button, textoOriginal) {
        $button.prop('disabled', true);
        $button.data('original-text', textoOriginal);
    }

    /**
     * Habilita un botón
     * @param {jQuery} $button - Botón a habilitar
     */
    function habilitarBoton($button) {
        $button.prop('disabled', false);
    }

    /**
     * Verifica si un botón debe estar habilitado según contenido de campos
     * @param {Array} campos - Array de valores de campos
     * @returns {boolean}
     */
    function tieneContenido(campos) {
        return campos.some(campo => campo && campo.trim && campo.trim().length > 0);
    }

    /**
     * Actualiza el estado de un botón según contenido de formulario
     * @param {jQuery} $button - Botón
     * @param {Array} campos - Array de valores de campos
     */
    function actualizarEstadoBoton($button, campos) {
        const hasContent = tieneContenido(campos);
        $button.prop('disabled', !hasContent);
    }

    /**
     * Muestra errores de validación en el formulario
     * @param {Object} errors - Objeto con errores {campo: mensaje}
     * @param {jQuery} $form - Formulario jQuery
     */
    function mostrarErroresFormulario(errors, $form) {
        // Limpiar errores previos
        $form.find(`.${config.CLASES.INVALIDO}`).removeClass(config.CLASES.INVALIDO);
        $form.find('.invalid-feedback').hide();

        // Mostrar nuevos errores
        let primerError = true;
        for (const field in errors) {
            const errorMessage = errors[field];
            const $input = $form.find(`[name="${field}"]`);

            if ($input.length) {
                $input.addClass(config.CLASES.INVALIDO);
                
                let $feedback = $input.siblings('.invalid-feedback');
                if ($feedback.length === 0) {
                    $feedback = $input.parent().find('.invalid-feedback');
                }
                
                if ($feedback.length > 0) {
                    $feedback.text(errorMessage).show();
                }

                // Scroll al primer error
                if (primerError) {
                    $input[0].scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    $input.focus();
                    primerError = false;
                }
            }
        }

        $form.addClass('was-validated');
    }

    /**
     * Actualiza la barra de fortaleza de contraseña
     * @param {jQuery} $passwordField - Campo de contraseña
     * @param {Object} strength - Objeto de fortaleza {score, percentage, class}
     */
    function actualizarBarraFortaleza($passwordField, strength) {
        const $strengthBar = $passwordField.closest('.row').find('.password-strength');
        
        $strengthBar.css('width', strength.percentage + '%');
        $strengthBar.removeClass(
            `${config.CLASES.PASSWORD_STRENGTH.DEBIL} ` +
            `${config.CLASES.PASSWORD_STRENGTH.REGULAR} ` +
            `${config.CLASES.PASSWORD_STRENGTH.FUERTE}`
        ).addClass(strength.class);
    }

    /**
     * Marca un campo como válido
     * @param {jQuery} $campo - Campo jQuery
     */
    function marcarCampoValido($campo) {
        $campo.removeClass(config.CLASES.INVALIDO).addClass(config.CLASES.VALIDO);
    }

    /**
     * Marca un campo como inválido
     * @param {jQuery} $campo - Campo jQuery
     */
    function marcarCampoInvalido($campo) {
        $campo.removeClass(config.CLASES.VALIDO).addClass(config.CLASES.INVALIDO);
    }

    /**
     * Limpia las marcas de validación de un campo
     * @param {jQuery} $campo - Campo jQuery
     */
    function limpiarMarcasValidacion($campo) {
        $campo.removeClass(`${config.CLASES.VALIDO} ${config.CLASES.INVALIDO}`);
    }

    /**
     * Establece un campo como readonly y le da estilo
     * @param {jQuery} $campo - Campo jQuery
     */
    function establecerReadonly($campo) {
        $campo.attr('readonly', true).addClass(config.CLASES.READONLY_BG);
    }

    /**
     * Quita el readonly de un campo
     * @param {jQuery} $campo - Campo jQuery
     */
    function quitarReadonly($campo) {
        $campo.attr('readonly', false).removeClass(config.CLASES.READONLY_BG);
    }

    /**
     * Muestra un elemento
     * @param {jQuery} $elemento - Elemento jQuery
     */
    function mostrar($elemento) {
        $elemento.show();
    }

    /**
     * Oculta un elemento
     * @param {jQuery} $elemento - Elemento jQuery
     */
    function ocultar($elemento) {
        $elemento.hide();
    }

    /**
     * Actualiza el texto de un elemento
     * @param {jQuery} $elemento - Elemento jQuery
     * @param {string} texto - Nuevo texto
     */
    function actualizarTexto($elemento, texto) {
        $elemento.text(texto);
    }

    /**
     * Limpia el valor de un campo
     * @param {jQuery} $campo - Campo jQuery
     */
    function limpiarCampo($campo) {
        $campo.val('');
    }

    /**
     * Establece el valor de un campo
     * @param {jQuery} $campo - Campo jQuery
     * @param {string} valor - Valor a establecer
     */
    function establecerValor($campo, valor) {
        $campo.val(valor);
    }

    /**
     * Toggle de visibilidad de contraseña
     * Maneja el cambio de tipo de input y el icono
     */
    function inicializarTogglePassword() {
        $(config.SELECTORES.PASSWORD_TOGGLE).on('click', function() {
            const $button = $(this);
            const $input = $button.siblings('input');
            const $icon = $button.find('i');

            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                $input.attr('type', 'password');
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    }

    // API Pública
    return {
        mostrarAlerta,
        mostrarConfirmacion,
        mostrarLoading,
        cerrarAlerta,
        mostrarToast,
        deshabilitarBoton,
        habilitarBoton,
        actualizarEstadoBoton,
        mostrarErroresFormulario,
        actualizarBarraFortaleza,
        marcarCampoValido,
        marcarCampoInvalido,
        limpiarMarcasValidacion,
        establecerReadonly,
        quitarReadonly,
        mostrar,
        ocultar,
        actualizarTexto,
        limpiarCampo,
        establecerValor,
        inicializarTogglePassword
    };
})();

// Hacer disponible globalmente
window.UIHandler = UIHandler;
