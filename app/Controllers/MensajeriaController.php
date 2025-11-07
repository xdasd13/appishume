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
        try {
            $usuarioId = session()->get('usuario_id') ?? session()->get('idusuario');
            if (!$usuarioId) {
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'No autenticado']);
            }
            $cache = \Config\Services::cache();
            $cache->save('presence_' . $usuarioId, time(), 70);
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /** Obtener presencia de un usuario */
    public function getPresence($otroUsuarioId)
    {
        try {
            $cache = \Config\Services::cache();
            $ts = $cache->get('presence_' . $otroUsuarioId);
            $online = $ts && (time() - (int)$ts) <= 65;
            return $this->response->setJSON(['success' => true, 'online' => $online]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'online' => false]);
        }
    }

    /** Iniciar estado escribiendo */
    public function typingStart($otroUsuarioId)
    {
        try {
            $usuarioId = session()->get('usuario_id') ?? session()->get('idusuario');
            if (!$usuarioId) { 
                return $this->response->setJSON(['success' => false, 'message' => 'No autenticado']); 
            }
            $cache = \Config\Services::cache();
            $cache->save('typing_' . $usuarioId . '_' . $otroUsuarioId, 1, 6);
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /** Finalizar estado escribiendo */
    public function typingStop($otroUsuarioId)
    {
        try {
            $usuarioId = session()->get('usuario_id') ?? session()->get('idusuario');
            if (!$usuarioId) { 
                return $this->response->setJSON(['success' => false, 'message' => 'No autenticado']); 
            }
            $cache = \Config\Services::cache();
            $cache->delete('typing_' . $usuarioId . '_' . $otroUsuarioId);
            return $this->response->setJSON(['success' => true]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /** Consultar si el otro está escribiendo */
    public function typingStatus($otroUsuarioId)
    {
        try {
            $usuarioId = session()->get('usuario_id') ?? session()->get('idusuario');
            if (!$usuarioId) { 
                return $this->response->setJSON(['success' => false, 'typing' => false]); 
            }
            $cache = \Config\Services::cache();
            $flag = $cache->get('typing_' . $otroUsuarioId . '_' . $usuarioId);
            return $this->response->setJSON(['success' => true, 'typing' => (bool)$flag]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'typing' => false]);
        }
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
        } else {
            // Verificar si la columna status existe y agregarla si no
            $this->verificarColumnaStatus();
        }
        
        // Verificar si la tabla notificaciones existe
        if (!$db->tableExists('notificaciones')) {
            $this->crearTablaNotificaciones();
        }

        // Verificar tabla de adjuntos de mensajes
        if (!$db->tableExists('archivos_mensaje')) {
            $this->crearTablaArchivosMensaje();
        }
    }

    /**
     * Verificar y agregar columna status si no existe
     */
    private function verificarColumnaStatus()
    {
        $db = \Config\Database::connect();
        
        try {
            // Verificar si la columna status existe
            $columnas = $db->getFieldNames('mensajes');
            
            if (!in_array('status', $columnas)) {
                // Agregar la columna status
                $db->query("ALTER TABLE mensajes ADD COLUMN status ENUM('sent','delivered','read') DEFAULT 'sent' AFTER leido");
            }
        } catch (\Throwable $e) {
            // Si hay error, intentar con sintaxis alternativa
            try {
                $db->query("ALTER TABLE mensajes ADD COLUMN status ENUM('sent','delivered','read') DEFAULT 'sent'");
            } catch (\Throwable $e2) {
                // Ignorar si no se puede agregar
                log_message('warning', 'No se pudo agregar la columna status: ' . $e2->getMessage());
            }
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
     * Crear tabla para almacenar adjuntos en BD (LONGBLOB)
     */
    private function crearTablaArchivosMensaje()
    {
        $db = \Config\Database::connect();
        
        // Crear tabla si no existe
        if (!$db->tableExists('archivos_mensaje')) {
            $sql = "CREATE TABLE archivos_mensaje (
                id INT AUTO_INCREMENT PRIMARY KEY,
                mensaje_id INT NULL,
                nombre_original VARCHAR(255) NOT NULL,
                mime VARCHAR(100) NOT NULL,
                tamanio INT NOT NULL,
                datos LONGBLOB NULL,
                path VARCHAR(500) NULL,
                creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_mensaje (mensaje_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $db->query($sql);
        } else {
            // Agregar columna path si no existe (migración)
            $columnas = $db->getFieldNames('archivos_mensaje');
            if (!in_array('path', $columnas)) {
                $db->query("ALTER TABLE archivos_mensaje ADD COLUMN path VARCHAR(500) NULL AFTER tamanio");
            }
            // Hacer datos opcional (puede ser NULL si usamos path)
            $db->query("ALTER TABLE archivos_mensaje MODIFY datos LONGBLOB NULL");
        }
    }

    /**
     * Página principal de mensajería - Interfaz moderna tipo WhatsApp
     */
    public function index()
    {
        // Obtener email del usuario si no está en sesión
        $usuarioEmail = session('usuario_email');
        if (empty($usuarioEmail)) {
            $usuarioId = session('usuario_id');
            if ($usuarioId) {
                $db = \Config\Database::connect();
                $usuario = $db->table('usuarios')
                    ->select('email')
                    ->where('idusuario', $usuarioId)
                    ->get()
                    ->getRow();
                if ($usuario && !empty($usuario->email)) {
                    session()->set('usuario_email', $usuario->email);
                    $usuarioEmail = $usuario->email;
                }
            }
        }
        
        $data = [
            'title' => 'Mensajería - ISHUME',
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer'),
            'usuario_email' => $usuarioEmail ?? session('usuario_email') ?? ''
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
            
            // Consulta optimizada: calcular mensajes no leídos en tiempo real desde la tabla mensajes
            // Usar directamente la consulta que agrupa desde mensajes para mayor precisión
            $sql = "
                SELECT 
                    other_id as usuario_id,
                    MAX(nombre_completo) as nombre_completo,
                    MAX(nombreusuario) as nombreusuario,
                    SUBSTRING_INDEX(GROUP_CONCAT(ultimo_contenido ORDER BY fecha_envio DESC), ',', 1) as ultimo_mensaje,
                    MAX(fecha_envio) as fecha_ultimo_mensaje,
                    SUM(CASE WHEN destinatario_id = ? AND leido = 0 AND (eliminado_destinatario = 0 OR eliminado_destinatario IS NULL) THEN 1 ELSE 0 END) as mensajes_no_leidos
                FROM (
                    SELECT DISTINCT
                        CASE WHEN m.remitente_id = ? THEN m.destinatario_id ELSE m.remitente_id END as other_id,
                        CASE WHEN m.remitente_id = ? THEN CONCAT(p_dest.nombres, ' ', p_dest.apellidos)
                             ELSE CONCAT(p_rem.nombres, ' ', p_rem.apellidos) END as nombre_completo,
                        CASE WHEN m.remitente_id = ? THEN u_dest.nombreusuario ELSE u_rem.nombreusuario END as nombreusuario,
                        m.contenido as ultimo_contenido,
                        m.fecha_envio,
                        m.destinatario_id,
                        m.leido,
                        m.eliminado_destinatario
                    FROM mensajes m
                    LEFT JOIN usuarios u_rem ON u_rem.idusuario = m.remitente_id
                    LEFT JOIN usuarios u_dest ON u_dest.idusuario = m.destinatario_id
                    LEFT JOIN personas p_rem ON p_rem.idpersona = u_rem.idpersona
                    LEFT JOIN personas p_dest ON p_dest.idpersona = u_dest.idpersona
                    WHERE (m.remitente_id = ? OR m.destinatario_id = ?)
                    AND (m.eliminado_remitente = 0 OR m.eliminado_remitente IS NULL)
                    AND (m.eliminado_destinatario = 0 OR m.eliminado_destinatario IS NULL)
                ) as subquery
                GROUP BY other_id
                ORDER BY MAX(fecha_envio) DESC
                LIMIT 50
            ";
            try {
                $result = $db->query($sql, [$usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId]);
            } catch (\Exception $e) {
                // Fallback: consulta agrupada sin duplicados
                $sql = "
                    SELECT 
                        other_id as usuario_id,
                        MAX(nombre_completo) as nombre_completo,
                        MAX(nombreusuario) as nombreusuario,
                        SUBSTRING_INDEX(GROUP_CONCAT(ultimo_contenido ORDER BY fecha_envio DESC), ',', 1) as ultimo_mensaje,
                        MAX(fecha_envio) as fecha_ultimo_mensaje,
                        SUM(CASE WHEN destinatario_id = ? AND leido = 0 THEN 1 ELSE 0 END) as mensajes_no_leidos
                    FROM (
                        SELECT DISTINCT
                            CASE WHEN m.remitente_id = ? THEN m.destinatario_id ELSE m.remitente_id END as other_id,
                            CASE WHEN m.remitente_id = ? THEN CONCAT(p_dest.nombres, ' ', p_dest.apellidos)
                                 ELSE CONCAT(p_rem.nombres, ' ', p_rem.apellidos) END as nombre_completo,
                            CASE WHEN m.remitente_id = ? THEN u_dest.nombreusuario ELSE u_rem.nombreusuario END as nombreusuario,
                            m.contenido as ultimo_contenido,
                            m.fecha_envio,
                            m.destinatario_id,
                            m.leido
                        FROM mensajes m
                        LEFT JOIN usuarios u_rem ON u_rem.idusuario = m.remitente_id
                        LEFT JOIN usuarios u_dest ON u_dest.idusuario = m.destinatario_id
                        LEFT JOIN personas p_rem ON p_rem.idpersona = u_rem.idpersona
                        LEFT JOIN personas p_dest ON p_dest.idpersona = u_dest.idpersona
                        WHERE (m.remitente_id = ? OR m.destinatario_id = ?)
                    ) as subquery
                    GROUP BY other_id
                    ORDER BY MAX(fecha_envio) DESC
                    LIMIT 50
                ";
                $result = $db->query($sql, [$usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId]);
            }
            
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
            
            // Marcar como entregados los mensajes recibidos aún en 'sent' (solo si la columna existe)
            try {
                $columnas = $db->getFieldNames('mensajes');
                if (in_array('status', $columnas)) {
                    $db->query("UPDATE mensajes SET status = 'delivered' WHERE destinatario_id = ? AND remitente_id = ? AND status = 'sent'", [$usuarioId, $otroUsuarioId]);
                }
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
            
            // Contar cuántos mensajes se van a marcar como leídos
            $countResult = $db->query(
                "SELECT COUNT(*) as total FROM mensajes 
                 WHERE destinatario_id = ? AND remitente_id = ? AND leido = 0",
                [$usuarioId, $otroUsuarioId]
            );
            $countRow = $countResult->getRowArray();
            $cantidadNoLeidos = $countRow['total'] ?? 0;
            
            if ($cantidadNoLeidos > 0) {
                // Verificar si la columna status existe
                $columnas = $db->getFieldNames('mensajes');
                $tieneStatus = in_array('status', $columnas);
                
                if ($tieneStatus) {
                    $sql = "UPDATE mensajes 
                            SET leido = 1, status = 'read', fecha_leido = NOW()
                            WHERE destinatario_id = ? AND remitente_id = ? AND leido = 0";
                } else {
                    $sql = "UPDATE mensajes 
                            SET leido = 1, fecha_leido = NOW()
                            WHERE destinatario_id = ? AND remitente_id = ? AND leido = 0";
                }
                
                $db->query($sql, [$usuarioId, $otroUsuarioId]);
                
                // Actualizar contador en tabla conversaciones
                // Primero verificar si existe la conversación
                $convResult = $db->query(
                    "SELECT * FROM conversaciones 
                     WHERE (usuario1_id = ? AND usuario2_id = ?) 
                        OR (usuario1_id = ? AND usuario2_id = ?)",
                    [$usuarioId, $otroUsuarioId, $otroUsuarioId, $usuarioId]
                );
                $conversacion = $convResult->getRowArray();
                
                if ($conversacion) {
                    // Recalcular el contador real desde mensajes para asegurar precisión
                    // Esto garantiza que el contador esté sincronizado con la realidad
                    $countReal = $db->query(
                        "SELECT COUNT(*) as total FROM mensajes 
                         WHERE destinatario_id = ? AND remitente_id = ? AND leido = 0 
                         AND (eliminado_destinatario = 0 OR eliminado_destinatario IS NULL)",
                        [$usuarioId, $otroUsuarioId]
                    )->getRowArray();
                    $totalReal = $countReal['total'] ?? 0;
                    
                    // Actualizar el contador según quién es usuario1 y usuario2
                    if ($conversacion['usuario1_id'] == $usuarioId) {
                        // El usuario actual es usuario1, está marcando como leídos mensajes que recibió de usuario2
                        // Por lo tanto, actualizar mensajes_no_leidos_usuario1 con el valor real
                        $db->query(
                            "UPDATE conversaciones 
                             SET mensajes_no_leidos_usuario1 = ?
                             WHERE id = ?",
                            [$totalReal, $conversacion['id']]
                        );
                    } else {
                        // El usuario actual es usuario2, está marcando como leídos mensajes que recibió de usuario1
                        // Por lo tanto, actualizar mensajes_no_leidos_usuario2 con el valor real
                        $db->query(
                            "UPDATE conversaciones 
                             SET mensajes_no_leidos_usuario2 = ?
                             WHERE id = ?",
                            [$totalReal, $conversacion['id']]
                        );
                    }
                } else {
                    // Si no existe la conversación, crear una nueva
                    // Esto no debería pasar, pero por si acaso
                    try {
                        $db->query(
                            "INSERT INTO conversaciones (usuario1_id, usuario2_id, mensajes_no_leidos_usuario1, mensajes_no_leidos_usuario2, fecha_ultimo_mensaje)
                             VALUES (?, ?, 0, 0, NOW())
                             ON DUPLICATE KEY UPDATE fecha_ultimo_mensaje = NOW()",
                            [min($usuarioId, $otroUsuarioId), max($usuarioId, $otroUsuarioId)]
                        );
                    } catch (\Exception $e) {
                        // Silencioso
                    }
                }
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error al marcar mensajes como leídos: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar conversación específica
     */
    public function conversacion($contactoId)
    {
        $usuarioId = session()->get('usuario_id');
        
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
        $usuarioId = session()->get('usuario_id');
        
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
            // Verificar autenticación - usar múltiples métodos para compatibilidad
            $usuarioId = session()->get('usuario_id') 
                      ?? session()->get('idusuario') 
                      ?? null;
            
            // Si aún no hay usuario_id, verificar si hay sesión activa
            if (!$usuarioId && session()->get('usuario_logueado')) {
                // Intentar obtener de otras variables de sesión
                $usuarioId = session()->get('usuario_id');
            }
            
            if (!$usuarioId) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Usuario no autenticado. Por favor, inicie sesión nuevamente.'
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

            // Sin restricción de caracteres mínimos - solo que no esté vacío
            if (!$asunto || strlen(trim($asunto)) === 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'El asunto no puede estar vacío'
                ]);
            }

            if (!$contenido || strlen(trim($contenido)) === 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'El contenido no puede estar vacío'
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

            // Adjuntos: guardar en sistema de archivos (optimizado)
            $file = $this->request->getFile('archivo');
            $archivoMetadata = null;
            $archivoPath = null;
            
            if ($file && $file->isValid()) {
                // Límite: 30 MB
                $maxBytes = 30 * 1024 * 1024;
                if ($file->getSize() > $maxBytes) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'El archivo excede el límite de 30 MB'
                    ]);
                }
                
                // Crear directorio si no existe
                $uploadDir = ROOTPATH . 'public/uploads/mensajeria/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Generar nombre único para el archivo
                $extension = $file->getClientExtension();
                $newName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
                $fullPath = $uploadDir . $newName;
                
                // Mover archivo directamente (más rápido que cargar en memoria)
                if ($file->move($uploadDir, $newName)) {
                    $archivoPath = 'uploads/mensajeria/' . $newName;
                    
                    // Optimizar imagen si es una imagen grande (reducir tamaño)
                    $mimeType = $file->getClientMimeType();
                    if (strpos($mimeType, 'image/') === 0 && $file->getSize() > 1024 * 1024) { // > 1MB
                        $this->optimizarImagen($fullPath, $mimeType);
                    }
                    
                    // Obtener tamaño final después de optimización
                    $tamanioFinal = file_exists($fullPath) ? filesize($fullPath) : $file->getSize();
                    
                    $archivoMetadata = [
                        'nombre_original' => $file->getClientName(),
                        'mime' => $mimeType ?: 'application/octet-stream',
                        'tamanio' => $tamanioFinal,
                        'path' => $archivoPath,
                    ];
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Error al guardar el archivo'
                    ]);
                }
            }

            // Insertar mensaje directamente con SQL
            $db = \Config\Database::connect();
            
            // Verificar si la columna status existe
            $columnas = $db->getFieldNames('mensajes');
            $tieneStatus = in_array('status', $columnas);
            
            if ($tieneStatus) {
                $sql = "INSERT INTO mensajes (remitente_id, destinatario_id, asunto, contenido, tipo, leido, status, fecha_envio) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $params = [
                    $data['remitente_id'],
                    $data['destinatario_id'], 
                    $data['asunto'],
                    $data['contenido'],
                    $data['tipo'],
                    $data['leido'],
                    'sent',
                    $data['fecha_envio']
                ];
            } else {
                // Si no tiene status, insertar sin esa columna
                $sql = "INSERT INTO mensajes (remitente_id, destinatario_id, asunto, contenido, tipo, leido, fecha_envio) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $params = [
                    $data['remitente_id'],
                    $data['destinatario_id'], 
                    $data['asunto'],
                    $data['contenido'],
                    $data['tipo'],
                    $data['leido'],
                    $data['fecha_envio']
                ];
            }
            
            $result = $db->query($sql, $params);
            
            if (!$result) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al guardar el mensaje en la base de datos'
                ]);
            }

            $mensajeId = $db->insertID();

            // Si hubo adjunto, guardar referencia en BD y anexar link directo
            if ($archivoMetadata && $archivoPath) {
                // Guardar solo metadatos en BD (no el archivo completo)
                $db->table('archivos_mensaje')->insert([
                    'mensaje_id' => $mensajeId,
                    'nombre_original' => $archivoMetadata['nombre_original'],
                    'mime' => $archivoMetadata['mime'],
                    'tamanio' => $archivoMetadata['tamanio'],
                    'path' => $archivoPath, // Ruta del archivo en lugar de datos BLOB
                ]);
                
                // Crear enlace directo al archivo (más rápido)
                $enlace = base_url($archivoPath);
                $db->query("UPDATE mensajes SET contenido = CONCAT(contenido, '\n', '[archivo] ', ?) WHERE id = ?", [$enlace, $mensajeId]);
            }

            // Actualizar o crear conversación en la tabla conversaciones
            try {
                // Normalizar IDs para evitar duplicados (menor primero)
                $usuario1 = min($usuarioId, $destinatarioId);
                $usuario2 = max($usuarioId, $destinatarioId);
                
                // Verificar si existe la conversación
                $convCheck = $db->query(
                    "SELECT id FROM conversaciones 
                     WHERE usuario1_id = ? AND usuario2_id = ?",
                    [$usuario1, $usuario2]
                );
                
                if ($convCheck->getNumRows() > 0) {
                    // Actualizar conversación existente
                    if ($usuarioId == $usuario1) {
                        // Si el remitente es usuario1, incrementar contador de usuario2
                        $db->query(
                            "UPDATE conversaciones 
                             SET ultimo_mensaje_id = ?, 
                                 fecha_ultimo_mensaje = ?,
                                 mensajes_no_leidos_usuario2 = mensajes_no_leidos_usuario2 + 1
                             WHERE usuario1_id = ? AND usuario2_id = ?",
                            [$mensajeId, $data['fecha_envio'], $usuario1, $usuario2]
                        );
                    } else {
                        // Si el remitente es usuario2, incrementar contador de usuario1
                        $db->query(
                            "UPDATE conversaciones 
                             SET ultimo_mensaje_id = ?, 
                                 fecha_ultimo_mensaje = ?,
                                 mensajes_no_leidos_usuario1 = mensajes_no_leidos_usuario1 + 1
                             WHERE usuario1_id = ? AND usuario2_id = ?",
                            [$mensajeId, $data['fecha_envio'], $usuario1, $usuario2]
                        );
                    }
                } else {
                    // Crear nueva conversación
                    $db->query(
                        "INSERT INTO conversaciones 
                         (usuario1_id, usuario2_id, ultimo_mensaje_id, fecha_ultimo_mensaje, mensajes_no_leidos_usuario1, mensajes_no_leidos_usuario2)
                         VALUES (?, ?, ?, ?, ?, ?)",
                        [
                            $usuario1, 
                            $usuario2, 
                            $mensajeId, 
                            $data['fecha_envio'],
                            $usuarioId == $usuario1 ? 0 : 1, // Si remitente es usuario1, usuario2 tiene 1 no leído
                            $usuarioId == $usuario2 ? 0 : 1  // Si remitente es usuario2, usuario1 tiene 1 no leído
                        ]
                    );
                }
            } catch (\Exception $e) {
                // Silencioso si falla, los triggers pueden manejar esto
                log_message('debug', 'No se pudo actualizar conversación: ' . $e->getMessage());
            }

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
     * Descargar/servir archivo adjunto desde sistema de archivos o BD (retrocompatibilidad)
     */
    public function archivo($id)
    {
        $db = \Config\Database::connect();
        $row = $db->table('archivos_mensaje')->where('id', $id)->get()->getRowArray();
        if (!$row) {
            return $this->response->setStatusCode(404, 'Archivo no encontrado');
        }
        
        // Si tiene path (nuevo sistema), servir desde archivo
        if (!empty($row['path']) && file_exists(ROOTPATH . 'public/' . $row['path'])) {
            $filePath = ROOTPATH . 'public/' . $row['path'];
            $fileContent = file_get_contents($filePath);
            $actualSize = filesize($filePath);
            
            return $this->response
                ->setHeader('Content-Type', $row['mime'])
                ->setHeader('Content-Length', (string)$actualSize)
                ->setHeader('Content-Disposition', 'inline; filename="' . $row['nombre_original'] . '"')
                ->setHeader('Cache-Control', 'public, max-age=31536000')
                ->setBody($fileContent);
        }
        
        // Retrocompatibilidad: servir desde BD si existe datos BLOB
        if (!empty($row['datos'])) {
            return $this->response
                ->setHeader('Content-Type', $row['mime'])
                ->setHeader('Content-Length', (string)$row['tamanio'])
                ->setHeader('Content-Disposition', 'inline; filename="' . $row['nombre_original'] . '"')
                ->setBody($row['datos']);
        }
        
        return $this->response->setStatusCode(404, 'Archivo no encontrado');
    }
    
    /**
     * Optimizar imagen para reducir tamaño
     */
    private function optimizarImagen($filePath, $mimeType)
    {
        try {
            // Solo optimizar si GD está disponible
            if (!extension_loaded('gd')) {
                return;
            }
            
            $maxWidth = 1920;
            $maxHeight = 1080;
            $quality = 85;
            
            $image = null;
            
            // Cargar imagen según tipo
            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = imagecreatefromjpeg($filePath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($filePath);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($filePath);
                    break;
                default:
                    return; // No optimizar otros formatos
            }
            
            if (!$image) {
                return;
            }
            
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);
            
            // Si la imagen es más pequeña que el máximo, no hacer nada
            if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
                imagedestroy($image);
                return;
            }
            
            // Calcular nuevas dimensiones manteniendo proporción
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            $newWidth = (int)($originalWidth * $ratio);
            $newHeight = (int)($originalHeight * $ratio);
            
            // Crear nueva imagen redimensionada
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preservar transparencia para PNG
            if ($mimeType === 'image/png') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
            }
            
            // Redimensionar
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            
            // Guardar imagen optimizada
            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    imagejpeg($newImage, $filePath, $quality);
                    break;
                case 'image/png':
                    imagepng($newImage, $filePath, 9);
                    break;
                case 'image/gif':
                    imagegif($newImage, $filePath);
                    break;
            }
            
            // Liberar memoria
            imagedestroy($image);
            imagedestroy($newImage);
            
        } catch (\Exception $e) {
            // Si falla la optimización, continuar con el archivo original
            log_message('debug', 'Error al optimizar imagen: ' . $e->getMessage());
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

        $usuarioId = session()->get('usuario_id');
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

        $usuarioId = session()->get('usuario_id');
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

        $usuarioId = session()->get('usuario_id');
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

        $usuarioId = session()->get('usuario_id');
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
        $usuarioId = session()->get('usuario_id');
        
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

        $usuarioId = session()->get('usuario_id');

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

        $usuarioId = session()->get('usuario_id');
        
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
