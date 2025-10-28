<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'idusuario';
    protected $returnType = 'object';
    protected $allowedFields = [
        'idpersona', 'idcargo', 'nombreusuario', 'claveacceso', 
        'tipo_usuario', 'email', 'password_hash', 'estado'
    ];

    /**
     * Autenticar usuario por email o nombre de usuario
     * 
     * @param string $login Email o nombre de usuario
     * @param string $password Contraseña
     * @return array Retorna ['success' => bool, 'message' => string, 'data' => object|null]
     */
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
        
        // Usuario no encontrado
        if (!$usuario) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado',
                'error_type' => 'user_not_found',
                'data' => null
            ];
        }
        
        // Usuario encontrado, verificar contraseña
        $passwordValida = false;
        
        // Verificar contraseña hasheada
        if (!empty($usuario->password_hash) && password_verify($password, $usuario->password_hash)) {
            $passwordValida = true;
        } 
        // Verificar contraseña en texto plano (legacy) y migrar
        elseif (!empty($usuario->claveacceso) && $usuario->claveacceso === $password) {
            $passwordValida = true;
            // Migrar contraseña de texto plano a hash
            $this->update($usuario->idusuario, [
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'claveacceso' => null
            ]);
        }
        
        // Contraseña incorrecta
        if (!$passwordValida) {
            return [
                'success' => false,
                'message' => 'Contraseña incorrecta',
                'error_type' => 'wrong_password',
                'data' => null
            ];
        }
        
        // Autenticación exitosa
        return [
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'error_type' => null,
            'data' => $usuario
        ];
    }

    // Obtener todos los usuarios técnicos/empleados
    public function getUsuarios()
    {
        $builder = $this->db->table('usuarios u');
        $builder->select('u.idusuario, u.idpersona, u.idcargo, u.nombreusuario, u.email, u.tipo_usuario, u.estado, p.nombres, p.apellidos, p.numerodoc, p.telprincipal, p.direccion, c.cargo');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('cargos c', 'u.idcargo = c.idcargo');
        $builder->where('u.estado', 1);
        return $builder->get()->getResult();
    }

    // Obtener todos los usuarios con información completa para gestión de credenciales
    public function getUsuariosCompletos($estado = 1)
    {
        $builder = $this->db->table('usuarios u');
        $builder->select('u.idusuario, u.idpersona, u.idcargo, u.nombreusuario, u.email, u.tipo_usuario, u.estado, 
                         p.nombres, p.apellidos, p.numerodoc, p.tipodoc, 
                         COALESCE(p.telprincipal, "") as telprincipal, 
                         COALESCE(p.telalternativo, "") as telalternativo, 
                         COALESCE(p.direccion, "") as direccion, 
                         COALESCE(p.referencia, "") as referencia, 
                         COALESCE(c.cargo, "Sin cargo") as cargo');
        $builder->join('personas p', 'u.idpersona = p.idpersona', 'left');
        $builder->join('cargos c', 'u.idcargo = c.idcargo', 'left');
        
        // Filtrar por estado: 1 = activos, 0 = desactivados, null = todos
        if ($estado !== null) {
            $builder->where('u.estado', $estado);
        }
        
        $builder->orderBy('p.nombres', 'ASC');
        return $builder->get()->getResult();
    }

    // Obtener usuario completo con datos de persona
    public function getUsuarioCompleto($idusuario)
    {
        $builder = $this->db->table('usuarios u');
        $builder->select('u.*, p.*, c.cargo');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('cargos c', 'u.idcargo = c.idcargo');
        $builder->where('u.idusuario', $idusuario);
        return $builder->get()->getRow();
    }

    // Actualizar datos de usuario
    public function actualizarUsuario($idusuario, $datosUsuario, $datosPersona)
    {
        $this->db->transStart();
        
        // Actualizar datos de persona
        if (!empty($datosPersona)) {
            $this->db->table('personas')->where('idpersona', $datosPersona['idpersona'])->update($datosPersona);
        }
        
        // Actualizar datos de usuario
        if (!empty($datosUsuario)) {
            $this->db->table('usuarios')->where('idusuario', $idusuario)->update($datosUsuario);
        }
        
        $this->db->transComplete();
        return $this->db->transStatus();
    }

    // Eliminar usuario (cambiar estado)
    public function eliminarUsuario($idusuario)
    {
        return $this->update($idusuario, ['estado' => 0]);
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
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'tipo_usuario' => 'trabajador',
            'estado' => 1
        ];

        return $this->insert($usuarioData);
    }

    // Obtener equipos asignados a un usuario
    public function getEquiposAsignados($usuarioId)
    {
        $builder = $this->db->table('equipos e');
        $builder->select('e.idequipo, e.descripcion as equipo, e.estadoservicio as estado, 
                         s.servicio, sc.fechahoraservicio as fechaservicio,
                         CONCAT(p.nombres, " ", p.apellidos) as cliente');
        $builder->join('servicioscontratados sc', 'e.idserviciocontratado = sc.idserviciocontratado');
        $builder->join('servicios s', 'sc.idservicio = s.idservicio');
        $builder->join('cotizaciones co', 'sc.idcotizacion = co.idcotizacion');
        $builder->join('clientes cl', 'co.idcliente = cl.idcliente');
        $builder->join('personas p', 'cl.idpersona = p.idpersona');
        $builder->where('e.idusuario', $usuarioId);
        $builder->orderBy('sc.fechahoraservicio', 'DESC');
        
        return $builder->get()->getResult();
    }

    // Actualizar estado de equipo
    public function actualizarEstadoEquipo($equipoId, $nuevoEstado)
    {
        $builder = $this->db->table('equipos');
        $builder->where('idequipo', $equipoId);
        return $builder->update(['estadoservicio' => $nuevoEstado]);
    }
}