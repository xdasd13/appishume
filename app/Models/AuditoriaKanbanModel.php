<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo de Auditoría del Kanban
 * 
 * Gestiona el registro de cambios realizados en el tablero Kanban de equipos.
 * Registra acciones como: crear, cambiar_estado, reasignar.
 * 
 * @author ISHUME Team
 * @version 2.0
 */
class AuditoriaKanbanModel extends Model
{
    protected $table = 'auditoria_kanban';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [
        'idequipo', 
        'idusuario', 
        'accion', 
        'estado_anterior', 
        'estado_nuevo'
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
     * Obtener todo el historial de actividades con información completa
     * 
     * Recupera todos los cambios realizados en el tablero Kanban con información
     * de usuarios, equipos, servicios y clientes.
     * 
     * @param string $filtroUsuario ID del usuario para filtrar o 'todos'
     * @param int $limite Número máximo de registros a retornar
     * @return array Lista de actividades
     */
    public function obtenerTodoElHistorial(string $filtroUsuario = 'todos', int $limite = 100): array
    {
        $builder = $this->db->table('auditoria_kanban a');
        
        // Seleccionar campos necesarios
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
        
        // Joins para obtener información relacionada
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

        // Aplicar filtro de usuario si no es 'todos'
        if ($filtroUsuario !== 'todos') {
            $builder->where('a.idusuario', $filtroUsuario);
        }

        // Ordenar por fecha descendente (más recientes primero)
        $builder->orderBy('a.fecha', 'DESC');
        $builder->limit($limite);
        
        return $builder->get()->getResult();
    }

    /**
     * Obtener lista de usuarios que han realizado cambios
     * 
     * Retorna todos los usuarios que tienen al menos una actividad registrada
     * en el historial, ordenados por número de cambios realizados.
     * 
     * @return array Lista de usuarios activos
     */
    public function obtenerUsuariosActivos(): array
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
