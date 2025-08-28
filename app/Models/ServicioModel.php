<?php

namespace App\Models;

use CodeIgniter\Model;

class ServicioModel extends Model
{
    protected $table = 'servicioscontratados';
    protected $primaryKey = 'idserviciocontratado';

    // Obtener servicios contratados
    public function getServiciosContratados()
    {
        $builder = $this->db->table('servicioscontratados sc');
        $builder->select('sc.*, s.servicio, c.razonsocial, p.nombres, p.apellidos, co.fechaevento');
        $builder->join('servicios s', 'sc.idservicio = s.idservicio');
        $builder->join('cotizaciones co', 'sc.idcotizacion = co.idcotizacion');
        $builder->join('clientes cl', 'co.idcliente = cl.idcliente');
        $builder->join('empresas c', 'cl.idempresa = c.idempresa', 'left');
        $builder->join('personas p', 'cl.idpersona = p.idpersona', 'left');
        return $builder->get()->getResult();
    }

    // Obtener un servicio contratado específico
    public function getServicioContratado($idserviciocontratado)
    {
        $builder = $this->db->table('servicioscontratados sc');
        $builder->select('sc.*, s.servicio, c.razonsocial, p.nombres, p.apellidos, co.fechaevento, te.evento as tipo_evento');
        $builder->join('servicios s', 'sc.idservicio = s.idservicio');
        $builder->join('cotizaciones co', 'sc.idcotizacion = co.idcotizacion');
        $builder->join('tipoeventos te', 'co.idtipoevento = te.idtipoevento');
        $builder->join('clientes cl', 'co.idcliente = cl.idcliente');
        $builder->join('empresas c', 'cl.idempresa = c.idempresa', 'left');
        $builder->join('personas p', 'cl.idpersona = p.idpersona', 'left');
        $builder->where('sc.idserviciocontratado', $idserviciocontratado);
        return $builder->get()->getRow();
    }
}