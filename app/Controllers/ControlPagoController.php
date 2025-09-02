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
        
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['titulo'] = 'Detalles del Pago';
        
        return view('ControlPagos/ver', $datos);
    }
}