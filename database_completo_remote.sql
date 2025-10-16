-- ========================================
-- SCRIPT SQL COMPLETO PARA ISHUME
-- Base de datos remota: freesqldatabase.com
-- COMPATIBLE CON MYSQL/HEIDISQL - SIN TRIGGERS
-- ========================================

-- Usar la base de datos
USE sql10803359;

-- ========================================
-- ELIMINAR TABLAS EN ORDEN CORRECTO (HIJAS PRIMERO)
-- ========================================

-- Eliminar vistas
DROP VIEW IF EXISTS v_usuarios_completos;
DROP VIEW IF EXISTS v_contratos_completos;

-- Eliminar tablas con dependencias (hijas primero)
DROP TABLE IF EXISTS mensajes;
DROP TABLE IF EXISTS notificaciones;
DROP TABLE IF EXISTS controlpagos;
DROP TABLE IF EXISTS entregas;
DROP TABLE IF EXISTS inventario;
DROP TABLE IF EXISTS servicioscontratados;
DROP TABLE IF EXISTS contratos;
DROP TABLE IF EXISTS proyectos;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS equipos;
DROP TABLE IF EXISTS servicios;
DROP TABLE IF EXISTS tipocontrato;
DROP TABLE IF EXISTS condiciones;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS cargos;
DROP TABLE IF EXISTS empresas;
DROP TABLE IF EXISTS personas;

-- ========================================
-- CREAR TABLAS EN ORDEN CORRECTO (PADRES PRIMERO)
-- ========================================

-- Tabla de personas (sin dependencias)
CREATE TABLE personas (
    idpersona INT AUTO_INCREMENT PRIMARY KEY,
    apellidos VARCHAR(100) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    tipodoc ENUM('DNI', 'Carne de Extranjeria', 'Pasaporte') DEFAULT 'DNI' NOT NULL,
    numerodoc VARCHAR(12) NOT NULL UNIQUE,
    telprincipal CHAR(9) NOT NULL,
    telalternativo CHAR(9) NULL,
    direccion VARCHAR(150) NOT NULL,
    referencia VARCHAR(150) NULL
);

-- Tabla de empresas (sin dependencias)
CREATE TABLE empresas (
    idempresa INT AUTO_INCREMENT PRIMARY KEY,
    ruc CHAR(11) NOT NULL,
    razonsocial VARCHAR(150) NOT NULL,
    direccion VARCHAR(150) NOT NULL,
    telefono CHAR(9) NOT NULL
);

-- Tabla de cargos (sin dependencias)
CREATE TABLE cargos (
    idcargo INT AUTO_INCREMENT PRIMARY KEY,
    cargo VARCHAR(100) NOT NULL
);

-- Tabla de categorías (sin dependencias)
CREATE TABLE categorias (
    idcategoria INT AUTO_INCREMENT PRIMARY KEY,
    categoria VARCHAR(100) NOT NULL
);

-- Tabla de condiciones (sin dependencias)
CREATE TABLE condiciones (
    idcondicion INT AUTO_INCREMENT PRIMARY KEY,
    condicion VARCHAR(100) NOT NULL
);

-- Tabla de tipo de contrato (sin dependencias)
CREATE TABLE tipocontrato (
    idtipocontrato INT AUTO_INCREMENT PRIMARY KEY,
    tipocontrato VARCHAR(100) NOT NULL,
    vigenciadias INT
);

-- Tabla de servicios (sin dependencias)
CREATE TABLE servicios (
    idservicio INT AUTO_INCREMENT PRIMARY KEY,
    servicio VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    estado TINYINT(1) DEFAULT 1
);

-- Tabla de equipos (sin dependencias)
CREATE TABLE equipos (
    idequipo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2),
    estado ENUM('disponible', 'en_uso', 'mantenimiento', 'danado') DEFAULT 'disponible'
);

-- Tabla de clientes (depende de personas y empresas)
CREATE TABLE clientes (
    idcliente INT AUTO_INCREMENT PRIMARY KEY,
    idpersona INT,
    idempresa INT,
    CONSTRAINT fk_cliente_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona),
    CONSTRAINT fk_cliente_empresa FOREIGN KEY (idempresa) REFERENCES empresas(idempresa)
);

-- Tabla de usuarios (depende de personas y cargos)
CREATE TABLE usuarios (
    idusuario INT AUTO_INCREMENT PRIMARY KEY,
    idpersona INT NOT NULL,
    idcargo INT NOT NULL,
    nombreusuario VARCHAR(50) NOT NULL UNIQUE,
    claveacceso VARCHAR(100),
    password_hash VARCHAR(255),
    tipo_usuario ENUM('admin', 'trabajador') DEFAULT 'trabajador',
    email VARCHAR(100),
    estado TINYINT(1) DEFAULT 1,
    CONSTRAINT fk_usuario_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona),
    CONSTRAINT fk_usuario_cargo FOREIGN KEY (idcargo) REFERENCES cargos(idcargo)
);

