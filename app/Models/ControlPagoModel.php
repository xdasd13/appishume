<?php

namespace App\Models;

use CodeIgniter\Model;

class ControlPagoModel extends Model
{
    protected $table = 'controlpagos';
    protected $primaryKey = 'idpagos';
    protected $allowedFields = [
        'idcontrato', 'saldo', 'amortizacion', 'deuda', 
        'idtipopago', 'numtransaccion', 'fechahora', 'idusuario'
    ];
    
    // Obtener información completa de pagos con joins
    public function obtenerPagosCompletos()
    {
        return $this->findAll(); // Primero usemos findAll() simple para testear
    }

    public function index(){
    // Debug: verificar conexión
    $db = db_connect();
    $pagos = $db->table('controlpagos')->get()->getResultArray();
    echo "<!-- Debug: " . count($pagos) . " registros encontrados -->";
    
    $controlPagoModel = new ControlPagoModel();
    $datos['pagos'] = $controlPagoModel->findAll();
    
    $datos['header'] = view('Layouts/header');
    $datos['footer'] = view('Layouts/footer');
    return view('ControlPagos/index', $datos);
}
}