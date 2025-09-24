<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo de Equipos refactorizado aplicando KISS
 * Consultas limpias y sin duplicación
 */
class EquipoModel extends Model
{
    protected $table = 'equipos';
    protected $primaryKey = 'idequipo';
    protected $allowedFields = ['idserviciocontratado', 'idusuario', 'descripcion', 'estadoservicio'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Consulta base con todos los joins necesarios
     * Aplicando DRY: una sola consulta reutilizable
     */
    private function getBaseQuery(): \CodeIgniter\Database\BaseBuilder
    {
        return $this->db->table('equipos e')
            ->select('
                e.idequipo,
                e.idserviciocontratado,
                e.idusuario,
                e.descripcion,
                e.estadoservicio,
                u.nombreusuario,
                p.nombres,
                p.apellidos,
                c.cargo,
                s.servicio,
                sc.direccion,
                sc.fechahoraservicio,
                co.fechaevento,
                te.evento as tipoevento,
                CONCAT(p.nombres, " ", p.apellidos) as nombre_completo
            ')
            ->join('usuarios u', 'e.idusuario = u.idusuario')
            ->join('personas p', 'u.idpersona = p.idpersona')
            ->join('cargos c', 'u.idcargo = c.idcargo')
            ->join('servicioscontratados sc', 'e.idserviciocontratado = sc.idserviciocontratado')
            ->join('servicios s', 'sc.idservicio = s.idservicio')
            ->join('cotizaciones co', 'sc.idcotizacion = co.idcotizacion')
            ->join('tipoeventos te', 'co.idtipoevento = te.idtipoevento', 'left');
    }

    /**
     * Obtiene todos los equipos con detalles completos
     * KISS: método simple y claro
     */
    public function getEquiposConDetalles(): array
    {
        return $this->getBaseQuery()
            ->orderBy('sc.fechahoraservicio', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Obtiene equipos por servicio específico
     * KISS: reutiliza consulta base
     */
    public function getEquiposPorServicio(int $idserviciocontratado): array
    {
        return $this->getBaseQuery()
            ->where('e.idserviciocontratado', $idserviciocontratado)
            ->orderBy('e.estadoservicio')
            ->get()
            ->getResultArray();
    }

    /**
     * Obtiene equipos por usuario específico
     * KISS: reutiliza consulta base
     */
    public function getEquiposPorUsuario(int $idusuario): array
    {
        return $this->getBaseQuery()
            ->where('e.idusuario', $idusuario)
            ->orderBy('sc.fechahoraservicio', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Obtiene un equipo específico con todos los detalles
     * KISS: reutiliza consulta base
     */
    public function getEquipoConDetalles(int $idequipo): ?array
    {
        $resultado = $this->getBaseQuery()
            ->where('e.idequipo', $idequipo)
            ->get()
            ->getRowArray();
            
        return $resultado ?: null;
    }

    /**
     * Obtiene equipos agrupados por estado para el Kanban
     * KISS: método específico para la vista
     */
    public function getEquiposParaKanban(?int $servicioId = null): array
    {
        $query = $this->getBaseQuery();
        
        if ($servicioId) {
            $query->where('e.idserviciocontratado', $servicioId);
        }
        
        $equipos = $query->orderBy('sc.fechahoraservicio', 'ASC')
            ->get()
            ->getResultArray();

        // Agrupar por estado para facilitar el renderizado
        $agrupados = [
            'Pendiente' => [],
            'En Proceso' => [],
            'Completado' => []
        ];

        foreach ($equipos as $equipo) {
            $estado = $equipo['estadoservicio'];
            // Agrupar Programado con Pendiente
            if ($estado === 'Programado') {
                $estado = 'Pendiente';
            }
            
            if (isset($agrupados[$estado])) {
                $agrupados[$estado][] = $equipo;
            }
        }

        return $agrupados;
    }

    /**
     * Obtiene información básica de un servicio contratado
     * KISS: método simple para obtener datos del servicio
     */
    public function getServicioInfo(int $servicioId): ?array
    {
        return $this->db->table('servicioscontratados sc')
            ->select('sc.*, s.servicio, co.fechaevento, te.evento as tipoevento,
                     CASE 
                        WHEN cl.idpersona IS NOT NULL THEN CONCAT(p.nombres, " ", p.apellidos)
                        WHEN cl.idempresa IS NOT NULL THEN e.razonsocial
                        ELSE "Cliente no especificado"
                     END as cliente_nombre')
            ->join('servicios s', 'sc.idservicio = s.idservicio')
            ->join('cotizaciones co', 'sc.idcotizacion = co.idcotizacion')
            ->join('clientes cl', 'co.idcliente = cl.idcliente')
            ->join('tipoeventos te', 'co.idtipoevento = te.idtipoevento', 'left')
            ->join('personas p', 'cl.idpersona = p.idpersona', 'left')
            ->join('empresas e', 'cl.idempresa = e.idempresa', 'left')
            ->where('sc.idserviciocontratado', $servicioId)
            ->get()
            ->getRowArray();
    }
}