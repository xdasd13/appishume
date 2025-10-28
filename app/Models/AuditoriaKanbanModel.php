<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditoriaKanbanModel extends Model
{
    protected $table = 'auditoria_kanban';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [
        'idequipo', 'idusuario', 'accion', 
        'estado_anterior', 'estado_nuevo'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'fecha';

    /**
     * Registrar un cambio en la auditoría
     */
    public function registrarCambio(int $idequipo, int $idusuario, string $accion, ?string $estadoAnterior = null, ?string $estadoNuevo = null): bool
    {
        try {
            // Usar NOW() de MySQL para evitar problemas de zona horaria
            $db = \Config\Database::connect();
            $fechaActual = $db->query("SELECT NOW() as fecha")->getRow()->fecha;
            
            $data = [
                'idequipo' => $idequipo,
                'idusuario' => $idusuario,
                'accion' => $accion,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $estadoNuevo,
                'fecha' => $fechaActual
            ];
            
            log_message('info', "Registrando auditoría con fecha: {$fechaActual}");

            $result = $this->insert($data);
            
            if ($result) {
                log_message('info', "Auditoría registrada: Equipo {$idequipo}, Usuario {$idusuario}, Acción {$accion}");
                return true;
            } else {
                log_message('error', "Error al registrar auditoría: " . json_encode($data));
                return false;
            }
            
        } catch (\Exception $e) {
            log_message('error', "Excepción al registrar auditoría: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener historial completo con información de usuarios y servicios
     */
    public function getHistorialCompleto($filtroFecha = null, $filtroUsuario = null, $limite = 50)
    {
        log_message('info', "getHistorialCompleto - Filtros recibidos: fecha={$filtroFecha}, usuario={$filtroUsuario}, limite={$limite}");
        log_message('info', "Fecha actual del servidor: " . date('Y-m-d H:i:s'));
        
        $builder = $this->db->table('auditoria_kanban a');
        $builder->select('
            a.id,
            a.fecha,
            a.accion,
            a.estado_anterior,
            a.estado_nuevo,
            CONCAT(p.nombres, " ", p.apellidos) as usuario_nombre,
            u.nombreusuario,
            s.servicio,
            eq.descripcion as equipo_descripcion,
            cat.categoria,
            cot.fechaevento,
            CASE 
                WHEN cl_p.idpersona IS NOT NULL THEN CONCAT(cl_p.apellidos, ", ", cl_p.nombres)
                WHEN emp.idempresa IS NOT NULL THEN emp.razonsocial
                ELSE "Cliente no identificado"
            END as cliente_nombre
        ');
        
        $builder->join('usuarios u', 'a.idusuario = u.idusuario');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->join('equipos eq', 'a.idequipo = eq.idequipo');
        $builder->join('servicioscontratados sc', 'eq.idserviciocontratado = sc.idserviciocontratado');
        $builder->join('servicios s', 'sc.idservicio = s.idservicio');
        $builder->join('categorias cat', 's.idcategoria = cat.idcategoria');
        $builder->join('cotizaciones cot', 'sc.idcotizacion = cot.idcotizacion');
        $builder->join('clientes cl', 'cot.idcliente = cl.idcliente');
        $builder->join('personas cl_p', 'cl.idpersona = cl_p.idpersona', 'left');
        $builder->join('empresas emp', 'cl.idempresa = emp.idempresa', 'left');

        // Aplicar filtros de fecha
        if ($filtroFecha) {
            switch ($filtroFecha) {
                case 'hoy':
                    // Registros de hoy: desde las 00:00:00 hasta las 23:59:59
                    $builder->where('DATE(a.fecha) =', date('Y-m-d'));
                    break;
                case 'ayer':
                    // Registros de ayer
                    $builder->where('DATE(a.fecha) =', date('Y-m-d', strtotime('-1 day')));
                    break;
                case 'semana':
                    // Últimos 7 días (incluyendo hoy)
                    $fechaInicio = date('Y-m-d', strtotime('-7 days'));
                    $fechaFin = date('Y-m-d');
                    $builder->where('DATE(a.fecha) >=', $fechaInicio);
                    $builder->where('DATE(a.fecha) <=', $fechaFin);
                    break;
                case 'mes':
                    // Últimos 30 días (incluyendo hoy)
                    $fechaInicio = date('Y-m-d', strtotime('-30 days'));
                    $fechaFin = date('Y-m-d');
                    $builder->where('DATE(a.fecha) >=', $fechaInicio);
                    $builder->where('DATE(a.fecha) <=', $fechaFin);
                    break;
            }
        }

        if ($filtroUsuario && $filtroUsuario !== 'todos') {
            $builder->where('a.idusuario', $filtroUsuario);
        }

        $builder->orderBy('a.fecha', 'DESC');
        $builder->limit($limite);

        // Log de la query SQL generada para debugging
        $sql = $builder->getCompiledSelect(false);
        log_message('info', "Query SQL generada: " . $sql);
        
        $resultado = $builder->get()->getResult();
        log_message('info', "Registros encontrados: " . count($resultado));
        
        return $resultado;
    }

    /**
     * Obtener estadísticas de actividad
     */
    public function getEstadisticas($periodo = 'mes')
    {
        $fechaInicio = match($periodo) {
            'hoy' => date('Y-m-d'),
            'semana' => date('Y-m-d', strtotime('-7 days')),
            'mes' => date('Y-m-d', strtotime('-30 days')),
            default => date('Y-m-d', strtotime('-30 days'))
        };

        $builder = $this->db->table('auditoria_kanban a');
        $builder->select('
            COUNT(*) as total_cambios,
            COUNT(DISTINCT a.idusuario) as usuarios_activos,
            COUNT(DISTINCT a.idequipo) as equipos_modificados
        ');
        $builder->where('DATE(a.fecha) >=', $fechaInicio);

        return $builder->get()->getRow();
    }

    /**
     * Obtener usuarios que han realizado cambios
     */
    public function getUsuariosActivos()
    {
        $builder = $this->db->table('auditoria_kanban a');
        $builder->select('
            u.idusuario,
            u.nombreusuario,
            CONCAT(p.nombres, " ", p.apellidos) as nombre_completo,
            COUNT(*) as total_cambios
        ');
        $builder->join('usuarios u', 'a.idusuario = u.idusuario');
        $builder->join('personas p', 'u.idpersona = p.idpersona');
        $builder->groupBy('u.idusuario');
        $builder->orderBy('total_cambios', 'DESC');

        return $builder->get()->getResult();
    }
}
