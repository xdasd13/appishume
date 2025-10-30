<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseConnection;

/**
 * Controlador de Búsqueda Global
 * 
 * Realiza búsquedas en todo el sistema: clientes, servicios, equipos, 
 * pagos, entregas, usuarios, cotizaciones, etc.
 * 
 * @author ISHUME Team
 * @version 1.0
 */
class BuscadorGlobal extends BaseController
{
    private BaseConnection $db;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Buscar en todo el sistema
     * 
     * Recibe un término de búsqueda y retorna resultados de todas las tablas
     * relevantes del sistema.
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON con resultados
     */
    public function buscar()
    {
        try {
            // Obtener término de búsqueda
            $termino = $this->request->getPost('termino');
            
            // Log para debugging
            log_message('info', 'Búsqueda recibida: ' . $termino);
            
            // Validar que el término tenga al menos 3 caracteres
            if (empty($termino) || strlen($termino) < 3) {
                return $this->response->setJSON([
                    'success' => true,
                    'resultados' => [],
                    'mensaje' => 'Ingrese al menos 3 caracteres',
                    'total' => 0
                ]);
            }

            // Buscar en todas las secciones del sistema
            $resultados = [
                'modulos' => $this->buscarModulos($termino),
                'clientes' => $this->buscarClientes($termino),
                'servicios' => $this->buscarServicios($termino),
                'equipos' => $this->buscarEquipos($termino),
                'pagos' => $this->buscarPagos($termino),
                'entregas' => $this->buscarEntregas($termino),
                'usuarios' => $this->buscarUsuarios($termino),
                'cotizaciones' => $this->buscarCotizaciones($termino)
            ];

            // Contar total de resultados
            $total = array_sum(array_map('count', $resultados));

            return $this->response->setJSON([
                'success' => true,
                'resultados' => $resultados,
                'total' => $total,
                'termino' => $termino
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en búsqueda global: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'mensaje' => 'Error al realizar la búsqueda: ' . $e->getMessage(),
                'error_detalle' => $e->getMessage()
            ]);
        }
    }

