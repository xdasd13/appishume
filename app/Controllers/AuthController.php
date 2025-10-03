<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    // Mostrar formulario de login
    public function login()
    {
        // Si ya está logueado, redirigir al dashboard
        if (session()->get('usuario_logueado')) {
            return redirect()->to('/welcome');
        }

        $data = [
            'title' => 'Iniciar Sesión - ISHUME',
            'error' => session()->getFlashdata('error'),
            'success' => session()->getFlashdata('success')
        ];

        return view('auth/login', $data);
    }

    // Procesar login
    public function authenticate()
    {
        $rules = [
            'login' => 'required',
            'password' => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Por favor, completa todos los campos correctamente.');
        }

        $login = $this->request->getPost('login');
        $password = $this->request->getPost('password');

        // Intentar autenticar
        $usuario = $this->usuarioModel->authenticate($login, $password);

        if ($usuario) {
            // Crear sesión
            $nombreCompleto = $usuario->nombres . ' ' . $usuario->apellidos;
            $primerNombre = explode(' ', $usuario->nombres)[0];
            $primeraLetraApellido = substr($usuario->apellidos, 0, 1);
            $nombreCorto = $primerNombre . '.' . $primeraLetraApellido;
            
            $sessionData = [
                'usuario_logueado' => true,
                'usuario_id' => $usuario->idusuario,
                'usuario_nombre' => $nombreCompleto,
                'usuario_nombre_corto' => $nombreCorto,
                'usuario_tipo' => $usuario->tipo_usuario,
                'tipo_usuario' => $usuario->tipo_usuario, // Agregar esta línea para compatibilidad
                'usuario_cargo' => $usuario->cargo,
                'login_time' => date('Y-m-d H:i:s')
            ];

            session()->set($sessionData);

            // Redirigir a welcome después del login exitoso
            return redirect()->to('/welcome')
                           ->with('success', '¡Bienvenido ' . $usuario->nombres . '!');

        } else {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Credenciales incorrectas. Verifica tu usuario/email y contraseña.');
        }
    }

    // Dashboard unificado
    public function dashboard()
    {
        if (!session()->get('usuario_logueado')) {
            return redirect()->to('/login')->with('error', 'Acceso denegado.');
        }

        $tipoUsuario = session()->get('tipo_usuario') ?? session()->get('role');
        $usuarioNombre = session()->get('usuario_nombre') ?? 'Usuario';
        
        // Dashboard unificado para todos los roles
        $data = [
            'title' => 'Dashboard - ISHUME',
            'usuario' => $usuarioNombre,
            'tipo_usuario' => $tipoUsuario,
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];

        // Agregar datos específicos según el rol
        if (in_array($tipoUsuario, ['admin', 'administrador'])) {
            $data['usuarios'] = $this->usuarioModel->getUsuarios();
            $data['trabajadores'] = $this->usuarioModel->getTrabajadores();
            $data['title'] = 'Dashboard Administrador - ISHUME';
        }

        return view('welcome', $data);
    }

    // Dashboard para trabajador
    public function trabajadorDashboard()
    {
        if (!session()->get('usuario_logueado') || session()->get('usuario_tipo') !== 'trabajador') {
            return redirect()->to('/login')->with('error', 'Acceso denegado.');
        }

        $usuarioId = session()->get('usuario_id');
        $equipos = $this->usuarioModel->getEquiposAsignados($usuarioId);

        $data = [
            'title' => 'Mi Panel de Trabajo - ISHUME',
            'usuario' => session()->get('usuario_nombre'),
            'equipos' => $equipos
        ];

        return view('trabajador/dashboard', $data);
    }

    // Crear nuevo trabajador (solo admin)
    public function crearTrabajador()
    {
        if (!session()->get('usuario_logueado') || session()->get('usuario_tipo') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Acceso denegado.');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'nombres' => 'required|min_length[2]',
                'apellidos' => 'required|min_length[2]',
                'numerodoc' => 'required|min_length[8]|is_unique[personas.numerodoc]',
                'telefono' => 'required|min_length[9]',
                'direccion' => 'required',
                'email' => 'required|valid_email|is_unique[usuarios.email]',
                'nombreusuario' => 'required|min_length[3]|is_unique[usuarios.nombreusuario]',
                'password' => 'required|min_length[6]',
                'idcargo' => 'required|integer'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Por favor, corrige los errores en el formulario.');
            }

            // Crear persona
            $personaData = [
                'nombres' => $this->request->getPost('nombres'),
                'apellidos' => $this->request->getPost('apellidos'),
                'tipodoc' => 'DNI',
                'numerodoc' => $this->request->getPost('numerodoc'),
                'telprincipal' => $this->request->getPost('telefono'),
                'direccion' => $this->request->getPost('direccion'),
                'referencia' => $this->request->getPost('referencia') ?? 'Trabajador del sistema'
            ];

            $personaModel = new \App\Models\PersonaModel();
            $personaId = $personaModel->insert($personaData);

            if ($personaId) {
                // Crear usuario
                $usuarioData = [
                    'idpersona' => $personaId,
                    'idcargo' => $this->request->getPost('idcargo'),
                    'nombreusuario' => $this->request->getPost('nombreusuario'),
                    'claveacceso' => $this->request->getPost('password'),
                    'email' => $this->request->getPost('email'),
                    'password' => $this->request->getPost('password')
                ];

                $usuarioId = $this->usuarioModel->crearTrabajador($usuarioData);

                if ($usuarioId) {
                    return redirect()->to('/dashboard')
                                   ->with('success', 'Trabajador creado exitosamente.');
                }
            }

            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear el trabajador.');
        }

        // Obtener cargos para el formulario
        $cargoModel = new \App\Models\CargoModel();
        $cargos = $cargoModel->findAll();

        $data = [
            'title' => 'Crear Trabajador - ISHUME',
            'cargos' => $cargos
        ];

        return view('admin/crear_trabajador', $data);
    }

    // Actualizar estado de equipo (solo trabajadores)
    public function actualizarEstado()
    {
        if (!session()->get('usuario_logueado') || session()->get('usuario_tipo') !== 'trabajador') {
            return $this->response->setJSON(['success' => false, 'message' => 'Acceso denegado']);
        }

        $equipoId = $this->request->getPost('equipo_id');
        $nuevoEstado = $this->request->getPost('estado');

        $estadosPermitidos = ['Pendiente', 'En Proceso', 'Completado'];
        
        if (!in_array($nuevoEstado, $estadosPermitidos)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Estado no válido']);
        }

        $resultado = $this->usuarioModel->actualizarEstadoEquipo($equipoId, $nuevoEstado);

        if ($resultado) {
            return $this->response->setJSON(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    }

    // Verificar estado de sesión (AJAX)
    public function checkSession()
    {
        $response = [
            'valid' => session()->get('usuario_logueado') ? true : false
        ];
        
        return $this->response->setJSON($response);
    }

    // Cerrar sesión
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('logout_success', true);
    }

}
