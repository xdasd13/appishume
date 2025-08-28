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
}