    /**
     * Buscar en módulos y rutas del sistema
     */
    private function buscarModulos(string $termino): array
    {
        // Definir todos los módulos del sistema
        $modulos = [
            [
                'nombre' => 'Cronograma',
                'descripcion' => 'Gestión de proyectos y cronograma de eventos',
                'url' => 'cronograma',
                'icono' => 'fas fa-calendar-alt',
                'keywords' => ['cronograma', 'calendario', 'eventos', 'proyectos', 'fechas', 'programación']
            ],
            [
                'nombre' => 'Historial de Actividades',
                'descripcion' => 'Registro de cambios en el tablero Kanban',
                'url' => 'historial',
                'icono' => 'fas fa-history',
                'keywords' => ['historial', 'actividades', 'registro', 'cambios', 'auditoría', 'log']
            ],
            [
                'nombre' => 'Equipos de Trabajo',
                'descripcion' => 'Gestión de equipos y asignaciones',
                'url' => 'equipos',
                'icono' => 'fas fa-users-cog',
                'keywords' => ['equipos', 'trabajo', 'asignaciones', 'kanban', 'tablero', 'tareas']
            ],
            [
                'nombre' => 'Control de Pagos',
                'descripcion' => 'Gestión de pagos y transacciones',
                'url' => 'controlpagos',
                'icono' => 'fas fa-money-bill-wave',
                'keywords' => ['pagos', 'transacciones', 'finanzas', 'dinero', 'cobros', 'voucher']
            ],
            [
                'nombre' => 'Entregas',
                'descripcion' => 'Gestión de entregables y seguimiento',
                'url' => 'entregas',
                'icono' => 'fas fa-box-open',
                'keywords' => ['entregas', 'entregables', 'seguimiento', 'delivery', 'productos']
            ],
            [
                'nombre' => 'Clientes',
                'descripcion' => 'Gestión de clientes y empresas',
                'url' => 'clientes',
                'icono' => 'fas fa-users',
                'keywords' => ['clientes', 'personas', 'empresas', 'contactos', 'directorio']
            ],
            [
                'nombre' => 'Servicios',
                'descripcion' => 'Catálogo de servicios disponibles',
                'url' => 'servicios',
                'icono' => 'fas fa-concierge-bell',
                'keywords' => ['servicios', 'catálogo', 'productos', 'ofertas', 'paquetes']
            ],
            [
                'nombre' => 'Usuarios',
                'descripcion' => 'Gestión de usuarios del sistema',
                'url' => 'usuarios',
                'icono' => 'fas fa-user-tie',
                'keywords' => ['usuarios', 'personal', 'trabajadores', 'empleados', 'staff', 'equipo']
            ],
            [
                'nombre' => 'Cotizaciones',
                'descripcion' => 'Gestión de cotizaciones y presupuestos',
                'url' => 'cotizaciones',
                'icono' => 'fas fa-file-invoice-dollar',
                'keywords' => ['cotizaciones', 'presupuestos', 'propuestas', 'precios', 'estimaciones']
            ],
            [
                'nombre' => 'Contratos',
                'descripcion' => 'Gestión de contratos y acuerdos',
                'url' => 'contratos',
                'icono' => 'fas fa-file-contract',
                'keywords' => ['contratos', 'acuerdos', 'documentos', 'legal', 'convenios']
            ],
            [
                'nombre' => 'Inventario',
                'descripcion' => 'Control de inventario y stock',
                'url' => 'inventario',
                'icono' => 'fas fa-warehouse',
                'keywords' => ['inventario', 'stock', 'almacén', 'productos', 'existencias']
            ],
            [
                'nombre' => 'Dashboard',
                'descripcion' => 'Panel principal del sistema',
                'url' => 'Home',
                'icono' => 'fas fa-home',
                'keywords' => ['dashboard', 'inicio', 'home', 'principal', 'panel']
            ]
        ];

        // Filtrar módulos que coincidan con el término de búsqueda
        $resultados = [];
        $terminoLower = strtolower($termino);

        foreach ($modulos as $modulo) {
            // Buscar en nombre, descripción y keywords
            $coincide = false;
            
            if (stripos($modulo['nombre'], $termino) !== false) {
                $coincide = true;
            } elseif (stripos($modulo['descripcion'], $termino) !== false) {
                $coincide = true;
            } else {
                foreach ($modulo['keywords'] as $keyword) {
                    if (stripos($keyword, $termino) !== false) {
                        $coincide = true;
                        break;
                    }
                }
            }

            if ($coincide) {
                $resultados[] = [
                    'id' => $modulo['url'],
                    'titulo' => $modulo['nombre'],
                    'subtitulo' => $modulo['descripcion'],
                    'icono' => $modulo['icono'],
                    'url' => base_url($modulo['url']),
                    'categoria' => 'Módulos del Sistema'
                ];
            }
        }

        return $resultados;
    }

    /**
     * Buscar en clientes (personas y empresas)
     */
    private function buscarClientes(string $termino): array
    {
        $builder = $this->db->table('clientes cl');
        
        $builder->select('cl.idcliente, CASE WHEN p.idpersona IS NOT NULL THEN CONCAT(p.apellidos, ", ", p.nombres) WHEN e.idempresa IS NOT NULL THEN e.razonsocial END as nombre, CASE WHEN p.idpersona IS NOT NULL THEN p.numerodoc WHEN e.idempresa IS NOT NULL THEN e.ruc END as documento, CASE WHEN p.idpersona IS NOT NULL THEN "Persona" WHEN e.idempresa IS NOT NULL THEN "Empresa" END as tipo', false);
        
        $builder->join('personas p', 'cl.idpersona = p.idpersona', 'left');
        $builder->join('empresas e', 'cl.idempresa = e.idempresa', 'left');
        
        $builder->groupStart()
            ->like('p.nombres', $termino)
            ->orLike('p.apellidos', $termino)
            ->orLike('p.numerodoc', $termino)
            ->orLike('e.razonsocial', $termino)
            ->orLike('e.ruc', $termino)
        ->groupEnd();
        
        $builder->limit(5);
        
        $resultados = $builder->get()->getResultArray();
        
        return array_map(function($item) {
            return [
                'id' => $item['idcliente'],
                'titulo' => $item['nombre'],
                'subtitulo' => $item['tipo'] . ' - ' . $item['documento'],
                'icono' => 'fas fa-user',
                'url' => base_url('clientes/ver/' . $item['idcliente']),
                'categoria' => 'Clientes'
            ];
        }, $resultados);
    }

