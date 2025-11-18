<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'usuario_id',
        'tipo',
        'titulo',
        'mensaje',
        'url',
        'leida',
        'fecha_creacion'
    ];

    protected $useTimestamps = false;

    public function getUnreadCount(int $usuarioId): int
    {
        return (int) $this->where('usuario_id', $usuarioId)
            ->where('leida', 0)
            ->countAllResults();
    }

    public function getRecent(int $usuarioId, int $limit = 10): array
    {
        return $this->where('usuario_id', $usuarioId)
            ->orderBy('fecha_creacion', 'DESC')
            ->findAll($limit);
    }

    public function getNotifications(int $usuarioId, ?string $status = null, int $limit = 20, int $offset = 0): array
    {
        $builder = $this->where('usuario_id', $usuarioId)
            ->orderBy('fecha_creacion', 'DESC');

        if ($status === 'unread') {
            $builder->where('leida', 0);
        } elseif ($status === 'read') {
            $builder->where('leida', 1);
        }

        return $builder->findAll($limit, $offset);
    }

    public function createNotification(array $data): int
    {
        return (int) $this->insert([
            'usuario_id' => $data['usuario_id'],
            'tipo' => $data['tipo'] ?? 'sistema',
            'titulo' => $data['titulo'],
            'mensaje' => $data['mensaje'],
            'url' => $data['url'] ?? null,
            'leida' => $data['leida'] ?? 0,
            'fecha_creacion' => $data['fecha_creacion'] ?? date('Y-m-d H:i:s')
        ]);
    }

    public function markAsRead(int $notificationId, int $usuarioId): bool
    {
        return (bool) $this->where('id', $notificationId)
            ->where('usuario_id', $usuarioId)
            ->set('leida', 1)
            ->update();
    }

    public function markAllAsRead(int $usuarioId): bool
    {
        return (bool) $this->where('usuario_id', $usuarioId)
            ->set('leida', 1)
            ->update();
    }

    public function existsByTitleAndUrl(int $usuarioId, string $titulo, string $url): bool
    {
        return $this->where('usuario_id', $usuarioId)
            ->where('titulo', $titulo)
            ->where('url', $url)
            ->countAllResults() > 0;
    }
}
