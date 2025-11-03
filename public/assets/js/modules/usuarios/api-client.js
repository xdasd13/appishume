/**
 * Módulo de Cliente API
 * 
 * Maneja todas las llamadas AJAX al servidor:
 * - Guardar usuario
 * - Validar DNI con RENIEC
 * - Obtener datos de persona
 * - Reactivar usuario
 */

const APIClient = (function() {
    'use strict';

    const config = window.UsuariosConfig;

    /**
     * Obtiene el base URL del sitio
     * @returns {string}
     */
    function getBaseUrl() {
        // Asume que hay una variable global base_url o la extrae del DOM
        return window.base_url || '';
    }

    /**
     * Construye la URL completa para un endpoint
     * @param {string} endpoint - Endpoint relativo
     * @returns {string}
     */
    function buildUrl(endpoint) {
        return getBaseUrl() + endpoint;
    }

    /**
     * Guarda un usuario (crear nuevo)
     * @param {FormData} formData - Datos del formulario
     * @returns {Promise}
     */
    function guardarUsuario(formData) {
        return $.ajax({
            url: buildUrl(config.API.GUARDAR_USUARIO),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            timeout: config.TIMEOUTS.AJAX_TIMEOUT
        });
    }

    /**
     * Valida un DNI con RENIEC
     * @param {string} dni - DNI a validar
     * @param {string} csrfToken - Token CSRF
     * @param {string} csrfHash - Hash CSRF
     * @returns {Promise}
     */
    function validarDNI(dni, csrfToken, csrfHash) {
        const data = {
            dni: dni
        };
        data[csrfToken] = csrfHash;

        return $.ajax({
            url: buildUrl(config.API.CHECK_DNI),
            type: 'POST',
            data: data,
            timeout: config.TIMEOUTS.AJAX_TIMEOUT
        });
    }

    /**
     * Obtiene los datos de una persona por ID
     * @param {number} idpersona - ID de la persona
     * @returns {Promise}
     */
    function obtenerPersona(idpersona) {
        return $.ajax({
            url: buildUrl(config.API.OBTENER_PERSONA + idpersona),
            type: 'GET',
            timeout: config.TIMEOUTS.AJAX_TIMEOUT
        });
    }

    /**
     * Reactiva un usuario desactivado
     * @param {number} idusuario - ID del usuario
     * @param {string} csrfToken - Token CSRF
     * @param {string} csrfHash - Hash CSRF
     * @returns {Promise}
     */
    function reactivarUsuario(idusuario, csrfToken, csrfHash) {
        const data = {};
        data[csrfToken] = csrfHash;

        return $.ajax({
            url: buildUrl(config.API.REACTIVAR_USUARIO + idusuario),
            type: 'POST',
            data: data,
            timeout: config.TIMEOUTS.AJAX_TIMEOUT
        });
    }

    /**
     * Maneja errores de AJAX de forma estandarizada
     * @param {Object} xhr - Objeto XMLHttpRequest
     * @param {string} status - Estado del error
     * @param {string} error - Mensaje de error
     * @returns {Object} - {mensaje, tipo}
     */
    function manejarErrorAjax(xhr, status, error) {
        if (status === 'timeout') {
            return {
                mensaje: config.MENSAJES.DNI.TIMEOUT,
                tipo: 'timeout'
            };
        }

        if (xhr.status === 429) {
            return {
                mensaje: config.MENSAJES.DNI.RATE_LIMIT,
                tipo: 'rate_limit'
            };
        }

        return {
            mensaje: config.MENSAJES.DNI.ERROR_CONEXION,
            tipo: 'error_conexion'
        };
    }

    // API Pública
    return {
        guardarUsuario,
        validarDNI,
        obtenerPersona,
        reactivarUsuario,
        manejarErrorAjax,
        buildUrl
    };
})();

// Hacer disponible globalmente
window.APIClient = APIClient;
