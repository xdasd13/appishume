<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EquipoModel;
use App\Services\EquipoService;

/**
 * Controlador de Equipos refactorizado aplicando KISS
 * Responsabilidades claras: solo manejo de flujo HTTP
 */
class Equipos extends BaseController
{
    protected EquipoModel $equipoModel;
    protected EquipoService $equipoService;

    public function __construct()
    {
        $this->equipoModel = new EquipoModel();
        $this->equipoService = new EquipoService();
        helper(['estado', 'url']); // Cargar helpers necesarios
    }

    /**
     * Método privado para renderizar vistas con header/footer
     * KISS: elimina duplicación de código
     */
    private function render(string $view, array $data = []): string
    {
        $data['header'] = view('Layouts/header');
        $data['footer'] = view('Layouts/footer');
        return view($view, $data);
    }

    /**
     * Vista principal del tablero Kanban
     * KISS: método simple y claro
     */
    public function index(): string
    {
        $data = [
            'titulo' => 'Gestión de Equipos',
            'equiposKanban' => $this->equipoModel->getEquiposParaKanban(),
            'estadisticas' => $this->equipoService->obtenerEstadisticas()
        ];
        
        return $this->render('equipos/listar', $data);
    }

    /**
     * Vista para asignar técnico a un servicio
     * KISS: delegación clara al servicio
     */
    public function asignar(int $idserviciocontratado)
    {
        $servicio = $this->equipoModel->getServicioInfo($idserviciocontratado);
        
        if (!$servicio) {
            session()->setFlashdata('error', 'Servicio no encontrado');
            return redirect()->to('equipos');
        }

        $data = [
            'titulo' => 'Asignar Técnico al Servicio',
            'servicio' => $servicio,
            'tecnicos' => $this->equipoService->obtenerTecnicosDisponibles(
                $idserviciocontratado, 
                $servicio['fechahoraservicio']
            )
        ];
        
        return $this->render('equipos/asignar', $data);
    }

    /**
     * Método unificado para guardar/actualizar equipos
     * KISS: una sola función para ambas operaciones
     */
    public function guardar(): \CodeIgniter\HTTP\RedirectResponse
    {
        return $this->saveEquipo();
    }

    /**
     * Método unificado para guardar/actualizar equipos
     * KISS: una sola función para ambas operaciones
     */
    public function actualizar(): \CodeIgniter\HTTP\RedirectResponse
    {
        return $this->saveEquipo();
    }

    /**
     * Método unificado para guardar/actualizar equipos
     * KISS: una sola función para ambas operaciones
     */
    public function saveEquipo(): \CodeIgniter\HTTP\RedirectResponse
    {
        // Validaciones básicas
        $rules = [
            'idusuario' => 'required|integer',
            'descripcion' => 'required|min_length[10]',
            'estadoservicio' => 'required|in_list[Pendiente,En Proceso,Completado,Programado]'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Por favor complete correctamente todos los campos.');
            return redirect()->back()->withInput();
        }

        $equipoId = $this->request->getPost('idequipo'); // null si es nuevo
        $usuarioId = (int)$this->request->getPost('idusuario');
        $servicioId = (int)$this->request->getPost('idserviciocontratado');
        $descripcion = $this->request->getPost('descripcion');
        $estado = $this->request->getPost('estadoservicio');

        // Si es edición y no viene servicioId, obtenerlo del equipo existente
        if ($equipoId && !$servicioId) {
            $equipoExistente = $this->equipoModel->asArray()->find($equipoId);
            if ($equipoExistente) {
                $servicioId = $equipoExistente['idserviciocontratado'];
            }
        }

        // Obtener información del servicio
        $servicio = $this->equipoModel->getServicioInfo($servicioId);
        if (!$servicio) {
            session()->setFlashdata('error', 'Servicio no encontrado.');
            return redirect()->to('equipos');
        }

        // Validar asignación usando el servicio
        $validacion = $this->equipoService->validarAsignacion(
            $usuarioId, 
            $servicioId, 
            $servicio['fechahoraservicio'], 
            $equipoId
        );

        if (!$validacion['valido']) {
            foreach ($validacion['errores'] as $error) {
                session()->setFlashdata('error', $error);
            }
            return redirect()->back()->withInput();
        }

        // Preparar datos
        $data = [
            'idusuario' => $usuarioId,
            'descripcion' => $descripcion,
            'estadoservicio' => $estado
        ];

        // Guardar o actualizar
        try {
            if ($equipoId) {
                // Actualizar
                $success = $this->equipoModel->update($equipoId, $data);
                $mensaje = $success ? 'Asignación actualizada correctamente' : 'Error al actualizar';
            } else {
                // Crear nuevo
                $data['idserviciocontratado'] = $servicioId;
                $success = $this->equipoModel->insert($data);
                $mensaje = $success ? 'Técnico asignado correctamente' : 'Error al asignar técnico';
            }

            session()->setFlashdata($success ? 'success' : 'error', $mensaje);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en saveEquipo: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error interno del sistema');
        }

        // Regresar al servicio específico si existe, sino a la vista general
        if ($servicioId) {
            return redirect()->to('equipos/porServicio/' . $servicioId);
        }
        return redirect()->to('equipos');
    }

