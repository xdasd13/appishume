<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\ControlPagoModel;

class ControlPagoController extends BaseController{

  public function index(){
    // Crear instancia del modelo
    $controlPagoModel = new ControlPagoModel();
    
    // Obtener todos los registros de pagos
    $datos['pagos'] = $controlPagoModel->obtenerPagosCompletos();
    
    $datos['header'] = view('Layouts/header');
    $datos['footer'] = view('Layouts/footer');
    return view('ControlPagos/index', $datos);
  }

  public function crear(){
    $datos['header'] = view('Layouts/header');
    $datos['footer'] = view('Layouts/footer');
    return view('ControlPagos/crear', $datos);
  }

  public function guardar() {
    // Validar y procesar los datos del formulario
    $model = new ControlPagoModel();
    
    $data = [
        'idcontrato' => $this->request->getPost('idcontrato'),
        'saldo' => $this->request->getPost('saldo'),
        'amortizacion' => $this->request->getPost('amortizacion'),
        'deuda' => $this->request->getPost('deuda'),
        'idtipopago' => $this->request->getPost('idtipopago'),
        'numtransaccion' => $this->request->getPost('numtransaccion'),
        'fechahora' => $this->request->getPost('fechahora'),
        'idusuario' => session()->get('idusuario') // ID del usuario logueado
    ];
    
    if ($model->save($data)) {
        // Éxito
        return redirect()->to('/controlpagos')->with('success', 'Pago registrado correctamente');
    } else {
        // Error
        return redirect()->back()->withInput()->with('error', 'Error al registrar el pago');
    }
  }
}