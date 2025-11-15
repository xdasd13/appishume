-- CREAR BASE DE DATOS
CREATE DATABASE IF NOT EXISTS ishumeProyectos;
USE ishumeProyectos;

-- TABLAS MAESTRAS
CREATE TABLE cargos (
    idcargo INT AUTO_INCREMENT PRIMARY KEY,
    cargo VARCHAR(100) NOT NULL
);

CREATE TABLE categorias (
    idcategoria INT AUTO_INCREMENT PRIMARY KEY,
    categoria VARCHAR(100) NOT NULL
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

-- TABLAS PRINCIPALES
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

CREATE TABLE servicios (
    idservicio INT AUTO_INCREMENT PRIMARY KEY,
    servicio VARCHAR(100) NOT NULL,
    descripcion VARCHAR(200),
    precioregular DECIMAL(10,2),
    idcategoria INT,
    CONSTRAINT fk_servicio_categoria FOREIGN KEY (idcategoria) REFERENCES categorias(idcategoria)
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
    dni_pagador VARCHAR(8) NULL,
    nombre_pagador VARCHAR(255) NULL,
    CONSTRAINT fk_pago_contrato FOREIGN KEY (idcontrato) REFERENCES contratos(idcontrato),
    CONSTRAINT fk_pago_tipopago FOREIGN KEY (idtipopago) REFERENCES tipospago(idtipopago),
    CONSTRAINT fk_pago_usuario FOREIGN KEY (idusuario) REFERENCES usuarios(idusuario)
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

CREATE TABLE listacondiciones (
    idlista INT AUTO_INCREMENT PRIMARY KEY,
    idcondicion INT,
    idtipocontrato INT,
    CONSTRAINT fk_listacondicion_condicion FOREIGN KEY (idcondicion) REFERENCES condiciones(idcondicion),
    CONSTRAINT fk_listacondicion_tipocontrato FOREIGN KEY (idtipocontrato) REFERENCES tipocontrato(idtipocontrato)
);


-- INSERCIÓN DE DATOS 

-- 1. DATOS BÁSICOS (cargos)
INSERT INTO cargos (cargo) VALUES 
('Gerente/a de Proyectos'),
('Coordinador/a de Eventos'),
('Técnico/a en Audio'),
('Fotógrafo/a'),
('Operador/a de Equipos');

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
-- Clientes (DNIs únicos)
('Quispe Mamani', 'Rosa María', 'DNI', '72458961', '987456123', '945123789', 'Av. Óscar R. Benavides 312, Chincha Alta', 'Frente al colegio José Pardo'),
('Torres Paucar', 'Carlos Alberto', 'DNI', '68542191', '965874123', NULL, 'Av. Progreso 845, Pueblo Nuevo, Chincha Alta', 'A media cuadra del Mercado Municipal de Pueblo Nuevo'),
('Tasayco Huamán', 'José Luis', 'DNI', '71236581', '954789632', '912456987', 'Jr. Ayacucho 215, Chincha Alta', 'Cerca a la Plaza de Armas de Chincha Alta'),
('Munaico Flores', 'Ana Patricia', 'DNI', '69874521', '978563214', NULL, 'Urb. Los Portales Mz B Lt 12, Pueblo Nuevo, Chincha Alta', 'Portón negro, tercer piso'),
('Chávez Quispe', 'María Elena', 'DNI', '74125891', '932147856', '987654123', 'Av. Mariscal Castilla 560, Chincha Alta', 'Edificio crema con balcones, dpto 301'),
('Ramírez Soto', 'Juan Carlos', 'DNI', '70589631', '921456789', NULL, 'Calle Lima 845, Chincha Alta', 'Esquina con Jr. Pisco'),
('Vásquez García', 'Lucía Mercedes', 'DNI', '73698741', '965123478', '945789632', 'Calle San Martín 605, Pueblo Nuevo, Chincha Alta', 'Segundo piso, timbre A'),
('Flores Mendoza', 'Roberto Antonio', 'DNI', '67452181', '978456321', NULL, 'Av. Luis Massaro 450, Chincha Alta', 'Condominio Los Huarangos, torre 2'),
('Gutiérrez Silva', 'Carmen Isabel', 'DNI', '75896321', '954123789', '912789456', 'Jr. Independencia 122, Chincha Alta', 'Casa color terracota con rejas verdes'),
('Castillo Díaz', 'Miguel Ángel', 'DNI', '68741251', '987321654', NULL, 'Av. Santo Domingo 389, Chincha Alta', 'Torre Empresarial Chincha, piso 4'),
('Paredes Rojas', 'Sofía Alejandra', 'DNI', '72589632', '921789456', '965123789', 'Jr. Libertad 234, Grocio Prado, Chincha', 'Edificio moderno, dpto 501'),
('Ccama Quispe', 'Pedro Pablo', 'DNI', '70123451', '978456123', NULL, 'Av. Benigno Moquillaza 180, Chincha Baja, Chincha', 'Frente al parque Lurinchincha'),
('Huamán Torres', 'Valeria Nicole', 'DNI', '73456781', '954321789', '912456123', 'Calle Real 567, El Carmen, Chincha', 'Casa esquina con jardín'),
('Poma Condori', 'Diego Alejandro', 'DNI', '68965431', '965789321', NULL, 'Av. José Olaya 456, Tambo de Mora, Chincha', 'Frente al muelle artesanal'),
('Cruz Ramírez', 'Camila Fernanda', 'DNI', '74789121', '932789456', '987456321', 'Jr. Ucayali 345, Chincha Alta', 'Local comercial anexo a ferretería'),
('Apaza Mamani', 'Jorge Luis', 'DNI', '71963251', '921456321', NULL, 'Av. San Martín 620, Grocio Prado, Chincha', 'Diagonal a la iglesia San Pedro'),
('Yupanqui Rojas', 'Andrea Beatriz', 'DNI', '69852141', '954789632', '976543210', 'Calle Los Jazmines 210, Pueblo Nuevo, Chincha Alta', 'Casa rosada con rejas negras'),
('Condori Quispe', 'Fernando José', 'DNI', '72147891', '987654789', NULL, 'Av. 28 de Julio 180, Chincha Alta', 'Edificio comercial Las Palmeras, piso 3'),
('Mamani Ccama', 'Gabriela Rosa', 'DNI', '70369851', '965478912', '932147896', 'Jr. Ayacucho 567, Chincha Alta', 'Al lado de la botica Santa Rosa'),
('Paucar Torres', 'Ricardo Martín', 'DNI', '73852141', '921789654', NULL, 'Av. Grau 2890, Grocio Prado, Chincha', 'Casa antigua con balcón de madera'),

-- Personal de la empresa (DNIs únicos)
('Admin', 'Sistema', 'DNI', '00000001', '999999999', NULL, 'Oficina Central ISHUME, Av. Óscar R. Benavides 150, Chincha Alta', 'Administrador del sistema'),
('Salazar Torres', 'Gabriela Andrea', 'DNI', '71452361', '932456789', '987123456', 'Calle Los Rosales 456, Chincha Alta', 'Urbanización Santa Patricia, portón verde'),
('Rojas Pérez', 'Raúl Fernando', 'DNI', '69874561', '945789123', NULL, 'Av. El Sol 890, Pueblo Nuevo, Chincha Alta', 'Conjunto Las Lomas, casa 12'),
('Mendoza Quispe', 'Daniela Sofía', 'DNI', '72589633', '921789456', '965123789', 'Jr. Libertad 310, Chincha Alta', 'Edificio moderno, dpto 502'),
('Vargas De La Cruz', 'Luis Enrique', 'DNI', '70123452', '978456123', NULL, 'Av. América Sur 540, Chincha Alta', 'Centro comercial anexo'),
('Huamán Flores', 'Valeria Nicole', 'DNI', '73456782', '954321789', '912456123', 'Calle Real 230, El Carmen, Chincha', 'Casa esquina con jardín interior'),
('Poma Tasayco', 'Diego Alejandro', 'DNI', '68965432', '965789321', NULL, 'Av. Progreso 910, Pueblo Nuevo, Chincha Alta', 'Conjunto Los Pinos, torre A'),
('Cruz Munaico', 'Camila Fernanda', 'DNI', '74789122', '932789456', '987456321', 'Jr. Ucayali 120, Chincha Alta', 'Local comercial anexo'),
('Soto Gutiérrez', 'Andrés Sebastián', 'DNI', '71963252', '921456321', NULL, 'Av. Los Libertadores 450, Chincha Baja, Chincha', 'Frente al centro de salud Chincha Baja');

INSERT INTO empresas (ruc, razonsocial, direccion, telefono) VALUES 
('20567894123', 'Eventos Corporativos del Perú SAC', 'Av. Óscar R. Benavides 410, Chincha Alta', '056234567'),
('20741258963', 'Celebraciones Especiales EIRL', 'Jr. Túpac Amaru 189, Pueblo Nuevo, Chincha', '056345678'),
('20896325147', 'Grupo Hotelero Los Andes SA', 'Av. Italia 120, Chincha Alta', '056456789'),
('20369741258', 'Soluciones Empresariales Premium SRL', 'Calle Comercio 567, Chincha Alta', '056567890'),
('20147852369', 'Banquetes y Eventos Gourmet SAC', 'Av. Ramón Marín 305, Chincha Alta', '056678901'),
('20852963741', 'Corporación de Eventos Nacionales SA', 'Jr. Arica 450, El Carmen, Chincha', '056789012'),
('20456789123', 'Producciones Audiovisuales Lima EIRL', 'Av. Luis Massaro 256, Grocio Prado, Chincha', '056890123'),
('20789456123', 'Catering & Banquetes del Sur SAC', 'Malecón Grau 180, Tambo de Mora, Chincha', '056901234');

-- 3. CLIENTES
INSERT INTO clientes (idpersona, idempresa) VALUES 
(1, NULL), (2, NULL), (NULL, 1), (3, NULL), (4, NULL),
(NULL, 2), (5, NULL), (6, NULL), (NULL, 3), (7, NULL),
(NULL, 4), (8, NULL), (9, NULL), (NULL, 5), (10, NULL),
(11, NULL), (12, NULL), (13, NULL), (NULL, 6), (14, NULL),
(15, NULL), (16, NULL), (NULL, 7), (17, NULL), (18, NULL),
(19, NULL), (NULL, 8);

-- 4. USUARIOS
INSERT INTO usuarios (idpersona, idcargo, nombreusuario, claveacceso, tipo_usuario, email, password_hash, estado) VALUES 
(20, 1, 'admin', 'admin123', 'admin', 'admin@ishume.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

INSERT INTO usuarios (idpersona, idcargo, nombreusuario, claveacceso, tipo_usuario, email, estado) VALUES 
(21, 2, 'gsalazar', 'Gaby2025', 'trabajador', 'gsalazar@ishume.com', 1),
(22, 3, 'rrojas', 'Raul2025', 'trabajador', 'rrojas@ishume.com', 1),
(23, 4, 'dmendoza', 'Dani2025', 'trabajador', 'dmendoza@ishume.com', 1),
(24, 5, 'lvargas', 'Luis2025', 'trabajador', 'lvargas@ishume.com', 1),
(25, 2, 'vhuaman', 'Vale2025', 'trabajador', 'vhuaman@ishume.com', 1),
(26, 3, 'dpoma', 'Diego2025', 'trabajador', 'dpoma@ishume.com', 1),
(27, 4, 'ccruz', 'Cami2025', 'trabajador', 'ccruz@ishume.com', 1),
(28, 5, 'asoto', 'Andres2025', 'trabajador', 'asoto@ishume.com', 1);

-- 5. SERVICIOS
INSERT INTO servicios (servicio, descripcion, precioregular, idcategoria) VALUES 
-- QUINCEAÑERAS (Categoría 2: Fotografía y Video)
('Sesión Pre-Quinceañera Creativa', 'Sesión fotográfica creativa previa al evento', 850.00, 2),
('Cobertura Completa Quinceañera', 'Cobertura fotográfica y video del evento completo', 2200.00, 2),
('Video Resumen Emotivo Quinceañera', 'Video editado con los mejores momentos del evento', 950.00, 2),
('Álbum Personalizado Quinceañera', 'Álbum de lujo con las mejores fotografías', 680.00, 2),

-- FOTOGRAFÍA PROFESIONAL (Categoría 2: Fotografía y Video)
('Retratos Individuales y Grupales', 'Sesión de retratos profesionales en estudio o exteriores', 450.00, 2),
('Fotografía de Producto y Publicitaria', 'Fotografía comercial para productos y publicidad', 750.00, 2),
('Sesiones en Estudio y Exteriores', 'Sesión fotográfica personalizada en locación', 580.00, 2),
('Retoque Digital Avanzado', 'Edición profesional y retoque de fotografías', 280.00, 2),

-- BABY SHOWERS (Categoría 2: Fotografía y Video)
('Fotografía Documental Baby Shower', 'Cobertura completa del evento tipo documental', 720.00, 2),
('Sesión de Maternidad', 'Sesión fotográfica de maternidad (opcional)', 550.00, 2),
('Video Resumen Baby Shower', 'Video editado con música del evento', 680.00, 2),
('Diseño Recuerdos Digitales', 'Diseño de invitaciones y recuerdos digitales', 320.00, 4),

-- BAUTIZOS (Categoría 2: Fotografía y Video)
('Cobertura Ceremonia Bautizo', 'Fotografía de la ceremonia religiosa completa', 650.00, 2),
('Fotografías Familiares Bautizo', 'Retratos familiares y grupales del evento', 480.00, 2),
('Video Documental Bautizo', 'Video documental de la ceremonia y celebración', 850.00, 2),
('Álbum Fotográfico Elegante Bautizo', 'Álbum de lujo con fotografías del bautizo', 580.00, 2),

-- BODAS (Categoría 2: Fotografía y Video)
('Sesiones Pre-Boda y Post-Boda', 'Sesiones fotográficas antes y después de la boda', 1200.00, 2),
('Cobertura Completa Día D', 'Cobertura fotográfica y video del día de la boda', 3500.00, 2),
('Video Highlights Boda', 'Video resumen de los mejores momentos', 1500.00, 2),
('Película de Boda', 'Video cinematográfico completo de la boda', 2800.00, 2),
('Entrega Digital y Física Lujo', 'Entrega de fotografías digitales y álbum físico premium', 950.00, 2),

-- CELEBRACIONES MEMORABLES (Categoría 2: Fotografía y Video)
('Cobertura Dinámica Evento', 'Fotografía dinámica de eventos sociales', 880.00, 2),
('Fotografía Invitados y Momentos', 'Captura de invitados y momentos clave del evento', 720.00, 2),
('Video Resumen Festivo', 'Video editado con los mejores momentos festivos', 950.00, 2),
('Galería Online Privada', 'Galería digital privada para compartir fotografías', 280.00, 2),

-- MAGIA INFANTIL - CUMPLEAÑOS (Categoría 2: Fotografía y Video / Categoría 4: Decoración)
('Sesiones Temáticas Infantiles', 'Sesión fotográfica temática para niños', 520.00, 2),
('Fotografía Cumpleaños Infantil', 'Cobertura completa de cumpleaños infantil', 680.00, 2),
('Retratos Familiares con Niños', 'Sesión de retratos familiares con niños', 450.00, 2),
('Edición Especializada Niños', 'Edición y retoque especializado para fotografías infantiles', 320.00, 2),

-- SERVICIOS COMPLEMENTARIOS
-- Audio y Sonido (Categoría 1)
('Sonido Básico para Eventos', 'Sistema de audio compacto para eventos pequeños', 450.00, 1),
('Sonido Premium para Bodas', 'Sistema de audio profesional completo', 1200.00, 1),
('DJ Profesional', 'DJ con equipo completo y música variada', 650.00, 1),

-- Iluminación (Categoría 3)
('Iluminación LED Básica', 'Luces LED de colores para ambientación', 400.00, 3),
('Iluminación Profesional', 'Sistema completo con moving heads y efectos', 950.00, 3),

-- Decoración (Categoría 4)
('Decoración Temática Evento', 'Ambientación completa según tema del evento', 1200.00, 4),
('Backdrop Personalizado', 'Fondo decorativo personalizado con nombre', 380.00, 4),
('Decoración Infantil Temática', 'Decoración especializada para eventos infantiles', 850.00, 4),

-- Catering (Categoría 5)
('Catering Cóctel', 'Servicio de piqueos y bebidas por persona', 35.00, 5),
('Catering Almuerzo/Cena', 'Menú completo por persona', 65.00, 5),
('Torta Personalizada', 'Torta temática según número de personas', 280.00, 5);

-- 6. COTIZACIONES
INSERT INTO cotizaciones (idcliente, idtipocontrato, idusuariocrea, fechacotizacion, fechaevento, idtipoevento) VALUES 
-- Noviembre 2025
(1, 1, 2, '2025-10-30', '2025-11-15', 1),
(2, 1, 2, '2025-10-30', '2025-11-08', 2),
(3, 4, 1, '2025-10-31', '2025-11-20', 3),
(4, 1, 2, '2025-11-01', '2025-11-22', 1),
(6, 1, 2, '2025-11-02', '2025-11-25', 1),
(7, 1, 6, '2025-11-03', '2025-11-28', 1),
(8, 1, 6, '2025-11-04', '2025-11-30', 2),

-- Diciembre 2025
(9, 3, 1, '2025-11-05', '2025-12-05', 4),
(10, 1, 2, '2025-11-06', '2025-12-07', 2),
(11, 4, 1, '2025-11-07', '2025-12-10', 3),
(12, 1, 6, '2025-11-08', '2025-12-12', 1),
(13, 1, 2, '2025-11-10', '2025-12-14', 1),
(15, 1, 6, '2025-11-12', '2025-12-18', 1),
(16, 1, 2, '2025-11-15', '2025-12-20', 2),
(17, 1, 1, '2025-11-18', '2025-12-27', 1),

-- Enero 2026
(18, 1, 2, '2025-12-01', '2026-01-10', 1),
(19, 4, 1, '2025-12-05', '2026-01-15', 3),
(20, 1, 6, '2025-12-08', '2026-01-20', 2),
(21, 1, 2, '2025-12-10', '2026-01-25', 1);

-- 7. CONTRATOS
INSERT INTO contratos (idcotizacion, idcliente, autorizapublicacion) VALUES 
(1, 1, 1), (2, 2, 0), (3, 3, 1), (4, 4, 1), (5, 6, 0),
(6, 7, 1), (7, 8, 1), (8, 9, 0), (9, 10, 1), (10, 11, 1),
(11, 12, 0), (12, 13, 1), (13, 15, 1), (14, 16, 0), (15, 17, 1),
(16, 18, 1), (17, 19, 0), (18, 20, 1), (19, 21, 1);

-- 8. SERVICIOS CONTRATADOS
INSERT INTO servicioscontratados (idcotizacion, idservicio, cantidad, precio, fechahoraservicio, direccion) VALUES 
-- Cotización 1: Matrimonio Rosa Quispe (Nov 15)
(1, 2, 1, 1200.00, '2025-11-15 15:00:00', 'Hacienda El Olivar, Panamericana Sur Km 197, El Carmen - Chincha'),
(1, 6, 1, 1800.00, '2025-11-15 14:00:00', 'Hacienda El Olivar, Panamericana Sur Km 197, El Carmen - Chincha'),
(1, 11, 1, 950.00, '2025-11-15 14:30:00', 'Hacienda El Olivar, Panamericana Sur Km 197, El Carmen - Chincha'),
(1, 14, 1, 1200.00, '2025-11-15 13:00:00', 'Hacienda El Olivar, Panamericana Sur Km 197, El Carmen - Chincha'),
(1, 18, 1, 2800.00, '2025-11-15 18:00:00', 'Hacienda El Olivar, Panamericana Sur Km 197, El Carmen - Chincha'),
(1, 22, 1, 650.00, '2025-11-15 20:00:00', 'Hacienda El Olivar, Panamericana Sur Km 197, El Carmen - Chincha'),

-- Cotización 2: Quinceañero Carlos Torres (Nov 08)
(2, 1, 1, 450.00, '2025-11-08 18:00:00', 'Salón Santa María, Av. Óscar R. Benavides 320, Chincha Alta'),
(2, 5, 1, 850.00, '2025-11-08 17:30:00', 'Salón Santa María, Av. Óscar R. Benavides 320, Chincha Alta'),
(2, 10, 1, 400.00, '2025-11-08 17:00:00', 'Salón Santa María, Av. Óscar R. Benavides 320, Chincha Alta'),
(2, 17, 1, 1750.00, '2025-11-08 19:30:00', 'Salón Santa María, Av. Óscar R. Benavides 320, Chincha Alta'),
(2, 22, 1, 650.00, '2025-11-08 20:00:00', 'Salón Santa María, Av. Óscar R. Benavides 320, Chincha Alta'),

-- Cotización 3: Evento Corporativo (Nov 20)
(3, 3, 1, 800.00, '2025-11-20 09:00:00', 'Centro Empresarial Chincha, Av. Ramón Marín 180, Chincha Alta'),
(3, 8, 1, 1500.00, '2025-11-20 08:30:00', 'Centro Empresarial Chincha, Av. Ramón Marín 180, Chincha Alta'),
(3, 12, 1, 750.00, '2025-11-20 08:45:00', 'Centro Empresarial Chincha, Av. Ramón Marín 180, Chincha Alta'),
(3, 17, 1, 3500.00, '2025-11-20 12:00:00', 'Centro Empresarial Chincha, Av. Ramón Marín 180, Chincha Alta'),

-- Cotización 4: Matrimonio José Tasayco (Nov 22)
(4, 4, 1, 350.00, '2025-11-22 17:00:00', 'Calle Los Algarrobos 234, Pueblo Nuevo, Chincha Alta'),
(4, 5, 1, 850.00, '2025-11-22 16:30:00', 'Calle Los Algarrobos 234, Pueblo Nuevo, Chincha Alta'),
(4, 15, 1, 380.00, '2025-11-22 16:00:00', 'Calle Los Algarrobos 234, Pueblo Nuevo, Chincha Alta'),
(4, 19, 1, 280.00, '2025-11-22 19:00:00', 'Calle Los Algarrobos 234, Pueblo Nuevo, Chincha Alta'),

-- Cotización 5: Matrimonio Ana Munaico (Nov 25)
(5, 1, 1, 450.00, '2025-11-25 11:00:00', 'Iglesia Santo Domingo, Plaza de Armas, Chincha Alta'),
(5, 5, 1, 850.00, '2025-11-25 10:30:00', 'Iglesia Santo Domingo, Plaza de Armas, Chincha Alta'),
(5, 13, 1, 550.00, '2025-11-25 10:00:00', 'Iglesia Santo Domingo, Plaza de Armas, Chincha Alta'),
(5, 17, 1, 1400.00, '2025-11-25 13:00:00', 'Plaza de Armas, Chincha Alta'),

-- Cotización 6: Matrimonio María Chávez (Nov 28)
(6, 2, 1, 1200.00, '2025-11-28 16:00:00', 'Hacienda San José, Fundo San José s/n, El Carmen - Chincha'),
(6, 7, 1, 2200.00, '2025-11-28 15:30:00', 'Hacienda San José, Fundo San José s/n, El Carmen - Chincha'),
(6, 11, 1, 950.00, '2025-11-28 15:00:00', 'Hacienda San José, Fundo San José s/n, El Carmen - Chincha'),
(6, 18, 1, 2100.00, '2025-11-28 19:00:00', 'Hacienda San José, Fundo San José s/n, El Carmen - Chincha'),
(6, 22, 1, 650.00, '2025-11-28 21:00:00', 'Hacienda San José, Fundo San José s/n, El Carmen - Chincha'),

-- Cotización 7: Quinceañero Juan Ramírez (Nov 30)
(7, 1, 1, 450.00, '2025-11-30 18:00:00', 'Salón Los Jardines de Chincha, Av. Luis Massaro 560, Chincha Alta'),
(7, 5, 1, 850.00, '2025-11-30 17:30:00', 'Salón Los Jardines de Chincha, Av. Luis Massaro 560, Chincha Alta'),
(7, 10, 1, 400.00, '2025-11-30 17:00:00', 'Salón Los Jardines de Chincha, Av. Luis Massaro 560, Chincha Alta'),
(7, 17, 1, 1225.00, '2025-11-30 20:00:00', 'Salón Los Jardines de Chincha, Av. Luis Massaro 560, Chincha Alta'),

-- Cotización 8: Conferencia Empresa (Dic 05)
(8, 3, 1, 1600.00, '2025-12-05 08:00:00', 'Centro de Convenciones Chincha, Av. Progreso 980, Pueblo Nuevo, Chincha Alta'),
(8, 8, 1, 1500.00, '2025-12-05 07:30:00', 'Centro de Convenciones Chincha, Av. Progreso 980, Pueblo Nuevo, Chincha Alta'),
(8, 12, 1, 750.00, '2025-12-05 07:45:00', 'Centro de Convenciones Chincha, Av. Progreso 980, Pueblo Nuevo, Chincha Alta'),
(8, 17, 1, 5250.00, '2025-12-05 12:00:00', 'Centro de Convenciones Chincha, Av. Progreso 980, Pueblo Nuevo, Chincha Alta'),

-- Cotización 9: Quinceañero Lucía Vásquez (Dic 07)
(9, 2, 1, 1200.00, '2025-12-07 18:00:00', 'Club Social Chincha, Malecón Grau 210, Tambo de Mora, Chincha'),
(9, 6, 1, 1800.00, '2025-12-07 17:30:00', 'Club Social Chincha, Malecón Grau 210, Tambo de Mora, Chincha'),
(9, 11, 1, 950.00, '2025-12-07 17:00:00', 'Club Social Chincha, Malecón Grau 210, Tambo de Mora, Chincha'),
(9, 18, 1, 2450.00, '2025-12-07 20:00:00', 'Club Social Chincha, Malecón Grau 210, Tambo de Mora, Chincha'),
(9, 22, 1, 650.00, '2025-12-07 21:30:00', 'Club Social Chincha, Malecón Grau 210, Tambo de Mora, Chincha'),

-- Cotización 10: Evento Corporativo (Dic 10)
(10, 3, 1, 800.00, '2025-12-10 19:00:00', 'Hotel La Estación, Jr. Lima 540, Chincha Alta'),
(10, 7, 1, 2200.00, '2025-12-10 18:30:00', 'Hotel La Estación, Jr. Lima 540, Chincha Alta'),
(10, 11, 1, 950.00, '2025-12-10 18:00:00', 'Hotel La Estación, Jr. Lima 540, Chincha Alta'),
(10, 15, 1, 380.00, '2025-12-10 17:30:00', 'Hotel La Estación, Jr. Lima 540, Chincha Alta'),
(10, 17, 2, 2800.00, '2025-12-10 21:00:00', 'Hotel La Estación, Jr. Lima 540, Chincha Alta'),

-- Cotización 11: Matrimonio Roberto Flores (Dic 12)
(11, 1, 1, 450.00, '2025-12-12 11:00:00', 'Parroquia San José, Jr. Santo Domingo 210, Chincha Alta'),
(11, 5, 1, 850.00, '2025-12-12 10:30:00', 'Parroquia San José, Jr. Santo Domingo 210, Chincha Alta'),
(11, 13, 1, 550.00, '2025-12-12 10:00:00', 'Parroquia San José, Jr. Santo Domingo 210, Chincha Alta'),
(11, 17, 1, 1575.00, '2025-12-12 13:30:00', 'Jr. Santo Domingo 210, Chincha Alta'),
(11, 23, 1, 400.00, '2025-12-12 14:00:00', 'Jr. Santo Domingo 210, Chincha Alta'),

-- Cotización 12: Matrimonio Carmen Gutiérrez (Dic 14)
(12, 2, 1, 1200.00, '2025-12-14 17:00:00', 'Country Club Chincha, Av. Luis Massaro 600, Chincha Alta'),
(12, 6, 1, 1800.00, '2025-12-14 16:30:00', 'Country Club Chincha, Av. Luis Massaro 600, Chincha Alta'),
(12, 9, 1, 600.00, '2025-12-14 16:00:00', 'Country Club Chincha, Av. Luis Massaro 600, Chincha Alta'),
(12, 11, 1, 950.00, '2025-12-14 15:30:00', 'Country Club Chincha, Av. Luis Massaro 600, Chincha Alta'),
(12, 14, 1, 1200.00, '2025-12-14 15:00:00', 'Country Club Chincha, Av. Luis Massaro 600, Chincha Alta'),
(12, 18, 1, 3500.00, '2025-12-14 19:30:00', 'Country Club Chincha, Av. Luis Massaro 600, Chincha Alta'),
(12, 22, 1, 650.00, '2025-12-14 22:00:00', 'Country Club Chincha, Av. Luis Massaro 600, Chincha Alta'),

-- Cotización 13: Matrimonio Miguel Castillo (Dic 18)
(13, 1, 1, 450.00, '2025-12-18 19:00:00', 'Restaurante Campestre Los Sauces, Fundo San Regis, El Carmen - Chincha'),
(13, 5, 1, 850.00, '2025-12-18 18:30:00', 'Restaurante Campestre Los Sauces, Fundo San Regis, El Carmen - Chincha'),
(13, 10, 1, 400.00, '2025-12-18 18:00:00', 'Restaurante Campestre Los Sauces, Fundo San Regis, El Carmen - Chincha'),
(13, 18, 1, 1750.00, '2025-12-18 21:00:00', 'Restaurante Campestre Los Sauces, Fundo San Regis, El Carmen - Chincha'),
(13, 22, 1, 650.00, '2025-12-18 22:00:00', 'Restaurante Campestre Los Sauces, Fundo San Regis, El Carmen - Chincha'),

-- Cotización 14: Quinceañero Sofía Paredes (Dic 20)
(14, 3, 1, 800.00, '2025-12-20 18:00:00', 'Salón de Eventos Miraflores, Jr. Libertad 234, Grocio Prado, Chincha'),
(14, 5, 1, 850.00, '2025-12-20 17:30:00', 'Salón de Eventos Miraflores, Jr. Libertad 234, Grocio Prado, Chincha'),
(14, 17, 1, 2100.00, '2025-12-20 19:30:00', 'Salón de Eventos Miraflores, Jr. Libertad 234, Grocio Prado, Chincha'),
(14, 19, 1, 280.00, '2025-12-20 20:30:00', 'Salón de Eventos Miraflores, Jr. Libertad 234, Grocio Prado, Chincha'),

-- Cotización 15: Matrimonio Pedro Ccama (Dic 27)
(15, 2, 1, 1200.00, '2025-12-27 18:00:00', 'Hacienda Villa Verde, Fundo Villa Verde s/n, Chincha Baja'),
(15, 7, 1, 2200.00, '2025-12-27 17:30:00', 'Hacienda Villa Verde, Fundo Villa Verde s/n, Chincha Baja'),
(15, 11, 1, 950.00, '2025-12-27 17:00:00', 'Hacienda Villa Verde, Fundo Villa Verde s/n, Chincha Baja'),
(15, 14, 1, 1200.00, '2025-12-27 16:30:00', 'Hacienda Villa Verde, Fundo Villa Verde s/n, Chincha Baja'),
(15, 18, 1, 3150.00, '2025-12-27 20:00:00', 'Hacienda Villa Verde, Fundo Villa Verde s/n, Chincha Baja'),
(15, 24, 1, 1200.00, '2025-12-27 22:00:00', 'Hacienda Villa Verde, Fundo Villa Verde s/n, Chincha Baja'),

-- Cotización 16: Matrimonio Valeria Huamán (Ene 10, 2026)
(16, 2, 1, 1200.00, '2026-01-10 16:00:00', 'Club Campestre Las Flores, Calle Los Álamos s/n, Pueblo Nuevo, Chincha Alta'),
(16, 6, 1, 1800.00, '2026-01-10 15:30:00', 'Club Campestre Las Flores, Calle Los Álamos s/n, Pueblo Nuevo, Chincha Alta'),
(16, 11, 1, 950.00, '2026-01-10 15:00:00', 'Club Campestre Las Flores, Calle Los Álamos s/n, Pueblo Nuevo, Chincha Alta'),
(16, 18, 1, 2450.00, '2026-01-10 19:00:00', 'Club Campestre Las Flores, Calle Los Álamos s/n, Pueblo Nuevo, Chincha Alta'),
(16, 22, 1, 650.00, '2026-01-10 21:00:00', 'Club Campestre Las Flores, Calle Los Álamos s/n, Pueblo Nuevo, Chincha Alta'),

-- Cotización 17: Evento Corporativo (Ene 15, 2026)
(17, 3, 1, 800.00, '2026-01-15 09:00:00', 'Centro Empresarial Chincha Sur, Calle Comercio 120, Chincha Alta'),
(17, 8, 1, 1500.00, '2026-01-15 08:30:00', 'Centro Empresarial Chincha Sur, Calle Comercio 120, Chincha Alta'),
(17, 12, 1, 750.00, '2026-01-15 08:45:00', 'Centro Empresarial Chincha Sur, Calle Comercio 120, Chincha Alta'),
(17, 17, 1, 4200.00, '2026-01-15 12:00:00', 'Centro Empresarial Chincha Sur, Calle Comercio 120, Chincha Alta'),

-- Cotización 18: Quinceañero Diego Poma (Ene 20, 2026)
(18, 1, 1, 450.00, '2026-01-20 18:00:00', 'Salón Cañaveral, Av. Tambo de Mora 210, Tambo de Mora, Chincha'),
(18, 5, 1, 850.00, '2026-01-20 17:30:00', 'Salón Cañaveral, Av. Tambo de Mora 210, Tambo de Mora, Chincha'),
(18, 10, 1, 400.00, '2026-01-20 17:00:00', 'Salón Cañaveral, Av. Tambo de Mora 210, Tambo de Mora, Chincha'),
(18, 17, 1, 1750.00, '2026-01-20 20:00:00', 'Salón Cañaveral, Av. Tambo de Mora 210, Tambo de Mora, Chincha'),
(18, 23, 1, 400.00, '2026-01-20 19:00:00', 'Salón Cañaveral, Av. Tambo de Mora 210, Tambo de Mora, Chincha'),

-- Cotización 19: Matrimonio Camila Cruz (Ene 25, 2026)
(19, 2, 1, 1200.00, '2026-01-25 17:00:00', 'Casa Hacienda El Paraíso, Fundo El Paraíso, El Carmen - Chincha'),
(19, 7, 1, 2200.00, '2026-01-25 16:30:00', 'Casa Hacienda El Paraíso, Fundo El Paraíso, El Carmen - Chincha'),
(19, 11, 1, 950.00, '2026-01-25 16:00:00', 'Casa Hacienda El Paraíso, Fundo El Paraíso, El Carmen - Chincha'),
(19, 14, 1, 1200.00, '2026-01-25 15:30:00', 'Casa Hacienda El Paraíso, Fundo El Paraíso, El Carmen - Chincha'),
(19, 18, 2, 2975.00, '2026-01-25 19:30:00', 'Casa Hacienda El Paraíso, Fundo El Paraíso, El Carmen - Chincha'),
(19, 22, 1, 650.00, '2026-01-25 22:00:00', 'Casa Hacienda El Paraíso, Fundo El Paraíso, El Carmen - Chincha');

-- 9. CONTROL DE PAGOS
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


-- CONSULTAS DE VERIFICACIÓN

-- Verificar que no hay DNIs duplicados
SELECT 'Verificación de DNIs duplicados' as verificación;
SELECT numerodoc, COUNT(*) as cantidad
FROM personas 
GROUP BY numerodoc 
HAVING COUNT(*) > 1;

-- Verificar conteo de registros
SELECT 'Conteo de registros por tabla' as verificación;
SELECT 'Personas' as tabla, COUNT(*) as total FROM personas
UNION ALL SELECT 'Clientes', COUNT(*) FROM clientes
UNION ALL SELECT 'Usuarios', COUNT(*) FROM usuarios
UNION ALL SELECT 'Cotizaciones', COUNT(*) FROM cotizaciones
UNION ALL SELECT 'Contratos', COUNT(*) FROM contratos
UNION ALL SELECT 'ServiciosContratados', COUNT(*) FROM servicioscontratados
UNION ALL SELECT 'ControlPagos', COUNT(*) FROM controlpagos
UNION ALL SELECT 'Equipos', COUNT(*) FROM equipos;

-- Consulta de ejemplo: Usuarios y sus cargos
SELECT 'Usuarios del sistema' as consulta;
SELECT u.nombreusuario, u.email, u.tipo_usuario, 
       CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo,
       c.cargo
FROM usuarios u
JOIN personas p ON u.idpersona = p.idpersona
JOIN cargos c ON u.idcargo = c.idcargo
WHERE u.estado = 1;

-- Consulta de ejemplo: Próximos eventos
SELECT 'Próximos eventos' as consulta;
SELECT c.idcotizacion, te.evento, c.fechaevento,
       CONCAT(per.nombres, ' ', per.apellidos) as cliente_persona,
       emp.razonsocial as cliente_empresa
FROM cotizaciones c
LEFT JOIN clientes cl ON c.idcliente = cl.idcliente
LEFT JOIN personas per ON cl.idpersona = per.idpersona
LEFT JOIN empresas emp ON cl.idempresa = emp.idempresa
JOIN tipoeventos te ON c.idtipoevento = te.idtipoevento
WHERE c.fechaevento >= CURDATE()
ORDER BY c.fechaevento ASC
LIMIT 10;