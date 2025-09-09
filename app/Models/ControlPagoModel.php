<?php

namespace App\Models;

use CodeIgniter\Model;

class ControlPagoModel extends Model
{
    protected $table = 'controlpagos';
    protected $primaryKey = 'idpagos';
    protected $allowedFields = [
        'idcontrato', 'saldo', 'amortizacion', 'deuda', 
        'idtipopago', 'numtransaccion', 'fechahora', 'idusuario', 'comprobante'
    ];
    
    // Obtener información completa de pagos con joins
    public function obtenerPagosCompletos()
    {
        return $this->select('controlpagos.*, contratos.idcontrato, personas.nombres, personas.apellidos, empresas.razonsocial, tipospago.tipopago, usuarios.nombreusuario')
                    ->join('contratos', 'contratos.idcontrato = controlpagos.idcontrato')
                    ->join('clientes', 'clientes.idcliente = contratos.idcliente')
                    ->join('personas', 'personas.idpersona = clientes.idpersona', 'left')
                    ->join('empresas', 'empresas.idempresa = clientes.idempresa', 'left')
                    ->join('tipospago', 'tipospago.idtipopago = controlpagos.idtipopago')
                    ->join('usuarios', 'usuarios.idusuario = controlpagos.idusuario', 'left')
                    ->orderBy('controlpagos.fechahora', 'DESC')
                    ->findAll();
    }

    // Obtener información de un pago específico
    public function obtenerPago($id)
    {
        return $this->select('controlpagos.*, usuarios.nombreusuario, personas.nombres, personas.apellidos')
                    ->join('usuarios', 'usuarios.idusuario = controlpagos.idusuario', 'left')
                    ->join('personas', 'personas.idpersona = usuarios.idpersona', 'left')
                    ->where('idpagos', $id)
                    ->first();
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
        return $this->select('controlpagos.*, tipospago.tipopago, usuarios.nombreusuario')
                    ->join('tipospago', 'tipospago.idtipopago = controlpagos.idtipopago')
                    ->join('usuarios', 'usuarios.idusuario = controlpagos.idusuario', 'left')
                    ->where('controlpagos.idcontrato', $idcontrato)
                    ->orderBy('controlpagos.fechahora', 'ASC')
                    ->findAll();
    }

    // Calcular total pagado por contrato
    public function calcularTotalPagado($idcontrato)
    {
        $result = $this->where('idcontrato', $idcontrato)
                    ->selectSum('amortizacion')
                    ->first();
        return $result['amortizacion'] ?? 0;
    }
}