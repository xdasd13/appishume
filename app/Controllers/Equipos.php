<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EquipoModel;
use App\Models\ServicioModel;
use App\Models\UsuarioModel;

class Equipos extends BaseController
{
    protected $equipoModel;
    protected $servicioModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->equipoModel = new EquipoModel();
        $this->servicioModel = new ServicioModel();
        $this->usuarioModel = new UsuarioModel();
    }

    // Listar todos los equipos
    public function index()
    {
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['equipos'] = $this->equipoModel->getEquipos();
        $datos['titulo'] = 'Gestión de Equipos';
        
        return view('equipos/listar', $datos);
    }

    // Asignar equipo a un servicio - MEJORADO
    public function asignar($idserviciocontratado)
    {
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['servicio'] = $this->servicioModel->getServicioContratado($idserviciocontratado);
        
        // Obtener usuarios con información de disponibilidad
        $datos['usuarios'] = $this->equipoModel->getUsuariosDisponibles($idserviciocontratado);
        $datos['titulo'] = 'Asignar Equipo al Servicio';
        
        return view('equipos/asignar', $datos);
    }

    // Guardar asignación de equipo - MEJORADO CON VALIDACIONES
    public function guardar()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'idusuario' => 'required',
            'descripcion' => 'required',
            'estadoservicio' => 'required'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Por favor complete todos los campos requeridos.');
            return redirect()->back();
        }

        $idusuario = $this->request->getPost('idusuario');
        $idserviciocontratado = $this->request->getPost('idserviciocontratado');

        // VALIDACIONES PERSONALIZADAS
        $erroresValidacion = $this->equipoModel->validarAsignacion($idusuario, $idserviciocontratado);
        
        if (!empty($erroresValidacion)) {
            foreach ($erroresValidacion as $error) {
                session()->setFlashdata('error', $error);
            }
            return redirect()->back()->withInput();
        }

        $data = [
            'idserviciocontratado' => $idserviciocontratado,
            'idusuario' => $idusuario,
            'descripcion' => $this->request->getPost('descripcion'),
            'estadoservicio' => $this->request->getPost('estadoservicio')
        ];

        if ($this->equipoModel->insertEquipo($data)) {
            session()->setFlashdata('success', 'Equipo asignado correctamente.');
        } else {
            session()->setFlashdata('error', 'Error al asignar el equipo.');
        }

        return redirect()->to('equipos/por-servicio/' . $data['idserviciocontratado']);
    }

    // Editar asignación de equipo - MEJORADO
    public function editar($idequipo)
    {
        $equipo = $this->equipoModel->getEquipo($idequipo);
        
        if (!$equipo) {
            session()->setFlashdata('error', 'Equipo no encontrado.');
            return redirect()->to('equipos');
        }

        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['equipo'] = $equipo;
        
        // Obtener usuarios con información de disponibilidad
        $datos['usuarios'] = $this->equipoModel->getUsuariosDisponibles($equipo->idserviciocontratado);
        $datos['titulo'] = 'Editar Asignación de Equipo';
        
        return view('equipos/editar', $datos);
    }

    // Actualizar asignación de equipo
    public function actualizar()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'idusuario' => 'required',
            'descripcion' => 'required',
            'estadoservicio' => 'required'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Por favor complete todos los campos requeridos.');
            return redirect()->back();
        }

        $idequipo = $this->request->getPost('idequipo');
        $idusuario = $this->request->getPost('idusuario');
        
        // Obtener equipo actual para validaciones
        $equipoActual = $this->equipoModel->find($idequipo);
        if (!$equipoActual) {
            session()->setFlashdata('error', 'Equipo no encontrado.');
            return redirect()->to('equipos');
        }

        // VALIDACIONES
        $erroresValidacion = $this->equipoModel->validarAsignacion($idusuario, $equipoActual['idserviciocontratado'], $idequipo);
        
        if (!empty($erroresValidacion)) {
            foreach ($erroresValidacion as $error) {
                session()->setFlashdata('error', $error);
            }
            return redirect()->back()->withInput();
        }

        $data = [
            'idusuario' => $idusuario,
            'descripcion' => $this->request->getPost('descripcion'),
            'estadoservicio' => $this->request->getPost('estadoservicio')
        ];

        if ($this->equipoModel->updateEquipo($idequipo, $data)) {
            session()->setFlashdata('success', 'Asignación actualizada correctamente.');
        } else {
            session()->setFlashdata('error', 'Error al actualizar la asignación.');
        }

        return redirect()->to('equipos');
    }

    // Ver equipos por servicio
    public function por_servicio($idserviciocontratado)
    {
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['equipos'] = $this->equipoModel->getEquiposPorServicio($idserviciocontratado);
        $datos['servicio'] = $this->servicioModel->getServicioContratado($idserviciocontratado);
        $datos['titulo'] = 'Equipos Asignados al Servicio';
        
        return view('equipos/listar', $datos);
    }

    // Ver equipos por usuario
    public function por_usuario($idusuario)
    {
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['equipos'] = $this->equipoModel->getEquiposPorUsuario($idusuario);
        $datos['usuario'] = $this->usuarioModel->getUsuario($idusuario);
        $datos['titulo'] = 'Equipos Asignados al Usuario';
        
        return view('equipos/listar', $datos);
    }

    // NUEVO: Endpoint AJAX para verificar disponibilidad de usuarios
    public function verificarDisponibilidad()
    {
        $idusuario = $this->request->getPost('idusuario');
        $idserviciocontratado = $this->request->getPost('idserviciocontratado');
        
        if (!$idusuario || !$idserviciocontratado) {
            return $this->response->setJSON(['error' => 'Parámetros faltantes']);
        }

        $errores = $this->equipoModel->validarAsignacion($idusuario, $idserviciocontratado);
        
        $response = [
            'disponible' => empty($errores),
            'errores' => $errores
        ];

        // Si hay conflictos de horario, obtener detalles
        if (!$response['disponible']) {
            $builderServicio = $this->equipoModel->db->table('servicioscontratados');
            $builderServicio->select('fechahoraservicio');
            $builderServicio->where('idserviciocontratado', $idserviciocontratado);
            $servicio = $builderServicio->get()->getRow();
            
            if ($servicio) {
                $response['conflictos'] = $this->equipoModel->getDetalleConflictos($idusuario, $servicio->fechahoraservicio);
            }
        }

        return $this->response->setJSON($response);
    }

    // NUEVO: Endpoint AJAX para actualizar estado de equipos con validaciones
    public function actualizarEstado()
    {
        // Verificar que sea una petición AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Petición no válida']);
        }

        $input = json_decode($this->request->getBody(), true);
        $idequipo = $input['id'] ?? null;
        $nuevoEstado = $input['estado'] ?? null;

        if (!$idequipo || !$nuevoEstado) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Parámetros faltantes'
            ]);
        }

        // Obtener el equipo actual
        $equipo = $this->equipoModel->find($idequipo);
        if (!$equipo) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Equipo no encontrado'
            ]);
        }

        $estadoActual = $equipo['estadoservicio'];

        // VALIDACIONES DE TRANSICIÓN DE ESTADOS
        $validacion = $this->validarTransicionEstado($estadoActual, $nuevoEstado);
        
        if (!$validacion['valido']) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $validacion['mensaje']
            ]);
        }

        // Actualizar el estado en la base de datos
        $data = ['estadoservicio' => $nuevoEstado];
        
        if ($this->equipoModel->update($idequipo, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al actualizar el estado en la base de datos'
            ]);
        }
    }

    // Función para validar transiciones de estado
    private function validarTransicionEstado($estadoActual, $nuevoEstado)
    {
        // Normalizar estados para comparación
        $estadoActual = trim($estadoActual);
        $nuevoEstado = trim($nuevoEstado);

        // Validación 1: Si el servicio está completo, no se puede mover a pendiente o en proceso
        if ($estadoActual === 'Completado') {
            if ($nuevoEstado === 'Pendiente' || $nuevoEstado === 'En Proceso') {
                return [
                    'valido' => false,
                    'mensaje' => 'Este servicio ya está completo'
                ];
            }
        }

        // Validación 2: Si el servicio está pendiente y se quiere mover directamente a completo
        if ($estadoActual === 'Pendiente' || $estadoActual === 'Programado') {
            if ($nuevoEstado === 'Completado') {
                return [
                    'valido' => false,
                    'mensaje' => 'Este servicio aún no tiene proceso'
                ];
            }
        }

        // Validación 3: Si el servicio está en proceso y se quiere mover a pendiente
        if ($estadoActual === 'En Proceso') {
            if ($nuevoEstado === 'Pendiente') {
                return [
                    'valido' => false,
                    'mensaje' => 'Este servicio está en proceso'
                ];
            }
        }

        // Si llegamos aquí, la transición es válida
        return [
            'valido' => true,
            'mensaje' => 'Transición válida'
        ];
    }
}