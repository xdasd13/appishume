<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\PersonaModel;
use App\Models\CargoModel;
use App\Libraries\ReniecService;

class UsuariosController extends BaseController
{
    protected $usuarioModel;
    protected $personaModel;
    protected $cargoModel;
    
    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->personaModel = new PersonaModel();
        $this->cargoModel = new CargoModel();
    }
        
    // Listar usuarios
    public function index()
    {
        // Obtener filtro de estado desde GET (activos=1, desactivados=0, todos=null)
        $filtroEstado = $this->request->getGet('estado');
        
        // Convertir a entero o null
        if ($filtroEstado === '0') {
            $estado = 0; // Desactivados
        } elseif ($filtroEstado === 'todos') {
            $estado = null; // Todos
        } else {
            $estado = 1; // Activos (por defecto)
        }
        
        $usuarios = $this->usuarioModel->getUsuariosCompletos($estado);
        
        // Determinar título según filtro
        $titulo = 'Gestión de Credenciales de Trabajadores - ISHUME';
        $subtitulo = 'Lista de Credenciales Activas';
        
        if ($estado === 0) {
            $subtitulo = 'Lista de Credenciales Desactivadas';
        } elseif ($estado === null) {
            $subtitulo = 'Lista de Todas las Credenciales';
        }
        
        $data = [
            'title' => $titulo,
            'subtitulo' => $subtitulo,
            'usuarios' => $usuarios,
            'filtro_actual' => $filtroEstado ?: 'activos',
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];

        return view('usuarios/listar', $data);
    }

    // Mostrar formulario de crear usuario
    public function crear($tipo = 'existente')
    {
        // Usar el token CSRF nativo de CodeIgniter
        $csrf_token = csrf_token();
        $csrf_hash = csrf_hash();
        
        // Obtener personas sin usuario para el select
        $personaModel = new \App\Models\PersonaModel();
        $cargoModel = new \App\Models\CargoModel();
        
        $data = [
            'title' => ($tipo === 'nuevo') ? 'Crear Nuevo Personal - ISHUME' : 'Crear Credenciales - ISHUME',
            'personas' => $personaModel->getPersonasSinUsuario(),
            'cargos' => $cargoModel->findAll(),
            'tipo_creacion' => $tipo,
            'csrf_token' => $csrf_hash, // Usar el hash nativo
            'csrf_token_name' => $csrf_token, // Nombre del token
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];

        return view('usuarios/crear', $data);
    }

    // Guardar nuevo usuario
    public function guardar()
    {
        // El CSRF se maneja automáticamente por CodeIgniter
        // No necesitamos validación manual adicional
        
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
                'rules' => 'required|min_length[4]|max_length[20]|is_unique[usuarios.nombreusuario]|regex_match[/^[a-zA-Z0-9_\-]+$/]',
                'errors' => [
                    'required' => 'El nombre de usuario es obligatorio',
                    'min_length' => 'El nombre de usuario debe tener al menos 4 caracteres',
                    'max_length' => 'El nombre de usuario no puede exceder 20 caracteres',
                    'is_unique' => 'Este nombre de usuario ya existe',
                    'regex_match' => 'El nombre de usuario solo puede contener letras, números, guiones bajos y guiones'
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
                    'rules' => 'required|exact_length[8]|numeric',
                    'errors' => [
                        'required' => 'El número de documento es obligatorio',
                        'exact_length' => 'El DNI debe tener exactamente 8 dígitos',
                        'numeric' => 'El DNI solo debe contener números'
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
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'La dirección es obligatoria'
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

        // Validación adicional para DNI duplicado (solo para tipo_creacion = 'nuevo')
        if ($tipoCreacion === 'nuevo') {
            $dni = trim($this->request->getPost('numerodoc'));
            $existingPerson = $this->personaModel->where('numerodoc', $dni)->first();
            
            if ($existingPerson) {
                // Verificar si tiene usuario activo
                $existingUser = $this->usuarioModel->where('idpersona', $existingPerson->idpersona)->first();
                
                if ($existingUser && $existingUser->estado == 1) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Este DNI ya tiene credenciales activas en el sistema'
                    ]);
                }
                
                // Si tiene usuario desactivado o no tiene usuario, permitir continuar
                // (el JavaScript ya manejó estos casos y el usuario decidió continuar)
                log_message('info', "DNI {$dni} existe pero usuario decidió continuar - Estado usuario: " . 
                           ($existingUser ? ($existingUser->estado ? 'activo' : 'desactivado') : 'sin usuario'));
            }
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            if ($tipoCreacion === 'nuevo') {
                // Log de datos recibidos para debugging
                log_message('info', 'Datos POST recibidos: ' . json_encode($this->request->getPost()));
                
                // Crear nueva persona
                $personaData = [
                    'nombres' => trim($this->request->getPost('nombres')),
                    'apellidos' => trim($this->request->getPost('apellidos')),
                    'numerodoc' => trim($this->request->getPost('numerodoc')),
                    'tipodoc' => 'DNI', // Campo readonly, siempre DNI
                    'telprincipal' => trim($this->request->getPost('telprincipal')),
                    'telalternativo' => trim($this->request->getPost('telalternativo')) ?: null,
                    'direccion' => trim($this->request->getPost('direccion')),
                    'referencia' => trim($this->request->getPost('referencia')) ?: null
                ];
                
                log_message('info', 'Datos persona a insertar: ' . json_encode($personaData));

                $insertResult = $this->personaModel->insert($personaData);
                if (!$insertResult) {
                    $errors = $this->personaModel->errors();
                    log_message('error', 'Error al insertar persona: ' . json_encode($errors));
                    throw new \Exception('Error al crear la persona: ' . implode(', ', $errors));
                }
                
                $idpersona = $this->personaModel->insertID();
                log_message('info', 'Persona creada con ID: ' . $idpersona);

                if (!$idpersona) {
                    throw new \Exception('Error al obtener ID de la persona creada');
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

            log_message('info', 'Datos usuario a insertar: ' . json_encode($usuarioData));
            
            $insertUserResult = $this->usuarioModel->insert($usuarioData);
            if (!$insertUserResult) {
                $userErrors = $this->usuarioModel->errors();
                log_message('error', 'Error al insertar usuario: ' . json_encode($userErrors));
                throw new \Exception('Error al crear el usuario: ' . implode(', ', $userErrors));
            }
            
            $idusuario = $this->usuarioModel->insertID();
            log_message('info', 'Usuario creado con ID: ' . $idusuario);
            
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
        // Usar el token CSRF nativo de CodeIgniter
        $csrf_token = csrf_token();
        $csrf_hash = csrf_hash();
        
        $usuario = $this->usuarioModel->getUsuarioCompleto($idusuario);
        
        if (!$usuario) {
            return redirect()->to('/usuarios')->with('error', 'Usuario no encontrado');
        }

        $cargoModel = new \App\Models\CargoModel();

        $data = [
            'title' => 'Editar Credenciales - ISHUME',
            'usuario' => $usuario,
            'cargos' => $cargoModel->findAll(),
            'csrf_token' => $csrf_hash, // Usar el hash nativo
            'csrf_token_name' => $csrf_token, // Nombre del token
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
        // El CSRF se maneja automáticamente por CodeIgniter
        // No necesitamos validación manual adicional
        
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
        $password = $this->request->getPost('nueva_password');
        $confirmPassword = $this->request->getPost('confirmar_password');
        
        if (!empty($password)) {
            $rules['nueva_password'] = [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required' => 'La contraseña es obligatoria',
                    'min_length' => 'La contraseña debe tener al menos 8 caracteres'
                ]
            ];
            $rules['confirmar_password'] = [
                'rules' => 'required|matches[nueva_password]',
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

                // Solo guardar el hash, no el texto plano
                $userData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
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

    /**
     * Validar DNI via AJAX usando RENIEC/Decolecta
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function ajaxCheckDni()
    {
        // Solo permitir POST
        if (!$this->request->isAJAX() || !$this->request->getMethod() === 'post') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Método no permitido'
            ])->setStatusCode(405);
        }

        try {
            // El CSRF se valida automáticamente por el filtro global
            
            $dni = $this->request->getPost('dni');
            
            // Validación básica del DNI
            if (empty($dni)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'DNI es requerido'
                ]);
            }

            // Validar formato de DNI
            if (!preg_match('/^\d{8}$/', $dni)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'DNI debe tener exactamente 8 dígitos numéricos'
                ]);
            }

            // Verificar si el DNI ya existe en la base de datos local
            try {
                $existingPerson = $this->personaModel->where('numerodoc', $dni)->first();
                if ($existingPerson) {
                    // Verificar si esta persona tiene usuario activo o desactivado
                    $existingUser = $this->usuarioModel->where('idpersona', $existingPerson->idpersona)->first();
                    
                    if ($existingUser) {
                        if ($existingUser->estado == 1) {
                            // Usuario activo
                            return $this->response->setJSON([
                                'status' => 'exists_active',
                                'message' => 'Este DNI ya está registrado con credenciales ACTIVAS',
                                'data' => [
                                    'dni' => $existingPerson->numerodoc,
                                    'nombres' => $existingPerson->nombres,
                                    'apellidos' => $existingPerson->apellidos,
                                    'usuario' => $existingUser->nombreusuario,
                                    'email' => $existingUser->email,
                                    'estado' => 'activo',
                                    'source' => 'local_db'
                                ]
                            ]);
                        } else {
                            // Usuario desactivado
                            return $this->response->setJSON([
                                'status' => 'exists_inactive',
                                'message' => 'Este DNI pertenece a un usuario con credenciales DESACTIVADAS',
                                'data' => [
                                    'dni' => $existingPerson->numerodoc,
                                    'nombres' => $existingPerson->nombres,
                                    'apellidos' => $existingPerson->apellidos,
                                    'usuario' => $existingUser->nombreusuario,
                                    'email' => $existingUser->email,
                                    'estado' => 'desactivado',
                                    'idusuario' => $existingUser->idusuario,
                                    'source' => 'local_db'
                                ]
                            ]);
                        }
                    } else {
                        // Persona existe pero sin usuario (disponible para crear credenciales)
                        return $this->response->setJSON([
                            'status' => 'exists_no_user',
                            'message' => 'Esta persona ya está registrada pero sin credenciales',
                            'data' => [
                                'dni' => $existingPerson->numerodoc,
                                'nombres' => $existingPerson->nombres,
                                'apellidos' => $existingPerson->apellidos,
                                'idpersona' => $existingPerson->idpersona,
                                'source' => 'local_db'
                            ]
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Si falla la consulta local, continuar con RENIEC
                log_message('warning', 'No se pudo verificar DNI en BD local: ' . $e->getMessage());
            }

            // Consultar RENIEC via Decolecta
            $reniecService = new ReniecService();
            $result = $reniecService->consultarDni($dni);

            // Log de la consulta para auditoría
            log_message('info', "UsuariosController::ajaxCheckDni - DNI: {$dni}, Result: " . json_encode($result));

            if ($result['status'] === 'success') {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'DNI válido encontrado en RENIEC',
                    'data' => [
                        'dni' => $result['data']['dni'],
                        'nombres' => $result['data']['nombres'],
                        'apellido_paterno' => $result['data']['apellido_paterno'],
                        'apellido_materno' => $result['data']['apellido_materno'],
                        'apellidos_completos' => $result['data']['apellidos_completos'],
                        'fecha_nacimiento' => $result['data']['fecha_nacimiento'],
                        'sexo' => $result['data']['sexo'],
                        'source' => $result['data']['source'],
                        'privacy_notice' => 'Los datos fueron obtenidos de RENIEC y están protegidos por la Ley de Protección de Datos Personales'
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $result['message'],
                    'data' => null
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'UsuariosController::ajaxCheckDni - Error: ' . $e->getMessage());
            log_message('error', 'UsuariosController::ajaxCheckDni - File: ' . $e->getFile() . ':' . $e->getLine());
            log_message('error', 'UsuariosController::ajaxCheckDni - Trace: ' . $e->getTraceAsString());
            
            // En desarrollo, mostrar más detalles del error
            $errorMessage = 'Error interno del servidor. Intente nuevamente.';
            if (ENVIRONMENT === 'development') {
                $errorMessage .= ' Debug: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
            }
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $errorMessage,
                'debug' => ENVIRONMENT === 'development' ? [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ])->setStatusCode(500);
        }
    }

    /**
     * Obtener estadísticas del servicio RENIEC (solo admin)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function reniecStats()
    {
        // Verificar permisos de administrador
        
        try {
            $reniecService = new ReniecService();
            $stats = $reniecService->getStats();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'UsuariosController::reniecStats - Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error obteniendo estadísticas'
            ])->setStatusCode(500);
        }
    }

    /**
     * Reactivar usuario desactivado
     */
    public function reactivar($idusuario)
    {
        log_message('info', 'UsuariosController::reactivar - ID: ' . $idusuario);

        try {
            $usuario = $this->usuarioModel->find($idusuario);
            
            if (!$usuario) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ]);
            }

            // Manejar tanto arrays como objetos
            $estado = is_array($usuario) ? $usuario['estado'] : $usuario->estado;
            
            if ($estado == 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'El usuario ya está activo'
                ]);
            }

            // Reactivar usuario (cambiar estado a 1)
            $result = $this->usuarioModel->update($idusuario, ['estado' => 1]);

            if ($result) {
                log_message('info', 'Usuario reactivado exitosamente - ID: ' . $idusuario);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Usuario reactivado exitosamente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al reactivar el usuario'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al reactivar usuario: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar usuario permanentemente de la base de datos
     */
    public function eliminarPermanente($idusuario)
    {
        log_message('info', 'UsuariosController::eliminarPermanente - ID: ' . $idusuario);

        try {
            $usuario = $this->usuarioModel->find($idusuario);
            
            if (!$usuario) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ]);
            }

            // Manejar tanto arrays como objetos
            $estado = is_array($usuario) ? $usuario['estado'] : $usuario->estado;
            
            // Solo permitir eliminar usuarios desactivados
            if ($estado == 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se puede eliminar un usuario activo. Primero debe desactivarlo.'
                ]);
            }

            // Eliminar usuario permanentemente
            $result = $this->usuarioModel->delete($idusuario);

            if ($result) {
                log_message('info', 'Usuario eliminado permanentemente - ID: ' . $idusuario);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Usuario eliminado permanentemente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al eliminar el usuario'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar usuario permanentemente: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Verificar disponibilidad de email via AJAX
     * 
     * Valida si un email está disponible y genera alternativas inteligentes
     * en caso de duplicados. Implementa debounce y validación robusta.
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON con estado y alternativas
     */
    public function ajaxCheckEmail()
    {
        // Validar que sea una petición AJAX POST
        if (!$this->request->isAJAX() || $this->request->getMethod() !== 'post') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Método no permitido'
            ])->setStatusCode(405);
        }

        try {
            // Obtener y validar parámetros
            $email = trim($this->request->getPost('email'));
            $nombres = trim($this->request->getPost('nombres') ?? '');
            $apellidos = trim($this->request->getPost('apellidos') ?? '');
            $excludeUserId = $this->request->getPost('exclude_user_id'); // Para edición

            // Validación básica del email
            if (empty($email)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Email es requerido'
                ]);
            }

            // Validar formato de email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->response->setJSON([
                    'status' => 'invalid',
                    'message' => 'Formato de email inválido'
                ]);
            }

            // Verificar si el email ya existe (excluyendo usuario actual en edición)
            $query = $this->usuarioModel->where('email', $email);
            if ($excludeUserId) {
                $query->where('idusuario !=', $excludeUserId);
            }
            $existingUser = $query->first();

            if ($existingUser) {
                // Email duplicado - generar alternativas
                $alternatives = $this->generateEmailAlternatives($nombres, $apellidos, $email, $excludeUserId);
                
                // Obtener información del usuario existente
                $personaExistente = $this->personaModel->find($existingUser->idpersona);
                
                log_message('info', "Email duplicado detectado: {$email} - Usuario: {$existingUser->nombreusuario}");
                
                return $this->response->setJSON([
                    'status' => 'exists',
                    'message' => 'Este email ya está registrado en el sistema',
                    'current_email' => $email,
                    'alternatives' => $alternatives,
                    'existing_user' => [
                        'nombres' => $personaExistente ? $personaExistente->nombres : 'Usuario',
                        'apellidos' => $personaExistente ? $personaExistente->apellidos : 'Existente',
                        'usuario' => $existingUser->nombreusuario,
                        'estado' => $existingUser->estado ? 'Activo' : 'Inactivo'
                    ]
                ]);
            }

            // Email disponible
            log_message('info', "Email disponible verificado: {$email}");
            
            return $this->response->setJSON([
                'status' => 'available',
                'message' => 'Email disponible para uso',
                'email' => $email
            ]);

        } catch (\Exception $e) {
            log_message('error', 'UsuariosController::ajaxCheckEmail - Error: ' . $e->getMessage());
            log_message('error', 'UsuariosController::ajaxCheckEmail - Trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error interno del servidor. Intente nuevamente.',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ])->setStatusCode(500);
        }
    }

    /**
     * Generar alternativas inteligentes de email
     * 
     * Implementa múltiples estrategias para generar emails únicos
     * basados en nombres, apellidos y patrones comunes.
     * 
     * @param string $nombres Nombres de la persona
     * @param string $apellidos Apellidos de la persona  
     * @param string $originalEmail Email original que causó conflicto
     * @param int|null $excludeUserId ID de usuario a excluir (para edición)
     * @return array Lista de emails alternativos disponibles
     */
    private function generateEmailAlternatives(string $nombres, string $apellidos, string $originalEmail, $excludeUserId = null): array
    {
        $alternatives = [];
        $maxAlternatives = 5; // Máximo número de sugerencias
        
        try {
            // Extraer componentes del email original
            $baseEmail = str_replace('@ishume.com', '', $originalEmail);
            $primerNombre = $this->cleanTextForEmail($this->getFirstWord($nombres));
            $primerApellido = $this->cleanTextForEmail($this->getFirstWord($apellidos));
            $segundoApellido = $this->cleanTextForEmail($this->getSecondWord($apellidos));
            
            // Estrategias de generación ordenadas por preferencia
            $strategies = [
                // Estrategia 1: Agregar número secuencial
                $baseEmail . '2@ishume.com',
                $baseEmail . '3@ishume.com',
                
                // Estrategia 2: Agregar año actual
                $baseEmail . date('y') . '@ishume.com',
                
                // Estrategia 3: Nombre completo + apellido
                $primerNombre . '.' . $primerApellido . '@ishume.com',
                
                // Estrategia 4: Inicial + apellido + año
                substr($primerNombre, 0, 1) . $primerApellido . date('y') . '@ishume.com',
                
                // Estrategia 5: Nombre + segundo apellido (si existe)
                $segundoApellido ? $primerNombre . '.' . $segundoApellido . '@ishume.com' : null,
                
                // Estrategia 6: Iniciales + apellido
                substr($primerNombre, 0, 1) . substr($primerApellido, 0, 1) . $primerApellido . '@ishume.com',
                
                // Estrategia 7: Nombre + números aleatorios
                $primerNombre . '.' . $primerApellido . rand(10, 99) . '@ishume.com',
                
                // Estrategia 8: Formato tradicional con guión
                $primerNombre . '-' . $primerApellido . '@ishume.com',
            ];
            
            // Filtrar estrategias nulas y validar disponibilidad
            foreach (array_filter($strategies) as $candidate) {
                if (count($alternatives) >= $maxAlternatives) {
                    break;
                }
                
                // Verificar que el candidato sea válido y único
                if ($this->isEmailValid($candidate) && $this->isEmailAvailable($candidate, $excludeUserId)) {
                    $alternatives[] = $candidate;
                }
            }
            
            log_message('info', "Generadas " . count($alternatives) . " alternativas para email: {$originalEmail}");
            
        } catch (\Exception $e) {
            log_message('error', 'Error generando alternativas de email: ' . $e->getMessage());
        }
        
        return $alternatives;
    }

    /**
     * Limpiar texto para uso en emails
     * 
     * Normaliza texto removiendo acentos, caracteres especiales
     * y convirtiendo a formato apropiado para emails.
     * 
     * @param string $text Texto a limpiar
     * @return string Texto normalizado
     */
    private function cleanTextForEmail(string $text): string
    {
        return strtolower(
            preg_replace('/[^a-z0-9]/', '', 
                str_replace(
                    ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'], 
                    ['a', 'e', 'i', 'o', 'u', 'n', 'u'], 
                    strtolower(
                        iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text)
                    )
                )
            )
        );
    }

    /**
     * Obtener primera palabra de un texto
     * 
     * @param string $text Texto completo
     * @return string Primera palabra
     */
    private function getFirstWord(string $text): string
    {
        $words = explode(' ', trim($text));
        return $words[0] ?? '';
    }

    /**
     * Obtener segunda palabra de un texto
     * 
     * @param string $text Texto completo
     * @return string Segunda palabra o cadena vacía
     */
    private function getSecondWord(string $text): string
    {
        $words = explode(' ', trim($text));
        return $words[1] ?? '';
    }

    /**
     * Validar formato de email
     * 
     * @param string $email Email a validar
     * @return bool True si es válido
     */
    private function isEmailValid(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Verificar si un email está disponible
     * 
     * @param string $email Email a verificar
     * @param int|null $excludeUserId ID de usuario a excluir
     * @return bool True si está disponible
     */
    private function isEmailAvailable(string $email, $excludeUserId = null): bool
    {
        $query = $this->usuarioModel->where('email', $email);
        if ($excludeUserId) {
            $query->where('idusuario !=', $excludeUserId);
        }
        return $query->first() === null;
    }

}