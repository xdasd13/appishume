<?php

namespace App\Services;

use App\Models\EquipoModel;
use CodeIgniter\Database\ConnectionInterface;

/**
 * Servicio para lógica de negocio de equipos
 * Aplicando KISS: responsabilidades claras y métodos simples
 */
class EquipoService
{
    protected EquipoModel $equipoModel;
    protected ConnectionInterface $db;

    public function __construct()
    {
        $this->equipoModel = new EquipoModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Verifica si un técnico está disponible para un servicio
     * 
     * @param int $usuarioId ID del técnico
     * @param string $fechaEvento Fecha y hora del evento
     * @param int|null $servicioExcluir ID del servicio a excluir (para edición)
     * @return bool
     */
    public function estaDisponible(int $usuarioId, string $fechaEvento, ?int $servicioExcluir = null): bool
    {
        return !$this->tieneConflictoHorario($usuarioId, $fechaEvento, $servicioExcluir);
    }

    /**
     * Verifica conflictos de horario para un técnico
     * 
     * @param int $usuarioId
     * @param string $fechaEvento
     * @param int|null $servicioExcluir
     * @return bool
     */
    public function tieneConflictoHorario(int $usuarioId, string $fechaEvento, ?int $servicioExcluir = null): bool
    {
        $conflictos = $this->obtenerConflictos($usuarioId, $fechaEvento, $servicioExcluir);
        return !empty($conflictos);
    }

    /**
     * Obtiene los conflictos de horario detallados
     * 
     * @param int $usuarioId
     * @param string $fechaEvento
     * @param int|null $servicioExcluir
     * @return array
     */
    public function obtenerConflictos(int $usuarioId, string $fechaEvento, ?int $servicioExcluir = null): array
    {
        // Ventana de conflicto: ±4 horas (configurable)
        $ventanaHoras = 4;
        $fechaInicio = date('Y-m-d H:i:s', strtotime($fechaEvento . " -{$ventanaHoras} hours"));
        $fechaFin = date('Y-m-d H:i:s', strtotime($fechaEvento . " +{$ventanaHoras} hours"));

        $builder = $this->db->table('equipos e');
        $builder->select('e.*, sc.fechahoraservicio, s.servicio, sc.direccion');
        $builder->join('servicioscontratados sc', 'e.idserviciocontratado = sc.idserviciocontratado');
        $builder->join('servicios s', 'sc.idservicio = s.idservicio');
        $builder->where('e.idusuario', $usuarioId);
        $builder->where('sc.fechahoraservicio >=', $fechaInicio);
        $builder->where('sc.fechahoraservicio <=', $fechaFin);

        if ($servicioExcluir) {
            $builder->where('e.idserviciocontratado !=', $servicioExcluir);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Verifica si un técnico ya está asignado a un servicio específico
     * 
     * @param int $usuarioId
     * @param int $servicioId
     * @param int|null $equipoExcluir
     * @return bool
     */
    public function yaEstaAsignado(int $usuarioId, int $servicioId, ?int $equipoExcluir = null): bool
    {
        $builder = $this->db->table('equipos');
        $builder->where('idusuario', $usuarioId);
        $builder->where('idserviciocontratado', $servicioId);

        if ($equipoExcluir) {
            $builder->where('idequipo !=', $equipoExcluir);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Valida una asignación completa
     * 
     * @param int $usuarioId
     * @param int $servicioId
     * @param string $fechaEvento
     * @param int|null $equipoExcluir
     * @return array ['valido' => bool, 'errores' => array]
     */
    public function validarAsignacion(int $usuarioId, int $servicioId, string $fechaEvento, ?int $equipoExcluir = null): array
    {
        $errores = [];

        // Verificar asignación duplicada
        if ($this->yaEstaAsignado($usuarioId, $servicioId, $equipoExcluir)) {
            $errores[] = 'El técnico ya está asignado a este servicio';
        }

        // Verificar conflictos de horario
        if ($this->tieneConflictoHorario($usuarioId, $fechaEvento, $servicioId)) {
            $errores[] = 'El técnico tiene conflictos de horario con otros servicios';
        }

        return [
            'valido' => empty($errores),
            'errores' => $errores
        ];
    }

    /**
     * Obtiene técnicos disponibles para un servicio
     * 
     * @param int $servicioId
     * @param string $fechaEvento
     * @return array
     */
    public function obtenerTecnicosDisponibles(int $servicioId, string $fechaEvento): array
    {
        // Obtener todos los técnicos activos
        $builder = $this->db->table('usuarios u');
        $builder->select('u.idusuario, p.nombres, p.apellidos, c.cargo');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('cargos c', 'u.idcargo = c.idcargo');
        $builder->where('u.estado', 1);
        $tecnicos = $builder->get()->getResultArray();

        // Evaluar disponibilidad de cada técnico
        foreach ($tecnicos as &$tecnico) {
            $tecnico['yaAsignado'] = $this->yaEstaAsignado($tecnico['idusuario'], $servicioId);
            $tecnico['conflictos'] = $this->obtenerConflictos($tecnico['idusuario'], $fechaEvento, $servicioId);
            $tecnico['disponible'] = !$tecnico['yaAsignado'] && empty($tecnico['conflictos']);
            $tecnico['nombreCompleto'] = "{$tecnico['nombres']} {$tecnico['apellidos']}";
        }

        return $tecnicos;
    }

    /**
     * Obtiene estadísticas de equipos por estado
     * 
     * @return array
     */
    public function obtenerEstadisticas(): array
    {
        $builder = $this->db->table('equipos');
        $builder->select('estadoservicio, COUNT(*) as total');
        $builder->groupBy('estadoservicio');
        $resultados = $builder->get()->getResultArray();

        $estadisticas = [
            'Pendiente' => 0,
            'En Proceso' => 0,
            'Completado' => 0,
            'Programado' => 0
        ];

        foreach ($resultados as $resultado) {
            $estadisticas[$resultado['estadoservicio']] = (int)$resultado['total'];
        }

        // Agrupar Pendiente y Programado
        $estadisticas['Pendiente'] += $estadisticas['Programado'];
        unset($estadisticas['Programado']);

        return $estadisticas;
    }

    /**
     * Actualiza el estado de un equipo con validación
     * 
     * @param int $equipoId
     * @param string $nuevoEstado
     * @return array ['success' => bool, 'message' => string]
     */
    public function actualizarEstado(int $equipoId, string $nuevoEstado): array
    {
        // Obtener equipo actual como array explícitamente
        $equipo = $this->equipoModel->asArray()->find($equipoId);
        if (!$equipo) {
            return ['success' => false, 'message' => 'Equipo no encontrado'];
        }

        // Validar transición usando el helper
        helper('estado');
        $estadoActual = $equipo['estadoservicio'] ?? '';
        $validacion = validarTransicionEstado($estadoActual, $nuevoEstado);
        
        if (!$validacion['valido']) {
            return ['success' => false, 'message' => $validacion['mensaje']];
        }

        // Actualizar estado
        $actualizado = $this->equipoModel->update($equipoId, ['estadoservicio' => $nuevoEstado]);
        
        return [
            'success' => $actualizado,
            'message' => $actualizado ? 'Estado actualizado correctamente' : 'Error al actualizar estado'
        ];
    }
}
