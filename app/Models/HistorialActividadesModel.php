<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialActividadesModel extends Model
{
    protected $table = 'historial_actividades';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'tabla_afectada',
        'registro_id', 
        'accion',
        'campo_modificado',
        'valor_anterior',
        'valor_nuevo',
        'descripcion',
        'usuario_id',
        'ip_address',
        'user_agent',
        'metadata',
        'created_at'
    ];

    protected $useTimestamps = false; // Manejamos manualmente
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'tabla_afectada' => 'required|max_length[50]',
        'registro_id' => 'required|integer',
        'accion' => 'required|in_list[crear,actualizar,eliminar,cambio_estado,asignar,completar,subir_archivo,comentario]',
        'descripcion' => 'required|max_length[500]',
        'usuario_id' => 'required|integer'
    ];

    protected $validationMessages = [
        'tabla_afectada' => [
            'required' => 'La tabla afectada es requerida',
            'max_length' => 'El nombre de la tabla no puede exceder 50 caracteres'
        ],
        'registro_id' => [
            'required' => 'El ID del registro es requerido',
            'integer' => 'El ID del registro debe ser un número entero'
        ],
        'accion' => [
            'required' => 'La acción es requerida',
            'in_list' => 'La acción debe ser válida'
        ],
        'descripcion' => [
            'required' => 'La descripción es requerida',
            'max_length' => 'La descripción no puede exceder 500 caracteres'
        ],
        'usuario_id' => [
            'required' => 'El ID del usuario es requerido',
            'integer' => 'El ID del usuario debe ser un número entero'
        ]
    ];

    /**
     * Registrar una actividad en el historial
     * 
     * @param array $data Datos de la actividad
     * @return bool|int ID del registro creado o false si falla
     */
    public function registrarActividad(array $data)
    {
        // Agregar información automática
        $request = service('request');
        
        $actividadData = array_merge($data, [
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Validar y guardar
        if ($this->validate($actividadData)) {
            return $this->insert($actividadData);
        }
        
        log_message('error', 'Error validando actividad: ' . json_encode($this->errors()));
        return false;
    }

    /**
     * Obtener historial de un registro específico
     * 
     * @param string $tabla Nombre de la tabla
     * @param int $registroId ID del registro
     * @param int $limit Límite de registros
     * @return array
     */
    public function getHistorialRegistro(string $tabla, int $registroId, int $limit = 50)
    {
        return $this->select('historial_actividades.*, usuarios.nombreusuario, personas.nombres, personas.apellidos')
                    ->join('usuarios', 'usuarios.idusuario = historial_actividades.usuario_id', 'left')
                    ->join('personas', 'personas.idpersona = usuarios.idpersona', 'left')
                    ->where('tabla_afectada', $tabla)
                    ->where('registro_id', $registroId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Obtener actividades recientes del sistema
     * 
     * @param int $limit Límite de registros
     * @param array $filtros Filtros opcionales
     * @return array
     */
    public function getActividadesRecientes(int $limit = 100, array $filtros = [])
    {
        $builder = $this->select('historial_actividades.*, usuarios.nombreusuario, personas.nombres, personas.apellidos')
                        ->join('usuarios', 'usuarios.idusuario = historial_actividades.usuario_id', 'left')
                        ->join('personas', 'personas.idpersona = usuarios.idpersona', 'left');

        // Aplicar filtros
        if (!empty($filtros['tabla'])) {
            $builder->where('tabla_afectada', $filtros['tabla']);
        }
        
        if (!empty($filtros['accion'])) {
            $builder->where('accion', $filtros['accion']);
        }
        
        if (!empty($filtros['usuario_id'])) {
            $builder->where('historial_actividades.usuario_id', $filtros['usuario_id']);
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $builder->where('created_at >=', $filtros['fecha_desde']);
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $builder->where('created_at <=', $filtros['fecha_hasta']);
        }

        return $builder->orderBy('created_at', 'DESC')
                      ->limit($limit)
                      ->findAll();
    }

    /**
     * Obtener estadísticas de actividades por período
     * 
     * @param string $fechaInicio Fecha de inicio (Y-m-d)
     * @param string $fechaFin Fecha de fin (Y-m-d)
     * @return array
     */
    public function getEstadisticasPeriodo(string $fechaInicio, string $fechaFin)
    {
        // Actividades por acción
        $actividadesPorAccion = $this->select('accion, COUNT(*) as total')
                                    ->where('DATE(created_at) >=', $fechaInicio)
                                    ->where('DATE(created_at) <=', $fechaFin)
                                    ->groupBy('accion')
                                    ->findAll();

        // Actividades por usuario
        $actividadesPorUsuario = $this->select('usuarios.nombreusuario, personas.nombres, personas.apellidos, COUNT(*) as total')
                                     ->join('usuarios', 'usuarios.idusuario = historial_actividades.usuario_id', 'left')
                                     ->join('personas', 'personas.idpersona = usuarios.idpersona', 'left')
                                     ->where('DATE(created_at) >=', $fechaInicio)
                                     ->where('DATE(created_at) <=', $fechaFin)
                                     ->groupBy('historial_actividades.usuario_id')
                                     ->orderBy('total', 'DESC')
                                     ->findAll();

        // Actividades por día
        $actividadesPorDia = $this->select('DATE(created_at) as fecha, COUNT(*) as total')
                                 ->where('DATE(created_at) >=', $fechaInicio)
                                 ->where('DATE(created_at) <=', $fechaFin)
                                 ->groupBy('DATE(created_at)')
                                 ->orderBy('fecha', 'ASC')
                                 ->findAll();

        return [
            'por_accion' => $actividadesPorAccion,
            'por_usuario' => $actividadesPorUsuario,
            'por_dia' => $actividadesPorDia,
            'total_actividades' => array_sum(array_column($actividadesPorAccion, 'total'))
        ];
    }

    /**
     * Obtener reporte de productividad de equipos
     * 
     * @param string $fechaInicio Fecha de inicio
     * @param string $fechaFin Fecha de fin
     * @return array
     */
    public function getReporteProductividadEquipos(string $fechaInicio, string $fechaFin)
    {
        // Cambios de estado a "Completado" por usuario
        $completados = $this->select('usuarios.nombreusuario, personas.nombres, personas.apellidos, COUNT(*) as proyectos_completados')
                           ->join('usuarios', 'usuarios.idusuario = historial_actividades.usuario_id', 'left')
                           ->join('personas', 'personas.idpersona = usuarios.idpersona', 'left')
                           ->where('tabla_afectada', 'servicioscontratados')
                           ->where('accion', 'cambio_estado')
                           ->where('valor_nuevo', '"Completado"')
                           ->where('DATE(created_at) >=', $fechaInicio)
                           ->where('DATE(created_at) <=', $fechaFin)
                           ->groupBy('historial_actividades.usuario_id')
                           ->orderBy('proyectos_completados', 'DESC')
                           ->findAll();

        // Estados actuales de proyectos (esto requiere consulta adicional)
        $db = \Config\Database::connect();
        $estadosActuales = $db->query("
            SELECT estado, COUNT(*) as total 
            FROM servicioscontratados 
            WHERE DATE(fechahoraservicio) BETWEEN ? AND ?
            GROUP BY estado
        ", [$fechaInicio, $fechaFin])->getResultArray();

        return [
            'completados_por_usuario' => $completados,
            'estados_actuales' => $estadosActuales,
            'trabajador_mas_activo' => !empty($completados) ? $completados[0] : null
        ];
    }

    /**
     * Limpiar historial antiguo (mantener solo últimos X días)
     * 
     * @param int $diasMantener Días a mantener
     * @return int Número de registros eliminados
     */
    public function limpiarHistorialAntiguo(int $diasMantener = 365)
    {
        $fechaLimite = date('Y-m-d H:i:s', strtotime("-{$diasMantener} days"));
        
        return $this->where('created_at <', $fechaLimite)->delete();
    }
}
