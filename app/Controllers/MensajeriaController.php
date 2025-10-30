<?php

namespace App\Controllers;

use App\Models\MensajeModel;
use App\Models\NotificacionModel;
use App\Models\UsuarioModel;

class MensajeriaController extends BaseController
{
    protected $mensajeModel;
    protected $notificacionModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->mensajeModel = new MensajeModel();
        $this->notificacionModel = new NotificacionModel();
        $this->usuarioModel = new UsuarioModel();
        
        // Verificar y crear tablas si no existen
        $this->verificarTablas();
    }

    /**
     * Heartbeat para presencia en línea (cache TTL ~ 70s)
     */
    public function heartbeat()
    {
        $usuarioId = session()->get('usuario_id');
        if (!$usuarioId) {
            return $this->response->setJSON(['success' => false]);
        }
        $cache = \Config\Services::cache();
        $cache->save('presence_' . $usuarioId, time(), 70);
        return $this->response->setJSON(['success' => true]);
    }

    /** Obtener presencia de un usuario */
    public function getPresence($otroUsuarioId)
    {
        $cache = \Config\Services::cache();
        $ts = $cache->get('presence_' . $otroUsuarioId);
        $online = $ts && (time() - (int)$ts) <= 65;
        return $this->response->setJSON(['success' => true, 'online' => $online]);
    }

    /** Iniciar estado escribiendo */
    public function typingStart($otroUsuarioId)
    {
        $usuarioId = session()->get('usuario_id');
        if (!$usuarioId) { return $this->response->setJSON(['success' => false]); }
        $cache = \Config\Services::cache();
        $cache->save('typing_' . $usuarioId . '_' . $otroUsuarioId, 1, 6);
        return $this->response->setJSON(['success' => true]);
    }

    /** Finalizar estado escribiendo */
    public function typingStop($otroUsuarioId)
    {
        $usuarioId = session()->get('usuario_id');
        if (!$usuarioId) { return $this->response->setJSON(['success' => false]); }
        $cache = \Config\Services::cache();
        $cache->delete('typing_' . $usuarioId . '_' . $otroUsuarioId);
        return $this->response->setJSON(['success' => true]);
    }

    /** Consultar si el otro está escribiendo */
    public function typingStatus($otroUsuarioId)
    {
        $usuarioId = session()->get('usuario_id');
        if (!$usuarioId) { return $this->response->setJSON(['success' => false]); }
        $cache = \Config\Services::cache();
        $flag = $cache->get('typing_' . $otroUsuarioId . '_' . $usuarioId);
        return $this->response->setJSON(['success' => true, 'typing' => (bool)$flag]);
    }

    /**
     * Verificar y crear las tablas de mensajería si no existen
     */
    private function verificarTablas()
    {
        $db = \Config\Database::connect();
        
        // Verificar si la tabla mensajes existe
        if (!$db->tableExists('mensajes')) {
            $this->crearTablaMensajes();
        }
        
        // Verificar si la tabla notificaciones existe
        if (!$db->tableExists('notificaciones')) {
            $this->crearTablaNotificaciones();
        }
    }

    /**
     * Crear tabla mensajes
     */
    private function crearTablaMensajes()
    {
        $db = \Config\Database::connect();
        
        $sql = "CREATE TABLE IF NOT EXISTS mensajes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            remitente_id INT NOT NULL,
            destinatario_id INT NOT NULL,
            asunto VARCHAR(255) NOT NULL,
            contenido TEXT NOT NULL,
            tipo ENUM('normal', 'importante', 'urgente') DEFAULT 'normal',
            leido BOOLEAN DEFAULT FALSE,
            status ENUM('sent','delivered','read') DEFAULT 'sent',
            fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
            fecha_eliminacion DATETIME NULL,
            INDEX idx_remitente (remitente_id),
            INDEX idx_destinatario (destinatario_id),
            INDEX idx_fecha_envio (fecha_envio)
        )";
        
        $db->query($sql);
        // Intentar agregar columna status si la tabla ya existía
        try {
            $db->query("ALTER TABLE mensajes ADD COLUMN IF NOT EXISTS status ENUM('sent','delivered','read') DEFAULT 'sent'");
        } catch (\Throwable $e) {
            // Ignorar incompatibilidades o si ya existe
        }
    }

    /**
     * Crear tabla notificaciones
     */
    private function crearTablaNotificaciones()
    {
        $db = \Config\Database::connect();
        
        $sql = "CREATE TABLE IF NOT EXISTS notificaciones (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            tipo ENUM('mensaje', 'mensaje_importante', 'mensaje_urgente', 'sistema') DEFAULT 'mensaje',
            titulo VARCHAR(255) NOT NULL,
            mensaje TEXT NOT NULL,
            url VARCHAR(500) NULL,
            leida BOOLEAN DEFAULT FALSE,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_usuario (usuario_id),
            INDEX idx_leida (leida),
            INDEX idx_fecha_creacion (fecha_creacion)
        )";
        
        $db->query($sql);
    }

    /**
     * Página principal de mensajería - Interfaz moderna tipo WhatsApp
     */
    public function index()
    {
        $data = [
            'title' => 'Mensajería - ISHUME',
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];

        return view('mensajeria/index', $data);
    }

    /**
     * Obtener conversaciones del usuario (AJAX)
     */
    public function getConversaciones()
    {
        try {
            $usuarioId = session()->get('usuario_id');
            
            if (!$usuarioId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ]);
            }

            $db = \Config\Database::connect();
            
            // Obtener conversaciones con información del otro usuario
            $sql = "
                SELECT DISTINCT
                    CASE 
                        WHEN m.remitente_id = ? THEN m.destinatario_id
                        ELSE m.remitente_id
                    END as usuario_id,
                    CASE 
                        WHEN m.remitente_id = ? THEN CONCAT(p_dest.nombres, ' ', p_dest.apellidos)
                        ELSE CONCAT(p_rem.nombres, ' ', p_rem.apellidos)
                    END as nombre_completo,
                    CASE 
                        WHEN m.remitente_id = ? THEN u_dest.nombreusuario
                        ELSE u_rem.nombreusuario
                    END as nombreusuario,
                    (SELECT contenido FROM mensajes m2 
                     WHERE (m2.remitente_id = ? AND m2.destinatario_id = usuario_id) 
                        OR (m2.destinatario_id = ? AND m2.remitente_id = usuario_id)
                     ORDER BY m2.fecha_envio DESC LIMIT 1) as ultimo_mensaje,
                    (SELECT fecha_envio FROM mensajes m2 
                     WHERE (m2.remitente_id = ? AND m2.destinatario_id = usuario_id) 
                        OR (m2.destinatario_id = ? AND m2.remitente_id = usuario_id)
                     ORDER BY m2.fecha_envio DESC LIMIT 1) as fecha_ultimo_mensaje,
                    (SELECT COUNT(*) FROM mensajes m3 
                     WHERE m3.destinatario_id = ? AND m3.remitente_id = usuario_id AND m3.leido = 0) as mensajes_no_leidos
                FROM mensajes m
                LEFT JOIN usuarios u_rem ON u_rem.idusuario = m.remitente_id
                LEFT JOIN usuarios u_dest ON u_dest.idusuario = m.destinatario_id
                LEFT JOIN personas p_rem ON p_rem.idpersona = u_rem.idpersona
                LEFT JOIN personas p_dest ON p_dest.idpersona = u_dest.idpersona
                WHERE m.remitente_id = ? OR m.destinatario_id = ?
                ORDER BY fecha_ultimo_mensaje DESC
            ";
            
            $result = $db->query($sql, [
                $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, 
                $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId
            ]);
            
            $conversaciones = $result->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $conversaciones
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener conversaciones: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener mensajes de una conversación específica (AJAX)
     */
    public function getMensajesConversacion($otroUsuarioId)
    {
        try {
            $usuarioId = session()->get('usuario_id');
            
            if (!$usuarioId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ]);
            }

            $db = \Config\Database::connect();
            
            $sql = "
                SELECT m.*, 
                       CONCAT(p.nombres, ' ', p.apellidos) as remitente_nombre,
                       u.nombreusuario as remitente_usuario
                FROM mensajes m
                LEFT JOIN usuarios u ON u.idusuario = m.remitente_id
                LEFT JOIN personas p ON p.idpersona = u.idpersona
                WHERE (m.remitente_id = ? AND m.destinatario_id = ?) 
                   OR (m.remitente_id = ? AND m.destinatario_id = ?)
                ORDER BY m.fecha_envio ASC
            ";
            
            $result = $db->query($sql, [$usuarioId, $otroUsuarioId, $otroUsuarioId, $usuarioId]);
            $mensajes = $result->getResultArray();
            
            // Marcar como entregados los mensajes recibidos aún en 'sent'
            try {
                $db->query("UPDATE mensajes SET status = 'delivered' WHERE destinatario_id = ? AND remitente_id = ? AND status = 'sent'", [$usuarioId, $otroUsuarioId]);
            } catch (\Throwable $e) {}

            // Marcar mensajes como leídos
            $this->marcarMensajesComoLeidos($usuarioId, $otroUsuarioId);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $mensajes
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener mensajes: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Marcar mensajes como leídos
     */
    private function marcarMensajesComoLeidos($usuarioId, $otroUsuarioId)
    {
        try {
            $db = \Config\Database::connect();
            
            $sql = "UPDATE mensajes 
                    SET leido = 1, status = 'read' 
                    WHERE destinatario_id = ? AND remitente_id = ? AND leido = 0";
            
            $db->query($sql, [$usuarioId, $otroUsuarioId]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error al marcar mensajes como leídos: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar conversación específica
     */
    public function conversacion($contactoId)
    {
        $usuarioId = session()->get('idusuario');
        
        // Verificar que el contacto existe
        $contacto = $this->usuarioModel->getUsuarioCompleto($contactoId);
        if (!$contacto) {
            return redirect()->to('/mensajeria')->with('error', 'Usuario no encontrado');
        }

        // Obtener mensajes de la conversación
        $mensajes = $this->mensajeModel->getConversacion($usuarioId, $contactoId, 50);
        
        // Marcar mensajes como leídos
        foreach ($mensajes as $mensaje) {
            if ($mensaje['destinatario_id'] == $usuarioId && !$mensaje['leido']) {
                $this->mensajeModel->marcarComoLeido($mensaje['id'], $usuarioId);
            }
        }

        $data = [
            'title' => 'Conversación con ' . $contacto->nombres . ' ' . $contacto->apellidos,
            'contacto' => $contacto,
            'mensajes' => $mensajes,
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];

        return view('mensajeria/conversacion', $data);
    }

    /**
     * Mostrar formulario para enviar mensaje
     */
    public function enviar()
    {
        $usuarioId = session()->get('idusuario');
        
        // Obtener lista de usuarios para enviar mensaje
        $usuarios = $this->usuarioModel->getUsuariosCompletos(1); // Solo usuarios activos
        
        $data = [
            'title' => 'Enviar Mensaje - ISHUME',
            'usuarios' => $usuarios,
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];

        return view('mensajeria/enviar', $data);
    }

    /**
     * Procesar envío de mensaje
     */
    public function procesarEnvio()
    {
        try {
            $usuarioId = session()->get('usuario_id');
            
            if (!$usuarioId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ]);
            }

            // Obtener datos del formulario
            $destinatarioId = $this->request->getPost('destinatario_id');
            $asunto = trim($this->request->getPost('asunto'));
            $contenido = trim($this->request->getPost('contenido'));
            $tipo = $this->request->getPost('tipo');

            // Validaciones básicas
            if (!$destinatarioId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Debe seleccionar un destinatario'
                ]);
            }

            if (!$asunto || strlen($asunto) < 3) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'El asunto debe tener al menos 3 caracteres'
                ]);
            }

            if (!$contenido || strlen($contenido) < 5) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'El contenido debe tener al menos 5 caracteres'
                ]);
            }

            if (!$tipo || !in_array($tipo, ['normal', 'importante', 'urgente'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Debe seleccionar un tipo de mensaje válido'
                ]);
            }

            // Verificar que no se envíe mensaje a sí mismo
            if ($usuarioId == $destinatarioId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No puedes enviarte un mensaje a ti mismo'
                ]);
            }

            // Preparar datos para insertar
            $data = [
                'remitente_id' => $usuarioId,
                'destinatario_id' => $destinatarioId,
                'asunto' => $asunto,
                'contenido' => $contenido,
                'tipo' => $tipo,
                'leido' => false,
                'fecha_envio' => date('Y-m-d H:i:s')
            ];

            // Adjuntos simples: guardar archivo si viene 'archivo'
            $file = $this->request->getFile('archivo');
            if ($file && $file->isValid()) {
                $uploadDir = FCPATH . 'uploads/mensajeria/';
                if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
                $newName = $file->getRandomName();
                $file->move($uploadDir, $newName);
                $url = base_url('uploads/mensajeria/' . $newName);
                $data['contenido'] .= "\n" . '[archivo] ' . $url;
            }

            // Insertar mensaje directamente con SQL
            $db = \Config\Database::connect();
            $sql = "INSERT INTO mensajes (remitente_id, destinatario_id, asunto, contenido, tipo, leido, status, fecha_envio) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $result = $db->query($sql, [
                $data['remitente_id'],
                $data['destinatario_id'], 
                $data['asunto'],
                $data['contenido'],
                $data['tipo'],
                $data['leido'],
                'sent',
                $data['fecha_envio']
            ]);
            
            if (!$result) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al guardar el mensaje en la base de datos'
                ]);
            }

            $mensajeId = $db->insertID();

            // Crear notificación para el destinatario
            $this->crearNotificacion($destinatarioId, $tipo, $asunto, $contenido, $mensajeId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Mensaje enviado exitosamente',
                'mensaje_id' => $mensajeId
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Crear notificación para el destinatario
     */
    private function crearNotificacion($destinatarioId, $tipo, $asunto, $contenido, $mensajeId)
    {
        try {
            $db = \Config\Database::connect();
            
            $titulo = "Nuevo mensaje";
            $mensaje = "Tienes un nuevo mensaje: " . $asunto;
            $url = base_url("mensajeria/conversacion/{$destinatarioId}");
            
            // Determinar el tipo de notificación
            $tipoNotificacion = 'mensaje';
            if ($tipo === 'importante') {
                $tipoNotificacion = 'mensaje_importante';
                $titulo = "Mensaje importante";
            } elseif ($tipo === 'urgente') {
                $tipoNotificacion = 'mensaje_urgente';
                $titulo = "Mensaje urgente";
            }
            
            $sql = "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, url, leida, fecha_creacion) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $db->query($sql, [
                $destinatarioId,
                $tipoNotificacion,
                $titulo,
                $mensaje,
                $url,
                false,
                date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            // Si falla la notificación, no es crítico
            log_message('error', 'Error al crear notificación: ' . $e->getMessage());
        }
    }

    /**
     * Obtener mensajes no leídos (AJAX)
     */
    public function getMensajesNoLeidos()
    {
        try {
            $usuarioId = session()->get('usuario_id');
            
            if (!$usuarioId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ]);
            }

            // Verificar si la tabla existe
            $db = \Config\Database::connect();
            if (!$db->tableExists('mensajes')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tabla mensajes no existe. Ejecuta el archivo mensajeria.sql'
                ]);
            }

            $contador = $this->mensajeModel->getContadorNoLeidos($usuarioId);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'total' => $contador
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener notificaciones no leídas (AJAX)
     */
    public function getNotificacionesNoLeidas()
    {
        try {
            $usuarioId = session()->get('usuario_id');
            
            if (!$usuarioId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ]);
            }

            // Verificar si la tabla existe
            $db = \Config\Database::connect();
            if (!$db->tableExists('notificaciones')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tabla notificaciones no existe. Ejecuta el archivo mensajeria.sql'
                ]);
            }

            $contador = $this->notificacionModel->getContadorNoLeidas($usuarioId);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'total' => $contador
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener notificaciones recientes para dropdown (AJAX)
     */
    public function getNotificacionesRecientes()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $usuarioId = session()->get('idusuario');
        $limit = $this->request->getGet('limit') ?: 5;
        
        try {
            $notificaciones = $this->notificacionModel->getNotificacionesRecientes($usuarioId, $limit);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $notificaciones
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener notificaciones'
            ]);
        }
    }

    /**
     * Marcar notificación como leída (AJAX)
     */
    public function marcarNotificacionLeida()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $usuarioId = session()->get('idusuario');
        $notificacionId = $this->request->getPost('notificacion_id');
        
        if (!$notificacionId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de notificación requerido'
            ]);
        }

        try {
            $result = $this->notificacionModel->marcarComoLeida($notificacionId, $usuarioId);
            
            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Notificación marcada como leída'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se pudo marcar la notificación como leída'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al marcar notificación como leída'
            ]);
        }
    }

    /**
     * Buscar usuarios para enviar mensaje (AJAX)
     */
    public function buscarUsuarios()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $usuarioId = session()->get('idusuario');
        $termino = $this->request->getGet('q');
        
        if (empty($termino) || strlen($termino) < 2) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }

        try {
            $usuarios = $this->mensajeModel->buscarUsuarios($termino, $usuarioId, 10);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $usuarios
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al buscar usuarios'
            ]);
        }
    }

    /**
     * Eliminar mensaje (AJAX)
     */
    public function eliminarMensaje()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $usuarioId = session()->get('idusuario');
        $mensajeId = $this->request->getPost('mensaje_id');
        
        if (!$mensajeId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de mensaje requerido'
            ]);
        }

        try {
            $result = $this->mensajeModel->eliminarMensaje($mensajeId, $usuarioId);
            
            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Mensaje eliminado exitosamente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se pudo eliminar el mensaje'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar mensaje'
            ]);
        }
    }

    /**
     * Configuración de notificaciones
     */
    public function configuracion()
    {
        $usuarioId = session()->get('idusuario');
        
        $configuracion = $this->notificacionModel->getConfiguracionNotificaciones($usuarioId);

        $data = [
            'title' => 'Configuración de Notificaciones - ISHUME',
            'configuracion' => $configuracion,
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];

        return view('mensajeria/configuracion', $data);
    }

    /**
     * Marcar todas las notificaciones como leídas (AJAX)
     */
    public function marcarTodasNotificacionesLeidas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $usuarioId = session()->get('idusuario');

        try {
            $result = $this->notificacionModel->marcarTodasComoLeidas($usuarioId);
            
            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Todas las notificaciones han sido marcadas como leídas'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se pudieron marcar las notificaciones como leídas'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al marcar notificaciones como leídas'
            ]);
        }
    }

    /**
     * Actualizar configuración de notificaciones
     */
    public function actualizarConfiguracion()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $usuarioId = session()->get('idusuario');
        
        try {
            $configuracion = [
                'notificaciones_mensajes' => $this->request->getPost('notificaciones_mensajes') ? true : false,
                'notificaciones_importantes' => $this->request->getPost('notificaciones_importantes') ? true : false,
                'notificaciones_urgentes' => $this->request->getPost('notificaciones_urgentes') ? true : false,
                'notificaciones_sistema' => $this->request->getPost('notificaciones_sistema') ? true : false,
                'sonido_notificaciones' => $this->request->getPost('sonido_notificaciones') ? true : false,
                'email_notificaciones' => $this->request->getPost('email_notificaciones') ? true : false
            ];

            $result = $this->notificacionModel->actualizarConfiguracionNotificaciones($usuarioId, $configuracion);
            
            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Configuración actualizada exitosamente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se pudo actualizar la configuración'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar configuración'
            ]);
        }
    }
}
