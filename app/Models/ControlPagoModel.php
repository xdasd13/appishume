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
    
    // Obtener información completa de pagos con joins y filtros
    public function obtenerPagosCompletos($filtro_contrato = null, $filtro_estado = null, $filtro_fecha = null)
    {
        $builder = $this->select('controlpagos.*, contratos.idcontrato, personas.nombres, personas.apellidos, empresas.razonsocial, tipospago.tipopago, usuarios.nombreusuario')
                    ->join('contratos', 'contratos.idcontrato = controlpagos.idcontrato')
                    ->join('clientes', 'clientes.idcliente = contratos.idcliente')
                    ->join('personas', 'personas.idpersona = clientes.idpersona', 'left')
                    ->join('empresas', 'empresas.idempresa = clientes.idempresa', 'left')
                    ->join('tipospago', 'tipospago.idtipopago = controlpagos.idtipopago')
                    ->join('usuarios', 'usuarios.idusuario = controlpagos.idusuario', 'left')
                    ->orderBy('controlpagos.fechahora', 'DESC');
        
        // Aplicar filtros
        if (!empty($filtro_contrato)) {
            $builder->where('controlpagos.idcontrato', $filtro_contrato);
        }
        
        if (!empty($filtro_estado)) {
            if ($filtro_estado === 'completo') {
                $builder->where('controlpagos.deuda', 0);
            } elseif ($filtro_estado === 'pendiente') {
                $builder->where('controlpagos.deuda >', 0);
            }
        }
        
        if (!empty($filtro_fecha)) {
            $builder->like('controlpagos.fechahora', $filtro_fecha, 'after');
        }
        
        return $builder->findAll();
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