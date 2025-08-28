<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'idusuario';

    // Obtener todos los usuarios técnicos/empleados
    public function getUsuarios()
    {
        $builder = $this->db->table('usuarios u');
        $builder->select('u.idusuario, u.nombreusuario, p.nombres, p.apellidos, c.cargo');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('cargos c', 'u.idcargo = c.idcargo');
        $builder->where('u.estado', 1);
        return $builder->get()->getResult();
    }

    // Obtener un usuario específico
    public function getUsuario($idusuario)
    {
        $builder = $this->db->table('usuarios u');
        $builder->select('u.idusuario, u.nombreusuario, p.nombres, p.apellidos, c.cargo');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('cargos c', 'u.idcargo = c.idcargo');
        $builder->where('u.idusuario', $idusuario);
        return $builder->get()->getRow();
    }
}