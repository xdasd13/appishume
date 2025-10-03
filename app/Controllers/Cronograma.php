<?php

namespace App\Controllers;

use App\Models\CronogramaModel;
use App\Models\ServicioModel;
use App\Models\EquipoModel;
use App\Models\ProyectoModel;
use CodeIgniter\Controller;
use Config\Database;

class Cronograma extends BaseController
{
    protected $cronogramaModel;
    protected $servicioModel;
    protected $equipoModel;
    protected $tecnicoModel;
    protected $proyectoModel;
    protected $db;

    public function __construct()
    {
        // Inicializar conexión a la base de datos
        $this->db = Database::connect();
    }

    /**
     * Vista principal del cronograma
     */
    public function index()
    {
        try {
            // Inicializar el modelo de cronograma
            if (!isset($this->cronogramaModel)) {
                $this->cronogramaModel = new CronogramaModel();
            }

            // Obtener estadísticas
            $estadisticas = $this->cronogramaModel->getEstadisticas();
            
            // Obtener próximos servicios
            $proximosServicios = $this->cronogramaModel->getProximosServicios(10);

            $data = [
                'header' => view('Layouts/header', ['titulo' => 'Cronograma']),
                'footer' => view('Layouts/footer'),
                'servicios_count' => $estadisticas['servicios_count'],
                'equipos' => $estadisticas['equipos'],
                'tecnicos' => $estadisticas['tecnicos'],
                'proximos' => $proximosServicios
            ];

            return view('cronograma/index', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en cronograma/index: ' . $e->getMessage());
            
            // Datos por defecto en caso de error
            $data = [
                'header' => view('Layouts/header', ['titulo' => 'Cronograma']),
                'footer' => view('Layouts/footer'),
                'servicios_count' => 0,
                'equipos' => 0,
                'tecnicos' => 0,
                'proximos' => []
            ];

            return view('cronograma/index', $data);
        }
    }

    /**
     * API endpoint para obtener eventos del calendario (AJAX)
     */
    public function getEventos()
    {
        try {
            // Inicializar el modelo de cronograma
            if (!isset($this->cronogramaModel)) {
                $this->cronogramaModel = new CronogramaModel();
            }

            // Obtener parámetros de fecha del calendario
            $start = $this->request->getGet('start');
            $end = $this->request->getGet('end');

            log_message('info', "getEventos - Parámetros: start=$start, end=$end");

            // Obtener eventos para el calendario
            $eventos = $this->cronogramaModel->getEventosCalendario($start, $end);

            log_message('info', "getEventos - Eventos encontrados: " . count($eventos));
            log_message('info', "getEventos - Datos: " . json_encode($eventos));

            return $this->response->setJSON($eventos);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en getEventos: ' . $e->getMessage());
            log_message('error', 'Error trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al obtener eventos']);
        }
    }

    /**
     * API endpoint para obtener servicios por fecha (AJAX)
     */
    public function serviciosPorFecha($fecha)
    {
        try {
            // Inicializar el modelo de cronograma
            if (!isset($this->cronogramaModel)) {
                $this->cronogramaModel = new CronogramaModel();
            }

            $servicios = $this->cronogramaModel->getServiciosPorFecha($fecha);
            return $this->response->setJSON($servicios);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en serviciosPorFecha: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al obtener servicios']);
        }
    }

    /**
     * API endpoint para obtener resumen semanal (AJAX)
     */
    public function resumenSemanal()
    {
        try {
            // Inicializar el modelo de cronograma
            if (!isset($this->cronogramaModel)) {
                $this->cronogramaModel = new CronogramaModel();
            }

            $resumen = $this->cronogramaModel->getResumenSemanal();
            return $this->response->setJSON($resumen);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en resumenSemanal: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al obtener resumen']);
        }
    }

