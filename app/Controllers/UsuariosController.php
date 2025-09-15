<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\PersonaModel;
use App\Models\CargoModel;

class UsuariosController extends BaseController
{
    protected $usuarioModel;
    protected $personaModel;
    
    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->personaModel = new PersonaModel();
    }
    
    /**
     * Generar token CSRF específico para un contexto
     */
    private function generateCSRFToken($context, $identifier = null)
    {
        $token = bin2hex(random_bytes(32));
        $key = 'csrf_token_' . $context . ($identifier ? '_' . $identifier : '');
        session()->set($key, $token);
        return $token;
    }
    
    /**
     * Validar token CSRF específico para un contexto
     */
    private function validateCSRFToken($context, $identifier = null)
    {
        $token_recibido = $this->request->getPost('csrf_token');
        $key = 'csrf_token_' . $context . ($identifier ? '_' . $identifier : '');
        $token_almacenado = session()->get($key);
        
        if (!$token_recibido || !$token_almacenado || !hash_equals($token_almacenado, $token_recibido)) {
            return false;
        }
        
        // Eliminar el token usado para evitar reutilización
        session()->remove($key);
        return true;
    }
    
    // Listar usuarios
    public function index()
    {
        $data = [
            'title' => 'Gestión de Credenciales de Trabajadores - ISHUME',
            'usuarios' => $this->usuarioModel->getUsuariosCompletos(),
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];

        return view('usuarios/listar', $data);
    }

    // Mostrar formulario de crear usuario
    public function crear($tipo = 'existente')
    {
        // Generar token CSRF específico para crear usuarios
        $csrf_token = $this->generateCSRFToken('crear_usuario');
        
        // Obtener personas sin usuario para el select
        $personaModel = new \App\Models\PersonaModel();
        $cargoModel = new \App\Models\CargoModel();
        
        $data = [
            'title' => ($tipo === 'nuevo') ? 'Crear Nuevo Personal - ISHUME' : 'Crear Credenciales - ISHUME',
            'personas' => $personaModel->getPersonasSinUsuario(),
            'cargos' => $cargoModel->findAll(),
            'tipo_creacion' => $tipo,
            'csrf_token' => $csrf_token, // Pasar el token a la vista
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];

        return view('usuarios/crear', $data);
    }

    // Guardar nuevo usuario
    public function guardar()
    {
        // Verificar token CSRF específico para crear usuarios
        if (!$this->validateCSRFToken('crear_usuario')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Token de seguridad inválido. Por favor, recargue la página e intente nuevamente.'
            ]);
        }
        
        // Validar que las contraseñas coincidan primero
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if ($password !== $confirmPassword) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Las contraseñas no coinciden'
            ]);
        }

        $validation = \Config\Services::validation();
        $tipoCreacion = $this->request->getPost('tipo_creacion');

        // Log para debug
        log_message('info', 'Tipo de creación: ' . $tipoCreacion);
        log_message('info', 'POST data: ' . print_r($this->request->getPost(), true));

        // Debug: Log de entrada
        log_message('info', 'UsuariosController::guardar - Iniciando proceso');
        log_message('info', 'POST data: ' . print_r($this->request->getPost(), true));

        $validation = \Config\Services::validation();

        // Validaciones robustas
        $validation->setRules([
            'tipo_creacion' => 'required|in_list[existente,nuevo]',
            'idcargo' => 'required|integer',
            'nombreusuario' => [
                'rules' => 'required|min_length[5]|max_length[50]|is_unique[usuarios.nombreusuario]|regex_match[/^[a-zA-Z0-9_]+$/]',
                'errors' => [
                    'required' => 'El nombre de usuario es obligatorio',
                    'min_length' => 'El nombre de usuario debe tener al menos 5 caracteres',
                    'max_length' => 'El nombre de usuario no puede exceder 50 caracteres',
                    'is_unique' => 'Este nombre de usuario ya existe',
                    'regex_match' => 'El nombre de usuario solo puede contener letras, números y guiones bajos'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[usuarios.email]',
                'errors' => [
                    'required' => 'El email es obligatorio',
                    'valid_email' => 'Debe ser un email válido',
                    'is_unique' => 'Este email ya está registrado'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required' => 'La contraseña es obligatoria',
                    'min_length' => 'La contraseña debe tener al menos 8 caracteres'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Debe confirmar la contraseña',
                    'matches' => 'Las contraseñas no coinciden'
                ]
            ]
        ]);

        // Validaciones adicionales para nuevas personas
        if ($this->request->getPost('tipo_creacion') === 'nuevo') {
            $validation->setRules(array_merge($validation->getRules(), [
                'nombres' => [
                    'rules' => 'required|min_length[2]|max_length[100]',
                    'errors' => [
                        'required' => 'Los nombres son obligatorios',
                        'min_length' => 'Los nombres deben tener al menos 2 caracteres'
                    ]
                ],
                'apellidos' => [
                    'rules' => 'required|min_length[2]|max_length[100]',
                    'errors' => [
                        'required' => 'Los apellidos son obligatorios',
                        'min_length' => 'Los apellidos deben tener al menos 2 caracteres'
                    ]
                ],
                'numerodoc' => [
                    'rules' => 'required|exact_length[8]|numeric|is_unique[personas.numerodoc]',
                    'errors' => [
                        'required' => 'El número de documento es obligatorio',
                        'exact_length' => 'El DNI debe tener exactamente 8 dígitos',
                        'numeric' => 'El DNI solo debe contener números',
                        'is_unique' => 'Este número de documento ya está registrado'
                    ]
                ],
                'telprincipal' => [
                    'rules' => 'required|min_length[9]|max_length[9]|numeric',
                    'errors' => [
                        'required' => 'El teléfono principal es obligatorio',
                        'min_length' => 'El teléfono debe tener exactamente 9 dígitos',
                        'max_length' => 'El teléfono debe tener exactamente 9 dígitos',
                        'numeric' => 'El teléfono solo debe contener números'
                    ]
                ],
                'direccion' => [
                    'rules' => 'required|min_length[10]',
                    'errors' => [
                        'required' => 'La dirección es obligatoria',
                        'min_length' => 'La dirección debe tener al menos 10 caracteres'
                    ]
                ]
            ]));
        } else {
            $validation->setRules(array_merge($validation->getRules(), [
                'idpersona' => [
                    'rules' => 'required|integer',
                    'errors' => [
                        'required' => 'Debe seleccionar una persona',
                        'integer' => 'Selección de persona inválida'
                    ]
                ]
            ]));
        }

        if (!$validation->withRequest($this->request)->run()) {
            log_message('error', 'Errores de validación: ' . print_r($validation->getErrors(), true));
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validation->getErrors()
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            if ($tipoCreacion === 'nuevo') {
                // Crear nueva persona
                $personaData = [
                    'nombres' => trim($this->request->getPost('nombres')),
                    'apellidos' => trim($this->request->getPost('apellidos')),
                    'numerodoc' => trim($this->request->getPost('numerodoc')),
                    'tipodoc' => $this->request->getPost('tipodoc') ?: 'DNI',
                    'telprincipal' => trim($this->request->getPost('telprincipal')),
                    'telalternativo' => trim($this->request->getPost('telalternativo')) ?: null,
                    'direccion' => trim($this->request->getPost('direccion')),
                    'referencia' => trim($this->request->getPost('referencia')) ?: null
                ];

                $this->personaModel->insert($personaData);
                $idpersona = $this->personaModel->insertID();

                if (!$idpersona) {
                    throw new \Exception('Error al crear la persona');
                }
            } else {
                $idpersona = $this->request->getPost('idpersona');
            }

            // Verificar que idpersona es válido
            if (empty($idpersona) || !is_numeric($idpersona)) {
                throw new \Exception('ID de persona no válido: ' . $idpersona);
            }

            // Crear usuario
            $usuarioData = [
                'idpersona' => $idpersona,
                'idcargo' => $this->request->getPost('idcargo'),
                'nombreusuario' => trim($this->request->getPost('nombreusuario')),
                'email' => trim($this->request->getPost('email')),
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'tipo_usuario' => 'trabajador',
                'estado' => 1
            ];

            $this->usuarioModel->insert($usuarioData);
            
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error en la transacción');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Usuario creado exitosamente'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Validar seguridad de contraseña
    private function validatePasswordSecurity($password, $username)
    {
        $errors = [];
        
        // Verificar complejidad
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Debe contener al menos una letra mayúscula';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Debe contener al menos una letra minúscula';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Debe contener al menos un número';
        }
        
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
            $errors[] = 'Debe contener al menos un símbolo especial';
        }
        
        // Verificar que no sea igual al username
        if (strtolower($password) === strtolower($username)) {
            $errors[] = 'La contraseña no puede ser igual al nombre de usuario';
        }
        
        // Verificar contraseñas comunes
        $commonPasswords = [
            '123456', 'password', '123456789', '12345678', '12345', '1234567', 
            'qwerty', 'abc123', 'password123', 'admin', 'letmein', 'welcome',
            '123123', 'password1', 'qwerty123', 'admin123'
        ];
        
        if (in_array(strtolower($password), $commonPasswords)) {
            $errors[] = 'La contraseña es demasiado común y fácil de adivinar';
        }
        
        return $errors;
    }

    // Mostrar formulario para editar usuario
    public function editar($idusuario)
    {
        // Generar token CSRF específico para editar usuarios
        $csrf_token = $this->generateCSRFToken('editar_usuario', $idusuario);
        
        $usuario = $this->usuarioModel->getUsuarioCompleto($idusuario);
        
        if (!$usuario) {
            return redirect()->to('/usuarios')->with('error', 'Usuario no encontrado');
        }

        $cargoModel = new \App\Models\CargoModel();

        $data = [
            'title' => 'Editar Credenciales - ISHUME',
            'usuario' => $usuario,
            'cargos' => $cargoModel->findAll(),
            'csrf_token' => $csrf_token, // Pasar el token a la vista
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];

        return view('usuarios/editar', $data);
    }

    // Obtener datos de una persona para mostrar en el formulario
    public function obtenerPersona($idpersona)
    {
        $personaModel = new \App\Models\PersonaModel();
        $persona = $personaModel->find($idpersona);
        
        if ($persona) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $persona
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Persona no encontrada'
            ]);
        }
    }

    // Actualizar usuario
    public function actualizar($idusuario)
    {
        // Verificar token CSRF específico para editar usuarios
        if (!$this->validateCSRFToken('editar_usuario', $idusuario)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Token de seguridad inválido. Por favor, recargue la página e intente nuevamente.'
            ]);
        }
        
        log_message('info', 'UsuariosController::actualizar - ID: ' . $idusuario);
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));

        $usuario = $this->usuarioModel->getUsuarioCompleto($idusuario);
        if (!$usuario) {
            return $this->response->setJSON(['success' => false, 'message' => 'Usuario no encontrado']);
        }

        $validation = \Config\Services::validation();
        
        // Reglas de validación para actualización de credenciales
        $rules = [
            'idcargo' => [
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'Debe seleccionar un cargo',
                    'integer' => 'Selección de cargo inválida'
                ]
            ],
            'tipo_usuario' => [
                'rules' => 'required|in_list[admin,trabajador]',
                'errors' => [
                    'required' => 'Debe seleccionar un tipo de usuario',
                    'in_list' => 'Tipo de usuario inválido'
                ]
            ]
        ];

        // Si se proporciona nueva contraseña, validarla
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required' => 'La contraseña es obligatoria',
                    'min_length' => 'La contraseña debe tener al menos 8 caracteres'
                ]
            ];
            $rules['confirm_password'] = [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Debe confirmar la contraseña',
                    'matches' => 'Las contraseñas no coinciden'
                ]
            ];
        }

        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Errores de validación: ' . implode(', ', $validation->getErrors())
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Datos para actualizar usuario
            $userData = [
                'idcargo' => $this->request->getPost('idcargo'),
                'tipo_usuario' => $this->request->getPost('tipo_usuario')
            ];

            // Si hay nueva contraseña, validar seguridad y hashear
            if (!empty($password)) {
                // Validar seguridad de contraseña
                $passwordErrors = $this->validatePasswordSecurity($password, $usuario->nombreusuario);
                
                if (!empty($passwordErrors)) {
                    $db->transRollback();
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'La contraseña no cumple con los requisitos de seguridad: ' . implode(', ', $passwordErrors)
                    ]);
                }

                $userData['claveacceso'] = $password;
                $userData['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
            }

            // Actualizar usuario
            $result = $this->usuarioModel->update($idusuario, $userData);

            if (!$result) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al actualizar las credenciales'
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error en la transacción de base de datos'
                ]);
            }

            log_message('info', 'Usuario actualizado exitosamente - ID: ' . $idusuario);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Credenciales actualizadas exitosamente'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar usuario: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    // Eliminar usuario (soft delete)
    public function eliminar($idusuario)
    {
        log_message('info', 'UsuariosController::eliminar - ID: ' . $idusuario);

        try {
            $usuario = $this->usuarioModel->find($idusuario);
            
            if (!$usuario) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ]);
            }

            // Usar soft delete (cambiar estado a 0) en lugar de eliminar físicamente
            $result = $this->usuarioModel->update($idusuario, ['estado' => 0]);

            if ($result) {
                log_message('info', 'Usuario desactivado exitosamente - ID: ' . $idusuario);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Credenciales desactivadas exitosamente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al desactivar las credenciales'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar usuario: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    // Obtener personas sin usuario (AJAX)
    public function getPersonasSinUsuario()
    {
        try {
            // Usar el modelo en lugar de consulta directa
            $personas = $this->personaModel->getPersonasSinUsuario();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $personas
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener personas sin usuario: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener las personas'
            ]);
        }
    }
}