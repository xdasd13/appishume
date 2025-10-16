<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ContratoModel;
use App\Models\ControlPagoModel;
use App\Models\EntregasModel;
use App\Models\EquipoModel;
use App\Models\PersonaModel;
use App\Models\ServicioModel;
use App\Models\CronogramaModel;

class ReportesController extends BaseController
{
    protected $contratoModel;
    protected $controlPagoModel;
    protected $entregasModel;
    protected $equipoModel;
    protected $personaModel;
    protected $servicioModel;
    protected $cronogramaModel;
    protected $db;

    public function __construct()
    {
        $this->contratoModel = new ContratoModel();
        $this->controlPagoModel = new ControlPagoModel();
        $this->entregasModel = new EntregasModel();
        $this->equipoModel = new EquipoModel();
        $this->personaModel = new PersonaModel();
        $this->servicioModel = new ServicioModel();
        $this->cronogramaModel = new CronogramaModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Vista principal de reportes con interfaz interactiva
     */
    public function index()
    {
        $data = [
            'title' => 'Reportes Dinámicos - IShume',
            'reportes_disponibles' => $this->obtenerReportesDisponibles(),
            'filtros_base' => $this->obtenerFiltrosBase()
        ];

        return view('reportes/index', $data);
    }

    /**
     * Generar reporte dinámico basado en parámetros
     */
    public function generar()
    {
        $tipo_reporte = $this->request->getPost('tipo_reporte');
        $filtros = $this->request->getPost('filtros');
        $formato = $this->request->getPost('formato') ?? 'html';

        if (!$tipo_reporte) {
            return $this->response->setJSON(['error' => 'Tipo de reporte requerido']);
        }

        try {
            $datos = $this->generarDatosReporte($tipo_reporte, $filtros ?? []);
            
            // Verificar si hay datos en el reporte
            if (empty($datos['datos']) || count($datos['datos']) === 0) {
                $mensajeSinDatos = $this->generarMensajeSinDatos($tipo_reporte, $filtros ?? []);
                
                if ($formato === 'json') {
                    return $this->response->setJSON([
                        'success' => false,
                        'sin_datos' => true,
                        'mensaje' => $mensajeSinDatos,
                        'filtros_aplicados' => $filtros ?? []
                    ]);
                }

                return view('reportes/sin_datos', [
                    'mensaje' => $mensajeSinDatos,
                    'tipo_reporte' => $tipo_reporte,
                    'filtros' => $filtros ?? [],
                    'metadata' => $this->obtenerMetadataReporte($tipo_reporte)
                ]);
            }
            
            if ($formato === 'json') {
                return $this->response->setJSON([
                    'success' => true,
                    'datos' => $datos,
                    'metadata' => $this->obtenerMetadataReporte($tipo_reporte)
                ]);
            }

            return view('reportes/resultado', [
                'datos' => $datos,
                'tipo_reporte' => $tipo_reporte,
                'filtros' => $filtros ?? [],
                'metadata' => $this->obtenerMetadataReporte($tipo_reporte)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error generando reporte: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error generando el reporte: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtener reportes disponibles con configuración
     */
    private function obtenerReportesDisponibles()
    {
        return [
            'financiero' => [
                'nombre' => 'Reporte Financiero',
                'descripcion' => 'Análisis de pagos, deudas y flujo de efectivo',
                'icono' => 'fas fa-chart-line',
                'color' => 'success',
                'categoria' => 'Finanzas',
                'filtros' => ['fecha_desde', 'fecha_hasta', 'tipo_pago', 'estado_pago']
            ],
            'entregas' => [
                'nombre' => 'Reporte de Entregas',
                'descripcion' => 'Estado de entregas y cumplimiento de servicios',
                'icono' => 'fas fa-truck-loading',
                'color' => 'primary',
                'categoria' => 'Operaciones',
                'filtros' => ['fecha_desde', 'fecha_hasta', 'estado_entrega', 'tipo_evento']
            ],
            'equipos' => [
                'nombre' => 'Reporte de Equipos',
                'descripcion' => 'Asignación y estado de equipos por técnico',
                'icono' => 'fas fa-tools',
                'color' => 'info',
                'categoria' => 'Recursos',
                'filtros' => ['tecnico', 'estado_equipo', 'categoria_equipo', 'fecha_desde', 'fecha_hasta']
            ],
            'clientes' => [
                'nombre' => 'Reporte de Clientes',
                'descripcion' => 'Análisis de clientes y satisfacción del servicio',
                'icono' => 'fas fa-users',
                'color' => 'warning',
                'categoria' => 'Clientes',
                'filtros' => ['tipo_cliente', 'fecha_desde', 'fecha_hasta', 'estado_contrato']
            ],
            'cronograma' => [
                'nombre' => 'Reporte de Cronograma',
                'descripcion' => 'Planificación y seguimiento de proyectos',
                'icono' => 'fas fa-calendar-alt',
                'color' => 'secondary',
                'categoria' => 'Planificación',
                'filtros' => ['fecha_desde', 'fecha_hasta', 'estado_proyecto', 'responsable']
            ],
            'rentabilidad' => [
                'nombre' => 'Análisis de Rentabilidad',
                'descripcion' => 'Margen de ganancia por servicio y cliente',
                'icono' => 'fas fa-coins',
                'color' => 'danger',
                'categoria' => 'Finanzas',
                'filtros' => ['fecha_desde', 'fecha_hasta', 'servicio', 'tipo_evento']
            ]
        ];
    }

    /**
     * Obtener filtros base disponibles
     */
    private function obtenerFiltrosBase()
    {
        return [
            'fecha_desde' => [
                'tipo' => 'date',
                'label' => 'Fecha Desde',
                'placeholder' => 'Seleccionar fecha inicial'
            ],
            'fecha_hasta' => [
                'tipo' => 'date',
                'label' => 'Fecha Hasta',
                'placeholder' => 'Seleccionar fecha final'
            ],
            'tipo_pago' => [
                'tipo' => 'select',
                'label' => 'Tipo de Pago',
                'opciones' => $this->obtenerTiposPago()
            ],
            'estado_pago' => [
                'tipo' => 'select',
                'label' => 'Estado de Pago',
                'opciones' => [
                    'todos' => 'Todos',
                    'completo' => 'Pagado',
                    'pendiente' => 'Con Deuda'
                ]
            ],
            'estado_entrega' => [
                'tipo' => 'select',
                'label' => 'Estado de Entrega',
                'opciones' => [
                    'todos' => 'Todos',
                    'pendiente' => 'Pendiente',
                    'completada' => 'Completada'
                ]
            ],
            'tipo_evento' => [
                'tipo' => 'select',
                'label' => 'Tipo de Evento',
                'opciones' => $this->obtenerTiposEvento()
            ],
            'tecnico' => [
                'tipo' => 'select',
                'label' => 'Técnico Responsable',
                'opciones' => $this->obtenerTecnicos()
            ],
            'estado_equipo' => [
                'tipo' => 'select',
                'label' => 'Estado del Equipo',
                'opciones' => [
                    'todos' => 'Todos',
                    'Pendiente' => 'Pendiente',
                    'En Proceso' => 'En Proceso',
                    'Completado' => 'Completado',
                    'Programado' => 'Programado'
                ]
            ],
            'tipo_cliente' => [
                'tipo' => 'select',
                'label' => 'Tipo de Cliente',
                'opciones' => [
                    'todos' => 'Todos',
                    'persona' => 'Persona Natural',
                    'empresa' => 'Empresa'
                ]
            ],
            'estado_contrato' => [
                'tipo' => 'select',
                'label' => 'Estado del Contrato',
                'opciones' => [
                    'todos' => 'Todos',
                    'activo' => 'Activo',
                    'completado' => 'Completado',
                    'pendiente' => 'Pendiente'
                ]
            ],
            'servicio' => [
                'tipo' => 'select',
                'label' => 'Servicio',
                'opciones' => $this->obtenerServicios()
            ]
        ];
    }

    /**
     * Generar datos específicos para cada tipo de reporte
     */
    private function generarDatosReporte($tipo_reporte, $filtros)
    {
        switch ($tipo_reporte) {
            case 'financiero':
                return $this->generarReporteFinanciero($filtros);
            case 'entregas':
                return $this->generarReporteEntregas($filtros);
            case 'equipos':
                return $this->generarReporteEquipos($filtros);
            case 'clientes':
                return $this->generarReporteClientes($filtros);
            case 'cronograma':
                return $this->generarReporteCronograma($filtros);
            case 'rentabilidad':
                return $this->generarReporteRentabilidad($filtros);
            default:
                throw new \Exception('Tipo de reporte no válido');
        }
    }

    /**
     * Reporte Financiero
     */
    private function generarReporteFinanciero($filtros)
    {
        $builder = $this->contratoModel->db->table('contratos c');
        $builder->select('
            c.idcontrato,
            COALESCE(p.nombres, e.razonsocial) as cliente,
            COALESCE(e.razonsocial, CONCAT(p.nombres, " ", p.apellidos)) as cliente_completo,
            te.evento as tipo_evento,
            cot.fechaevento,
            SUM(sc.precio) as monto_total,
            COALESCE(ultimo_pago.deuda, SUM(sc.precio)) as deuda_actual,
            COALESCE(ultimo_pago.amortizacion, 0) as total_pagado,
            CASE 
                WHEN COALESCE(ultimo_pago.deuda, SUM(sc.precio)) <= 0.01 THEN "Pagado"
                ELSE "Con Deuda"
            END as estado_pago
        ');
        
        $builder->join('cotizaciones cot', 'cot.idcotizacion = c.idcotizacion');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente');
        $builder->join('personas p', 'p.idpersona = cl.idpersona', 'left');
        $builder->join('empresas e', 'e.idempresa = cl.idempresa', 'left');
        $builder->join('tipoeventos te', 'te.idtipoevento = cot.idtipoevento', 'left');
        $builder->join('servicioscontratados sc', 'sc.idcotizacion = cot.idcotizacion');
        
        // Subquery para obtener el último pago de cada contrato
        $subquery = $this->contratoModel->db->table('controlpagos cp2');
        $subquery->select('cp2.idcontrato, cp2.deuda, cp2.amortizacion');
        $subquery->where('cp2.fechahora = (SELECT MAX(fechahora) FROM controlpagos cp3 WHERE cp3.idcontrato = cp2.idcontrato)');
        
        $builder->join("({$subquery->getCompiledSelect(false)}) as ultimo_pago", 'ultimo_pago.idcontrato = c.idcontrato', 'left');
        
        $builder->groupBy('c.idcontrato');
        
        // Aplicar filtros
        $this->aplicarFiltrosFecha($builder, $filtros, 'cot.fechaevento');
        $this->aplicarFiltroEstadoPago($builder, $filtros);
        
        $datos = $builder->get()->getResultArray();
        
        // Calcular estadísticas
        $estadisticas = [
            'total_contratos' => count($datos),
            'monto_total' => array_sum(array_column($datos, 'monto_total')),
            'total_pagado' => array_sum(array_column($datos, 'total_pagado')),
            'deuda_total' => array_sum(array_column($datos, 'deuda_actual')),
            'contratos_pagados' => count(array_filter($datos, function($d) { return $d['deuda_actual'] <= 0.01; })),
            'contratos_con_deuda' => count(array_filter($datos, function($d) { return $d['deuda_actual'] > 0.01; }))
        ];
        
        return [
            'datos' => $datos,
            'estadisticas' => $estadisticas
        ];
    }

    /**
     * Reporte de Entregas
     */
    private function generarReporteEntregas($filtros)
    {
        $builder = $this->contratoModel->db->table('entregables en');
        $builder->select('
            en.identregable,
            en.fechahoraentrega,
            en.fecha_real_entrega,
            en.estado,
            en.observaciones,
            CONCAT(p.nombres, " ", p.apellidos) as cliente,
            s.servicio,
            sc.direccion,
            te.evento as tipo_evento,
            cot.fechaevento
        ');
        
        $builder->join('servicioscontratados sc', 'sc.idserviciocontratado = en.idserviciocontratado');
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $builder->join('cotizaciones cot', 'cot.idcotizacion = sc.idcotizacion');
        $builder->join('contratos c', 'c.idcotizacion = cot.idcotizacion');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente');
        $builder->join('personas p', 'p.idpersona = cl.idpersona');
        $builder->join('tipoeventos te', 'te.idtipoevento = cot.idtipoevento', 'left');
        
        // Aplicar filtros
        $this->aplicarFiltrosFecha($builder, $filtros, 'cot.fechaevento');
        $this->aplicarFiltroEstadoEntrega($builder, $filtros);
        $this->aplicarFiltroTipoEvento($builder, $filtros);
        
        $builder->orderBy('cot.fechaevento', 'DESC');
        
        $datos = $builder->get()->getResultArray();
        
        // Agregar estado_visual calculado en PHP
        foreach ($datos as &$dato) {
            if ($dato['estado'] === 'completada' || $dato['fecha_real_entrega'] !== null) {
                $dato['estado_visual'] = 'Entregado';
            } else {
                $dato['estado_visual'] = 'Pendiente';
            }
        }
        
        // Calcular estadísticas
        $estadisticas = [
            'total_entregas' => count($datos),
            'entregas_completadas' => count(array_filter($datos, function($d) { 
                return $d['estado'] === 'completada' || $d['fecha_real_entrega'] !== null; 
            })),
            'entregas_pendientes' => count(array_filter($datos, function($d) { 
                return $d['estado'] === 'pendiente' && $d['fecha_real_entrega'] === null; 
            })),
            'porcentaje_cumplimiento' => count($datos) > 0 ? 
                round((count(array_filter($datos, function($d) { 
                    return $d['estado'] === 'completada' || $d['fecha_real_entrega'] !== null; 
                })) / count($datos)) * 100, 2) : 0
        ];
        
        return [
            'datos' => $datos,
            'estadisticas' => $estadisticas
        ];
    }

    /**
     * Reporte de Equipos
     */
    private function generarReporteEquipos($filtros)
    {
        $builder = $this->equipoModel->db->table('equipos eq');
        $builder->select('
            eq.idequipo,
            eq.descripcion,
            eq.estadoservicio,
            eq.fecha_asignacion,
            CONCAT(p.nombres, " ", p.apellidos) as tecnico,
            c.cargo,
            s.servicio,
            sc.fechahoraservicio,
            sc.direccion,
            CONCAT(cliente.nombres, " ", cliente.apellidos) as cliente,
            te.evento as tipo_evento
        ');
        
        $builder->join('usuarios u', 'u.idusuario = eq.idusuario');
        $builder->join('personas p', 'p.idpersona = u.idpersona');
        $builder->join('cargos c', 'c.idcargo = u.idcargo');
        $builder->join('servicioscontratados sc', 'sc.idserviciocontratado = eq.idserviciocontratado');
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $builder->join('cotizaciones cot', 'cot.idcotizacion = sc.idcotizacion');
        $builder->join('clientes cl', 'cl.idcliente = cot.idcliente');
        $builder->join('personas cliente', 'cliente.idpersona = cl.idpersona');
        $builder->join('tipoeventos te', 'te.idtipoevento = cot.idtipoevento', 'left');
        
        // Aplicar filtros
        $this->aplicarFiltrosFecha($builder, $filtros, 'sc.fechahoraservicio');
        $this->aplicarFiltroTecnico($builder, $filtros);
        $this->aplicarFiltroEstadoEquipo($builder, $filtros);
        
        $builder->orderBy('sc.fechahoraservicio', 'DESC');
        
        $datos = $builder->get()->getResultArray();
        
        // Calcular estadísticas por técnico
        $estadisticas_por_tecnico = [];
        foreach ($datos as $equipo) {
            $tecnico = $equipo['tecnico'];
            if (!isset($estadisticas_por_tecnico[$tecnico])) {
                $estadisticas_por_tecnico[$tecnico] = [
                    'total_equipos' => 0,
                    'completados' => 0,
                    'en_proceso' => 0,
                    'pendientes' => 0
                ];
            }
            $estadisticas_por_tecnico[$tecnico]['total_equipos']++;
            switch ($equipo['estadoservicio']) {
                case 'Completado':
                    $estadisticas_por_tecnico[$tecnico]['completados']++;
                    break;
                case 'En Proceso':
                    $estadisticas_por_tecnico[$tecnico]['en_proceso']++;
                    break;
                case 'Pendiente':
                case 'Programado':
                    $estadisticas_por_tecnico[$tecnico]['pendientes']++;
                    break;
            }
        }
        
        return [
            'datos' => $datos,
            'estadisticas_por_tecnico' => $estadisticas_por_tecnico
        ];
    }

    /**
     * Reporte de Clientes
     */
    private function generarReporteClientes($filtros)
    {
        $builder = $this->contratoModel->db->table('clientes cl');
        $builder->select('
            cl.idcliente,
            CASE 
                WHEN cl.idempresa IS NOT NULL THEN e.razonsocial
                ELSE CONCAT(p.nombres, " ", p.apellidos)
            END as cliente,
            CASE 
                WHEN cl.idempresa IS NOT NULL THEN "Empresa"
                ELSE "Persona Natural"
            END as tipo_cliente,
            COALESCE(e.ruc, p.numerodoc) as documento,
            COALESCE(e.telefono, p.telprincipal) as telefono,
            COUNT(DISTINCT c.idcontrato) as total_contratos,
            SUM(sc.precio) as monto_total_contratado,
            COUNT(DISTINCT CASE WHEN ultimo_pago.deuda <= 0.01 THEN c.idcontrato END) as contratos_pagados,
            COUNT(DISTINCT CASE WHEN ultimo_pago.deuda > 0.01 THEN c.idcontrato END) as contratos_con_deuda
        ');
        
        $builder->join('personas p', 'p.idpersona = cl.idpersona', 'left');
        $builder->join('empresas e', 'e.idempresa = cl.idempresa', 'left');
        $builder->join('contratos c', 'c.idcliente = cl.idcliente', 'left');
        $builder->join('cotizaciones cot', 'cot.idcotizacion = c.idcotizacion', 'left');
        $builder->join('servicioscontratados sc', 'sc.idcotizacion = cot.idcotizacion', 'left');
        
        // Subquery para último pago
        $subquery = $this->contratoModel->db->table('controlpagos cp2');
        $subquery->select('cp2.idcontrato, cp2.deuda');
        $subquery->where('cp2.fechahora = (SELECT MAX(fechahora) FROM controlpagos cp3 WHERE cp3.idcontrato = cp2.idcontrato)');
        
        $builder->join("({$subquery->getCompiledSelect(false)}) as ultimo_pago", 'ultimo_pago.idcontrato = c.idcontrato', 'left');
        
        $builder->groupBy('cl.idcliente');
        
        // Aplicar filtros
        $this->aplicarFiltroTipoCliente($builder, $filtros);
        
        $builder->orderBy('monto_total_contratado', 'DESC');
        
        $datos = $builder->get()->getResultArray();
        
        // Calcular estadísticas
        $estadisticas = [
            'total_clientes' => count($datos),
            'clientes_persona' => count(array_filter($datos, function($d) { return $d['tipo_cliente'] === 'Persona Natural'; })),
            'clientes_empresa' => count(array_filter($datos, function($d) { return $d['tipo_cliente'] === 'Empresa'; })),
            'monto_total_contratado' => array_sum(array_column($datos, 'monto_total_contratado')),
            'promedio_contratos_por_cliente' => count($datos) > 0 ? round(array_sum(array_column($datos, 'total_contratos')) / count($datos), 2) : 0
        ];
        
        return [
            'datos' => $datos,
            'estadisticas' => $estadisticas
        ];
    }

    /**
     * Reporte de Cronograma
     */
    private function generarReporteCronograma($filtros)
    {
        $builder = $this->cronogramaModel->db->table('servicioscontratados sc');
        $builder->select('
            sc.idserviciocontratado,
            sc.fechahoraservicio,
            sc.direccion,
            s.servicio,
            CASE 
                WHEN cl.idempresa IS NOT NULL THEN e.razonsocial
                ELSE CONCAT(p.nombres, " ", p.apellidos)
            END as cliente,
            te.evento as tipo_evento,
            COALESCE(eq.estadoservicio, "Pendiente") as estado_proyecto,
            CONCAT(responsable.nombres, " ", responsable.apellidos) as responsable,
            responsable_cargo.cargo as cargo_responsable,
            CASE 
                WHEN COALESCE(eq.estadoservicio, "Pendiente") = "Completado" THEN 100
                WHEN COALESCE(eq.estadoservicio, "Pendiente") = "En Proceso" THEN 65
                WHEN COALESCE(eq.estadoservicio, "Pendiente") = "Programado" THEN 35
                ELSE 10
            END as progreso_porcentaje
        ');
        
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $builder->join('cotizaciones cot', 'cot.idcotizacion = sc.idcotizacion');
        $builder->join('contratos c', 'c.idcotizacion = cot.idcotizacion');
        $builder->join('clientes cl', 'cl.idcliente = cot.idcliente');
        $builder->join('personas p', 'p.idpersona = cl.idpersona', 'left');
        $builder->join('empresas e', 'e.idempresa = cl.idempresa', 'left');
        $builder->join('tipoeventos te', 'te.idtipoevento = cot.idtipoevento', 'left');
        $builder->join('equipos eq', 'eq.idserviciocontratado = sc.idserviciocontratado', 'left');
        $builder->join('usuarios u_resp', 'u_resp.idusuario = eq.idusuario', 'left');
        $builder->join('personas responsable', 'responsable.idpersona = u_resp.idpersona', 'left');
        $builder->join('cargos responsable_cargo', 'responsable_cargo.idcargo = u_resp.idcargo', 'left');
        
        // Aplicar filtros
        $this->aplicarFiltrosFecha($builder, $filtros, 'sc.fechahoraservicio');
        
        $builder->orderBy('sc.fechahoraservicio', 'ASC');
        
        $datos = $builder->get()->getResultArray();
        
        // Calcular estadísticas
        $estadisticas = [
            'total_proyectos' => count($datos),
            'proyectos_completados' => count(array_filter($datos, function($d) { return $d['estado_proyecto'] === 'Completado'; })),
            'proyectos_en_proceso' => count(array_filter($datos, function($d) { return $d['estado_proyecto'] === 'En Proceso'; })),
            'proyectos_pendientes' => count(array_filter($datos, function($d) { return in_array($d['estado_proyecto'], ['Pendiente', 'Programado']); })),
            'progreso_promedio' => count($datos) > 0 ? round(array_sum(array_column($datos, 'progreso_porcentaje')) / count($datos), 2) : 0
        ];
        
        return [
            'datos' => $datos,
            'estadisticas' => $estadisticas
        ];
    }

    /**
     * Reporte de Rentabilidad
     */
    private function generarReporteRentabilidad($filtros)
    {
        $builder = $this->contratoModel->db->table('servicioscontratados sc');
        $builder->select('
            s.servicio,
            s.precioregular,
            sc.precio as precio_contratado,
            (sc.precio - s.precioregular) as diferencia_precio,
            ROUND(((sc.precio - s.precioregular) / s.precioregular) * 100, 2) as margen_porcentaje,
            COUNT(*) as cantidad_contratada,
            SUM(sc.precio) as ingresos_totales,
            SUM(s.precioregular) as costo_base_total,
            SUM(sc.precio - s.precioregular) as ganancia_total,
            te.evento as tipo_evento,
            COUNT(DISTINCT cot.idcliente) as clientes_unicos
        ');
        
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $builder->join('cotizaciones cot', 'cot.idcotizacion = sc.idcotizacion');
        $builder->join('tipoeventos te', 'te.idtipoevento = cot.idtipoevento', 'left');
        
        // Aplicar filtros
        $this->aplicarFiltrosFecha($builder, $filtros, 'cot.fechaevento');
        $this->aplicarFiltroServicio($builder, $filtros);
        $this->aplicarFiltroTipoEvento($builder, $filtros);
        
        $builder->groupBy('s.idservicio, te.idtipoevento');
        $builder->orderBy('ganancia_total', 'DESC');
        
        $datos = $builder->get()->getResultArray();
        
        // Calcular estadísticas generales
        $estadisticas = [
            'total_servicios_analizados' => count($datos),
            'ingresos_totales' => array_sum(array_column($datos, 'ingresos_totales')),
            'ganancia_total' => array_sum(array_column($datos, 'ganancia_total')),
            'margen_promedio' => count($datos) > 0 ? round(array_sum(array_column($datos, 'margen_porcentaje')) / count($datos), 2) : 0,
            'servicio_mas_rentable' => count($datos) > 0 ? $datos[0]['servicio'] : 'N/A',
            'mayor_ganancia' => count($datos) > 0 ? $datos[0]['ganancia_total'] : 0
        ];
        
        return [
            'datos' => $datos,
            'estadisticas' => $estadisticas
        ];
    }

    // Métodos auxiliares para filtros

    private function aplicarFiltrosFecha($builder, $filtros, $campo_fecha)
    {
        if (!empty($filtros['fecha_desde'])) {
            $builder->where("DATE({$campo_fecha}) >=", $filtros['fecha_desde']);
        }
        if (!empty($filtros['fecha_hasta'])) {
            $builder->where("DATE({$campo_fecha}) <=", $filtros['fecha_hasta']);
        }
    }

    private function aplicarFiltroEstadoPago($builder, $filtros)
    {
        if (!empty($filtros['estado_pago']) && $filtros['estado_pago'] !== 'todos') {
            if ($filtros['estado_pago'] === 'completo') {
                $builder->having('deuda_actual <=', 0.01);
            } elseif ($filtros['estado_pago'] === 'pendiente') {
                $builder->having('deuda_actual >', 0.01);
            }
        }
    }

    private function aplicarFiltroEstadoEntrega($builder, $filtros)
    {
        if (!empty($filtros['estado_entrega']) && $filtros['estado_entrega'] !== 'todos') {
            $builder->where('en.estado', $filtros['estado_entrega']);
        }
    }

    private function aplicarFiltroTipoEvento($builder, $filtros)
    {
        if (!empty($filtros['tipo_evento']) && $filtros['tipo_evento'] !== 'todos') {
            $builder->where('te.idtipoevento', $filtros['tipo_evento']);
        }
    }

    private function aplicarFiltroTecnico($builder, $filtros)
    {
        if (!empty($filtros['tecnico']) && $filtros['tecnico'] !== 'todos') {
            $builder->where('eq.idusuario', $filtros['tecnico']);
        }
    }

    private function aplicarFiltroEstadoEquipo($builder, $filtros)
    {
        if (!empty($filtros['estado_equipo']) && $filtros['estado_equipo'] !== 'todos') {
            $builder->where('eq.estadoservicio', $filtros['estado_equipo']);
        }
    }

    private function aplicarFiltroTipoCliente($builder, $filtros)
    {
        if (!empty($filtros['tipo_cliente']) && $filtros['tipo_cliente'] !== 'todos') {
            if ($filtros['tipo_cliente'] === 'persona') {
                $builder->where('cl.idpersona IS NOT NULL');
            } elseif ($filtros['tipo_cliente'] === 'empresa') {
                $builder->where('cl.idempresa IS NOT NULL');
            }
        }
    }

    private function aplicarFiltroServicio($builder, $filtros)
    {
        if (!empty($filtros['servicio']) && $filtros['servicio'] !== 'todos') {
            $builder->where('s.idservicio', $filtros['servicio']);
        }
    }

    // Métodos para obtener datos de catálogos

    private function obtenerTiposPago()
    {
        $result = $this->controlPagoModel->db->table('tipospago')->get()->getResultArray();
        $opciones = ['todos' => 'Todos'];
        foreach ($result as $row) {
            $opciones[$row['idtipopago']] = $row['tipopago'];
        }
        return $opciones;
    }

    private function obtenerTiposEvento()
    {
        $result = $this->contratoModel->db->table('tipoeventos')->get()->getResultArray();
        $opciones = ['todos' => 'Todos'];
        foreach ($result as $row) {
            $opciones[$row['idtipoevento']] = $row['evento'];
        }
        return $opciones;
    }

    private function obtenerTecnicos()
    {
        $result = $this->equipoModel->db->query("
            SELECT DISTINCT u.idusuario, CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo
            FROM usuarios u
            INNER JOIN personas p ON p.idpersona = u.idpersona
            INNER JOIN equipos eq ON eq.idusuario = u.idusuario
            WHERE u.estado = 1
            ORDER BY nombre_completo
        ")->getResultArray();
        
        $opciones = ['todos' => 'Todos'];
        foreach ($result as $row) {
            $opciones[$row['idusuario']] = $row['nombre_completo'];
        }
        return $opciones;
    }

    private function obtenerServicios()
    {
        $result = $this->db->table('servicios')->get()->getResultArray();
        $opciones = ['todos' => 'Todos'];
        foreach ($result as $row) {
            $opciones[$row['idservicio']] = $row['servicio'];
        }
        return $opciones;
    }

    private function obtenerMetadataReporte($tipo_reporte)
    {
        $reportes = $this->obtenerReportesDisponibles();
        return [
            'nombre' => $reportes[$tipo_reporte]['nombre'] ?? 'Reporte',
            'descripcion' => $reportes[$tipo_reporte]['descripcion'] ?? '',
            'categoria' => $reportes[$tipo_reporte]['categoria'] ?? 'General',
            'fecha_generacion' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Generar mensaje personalizado cuando no hay datos
     */
    private function generarMensajeSinDatos($tipo_reporte, $filtros)
    {
        $reportes = $this->obtenerReportesDisponibles();
        $nombreReporte = $reportes[$tipo_reporte]['nombre'] ?? 'Reporte';
        
        $mensaje = "No se encontraron datos para el {$nombreReporte}";
        
        // Agregar información sobre los filtros aplicados
        if (!empty($filtros)) {
            $filtrosTexto = [];
            
            if (!empty($filtros['fecha_desde']) && !empty($filtros['fecha_hasta'])) {
                $filtrosTexto[] = "período del " . date('d/m/Y', strtotime($filtros['fecha_desde'])) . " al " . date('d/m/Y', strtotime($filtros['fecha_hasta']));
            } elseif (!empty($filtros['fecha_desde'])) {
                $filtrosTexto[] = "desde el " . date('d/m/Y', strtotime($filtros['fecha_desde']));
            } elseif (!empty($filtros['fecha_hasta'])) {
                $filtrosTexto[] = "hasta el " . date('d/m/Y', strtotime($filtros['fecha_hasta']));
            }
            
            if (!empty($filtros['estado_entrega']) && $filtros['estado_entrega'] !== 'todos') {
                $filtrosTexto[] = "estado: " . ucfirst($filtros['estado_entrega']);
            }
            
            if (!empty($filtros['tipo_evento']) && $filtros['tipo_evento'] !== 'todos') {
                $filtrosTexto[] = "tipo de evento: " . $filtros['tipo_evento'];
            }
            
            if (!empty($filtros['estado_pago']) && $filtros['estado_pago'] !== 'todos') {
                $filtrosTexto[] = "estado de pago: " . ucfirst($filtros['estado_pago']);
            }
            
            if (!empty($filtrosTexto)) {
                $mensaje .= " con los filtros aplicados: " . implode(', ', $filtrosTexto);
            }
        }
        
        $mensaje .= ".\n\nSugerencias:\n";
        $mensaje .= "• Verifique que el rango de fechas sea correcto\n";
        $mensaje .= "• Intente ampliar el período de búsqueda\n";
        $mensaje .= "• Revise los filtros aplicados\n";
        $mensaje .= "• Asegúrese de que existan registros en el sistema para el tipo de reporte seleccionado";
        
        return $mensaje;
    }

    /**
     * Exportar reporte a PDF
     */
    public function exportarPDF()
    {
        try {
            $tipo_reporte = $this->request->getPost('tipo_reporte');
            $filtros = $this->request->getPost('filtros');
            
            if (!$tipo_reporte) {
                return $this->response->setJSON(['error' => 'Tipo de reporte requerido']);
            }
            
            $datos = $this->generarDatosReporte($tipo_reporte, $filtros ?? []);
            $metadata = $this->obtenerMetadataReporte($tipo_reporte);
            
            // Verificar si hay datos
            if (empty($datos['datos'])) {
                return $this->response->setJSON([
                    'error' => 'No hay datos para exportar en el rango de fechas seleccionado'
                ]);
            }
            
            // Generar contenido HTML para el PDF
            $html = $this->generarHTMLParaPDF($datos, $metadata, $filtros ?? []);
            
            // Por ahora, devolver el HTML para que el usuario pueda imprimir
            // En el futuro se puede implementar una librería como TCPDF o DomPDF
            return $this->response->setContentType('text/html')->setBody($html);
            
        } catch (\Exception $e) {
            log_message('error', 'Error exportando PDF: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error al exportar PDF: ' . $e->getMessage()]);
        }
    }

    /**
     * Exportar reporte a Excel
     */
    public function exportarExcel()
    {
        try {
            $tipo_reporte = $this->request->getPost('tipo_reporte');
            $filtros = $this->request->getPost('filtros');
            
            if (!$tipo_reporte) {
                return $this->response->setJSON(['error' => 'Tipo de reporte requerido']);
            }
            
            $datos = $this->generarDatosReporte($tipo_reporte, $filtros ?? []);
            $metadata = $this->obtenerMetadataReporte($tipo_reporte);
            
            // Verificar si hay datos
            if (empty($datos['datos'])) {
                return $this->response->setJSON([
                    'error' => 'No hay datos para exportar en el rango de fechas seleccionado'
                ]);
            }
            
            // Generar CSV (formato compatible con Excel)
            $csv = $this->generarCSVParaExcel($datos, $metadata, $filtros ?? []);
            
            // Configurar headers para descarga
            $filename = 'reporte_' . $tipo_reporte . '_' . date('Y-m-d_H-i-s') . '.csv';
            
            return $this->response
                ->setContentType('text/csv')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($csv);
            
        } catch (\Exception $e) {
            log_message('error', 'Error exportando Excel: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error al exportar Excel: ' . $e->getMessage()]);
        }
    }

    /**
     * Generar HTML para PDF
     */
    private function generarHTMLParaPDF($datos, $metadata, $filtros)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . $metadata['nombre'] . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { color: #333; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        .info { margin-bottom: 20px; }
        .info h3 { color: #555; margin-bottom: 10px; }
        .info p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .stats { margin-top: 20px; }
        .stats h3 { color: #555; }
        .stats p { margin: 5px 0; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>' . $metadata['nombre'] . '</h1>
        <p>' . $metadata['descripcion'] . '</p>
        <p>Generado el: ' . date('d/m/Y H:i', strtotime($metadata['fecha_generacion'])) . '</p>
    </div>';

        // Información de filtros
        if (!empty($filtros)) {
            $html .= '<div class="info">
                <h3>Filtros Aplicados</h3>';
            
            if (!empty($filtros['fecha_desde']) && !empty($filtros['fecha_hasta'])) {
                $html .= '<p><strong>Período:</strong> ' . date('d/m/Y', strtotime($filtros['fecha_desde'])) . ' - ' . date('d/m/Y', strtotime($filtros['fecha_hasta'])) . '</p>';
            }
            
            if (!empty($filtros['estado_entrega']) && $filtros['estado_entrega'] !== 'todos') {
                $html .= '<p><strong>Estado de Entrega:</strong> ' . ucfirst($filtros['estado_entrega']) . '</p>';
            }
            
            if (!empty($filtros['tipo_evento']) && $filtros['tipo_evento'] !== 'todos') {
                $html .= '<p><strong>Tipo de Evento:</strong> ' . $filtros['tipo_evento'] . '</p>';
            }
            
            $html .= '</div>';
        }

        // Estadísticas
        if (isset($datos['estadisticas'])) {
            $html .= '<div class="stats">
                <h3>Estadísticas</h3>';
            
            foreach ($datos['estadisticas'] as $key => $value) {
                $label = ucfirst(str_replace('_', ' ', $key));
                if (is_numeric($value)) {
                    $html .= '<p><strong>' . $label . ':</strong> ' . number_format($value, 2) . '</p>';
                } else {
                    $html .= '<p><strong>' . $label . ':</strong> ' . $value . '</p>';
                }
            }
            
            $html .= '</div>';
        }

        // Tabla de datos
        if (!empty($datos['datos'])) {
            $html .= '<table>
                <thead>
                    <tr>';
            
            // Encabezados
            $primerRegistro = $datos['datos'][0];
            foreach (array_keys($primerRegistro) as $campo) {
                $label = ucfirst(str_replace('_', ' ', $campo));
                $html .= '<th>' . $label . '</th>';
            }
            
            $html .= '</tr>
                </thead>
                <tbody>';
            
            // Datos
            foreach ($datos['datos'] as $registro) {
                $html .= '<tr>';
                foreach ($registro as $valor) {
                    $html .= '<td>' . htmlspecialchars($valor) . '</td>';
                }
                $html .= '</tr>';
            }
            
            $html .= '</tbody>
            </table>';
        }

        $html .= '
    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Imprimir PDF</button>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Generar CSV para Excel
     */
    private function generarCSVParaExcel($datos, $metadata, $filtros)
    {
        $csv = '';
        
        // BOM para UTF-8 (para que Excel reconozca correctamente los caracteres especiales)
        $csv .= "\xEF\xBB\xBF";
        
        // Encabezado del reporte
        $csv .= $metadata['nombre'] . "\n";
        $csv .= $metadata['descripcion'] . "\n";
        $csv .= "Generado el: " . date('d/m/Y H:i', strtotime($metadata['fecha_generacion'])) . "\n";
        
        // Información de filtros
        if (!empty($filtros)) {
            $csv .= "\nFiltros Aplicados:\n";
            
            if (!empty($filtros['fecha_desde']) && !empty($filtros['fecha_hasta'])) {
                $csv .= "Período: " . date('d/m/Y', strtotime($filtros['fecha_desde'])) . " - " . date('d/m/Y', strtotime($filtros['fecha_hasta'])) . "\n";
            }
            
            if (!empty($filtros['estado_entrega']) && $filtros['estado_entrega'] !== 'todos') {
                $csv .= "Estado de Entrega: " . ucfirst($filtros['estado_entrega']) . "\n";
            }
            
            if (!empty($filtros['tipo_evento']) && $filtros['tipo_evento'] !== 'todos') {
                $csv .= "Tipo de Evento: " . $filtros['tipo_evento'] . "\n";
            }
        }
        
        $csv .= "\n";
        
        // Estadísticas
        if (isset($datos['estadisticas'])) {
            $csv .= "Estadísticas:\n";
            foreach ($datos['estadisticas'] as $key => $value) {
                $label = ucfirst(str_replace('_', ' ', $key));
                if (is_numeric($value)) {
                    $csv .= $label . ": " . number_format($value, 2) . "\n";
                } else {
                    $csv .= $label . ": " . $value . "\n";
                }
            }
            $csv .= "\n";
        }
        
        // Datos
        if (!empty($datos['datos'])) {
            // Encabezados de la tabla
            $primerRegistro = $datos['datos'][0];
            $encabezados = array_keys($primerRegistro);
            $csv .= implode(',', array_map(function($h) { return '"' . ucfirst(str_replace('_', ' ', $h)) . '"'; }, $encabezados)) . "\n";
            
            // Datos
            foreach ($datos['datos'] as $registro) {
                $fila = [];
                foreach ($registro as $valor) {
                    // Escapar comillas y envolver en comillas
                    $valor = str_replace('"', '""', $valor);
                    $fila[] = '"' . $valor . '"';
                }
                $csv .= implode(',', $fila) . "\n";
            }
        }
        
        return $csv;
    }
}