    /**
     * Actualizar estado de un servicio (AJAX)
     */
    public function actualizarEstado()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Solo peticiones AJAX']);
        }

        $json = $this->request->getJSON(true);
        
        if (!isset($json['id']) || !isset($json['estado'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Datos incompletos']);
        }

        try {
            // Inicializar el modelo de cronograma
            if (!isset($this->cronogramaModel)) {
                $this->cronogramaModel = new CronogramaModel();
            }

            $resultado = $this->cronogramaModel->actualizarEstadoServicio($json['id'], $json['estado']);
            
            if ($resultado) {
                return $this->response->setJSON(['success' => true, 'message' => 'Estado actualizado correctamente']);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al actualizar estado']);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error en actualizarEstado: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al actualizar estado']);
        }
    }

    /**
     * Crear nuevo servicio desde calendario
     */
    public function crearServicio()
    {
        $fecha = $this->request->getGet('fecha');
        
        if ($fecha) {
            session()->setFlashdata('fecha_preseleccionada', $fecha);
        }
        
        return redirect()->to('/servicios/crear');
    }

    /**
     * Vista de proyectos activos
     */
    public function proyectos()
    {
        try {
            // Inicializar el modelo si no existe
            if (!isset($this->proyectoModel)) {
                $this->proyectoModel = new ProyectoModel();
            }

            // Primero verificar la conexión y datos básicos
            $testConexion = $this->proyectoModel->testConexion();
            log_message('info', 'Test conexión: ' . json_encode($testConexion));
            
            // Obtener proyectos activos
            $proyectos = $this->proyectoModel->getProyectosActivos();
            
            // Debug: Verificar si hay proyectos
            log_message('info', 'Número de proyectos encontrados: ' . count($proyectos));

            $data = [
                'proyectos' => $proyectos,
                'titulo' => 'Proyectos Activos',
                'header' => view('Layouts/header', ['titulo' => 'Proyectos Activos']),
                'footer' => view('Layouts/footer')
            ];

            return view('cronograma/proyectos', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en cronograma/proyectos: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error al cargar los proyectos: ' . $e->getMessage());
            return redirect()->to('/cronograma');
        }
    }

    /**
     * Vista de todos los proyectos (activos e inactivos)
     */
    public function todosLosProyectos()
    {
        try {
            // Inicializar el modelo si no existe
            if (!isset($this->proyectoModel)) {
                $this->proyectoModel = new ProyectoModel();
            }

            // Obtener todos los proyectos
            $proyectos = $this->proyectoModel->getTodosLosProyectos();
            $estadisticas = $this->proyectoModel->getEstadisticasProyectos();

            $data = [
                'proyectos' => $proyectos,
                'estadisticas' => $estadisticas,
                'titulo' => 'Todos los Proyectos',
                'header' => view('Layouts/header', ['titulo' => 'Todos los Proyectos']),
                'footer' => view('Layouts/footer')
            ];

            return view('cronograma/todos_proyectos', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en cronograma/todosLosProyectos: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error al cargar los proyectos: ' . $e->getMessage());
            return redirect()->to('/cronograma');
        }
    }

    /**
     * Ver detalle de un proyecto específico
     */
    public function verProyecto($idserviciocontratado)
    {
        try {
            // Inicializar el modelo si no existe
            if (!isset($this->proyectoModel)) {
                $this->proyectoModel = new ProyectoModel();
            }

            // Obtener proyecto específico
            $proyecto = $this->proyectoModel->getProyectoPorId($idserviciocontratado);

            if (!$proyecto) {
                session()->setFlashdata('error', 'Proyecto no encontrado');
                return redirect()->to('/cronograma/proyectos');
            }

            $data = [
                'proyecto' => $proyecto,
                'titulo' => 'Detalle del Proyecto',
                'header' => view('Layouts/header', ['titulo' => 'Detalle del Proyecto']),
                'footer' => view('Layouts/footer')
            ];

            return view('cronograma/detalle_proyecto', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en cronograma/verProyecto: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error al cargar el proyecto: ' . $e->getMessage());
            return redirect()->to('/cronograma/proyectos');
        }
    }

    /**
     * API para obtener proyectos por estado (AJAX)
     */
    public function proyectosPorEstado($estado)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Solo peticiones AJAX']);
        }

        try {
            // Inicializar el modelo si no existe
            if (!isset($this->proyectoModel)) {
                $this->proyectoModel = new ProyectoModel();
            }

            $proyectos = $this->proyectoModel->getProyectosPorEstado($estado);
            return $this->response->setJSON($proyectos);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en proyectosPorEstado: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al obtener proyectos']);
        }
    }


    /**
     * Vista de configuración del cronograma
     */
    public function configuracion()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'header' => view('Layouts/header', ['titulo' => 'Configuración del Cronograma']),
            'footer' => view('Layouts/footer')
        ];

        return view('cronograma/configuracion', $data);
    }

}
