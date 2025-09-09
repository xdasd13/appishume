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
    
    // Obtener monto total de un contrato
    public function obtenerMontoContrato($idcontrato)
    {
        // Esta función debería calcular el monto total basado en los servicios contratados
        // Por ahora devolvemos un valor estático para demostración
        return ['monto_total' => 2800.00];
    }
}
