<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'idusuario';
    protected $allowedFields = [
        'idpersona', 'idcargo', 'nombreusuario', 'claveacceso', 
        'tipo_usuario', 'email', 'password_hash', 'estado'
    ];

    // Autenticar usuario por email o nombre de usuario
    public function authenticate($login, $password)
    {
        $builder = $this->db->table('usuarios u');
        $builder->select('u.*, p.nombres, p.apellidos, c.cargo');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('cargos c', 'u.idcargo = c.idcargo');
        $builder->where('u.estado', 1);
        $builder->groupStart();
        $builder->where('u.nombreusuario', $login);
        $builder->orWhere('u.email', $login);
        $builder->groupEnd();
        
        $usuario = $builder->get()->getRow();
        
        if ($usuario) {
            // Verificar contraseña hasheada primero, luego texto plano (compatibilidad)
            if (!empty($usuario->password_hash) && password_verify($password, $usuario->password_hash)) {
                return $usuario;
            } elseif ($usuario->claveacceso === $password) {
                // Actualizar a hash si aún usa texto plano
                $this->update($usuario->idusuario, [
                    'password_hash' => password_hash($password, PASSWORD_BCRYPT)
                ]);
                return $usuario;
            }
        }
        
        return false;
    }

    // Obtener todos los usuarios técnicos/empleados
    public function getUsuarios()
    {
        $builder = $this->db->table('usuarios u');
        $builder->select('u.idusuario, u.nombreusuario, u.email, u.tipo_usuario, p.nombres, p.apellidos, c.cargo');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('cargos c', 'u.idcargo = c.idcargo');
        $builder->where('u.estado', 1);
        return $builder->get()->getResult();
    }

    // Obtener solo trabajadores
    public function getTrabajadores()
    {
        $builder = $this->db->table('usuarios u');
        $builder->select('u.idusuario, u.nombreusuario, u.email, p.nombres, p.apellidos, c.cargo');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('cargos c', 'u.idcargo = c.idcargo');
        $builder->where('u.estado', 1);
        $builder->where('u.tipo_usuario', 'trabajador');
        return $builder->get()->getResult();
    }

    // Obtener un usuario específico
    public function getUsuario($idusuario)
    {
        $builder = $this->db->table('usuarios u');
        $builder->select('u.idusuario, u.nombreusuario, u.email, u.tipo_usuario, p.nombres, p.apellidos, c.cargo');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('cargos c', 'u.idcargo = c.idcargo');
        $builder->where('u.idusuario', $idusuario);
        return $builder->get()->getRow();
    }

    // Crear nuevo trabajador
    public function crearTrabajador($data)
    {
        $usuarioData = [
            'idpersona' => $data['idpersona'],
            'idcargo' => $data['idcargo'],
            'nombreusuario' => $data['nombreusuario'],
            'claveacceso' => $data['password'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'tipo_usuario' => 'trabajador',
            'estado' => 1
        ];

        return $this->insert($usuarioData);
    }

    // Obtener equipos asignados a un usuario
    public function getEquiposAsignados($usuarioId)
    {
        $builder = $this->db->table('equipos e');
        $builder->select('e.idequipo, e.equipo, e.estado, s.servicio, 
                         CONCAT(p.nombres, " ", p.apellidos) as cliente, 
                         s.fechaservicio');
        $builder->join('servicios s', 'e.idservicio = s.idservicio');
        $builder->join('personas p', 's.idpersona = p.idpersona');
        $builder->where('e.idusuario', $usuarioId);
        $builder->orderBy('s.fechaservicio', 'DESC');
        
        return $builder->get()->getResult();
    }

    // Actualizar estado de equipo
    public function actualizarEstadoEquipo($equipoId, $nuevoEstado)
    {
        $builder = $this->db->table('equipos');
        $builder->where('idequipo', $equipoId);
        return $builder->update(['estado' => $nuevoEstado]);
    }
}