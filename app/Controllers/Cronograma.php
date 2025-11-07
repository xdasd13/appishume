<?php
namespace App\Controllers;

use App\Models\CronogramaModel;
use App\Models\ProyectoModel;
use Config\Database;

class Cronograma extends BaseController
{
    protected $cronogramaModel;
    protected $proyectoModel;
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function index()
    {
        try {
            if (!isset($this->cronogramaModel)) {
                $this->cronogramaModel = new CronogramaModel();
            }

            $estadisticas = $this->cronogramaModel->getEstadisticas();
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

    public function getEventos()
    {
        try {
            if (!isset($this->cronogramaModel)) {
                $this->cronogramaModel = new CronogramaModel();
            }

            $start = $this->request->getGet('start');
            $end = $this->request->getGet('end');
            $eventos = $this->cronogramaModel->getEventosCalendario($start, $end);

            return $this->response->setJSON($eventos);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en getEventos: ' . $e->getMessage());
            log_message('error', 'Error trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al obtener eventos']);
        }
    }

    public function serviciosPorFecha($fecha)
    {
        try {
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

    public function resumenSemanal()
    {
        try {
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

    public function crearServicio()
    {
        $fecha = $this->request->getGet('fecha');
        
        if ($fecha) {
            session()->setFlashdata('fecha_preseleccionada', $fecha);
        }
        
        return redirect()->to('/servicios/crear');
    }


    public function proyectos()
    {
        try {
            if (!isset($this->proyectoModel)) {
                $this->proyectoModel = new ProyectoModel();
            }

            $proyectos = $this->proyectoModel->getProyectosAgrupadosPorCliente();

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

    public function todosLosProyectos()
    {
        try {
            if (!isset($this->proyectoModel)) {
                $this->proyectoModel = new ProyectoModel();
            }

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

    public function verProyecto($idserviciocontratado)
    {
        try {
            if (!isset($this->proyectoModel)) {
                $this->proyectoModel = new ProyectoModel();
            }

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

    public function proyectosPorEstado($estado)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Solo peticiones AJAX']);
        }

        try {
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