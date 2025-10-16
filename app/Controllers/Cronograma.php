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
     * Debug: Verificar consulta SQL directamente
     */
    public function debugProyectos()
    {
        if (!isset($this->proyectoModel)) {
            $this->proyectoModel = new ProyectoModel();
        }

        // Probar consulta directa
        $db = \Config\Database::connect();
        $query = "
            SELECT 
                c.idcliente,
                CASE 
                    WHEN c.idempresa IS NOT NULL THEN e.razonsocial
                    ELSE CONCAT(p.nombres, ' ', p.apellidos)
                END as cliente,
                sc.idserviciocontratado,
                s.servicio,
                sc.fechahoraservicio
            FROM servicioscontratados sc
            INNER JOIN cotizaciones cot ON sc.idcotizacion = cot.idcotizacion
            INNER JOIN contratos con ON cot.idcotizacion = con.idcotizacion
            INNER JOIN clientes c ON cot.idcliente = c.idcliente
            LEFT JOIN personas p ON c.idpersona = p.idpersona
            LEFT JOIN empresas e ON c.idempresa = e.idempresa
            INNER JOIN servicios s ON sc.idservicio = s.idservicio
            LIMIT 10
        ";
        
        $result = $db->query($query)->getResult();
        
        echo "<h2>Debug de Proyectos</h2>";
        echo "<h3>Total registros encontrados: " . count($result) . "</h3>";
        echo "<pre>";
        print_r($result);
        echo "</pre>";
        
        echo "<hr>";
        echo "<h3>Ejecutando consulta completa directamente:</h3>";
        
        // Ejecutar la consulta completa manualmente
        $queryCompleta = "
            SELECT 
                c.idcliente,
                CASE 
                    WHEN c.idempresa IS NOT NULL THEN e.razonsocial
                    ELSE CONCAT(p.nombres, ' ', p.apellidos)
                END as cliente,
                CASE 
                    WHEN c.idempresa IS NOT NULL THEN e.telefono
                    ELSE p.telprincipal
                END as telefono_cliente,
                sc.idserviciocontratado,
                s.servicio,
                sc.fechahoraservicio,
                sc.direccion,
                COALESCE(eq.estadoservicio, 'Pendiente') as estado,
                CASE 
                    WHEN COALESCE(eq.estadoservicio, 'Pendiente') = 'Completado' THEN 100
                    WHEN COALESCE(eq.estadoservicio, 'Pendiente') = 'En Proceso' THEN 65
                    WHEN COALESCE(eq.estadoservicio, 'Pendiente') = 'Programado' THEN 35
                    ELSE 10
                END as progreso,
                cot.fechaevento,
                te.evento as tipoevento
            FROM servicioscontratados sc
            INNER JOIN cotizaciones cot ON sc.idcotizacion = cot.idcotizacion
            INNER JOIN clientes c ON cot.idcliente = c.idcliente
            LEFT JOIN personas p ON c.idpersona = p.idpersona
            LEFT JOIN empresas e ON c.idempresa = e.idempresa
            INNER JOIN servicios s ON sc.idservicio = s.idservicio
            LEFT JOIN equipos eq ON sc.idserviciocontratado = eq.idserviciocontratado
            LEFT JOIN tipoeventos te ON cot.idtipoevento = te.idtipoevento
            ORDER BY c.idcliente, sc.fechahoraservicio ASC
        ";
        
        $resultCompleto = $db->query($queryCompleta)->getResult();
        echo "<h4>Total registros consulta completa: " . count($resultCompleto) . "</h4>";
        echo "<pre>";
        print_r($resultCompleto);
        echo "</pre>";
        
        echo "<hr>";
        echo "<h3>Resultado del método getProyectosAgrupadosPorCliente():</h3>";
        $proyectos = $this->proyectoModel->getProyectosAgrupadosPorCliente();
        echo "<h4>Total clientes agrupados: " . count($proyectos) . "</h4>";
        echo "<pre>";
        print_r($proyectos);
        echo "</pre>";
        
        die();
    }

    /**
     * Vista de proyectos activos agrupados por cliente
     */
    public function proyectos()
    {
        try {
            // Inicializar el modelo si no existe
            if (!isset($this->proyectoModel)) {
                $this->proyectoModel = new ProyectoModel();
            }

            // Obtener proyectos agrupados por cliente
            $proyectos = $this->proyectoModel->getProyectosAgrupadosPorCliente();
            
            // Debug: Verificar si hay proyectos
            log_message('info', 'Número de clientes con proyectos: ' . count($proyectos));
            
            // Debug adicional: Mostrar estructura de datos
            if (!empty($proyectos)) {
                log_message('info', 'Primer proyecto: ' . json_encode($proyectos[0]));
            } else {
                log_message('warning', 'No se encontraron proyectos agrupados');
                // Intentar con método antiguo para verificar si hay datos
                $proyectosAntiguos = $this->proyectoModel->getProyectosActivos();
                log_message('info', 'Proyectos método antiguo: ' . count($proyectosAntiguos));
            }

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
