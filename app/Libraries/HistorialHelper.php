<?php

namespace App\Libraries;

use App\Models\HistorialActividadesModel;

class HistorialHelper
{
    protected $historialModel;
    protected $session;

    public function __construct()
    {
        $this->historialModel = new HistorialActividadesModel();
        $this->session = session();
    }

    /**
     * Registrar cambio de estado en servicios/equipos
     * 
     * @param string $tabla Tabla afectada
     * @param int $registroId ID del registro
     * @param string $estadoAnterior Estado anterior
     * @param string $estadoNuevo Estado nuevo
     * @param string $contexto Contexto adicional (nombre del proyecto, etc.)
     * @return bool
     */
    public function registrarCambioEstado(string $tabla, int $registroId, string $estadoAnterior, string $estadoNuevo, string $contexto = '')
    {
        $usuarioId = $this->session->get('user_id');
        if (!$usuarioId) {
            return false;
        }

        $descripcion = $this->generarDescripcionCambioEstado($estadoAnterior, $estadoNuevo, $contexto);

        return $this->historialModel->registrarActividad([
            'tabla_afectada' => $tabla,
            'registro_id' => $registroId,
            'accion' => 'cambio_estado',
            'campo_modificado' => 'estado',
            'valor_anterior' => json_encode($estadoAnterior),
            'valor_nuevo' => json_encode($estadoNuevo),
            'descripcion' => $descripcion,
            'usuario_id' => $usuarioId,
            'metadata' => json_encode([
                'contexto' => $contexto,
                'timestamp' => date('Y-m-d H:i:s')
            ])
        ]);
    }

    /**
     * Registrar asignación de equipo/técnico
     * 
     * @param int $servicioId ID del servicio
     * @param int $equipoId ID del equipo
     * @param string $tecnicoNombre Nombre del técnico asignado
     * @param string $contexto Contexto del proyecto
     * @return bool
     */
    public function registrarAsignacion(int $servicioId, int $equipoId, string $tecnicoNombre, string $contexto = '')
    {
        $usuarioId = $this->session->get('user_id');
        if (!$usuarioId) {
            return false;
        }

        $descripcion = "Equipo asignado a {$tecnicoNombre}" . ($contexto ? " para '{$contexto}'" : '');

        return $this->historialModel->registrarActividad([
            'tabla_afectada' => 'equipos',
            'registro_id' => $equipoId,
            'accion' => 'asignar',
            'descripcion' => $descripcion,
            'usuario_id' => $usuarioId,
            'metadata' => json_encode([
                'servicio_id' => $servicioId,
                'tecnico' => $tecnicoNombre,
                'contexto' => $contexto
            ])
        ]);
    }

    /**
     * Registrar completación de proyecto
     * 
     * @param int $servicioId ID del servicio
     * @param string $proyectoNombre Nombre del proyecto
     * @return bool
     */
    public function registrarCompletacion(int $servicioId, string $proyectoNombre)
    {
        $usuarioId = $this->session->get('user_id');
        if (!$usuarioId) {
            return false;
        }

        $nombreUsuario = $this->session->get('user_name') ?? 'Usuario';
        $descripcion = "El proyecto '{$proyectoNombre}' fue marcado como completado por {$nombreUsuario}";

        return $this->historialModel->registrarActividad([
            'tabla_afectada' => 'servicioscontratados',
            'registro_id' => $servicioId,
            'accion' => 'completar',
            'campo_modificado' => 'estado',
            'valor_nuevo' => json_encode('Completado'),
            'descripcion' => $descripcion,
            'usuario_id' => $usuarioId,
            'metadata' => json_encode([
                'proyecto' => $proyectoNombre,
                'fecha_completacion' => date('Y-m-d H:i:s')
            ])
        ]);
    }

    /**
     * Registrar subida de archivos
     * 
     * @param int $servicioId ID del servicio
     * @param string $tipoArchivo Tipo de archivo (audio, video, foto, etc.)
     * @param string $nombreArchivo Nombre del archivo
     * @param string $contexto Contexto del proyecto
     * @return bool
     */
    public function registrarSubidaArchivo(int $servicioId, string $tipoArchivo, string $nombreArchivo, string $contexto = '')
    {
        $usuarioId = $this->session->get('user_id');
        if (!$usuarioId) {
            return false;
        }

        $nombreUsuario = $this->session->get('user_name') ?? 'Usuario';
        $descripcion = "El técnico {$nombreUsuario} subió archivos de {$tipoArchivo}" . 
                      ($contexto ? " para '{$contexto}'" : '');

        return $this->historialModel->registrarActividad([
            'tabla_afectada' => 'servicioscontratados',
            'registro_id' => $servicioId,
            'accion' => 'subir_archivo',
            'descripcion' => $descripcion,
            'usuario_id' => $usuarioId,
            'metadata' => json_encode([
                'tipo_archivo' => $tipoArchivo,
                'nombre_archivo' => $nombreArchivo,
                'contexto' => $contexto,
                'fecha_subida' => date('Y-m-d H:i:s')
            ])
        ]);
    }

