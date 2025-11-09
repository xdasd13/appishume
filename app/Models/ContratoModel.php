<?php

namespace App\Models;

use CodeIgniter\Model;

class ContratoModel extends Model
{
    protected $table = 'contratos';
    protected $primaryKey = 'idcontrato';
    protected $allowedFields = ['idcotizacion', 'idcliente', 'autorizapublicacion'];
    
    // Obtener contratos con información de clientes
    public function obtenerContratosConClientes()
    {
        return $this->select('contratos.idcontrato, personas.nombres, personas.apellidos, empresas.razonsocial')
                    ->join('clientes', 'clientes.idcliente = contratos.idcliente')
                    ->join('personas', 'personas.idpersona = clientes.idpersona', 'left')
                    ->join('empresas', 'empresas.idempresa = clientes.idempresa', 'left')
                    ->findAll();
    }
    
    // NUEVO MÉTODO: Obtener solo contratos con deuda - CORREGIDO
    public function obtenerContratosConDeuda()
    {
        $db = db_connect();
        
        // Primero obtener todos los contratos con su información
        $contratos = $this->select('contratos.idcontrato, personas.nombres, personas.apellidos, empresas.razonsocial')
                         ->join('clientes', 'clientes.idcliente = contratos.idcliente')
                         ->join('personas', 'personas.idpersona = clientes.idpersona', 'left')
                         ->join('empresas', 'empresas.idempresa = clientes.idempresa', 'left')
                         ->findAll();
        
        $contratosConDeuda = [];
        
        foreach ($contratos as $contrato) {
            // Obtener monto total del contrato
            $montoInfo = $this->obtenerMontoContrato($contrato['idcontrato']);
            $montoTotal = $montoInfo['monto_total'] ?? 0;
            
            // Obtener total pagado
            $controlPagoModel = new ControlPagoModel();
            $totalPagado = $controlPagoModel->calcularTotalPagado($contrato['idcontrato']);
            
            // Calcular saldo actual
            $saldoActual = $montoTotal - $totalPagado;
            
            // Solo incluir contratos con deuda
            if ($saldoActual > 0.01) {
                $contrato['saldo_actual'] = $saldoActual;
                $contratosConDeuda[] = $contrato;
            }
        }
        
        return $contratosConDeuda;
    }
    
    // Obtener monto total de un contrato (CALCULO REAL)
    public function obtenerMontoContrato($idcontrato)
    {
        $db = db_connect();
        
        $query = $db->query("
            SELECT SUM(sc.cantidad * sc.precio) as monto_total 
            FROM servicioscontratados sc 
            JOIN cotizaciones co ON co.idcotizacion = sc.idcotizacion 
            WHERE co.idcotizacion IN (
                SELECT idcotizacion FROM contratos WHERE idcontrato = ?
            )
        ", [$idcontrato]);
        
        $result = $query->getRow();
        
        return ['monto_total' => $result->monto_total ?? 0];
    }
}