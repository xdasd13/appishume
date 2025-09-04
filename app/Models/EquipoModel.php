<?php

namespace App\Models;

use CodeIgniter\Model;

class EquipoModel extends Model
{
    protected $table = 'equipos';
    protected $primaryKey = 'idequipo';
    protected $allowedFields = ['idserviciocontratado', 'idusuario', 'descripcion', 'estadoservicio'];

    // Obtener todos los equipos
    public function getEquipos()
    {
        $builder = $this->db->table('equipos e');
        $builder->select('e.*, u.nombreusuario, p.nombres, p.apellidos, s.servicio, sc.direccion, sc.fechahoraservicio');
        $builder->join('usuarios u', 'e.idusuario = u.idusuario');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('servicioscontratados sc', 'e.idserviciocontratado = sc.idserviciocontratado');
        $builder->join('servicios s', 'sc.idservicio = s.idservicio');
        return $builder->get()->getResult();
    }

    // Obtener equipos por servicio
    public function getEquiposPorServicio($idserviciocontratado)
    {
        $builder = $this->db->table('equipos e');
        $builder->select('e.*, u.nombreusuario, p.nombres, p.apellidos, s.servicio');
        $builder->join('usuarios u', 'e.idusuario = u.idusuario');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('servicioscontratados sc', 'e.idserviciocontratado = sc.idserviciocontratado');
        $builder->join('servicios s', 'sc.idservicio = s.idservicio');
        $builder->where('e.idserviciocontratado', $idserviciocontratado);
        return $builder->get()->getResult();
    }

    // Obtener equipos por usuario
    public function getEquiposPorUsuario($idusuario)
    {
        $builder = $this->db->table('equipos e');
        $builder->select('e.*, s.servicio, sc.direccion, sc.fechahoraservicio');
        $builder->join('servicioscontratados sc', 'e.idserviciocontratado = sc.idserviciocontratado');
        $builder->join('servicios s', 'sc.idservicio = s.idservicio');
        $builder->where('e.idusuario', $idusuario);
        return $builder->get()->getResult();
    }

    // Obtener un equipo específico
    public function getEquipo($idequipo)
    {
        $builder = $this->db->table('equipos e');
        $builder->select('e.*, u.nombreusuario, p.nombres, p.apellidos, s.servicio, sc.idserviciocontratado');
        $builder->join('usuarios u', 'e.idusuario = u.idusuario');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('servicioscontratados sc', 'e.idserviciocontratado = sc.idserviciocontratado');
        $builder->join('servicios s', 'sc.idservicio = s.idservicio');
        $builder->where('e.idequipo', $idequipo);
        return $builder->get()->getRow();
    }

    // Insertar nuevo equipo
    public function insertEquipo($data)
    {
        $builder = $this->db->table('equipos');
        return $builder->insert($data);
    }

    // Actualizar equipo
    public function updateEquipo($idequipo, $data)
    {
        $builder = $this->db->table('equipos');
        $builder->where('idequipo', $idequipo);
        return $builder->update($data);
    }

    // **NUEVAS FUNCIONES PARA VALIDACIONES**

    /**
     * Verificar si un usuario ya está asignado a un servicio específico
     */
    public function usuarioYaAsignado($idusuario, $idserviciocontratado)
    {
        $builder = $this->db->table('equipos');
        $builder->where('idusuario', $idusuario);
        $builder->where('idserviciocontratado', $idserviciocontratado);
        $result = $builder->get()->getRow();
        return $result !== null;
    }

