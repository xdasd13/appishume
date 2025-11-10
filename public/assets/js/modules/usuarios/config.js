/**
 * Configuración del Módulo de Usuarios
 * 
 * Centraliza todas las constantes, URLs y configuraciones
 * para facilitar el mantenimiento y evitar valores hardcodeados.
 */

const UsuariosConfig = {
    // URLs del API
    API: {
        GUARDAR_USUARIO: '/usuarios/guardar',
        CHECK_DNI: '/usuarios/ajax-check-dni',
        OBTENER_PERSONA: '/usuarios/obtenerPersona/',
        REACTIVAR_USUARIO: '/usuarios/reactivar/'
    },

    // Timeouts y delays
    TIMEOUTS: {
        DNI_DEBOUNCE: 500,           // Espera antes de validar DNI
        AJAX_TIMEOUT: 15000,         // Timeout para llamadas AJAX
        BUTTON_RESET_SAFETY: 30000,  // Timeout de seguridad para botones
        REDIRECT_DELAY: 2000,        // Delay antes de redireccionar
        ERROR_RESET_DELAY: 100       // Delay para resetear en errores
    },

    // Validaciones
    VALIDACION: {
        DNI_LENGTH: 8,
        PASSWORD_MIN_LENGTH: 8,
        PASSWORD_MIN_SCORE: 3,       // Score mínimo de fortaleza (de 5)
        NOMBRE_MIN_LENGTH: 2,
        NOMBRE_MAX_LENGTH: 50,
        DIRECCION_MIN_LENGTH: 5,
        DIRECCION_MAX_LENGTH: 150,
        TELEFONO_LENGTH: 9,
        USERNAME_MIN_LENGTH: 4,
        USERNAME_MAX_LENGTH: 20
    },

    // Patrones de validación (RegEx)
    PATTERNS: {
        DNI: /^\d{8}$/,
        TELEFONO: /^[0-9]{9}$/,
        USERNAME: /^[a-zA-Z0-9_\-]{4,20}$/,
        SOLO_NUMEROS: /^\d+$/,
        MAYUSCULA: /[A-Z]/,
        MINUSCULA: /[a-z]/,
        NUMERO: /[0-9]/,
        SIMBOLO: /[^A-Za-z0-9]/
    },

    // Clases CSS para estados
    CLASES: {
        VALIDO: 'is-valid',
        INVALIDO: 'is-invalid',
        READONLY_BG: 'bg-light',
        REQUIREMENT_MET: 'requirement-met',
        REQUIREMENT_UNMET: 'requirement-unmet',
        PASSWORD_STRENGTH: {
            DEBIL: 'bg-danger',
            REGULAR: 'bg-warning',
            FUERTE: 'bg-success'
        }
    },

    // Selectores jQuery
    SELECTORES: {
        // Formularios
        FORM_EXISTENTE: '#formExistente',
        FORM_NUEVO: '#formNuevo',
        
        // Botones
        BTN_SUBMIT_EXISTENTE: '#btnSubmitExistente',
        BTN_SUBMIT_NUEVO: '#btnSubmitNuevo',
        PASSWORD_TOGGLE: '.password-toggle',
        
        // Campos - Formulario Existente
        IDPERSONA: '#idpersona',
        CARGO_EXISTENTE: '#idcargo_existente',
        USERNAME_EXISTENTE: '#nombreusuario_existente',
        EMAIL_EXISTENTE: '#email_existente',
        PASSWORD_EXISTENTE: '#password_existente',
        CONFIRM_PASSWORD_EXISTENTE: '#confirm_password_existente',
        
        // Campos - Formulario Nuevo
        DNI: '#numerodoc',
        NOMBRES: '#nombres',
        APELLIDOS: '#apellidos',
        TELEFONO_PRINCIPAL: '#telprincipal',
        DIRECCION: '#direccion',
        CARGO_NUEVO: '#idcargo_nuevo',
        USERNAME_NUEVO: '#nombreusuario_nuevo',
        EMAIL_NUEVO: '#email_nuevo',
        PASSWORD_NUEVO: '#password_nuevo',
        CONFIRM_PASSWORD_NUEVO: '#confirm_password_nuevo',
        
        // Feedback visual
        DNI_LOADING: '#loading-numerodoc',
        DNI_SUCCESS: '#success-numerodoc',
        DNI_ERROR: '#error-numerodoc',
        DNI_EXAMPLE: '#example-numerodoc',
        PRIVACY_NOTICE: '#privacy-notice'
    },

    // Mensajes de usuario
    MENSAJES: {
        CONFIRMACION: {
            TITULO: '¿Está seguro?',
            TEXTO: '¿Desea guardar los datos del usuario?',
            CONFIRMAR: 'Sí, guardar',
            CANCELAR: 'Cancelar'
        },
        PROCESANDO: {
            TITULO: 'Creando...',
            TEXTO: 'Guardando información del usuario...'
        },
        EXITO: {
            TITULO: '¡Éxito!',
            TEXTO: 'Usuario creado correctamente.'
        },
        ERROR: {
            VALIDACION: 'Error de validación',
            VALIDACION_TEXTO: 'Por favor complete todos los campos correctamente.',
            PASSWORD_NO_COINCIDE: 'Las contraseñas no coinciden',
            PASSWORD_NO_COINCIDE_TEXTO: 'Las contraseñas ingresadas no coinciden.',
            PASSWORD_DEBIL: 'Contraseña débil',
            PASSWORD_DEBIL_TEXTO: 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo.',
            GENERICO: 'Error',
            DNI_VALIDACION_PROGRESO: 'Validación en Progreso',
            DNI_VALIDACION_PROGRESO_TEXTO: 'Por favor espere a que termine la validación del DNI'
        },
        DNI: {
            SOLO_NUMEROS: 'Solo se permiten números',
            TIMEOUT: 'Tiempo de espera agotado. Intente nuevamente.',
            RATE_LIMIT: 'Demasiadas consultas. Espere un momento e intente nuevamente.',
            ERROR_CONEXION: 'Error de conexión. Verifique su internet e intente nuevamente.',
            VALIDO: 'DNI válido encontrado en RENIEC',
            YA_EXISTE_ACTIVO: 'Este DNI ya tiene credenciales ACTIVAS',
            YA_EXISTE_INACTIVO: 'Este DNI tiene credenciales DESACTIVADAS',
            PERSONA_SIN_USUARIO: 'Esta persona ya está registrada pero sin credenciales'
        }
    },

    // Configuración de SweetAlert
    SWAL: {
        CONFIRM_BUTTON_COLOR: '#4e73df',
        CANCEL_BUTTON_COLOR: '#6c757d',
        SUCCESS_BUTTON_COLOR: '#28a745',
        DANGER_BUTTON_COLOR: '#dc3545',
        INFO_BUTTON_COLOR: '#3085d6',
        TOAST_TIMER: 4000,
        SUCCESS_TIMER: 3000
    },

    // Configuración de generación de email
    EMAIL: {
        DOMINIO: '@ishume.com'
    }
};

// Hacer disponible globalmente
window.UsuariosConfig = UsuariosConfig;