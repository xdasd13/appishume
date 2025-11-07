<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ControlPagoModel;
use App\Models\ContratoModel;
use App\Libraries\ReniecService;

class ControlPagoController extends BaseController
{
    protected $controlPagoModel;
    protected $contratoModel;

    public function __construct()
    {
        $this->controlPagoModel = new ControlPagoModel();
        $this->contratoModel = new ContratoModel();
    }

    public function index()
    {
        if (!session()->get('usuario_logueado')) {
            return redirect()->to('/login')->with('error', 'Acceso denegado.');
        }

        // Obtener parámetros de filtro
        $filtro_contrato = $this->request->getGet('filtro_contrato');
        $filtro_estado = $this->request->getGet('filtro_estado');
        $filtro_fecha = $this->request->getGet('filtro_fecha');
        
        // Obtener pagos con filtros
        $datos['pagos'] = $this->controlPagoModel->obtenerPagosCompletos($filtro_contrato, $filtro_estado, $filtro_fecha);
        
        // Obtener contratos para el filtro
        $datos['contratos'] = $this->contratoModel->obtenerContratosConClientes();

        // Calcular estadísticas para los gráficos
        $datos['estadisticas'] = $this->calcularEstadisticas($datos['pagos']);

        // Pasar valores de filtros a la vista
        $datos['filtro_contrato'] = $filtro_contrato;
        $datos['filtro_estado'] = $filtro_estado;
        $datos['filtro_fecha'] = $filtro_fecha;

        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['titulo'] = 'Control de Pagos';

        return view('ControlPagos/index', $datos);
    }

    public function crear()
    {
        if (!session()->get('usuario_logueado')) {
            return redirect()->to('/login')->with('error', 'Acceso denegado.');
        }

        // Obtener contratos para el formulario
        $contratoModel = new ContratoModel();
        $datos['contratos'] = $contratoModel->obtenerContratosConClientes();

        // Obtener tipos de pago desde la base de datos
        $db = db_connect();
        $datos['tipospago'] = $db->table('tipospago')->get()->getResultArray();

        // Verificar si se pasó un contrato específico en la URL
        $contratoId = $this->request->getGet('contrato');
        $datos['contrato_seleccionado'] = $contratoId;
        
        // Si hay un contrato pre-seleccionado, cargar su información directamente
        if ($contratoId) {
            try {
                // Obtener información del contrato
                $infoContrato = $this->infoContratoCalculo($contratoId);
                
                if ($infoContrato) {
                    $datos['info_contrato_precargada'] = [
                        'monto_total' => $infoContrato['monto_total'],
                        'saldo_actual' => $infoContrato['saldo_actual'],
                        'total_pagado' => $infoContrato['total_pagado']
                    ];
                } else {
                    $datos['info_contrato_precargada'] = null;
                }
            } catch (\Exception $e) {
                log_message('error', 'Error al cargar información del contrato: ' . $e->getMessage());
                $datos['info_contrato_precargada'] = null;
            }
        } else {
            $datos['info_contrato_precargada'] = null;
        }

        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['titulo'] = 'Registrar Nuevo Pago';

        return view('ControlPagos/crear', $datos);
    }

