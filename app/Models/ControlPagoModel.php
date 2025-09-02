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
        return $this->select('controlpagos.*, contratos.idcontrato, personas.nombres, personas.apellidos, tipospago.tipopago')
                    ->join('contratos', 'contratos.idcontrato = controlpagos.idcontrato')
                    ->join('clientes', 'clientes.idcliente = contratos.idcliente')
                    ->join('personas', 'personas.idpersona = clientes.idpersona')
                    ->join('tipospago', 'tipospago.idtipopago = controlpagos.idtipopago')
                    ->orderBy('controlpagos.fechahora', 'DESC')
                    ->findAll();
    }

    // Obtener información de un pago específico
    public function obtenerPago($id)
    {
        return $this->find($id);
    }

    // Obtener el último pago de un contrato
    public function obtenerUltimoPagoContrato($idcontrato)
    {
        return $this->where('idcontrato', $idcontrato)
                    ->orderBy('fechahora', 'DESC')
                    ->first();
    }

    // Obtener historial de pagos de un contrato
    public function obtenerPagosPorContrato($idcontrato)
    {
        return $this->where('idcontrato', $idcontrato)
                    ->orderBy('fechahora', 'ASC')
                    ->findAll();
    }

    // Calcular total pagado por contrato
    public function calcularTotalPagado($idcontrato)
    {
        return $this->where('idcontrato', $idcontrato)
                    ->selectSum('amortizacion')
                    ->first()['amortizacion'] ?? 0;
    }
}