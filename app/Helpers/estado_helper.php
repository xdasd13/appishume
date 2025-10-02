<?php

/**
 * Helper para validación de estados de equipos
 * Aplicando principio KISS: funciones simples y claras
 */

if (!function_exists('validarTransicionEstado')) {
    /**
     * Valida si una transición de estado es permitida
     * 
     * @param string $estadoActual Estado actual del equipo
     * @param string $nuevoEstado Nuevo estado deseado
     * @return array ['valido' => bool, 'mensaje' => string]
     */
    function validarTransicionEstado(string $estadoActual, string $nuevoEstado): array
    {
        // Normalizar estados
        $estadoActual = trim($estadoActual);
        $nuevoEstado = trim($nuevoEstado);

        // Si no hay cambio, es válido
        if ($estadoActual === $nuevoEstado) {
            return ['valido' => true, 'mensaje' => 'Sin cambios'];
        }

        // Regla 1: Completado no puede regresar
        if ($estadoActual === 'Completado') {
            return [
                'valido' => false,
                'mensaje' => 'Este servicio ya está completo'
            ];
        }

        // Regla 2: No saltar directamente a Completado
        if (in_array($estadoActual, ['Pendiente', 'Programado']) && $nuevoEstado === 'Completado') {
            return [
                'valido' => false,
                'mensaje' => 'Este servicio aún no tiene proceso'
            ];
        }

        // Regla 3: En Proceso no puede regresar a Pendiente
        if ($estadoActual === 'En Proceso' && $nuevoEstado === 'Pendiente') {
            return [
                'valido' => false,
                'mensaje' => 'Este servicio está en proceso'
            ];
        }

        return ['valido' => true, 'mensaje' => 'Transición válida'];
    }
}

if (!function_exists('getEstadosPermitidos')) {
    /**
     * Obtiene los estados permitidos desde un estado actual
     * 
     * @param string $estadoActual
     * @return array
     */
    function getEstadosPermitidos(string $estadoActual): array
    {
        $estados = ['Pendiente', 'En Proceso', 'Completado', 'Programado'];
        $permitidos = [];

        foreach ($estados as $estado) {
            $validacion = validarTransicionEstado($estadoActual, $estado);
            if ($validacion['valido']) {
                $permitidos[] = $estado;
            }
        }

        return $permitidos;
    }
}

if (!function_exists('getColorEstado')) {
    /**
     * Obtiene el color CSS para un estado
     * 
     * @param string $estado
     * @return string
     */
    function getColorEstado(string $estado): string
    {
        return match ($estado) {
            'Pendiente', 'Programado' => 'warning',  // 🟡 Amarillo
            'En Proceso' => 'info',                  // 🔵 Azul
            'Completado' => 'success',               // 🟢 Verde
            default => 'secondary'
        };
    }
}

if (!function_exists('getIconoEstado')) {
    /**
     * Obtiene el ícono FontAwesome para un estado
     * 
     * @param string $estado
     * @return string
     */
    function getIconoEstado(string $estado): string
    {
        return match ($estado) {
            'Pendiente', 'Programado' => 'fas fa-clock',
            'En Proceso' => 'fas fa-spinner',
            'Completado' => 'fas fa-check-circle',
            default => 'fas fa-question-circle'
        };
    }
}

if (!function_exists('getSweetAlertIcon')) {
    /**
     * Obtiene el ícono compatible con SweetAlert2 para un estado
     * 
     * @param string $estado
     * @return string
     */
    function getSweetAlertIcon(string $estado): string
    {
        return match ($estado) {
            'Pendiente', 'Programado' => 'warning',
            'En Proceso' => 'info',
            'Completado' => 'success',
            default => 'question'
        };
    }
}
