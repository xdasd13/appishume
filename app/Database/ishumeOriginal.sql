CREATE DATABASE ishume;
USE ishume;

CREATE TABLE cargos (
    idcargo INT AUTO_INCREMENT PRIMARY KEY,
    cargo VARCHAR(100) NOT NULL
);

CREATE TABLE categorias (
    idcategoria INT AUTO_INCREMENT PRIMARY KEY,
    categoria VARCHAR(100) NOT NULL
);

CREATE TABLE personas (
    idpersona       INT AUTO_INCREMENT PRIMARY KEY,
    apellidos       VARCHAR(100) NOT NULL,
    nombres         VARCHAR(100) NOT NULL,
    tipodoc         ENUM ('DNI', 'Carne de Extranjería', 'Pasaporte') DEFAULT 'DNI' NOT NULL,
    numerodoc       VARCHAR(12) NOT NULL UNIQUE,
    telprincipal    CHAR(9) NOT NULL,
    telalternativo  CHAR(9) NULL,
    direccion       VARCHAR(150) NOT NULL,
    referencia      VARCHAR(150) NULL
);

CREATE TABLE empresas (
    idempresa       INT AUTO_INCREMENT PRIMARY KEY,
    ruc             CHAR(11) NOT NULL,
    razonsocial     VARCHAR(150) NOT NULL,
    direccion       VARCHAR(150) NOT NULL,
    telefono        CHAR(9) NOT NULL
);

CREATE TABLE clientes (
    idcliente INT AUTO_INCREMENT PRIMARY KEY,
    idpersona INT,
    idempresa INT,
    CONSTRAINT fk_cliente_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona),
    CONSTRAINT fk_cliente_empresa FOREIGN KEY (idempresa) REFERENCES empresas(idempresa)
);

CREATE TABLE condiciones (
    idcondicion INT AUTO_INCREMENT PRIMARY KEY,
    condicion VARCHAR(100) NOT NULL
);

CREATE TABLE tipocontrato (
    idtipocontrato INT AUTO_INCREMENT PRIMARY KEY,
    tipocontrato VARCHAR(100) NOT NULL,
    vigenciadias INT
);

CREATE TABLE tipoeventos (
    idtipoevento INT AUTO_INCREMENT PRIMARY KEY,
    evento VARCHAR(100) NOT NULL
);

CREATE TABLE tipospago (
    idtipopago INT AUTO_INCREMENT PRIMARY KEY,
    tipopago VARCHAR(100) NOT NULL
);

CREATE TABLE usuarios (
    idusuario INT AUTO_INCREMENT PRIMARY KEY,
    idpersona INT,
    idcargo INT,
    nombreusuario VARCHAR(50) UNIQUE NOT NULL,
    claveacceso VARCHAR(255) NOT NULL,
    estado TINYINT DEFAULT 1,
    tipo_usuario ENUM('admin', 'trabajador') DEFAULT 'trabajador',
    email VARCHAR(100) UNIQUE,
    password_hash VARCHAR(255),
    CONSTRAINT fk_usuario_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona),
    CONSTRAINT fk_usuario_cargo FOREIGN KEY (idcargo) REFERENCES cargos(idcargo)
);

CREATE TABLE cotizaciones (
    idcotizacion INT AUTO_INCREMENT PRIMARY KEY,
    idcliente INT,
    idtipocontrato INT,
    idusuariocrea INT,
    fechacotizacion DATE,
    fechaevento DATE,
    idtipoevento INT,
    CONSTRAINT fk_cotizacion_cliente FOREIGN KEY (idcliente) REFERENCES clientes(idcliente),
    CONSTRAINT fk_cotizacion_tipocontrato FOREIGN KEY (idtipocontrato) REFERENCES tipocontrato(idtipocontrato),
    CONSTRAINT fk_cotizacion_usuario FOREIGN KEY (idusuariocrea) REFERENCES usuarios(idusuario),
    CONSTRAINT fk_cotizacion_evento FOREIGN KEY (idtipoevento) REFERENCES tipoeventos(idtipoevento)
);

