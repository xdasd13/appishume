<?php

namespace App\Controllers;

use App\Models\CronogramaModel;
use App\Models\ServicioModel;
use App\Models\EquipoModel;
use CodeIgniter\Controller;

class Cronograma extends BaseController
{
    protected $cronogramaModel;
    protected $servicioModel;
    protected $equipoModel;
    protected $tecnicoModel;

    

    /**
     * Vista principal del cronograma
     */
    public function index()
    {
        // Verificar autenticación
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login');
        }

        try {
            $data = [
                // Obtener estadísticas para los cards
                'servicios_count' => $this->cronogramaModel->contarServiciosActivos(),
                'equipos' => $this->cronogramaModel->contarEquiposAsignados(),
                'tecnicos' => $this->cronogramaModel->contarTecnicosDisponibles(),
                
                // Obtener próximos servicios
                'proximos' => $this->cronogramaModel->obtenerProximosServicios(),
                
                // Cargar header y footer
                'header' => view('templates/header', ['titulo' => 'Cronograma de Servicios']),
                'footer' => view('templates/footer')
            ];

            return view('cronograma/index', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en cronograma/index: ' . $e->getMessage());
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Error al cargar el cronograma: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint para obtener eventos del calendario (AJAX)
     */
    public function getEventos()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Solo peticiones AJAX']);
        }

        try {
            // Obtener parámetros del calendario
            $start = $this->request->getGet('start');
            $end = $this->request->getGet('end');
            
            // Obtener eventos del modelo para el calendario
            $eventos = $this->cronogramaModel->obtenerEventosCalendario($start, $end);
            
            return $this->response->setJSON($eventos);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en getEventos: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al obtener eventos']);
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
     * Obtener servicios por fecha (AJAX)
     */
    public function serviciosPorFecha($fecha = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Solo peticiones AJAX']);
        }

        if (!$fecha) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Fecha requerida']);
        }

        try {
            $servicios = $this->cronogramaModel->obtenerServiciosPorFecha($fecha);
            return $this->response->setJSON($servicios);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en serviciosPorFecha: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al obtener servicios']);
        }
    }

    /**
     * Actualizar estado de servicio (AJAX)
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
     * Obtener resumen semanal
     */
    public function resumenSemanal()
    {
        try {
            $resumen = $this->cronogramaModel->obtenerResumenSemanal();
            return $this->response->setJSON($resumen);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en resumenSemanal: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al obtener resumen']);
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
            'header' => view('templates/header', ['titulo' => 'Configuración del Cronograma']),
            'footer' => view('templates/footer')
        ];

        return view('cronograma/configuracion', $data);
    }
}