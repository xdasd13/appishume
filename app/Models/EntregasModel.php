<?php

namespace App\Models;

use CodeIgniter\Model;

class EntregasModel extends Model
{
    protected $table = 'entregables';
    protected $primaryKey = 'identregable';
    protected $allowedFields = [
        'idserviciocontratado',
        'idpersona',
        'fechahoraentrega',
        'fecha_real_entrega',
        'observaciones',
        'estado'
    ];
    protected $returnType = 'array';

    public function obtenerEntregasCompletas()
    {
        $builder = $this->db->table('entregables e');
        $builder->select('e.identregable, e.fechahoraentrega, e.fecha_real_entrega, e.estado, e.observaciones,
                         p.nombres as nombre_cliente, p.apellidos as apellido_cliente,
                         s.servicio, sc.direccion, sc.fechahoraservicio,
                         per.nombres as nombre_entrega, per.apellidos as apellido_entrega,
                         sc.cantidad, sc.precio,
                         CASE 
                             WHEN e.estado = "completada" THEN "✅ ENTREGADO"
                             WHEN e.estado = "pendiente" AND e.fechahoraentrega < NOW() THEN "⚠️ VENCIDA"
                             WHEN e.estado = "pendiente" THEN "⏳ EN POSTPRODUCCIÓN"
                             ELSE "❓ DESCONOCIDO"
                         END as estado_visual,
                         DATEDIFF(e.fechahoraentrega, NOW()) as dias_restantes');
        $builder->join('servicioscontratados sc', 'sc.idserviciocontratado = e.idserviciocontratado');
        $builder->join('cotizaciones c', 'c.idcotizacion = sc.idcotizacion');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente');
        $builder->join('personas p', 'p.idpersona = cl.idpersona');
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $builder->join('personas per', 'per.idpersona = e.idpersona', 'left');
        $builder->orderBy('e.fechahoraentrega', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function obtenerEntregasPendientes()
    {
        $builder = $this->db->table('entregables e');
        $builder->select('e.identregable, e.fechahoraentrega, e.fecha_real_entrega, e.estado, e.observaciones,
                         p.nombres as nombre_cliente, p.apellidos as apellido_cliente,
                         s.servicio, sc.direccion, sc.fechahoraservicio,
                         per.nombres as nombre_entrega, per.apellidos as apellido_entrega,
                         sc.cantidad, sc.precio,
                         DATEDIFF(e.fechahoraentrega, sc.fechahoraservicio) as dias_postproduccion,
                         DATEDIFF(e.fechahoraentrega, NOW()) as dias_restantes,
                         CASE 
                             WHEN e.estado = "pendiente" AND e.fechahoraentrega < NOW() THEN "vencida"
                             WHEN e.estado = "pendiente" THEN "pendiente"
                             ELSE "completada"
                         END as estado_entrega');
        $builder->join('servicioscontratados sc', 'sc.idserviciocontratado = e.idserviciocontratado');
        $builder->join('cotizaciones c', 'c.idcotizacion = sc.idcotizacion');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente');
        $builder->join('personas p', 'p.idpersona = cl.idpersona');
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $builder->join('personas per', 'per.idpersona = e.idpersona', 'left');
        
        // SOLO ENTREGAS PENDIENTES (no completadas)
        $builder->where('e.estado', 'pendiente');
        
        $builder->orderBy('e.fechahoraentrega', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function obtenerEntregasVencidas()
    {
        $builder = $this->db->table('entregables e');
        $builder->select('e.identregable, e.fechahoraentrega, e.fecha_real_entrega, e.estado, e.observaciones,
                         p.nombres as nombre_cliente, p.apellidos as apellido_cliente,
                         s.servicio, sc.direccion, sc.fechahoraservicio,
                         per.nombres as nombre_entrega, per.apellidos as apellido_entrega,
                         sc.cantidad, sc.precio,
                         DATEDIFF(NOW(), e.fechahoraentrega) as dias_vencida');
        $builder->join('servicioscontratados sc', 'sc.idserviciocontratado = e.idserviciocontratado');
        $builder->join('cotizaciones c', 'c.idcotizacion = sc.idcotizacion');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente');
        $builder->join('personas p', 'p.idpersona = cl.idpersona');
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $builder->join('personas per', 'per.idpersona = e.idpersona', 'left');
        
        // Entregas pendientes pero con fecha vencida
        $builder->where('e.estado', 'pendiente');
        $builder->where('e.fechahoraentrega <', date('Y-m-d H:i:s'));
        
        $builder->orderBy('e.fechahoraentrega', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function obtenerEntregasCompletadas()
    {
        $builder = $this->db->table('entregables e');
        $builder->select('e.identregable, e.fechahoraentrega, e.fecha_real_entrega, e.estado, e.observaciones,
                         p.nombres as nombre_cliente, p.apellidos as apellido_cliente,
                         s.servicio, sc.direccion, sc.fechahoraservicio,
                         per.nombres as nombre_entrega, per.apellidos as apellido_entrega,
                         sc.cantidad, sc.precio,
                         DATEDIFF(e.fecha_real_entrega, sc.fechahoraservicio) as dias_total_produccion');
        $builder->join('servicioscontratados sc', 'sc.idserviciocontratado = e.idserviciocontratado');
        $builder->join('cotizaciones c', 'c.idcotizacion = sc.idcotizacion');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente');
        $builder->join('personas p', 'p.idpersona = cl.idpersona');
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $builder->join('personas per', 'per.idpersona = e.idpersona', 'left');
        
        // Solo entregas completadas
        $builder->where('e.estado', 'completada');
        
        $builder->orderBy('e.fecha_real_entrega', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function obtenerEntregaCompleta($id)
    {
        $builder = $this->db->table('entregables e');
        $builder->select('e.identregable, e.fechahoraentrega, e.fecha_real_entrega, e.estado, e.observaciones,
                         p.nombres as nombre_cliente, p.apellidos as apellido_cliente,
                         p.tipodoc, p.numerodoc, p.telprincipal, p.direccion,
                         s.servicio, s.descripcion as descripcion_servicio,
                         sc.direccion as direccion_servicio, sc.fechahoraservicio,
                         sc.cantidad, sc.precio,
                         per.nombres as nombre_entrega, per.apellidos as apellido_entrega,
                         per.tipodoc as tipodoc_entrega, per.numerodoc as numerodoc_entrega,
                         c.idcotizacion, contr.idcontrato,
                         DATEDIFF(e.fechahoraentrega, sc.fechahoraservicio) as dias_postproduccion,
                         DATEDIFF(e.fechahoraentrega, NOW()) as dias_restantes,
                         CASE 
                             WHEN e.estado = "completada" THEN "Completada"
                             WHEN e.estado = "pendiente" AND e.fechahoraentrega < NOW() THEN "Vencida"
                             WHEN e.estado = "pendiente" THEN "En Postproducción"
                             ELSE "Desconocido"
                         END as estado_visual');
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

    public function contarEntregasPorEstado()
    {
        $builder = $this->db->table('entregables');
        $builder->select('estado, COUNT(*) as total');
        $builder->groupBy('estado');
        
        $result = $builder->get()->getResultArray();
        
        $contadores = [
            'pendiente' => 0,
            'completada' => 0,
            'total' => 0
        ];
        
        foreach ($result as $row) {
            $contadores[$row['estado']] = $row['total'];
            $contadores['total'] += $row['total'];
        }
        
        return $contadores;
    }

    public function actualizarEstadoEntrega($id, $estado, $fechaReal = null)
    {
        $data = ['estado' => $estado];
        
        if ($fechaReal && $estado == 'completada') {
            $data['fecha_real_entrega'] = $fechaReal;
        }
        
        return $this->update($id, $data);
    }
}