CREATE TABLE contratos (
    idcontrato INT AUTO_INCREMENT PRIMARY KEY,
    idcotizacion INT,
    idcliente INT,
    autorizapublicacion TINYINT DEFAULT 0,
    CONSTRAINT fk_contrato_cotizacion FOREIGN KEY (idcotizacion) REFERENCES cotizaciones(idcotizacion),
    CONSTRAINT fk_contrato_cliente FOREIGN KEY (idcliente) REFERENCES clientes(idcliente)
);

CREATE TABLE controlpagos (
    idpagos INT AUTO_INCREMENT PRIMARY KEY,
    idcontrato INT,
    saldo DECIMAL(10,2),
    amortizacion DECIMAL(10,2),
    deuda DECIMAL(10,2),
    idtipopago INT,
    numtransaccion VARCHAR(50),
    fechahora DATETIME,
    idusuario INT,
    comprobante VARCHAR(255) NULL,
    CONSTRAINT fk_pago_contrato FOREIGN KEY (idcontrato) REFERENCES contratos(idcontrato),
    CONSTRAINT fk_pago_tipopago FOREIGN KEY (idtipopago) REFERENCES tipospago(idtipopago),
    CONSTRAINT fk_pago_usuario FOREIGN KEY (idusuario) REFERENCES usuarios(idusuario)
);

CREATE TABLE listacondiciones (
    idlista INT AUTO_INCREMENT PRIMARY KEY,
    idcondicion INT,
    idtipocontrato INT,
    CONSTRAINT fk_listacondicion_condicion FOREIGN KEY (idcondicion) REFERENCES condiciones(idcondicion),
    CONSTRAINT fk_listacondicion_tipocontrato FOREIGN KEY (idtipocontrato) REFERENCES tipocontrato(idtipocontrato)
);

CREATE TABLE servicios (
    idservicio INT AUTO_INCREMENT PRIMARY KEY,
    servicio VARCHAR(100) NOT NULL,
    descripcion VARCHAR(200),
    precioregular DECIMAL(10,2),
    idcategoria INT,
    CONSTRAINT fk_servicio_categoria FOREIGN KEY (idcategoria) REFERENCES categorias(idcategoria)
);

CREATE TABLE servicioscontratados (
    idserviciocontratado INT AUTO_INCREMENT PRIMARY KEY,
    idcotizacion INT,
    idservicio INT,
    cantidad INT,
    precio DECIMAL(10,2),
    fechahoraservicio DATETIME,
    direccion VARCHAR(150),
    CONSTRAINT fk_servcontratado_cotizacion FOREIGN KEY (idcotizacion) REFERENCES cotizaciones(idcotizacion),
    CONSTRAINT fk_servcontratado_servicio FOREIGN KEY (idservicio) REFERENCES servicios(idservicio)
);

CREATE TABLE entregables (
    identregable INT AUTO_INCREMENT PRIMARY KEY,
    idserviciocontratado INT,
    idpersona INT,
    fechahoraentrega DATETIME,
    fecha_real_entrega DATETIME NULL,
    observaciones VARCHAR(200),
    estado ENUM('pendiente', 'completada') DEFAULT 'pendiente',
    comprobante_entrega VARCHAR(255) NULL,
    CONSTRAINT fk_entregable_servicio FOREIGN KEY (idserviciocontratado) REFERENCES servicioscontratados(idserviciocontratado),
    CONSTRAINT fk_entregable_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona)
);

CREATE TABLE equipos (
    idequipo INT AUTO_INCREMENT PRIMARY KEY,
    idserviciocontratado INT,
    idusuario INT,
    descripcion VARCHAR(200),
    estadoservicio ENUM('Pendiente','En Proceso','Completado','Programado') DEFAULT 'Pendiente',
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipo_servicio FOREIGN KEY (idserviciocontratado) REFERENCES servicioscontratados(idserviciocontratado),
    CONSTRAINT fk_equipo_usuario FOREIGN KEY (idusuario) REFERENCES usuarios(idusuario)
);

-- DATOS DE PRUEBA CORREGIDOS Y CONSISTENTES

