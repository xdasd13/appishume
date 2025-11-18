<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;
use InvalidArgumentException;

class DueReminderService
{
    protected NotificationService $notificationService;
    protected BaseConnection $db;

    public function __construct()
    {
        helper('url');

        $this->notificationService = new NotificationService();
        $this->db = Database::connect();
    }

    /**
     * Ejecuta la búsqueda de proyectos que vencen en 3 días
     * y crea notificaciones para los usuarios asignados.
     */
    public function run(?string $targetDate = null): array
    {
        $date = $this->resolveDate($targetDate);

        $rows = $this->fetchUpcomingProjects($date);

        $summary = [
            'date' => $date,
            'total' => count($rows),
            'created' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        foreach ($rows as $row) {
            try {
                $usuarioId = (int) $row['idusuario'];
                $servicioId = (int) $row['servicio_id'];
                $clienteNombre = $row['cliente_nombre'] ?? 'Cliente';
                $servicioNombre = $row['servicio_nombre'] ?? 'Servicio';
                $url = base_url("/cronograma/proyecto/{$servicioId}");
                $titulo = 'Proyecto próximo a vencer';

                if (!$this->notificationService->shouldCreateReminder($usuarioId, $titulo, $url)) {
                    $summary['skipped']++;
                    continue;
                }

                $this->notificationService->createDueReminder(
                    $usuarioId,
                    $clienteNombre,
                    $servicioNombre,
                    $servicioId
                );

                $summary['created']++;
            } catch (\Throwable $th) {
                log_message('error', 'Error creando recordatorio de vencimiento: ' . $th->getMessage());
                $summary['errors']++;
            }
        }

        log_message('info', sprintf(
            'DueReminderService: fecha %s - evaluados %d, creados %d, saltados %d, errores %d',
            $date,
            $summary['total'],
            $summary['created'],
            $summary['skipped'],
            $summary['errors']
        ));

        return $summary;
    }

    protected function resolveDate(?string $date): string
    {
        if ($date === null || $date === '') {
            return date('Y-m-d', strtotime('+3 days'));
        }

        $dt = \DateTime::createFromFormat('Y-m-d', $date);
        if (!$dt || $dt->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException('La fecha proporcionada no tiene el formato Y-m-d');
        }

        return $date;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function fetchUpcomingProjects(string $date): array
    {
        $sql = "
            SELECT DISTINCT
                eq.idusuario,
                sc.idserviciocontratado AS servicio_id,
                s.servicio AS servicio_nombre,
                CASE 
                    WHEN c.idempresa IS NOT NULL THEN COALESCE(emp.razonsocial, 'Cliente empresa')
                    ELSE CONCAT(COALESCE(per.nombres, 'Cliente'), ' ', COALESCE(per.apellidos, ''))
                END AS cliente_nombre
            FROM servicioscontratados sc
            INNER JOIN servicios s ON sc.idservicio = s.idservicio
            INNER JOIN cotizaciones cot ON sc.idcotizacion = cot.idcotizacion
            INNER JOIN clientes c ON cot.idcliente = c.idcliente
            LEFT JOIN personas per ON c.idpersona = per.idpersona
            LEFT JOIN empresas emp ON c.idempresa = emp.idempresa
            INNER JOIN equipos eq ON sc.idserviciocontratado = eq.idserviciocontratado
            WHERE DATE(sc.fechahoraservicio) = ?
              AND COALESCE(eq.estadoservicio, 'Pendiente') <> 'Completado'
              AND eq.idusuario IS NOT NULL
        ";

        $result = $this->db->query($sql, [$date])->getResultArray();

        return $result ?? [];
    }
}