    /**
     * Vista para editar asignación de equipo
     * KISS: método simple y claro
     */
    public function editar(int $idequipo)
    {
        $equipo = $this->equipoModel->getEquipoConDetalles($idequipo);
        
        if (!$equipo) {
            session()->setFlashdata('error', 'Equipo no encontrado');
            return redirect()->to('equipos');
        }

        $data = [
            'titulo' => 'Editar Asignación de Equipo',
            'equipo' => $equipo,
            'tecnicos' => $this->equipoService->obtenerTecnicosDisponibles(
                $equipo['idserviciocontratado'], 
                $equipo['fechahoraservicio']
            )
        ];
        
        return $this->render('equipos/editar', $data);
    }

    /**
     * Vistas filtradas por servicio o usuario
     * KISS: métodos simples para vistas específicas
     */
    public function porServicio(int $servicioId)
    {
        $servicio = $this->equipoModel->getServicioInfo($servicioId);
        
        if (!$servicio) {
            session()->setFlashdata('error', 'Servicio no encontrado');
            return redirect()->to('equipos');
        }

        $data = [
            'titulo' => 'Equipos del Servicio: ' . $servicio['servicio'],
            'equiposKanban' => $this->equipoModel->getEquiposParaKanban($servicioId),
            'servicio' => $servicio
        ];
        
        return $this->render('equipos/listar', $data);
    }

    public function porUsuario(int $usuarioId): string
    {
        $equipos = $this->equipoModel->getEquiposPorUsuario($usuarioId);
        
        $data = [
            'titulo' => 'Mis Asignaciones',
            'equipos' => $equipos
        ];
        
        return $this->render('equipos/listar', $data);
    }

    /**
     * Endpoint AJAX para actualizar estado de equipos
     * KISS: delegación completa al servicio
     */
    public function actualizarEstado(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Log para debugging
        log_message('info', 'Método actualizarEstado llamado');
        
        if (!$this->request->isAJAX()) {
            log_message('error', 'Petición no es AJAX');
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Petición no válida']);
        }

        $input = json_decode($this->request->getBody(), true);
        log_message('info', 'Input recibido: ' . json_encode($input));
        
        $equipoId = (int)($input['id'] ?? 0);
        $nuevoEstado = $input['estado'] ?? '';

        if (!$equipoId || !$nuevoEstado) {
            log_message('error', 'Parámetros faltantes - ID: ' . $equipoId . ', Estado: ' . $nuevoEstado);
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Parámetros faltantes'
            ]);
        }

        try {
            // Delegar al servicio
            $resultado = $this->equipoService->actualizarEstado($equipoId, $nuevoEstado);
            log_message('info', 'Resultado del servicio: ' . json_encode($resultado));
            
            return $this->response->setJSON([
                'success' => $resultado['success'],
                'message' => $resultado['message']
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en actualizarEstado: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }
}