-- 1. DATOS BÁSICOS (Catálogos)
INSERT INTO cargos (cargo) VALUES 
('Gerente de Proyectos'),
('Coordinador de Eventos'),
('Técnico en Audio'),
('Fotógrafo'),
('Operador de Equipos');

INSERT INTO categorias (categoria) VALUES 
('Audio y Sonido'),
('Fotografía y Video'),
('Iluminación'),
('Decoración'),
('Catering');

INSERT INTO condiciones (condicion) VALUES 
('Pago 50% adelanto'),
('Entrega de equipos 2 horas antes'),
('Cliente proporciona energía eléctrica'),
('Acceso vehicular requerido'),
('Cancelación con 48h anticipación');

INSERT INTO tipocontrato (tipocontrato, vigenciadias) VALUES 
('Evento Único', 1),
('Paquete Mensual', 30),
('Contrato Anual', 365),
('Servicio Corporativo', 90);

INSERT INTO tipoeventos (evento) VALUES 
('Boda'),
('Quinceañero'),
('Evento Corporativo'),
('Conferencia'),
('Concierto');

INSERT INTO tipospago (tipopago) VALUES 
('Efectivo'),
('Transferencia Bancaria'),
('Tarjeta de Crédito'),
('Cheque'),
('Yape/Plin');

-- 2. PERSONAS Y EMPRESAS
INSERT INTO personas (apellidos, nombres, tipodoc, numerodoc, telprincipal, telalternativo, direccion, referencia) VALUES 
('García López', 'Carlos Eduardo', 'DNI', '12345678', '987654321', '945123456', 'Av. Los Álamos 123, San Isidro', 'Cerca al parque central'),
('Rodríguez Silva', 'María Carmen', 'DNI', '87654321', '976543210', NULL, 'Jr. Las Flores 456, Miraflores', 'Frente a la iglesia San Antonio'),
('Mendoza Torres', 'José Antonio', 'DNI', '11223344', '965432109', '912345678', 'Calle Los Pinos 789, Surco', 'A 2 cuadras del mercado central'),
('Fernández Ruiz', 'Ana Lucía', 'DNI', '55667788', '954321098', NULL, 'Av. Industrial 321, Ate', 'Edificio azul, tercer piso'),
('Vásquez Castro', 'Luis Miguel', 'DNI', '99887766', '943210987', '987123456', 'Urbanización El Sol 654, La Molina', 'Casa esquina con jardín'),
('Morales Díaz', 'Patricia Isabel', 'DNI', '44556677', '932109876', NULL, 'Calle Real 987, Pueblo Libre', 'Portón verde, casa colonial'),
('Jiménez Vargas', 'Ricardo Andrés', 'DNI', '33445566', '921098765', '956789012', 'Jr. Comercio 147, Breña', 'Al costado del Banco de Crédito'),
('Smith Johnson', 'Robert William', 'Pasaporte', 'AB1234567', '998877665', NULL, 'Calle Extranjeros 555, San Borja', 'Condominio Las Torres, Dpto 301'),
('González Pérez', 'Carmen Rosa', 'DNI', '77889900', '987123789', '945678123', 'Av. Primavera 888, Surco', 'Cerca al centro comercial');

INSERT INTO empresas (ruc, razonsocial, direccion, telefono) VALUES 
('20123456789', 'Eventos Premium SAC', 'Av. Empresarial 1001, San Isidro', '014567890'),
('20987654321', 'Corporativo Los Andes EIRL', 'Jr. Negocios 202, Miraflores', '014445556'),
('20111222333', 'Celebraciones Especiales SRL', 'Calle Eventos 303, Surco', '013334445'),
('20555666777', 'Hoteles & Convenciones SA', 'Av. Javier Prado 2500, San Borja', '012223334');

-- 3. CLIENTES
INSERT INTO clientes (idpersona, idempresa) VALUES 
(1, NULL),  -- Carlos García (persona)
(2, NULL),  -- María Rodríguez (persona)
(NULL, 1),  -- Eventos Premium SAC
(3, NULL),  -- José Mendoza (persona)
(NULL, 2),  -- Corporativo Los Andes EIRL
(4, NULL),  -- Ana Fernández (persona)
(NULL, 3),  -- Celebraciones Especiales
(8, NULL);  -- Robert Smith (extranjero)

