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
        'estado',
        'comprobante_entrega'
    ];
    protected $returnType = 'array';

    public function obtenerEntregasCompletas()
    {
        $builder = $this->db->table('entregables e');
        $builder->select('e.identregable, e.fechahoraentrega, e.fecha_real_entrega, e.estado, e.observaciones, e.comprobante_entrega,
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
        $builder->join('personas p', 'p.idpersona = cl.idpersona', 'left');
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $builder->join('personas per', 'per.idpersona = e.idpersona', 'left');
        $builder->orderBy('e.fechahoraentrega', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function obtenerEntregasPendientes()
    {
        $builder = $this->db->table('entregables e');
        $builder->select('e.identregable, e.fechahoraentrega, e.fecha_real_entrega, e.estado, e.observaciones, e.comprobante_entrega,
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

    public function obtenerEntregaCompleta($id)
    {
        // Consulta mejorada para obtener toda la información del cliente y del responsable
        $builder = $this->db->table('entregables e');
        $builder->select('e.identregable, e.fechahoraentrega, e.fecha_real_entrega, e.estado, e.observaciones, e.comprobante_entrega,
                    e.idpersona,
                    COALESCE(
                        CASE 
                            WHEN cl.idempresa IS NOT NULL THEN emp.razonsocial
                            ELSE CONCAT(p.nombres, " ", p.apellidos)
                        END, 
                        "No disponible"
                    ) as nombre_cliente,
                    CASE 
                        WHEN cl.idempresa IS NOT NULL THEN ""
                        ELSE COALESCE(p.apellidos, "")
                    END as apellido_cliente,
                    COALESCE(
                        CASE 
                            WHEN cl.idempresa IS NOT NULL THEN "RUC"
                            ELSE p.tipodoc
                        END, 
                        ""
                    ) as tipodoc,
                    COALESCE(
                        CASE 
                            WHEN cl.idempresa IS NOT NULL THEN emp.ruc
                            ELSE p.numerodoc
                        END, 
                        ""
                    ) as numerodoc,
                    COALESCE(
                        CASE 
                            WHEN cl.idempresa IS NOT NULL THEN emp.telefono
                            ELSE p.telprincipal
                        END, 
                        ""
                    ) as telprincipal,
                    COALESCE(
                        CASE 
                            WHEN cl.idempresa IS NOT NULL THEN emp.direccion
                            ELSE p.direccion
                        END, 
                        ""
                    ) as direccion,
                    COALESCE(s.servicio, "No disponible") as servicio, 
                    COALESCE(s.descripcion, "No disponible") as descripcion_servicio,
                    COALESCE(sc.direccion, "") as direccion_servicio, 
                    COALESCE(sc.fechahoraservicio, NOW()) as fechahoraservicio,
                    COALESCE(sc.cantidad, 0) as cantidad, 
                    COALESCE(sc.precio, 0) as precio,
                    COALESCE(per.nombres, "Sin nombre") as nombre_entrega, 
                    COALESCE(per.apellidos, "Sin apellido") as apellido_entrega,
                    COALESCE(per.tipodoc, "") as tipodoc_entrega, 
                    COALESCE(per.numerodoc, "") as numerodoc_entrega,
                    COALESCE(c.idcotizacion, 0) as idcotizacion, 
                    COALESCE(contr.idcontrato, 0) as idcontrato,
                    COALESCE(DATEDIFF(e.fechahoraentrega, sc.fechahoraservicio), 0) as dias_postproduccion,
                    COALESCE(DATEDIFF(e.fechahoraentrega, NOW()), 0) as dias_restantes');
        $builder->join('servicioscontratados sc', 'sc.idserviciocontratado = e.idserviciocontratado', 'left');
        $builder->join('cotizaciones c', 'c.idcotizacion = sc.idcotizacion', 'left');
        $builder->join('contratos contr', 'contr.idcotizacion = c.idcotizacion', 'left');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente', 'left');
        $builder->join('personas p', 'p.idpersona = cl.idpersona', 'left');
        $builder->join('empresas emp', 'emp.idempresa = cl.idempresa', 'left');
        $builder->join('servicios s', 's.idservicio = sc.idservicio', 'left');
        // Aseguramos que siempre se obtenga la información del responsable
        $builder->join('personas per', 'per.idpersona = e.idpersona', 'left');
        $builder->where('e.identregable', $id);

        $entrega = $builder->get()->getRowArray();

        // Agregar el estado_visual si existe la entrega
        if ($entrega) {
            if ($entrega['estado'] == 'completada') {
                $entrega['estado_visual'] = "✅ ENTREGADO";
            } else if ($entrega['estado'] == 'pendiente' && strtotime($entrega['fechahoraentrega']) < time()) {
                $entrega['estado_visual'] = "⚠️ VENCIDA";
            } else if ($entrega['estado'] == 'pendiente') {
                $entrega['estado_visual'] = "⏳ EN POSTPRODUCCIÓN";
            } else {
                $entrega['estado_visual'] = "❓ DESCONOCIDO";
            }
        }

        return $entrega;
    }

    public function obtenerContratosConEstadoPago()
    {
        // Esta consulta es más directa para verificar el estado de pago
        $builder = $this->db->table('contratos c');
        $builder->select('
            c.idcontrato, c.idcotizacion,
            CONCAT(COALESCE(p.nombres, emp.razonsocial), " ", COALESCE(p.apellidos, "")) as cliente_nombre,
            co.fechaevento, te.evento as tipo_evento,
            (SELECT SUM(sc.cantidad * sc.precio) FROM servicioscontratados sc WHERE sc.idcotizacion = c.idcotizacion) as monto_total,
            (SELECT SUM(cp.amortizacion) FROM controlpagos cp WHERE cp.idcontrato = c.idcontrato) as monto_pagado,
            (SELECT COUNT(*) FROM entregables e JOIN servicioscontratados sc ON sc.idserviciocontratado = e.idserviciocontratado 
             WHERE sc.idcotizacion = c.idcotizacion) as total_entregas
        ');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente');
        $builder->join('personas p', 'p.idpersona = cl.idpersona', 'left');
        $builder->join('empresas emp', 'emp.idempresa = cl.idempresa', 'left');
        $builder->join('cotizaciones co', 'co.idcotizacion = c.idcotizacion');
        $builder->join('tipoeventos te', 'te.idtipoevento = co.idtipoevento');
        $builder->orderBy('co.fechaevento', 'DESC');

        $contratos = $builder->get()->getResultArray();

        // Calcular deuda manualmente para mayor precisión
        foreach ($contratos as &$contrato) {
            $contrato['deuda_actual'] = $contrato['monto_total'] - $contrato['monto_pagado'];
            // Pequeña tolerancia para evitar problemas de redondeo
            if ($contrato['deuda_actual'] < 0.01) {
                $contrato['deuda_actual'] = 0;
            }
        }

        return $contratos;
    }

    public function obtenerContratosPagadosCompletos()
    {
        $builder = $this->db->table('contratos c');
        $builder->select('c.idcontrato, c.idcotizacion, c.idcliente,
                         cl.idpersona, cl.idempresa,
                         CONCAT(COALESCE(p.nombres, emp.razonsocial), " ", COALESCE(p.apellidos, "")) as cliente_nombre,
                         co.fechaevento,
                         (SELECT SUM(sc.cantidad * sc.precio) FROM servicioscontratados sc WHERE sc.idcotizacion = c.idcotizacion) as monto_total,
                         (SELECT SUM(cp.amortizacion) FROM controlpagos cp WHERE cp.idcontrato = c.idcontrato) as monto_pagado');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente');
        $builder->join('personas p', 'p.idpersona = cl.idpersona', 'left');
        $builder->join('empresas emp', 'emp.idempresa = cl.idempresa', 'left');
        $builder->join('cotizaciones co', 'co.idcotizacion = c.idcotizacion');

        $contratos = $builder->get()->getResultArray();

        // Filtrar manualmente solo los contratos pagados
        $contratosPagados = [];
        foreach ($contratos as $contrato) {
            $deuda = $contrato['monto_total'] - $contrato['monto_pagado'];
            if ($deuda < 0.01) { // Tolerancia para redondeo
                $contrato['deuda_actual'] = 0;
                $contratosPagados[] = $contrato;
            }
        }

        return $contratosPagados;
    }

    public function obtenerServiciosPorContratoPagado($idcontrato)
    {
        $builder = $this->db->table('servicioscontratados sc');
        $builder->select('sc.idserviciocontratado, sc.idservicio, sc.cantidad, sc.precio, 
                         sc.fechahoraservicio, sc.direccion,
                         s.servicio, s.descripcion,
                         c.idcotizacion, c.fechaevento');
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $builder->join('cotizaciones c', 'c.idcotizacion = sc.idcotizacion');
        $builder->join('contratos cont', 'cont.idcotizacion = c.idcotizacion');
        $builder->where('cont.idcontrato', $idcontrato);
        $builder->where('sc.idserviciocontratado NOT IN (
            SELECT e.idserviciocontratado FROM entregables e WHERE e.idserviciocontratado IS NOT NULL
        )');
        $builder->orderBy('sc.fechahoraservicio', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function obtenerContratoPagado($idcontrato)
    {
        $builder = $this->db->table('contratos c');
        $builder->select('
            c.idcontrato, c.idcotizacion,
            (SELECT SUM(sc.cantidad * sc.precio) FROM servicioscontratados sc WHERE sc.idcotizacion = c.idcotizacion) as monto_total,
            (SELECT SUM(cp.amortizacion) FROM controlpagos cp WHERE cp.idcontrato = c.idcontrato) as monto_pagado
        ');
        $builder->where('c.idcontrato', $idcontrato);
        $contrato = $builder->get()->getRowArray();

        if ($contrato) {
            $contrato['deuda_actual'] = $contrato['monto_total'] - $contrato['monto_pagado'];
            // Pequeña tolerancia para evitar problemas de redondeo
            if ($contrato['deuda_actual'] < 0.01) {
                $contrato['deuda_actual'] = 0;
                return $contrato;
            }
        }
        return null;
    }

    public function obtenerEntregasCompletasConDetalle()
    {
        $builder = $this->db->table('entregables e');
        $builder->select('e.identregable, e.fechahoraentrega, e.fecha_real_entrega, e.estado, e.observaciones, e.comprobante_entrega,
                     p.nombres as nombre_cliente, p.apellidos as apellido_cliente,
                     s.servicio, sc.direccion, sc.fechahoraservicio,
                     per.nombres as nombre_entrega, per.apellidos as apellido_entrega,
                     sc.cantidad, sc.precio, contr.idcontrato,
                     CASE 
                         WHEN e.estado = "completada" THEN "Completada"
                         WHEN e.estado = "pendiente" AND e.fechahoraentrega < NOW() THEN "Vencida"
                         WHEN e.estado = "pendiente" THEN "Pendiente"
                         ELSE "Desconocido"
                     END as estado_visual');
        $builder->join('servicioscontratados sc', 'sc.idserviciocontratado = e.idserviciocontratado', 'left');
        $builder->join('cotizaciones c', 'c.idcotizacion = sc.idcotizacion', 'left');
        $builder->join('contratos contr', 'contr.idcotizacion = c.idcotizacion', 'left');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente', 'left');
        $builder->join('personas p', 'p.idpersona = cl.idpersona', 'left');
        $builder->join('servicios s', 's.idservicio = sc.idservicio', 'left');
        $builder->join('personas per', 'per.idpersona = e.idpersona', 'left');

        // NO filtrar por estado para mostrar todas las entregas
        $builder->orderBy('e.fechahoraentrega', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function obtenerTodasLasEntregas()
    {
        // Consulta mejorada para obtener todos los datos necesarios
        $sql = "SELECT e.identregable, e.fechahoraentrega, e.fecha_real_entrega, e.estado, 
                       e.observaciones, e.comprobante_entrega, e.idpersona,
                       sc.fechahoraservicio, sc.cantidad, sc.precio,
                       s.servicio, s.descripcion as descripcion_servicio,
                       contr.idcontrato, c.idcotizacion,
                       
                       /* Cliente info - puede ser persona o empresa */
                       CASE 
                           WHEN cl.idempresa IS NOT NULL THEN emp.razonsocial
                           WHEN cl.idpersona IS NOT NULL THEN p_cliente.nombres
                           ELSE 'Sin nombre'
                       END as nombre_cliente,
                       
                       CASE 
                           WHEN cl.idempresa IS NOT NULL THEN ''
                           WHEN cl.idpersona IS NOT NULL THEN p_cliente.apellidos
                           ELSE ''
                       END as apellido_cliente,
                       
                       /* Responsable info - siempre es una persona */
                       CASE 
                           WHEN p_entrega.idpersona IS NOT NULL THEN p_entrega.nombres
                           ELSE 'Sin nombre'
                       END as nombre_entrega,
                       
                       CASE 
                           WHEN p_entrega.idpersona IS NOT NULL THEN p_entrega.apellidos
                           ELSE ''
                       END as apellido_entrega,
                       
                       CASE
                           WHEN p_entrega.idpersona IS NOT NULL THEN p_entrega.tipodoc
                           ELSE ''
                       END as tipodoc_entrega,
                       
                       CASE
                           WHEN p_entrega.idpersona IS NOT NULL THEN p_entrega.numerodoc
                           ELSE ''
                       END as numerodoc_entrega,
                       
                       CASE 
                           WHEN e.estado = 'completada' THEN '✅ ENTREGADO'
                           WHEN e.estado = 'pendiente' AND e.fechahoraentrega < NOW() THEN '⚠️ VENCIDA'
                           WHEN e.estado = 'pendiente' THEN '⏳ EN POSTPRODUCCIÓN'
                           ELSE '❓ DESCONOCIDO'
                       END as estado_visual
                       
                FROM entregables e
                LEFT JOIN servicioscontratados sc ON sc.idserviciocontratado = e.idserviciocontratado
                LEFT JOIN servicios s ON s.idservicio = sc.idservicio
                LEFT JOIN cotizaciones c ON c.idcotizacion = sc.idcotizacion
                LEFT JOIN contratos contr ON contr.idcotizacion = c.idcotizacion
                LEFT JOIN clientes cl ON cl.idcliente = c.idcliente
                LEFT JOIN personas p_cliente ON p_cliente.idpersona = cl.idpersona
                LEFT JOIN empresas emp ON emp.idempresa = cl.idempresa
                LEFT JOIN personas p_entrega ON p_entrega.idpersona = e.idpersona
                ORDER BY e.identregable DESC";

        $query = $this->db->query($sql);
        $entregas = $query->getResultArray();
        
        // Si hay entregas sin información del responsable, usamos la sesión actual
        $session = \Config\Services::session();
        $usuarioActual = $session->get('usuario_nombre');
        $usuarioId = $session->get('usuario_id');
        
        // Si tenemos el ID del usuario, intentamos obtener sus datos completos
        $personaInfo = null;
        if ($usuarioId) {
            $personaInfo = $this->db->table('usuarios u')
                ->select('p.nombres, p.apellidos, p.tipodoc, p.numerodoc')
                ->join('personas p', 'p.idpersona = u.idpersona')
                ->where('u.idusuario', $usuarioId)
                ->get()
                ->getRowArray();
        }
        
        foreach ($entregas as &$entrega) {
            if (empty($entrega['nombre_entrega']) || $entrega['nombre_entrega'] == 'Sin nombre') {
                if ($personaInfo) {
                    // Si tenemos datos completos del usuario actual, los usamos
                    $entrega['nombre_entrega'] = $personaInfo['nombres'];
                    $entrega['apellido_entrega'] = $personaInfo['apellidos'];
                    $entrega['tipodoc_entrega'] = $personaInfo['tipodoc'];
                    $entrega['numerodoc_entrega'] = $personaInfo['numerodoc'];
                } else {
                    // Si no, usamos al menos el nombre de usuario
                    $entrega['nombre_entrega'] = $usuarioActual ?? 'Usuario actual';
                    $entrega['apellido_entrega'] = '';
                }
            }
        }
        
        return $entregas;
    }
}