-- Tabla de proyectos (depende de clientes)
CREATE TABLE proyectos (
    idproyecto INT AUTO_INCREMENT PRIMARY KEY,
    nombreproyecto VARCHAR(150) NOT NULL,
    descripcion TEXT,
    fechainicio DATE,
    fechafin DATE,
    estado ENUM('activo', 'completado', 'cancelado') DEFAULT 'activo',
    idcliente INT,
    CONSTRAINT fk_proyecto_cliente FOREIGN KEY (idcliente) REFERENCES clientes(idcliente)
);

-- Tabla de contratos (depende de clientes y tipocontrato)
CREATE TABLE contratos (
    idcontrato INT AUTO_INCREMENT PRIMARY KEY,
    idcliente INT NOT NULL,
    idtipocontrato INT NOT NULL,
    fechainicio DATE NOT NULL,
    fechafin DATE NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    estado ENUM('activo', 'vencido', 'cancelado') DEFAULT 'activo',
    CONSTRAINT fk_contrato_cliente FOREIGN KEY (idcliente) REFERENCES clientes(idcliente),
    CONSTRAINT fk_contrato_tipo FOREIGN KEY (idtipocontrato) REFERENCES tipocontrato(idtipocontrato)
);

-- Tabla de servicios contratados (depende de contratos y servicios)
CREATE TABLE servicioscontratados (
    idserviciocontratado INT AUTO_INCREMENT PRIMARY KEY,
    idcontrato INT NOT NULL,
    idservicio INT NOT NULL,
    cantidad INT DEFAULT 1,
    precio DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_servicio_contrato FOREIGN KEY (idcontrato) REFERENCES contratos(idcontrato),
    CONSTRAINT fk_servicio_servicio FOREIGN KEY (idservicio) REFERENCES servicios(idservicio)
);

-- Tabla de inventario (depende de equipos)
CREATE TABLE inventario (
    idinventario INT AUTO_INCREMENT PRIMARY KEY,
    idequipo INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    ubicacion VARCHAR(100),
    fechaingreso DATE DEFAULT NULL,
    CONSTRAINT fk_inventario_equipo FOREIGN KEY (idequipo) REFERENCES equipos(idequipo)
);

-- Tabla de entregas (depende de contratos y equipos)
CREATE TABLE entregas (
    identrega INT AUTO_INCREMENT PRIMARY KEY,
    idcontrato INT NOT NULL,
    idequipo INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    fechaentrega DATE DEFAULT NULL,
    fechadevolucion DATE,
    estado ENUM('entregado', 'devuelto', 'pendiente') DEFAULT 'entregado',
    observaciones TEXT,
    CONSTRAINT fk_entrega_contrato FOREIGN KEY (idcontrato) REFERENCES contratos(idcontrato),
    CONSTRAINT fk_entrega_equipo FOREIGN KEY (idequipo) REFERENCES equipos(idequipo)
);

-- Tabla de control de pagos (depende de contratos)
CREATE TABLE controlpagos (
    idcontrolpago INT AUTO_INCREMENT PRIMARY KEY,
    idcontrato INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fechapago DATE DEFAULT NULL,
    metodopago ENUM('efectivo', 'transferencia', 'cheque', 'tarjeta') DEFAULT 'efectivo',
    observaciones TEXT,
    CONSTRAINT fk_pago_contrato FOREIGN KEY (idcontrato) REFERENCES contratos(idcontrato)
);

-- ========================================
-- TABLAS DE MENSAJERÍA
-- ========================================

-- Tabla de mensajes (depende de usuarios)
CREATE TABLE mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    remitente_id INT NOT NULL,
    destinatario_id INT NOT NULL,
    asunto VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    tipo ENUM('normal', 'importante', 'urgente') DEFAULT 'normal',
    leido BOOLEAN DEFAULT FALSE,
    fecha_envio DATETIME DEFAULT NULL,
    fecha_eliminacion DATETIME NULL,
    INDEX idx_remitente (remitente_id),
    INDEX idx_destinatario (destinatario_id),
    INDEX idx_fecha_envio (fecha_envio),
    CONSTRAINT fk_mensaje_remitente FOREIGN KEY (remitente_id) REFERENCES usuarios(idusuario),
    CONSTRAINT fk_mensaje_destinatario FOREIGN KEY (destinatario_id) REFERENCES usuarios(idusuario)
);

-- Tabla de notificaciones (depende de usuarios)
CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('mensaje', 'mensaje_importante', 'mensaje_urgente', 'sistema') DEFAULT 'mensaje',
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    url VARCHAR(500) NULL,
    leida BOOLEAN DEFAULT FALSE,
    fecha_creacion DATETIME DEFAULT NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_leida (leida),
    INDEX idx_fecha_creacion (fecha_creacion),
    CONSTRAINT fk_notificacion_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(idusuario)
);

