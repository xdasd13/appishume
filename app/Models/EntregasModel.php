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
        // Consulta mejorada y consistente
        $builder = $this->db->table('contratos c');
        $builder->select('
            c.idcontrato, c.idcotizacion,
            CONCAT(COALESCE(p.nombres, emp.razonsocial), " ", COALESCE(p.apellidos, "")) as cliente_nombre,
            co.fechaevento, te.evento as tipo_evento,
            (SELECT SUM(sc.cantidad * sc.precio) FROM servicioscontratados sc WHERE sc.idcotizacion = c.idcotizacion) as monto_total,
            (SELECT SUM(cp.amortizacion) FROM controlpagos cp WHERE cp.idcontrato = c.idcontrato) as monto_pagado,
            (SELECT COUNT(*) FROM entregables e JOIN servicioscontratados sc ON sc.idserviciocontratado = e.idserviciocontratado 
             WHERE sc.idcotizacion = c.idcotizacion) as total_entregas,
            (SELECT COUNT(*) FROM entregables e JOIN servicioscontratados sc ON sc.idserviciocontratado = e.idserviciocontratado 
             WHERE sc.idcotizacion = c.idcotizacion AND e.estado = "completada") as entregas_completadas,
            (SELECT COUNT(*) FROM servicioscontratados sc WHERE sc.idcotizacion = c.idcotizacion) as total_servicios
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
            // Asegurar que los valores sean numéricos
            $montoTotal = floatval($contrato['monto_total'] ?? 0);
            $montoPagado = floatval($contrato['monto_pagado'] ?? 0);
            
            $contrato['deuda_actual'] = $montoTotal - $montoPagado;
            
            // Tolerancia para evitar problemas de redondeo
            if ($contrato['deuda_actual'] < 0.01) {
                $contrato['deuda_actual'] = 0;
            }
            
            // Determinar si todos los servicios están completados
            $totalEntregas = intval($contrato['total_entregas'] ?? 0);
            $entregasCompletadas = intval($contrato['entregas_completadas'] ?? 0);
            $totalServicios = intval($contrato['total_servicios'] ?? 0);
            
            // Verificar si todos los servicios están completados
            if ($totalServicios > 0 && $totalServicios == $entregasCompletadas) {
                $contrato['todos_servicios_completados'] = true;
                $contrato['estado_entregas'] = 'Entregas completadas';
            } else if ($totalEntregas > 0) {
                $contrato['todos_servicios_completados'] = false;
                $contrato['estado_entregas'] = $entregasCompletadas . ' de ' . $totalServicios . ' servicios';
            } else {
                $contrato['todos_servicios_completados'] = false;
                $contrato['estado_entregas'] = 'Sin entregas';
            }
            
            // Estado del pago para mostrar en la interfaz
            if ($contrato['deuda_actual'] <= 0.01) {
                $contrato['estado_pago'] = 'pagado';
            } else {
                $contrato['estado_pago'] = 'pendiente';
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
                         (SELECT SUM(cp.amortizacion) FROM controlpagos cp WHERE cp.idcontrato = c.idcontrato) as monto_pagado,
                         (SELECT COUNT(*) FROM servicioscontratados sc WHERE sc.idcotizacion = c.idcotizacion) as total_servicios,
                         (SELECT COUNT(*) FROM entregables e 
                          JOIN servicioscontratados sc ON sc.idserviciocontratado = e.idserviciocontratado 
                          WHERE sc.idcotizacion = c.idcotizacion) as total_entregas');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente');
        $builder->join('personas p', 'p.idpersona = cl.idpersona', 'left');
        $builder->join('empresas emp', 'emp.idempresa = cl.idempresa', 'left');
        $builder->join('cotizaciones co', 'co.idcotizacion = c.idcotizacion');
        
        // Filtrar solo contratos con fecha de evento hasta hoy
        $builder->where('co.fechaevento <=', date('Y-m-d'));

        $contratos = $builder->get()->getResultArray();

        // Filtrar manualmente solo los contratos pagados Y con entregas pendientes
        $contratosPagados = [];
        foreach ($contratos as $contrato) {
            $deuda = $contrato['monto_total'] - $contrato['monto_pagado'];
            $totalServicios = intval($contrato['total_servicios']);
            $totalEntregas = intval($contrato['total_entregas']);
            
            // Solo incluir si está pagado Y tiene entregas pendientes
            if ($deuda < 0.01 && $totalServicios > $totalEntregas) {
                $contrato['deuda_actual'] = 0;
                $contrato['entregas_pendientes'] = $totalServicios - $totalEntregas;
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
            CONCAT(COALESCE(p.nombres, emp.razonsocial), " ", COALESCE(p.apellidos, "")) as cliente_nombre,
            co.fechaevento, te.evento as tipo_evento,
            (SELECT SUM(sc.cantidad * sc.precio) FROM servicioscontratados sc WHERE sc.idcotizacion = c.idcotizacion) as monto_total,
            (SELECT SUM(cp.amortizacion) FROM controlpagos cp WHERE cp.idcontrato = c.idcontrato) as monto_pagado
        ');
        $builder->join('cotizaciones co', 'co.idcotizacion = c.idcotizacion');
        $builder->join('clientes cl', 'cl.idcliente = c.idcliente');
        $builder->join('personas p', 'p.idpersona = cl.idpersona', 'left');
        $builder->join('empresas emp', 'emp.idempresa = cl.idempresa', 'left');
        $builder->join('tipoeventos te', 'te.idtipoevento = co.idtipoevento');
        $builder->where('c.idcontrato', $idcontrato);
        $contrato = $builder->get()->getRowArray();

        if ($contrato) {
            // Asegurar que los valores sean numéricos
            $montoTotal = floatval($contrato['monto_total'] ?? 0);
            $montoPagado = floatval($contrato['monto_pagado'] ?? 0);
            
            $contrato['deuda_actual'] = $montoTotal - $montoPagado;
            
            // Tolerancia más amplia para evitar problemas de redondeo
            if ($contrato['deuda_actual'] < 0.1) {
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
                       DATEDIFF(e.fechahoraentrega, sc.fechahoraservicio) as dias_postproduccion,
                       CASE 
                           WHEN e.estado = 'completada' THEN '✅ Entregada'
                           WHEN e.estado = 'pendiente' THEN '⏳ Pendiente'
                           ELSE '❓ Desconocido'
                       END as estado_visual,
                       
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
                           WHEN p_entrega.nombres IS NOT NULL AND p_entrega.nombres != '' THEN p_entrega.nombres
                           WHEN e.idpersona IS NULL THEN 'Usuario del Sistema'
                           ELSE 'Sin nombre'
                       END as nombre_entrega,
                       
                       CASE 
                           WHEN p_entrega.apellidos IS NOT NULL AND p_entrega.apellidos != '' THEN p_entrega.apellidos
                           WHEN e.idpersona IS NULL THEN ''
                           ELSE ''
                       END as apellido_entrega,
                       
                       CASE
                           WHEN p_entrega.tipodoc IS NOT NULL AND p_entrega.tipodoc != '' THEN p_entrega.tipodoc
                           ELSE ''
                       END as tipodoc_entrega,
                       
                       CASE
                           WHEN p_entrega.numerodoc IS NOT NULL AND p_entrega.numerodoc != '' THEN p_entrega.numerodoc
                           ELSE ''
                       END as numerodoc_entrega
                       
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
        
        return $entregas;
    }

    // Método para actualizar entregas existentes sin responsable
    public function actualizarEntregasSinResponsable()
    {
        // Buscar entregas que no tienen idpersona asignado
        $entregasSinResponsable = $this->db->query("
            SELECT e.identregable, e.idpersona 
            FROM entregables e 
            WHERE e.idpersona IS NULL
        ")->getResultArray();

        $actualizadas = 0;
        foreach ($entregasSinResponsable as $entrega) {
            // Por ahora, asignar un valor por defecto o dejarlo como está
            // En el futuro se podría asignar al usuario administrador
            $this->db->query("
                UPDATE entregables 
                SET idpersona = 1 
                WHERE identregable = ?
            ", [$entrega['identregable']]);
            $actualizadas++;
        }

        return $actualizadas;
    }
}