    /**
     * Buscar en servicios
     */
    private function buscarServicios(string $termino): array
    {
        $builder = $this->db->table('servicios s');
        
        $builder->select('s.idservicio, s.servicio, s.descripcion, c.categoria, s.precioregular', false);
        
        $builder->join('categorias c', 's.idcategoria = c.idcategoria');
        
        $builder->groupStart()
            ->like('s.servicio', $termino)
            ->orLike('s.descripcion', $termino)
            ->orLike('c.categoria', $termino)
        ->groupEnd();
        
        $builder->limit(5);
        
        $resultados = $builder->get()->getResultArray();
        
        return array_map(function($item) {
            return [
                'id' => $item['idservicio'],
                'titulo' => $item['servicio'],
                'subtitulo' => $item['categoria'] . ' - S/ ' . number_format($item['precioregular'], 2),
                'icono' => 'fas fa-concierge-bell',
                'url' => base_url('servicios/ver/' . $item['idservicio']),
                'categoria' => 'Servicios'
            ];
        }, $resultados);
    }

    /**
     * Buscar en equipos
     */
    private function buscarEquipos(string $termino): array
    {
        $builder = $this->db->table('equipos eq');
        
        $builder->select('eq.idequipo, eq.descripcion, eq.estadoservicio, s.servicio, CONCAT(p.nombres, " ", p.apellidos) as usuario', false);
        
        $builder->join('servicioscontratados sc', 'eq.idserviciocontratado = sc.idserviciocontratado');
        $builder->join('servicios s', 'sc.idservicio = s.idservicio');
        $builder->join('usuarios u', 'eq.idusuario = u.idusuario');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        
        $builder->groupStart()
            ->like('eq.descripcion', $termino)
            ->orLike('s.servicio', $termino)
            ->orLike('eq.estadoservicio', $termino)
        ->groupEnd();
        
        $builder->limit(5);
        
        $resultados = $builder->get()->getResultArray();
        
        return array_map(function($item) {
            return [
                'id' => $item['idequipo'],
                'titulo' => $item['descripcion'],
                'subtitulo' => $item['servicio'] . ' - ' . $item['estadoservicio'],
                'icono' => 'fas fa-tools',
                'url' => base_url('equipos'),
                'categoria' => 'Equipos'
            ];
        }, $resultados);
    }

    /**
     * Buscar en pagos
     */
    private function buscarPagos(string $termino): array
    {
        $builder = $this->db->table('controlpagos cp');
        
        $builder->select('cp.idpagos, cp.numtransaccion, cp.amortizacion, cp.fechahora, tp.tipopago, CASE WHEN p.idpersona IS NOT NULL THEN CONCAT(p.apellidos, ", ", p.nombres) WHEN e.idempresa IS NOT NULL THEN e.razonsocial END as cliente', false);
        
        $builder->join('contratos ct', 'cp.idcontrato = ct.idcontrato');
        $builder->join('clientes cl', 'ct.idcliente = cl.idcliente');
        $builder->join('personas p', 'cl.idpersona = p.idpersona', 'left');
        $builder->join('empresas e', 'cl.idempresa = e.idempresa', 'left');
        $builder->join('tipospago tp', 'cp.idtipopago = tp.idtipopago');
        
        $builder->groupStart()
            ->like('cp.numtransaccion', $termino)
            ->orLike('tp.tipopago', $termino)
            ->orLike('p.nombres', $termino)
            ->orLike('p.apellidos', $termino)
            ->orLike('e.razonsocial', $termino)
        ->groupEnd();
        
        $builder->limit(5);
        
        $resultados = $builder->get()->getResultArray();
        
        return array_map(function($item) {
            return [
                'id' => $item['idpagos'],
                'titulo' => 'Pago - ' . $item['numtransaccion'],
                'subtitulo' => $item['cliente'] . ' - S/ ' . number_format($item['amortizacion'], 2),
                'icono' => 'fas fa-money-bill-wave',
                'url' => base_url('controlpagos'),
                'categoria' => 'Pagos'
            ];
        }, $resultados);
    }

