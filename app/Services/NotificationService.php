<?php

namespace App\Services;

use App\Models\NotificationModel;

class NotificationService
{
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function createNotification(array $data): int
    {
        return $this->notificationModel->createNotification($data);
    }

    public function createAssignmentNotification(int $usuarioId, string $cliente, string $servicio, int $proyectoId): int
    {
        return $this->createNotification([
            'usuario_id' => $usuarioId,
            'tipo' => 'asignacion_proyecto',
            'titulo' => "Nuevo proyecto asignado",
            'mensaje' => "Se le ha asignado el proyecto {$cliente} – {$servicio} – ID {$proyectoId}",
            'url' => base_url("/cronograma/proyecto/{$proyectoId}")
        ]);
    }

    public function createDueReminder(int $usuarioId, string $cliente, string $servicio, int $proyectoId): int
    {
        return $this->createNotification([
            'usuario_id' => $usuarioId,
            'tipo' => 'vencimiento_proyecto',
            'titulo' => 'Proyecto próximo a vencer',
            'mensaje' => "El proyecto {$cliente} – {$servicio} vencerá en 3 días.",
            'url' => base_url("/cronograma/proyecto/{$proyectoId}")
        ]);
    }

    public function getUnreadCount(int $usuarioId): int
    {
        return $this->notificationModel->getUnreadCount($usuarioId);
    }

    public function getNotifications(int $usuarioId, ?string $status = null, int $limit = 20, int $offset = 0): array
    {
        return $this->notificationModel->getNotifications($usuarioId, $status, $limit, $offset);
    }

    public function getRecent(int $usuarioId, int $limit = 5): array
    {
        return $this->notificationModel->getRecent($usuarioId, $limit);
    }

    public function markAsRead(int $notificationId, int $usuarioId): bool
    {
        return $this->notificationModel->markAsRead($notificationId, $usuarioId);
    }

    public function markAllAsRead(int $usuarioId): bool
    {
        return $this->notificationModel->markAllAsRead($usuarioId);
    }

    public function shouldCreateReminder(int $usuarioId, string $titulo, string $url): bool
    {
        return !$this->notificationModel->existsByTitleAndUrl($usuarioId, $titulo, $url);
    }
}
