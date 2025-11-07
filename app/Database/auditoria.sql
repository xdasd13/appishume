USE ishume;

-- 1. CREAR TABLA DE AUDITORÍA
CREATE TABLE auditoria_kanban (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idequipo INT NOT NULL,
    idusuario INT NOT NULL,
    accion VARCHAR(50) NOT NULL COMMENT 'crear, cambiar_estado, reasignar',
    estado_anterior VARCHAR(20) NULL COMMENT 'NULL si es nuevo',
    estado_nuevo VARCHAR(20),
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_auditoria_equipo FOREIGN KEY (idequipo) REFERENCES equipos(idequipo),
    CONSTRAINT fk_auditoria_usuario FOREIGN KEY (idusuario) REFERENCES usuarios(idusuario)
) ENGINE=InnoDB COMMENT='Registro de todos los cambios en el Kanban';

-- 2. CREAR ÍNDICES PARA MEJORAR RENDIMIENTO
CREATE INDEX idx_equipo ON auditoria_kanban(idequipo);
CREATE INDEX idx_usuario ON auditoria_kanban(idusuario);
CREATE INDEX idx_fecha ON auditoria_kanban(fecha);

-- 3. MODIFICACIONES EN LA TABLA EQUIPOS (agregar campos de auditoría)
ALTER TABLE equipos 
    ADD COLUMN fecha_ultima_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
        COMMENT 'Se actualiza automáticamente en cada cambio',
    ADD COLUMN idusuario_ultima_modificacion INT NULL 
        COMMENT 'Usuario que hizo el último cambio',
    ADD CONSTRAINT fk_equipo_modificador 
        FOREIGN KEY (idusuario_ultima_modificacion) REFERENCES usuarios(idusuario);
-- Verificación rápida:
SELECT 'Tablas creadas correctamente' AS resultado;
SHOW TABLES LIKE '%auditoria%';
DESCRIBE equipos;