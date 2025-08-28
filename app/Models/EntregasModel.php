<?php

namespace App\Models;

use CodeIgniter\Model;

class EntregasModel extends Model
{
    protected $table = 'entregables';
    protected $primaryKey = 'identregable';
    protected $allowedFields = ['idserviciocontratado', 'idpersona', 'fechahoraentrega'];
    protected $returnType = 'array';
    
    public function obtenerEntregasCompletas()
    {
        $builder = $this->db->table('entregables e');
        $builder->select('e.identregable, e.fechahoraentrega, 
                         p.nombres as nombre_cliente, p.apellidos as apellido_cliente,
                         s.servicio, sc.direccion, sc.fechahoraservicio,
                         per.nombres as nombre_entrega, per.apellidos as apellido_entrega,
                         sc.cantidad, sc.precio');
        $builder->join('servicioscontratados sc', 'sc.idserviciocontratado = e.idserviciocontratado');
        $builder->join('cotizaciones c', 'c.idcotizacion = sc.idcotizacion');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente');
        $builder->join('personas p', 'p.idpersona = cl.idpersona');
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $builder->join('personas per', 'per.idpersona = e.idpersona', 'left');
        $builder->orderBy('e.fechahoraentrega', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    public function obtenerEntregaCompleta($id)
    {
        $builder = $this->db->table('entregables e');
        $builder->select('e.identregable, e.fechahoraentrega, 
                         p.nombres as nombre_cliente, p.apellidos as apellido_cliente,
                         p.tipodoc, p.numerodoc, p.telprincipal, p.direccion,
                         s.servicio, s.descripcion as descripcion_servicio,
                         sc.direccion as direccion_servicio, sc.fechahoraservicio,
                         sc.cantidad, sc.precio,
                         per.nombres as nombre_entrega, per.apellidos as apellido_entrega,
                         per.tipodoc as tipodoc_entrega, per.numerodoc as numerodoc_entrega,
                         c.idcotizacion, contr.idcontrato');
        $builder->join('servicioscontratados sc', 'sc.idserviciocontratado = e.idserviciocontratado');
        $builder->join('cotizaciones c', 'c.idcotizacion = sc.idcotizacion');
        $builder->join('contratos contr', 'contr.idcotizacion = c.idcotizacion', 'left');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente');
        $builder->join('personas p', 'p.idpersona = cl.idpersona');
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $builder->join('personas per', 'per.idpersona = e.idpersona', 'left');
        $builder->where('e.identregable', $id);
        
        return $builder->get()->getRowArray();
    }
}