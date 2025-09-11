<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class UsuariosControllerSimple extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    // Listar usuarios
    public function index()
    {
        $data = [
            'title' => 'Usuarios - ISHUME',
            'usuarios' => $this->usuarioModel->findAll()
        ];

        return view('usuarios/listar_simple', $data);
    }

    // Mostrar formulario de crear usuario
    public function crear()
    {
        $data = [
            'title' => 'Crear Usuario - ISHUME'
        ];

        return view('usuarios/crear_ultra_simple', $data);
    }

    // Guardar nuevo usuario - VERSIÓN ULTRA SIMPLE
    public function guardar()
    {
        // Log para debug
        log_message('info', 'UsuariosControllerSimple::guardar - Iniciando');
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));
        
        // Deshabilitar CSRF para esta prueba
        $this->request->setGlobal('post', $this->request->getPost());
        
        // Obtener datos del formulario
        $nombreusuario = $this->request->getPost('nombreusuario');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validaciones básicas
        if (empty($nombreusuario) || empty($email) || empty($password)) {
            $response = [
                'success' => false,
                'message' => 'Todos los campos son obligatorios'
            ];
            log_message('info', 'Error validación: ' . json_encode($response));
            return $this->response->setJSON($response);
        }

        try {
            // Verificar que el usuario no exista
            $existeUsuario = $this->usuarioModel->where('nombreusuario', $nombreusuario)->first();
            if ($existeUsuario) {
                $response = [
                    'success' => false,
                    'message' => 'El nombre de usuario ya existe'
                ];
                log_message('info', 'Usuario existe: ' . json_encode($response));
                return $this->response->setJSON($response);
            }

            // Crear el usuario
            $data = [
                'nombreusuario' => $nombreusuario,
                'email' => $email,
                'claveacceso' => $password,
                'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                'tipo_usuario' => 'trabajador',
                'estado' => 1,
                'idpersona' => 1, // Valor por defecto
                'idcargo' => 1    // Valor por defecto
            ];

            log_message('info', 'Insertando usuario: ' . json_encode($data));
            $result = $this->usuarioModel->insert($data);
            log_message('info', 'Resultado insert: ' . json_encode($result));

            $response = [
                'success' => true,
                'message' => 'Usuario creado exitosamente'
            ];
            log_message('info', 'Éxito: ' . json_encode($response));
            return $this->response->setJSON($response);

        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ];
            log_message('error', 'Exception: ' . $e->getMessage());
            return $this->response->setJSON($response);
        }
    }

    // Eliminar usuario
    public function eliminar($id)
    {
        try {
            $this->usuarioModel->delete($id);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ]);
        }
    }
}