-- ========================================
-- DATOS INICIALES
-- ========================================

-- Insertar cargos básicos
INSERT INTO cargos (cargo) VALUES 
('Administrador'),
('Trabajador'),
('Supervisor'),
('Tecnico');

-- Insertar categorías básicas
INSERT INTO categorias (categoria) VALUES 
('Equipos de Computo'),
('Equipos de Red'),
('Equipos de Seguridad'),
('Equipos de Oficina');

-- Insertar condiciones básicas
INSERT INTO condiciones (condicion) VALUES 
('Nuevo'),
('Usado'),
('Refurbished'),
('Danado');

-- Insertar tipos de contrato
INSERT INTO tipocontrato (tipocontrato, vigenciadias) VALUES 
('Mensual', 30),
('Trimestral', 90),
('Semestral', 180),
('Anual', 365);

-- Insertar servicios básicos
INSERT INTO servicios (servicio, descripcion, precio) VALUES 
('Mantenimiento de Equipos', 'Servicio de mantenimiento preventivo y correctivo', 150.00),
('Instalacion de Software', 'Instalacion y configuracion de software', 80.00),
('Soporte Tecnico', 'Soporte tecnico remoto y presencial', 100.00),
('Consultoria IT', 'Servicios de consultoria en tecnologia', 200.00);

-- Insertar equipos básicos
INSERT INTO equipos (nombre, descripcion, precio, estado) VALUES 
('Laptop Dell Inspiron', 'Laptop para trabajo de oficina', 2500.00, 'disponible'),
('Desktop HP Pavilion', 'Computadora de escritorio', 1800.00, 'disponible'),
('Router TP-Link', 'Router inalambrico', 120.00, 'disponible'),
('Switch 24 Puertos', 'Switch de red 24 puertos', 300.00, 'disponible');

-- Insertar inventario inicial
INSERT INTO inventario (idequipo, cantidad, ubicacion, fechaingreso) VALUES 
(1, 5, 'Almacen Principal', CURDATE()),
(2, 3, 'Almacen Principal', CURDATE()),
(3, 10, 'Almacen Red', CURDATE()),
(4, 2, 'Almacen Red', CURDATE());

-- ========================================
-- USUARIO ADMINISTRADOR POR DEFECTO
-- ========================================

-- Insertar persona administrador
INSERT INTO personas (apellidos, nombres, tipodoc, numerodoc, telprincipal, direccion) VALUES 
('Administrador', 'Sistema', 'DNI', '12345678', '999999999', 'Direccion del Sistema');

-- Insertar usuario administrador
INSERT INTO usuarios (idpersona, idcargo, nombreusuario, claveacceso, tipo_usuario, email, estado) VALUES 
(1, 1, 'admin', 'admin123', 'admin', 'admin@sistema.com', 1);

-- ========================================
-- ÍNDICES ADICIONALES PARA RENDIMIENTO
-- ========================================

-- Índices para búsquedas frecuentes
CREATE INDEX idx_personas_doc ON personas(numerodoc);
CREATE INDEX idx_usuarios_username ON usuarios(nombreusuario);
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_contratos_fecha ON contratos(fechainicio, fechafin);
CREATE INDEX idx_entregas_fecha ON entregas(fechaentrega);
CREATE INDEX idx_pagos_fecha ON controlpagos(fechapago);

-- ========================================
-- VISTAS ÚTILES
-- ========================================

-- Vista de usuarios completos
CREATE VIEW v_usuarios_completos AS
SELECT 
    u.idusuario,
    u.nombreusuario,
    u.email,
    u.tipo_usuario,
    u.estado,
    p.nombres,
    p.apellidos,
    c.cargo
FROM usuarios u
JOIN personas p ON u.idpersona = p.idpersona
JOIN cargos c ON u.idcargo = c.idcargo;

-- Vista de contratos con información del cliente
CREATE VIEW v_contratos_completos AS
SELECT 
    co.idcontrato,
    co.fechainicio,
    co.fechafin,
    co.monto,
    co.estado,
    tc.tipocontrato,
    CASE 
        WHEN cl.idpersona IS NOT NULL THEN CONCAT(p.nombres, ' ', p.apellidos)
        WHEN cl.idempresa IS NOT NULL THEN e.razonsocial
    END as cliente_nombre
FROM contratos co
JOIN tipocontrato tc ON co.idtipocontrato = tc.idtipocontrato
JOIN clientes cl ON co.idcliente = cl.idcliente
LEFT JOIN personas p ON cl.idpersona = p.idpersona
LEFT JOIN empresas e ON cl.idempresa = e.idempresa;

-- ========================================
-- FIN DEL SCRIPT
-- ========================================

-- Mensaje de confirmación
SELECT 'Base de datos ISHUME configurada correctamente' as mensaje;