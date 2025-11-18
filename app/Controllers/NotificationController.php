<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\NotificationService;

class NotificationController extends BaseController
{
    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    public function index()
    {
        $usuarioId = session()->get('usuario_id');
        if (!$usuarioId) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'No autenticado'
            ]);
        }

        $status = $this->request->getGet('status');
        $limit = (int) ($this->request->getGet('limit') ?? 20);
        $offset = (int) ($this->request->getGet('offset') ?? 0);

        $notificaciones = $this->notificationService->getNotifications($usuarioId, $status, $limit, $offset);

        return $this->response->setJSON([
            'success' => true,
            'data' => $notificaciones
        ]);
    }

    public function recent()
    {
        $usuarioId = session()->get('usuario_id');
        if (!$usuarioId) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'No autenticado'
            ]);
        }

        $limit = (int) ($this->request->getGet('limit') ?? 5);
        $notificaciones = $this->notificationService->getRecent($usuarioId, $limit);

        return $this->response->setJSON([
            'success' => true,
            'data' => $notificaciones
        ]);
    }

    public function unreadCount()
    {
        $usuarioId = session()->get('usuario_id');
        if (!$usuarioId) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'No autenticado'
            ]);
        }

        $total = $this->notificationService->getUnreadCount($usuarioId);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'total' => $total
            ]
        ]);
    }

    public function markRead($id)
    {
        if (!$this->request->isAJAX() && $this->request->getMethod() !== 'post') {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
        }

        $usuarioId = session()->get('usuario_id');
        if (!$usuarioId) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'No autenticado'
            ]);
        }

        $result = $this->notificationService->markAsRead((int) $id, $usuarioId);

        return $this->response->setJSON([
            'success' => $result,
            'message' => $result ? 'Notificación marcada como leída' : 'No se pudo marcar la notificación'
        ]);
    }

    public function markAllRead()
    {
        if (!$this->request->isAJAX() && $this->request->getMethod() !== 'post') {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
        }

        $usuarioId = session()->get('usuario_id');
        if (!$usuarioId) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'No autenticado'
            ]);
        }

        $result = $this->notificationService->markAllAsRead($usuarioId);

        return $this->response->setJSON([
            'success' => $result,
            'message' => $result ? 'Todas las notificaciones marcadas como leídas' : 'No se pudo completar la operación'
        ]);
    }
}