<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificacionModel extends Model
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'usuario_id',
        'tipo',
        'titulo',
        'mensaje',
        'leida',
        'datos_extra'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fecha_creacion';
    protected $updatedField = 'fecha_leida';

    protected $validationRules = [
        'usuario_id' => 'required|integer',
        'tipo' => 'required|in_list[mensaje,mensaje_importante,mensaje_urgente,sistema]',
        'titulo' => 'required|min_length[1]|max_length[255]',
        'mensaje' => 'required|min_length[1]'
    ];

    protected $validationMessages = [
        'usuario_id' => [
            'required' => 'El usuario es obligatorio',
            'integer' => 'ID de usuario inválido'
        ],
        'tipo' => [
            'required' => 'El tipo de notificación es obligatorio',
            'in_list' => 'Tipo de notificación inválido'
        ],
        'titulo' => [
            'required' => 'El título es obligatorio',
            'min_length' => 'El título no puede estar vacío',
            'max_length' => 'El título no puede exceder 255 caracteres'
        ],
        'mensaje' => [
            'required' => 'El mensaje es obligatorio',
            'min_length' => 'El mensaje no puede estar vacío'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtener notificaciones de un usuario
     */
    public function getNotificaciones($usuarioId, $limit = 20, $offset = 0, $soloNoLeidas = false)
    {
        $whereClause = "WHERE usuario_id = ?";
        $params = [$usuarioId];
        
        if ($soloNoLeidas) {
            $whereClause .= " AND leida = FALSE";
        }

        return $this->db->query("
            SELECT *
            FROM notificaciones
            {$whereClause}
            ORDER BY fecha_creacion DESC
            LIMIT ? OFFSET ?
        ", array_merge($params, [$limit, $offset]))->getResultArray();
    }

    /**
     * Obtener contador de notificaciones no leídas
     */
    public function getContadorNoLeidas($usuarioId)
    {
        $result = $this->db->query("
            SELECT COUNT(*) as total,
                   SUM(CASE WHEN tipo = 'mensaje_urgente' THEN 1 ELSE 0 END) as urgentes,
                   SUM(CASE WHEN tipo = 'mensaje_importante' THEN 1 ELSE 0 END) as importantes,
                   SUM(CASE WHEN tipo = 'sistema' THEN 1 ELSE 0 END) as sistema
            FROM notificaciones 
            WHERE usuario_id = ? AND leida = FALSE
        ", [$usuarioId])->getRowArray();

        return $result ?: ['total' => 0, 'urgentes' => 0, 'importantes' => 0, 'sistema' => 0];
    }

    /**
     * Marcar notificación como leída
     */
    public function marcarComoLeida($notificacionId, $usuarioId)
    {
        return $this->db->query("
            UPDATE notificaciones 
            SET leida = TRUE, fecha_leida = NOW()
            WHERE id = ? AND usuario_id = ? AND leida = FALSE
        ", [$notificacionId, $usuarioId]);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function marcarTodasComoLeidas($usuarioId)
    {
        return $this->db->query("
            UPDATE notificaciones 
            SET leida = TRUE, fecha_leida = NOW()
            WHERE usuario_id = ? AND leida = FALSE
        ", [$usuarioId]);
    }

    /**
     * Crear notificación
     */
    public function crearNotificacion($usuarioId, $tipo, $titulo, $mensaje, $datosExtra = null)
    {
        $data = [
            'usuario_id' => $usuarioId,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'leida' => false
        ];

        if ($datosExtra) {
            $data['datos_extra'] = json_encode($datosExtra);
        }

        return $this->insert($data);
    }

    /**
     * Crear notificación para múltiples usuarios
     */
    public function crearNotificacionMultiple($usuarioIds, $tipo, $titulo, $mensaje, $datosExtra = null)
    {
        $data = [];
        $datosExtraJson = $datosExtra ? json_encode($datosExtra) : null;

        foreach ($usuarioIds as $usuarioId) {
            $data[] = [
                'usuario_id' => $usuarioId,
                'tipo' => $tipo,
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'leida' => false,
                'datos_extra' => $datosExtraJson,
                'fecha_creacion' => date('Y-m-d H:i:s')
            ];
        }

        return $this->insertBatch($data);
    }

    /**
     * Eliminar notificaciones antiguas (más de 30 días)
     */
    public function limpiarNotificacionesAntiguas($dias = 30)
    {
        return $this->db->query("
            DELETE FROM notificaciones 
            WHERE fecha_creacion < DATE_SUB(NOW(), INTERVAL ? DAY)
        ", [$dias]);
    }

    /**
     * Obtener notificaciones recientes para el dropdown
     */
    public function getNotificacionesRecientes($usuarioId, $limit = 5)
    {
        return $this->db->query("
            SELECT id, tipo, titulo, mensaje, fecha_creacion,
                   CASE 
                       WHEN fecha_creacion > DATE_SUB(NOW(), INTERVAL 1 HOUR) THEN 'hace menos de 1 hora'
                       WHEN fecha_creacion > DATE_SUB(NOW(), INTERVAL 1 DAY) THEN CONCAT('hace ', HOUR(TIMEDIFF(NOW(), fecha_creacion)), ' horas')
                       ELSE CONCAT('hace ', DAY(TIMEDIFF(NOW(), fecha_creacion)), ' días')
                   END as tiempo_transcurrido
            FROM notificaciones
            WHERE usuario_id = ?
            ORDER BY fecha_creacion DESC
            LIMIT ?
        ", [$usuarioId, $limit])->getResultArray();
    }

    /**
     * Obtener configuración de notificaciones del usuario
     */
    public function getConfiguracionNotificaciones($usuarioId)
    {
        $result = $this->db->query("
            SELECT * FROM configuracion_notificaciones 
            WHERE usuario_id = ?
        ", [$usuarioId])->getRowArray();

        // Si no existe configuración, crear una por defecto
        if (!$result) {
            $this->db->query("
                INSERT INTO configuracion_notificaciones (usuario_id) 
                VALUES (?)
            ", [$usuarioId]);
            
            $result = [
                'usuario_id' => $usuarioId,
                'notificaciones_mensajes' => true,
                'notificaciones_importantes' => true,
                'notificaciones_urgentes' => true,
                'notificaciones_sistema' => true,
                'sonido_notificaciones' => true,
                'email_notificaciones' => false
            ];
        }

        return $result;
    }

    /**
     * Actualizar configuración de notificaciones
     */
    public function actualizarConfiguracionNotificaciones($usuarioId, $configuracion)
    {
        $allowedFields = [
            'notificaciones_mensajes',
            'notificaciones_importantes', 
            'notificaciones_urgentes',
            'notificaciones_sistema',
            'sonido_notificaciones',
            'email_notificaciones'
        ];

        $data = [];
        foreach ($allowedFields as $field) {
            if (isset($configuracion[$field])) {
                $data[$field] = (bool) $configuracion[$field];
            }
        }

        if (empty($data)) {
            return false;
        }

        $sql = "INSERT INTO configuracion_notificaciones (usuario_id, " . implode(', ', array_keys($data)) . ")
                VALUES (?, " . str_repeat('?,', count($data) - 1) . "?)
                ON DUPLICATE KEY UPDATE " . 
                implode(', ', array_map(function($key) { return "{$key} = VALUES({$key})"; }, array_keys($data)));
        
        return $this->db->query($sql, array_merge([$usuarioId], array_values($data)));
    }
}
