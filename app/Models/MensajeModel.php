<?php

namespace App\Models;

use CodeIgniter\Model;

class MensajeModel extends Model
{
    protected $table = 'mensajes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'remitente_id',
        'destinatario_id', 
        'asunto',
        'contenido',
        'tipo',
        'leido',
        'eliminado_remitente',
        'eliminado_destinatario'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fecha_envio';
    protected $updatedField = 'fecha_leido';

    protected $validationRules = [
        'remitente_id' => 'required|integer',
        'destinatario_id' => 'required|integer',
        'asunto' => 'required|min_length[1]|max_length[255]',
        'contenido' => 'required|min_length[1]',
        'tipo' => 'required|in_list[normal,importante,urgente]'
    ];

    protected $validationMessages = [
        'remitente_id' => [
            'required' => 'El remitente es obligatorio',
            'integer' => 'ID de remitente inválido'
        ],
        'destinatario_id' => [
            'required' => 'El destinatario es obligatorio', 
            'integer' => 'ID de destinatario inválido'
        ],
        'asunto' => [
            'required' => 'El asunto es obligatorio',
            'min_length' => 'El asunto no puede estar vacío',
            'max_length' => 'El asunto no puede exceder 255 caracteres'
        ],
        'contenido' => [
            'required' => 'El contenido es obligatorio',
            'min_length' => 'El contenido no puede estar vacío'
        ],
        'tipo' => [
            'required' => 'El tipo de mensaje es obligatorio',
            'in_list' => 'Tipo de mensaje inválido'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Obtener mensajes recibidos por un usuario
     */
    public function getMensajesRecibidos($usuarioId, $limit = 20, $offset = 0)
    {
        return $this->db->query("
            SELECT m.*, 
                   CONCAT(p.nombres, ' ', p.apellidos) as remitente_nombre,
                   u.nombreusuario as remitente_usuario,
                   u.email as remitente_email
            FROM mensajes m
            JOIN usuarios u ON m.remitente_id = u.idusuario
            JOIN personas p ON u.idpersona = p.idpersona
            WHERE m.destinatario_id = ? 
            AND m.eliminado_destinatario = FALSE
            ORDER BY m.fecha_envio DESC
            LIMIT ? OFFSET ?
        ", [$usuarioId, $limit, $offset])->getResultArray();
    }

    /**
     * Obtener mensajes enviados por un usuario
     */
    public function getMensajesEnviados($usuarioId, $limit = 20, $offset = 0)
    {
        return $this->db->query("
            SELECT m.*,
                   CONCAT(p.nombres, ' ', p.apellidos) as destinatario_nombre,
                   u.nombreusuario as destinatario_usuario,
                   u.email as destinatario_email
            FROM mensajes m
            JOIN usuarios u ON m.destinatario_id = u.idusuario
            JOIN personas p ON u.idpersona = p.idpersona
            WHERE m.remitente_id = ?
            AND m.eliminado_remitente = FALSE
            ORDER BY m.fecha_envio DESC
            LIMIT ? OFFSET ?
        ", [$usuarioId, $limit, $offset])->getResultArray();
    }

    /**
     * Obtener conversación entre dos usuarios
     */
    public function getConversacion($usuario1Id, $usuario2Id, $limit = 50)
    {
        return $this->db->query("
            SELECT m.*,
                   CASE 
                       WHEN m.remitente_id = ? THEN 'enviado'
                       ELSE 'recibido'
                   END as direccion,
                   CONCAT(p.nombres, ' ', p.apellidos) as remitente_nombre,
                   u.nombreusuario as remitente_usuario
            FROM mensajes m
            JOIN usuarios u ON m.remitente_id = u.idusuario
            JOIN personas p ON u.idpersona = p.idpersona
            WHERE ((m.remitente_id = ? AND m.destinatario_id = ? AND m.eliminado_remitente = FALSE)
                OR (m.remitente_id = ? AND m.destinatario_id = ? AND m.eliminado_destinatario = FALSE))
            ORDER BY m.fecha_envio ASC
            LIMIT ?
        ", [$usuario1Id, $usuario1Id, $usuario2Id, $usuario2Id, $usuario1Id, $limit])->getResultArray();
    }

    /**
     * Marcar mensaje como leído
     */
    public function marcarComoLeido($mensajeId, $usuarioId)
    {
        return $this->db->query("
            UPDATE mensajes 
            SET leido = TRUE, fecha_leido = NOW()
            WHERE id = ? AND destinatario_id = ? AND leido = FALSE
        ", [$mensajeId, $usuarioId]);
    }

    /**
     * Obtener contador de mensajes no leídos
     */
    public function getContadorNoLeidos($usuarioId)
    {
        $result = $this->db->query("
            SELECT COUNT(*) as total,
                   SUM(CASE WHEN tipo = 'urgente' THEN 1 ELSE 0 END) as urgentes,
                   SUM(CASE WHEN tipo = 'importante' THEN 1 ELSE 0 END) as importantes
            FROM mensajes 
            WHERE destinatario_id = ? 
            AND leido = FALSE 
            AND eliminado_destinatario = FALSE
        ", [$usuarioId])->getRowArray();

        return $result ?: ['total' => 0, 'urgentes' => 0, 'importantes' => 0];
    }

    /**
     * Obtener conversaciones del usuario
     */
    public function getConversaciones($usuarioId, $limit = 20)
    {
        return $this->db->query("
            SELECT c.*,
                   CASE 
                       WHEN c.usuario1_id = ? THEN 
                           CONCAT(p2.nombres, ' ', p2.apellidos)
                       ELSE 
                           CONCAT(p1.nombres, ' ', p1.apellidos)
                   END as contacto_nombre,
                   CASE 
                       WHEN c.usuario1_id = ? THEN u2.nombreusuario
                       ELSE u1.nombreusuario
                   END as contacto_usuario,
                   CASE 
                       WHEN c.usuario1_id = ? THEN c.mensajes_no_leidos_usuario1
                       ELSE c.mensajes_no_leidos_usuario2
                   END as mensajes_no_leidos,
                   m.asunto as ultimo_asunto,
                   m.contenido as ultimo_contenido,
                   m.tipo as ultimo_tipo,
                   m.fecha_envio as ultima_fecha
            FROM conversaciones c
            JOIN usuarios u1 ON c.usuario1_id = u1.idusuario
            JOIN personas p1 ON u1.idpersona = p1.idpersona
            JOIN usuarios u2 ON c.usuario2_id = u2.idusuario
            JOIN personas p2 ON u2.idpersona = p2.idpersona
            LEFT JOIN mensajes m ON c.ultimo_mensaje_id = m.id
            WHERE c.usuario1_id = ? OR c.usuario2_id = ?
            ORDER BY c.fecha_ultimo_mensaje DESC
            LIMIT ?
        ", [$usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $limit])->getResultArray();
    }

    /**
     * Eliminar mensaje (soft delete)
     */
    public function eliminarMensaje($mensajeId, $usuarioId)
    {
        // Verificar si el usuario es remitente o destinatario
        $mensaje = $this->find($mensajeId);
        if (!$mensaje) {
            return false;
        }

        if ($mensaje['remitente_id'] == $usuarioId) {
            return $this->update($mensajeId, ['eliminado_remitente' => true]);
        } elseif ($mensaje['destinatario_id'] == $usuarioId) {
            return $this->update($mensajeId, ['eliminado_destinatario' => true]);
        }

        return false;
    }

    /**
     * Buscar usuarios para enviar mensaje
     */
    public function buscarUsuarios($termino, $usuarioActualId, $limit = 10)
    {
        return $this->db->query("
            SELECT u.idusuario, u.nombreusuario, u.email,
                   CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo,
                   c.nombre as cargo_nombre
            FROM usuarios u
            JOIN personas p ON u.idpersona = p.idpersona
            LEFT JOIN cargos c ON u.idcargo = c.idcargo
            WHERE u.idusuario != ? 
            AND u.estado = 1
            AND (u.nombreusuario LIKE ? 
                OR u.email LIKE ? 
                OR CONCAT(p.nombres, ' ', p.apellidos) LIKE ?)
            ORDER BY p.nombres, p.apellidos
            LIMIT ?
        ", [$usuarioActualId, "%{$termino}%", "%{$termino}%", "%{$termino}%", $limit])->getResultArray();
    }
}