-- 4. USUARIOS
INSERT INTO usuarios (idpersona, idcargo, nombreusuario, claveacceso, estado) VALUES 
(5, 1, 'lvasquez', '1Vasque3', 1),
(6, 2, 'pmorales', 'pM0rales', 1),
(7, 3, 'rjimenez', '4J1menez', 1),
(9, 4, 'cgonzalez', '3Gon3ale3z', 1);

INSERT INTO usuarios (idpersona, idcargo, nombreusuario, claveacceso, tipo_usuario, email, password_hash, estado) VALUES 
(1, 1, 'admin', 'admin123', 'admin', 'admin@ishume.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- 5. SERVICIOS
INSERT INTO servicios (servicio, descripcion, precioregular, idcategoria) VALUES 
('Sonido para Bodas', 'Equipo completo de sonido para ceremonias y recepciones', 800.00, 1),
('Fotografía de Eventos', 'Cobertura fotográfica completa del evento', 1200.00, 2),
('Iluminación LED', 'Sistema de iluminación decorativa con luces LED', 600.00, 3),
('Video en Vivo', 'Transmisión en vivo del evento', 1500.00, 2),
('DJ Profesional', 'Servicio de DJ con música y animación', 400.00, 1),
('Catering Premium', 'Servicio de alimentación para eventos', 25.00, 5),
('Decoración Floral', 'Arreglos florales y decoración temática', 350.00, 4);

-- 6. COTIZACIONES
INSERT INTO cotizaciones (idcliente, idtipocontrato, idusuariocrea, fechacotizacion, fechaevento, idtipoevento) VALUES 
(1, 1, 1, '2025-08-30', '2025-09-14', 1),  -- Boda Carlos García
(2, 1, 2, '2025-08-29', '2025-09-10', 2),  -- Quinceañero María Rodríguez
(3, 4, 1, '2025-08-25', '2025-09-28', 3),  -- Evento Corporativo Empresa
(4, 1, 2, '2025-09-01', '2025-10-15', 1),  -- Boda José Mendoza
(5, 3, 1, '2025-10-05', '2025-11-20', 4),  -- Conferencia Empresa
(6, 1, 3, '2025-09-10', '2025-10-18', 1),  -- Boda Ana Fernández
(8, 1, 4, '2025-10-12', '2025-11-22', 3);  -- Evento Robert Smith

-- 7. CONTRATOS
INSERT INTO contratos (idcotizacion, idcliente, autorizapublicacion) VALUES 
(1, 1, 1),  -- Contrato boda Carlos
(2, 2, 0),  -- Contrato quinceañero María
(3, 3, 1),  -- Contrato evento corporativo
(4, 4, 1),  -- Contrato boda José
(5, 5, 0),  -- Contrato conferencia
(6, 6, 1),  -- Contrato boda Ana
(7, 8, 0);  -- Contrato evento Robert

-- 8. SERVICIOS CONTRATADOS - DATOS CONSISTENTES
INSERT INTO servicioscontratados (idcotizacion, idservicio, cantidad, precio, fechahoraservicio, direccion) VALUES 
-- Contrato 1 (Boda Carlos García) - TOTAL: 2800
(1, 1, 1, 1600.00, '2025-11-08 15:00:00', 'Hacienda Los Olivos - Km 25 Panamericana Sur'),
(1, 2, 1, 1200.00, '2025-11-08 14:00:00', 'Hacienda Los Olivos - Km 25 Panamericana Sur'),

-- Contrato 2 (Quinceañero María Rodríguez) - TOTAL: 1400
(2, 1, 1, 800.00, '2025-12-05 19:00:00', 'Salón de Eventos El Dorado - Av. Principal 890, Chorrillos'),
(2, 3, 1, 600.00, '2025-12-05 18:30:00', 'Salón de Eventos El Dorado - Av. Principal 890, Chorrillos'),

-- Contrato 3 (Evento Corporativo) - TOTAL: 2500
(3, 4, 1, 1500.00, '2025-11-20 09:00:00', 'Hotel Business Center - Jr. Ejecutivo 445, San Isidro'),
(3, 2, 1, 1000.00, '2025-11-20 08:30:00', 'Hotel Business Center - Jr. Ejecutivo 445, San Isidro'),

-- Contrato 4 (Boda José Mendoza) - TOTAL: 1200
(4, 1, 1, 800.00, '2026-01-10 16:00:00', 'Club Campestre Las Flores - Cieneguilla'),
(4, 5, 1, 400.00, '2026-01-10 20:00:00', 'Club Campestre Las Flores - Cieneguilla'),

-- Contrato 5 (Conferencia) - TOTAL: 4000
(5, 2, 2, 2000.00, '2025-11-28 08:00:00', 'Centro de Convenciones Lima - Av. Javier Prado 2500, San Borja'),

-- Contrato 6 (Boda Ana Fernández) - TOTAL: 1150
(6, 1, 1, 800.00, '2025-12-14 17:00:00', 'Casa Hacienda San José - Pachacamac'),
(6, 6, 1, 350.00, '2025-12-14 16:00:00', 'Casa Hacienda San José - Pachacamac'),

-- Contrato 7 (Evento Robert Smith) - TOTAL: 2250
(7, 2, 1, 1000.00, '2025-10-19 10:00:00', 'Salón Empresarial Pacífico - Av. La Marina 150, Miraflores'),
(7, 1, 1, 850.00, '2025-10-20 16:30:00', 'Club de Campo La Pradera - Km 10 Carretera Central'),
(7, 6, 1, 400.00, '2025-10-20 15:30:00', 'Club de Campo La Pradera - Km 10 Carretera Central');

-- 9. CONTROL DE PAGOS - DATOS CONSISTENTES
INSERT INTO controlpagos (idcontrato, saldo, amortizacion, deuda, idtipopago, numtransaccion, fechahora, idusuario) VALUES 
-- Contrato 1 (Boda Carlos - Total: 2800) - COMPLETAMENTE PAGADO
(1, 2800.00, 1400.00, 1400.00, 2, 'TXN20240120001', '2025-01-20 10:30:00', 1),
(1, 1400.00, 1400.00, 0.00, 2, 'TXN20240210001', '2025-02-10 14:15:00', 1),

-- Contrato 2 (Quinceañero María - Total: 1400) - COMPLETAMENTE PAGADO
(2, 1400.00, 700.00, 700.00, 1, 'EFE20240125001', '2025-01-25 16:45:00', 2),
(2, 700.00, 700.00, 0.00, 2, 'TXN20240215001', '2025-02-15 11:20:00', 2),

-- Contrato 3 (Evento Corporativo - Total: 2500) - COMPLETAMENTE PAGADO
(3, 2500.00, 1250.00, 1250.00, 2, 'TXN20240201001', '2025-02-01 09:20:00', 1),
(3, 1250.00, 1250.00, 0.00, 2, 'TXN20240225001', '2025-02-25 11:10:00', 1),

-- Contrato 4 (Boda José - Total: 1200) - PAGADO PARCIALMENTE
(4, 1200.00, 600.00, 600.00, 5, 'YAPE20240205001', '2025-02-05 13:25:00', 2),

-- Contrato 5 (Conferencia - Total: 4000) - PAGADO PARCIALMENTE (DEBE 3000)
(5, 4000.00, 1000.00, 3000.00, 3, 'TC20240208001', '2025-02-08 15:40:00', 1),

-- Contrato 6 (Boda Ana - Total: 1150) - PAGADO PARCIALMENTE
(6, 1150.00, 575.00, 575.00, 2, 'TXN20240212001', '2025-02-12 11:20:00', 3),

-- Contrato 7 (Evento Robert - Total: 2250) - PAGADO PARCIALMENTE
(7, 2250.00, 1125.00, 1125.00, 3, 'TC20240214001', '2025-02-14 16:30:00', 4);

-- 10. EQUIPOS
INSERT INTO equipos (idserviciocontratado, idusuario, descripcion, estadoservicio) VALUES 
-- Servicios de cotización 1 (Boda Carlos García)
(1, 3, 'Equipo de sonido: mezcladora Allen & Heath, micrófonos inalámbricos, parlantes JBL', 'Completado'),
(2, 4, 'Cobertura fotográfica: Canon EOS R5, lentes 24-70mm, flash Godox', 'Completado'),

-- Servicios de cotización 2 (Quinceañero María)
(3, 3, 'Sistema de audio: consola digital, micrófonos de corbata, parlantes activos', 'En Proceso'),
(4, 1, 'Luces LED decorativas: panel RGB, controlador DMX, efectos laser', 'Pendiente'),

-- Servicios de cotización 3 (Evento Corporativo)
(5, 2, 'Transmisión en vivo: cámaras 4K, encoder, plataforma streaming', 'Completado'),
(6, 4, 'Fotografía corporativa: retratos ejecutivos, cobertura de presentaciones', 'Completado'),

-- Servicios de cotización 4 (Boda José)
(7, 3, 'Audio para ceremonia: sistema inalámbrico, altavoces discretos', 'Pendiente'),
(8, 1, 'DJ profesional: controlador Pioneer, biblioteca musical, micrófonos', 'Pendiente'),

-- Servicios de cotización 5 (Conferencia)
(9, 4, 'Fotografía corporativa para conferencia', 'Programado'),

-- Servicios de cotización 6 (Boda Ana)
(10, 3, 'Sistema de sonido para ceremonia exterior', 'Pendiente'),
(11, 2, 'Servicio de catering premium', 'Pendiente'),

-- Servicios de cotización 7 (Evento Robert Smith)
(12, 4, 'Fotografía de eventos internacionales', 'Programado'),
(13, 3, 'Sistema de sonido para evento empresarial', 'Programado'),
(14, 1, 'Servicio de catering para ejecutivos', 'Programado');

-- 11. LISTA DE CONDICIONES
INSERT INTO listacondiciones (idcondicion, idtipocontrato) VALUES 
(1, 1), (2, 1), (3, 1),
(1, 4), (4, 4),
(1, 3), (5, 3),
(2, 2), (3, 2);

-- Pruebas
-- Ver los miembros del equipo y su cargo
SELECT p.nombres, p.apellidos, p.numerodoc, c.cargo
FROM usuarios u
INNER JOIN personas p ON u.idpersona = p.idpersona
INNER JOIN cargos c ON u.idcargo = c.idcargo
WHERE u.estado = 1;

-- Ver a quien esta asignado 
SELECT p.nombres, p.apellidos, c.cargo, s.servicio, eq.descripcion, eq.estadoservicio
FROM equipos eq
INNER JOIN usuarios us ON eq.idusuario = us.idusuario
INNER JOIN personas p ON us.idpersona = p.idpersona
INNER JOIN cargos c ON us.idcargo = c.idcargo
INNER JOIN servicioscontratados sc ON eq.idserviciocontratado = sc.idserviciocontratado
INNER JOIN servicios s ON sc.idservicio = s.idservicio
WHERE sc.idcotizacion = 2;


--
SELECT p.nombres, p.apellidos, s.servicio, sc.fechahoraservicio, co.fechaevento
FROM equipos eq
INNER JOIN servicioscontratados sc ON eq.idserviciocontratado = sc.idserviciocontratado
INNER JOIN servicios s ON sc.idservicio = s.idservicio
INNER JOIN cotizaciones co ON sc.idcotizacion = co.idcotizacion
INNER JOIN usuarios us ON eq.idusuario = us.idusuario
INNER JOIN personas p ON us.idpersona = p.idpersona
WHERE us.idusuario = 4
AND sc.fechahoraservicio BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY);


SELECT 
    u.nombreusuario,
    u.email,
    u.tipo_usuario,
    CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo,
    c.cargo
FROM usuarios u
JOIN personas p ON u.idpersona = p.idpersona
JOIN cargos c ON u.idcargo = c.idcargo
WHERE u.estado = 1;

SELECT * FROM usuarios;