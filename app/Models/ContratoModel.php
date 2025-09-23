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