    /**
     * Registrar comentario o nota
     * 
     * @param string $tabla Tabla afectada
     * @param int $registroId ID del registro
     * @param string $comentario Comentario realizado
     * @param string $contexto Contexto adicional
     * @return bool
     */
    public function registrarComentario(string $tabla, int $registroId, string $comentario, string $contexto = '')
    {
        $usuarioId = $this->session->get('user_id');
        if (!$usuarioId) {
            return false;
        }

        $nombreUsuario = $this->session->get('user_name') ?? 'Usuario';
        $descripcion = "{$nombreUsuario} agregó un comentario" . ($contexto ? " en '{$contexto}'" : '');

        return $this->historialModel->registrarActividad([
            'tabla_afectada' => $tabla,
            'registro_id' => $registroId,
            'accion' => 'comentario',
            'descripcion' => $descripcion,
            'usuario_id' => $usuarioId,
            'metadata' => json_encode([
                'comentario' => $comentario,
                'contexto' => $contexto
            ])
        ]);
    }

    /**
     * Registrar actividad genérica
     * 
     * @param array $data Datos de la actividad
     * @return bool
     */
    public function registrarActividad(array $data)
    {
        $usuarioId = $this->session->get('user_id');
        if (!$usuarioId) {
            return false;
        }

        $data['usuario_id'] = $usuarioId;
        return $this->historialModel->registrarActividad($data);
    }

    /**
     * Obtener historial formateado para mostrar en UI
     * 
     * @param string $tabla Tabla a consultar
     * @param int $registroId ID del registro
     * @param int $limit Límite de registros
     * @return array
     */
    public function getHistorialFormateado(string $tabla, int $registroId, int $limit = 20)
    {
        $historial = $this->historialModel->getHistorialRegistro($tabla, $registroId, $limit);
        
        return array_map(function($actividad) {
            return [
                'id' => $actividad['id'],
                'descripcion' => $actividad['descripcion'],
                'usuario' => $this->formatearNombreUsuario($actividad),
                'fecha' => $this->formatearFecha($actividad['created_at']),
                'fecha_relativa' => $this->formatearFechaRelativa($actividad['created_at']),
                'accion' => $actividad['accion'],
                'icono' => $this->getIconoAccion($actividad['accion']),
                'color' => $this->getColorAccion($actividad['accion']),
                'metadata' => json_decode($actividad['metadata'] ?? '{}', true)
            ];
        }, $historial);
    }

    /**
     * Generar descripción para cambio de estado
     */
    private function generarDescripcionCambioEstado(string $estadoAnterior, string $estadoNuevo, string $contexto): string
    {
        $nombreUsuario = $this->session->get('user_name') ?? 'Usuario';
        
        $descripcion = "El estado cambió de '{$estadoAnterior}' a '{$estadoNuevo}'";
        
        if ($contexto) {
            $descripcion .= " en '{$contexto}'";
        }
        
        $descripcion .= " por {$nombreUsuario}";
        
        return $descripcion;
    }

    /**
     * Formatear nombre de usuario
     */
    private function formatearNombreUsuario(array $actividad): string
    {
        if (!empty($actividad['nombres']) && !empty($actividad['apellidos'])) {
            $nombres = explode(' ', $actividad['nombres']);
            $apellidos = explode(' ', $actividad['apellidos']);
            return $nombres[0] . ' ' . $apellidos[0] . '.';
        }
        
        return $actividad['nombreusuario'] ?? 'Usuario';
    }

    /**
     * Formatear fecha para mostrar
     */
    private function formatearFecha(string $fecha): string
    {
        return date('d/m/Y H:i', strtotime($fecha));
    }

    /**
     * Formatear fecha relativa (hace X tiempo)
     */
    private function formatearFechaRelativa(string $fecha): string
    {
        $tiempo = time() - strtotime($fecha);
        
        if ($tiempo < 60) {
            return 'hace unos segundos';
        } elseif ($tiempo < 3600) {
            $minutos = floor($tiempo / 60);
            return "hace {$minutos} minuto" . ($minutos > 1 ? 's' : '');
        } elseif ($tiempo < 86400) {
            $horas = floor($tiempo / 3600);
            return "hace {$horas} hora" . ($horas > 1 ? 's' : '');
        } else {
            $dias = floor($tiempo / 86400);
            return "hace {$dias} día" . ($dias > 1 ? 's' : '');
        }
    }

    /**
     * Obtener icono para tipo de acción
     */
    private function getIconoAccion(string $accion): string
    {
        $iconos = [
            'crear' => 'fas fa-plus-circle',
            'actualizar' => 'fas fa-edit',
            'eliminar' => 'fas fa-trash',
            'cambio_estado' => 'fas fa-exchange-alt',
            'asignar' => 'fas fa-user-plus',
            'completar' => 'fas fa-check-circle',
            'subir_archivo' => 'fas fa-upload',
            'comentario' => 'fas fa-comment'
        ];
        
        return $iconos[$accion] ?? 'fas fa-info-circle';
    }

    /**
     * Obtener color para tipo de acción
     */
    private function getColorAccion(string $accion): string
    {
        $colores = [
            'crear' => 'success',
            'actualizar' => 'info',
            'eliminar' => 'danger',
            'cambio_estado' => 'warning',
            'asignar' => 'primary',
            'completar' => 'success',
            'subir_archivo' => 'info',
            'comentario' => 'secondary'
        ];
        
        return $colores[$accion] ?? 'secondary';
    }
}
