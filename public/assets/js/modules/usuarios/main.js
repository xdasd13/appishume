/**
 * Módulo Principal - Creación de Usuarios
 * 
 * Orquesta todos los módulos y maneja la lógica principal:
 * - Inicialización de componentes
 * - Manejo de eventos de formularios
 * - Coordinación entre módulos
 * - Envío de formularios
 */

const UsuariosCrearApp = (function() {
    'use strict';

    const config = window.UsuariosConfig;
    const ui = window.UIHandler;
    const api = window.APIClient;
    const validation = window.ValidationModule;
    const dniValidator = window.DNIValidator;

    /**
     * Verifica si los campos del formulario existente tienen contenido
     */
    function verificarCamposFormularioExistente() {
        const campos = [
            $(config.SELECTORES.IDPERSONA).val(),
            $(config.SELECTORES.CARGO_EXISTENTE).val(),
            $(config.SELECTORES.USERNAME_EXISTENTE).val().trim(),
            $(config.SELECTORES.EMAIL_EXISTENTE).val().trim(),
            $(config.SELECTORES.PASSWORD_EXISTENTE).val()
        ];

        ui.actualizarEstadoBoton($(config.SELECTORES.BTN_SUBMIT_EXISTENTE), campos);
    }

    /**
     * Verifica si los campos del formulario nuevo tienen contenido
     */
    function verificarCamposFormularioNuevo() {
        const campos = [
            $(config.SELECTORES.DNI).val().trim(),
            $(config.SELECTORES.NOMBRES).val().trim(),
            $(config.SELECTORES.APELLIDOS).val().trim(),
            $(config.SELECTORES.TELEFONO_PRINCIPAL).val().trim(),
            $(config.SELECTORES.DIRECCION).val().trim(),
            $(config.SELECTORES.CARGO_NUEVO).val(),
            $(config.SELECTORES.USERNAME_NUEVO).val().trim(),
            $(config.SELECTORES.EMAIL_NUEVO).val().trim(),
            $(config.SELECTORES.PASSWORD_NUEVO).val()
        ];

        ui.actualizarEstadoBoton($(config.SELECTORES.BTN_SUBMIT_NUEVO), campos);
    }

    /**
     * Maneja la validación de contraseña en tiempo real
     * @param {jQuery} $passwordField - Campo de contraseña
     * @param {string} formType - 'existente' o 'nuevo'
     */
    function manejarValidacionPassword($passwordField, formType) {
        const password = $passwordField.val();
        const strength = validation.calcularFortalezaPassword(password);

        // Actualizar barra de fortaleza
        ui.actualizarBarraFortaleza($passwordField, strength);

        // Actualizar requisitos visuales
        validation.actualizarRequisitosPassword(password, formType);

        // Validar si cumple requisitos
        if (validation.esPasswordValida(password)) {
            validation.limpiarMensajeValidacion($passwordField);
        } else {
            validation.establecerMensajeValidacion(
                $passwordField,
                'La contraseña no cumple con los requisitos de seguridad'
            );
        }
    }

    /**
     * Maneja la validación de confirmación de contraseña
     * @param {jQuery} $confirmField - Campo de confirmación
     * @param {jQuery} $passwordField - Campo de contraseña original
     */
    function manejarConfirmacionPassword($confirmField, $passwordField) {
        const password = $passwordField.val();
        const confirmPassword = $confirmField.val();

        if (validation.passwordsCoinciden(password, confirmPassword)) {
            validation.limpiarMensajeValidacion($confirmField);
        } else {
            validation.establecerMensajeValidacion(
                $confirmField,
                'Las contraseñas no coinciden'
            );
        }
    }

    /**
     * Valida un formulario antes de enviarlo
     * @param {HTMLFormElement} form - Formulario a validar
     * @param {string} formType - 'existente' o 'nuevo'
     * @returns {boolean}
     */
    function validarFormularioCompleto(form, formType) {
        const $form = $(form);

        // Validación HTML5
        if (!validation.validarFormulario(form)) {
            validation.marcarFormularioValidado($form);
            ui.mostrarAlerta(
                'error',
                config.MENSAJES.ERROR.VALIDACION,
                config.MENSAJES.ERROR.VALIDACION_TEXTO
            );
            return false;
        }

        // Obtener campos según tipo de formulario
        const passwordSelector = formType === 'existente' ? 
            config.SELECTORES.PASSWORD_EXISTENTE : 
            config.SELECTORES.PASSWORD_NUEVO;
        
        const confirmSelector = formType === 'existente' ? 
            config.SELECTORES.CONFIRM_PASSWORD_EXISTENTE : 
            config.SELECTORES.CONFIRM_PASSWORD_NUEVO;

        const $password = $(passwordSelector);
        const $confirm = $(confirmSelector);

        // Validar coincidencia de contraseñas
        if (!validation.passwordsCoinciden($password.val(), $confirm.val())) {
            validation.establecerMensajeValidacion($confirm, 'Las contraseñas no coinciden');
            $confirm[0].reportValidity();
            ui.mostrarAlerta(
                'error',
                config.MENSAJES.ERROR.PASSWORD_NO_COINCIDE,
                config.MENSAJES.ERROR.PASSWORD_NO_COINCIDE_TEXTO
            );
            return false;
        }

        // Validar fortaleza de contraseña
        if (!validation.esPasswordValida($password.val())) {
            validation.establecerMensajeValidacion(
                $password,
                'La contraseña no cumple con los requisitos de seguridad'
            );
            $password[0].reportValidity();
            ui.mostrarAlerta(
                'error',
                config.MENSAJES.ERROR.PASSWORD_DEBIL,
                config.MENSAJES.ERROR.PASSWORD_DEBIL_TEXTO
            );
            return false;
        }

        return true;
    }

    /**
     * Procesa el envío del formulario
     * @param {HTMLFormElement} form - Formulario
     * @param {jQuery} $submitButton - Botón de submit
     * @param {string} textoOriginal - Texto original del botón
     */
    function procesarEnvioFormulario(form, $submitButton, textoOriginal) {
        const formData = new FormData(form);

        ui.mostrarLoading();

        api.guardarUsuario(formData)
            .done(function(response) {
                ui.cerrarAlerta();

                if (response.success) {
                    ui.mostrarAlerta(
                        'success',
                        config.MENSAJES.EXITO.TITULO,
                        config.MENSAJES.EXITO.TEXTO
                    );

                    // Limpiar formulario
                    form.reset();
                    validation.limpiarValidacionFormulario($(form));

                    // Redireccionar después de un delay
                    setTimeout(function() {
                        window.location.href = api.buildUrl('/usuarios');
                    }, config.TIMEOUTS.REDIRECT_DELAY);
                } else {
                    ui.mostrarAlerta(
                        'error',
                        config.MENSAJES.ERROR.VALIDACION,
                        response.message || config.MENSAJES.ERROR.VALIDACION_TEXTO
                    );

                    if (response.errors) {
                        ui.mostrarErroresFormulario(response.errors, $(form));
                    }
                }

                ui.habilitarBoton($submitButton);
            })
            .fail(function(xhr, status, error) {
                ui.cerrarAlerta();
                ui.mostrarAlerta(
                    'error',
                    config.MENSAJES.ERROR.GENERICO,
                    'Error en la solicitud: ' + error
                );
                ui.habilitarBoton($submitButton);
            });
    }

    /**
     * Maneja el submit de un formulario
     * @param {Event} e - Evento de submit
     * @param {string} formType - 'existente' o 'nuevo'
     */
    function manejarSubmitFormulario(e, formType) {
        e.preventDefault();

        const form = e.target;
        const $form = $(form);
        const $submitButton = $form.find('button[type="submit"]');

        // Determinar texto original del botón
        const textoOriginal = formType === 'existente' ?
            '<i class="fas fa-save me-1"></i> Crear Credenciales' :
            '<i class="fas fa-save me-1"></i> Crear Personal y Credenciales';

        // Validar formulario
        if (!validarFormularioCompleto(form, formType)) {
            return;
        }

        // Mostrar confirmación
        ui.mostrarConfirmacion().then((result) => {
            if (result.isConfirmed) {
                ui.deshabilitarBoton($submitButton, textoOriginal);
                procesarEnvioFormulario(form, $submitButton, textoOriginal);
            }
        });
    }

    /**
     * Inicializa los event listeners para validación de contraseñas
     */
    function inicializarValidacionPasswords() {
        // Formulario Existente
        $(config.SELECTORES.PASSWORD_EXISTENTE).on('input', function() {
            manejarValidacionPassword($(this), 'existente');
        });

        $(config.SELECTORES.CONFIRM_PASSWORD_EXISTENTE).on('input', function() {
            manejarConfirmacionPassword(
                $(this),
                $(config.SELECTORES.PASSWORD_EXISTENTE)
            );
        });

        // Formulario Nuevo
        $(config.SELECTORES.PASSWORD_NUEVO).on('input', function() {
            manejarValidacionPassword($(this), 'nuevo');
        });

        $(config.SELECTORES.CONFIRM_PASSWORD_NUEVO).on('input', function() {
            manejarConfirmacionPassword(
                $(this),
                $(config.SELECTORES.PASSWORD_NUEVO)
            );
        });
    }

    /**
     * Inicializa los event listeners para habilitar/deshabilitar botones
     */
    function inicializarControlBotones() {
        // Formulario Existente
        $(`${config.SELECTORES.FORM_EXISTENTE} input, ${config.SELECTORES.FORM_EXISTENTE} select`)
            .on('input change', verificarCamposFormularioExistente);

        // Formulario Nuevo
        $(`${config.SELECTORES.FORM_NUEVO} input, ${config.SELECTORES.FORM_NUEVO} select`)
            .on('input change', verificarCamposFormularioNuevo);

        // Verificar estado inicial
        verificarCamposFormularioExistente();
        verificarCamposFormularioNuevo();
    }

    /**
     * Inicializa los event listeners para submit de formularios
     */
    function inicializarSubmitFormularios() {
        $(config.SELECTORES.FORM_EXISTENTE).on('submit', function(e) {
            manejarSubmitFormulario(e, 'existente');
        });

        $(config.SELECTORES.FORM_NUEVO).on('submit', function(e) {
            // Prevenir submit si hay validación de DNI en progreso
            if (dniValidator.estaValidando()) {
                e.preventDefault();
                ui.mostrarAlerta(
                    'info',
                    config.MENSAJES.ERROR.DNI_VALIDACION_PROGRESO,
                    config.MENSAJES.ERROR.DNI_VALIDACION_PROGRESO_TEXTO
                );
                return false;
            }

            manejarSubmitFormulario(e, 'nuevo');
        });
    }

    /**
     * Inicializa el evento para cargar datos de persona seleccionada
     */
    function inicializarCargaPersona() {
        $(config.SELECTORES.IDPERSONA).on('change', function() {
            const idpersona = $(this).val();
            if (idpersona) {
                api.obtenerPersona(idpersona)
                    .done(function(response) {
                        if (response.success) {
                            // Aquí se pueden usar los datos si es necesario
                            console.log('Datos de persona cargados:', response.data);
                        }
                    });
            }
        });
    }

    /**
     * Inicializa todos los componentes de la aplicación
     */
    function inicializar() {
        // Inicializar toggle de contraseñas
        ui.inicializarTogglePassword();

        // Inicializar validación de contraseñas
        inicializarValidacionPasswords();

        // Inicializar control de botones
        inicializarControlBotones();

        // Inicializar submit de formularios
        inicializarSubmitFormularios();

        // Inicializar carga de persona
        inicializarCargaPersona();

        // Inicializar validador de DNI
        dniValidator.inicializar();
    }

    // API Pública
    return {
        inicializar
    };
})();

// Inicializar cuando el DOM esté listo
$(document).ready(function() {
    // Establecer base_url global si no existe
    if (typeof window.base_url === 'undefined') {
        window.base_url = $('base').attr('href') || '';
    }

    // Inicializar aplicación
    UsuariosCrearApp.inicializar();
});