    /**
     * Verificar conflictos de horario para un usuario en una fecha/hora específica
     */
    public function verificarConflictoHorario($idusuario, $fechahoraservicio, $idserviciocontratadoExcluir = null)
    {
        $builder = $this->db->table('equipos e');
        $builder->select('e.*, sc.fechahoraservicio, s.servicio');
        $builder->join('servicioscontratados sc', 'e.idserviciocontratado = sc.idserviciocontratado');
        $builder->join('servicios s', 'sc.idservicio = s.idservicio');
        $builder->where('e.idusuario', $idusuario);
        
        // Excluir el servicio actual si se está editando
        if ($idserviciocontratadoExcluir) {
            $builder->where('e.idserviciocontratado !=', $idserviciocontratadoExcluir);
        }

        // Verificar conflictos en un rango de ±4 horas (ajusta según tus necesidades)
        $fechaInicio = date('Y-m-d H:i:s', strtotime($fechahoraservicio . ' -4 hours'));
        $fechaFin = date('Y-m-d H:i:s', strtotime($fechahoraservicio . ' +4 hours'));
        
        $builder->where('sc.fechahoraservicio BETWEEN "' . $fechaInicio . '" AND "' . $fechaFin . '"');
        
        return $builder->get()->getResult();
    }

    /**
     * Obtener usuarios disponibles para un servicio específico
     */
    public function getUsuariosDisponibles($idserviciocontratado)
    {
        // Primero obtenemos la fecha/hora del servicio
        $builderServicio = $this->db->table('servicioscontratados');
        $builderServicio->select('fechahoraservicio');
        $builderServicio->where('idserviciocontratado', $idserviciocontratado);
        $servicio = $builderServicio->get()->getRow();
        
        if (!$servicio) {
            return [];
        }

        // Obtener todos los usuarios activos
        $builderUsuarios = $this->db->table('usuarios u');
        $builderUsuarios->select('u.idusuario, p.nombres, p.apellidos, c.cargo');
        $builderUsuarios->join('personas p', 'u.idpersona = p.idpersona');
        $builderUsuarios->join('cargos c', 'u.idcargo = c.idcargo');
        $builderUsuarios->where('u.estado', 1);
        $usuarios = $builderUsuarios->get()->getResult();

        // Verificar disponibilidad de cada usuario
        foreach ($usuarios as &$usuario) {
            $usuario->yaAsignado = $this->usuarioYaAsignado($usuario->idusuario, $idserviciocontratado);
            $usuario->conflictos = $this->verificarConflictoHorario($usuario->idusuario, $servicio->fechahoraservicio, $idserviciocontratado);
            $usuario->disponible = !$usuario->yaAsignado && empty($usuario->conflictos);
        }

        return $usuarios;
    }

    /**
     * Obtener información detallada de conflictos para un usuario
     */
    public function getDetalleConflictos($idusuario, $fechahoraservicio)
    {
        $conflictos = $this->verificarConflictoHorario($idusuario, $fechahoraservicio);
        
        $detalles = [];
        foreach ($conflictos as $conflicto) {
            $detalles[] = [
                'servicio' => $conflicto->servicio,
                'fecha' => date('d/m/Y H:i', strtotime($conflicto->fechahoraservicio)),
                'descripcion' => $conflicto->descripcion
            ];
        }
        
        return $detalles;
    }

    /**
     * Validar antes de insertar/actualizar asignación
     */
    public function validarAsignacion($idusuario, $idserviciocontratado, $idequipoExcluir = null)
    {
        $errores = [];

        // Verificar si el usuario ya está asignado
        if ($this->usuarioYaAsignado($idusuario, $idserviciocontratado)) {
            // Si estamos editando, verificar que no sea la misma asignación
            if ($idequipoExcluir) {
                $equipoActual = $this->find($idequipoExcluir);
                if (!$equipoActual || $equipoActual['idusuario'] != $idusuario) {
                    $errores[] = 'El usuario ya está asignado a este servicio.';
                }
            } else {
                $errores[] = 'El usuario ya está asignado a este servicio.';
            }
        }

        // Obtener fecha del servicio para verificar conflictos
        $builderServicio = $this->db->table('servicioscontratados');
        $builderServicio->select('fechahoraservicio');
        $builderServicio->where('idserviciocontratado', $idserviciocontratado);
        $servicio = $builderServicio->get()->getRow();

        if ($servicio) {
            $conflictos = $this->verificarConflictoHorario($idusuario, $servicio->fechahoraservicio, $idserviciocontratado);
            if (!empty($conflictos)) {
                $errores[] = 'El usuario tiene conflictos de horario con otros servicios programados.';
            }
        }

        return $errores;
    }
}