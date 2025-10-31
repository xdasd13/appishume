<?php

/**
 * Helper para validación de estados de equipos
 * Aplicando principio KISS: funciones simples y claras
 */

if (!function_exists('validarTransicionEstado')) {
    /**
     * Valida si una transición de estado es permitida
     * Flujo: Programado → Pendiente → En Proceso → Completado
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

        // Regla 1: Completado no puede regresar a ningún estado
        if ($estadoActual === 'Completado') {
            return [
                'valido' => false,
                'mensaje' => 'Este servicio ya está completo y no puede modificarse'
            ];
        }

        // Regla 2: No se puede saltar de Programado directamente a En Proceso o Completado
        if ($estadoActual === 'Programado' && in_array($nuevoEstado, ['En Proceso', 'Completado'])) {
            return [
                'valido' => false,
                'mensaje' => 'Debe pasar primero por Pendiente antes de iniciar el proceso'
            ];
        }

        // Regla 3: No se puede saltar de Pendiente directamente a Completado
        if ($estadoActual === 'Pendiente' && $nuevoEstado === 'Completado') {
            return [
                'valido' => false,
                'mensaje' => 'Debe iniciar el proceso antes de completar el servicio'
            ];
        }

        // Regla 4: En Proceso no puede regresar a Pendiente o Programado
        if ($estadoActual === 'En Proceso' && in_array($nuevoEstado, ['Pendiente', 'Programado'])) {
            return [
                'valido' => false,
                'mensaje' => 'Este servicio ya está en proceso y no puede retroceder'
            ];
        }

        // Regla 5: Pendiente no puede regresar a Programado
        if ($estadoActual === 'Pendiente' && $nuevoEstado === 'Programado') {
            return [
                'valido' => false,
                'mensaje' => 'Este servicio ya salió de programación'
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
            'Programado' => 'secondary',  // 🔘 Gris - Aún no iniciado
            'Pendiente' => 'warning',     // 🟡 Amarillo - Listo para iniciar
            'En Proceso' => 'info',       // 🔵 Azul - En ejecución
            'Completado' => 'success',    // 🟢 Verde - Finalizado
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
            'Programado' => 'fas fa-calendar-alt',
            'Pendiente' => 'fas fa-clock',
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
            'Programado' => 'info',
            'Pendiente' => 'warning',
            'En Proceso' => 'info',
            'Completado' => 'success',
            default => 'question'
        };
    }
}