    /**
     * Buscar en entregas
     */
    private function buscarEntregas(string $termino): array
    {
        $builder = $this->db->table('entregables en');
        
        $builder->select('en.identregable, en.observaciones, en.estado, en.fechahoraentrega, s.servicio, CONCAT(p.nombres, " ", p.apellidos) as responsable', false);
        
        $builder->join('servicioscontratados sc', 'en.idserviciocontratado = sc.idserviciocontratado');
        $builder->join('servicios s', 'sc.idservicio = s.idservicio');
        $builder->join('personas p', 'en.idpersona = p.idpersona');
        
        $builder->groupStart()
            ->like('en.observaciones', $termino)
            ->orLike('s.servicio', $termino)
            ->orLike('en.estado', $termino)
        ->groupEnd();
        
        $builder->limit(5);
        
        $resultados = $builder->get()->getResultArray();
        
        return array_map(function($item) {
            return [
                'id' => $item['identregable'],
                'titulo' => 'Entrega - ' . $item['servicio'],
                'subtitulo' => $item['responsable'] . ' - ' . ucfirst($item['estado']),
                'icono' => 'fas fa-box',
                'url' => base_url('entregas/ver/' . $item['identregable']),
                'categoria' => 'Entregas'
            ];
        }, $resultados);
    }

    /**
     * Buscar en usuarios
     */
    private function buscarUsuarios(string $termino): array
    {
        $builder = $this->db->table('usuarios u');
        
        $builder->select('u.idusuario, u.nombreusuario, u.email, CONCAT(p.nombres, " ", p.apellidos) as nombre_completo, c.cargo', false);
        
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('cargos c', 'u.idcargo = c.idcargo');
        
        $builder->groupStart()
            ->like('u.nombreusuario', $termino)
            ->orLike('u.email', $termino)
            ->orLike('p.nombres', $termino)
            ->orLike('p.apellidos', $termino)
            ->orLike('c.cargo', $termino)
        ->groupEnd();
        
        $builder->where('u.estado', 1);
        $builder->limit(5);
        
        $resultados = $builder->get()->getResultArray();
        
        return array_map(function($item) {
            return [
                'id' => $item['idusuario'],
                'titulo' => $item['nombre_completo'],
                'subtitulo' => $item['cargo'] . ' - ' . $item['email'],
                'icono' => 'fas fa-user-tie',
                'url' => base_url('usuarios'),
                'categoria' => 'Usuarios'
            ];
        }, $resultados);
    }

    /**
     * Buscar en cotizaciones
     */
    private function buscarCotizaciones(string $termino): array
    {
        $builder = $this->db->table('cotizaciones cot');
        
        $builder->select('cot.idcotizacion, cot.fechacotizacion, cot.fechaevento, te.evento, CASE WHEN p.idpersona IS NOT NULL THEN CONCAT(p.apellidos, ", ", p.nombres) WHEN e.idempresa IS NOT NULL THEN e.razonsocial END as cliente', false);
        
        $builder->join('clientes cl', 'cot.idcliente = cl.idcliente');
        $builder->join('personas p', 'cl.idpersona = p.idpersona', 'left');
        $builder->join('empresas e', 'cl.idempresa = e.idempresa', 'left');
        $builder->join('tipoeventos te', 'cot.idtipoevento = te.idtipoevento');
        
        $builder->groupStart()
            ->like('te.evento', $termino)
            ->orLike('p.nombres', $termino)
            ->orLike('p.apellidos', $termino)
            ->orLike('e.razonsocial', $termino)
            ->orLike('cot.idcotizacion', $termino)
        ->groupEnd();
        
        $builder->limit(5);
        
        $resultados = $builder->get()->getResultArray();
        
        return array_map(function($item) {
            return [
                'id' => $item['idcotizacion'],
                'titulo' => 'Cotización #' . $item['idcotizacion'] . ' - ' . $item['evento'],
                'subtitulo' => $item['cliente'] . ' - ' . date('d/m/Y', strtotime($item['fechaevento'])),
                'icono' => 'fas fa-file-invoice',
                'url' => base_url('cotizaciones/ver/' . $item['idcotizacion']),
                'categoria' => 'Cotizaciones'
            ];
        }, $resultados);
    }
}
