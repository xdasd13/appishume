/**
 * Módulo de Validaciones
 * 
 * Maneja todas las validaciones de formularios:
 * - Validación de contraseñas y fortaleza
 * - Validación de campos de formulario
 * - Validación de coincidencia de contraseñas
 * - Validación de DNI (formato básico)
 */

const ValidationModule = (function() {
    'use strict';

    const config = window.UsuariosConfig;

    /**
     * Calcula la fortaleza de una contraseña
     * @param {string} password - Contraseña a evaluar
     * @returns {Object} - {score, percentage, class}
     */
    function calcularFortalezaPassword(password) {
        let score = 0;

        if (password.length >= config.VALIDACION.PASSWORD_MIN_LENGTH) score++;
        if (config.PATTERNS.MINUSCULA.test(password)) score++;
        if (config.PATTERNS.MAYUSCULA.test(password)) score++;
        if (config.PATTERNS.NUMERO.test(password)) score++;
        if (config.PATTERNS.SIMBOLO.test(password)) score++;

        const percentage = (score / 5) * 100;
        let strengthClass = config.CLASES.PASSWORD_STRENGTH.DEBIL;

        if (score >= 4) {
            strengthClass = config.CLASES.PASSWORD_STRENGTH.FUERTE;
        } else if (score >= 3) {
            strengthClass = config.CLASES.PASSWORD_STRENGTH.REGULAR;
        }

        return {
            score: score,
            percentage: percentage,
            class: strengthClass
        };
    }

    /**
     * Actualiza la visualización de requisitos de contraseña
     * @param {string} password - Contraseña a validar
     * @param {string} formType - 'existente' o 'nuevo'
     */
    function actualizarRequisitosPassword(password, formType) {
        const requisitos = [
            {
                id: `length-${formType}`,
                cumplido: password.length >= config.VALIDACION.PASSWORD_MIN_LENGTH
            },
            {
                id: `uppercase-${formType}`,
                cumplido: config.PATTERNS.MAYUSCULA.test(password)
            },
            {
                id: `lowercase-${formType}`,
                cumplido: config.PATTERNS.MINUSCULA.test(password)
            },
            {
                id: `number-${formType}`,
                cumplido: config.PATTERNS.NUMERO.test(password)
            },
            {
                id: `special-${formType}`,
                cumplido: config.PATTERNS.SIMBOLO.test(password)
            }
        ];

        requisitos.forEach(req => {
            const $elemento = $(`#${req.id}`);
            if (req.cumplido) {
                $elemento
                    .removeClass(config.CLASES.REQUIREMENT_UNMET)
                    .addClass(config.CLASES.REQUIREMENT_MET);
            } else {
                $elemento
                    .removeClass(config.CLASES.REQUIREMENT_MET)
                    .addClass(config.CLASES.REQUIREMENT_UNMET);
            }
        });
    }

    /**
     * Valida si una contraseña cumple los requisitos mínimos
     * @param {string} password - Contraseña a validar
     * @returns {boolean}
     */
    function esPasswordValida(password) {
        const strength = calcularFortalezaPassword(password);
        return password.length >= config.VALIDACION.PASSWORD_MIN_LENGTH && 
               strength.score >= config.VALIDACION.PASSWORD_MIN_SCORE;
    }

    /**
     * Valida si dos contraseñas coinciden
     * @param {string} password - Contraseña original
     * @param {string} confirmPassword - Contraseña de confirmación
     * @returns {boolean}
     */
    function passwordsCoinciden(password, confirmPassword) {
        return password === confirmPassword;
    }

    /**
     * Valida el formato de un DNI
     * @param {string} dni - DNI a validar
     * @returns {Object} - {valido, mensaje}
     */
    function validarFormatoDNI(dni) {
        if (!dni || dni.length === 0) {
            return { valido: false, mensaje: '' };
        }

        if (!config.PATTERNS.SOLO_NUMEROS.test(dni)) {
            return { 
                valido: false, 
                mensaje: config.MENSAJES.DNI.SOLO_NUMEROS 
            };
        }

        if (dni.length < config.VALIDACION.DNI_LENGTH) {
            const faltantes = config.VALIDACION.DNI_LENGTH - dni.length;
            return { 
                valido: false, 
                mensaje: `Faltan ${faltantes} dígitos` 
            };
        }

        if (!config.PATTERNS.DNI.test(dni)) {
            return { 
                valido: false, 
                mensaje: 'El DNI debe tener exactamente 8 dígitos numéricos.' 
            };
        }

        return { valido: true, mensaje: '' };
    }

    /**
     * Valida un campo de nombre de usuario
     * @param {string} username - Nombre de usuario
     * @returns {boolean}
     */
    function validarUsername(username) {
        return config.PATTERNS.USERNAME.test(username);
    }

    /**
     * Valida un campo de teléfono
     * @param {string} telefono - Número de teléfono
     * @returns {boolean}
     */
    function validarTelefono(telefono) {
        return config.PATTERNS.TELEFONO.test(telefono);
    }

    /**
     * Establece un mensaje de validación personalizado en un campo
     * @param {jQuery} $campo - Campo jQuery
     * @param {string} mensaje - Mensaje de error
     */
    function establecerMensajeValidacion($campo, mensaje) {
        if ($campo.length && $campo[0].setCustomValidity) {
            $campo[0].setCustomValidity(mensaje);
        }
    }

    /**
     * Limpia el mensaje de validación de un campo
     * @param {jQuery} $campo - Campo jQuery
     */
    function limpiarMensajeValidacion($campo) {
        establecerMensajeValidacion($campo, '');
    }

    /**
     * Valida un formulario completo
     * @param {HTMLFormElement} form - Formulario a validar
     * @returns {boolean}
     */
    function validarFormulario(form) {
        return form.checkValidity();
    }

    /**
     * Marca un formulario como validado (muestra errores)
     * @param {jQuery} $form - Formulario jQuery
     */
    function marcarFormularioValidado($form) {
        $form.addClass('was-validated');
    }

    /**
     * Limpia la validación de un formulario
     * @param {jQuery} $form - Formulario jQuery
     */
    function limpiarValidacionFormulario($form) {
        $form.removeClass('was-validated');
        $form.find(`.${config.CLASES.VALIDO}, .${config.CLASES.INVALIDO}`)
             .removeClass(`${config.CLASES.VALIDO} ${config.CLASES.INVALIDO}`);
    }

    // API Pública
    return {
        calcularFortalezaPassword,
        actualizarRequisitosPassword,
        esPasswordValida,
        passwordsCoinciden,
        validarFormatoDNI,
        validarUsername,
        validarTelefono,
        establecerMensajeValidacion,
        limpiarMensajeValidacion,
        validarFormulario,
        marcarFormularioValidado,
        limpiarValidacionFormulario
    };
})();

// Hacer disponible globalmente
window.ValidationModule = ValidationModule;
