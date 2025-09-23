<?php

namespace App\Models;

use CodeIgniter\Model;

class ControlPagoModel extends Model
{
    protected $table = 'controlpagos';
    protected $primaryKey = 'idpagos';
    protected $allowedFields = [
        'idcontrato', 
        'saldo', 
        'amortizacion', 
        'deuda', 
        'idtipopago', 
        'numtransaccion', 
        'fechahora', 
        'idusuario', 
        'comprobante'
    ];

    public function obtenerPagosCompletos($filtro_contrato = null, $filtro_estado = null, $filtro_fecha = null)
    {
        $builder = $this->db->table('controlpagos p');
        $builder->select('p.*, 
                         tp.tipopago, 
                         u.nombreusuario,
                         CONCAT(per.nombres, " ", per.apellidos) as nombre_completo,
                         per.nombres, per.apellidos, 
                         emp.razonsocial');
        $builder->join('tipospago tp', 'tp.idtipopago = p.idtipopago', 'left');
        $builder->join('usuarios u', 'u.idusuario = p.idusuario', 'left');
        $builder->join('contratos c', 'c.idcontrato = p.idcontrato', 'left');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente', 'left');
        $builder->join('personas per', 'per.idpersona = cl.idpersona', 'left');
        $builder->join('empresas emp', 'emp.idempresa = cl.idempresa', 'left');
        $builder->orderBy('p.fechahora', 'DESC');

        // Aplicar filtros
        if (!empty($filtro_contrato)) {
            $builder->where('p.idcontrato', $filtro_contrato);
        }

        if (!empty($filtro_estado)) {
            if ($filtro_estado == 'completo') {
                $builder->where('p.deuda', 0);
            } elseif ($filtro_estado == 'pendiente') {
                $builder->where('p.deuda >', 0);
            }
        }

        if (!empty($filtro_fecha)) {
            $builder->like('p.fechahora', $filtro_fecha, 'after');
        }

        return $builder->get()->getResultArray();
    }

    public function obtenerPago($id)
    {
        $builder = $this->db->table('controlpagos p');
        $builder->select('p.*, 
                         tp.tipopago, 
                         u.nombreusuario,
                         per.nombres, per.apellidos, 
                         emp.razonsocial');
        $builder->join('tipospago tp', 'tp.idtipopago = p.idtipopago', 'left');
        $builder->join('usuarios u', 'u.idusuario = p.idusuario', 'left');
        $builder->join('contratos c', 'c.idcontrato = p.idcontrato', 'left');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente', 'left');
        $builder->join('personas per', 'per.idpersona = cl.idpersona', 'left');
        $builder->join('empresas emp', 'emp.idempresa = cl.idempresa', 'left');
        $builder->where('p.idpagos', $id);
        
        return $builder->get()->getRowArray();
    }

    public function obtenerUltimoPagoContrato($idcontrato)
    {
        return $this->where('idcontrato', $idcontrato)
                    ->orderBy('fechahora', 'DESC')
                    ->first();
    }

    public function obtenerPagosPorContrato($idcontrato)
    {
        $builder = $this->db->table('controlpagos p');
        $builder->select('p.*, tp.tipopago, u.nombreusuario');
        $builder->join('tipospago tp', 'tp.idtipopago = p.idtipopago', 'left');
        $builder->join('usuarios u', 'u.idusuario = p.idusuario', 'left');
        $builder->where('p.idcontrato', $idcontrato);
        $builder->orderBy('p.fechahora', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    public function calcularTotalPagado($idcontrato)
    {
        $result = $this->where('idcontrato', $idcontrato)
                    ->selectSum('amortizacion')
                    ->first();
        return $result['amortizacion'] ?? 0;
    }

    public function obtenerEstadisticasPagos()
    {
        $estadisticas = [
            'total_pagado' => 0,
            'deuda_total' => 0,
            'pagos_count' => 0,
            'por_tipo_pago' => [],
            'contratos_con_deuda' => 0,
            'contratos_pagados' => 0
        ];

        // Obtener total pagado y conteo de pagos
        $result = $this->selectSum('amortizacion')->selectCount('idpagos')->first();
        $estadisticas['total_pagado'] = $result['amortizacion'] ?? 0;
        $estadisticas['pagos_count'] = $result['idpagos'] ?? 0;

        // Obtener pagos por tipo
        $builder = $this->db->table('controlpagos p');
        $builder->select('tp.tipopago, SUM(p.amortizacion) as total');
        $builder->join('tipospago tp', 'tp.idtipopago = p.idtipopago', 'left');
        $builder->groupBy('p.idtipopago');
        $result = $builder->get()->getResultArray();
        
        foreach ($result as $row) {
            $estadisticas['por_tipo_pago'][$row['tipopago']] = $row['total'];
        }

        // Obtener contratos únicos
        $contratos = $this->distinct()->select('idcontrato')->findAll();

        // Calcular deuda total y contar contratos con deuda/pagados
        foreach ($contratos as $contrato) {
            $ultimoPago = $this->obtenerUltimoPagoContrato($contrato['idcontrato']);
            if ($ultimoPago) {
                $estadisticas['deuda_total'] += $ultimoPago['deuda'];
                if ($ultimoPago['deuda'] == 0) {
                    $estadisticas['contratos_pagados']++;
                } else {
                    $estadisticas['contratos_con_deuda']++;
                }
            }
        }

        return $estadisticas;
    }
}