/**
 * Módulo de Validación de DNI
 * 
 * Maneja la validación automática de DNI con RENIEC:
 * - Validación en tiempo real con debounce
 * - Autocompletado de datos desde RENIEC
 * - Generación automática de email
 * - Manejo de diferentes estados (activo, inactivo, sin usuario)
 */

const DNIValidator = (function() {
    'use strict';

    const config = window.UsuariosConfig;
    const ui = window.UIHandler;
    const api = window.APIClient;
    const validation = window.ValidationModule;

    // Estado interno del módulo
    let dniValidationTimeout = null;
    let lastValidatedDni = '';
    let validationInProgress = false;

    /**
     * Genera un email automático basado en nombres y apellidos
     * @param {string} nombres - Nombres de la persona
     * @param {string} apellidos - Apellidos de la persona
     * @returns {string} - Email generado
     */
    function generarEmailAutomatico(nombres, apellidos) {
        function limpiarTexto(texto) {
            return texto
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '') // Eliminar acentos
                .replace(/[^a-z\s]/g, '')         // Solo letras y espacios
                .trim();
        }

        const nombresLimpio = limpiarTexto(nombres || '');
        const apellidosLimpio = limpiarTexto(apellidos || '');

        if (!nombresLimpio || !apellidosLimpio) {
            return '';
        }

        const primerNombre = nombresLimpio.split(' ')[0];
        const primerApellido = apellidosLimpio.split(' ')[0];

        return `${primerNombre.charAt(0)}${primerApellido}${config.EMAIL.DOMINIO}`;
    }

    /**
     * Resetea el estado visual de validación del DNI
     */
    function resetearEstadoVisual() {
        ui.ocultar($(config.SELECTORES.DNI_LOADING));
        ui.ocultar($(config.SELECTORES.DNI_SUCCESS));
        ui.ocultar($(config.SELECTORES.PRIVACY_NOTICE));
        ui.actualizarTexto(
            $(config.SELECTORES.DNI_ERROR), 
            'Por favor ingrese un número de documento válido.'
        );
    }

    /**
     * Muestra el estado de carga durante validación
     */
    function mostrarCargando() {
        resetearEstadoVisual();
        ui.mostrar($(config.SELECTORES.DNI_LOADING));
        ui.limpiarMarcasValidacion($(config.SELECTORES.DNI));
    }

    /**
     * Muestra éxito en la validación
     * @param {string} mensaje - Mensaje de éxito
     */
    function mostrarExito(mensaje) {
        resetearEstadoVisual();
        ui.actualizarTexto($(config.SELECTORES.DNI_SUCCESS), mensaje);
        ui.mostrar($(config.SELECTORES.DNI_SUCCESS));
        ui.marcarCampoValido($(config.SELECTORES.DNI));
    }

    /**
     * Muestra error en la validación
     * @param {string} mensaje - Mensaje de error
     */
    function mostrarError(mensaje) {
        resetearEstadoVisual();
        ui.actualizarTexto($(config.SELECTORES.DNI_ERROR), mensaje);
        ui.marcarCampoInvalido($(config.SELECTORES.DNI));
        limpiarCamposAutocompletados();
    }

    /**
     * Limpia los campos que fueron autocompletados
     */
    function limpiarCamposAutocompletados() {
        const $nombres = $(config.SELECTORES.NOMBRES);
        const $apellidos = $(config.SELECTORES.APELLIDOS);
        const $email = $(config.SELECTORES.EMAIL_NUEVO);

        ui.limpiarCampo($nombres);
        ui.limpiarCampo($apellidos);
        ui.limpiarCampo($email);
        
        ui.limpiarMarcasValidacion($nombres);
        ui.limpiarMarcasValidacion($apellidos);
        ui.limpiarMarcasValidacion($email);
        
        ui.quitarReadonly($nombres);
        ui.quitarReadonly($apellidos);
        
        ui.ocultar($(config.SELECTORES.PRIVACY_NOTICE));
    }

    /**
     * Maneja DNI válido encontrado en RENIEC
     * @param {Object} response - Respuesta del servidor
     */
    function manejarDNIValido(response) {
        const data = response.data;
        
        mostrarExito(config.MENSAJES.DNI.VALIDO);

        // Extraer nombres y apellidos
        const nombres = data.nombres || '';
        const apellidos = data.apellidos_completos || 
                         (data.apellido_paterno && data.apellido_materno ? 
                          data.apellido_paterno + ' ' + data.apellido_materno : 
                          data.apellido_paterno || data.apellido_materno || '');

        // Autocompletar campos
        const $nombres = $(config.SELECTORES.NOMBRES);
        const $apellidos = $(config.SELECTORES.APELLIDOS);
        const $email = $(config.SELECTORES.EMAIL_NUEVO);

        ui.establecerValor($nombres, nombres);
        ui.establecerValor($apellidos, apellidos);
        ui.marcarCampoValido($nombres);
        ui.marcarCampoValido($apellidos);

        // Generar email automáticamente
        const emailGenerado = generarEmailAutomatico(nombres, apellidos);
        if (emailGenerado) {
            ui.establecerValor($email, emailGenerado);
            ui.marcarCampoValido($email);
        }

        // Marcar como readonly
        ui.establecerReadonly($nombres);
        ui.establecerReadonly($apellidos);
        ui.mostrar($(config.SELECTORES.PRIVACY_NOTICE));

        // Mostrar notificación
        ui.mostrarToast(
            'success',
            '¡DNI Válido!',
            `<strong>Datos encontrados en RENIEC:</strong><br>
             <strong>Nombres:</strong> ${data.nombres}<br>
             <strong>Apellidos:</strong> ${data.apellidos_completos}<br>
             <small class="text-muted">Fuente: ${data.source === 'cache' ? 'Cache local' : 'RENIEC'}</small>`
        );
    }

    /**
     * Maneja DNI con usuario activo
     * @param {Object} response - Respuesta del servidor
     */
    function manejarUsuarioActivo(response) {
        const data = response.data;
        
        mostrarError(config.MENSAJES.DNI.YA_EXISTE_ACTIVO);

        Swal.fire({
            icon: 'error',
            title: 'Usuario Ya Existe',
            html: `
                <div class="text-start">
                    <p><strong>Este DNI ya tiene credenciales activas:</strong></p>
                    <hr>
                    <p><i class="fas fa-user me-2"></i><strong>Nombre:</strong> ${data.nombres} ${data.apellidos}</p>
                    <p><i class="fas fa-id-card me-2"></i><strong>DNI:</strong> ${data.dni}</p>
                    <p><i class="fas fa-envelope me-2"></i><strong>Email:</strong> ${data.email}</p>
                    <p><i class="fas fa-user-circle me-2"></i><strong>Usuario:</strong> ${data.usuario}</p>
                    <p><span class="badge bg-success">Estado: ACTIVO</span></p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Ver Lista de Usuarios',
            cancelButtonText: 'Entendido',
            confirmButtonColor: config.SWAL.INFO_BUTTON_COLOR,
            width: '500px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = api.buildUrl('/usuarios');
            }
        });
    }

    /**
     * Maneja DNI con usuario desactivado
     * @param {Object} response - Respuesta del servidor
     */
    function manejarUsuarioInactivo(response) {
        const data = response.data;
        
        mostrarError(config.MENSAJES.DNI.YA_EXISTE_INACTIVO);

        Swal.fire({
            icon: 'warning',
            title: 'Usuario Desactivado',
            html: `
                <div class="text-start">
                    <p><strong>Este DNI pertenece a un usuario desactivado:</strong></p>
                    <hr>
                    <p><i class="fas fa-user me-2"></i><strong>Nombre:</strong> ${data.nombres} ${data.apellidos}</p>
                    <p><i class="fas fa-id-card me-2"></i><strong>DNI:</strong> ${data.dni}</p>
                    <p><i class="fas fa-envelope me-2"></i><strong>Email:</strong> ${data.email}</p>
                    <p><i class="fas fa-user-circle me-2"></i><strong>Usuario:</strong> ${data.usuario}</p>
                    <p><span class="badge bg-danger">Estado: DESACTIVADO</span></p>
                    <hr>
                    <p class="text-info"><i class="fas fa-info-circle me-2"></i>¿Desea reactivar este usuario?</p>
                </div>
            `,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Reactivar Usuario',
            denyButtonText: 'Ver Lista de Usuarios',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: config.SWAL.SUCCESS_BUTTON_COLOR,
            denyButtonColor: config.SWAL.INFO_BUTTON_COLOR,
            width: '550px'
        }).then((result) => {
            if (result.isConfirmed) {
                reactivarUsuario(data.idusuario, data.nombres + ' ' + data.apellidos);
            } else if (result.isDenied) {
                window.location.href = api.buildUrl('/usuarios');
            }
        });
    }

    /**
     * Maneja DNI de persona sin usuario
     * @param {Object} response - Respuesta del servidor
     */
    function manejarPersonaSinUsuario(response) {
        const data = response.data;
        
        mostrarError(config.MENSAJES.DNI.PERSONA_SIN_USUARIO);

        Swal.fire({
            icon: 'info',
            title: 'Persona Ya Registrada',
            html: `
                <div class="text-start">
                    <p><strong>Esta persona ya está en el sistema:</strong></p>
                    <hr>
                    <p><i class="fas fa-user me-2"></i><strong>Nombre:</strong> ${data.nombres} ${data.apellidos}</p>
                    <p><i class="fas fa-id-card me-2"></i><strong>DNI:</strong> ${data.dni}</p>
                    <p><span class="badge bg-warning">Sin credenciales de acceso</span></p>
                    <hr>
                    <p class="text-info"><i class="fas fa-info-circle me-2"></i>Se recomienda crear credenciales para esta persona existente.</p>
                </div>
            `,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Crear Credenciales',
            denyButtonText: 'Ver Lista de Personal',
            cancelButtonText: 'Continuar Aquí',
            confirmButtonColor: config.SWAL.SUCCESS_BUTTON_COLOR,
            denyButtonColor: config.SWAL.INFO_BUTTON_COLOR,
            width: '550px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = api.buildUrl('/usuarios/crear/existente');
            } else if (result.isDenied) {
                window.location.href = api.buildUrl('/usuarios');
            }
        });
    }

    /**
     * Reactiva un usuario desactivado
     * @param {number} idusuario - ID del usuario
     * @param {string} nombreCompleto - Nombre completo del usuario
     */
    function reactivarUsuario(idusuario, nombreCompleto) {
        ui.mostrarLoading('Reactivando Usuario...', 'Por favor espere');

        // Obtener tokens CSRF del DOM
        const csrfToken = $('input[name^="csrf_"]').attr('name');
        const csrfHash = $('input[name^="csrf_"]').val();

        api.reactivarUsuario(idusuario, csrfToken, csrfHash)
            .done(function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Usuario Reactivado!',
                        text: `Las credenciales de ${nombreCompleto} han sido reactivadas exitosamente.`,
                        confirmButtonColor: config.SWAL.SUCCESS_BUTTON_COLOR,
                        timer: config.SWAL.SUCCESS_TIMER,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = api.buildUrl('/usuarios');
                    });
                } else {
                    ui.mostrarAlerta(
                        'error',
                        'Error',
                        response.message || 'No se pudo reactivar el usuario'
                    );
                }
            })
            .fail(function() {
                ui.mostrarAlerta(
                    'error',
                    'Error de Conexión',
                    'No se pudo conectar con el servidor'
                );
            });
    }

    /**
     * Valida un DNI con RENIEC
     * @param {string} dni - DNI a validar
     */
    function validarConRENIEC(dni) {
        if (validationInProgress) {
            return;
        }

        validationInProgress = true;
        mostrarCargando();

        // Obtener tokens CSRF del DOM
        const csrfToken = $('input[name^="csrf_"]').attr('name');
        const csrfHash = $('input[name^="csrf_"]').val();

        api.validarDNI(dni, csrfToken, csrfHash)
            .done(function(response) {
                validationInProgress = false;
                lastValidatedDni = dni;

                if (response.status === 'success') {
                    manejarDNIValido(response);
                } else if (response.status === 'exists_active') {
                    manejarUsuarioActivo(response);
                } else if (response.status === 'exists_inactive') {
                    manejarUsuarioInactivo(response);
                } else if (response.status === 'exists_no_user') {
                    manejarPersonaSinUsuario(response);
                } else {
                    mostrarError(response.message || 'DNI no encontrado');
                }
            })
            .fail(function(xhr, status, error) {
                validationInProgress = false;
                const errorInfo = api.manejarErrorAjax(xhr, status, error);
                mostrarError(errorInfo.mensaje);
            });
    }

    /**
     * Inicializa la validación automática de DNI
     */
    function inicializar() {
        const $dniInput = $(config.SELECTORES.DNI);

        $dniInput.on('input', function() {
            const dni = $(this).val().trim();

            // Limpiar timeout anterior
            clearTimeout(dniValidationTimeout);
            resetearEstadoVisual();

            // Validar formato básico
            if (dni.length === 0) {
                return;
            }

            const validacion = validation.validarFormatoDNI(dni);
            
            if (!validacion.valido && validacion.mensaje) {
                if (validacion.mensaje.includes('Faltan')) {
                    ui.actualizarTexto($(config.SELECTORES.DNI_EXAMPLE), validacion.mensaje);
                    $(config.SELECTORES.DNI_EXAMPLE).addClass('text-muted');
                } else {
                    mostrarError(validacion.mensaje);
                }
                return;
            } else {
                ui.actualizarTexto(
                    $(config.SELECTORES.DNI_EXAMPLE), 
                    'Ejemplo: 12345678 (8 dígitos)'
                );
                $(config.SELECTORES.DNI_EXAMPLE).removeClass('text-muted');
            }

            // Si es el mismo DNI ya validado, no revalidar
            if (dni === lastValidatedDni) {
                return;
            }

            // Debounce: esperar antes de validar
            dniValidationTimeout = setTimeout(() => {
                validarConRENIEC(dni);
            }, config.TIMEOUTS.DNI_DEBOUNCE);
        });

        // Limpiar cuando se borra el campo
        $dniInput.on('keyup', function() {
            if ($(this).val().length === 0) {
                resetearEstadoVisual();
                ui.limpiarMarcasValidacion($(this));
                limpiarCamposAutocompletados();
                lastValidatedDni = '';
            }
        });
    }

    /**
     * Verifica si hay una validación en progreso
     * @returns {boolean}
     */
    function estaValidando() {
        return validationInProgress;
    }

    // API Pública
    return {
        inicializar,
        estaValidando,
        generarEmailAutomatico
    };
})();

// Hacer disponible globalmente
window.DNIValidator = DNIValidator;