    public function guardar()
    {
        // Siempre responder como AJAX para simplificar
        header('Content-Type: application/json');
        
        // Log para debugging
        log_message('info', '=== MÉTODO GUARDAR() LLAMADO ===');
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));
        log_message('info', 'GET data: ' . json_encode($this->request->getGet()));
        log_message('info', 'Usuario logueado: ' . (session()->get('usuario_logueado') ? 'Sí' : 'No'));
        log_message('info', 'Usuario ID: ' . (session()->get('usuario_id') ?? 'N/A'));
        log_message('info', 'Es AJAX: ' . ($this->request->isAJAX() ? 'Sí' : 'No'));
        log_message('info', 'Método HTTP: ' . $this->request->getMethod());
        log_message('info', 'URI: ' . $this->request->getUri());

        try {
            // Obtener datos del formulario - validación simplificada
            $idcontrato = $this->request->getPost('idcontrato');
            $amortizacion = floatval($this->request->getPost('amortizacion') ?? 0);
            $idtipopago = $this->request->getPost('idtipopago');
            $dniPagador = $this->request->getPost('dni_pagador') ?? '';
            $nombrePagador = $this->request->getPost('nombre_pagador_hidden') 
                          ?? $this->request->getPost('nombre_pagador') 
                          ?? '';
            
            // Validaciones básicas mínimas
            if (empty($idcontrato) || $idcontrato <= 0) {
                throw new \Exception('Debe seleccionar un contrato válido.');
            }
            
            if ($amortizacion <= 0) {
                throw new \Exception('La amortización debe ser mayor a cero.');
            }
            
            if (empty($idtipopago)) {
                throw new \Exception('Debe seleccionar un tipo de pago.');
            }
            
            // Obtener tipo de pago para validación condicional
            $db = db_connect();
            $tipoPagoInfo = $db->table('tipospago')->where('idtipopago', $idtipopago)->get()->getRowArray();
            $esEfectivo = false;
            
            if ($tipoPagoInfo) {
                $tipoPagoTexto = strtolower($tipoPagoInfo['tipopago'] ?? '');
                $esEfectivo = strpos($tipoPagoTexto, 'efectivo') !== false;
            }

            // Obtener información actual del contrato
            $infoContrato = $this->infoContratoCalculo($idcontrato);
            
            if (!$infoContrato) {
                throw new \Exception('Error al obtener información del contrato. Verifique que el contrato exista.');
            }

            $saldo = floatval($infoContrato['saldo_actual']);
            $deuda = $saldo - $amortizacion;

            // Validar que la amortización no sea mayor al saldo
            if ($amortizacion > $saldo) {
                throw new \Exception('La amortización (S/ ' . number_format($amortizacion, 2) . ') no puede ser mayor al saldo actual (S/ ' . number_format($saldo, 2) . ').');
            }

            // Procesar comprobante (opcional) - simplificado
            $comprobante = $this->request->getFile('comprobante');
            $nombreComprobante = null;

            if ($comprobante && $comprobante->isValid() && !$comprobante->hasMoved()) {
                try {
                    $uploadPath = ROOTPATH . 'public/uploads/comprobantes';
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    
                    $nuevoNombre = $comprobante->getRandomName();
                    $comprobante->move($uploadPath, $nuevoNombre);
                    $nombreComprobante = $nuevoNombre;
                } catch (\Exception $e) {
                    log_message('warning', 'Error al subir comprobante: ' . $e->getMessage());
                    // Continuar sin comprobante si falla la subida
                }
            }

            // Preparar datos para guardar - simplificado
            $fechahora = $this->request->getPost('fechahora_hidden');
            if (empty($fechahora)) {
                $fechahora = $this->getPeruDateTime();
            }
            
            $numtransaccion = $esEfectivo ? '' : ($this->request->getPost('numtransaccion') ?? '');
            $idusuario = session()->get('usuario_id') ?? 1; // Fallback a usuario 1 si no hay sesión
            
            // Preparar datos
            $data = [
                'idcontrato' => intval($idcontrato),
                'saldo' => $saldo,
                'amortizacion' => $amortizacion,
                'deuda' => $deuda,
                'idtipopago' => intval($idtipopago),
                'numtransaccion' => $numtransaccion,
                'fechahora' => $fechahora,
                'idusuario' => intval($idusuario),
                'comprobante' => $nombreComprobante,
                'dni_pagador' => $dniPagador,
                'nombre_pagador' => $nombrePagador
            ];
            
            log_message('info', 'Intentando guardar pago: ' . json_encode($data));

            // Guardar en la base de datos
            $guardado = $this->controlPagoModel->save($data);
            
            if (!$guardado) {
                $modelErrors = $this->controlPagoModel->errors();
                $errorMsg = !empty($modelErrors) ? implode('. ', $modelErrors) : 'Error al guardar en la base de datos.';
                
                // Eliminar comprobante si falló
                if ($nombreComprobante && file_exists(ROOTPATH . 'public/uploads/comprobantes/' . $nombreComprobante)) {
                    @unlink(ROOTPATH . 'public/uploads/comprobantes/' . $nombreComprobante);
                }
                
                throw new \Exception($errorMsg);
            }
            
            // Éxito
            $mensaje = '✅ Pago registrado correctamente';
            if ($deuda <= 0.01) {
                $mensaje .= '. ¡Felicidades! El contrato ha sido completamente pagado.';
            } else {
                $mensaje .= '. Saldo restante: S/ ' . number_format($deuda, 2);
            }
            
            $idPagoGuardado = $this->controlPagoModel->getInsertID();
            log_message('info', '✅ Pago guardado exitosamente: ID ' . $idPagoGuardado);
            
            // Siempre responder JSON
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $mensaje,
                'redirect' => base_url('/controlpagos'),
                'pago_id' => $idPagoGuardado
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '=== EXCEPCIÓN EN GUARDAR() ===');
            log_message('error', 'Mensaje: ' . $e->getMessage());
            log_message('error', 'Archivo: ' . $e->getFile() . ':' . $e->getLine());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            
            // Eliminar comprobante si existe
            if (isset($nombreComprobante) && $nombreComprobante && file_exists(ROOTPATH . 'public/uploads/comprobantes/' . $nombreComprobante)) {
                @unlink(ROOTPATH . 'public/uploads/comprobantes/' . $nombreComprobante);
            }
            
            $errorMessage = $e->getMessage();
            
            // Siempre responder JSON
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => $errorMessage,
                'error_details' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ]);
        }
    }

    public function ver($id)
    {
        if (!session()->get('usuario_logueado')) {
            return redirect()->to('/login')->with('error', 'Acceso denegado.');
        }

        $datos['pago'] = $this->controlPagoModel->obtenerPago($id);

        if (!$datos['pago']) {
            return redirect()->to('/controlpagos')->with('error', 'Pago no encontrado');
        }

        // Obtener información adicional del pago
        $db = db_connect();

        // Obtener información del contrato
        $datos['info_contrato'] = $db->table('contratos')
            ->join('clientes', 'clientes.idcliente = contratos.idcliente')
            ->join('personas', 'personas.idpersona = clientes.idpersona', 'left')
            ->join('empresas', 'empresas.idempresa = clientes.idempresa', 'left')
            ->where('contratos.idcontrato', $datos['pago']['idcontrato'])
            ->get()->getRowArray();

        // Obtener monto total del contrato
        $montoInfo = $this->contratoModel->obtenerMontoContrato($datos['pago']['idcontrato']);
        $datos['info_contrato']['monto_total'] = $montoInfo['monto_total'] ?? 0;

        $datos['tipo_pago'] = $db->table('tipospago')
            ->where('idtipopago', $datos['pago']['idtipopago'])
            ->get()->getRowArray();

        // Obtener historial de pagos del contrato
        $datos['historial_pagos'] = $this->controlPagoModel->obtenerPagosPorContrato($datos['pago']['idcontrato']);

        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['titulo'] = 'Detalles del Pago';

        return view('ControlPagos/ver', $datos);
    }

    // Función helper para obtener fecha y hora de Perú
    private function getPeruDateTime()
    {
        // Configurar zona horaria de Perú
        date_default_timezone_set('America/Lima');
        $peruDateTime = date('Y-m-d H:i:s');
        
        // Mantener zona horaria de Perú
        date_default_timezone_set('America/Lima');
        
        return $peruDateTime;
    }

    // Método para validar DNI del pagador via AJAX
    public function validarDniPagador()
    {
        if (!session()->get('usuario_logueado')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Acceso denegado']);
        }

        try {
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

            // Consultar RENIEC via Decolecta
            $reniecService = new ReniecService();
            $result = $reniecService->consultarDni($dni);

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
                        'fecha_nacimiento' => $result['data']['fecha_nacimiento'] ?? '',
                        'sexo' => $result['data']['sexo'] ?? '',
                        'source' => $result['data']['source'] ?? 'api'
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'DNI no encontrado en RENIEC',
                    'data' => null
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error en validarDniPagador: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al validar DNI: ' . $e->getMessage()
            ]);
        }
    }

    // Método para obtener información del contrato via AJAX
    public function infoContrato($idcontrato)
    {
        if (!session()->get('usuario_logueado')) {
            return $this->response->setJSON(['error' => 'Acceso denegado']);
        }
        
        try {
            $infoContrato = $this->infoContratoCalculo($idcontrato);
            
            if (!$infoContrato) {
                return $this->response->setJSON([
                    'error' => 'Contrato no encontrado',
                    'saldo_actual' => 0,
                    'monto_total' => 0,
                    'total_pagado' => 0
                ]);
            }

            return $this->response->setJSON([
                'saldo_actual' => $infoContrato['saldo_actual'],
                'monto_total' => $infoContrato['monto_total'],
                'total_pagado' => $infoContrato['total_pagado'],
                'contrato_id' => $idcontrato
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en infoContrato: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => 'Error: ' . $e->getMessage(),
                'saldo_actual' => 0,
                'monto_total' => 0,
                'total_pagado' => 0
            ]);
        }
    }

    // Método interno para calcular información del contrato
    private function infoContratoCalculo($idcontrato)
    {
        // Verificar que el contrato existe
        $contrato = $this->contratoModel->find($idcontrato);
        if (!$contrato) {
            return null;
        }
        
        // Obtener monto total del contrato
        $montoInfo = $this->contratoModel->obtenerMontoContrato($idcontrato);
        $montoTotal = $montoInfo['monto_total'] ?? 0;
        
        // Obtener total pagado
        $totalPagado = $this->controlPagoModel->calcularTotalPagado($idcontrato);
        
        // Calcular saldo actual
        $saldoActual = $montoTotal - $totalPagado;
        
        return [
            'monto_total' => $montoTotal,
            'total_pagado' => $totalPagado,
            'saldo_actual' => $saldoActual
        ];
    }

    // Método para ver pagos por contrato
    public function porContrato($idcontrato)
    {
        if (!session()->get('usuario_logueado')) {
            return redirect()->to('/login')->with('error', 'Acceso denegado.');
        }

        // Obtener información del contrato
        $db = db_connect();
        $datos['contrato'] = $db->table('contratos')
            ->join('clientes', 'clientes.idcliente = contratos.idcliente')
            ->join('personas', 'personas.idpersona = clientes.idpersona', 'left')
            ->join('empresas', 'empresas.idempresa = clientes.idempresa', 'left')
            ->where('contratos.idcontrato', $idcontrato)
            ->get()->getRowArray();

        if (!$datos['contrato']) {
            return redirect()->to('/controlpagos')->with('error', 'Contrato no encontrado');
        }

        // Obtener monto total del contrato
        $montoInfo = $this->contratoModel->obtenerMontoContrato($idcontrato);
        $datos['contrato']['monto_total'] = $montoInfo['monto_total'] ?? 0;

        // Obtener pagos del contrato
        $datos['pagos'] = $this->controlPagoModel->obtenerPagosPorContrato($idcontrato);
        
        // Calcular total pagado
        $datos['total_pagado'] = $this->controlPagoModel->calcularTotalPagado($idcontrato);
        $datos['deuda_actual'] = $datos['contrato']['monto_total'] - $datos['total_pagado'];

        // Verificar si el contrato está completamente pagado
        $datos['completamente_pagado'] = ($datos['deuda_actual'] <= 0.01);

        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['titulo'] = 'Pagos del Contrato #' . $idcontrato;

        return view('ControlPagos/por_contrato', $datos);
    }

    // Método para descargar comprobante
    public function descargarComprobante($id)
    {
        if (!session()->get('usuario_logueado')) {
            return redirect()->to('/login')->with('error', 'Acceso denegado.');
        }

        $pago = $this->controlPagoModel->find($id);
        
        if (!$pago || empty($pago['comprobante'])) {
            return redirect()->back()->with('error', 'Comprobante no encontrado');
        }
        
        $filePath = ROOTPATH . 'public/uploads/comprobantes/' . $pago['comprobante'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'El archivo del comprobante no existe');
        }
        
        return $this->response->download($filePath, null);
    }

    // Método para generar voucher
    public function generarVoucher($id)
    {
        if (!session()->get('usuario_logueado')) {
            return redirect()->to('/login')->with('error', 'Acceso denegado.');
        }

        $datos['pago'] = $this->controlPagoModel->obtenerPago($id);

        if (!$datos['pago']) {
            return redirect()->to('/controlpagos')->with('error', 'Pago no encontrado');
        }

        // Obtener información adicional del pago
        $db = db_connect();

        // Obtener información del contrato
        $datos['info_contrato'] = $db->table('contratos')
            ->join('clientes', 'clientes.idcliente = contratos.idcliente')
            ->join('personas', 'personas.idpersona = clientes.idpersona', 'left')
            ->join('empresas', 'empresas.idempresa = clientes.idempresa', 'left')
            ->where('contratos.idcontrato', $datos['pago']['idcontrato'])
            ->get()->getRowArray();

        // Obtener monto total del contrato
        $montoInfo = $this->contratoModel->obtenerMontoContrato($datos['pago']['idcontrato']);
        $datos['info_contrato']['monto_total'] = $montoInfo['monto_total'] ?? 0;

        $datos['tipo_pago'] = $db->table('tipospago')
            ->where('idtipopago', $datos['pago']['idtipopago'])
            ->get()->getRowArray();

        // Obtener información de la empresa
        $datos['empresa'] = $db->table('empresas')->where('idempresa', 1)->get()->getRowArray();

        return view('ControlPagos/voucher', $datos);
    }

    // Método para calcular estadísticas
    private function calcularEstadisticas($pagos)
    {
        $estadisticas = [
            'total_pagado' => 0,
            'deuda_total' => 0,
            'pagos_count' => count($pagos),
            'por_tipo_pago' => [],
            'contratos_con_deuda' => 0,
            'contratos_pagados' => 0
        ];

        $contratosProcesados = [];

        foreach ($pagos as $pago) {
            $estadisticas['total_pagado'] += $pago['amortizacion'];

            // Contar por tipo de pago
            $tipoPago = $pago['tipopago'];
            if (!isset($estadisticas['por_tipo_pago'][$tipoPago])) {
                $estadisticas['por_tipo_pago'][$tipoPago] = 0;
            }
            $estadisticas['por_tipo_pago'][$tipoPago] += $pago['amortizacion'];

            // Contar contratos con deuda y pagados
            if (!in_array($pago['idcontrato'], $contratosProcesados)) {
                $ultimoPago = $this->controlPagoModel->obtenerUltimoPagoContrato($pago['idcontrato']);
                if ($ultimoPago && $ultimoPago['deuda'] <= 0.01) {
                    $estadisticas['contratos_pagados']++;
                } else {
                    $estadisticas['contratos_con_deuda']++;
                }
                $contratosProcesados[] = $pago['idcontrato'];
            }
        }

        // Calcular deuda total (último pago de cada contrato)
        foreach ($contratosProcesados as $idcontrato) {
            $ultimoPago = $this->controlPagoModel->obtenerUltimoPagoContrato($idcontrato);
            if ($ultimoPago) {
                $estadisticas['deuda_total'] += $ultimoPago['deuda'];
            } else {
                // Si no hay pagos, la deuda es el monto total
                $montoInfo = $this->contratoModel->obtenerMontoContrato($idcontrato);
                $estadisticas['deuda_total'] += $montoInfo['monto_total'] ?? 0;
            }
        }

        return $estadisticas;
    }
}