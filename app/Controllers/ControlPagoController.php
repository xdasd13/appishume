<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ControlPagoModel;
use App\Models\ContratoModel;

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
        // Obtener todos los registros de pagos con información completa
        $datos['pagos'] = $this->controlPagoModel->obtenerPagosCompletos();

        // Calcular estadísticas para los gráficos
        $datos['estadisticas'] = $this->calcularEstadisticas($datos['pagos']);

        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['titulo'] = 'Control de Pagos';

        return view('ControlPagos/index', $datos);
    }

    public function crear()
    {
        // Obtener contratos para el formulario
        $contratoModel = new ContratoModel();
        $datos['contratos'] = $contratoModel->obtenerContratosConClientes();

        // Obtener tipos de pago desde la base de datos
        $db = db_connect();
        $datos['tipospago'] = $db->table('tipospago')->get()->getResultArray();

        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['titulo'] = 'Registrar Nuevo Pago';

        return view('ControlPagos/crear', $datos);
    }

    public function guardar()
    {
        // Validar los datos del formulario
        $reglas = [
            'idcontrato' => 'required|numeric',
            'amortizacion' => 'required|decimal',
            'idtipopago' => 'required|numeric',
            'numtransaccion' => 'permit_empty|string',
            'fechahora' => 'required|valid_date'
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('error', 'Por favor, complete todos los campos requeridos correctamente.');
        }

        // Obtener datos del formulario
        $idcontrato = $this->request->getPost('idcontrato');
        $amortizacion = $this->request->getPost('amortizacion');

        // Obtener el último pago del contrato para calcular saldos
        $ultimoPago = $this->controlPagoModel->obtenerUltimoPagoContrato($idcontrato);

        if ($ultimoPago) {
            $saldo = $ultimoPago['deuda'];
            $deuda = $saldo - $amortizacion;
        } else {
            // Si es el primer pago, obtener el monto total del contrato
            $contratoInfo = $this->contratoModel->obtenerMontoContrato($idcontrato);
            $saldo = $contratoInfo['monto_total'] ?? 0;
            $deuda = $saldo - $amortizacion;
        }

        // Validar que la amortización no sea mayor al saldo
        if ($amortizacion > $saldo) {
            return redirect()->back()->withInput()->with('error', 'La amortización no puede ser mayor al saldo actual del contrato.');
        }

        // Preparar datos para guardar
        $data = [
            'idcontrato' => $idcontrato,
            'saldo' => $saldo,
            'amortizacion' => $amortizacion,
            'deuda' => $deuda,
            'idtipopago' => $this->request->getPost('idtipopago'),
            'numtransaccion' => $this->request->getPost('numtransaccion'),
            'fechahora' => $this->request->getPost('fechahora'),
            'idusuario' => session()->get('idusuario') // ID del usuario logueado
        ];

        // Guardar en la base de datos
        if ($this->controlPagoModel->save($data)) {
            return redirect()->to('/controlpagos')->with('success', 'Pago registrado correctamente');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al registrar el pago. Por favor, intente nuevamente.');
        }
    }

    public function ver($id)
    {
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
        $montoQuery = $db->query("
        SELECT SUM(sc.cantidad * sc.precio) as monto_total 
        FROM servicioscontratados sc 
        JOIN cotizaciones co ON co.idcotizacion = sc.idcotizacion 
        WHERE co.idcotizacion IN (
            SELECT idcotizacion FROM contratos WHERE idcontrato = ?
        )
    ", [$datos['pago']['idcontrato']]);

        if ($montoQuery->getNumRows() > 0) {
            $datos['info_contrato']['monto_total'] = $montoQuery->getRow()->monto_total ?? 0;
        } else {
            $datos['info_contrato']['monto_total'] = 0;
        }

        $datos['tipo_pago'] = $db->table('tipospago')
            ->where('idtipopago', $datos['pago']['idtipopago'])
            ->get()->getRowArray();

        // VERIFICAR Y CORREGIR: Obtener información del usuario
        $usuarioQuery = $db->table('usuarios')
            ->join('personas', 'personas.idpersona = usuarios.idpersona')
            ->where('idusuario', $datos['pago']['idusuario'])
            ->get();

        if ($usuarioQuery->getNumRows() > 0) {
            $datos['usuario'] = $usuarioQuery->getRowArray();
        } else {
            // Si no encuentra el usuario, crear un array vacío para evitar errores
            $datos['usuario'] = [
                'nombres' => 'Usuario',
                'apellidos' => 'No encontrado'
            ];
        }

        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['titulo'] = 'Detalles del Pago';

        return view('ControlPagos/ver', $datos);
    }

    // Nuevo método para obtener información del contrato via AJAX
    public function infoContrato($idcontrato)
    {
        $ultimoPago = $this->controlPagoModel->obtenerUltimoPagoContrato($idcontrato);

        // Obtener monto total del contrato
        $db = db_connect();
        $montoQuery = $db->query("
            SELECT SUM(sc.cantidad * sc.precio) as monto_total 
            FROM servicioscontratados sc 
            JOIN cotizaciones co ON co.idcotizacion = sc.idcotizacion 
            WHERE co.idcotizacion IN (
                SELECT idcotizacion FROM contratos WHERE idcontrato = ?
            )
        ", [$idcontrato]);

        $montoTotal = 0;
        if ($montoQuery->getNumRows() > 0) {
            $montoTotal = $montoQuery->getRow()->monto_total ?? 0;
        }

        if ($ultimoPago) {
            $saldo = $ultimoPago['deuda'];
        } else {
            $saldo = $montoTotal;
        }

        return $this->response->setJSON([
            'saldo_actual' => $saldo,
            'monto_total' => $montoTotal
        ]);
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
                if ($ultimoPago && $ultimoPago['deuda'] == 0) {
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
            }
        }

        return $estadisticas;
    }
}