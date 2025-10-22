-- =============================================
-- SISTEMA DE MENSAJERÍA INTERNA - ISHUME
-- =============================================

-- Tabla de mensajes
CREATE TABLE IF NOT EXISTS mensajes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    remitente_id INT NOT NULL,
    destinatario_id INT NOT NULL,
    asunto VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    tipo ENUM('normal', 'importante', 'urgente') NOT NULL DEFAULT 'normal',
    leido BOOLEAN DEFAULT FALSE,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_leido DATETIME NULL,
    eliminado_remitente BOOLEAN DEFAULT FALSE,
    eliminado_destinatario BOOLEAN DEFAULT FALSE,
    INDEX idx_destinatario (destinatario_id),
    INDEX idx_remitente (remitente_id),
    INDEX idx_fecha_envio (fecha_envio),
    INDEX idx_tipo (tipo),
    FOREIGN KEY (remitente_id) REFERENCES usuarios(idusuario) ON DELETE CASCADE,
    FOREIGN KEY (destinatario_id) REFERENCES usuarios(idusuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de notificaciones
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    tipo ENUM('mensaje', 'mensaje_importante', 'mensaje_urgente', 'sistema') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    leida BOOLEAN DEFAULT FALSE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_leida DATETIME NULL,
    datos_extra JSON NULL, -- Para datos adicionales como ID del mensaje
    INDEX idx_usuario (usuario_id),
    INDEX idx_tipo (tipo),
    INDEX idx_fecha_creacion (fecha_creacion),
    INDEX idx_leida (leida),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(idusuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de conversaciones (para agrupar mensajes)
CREATE TABLE IF NOT EXISTS conversaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario1_id INT NOT NULL,
    usuario2_id INT NOT NULL,
    ultimo_mensaje_id INT NULL,
    fecha_ultimo_mensaje DATETIME NULL,
    mensajes_no_leidos_usuario1 INT DEFAULT 0,
    mensajes_no_leidos_usuario2 INT DEFAULT 0,
    INDEX idx_usuario1 (usuario1_id),
    INDEX idx_usuario2 (usuario2_id),
    INDEX idx_ultimo_mensaje (fecha_ultimo_mensaje),
    FOREIGN KEY (usuario1_id) REFERENCES usuarios(idusuario) ON DELETE CASCADE,
    FOREIGN KEY (usuario2_id) REFERENCES usuarios(idusuario) ON DELETE CASCADE,
    FOREIGN KEY (ultimo_mensaje_id) REFERENCES mensajes(id) ON DELETE SET NULL,
    UNIQUE KEY unique_conversation (usuario1_id, usuario2_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de configuración de notificaciones por usuario
CREATE TABLE IF NOT EXISTS configuracion_notificaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    notificaciones_mensajes BOOLEAN DEFAULT TRUE,
    notificaciones_importantes BOOLEAN DEFAULT TRUE,
    notificaciones_urgentes BOOLEAN DEFAULT TRUE,
    notificaciones_sistema BOOLEAN DEFAULT TRUE,
    sonido_notificaciones BOOLEAN DEFAULT TRUE,
    email_notificaciones BOOLEAN DEFAULT FALSE,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_usuario (usuario_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(idusuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuración por defecto para usuarios existentes
INSERT IGNORE INTO configuracion_notificaciones (usuario_id)
SELECT idusuario FROM usuarios WHERE estado = 1;

-- Triggers para actualizar contadores automáticamente
DELIMITER //

-- Trigger para crear notificación cuando se envía un mensaje
CREATE TRIGGER tr_mensaje_enviado 
AFTER INSERT ON mensajes
FOR EACH ROW
BEGIN
    DECLARE tipo_notificacion VARCHAR(20);
    DECLARE titulo_notificacion VARCHAR(255);
    
    -- Determinar tipo de notificación según el tipo de mensaje
    CASE NEW.tipo
        WHEN 'urgente' THEN SET tipo_notificacion = 'mensaje_urgente';
        WHEN 'importante' THEN SET tipo_notificacion = 'mensaje_importante';
        ELSE SET tipo_notificacion = 'mensaje';
    END CASE;
    
    -- Crear título según el tipo
    CASE NEW.tipo
        WHEN 'urgente' THEN SET titulo_notificacion = '🚨 Mensaje Urgente';
        WHEN 'importante' THEN SET titulo_notificacion = '⚠️ Mensaje Importante';
        ELSE SET titulo_notificacion = '💬 Nuevo Mensaje';
    END CASE;
    
    -- Insertar notificación
    INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, datos_extra)
    VALUES (
        NEW.destinatario_id, 
        tipo_notificacion, 
        titulo_notificacion,
        CONCAT('De: ', (SELECT CONCAT(nombres, ' ', apellidos) FROM personas p JOIN usuarios u ON p.idpersona = u.idpersona WHERE u.idusuario = NEW.remitente_id), ' - ', NEW.asunto),
        JSON_OBJECT('mensaje_id', NEW.id, 'remitente_id', NEW.remitente_id)
    );
    
    -- Actualizar o crear conversación
    INSERT INTO conversaciones (usuario1_id, usuario2_id, ultimo_mensaje_id, fecha_ultimo_mensaje, mensajes_no_leidos_usuario2)
    VALUES (NEW.remitente_id, NEW.destinatario_id, NEW.id, NEW.fecha_envio, 1)
    ON DUPLICATE KEY UPDATE 
        ultimo_mensaje_id = NEW.id,
        fecha_ultimo_mensaje = NEW.fecha_envio,
        mensajes_no_leidos_usuario2 = mensajes_no_leidos_usuario2 + 1;
        
    -- También crear conversación inversa si no existe
    INSERT IGNORE INTO conversaciones (usuario1_id, usuario2_id, ultimo_mensaje_id, fecha_ultimo_mensaje)
    VALUES (NEW.destinatario_id, NEW.remitente_id, NEW.id, NEW.fecha_envio);
END//

-- Trigger para actualizar cuando se marca mensaje como leído
CREATE TRIGGER tr_mensaje_leido 
AFTER UPDATE ON mensajes
FOR EACH ROW
BEGIN
    IF OLD.leido = FALSE AND NEW.leido = TRUE THEN
        -- Actualizar fecha de lectura
        UPDATE mensajes SET fecha_leido = NOW() WHERE id = NEW.id;
        
        -- Actualizar contador de conversación
        UPDATE conversaciones 
        SET mensajes_no_leidos_usuario2 = GREATEST(0, mensajes_no_leidos_usuario2 - 1)
        WHERE usuario1_id = NEW.remitente_id AND usuario2_id = NEW.destinatario_id;
    END IF;
END//

DELIMITER ;

-- Vista para obtener mensajes con información del remitente
CREATE VIEW v_mensajes_completos AS
SELECT 
    m.*,
    CONCAT(p_rem.nombres, ' ', p_rem.apellidos) as remitente_nombre,
    u_rem.nombreusuario as remitente_usuario,
    u_rem.email as remitente_email,
    CONCAT(p_dest.nombres, ' ', p_dest.apellidos) as destinatario_nombre,
    u_dest.nombreusuario as destinatario_usuario,
    u_dest.email as destinatario_email
FROM mensajes m
JOIN usuarios u_rem ON m.remitente_id = u_rem.idusuario
JOIN personas p_rem ON u_rem.idpersona = p_rem.idpersona
JOIN usuarios u_dest ON m.destinatario_id = u_dest.idusuario
JOIN personas p_dest ON u_dest.idpersona = p_dest.idpersona;

-- Vista para obtener conversaciones con información de usuarios
CREATE VIEW v_conversaciones_completas AS
SELECT 
    c.*,
    CONCAT(p1.nombres, ' ', p1.apellidos) as usuario1_nombre,
    u1.nombreusuario as usuario1_usuario,
    CONCAT(p2.nombres, ' ', p2.apellidos) as usuario2_nombre,
    u2.nombreusuario as usuario2_usuario,
    m.asunto as ultimo_asunto,
    m.contenido as ultimo_contenido,
    m.tipo as ultimo_tipo
FROM conversaciones c
JOIN usuarios u1 ON c.usuario1_id = u1.idusuario
JOIN personas p1 ON u1.idpersona = p1.idpersona
JOIN usuarios u2 ON c.usuario2_id = u2.idusuario
JOIN personas p2 ON u2.idpersona = p2.idpersona
LEFT JOIN mensajes m ON c.ultimo_mensaje_id = m.id;
