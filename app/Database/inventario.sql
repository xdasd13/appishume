USE ishume;

-- TABLA DE CATEGORÍAS DE EQUIPOS
CREATE TABLE cateEquipo (
    idCateEquipo INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nomCate VARCHAR(70) NOT NULL UNIQUE,
    descripcion TEXT NULL
) ENGINE=INNODB;


-- TABLA DE MARCAS
CREATE TABLE marcaEquipo (
    idMarca INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nomMarca VARCHAR(70) NOT NULL UNIQUE
) ENGINE=INNODB;

-- TABLA DE EQUIPOS (CORREGIDA)
CREATE TABLE equipo (
    idEquipo INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idCateEquipo INT UNSIGNED NOT NULL,
    idMarca INT UNSIGNED NOT NULL,
    modelo VARCHAR(70) NOT NULL,
    descripcion VARCHAR(255) NULL,
    caracteristica TEXT NULL,
    sku VARCHAR(50) UNIQUE NULL,
    numSerie VARCHAR(100) UNIQUE NULL,
    cantDisponible INT UNSIGNED NOT NULL DEFAULT 1,
    estado ENUM('Nuevo','EnUso','EnMantenimiento','Dañado','Otro') NOT NULL DEFAULT 'Nuevo',
    fechaCompra DATE NULL,
    fechaUso DATE NULL,
    imgEquipo VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipo_categoria FOREIGN KEY (idCateEquipo) REFERENCES cateEquipo(idCateEquipo) ON DELETE RESTRICT,
    CONSTRAINT fk_equipo_marca FOREIGN KEY (idMarca) REFERENCES marcaEquipo(idMarca) ON DELETE RESTRICT
) ENGINE=INNODB;

-- TABLA DE MOVIMIENTOS DE INVENTARIO
CREATE TABLE movimientoEquipo (
    idMovimiento INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idEquipo INT UNSIGNED NOT NULL,
    tipoMovimiento ENUM('Entrada','Salida','Mantenimiento','Baja') NOT NULL,
    cantidad INT UNSIGNED NOT NULL,
    fechaMovimiento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    observacion TEXT NULL,
    CONSTRAINT fk_movimiento_equipo FOREIGN KEY (idEquipo) REFERENCES equipo(idEquipo) ON DELETE CASCADE
) ENGINE=INNODB;

-- TABLA DE UBICACIONES (lugares físicos)
CREATE TABLE ubicacion (
    idUbicacion INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nombreUbicacion VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL
) ENGINE=INNODB;

-- TABLA RELACIÓN EQUIPO - UBICACIÓN
CREATE TABLE equipoUbicacion (
    idEquipoUbicacion INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    idEquipo INT UNSIGNED NOT NULL,
    idUbicacion INT UNSIGNED NOT NULL,
    fechaAsignacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipoUbicacion_equipo FOREIGN KEY (idEquipo) REFERENCES equipo(idEquipo) ON DELETE CASCADE,
    CONSTRAINT fk_equipoUbicacion_ubicacion FOREIGN KEY (idUbicacion) REFERENCES ubicacion(idUbicacion) ON DELETE CASCADE
) ENGINE=INNODB;

-- INSERTS CORREGIDOS
INSERT IGNORE INTO cateEquipo (nomCate, descripcion) VALUES
('Cámaras', 'Cámaras fotográficas y de video'),
('Lentes', 'Lentes intercambiables'),
('Iluminación', 'Luces y accesorios de iluminación'),
('Audio', 'Micrófonos y accesorios'),
('Estabilización y Soporte', 'Gimbals, trípodes y soportes'),
('Almacenamiento', 'Memorias y dispositivos de almacenamiento'),
('Computadoras y Edición', 'PCs y estaciones de edición'),
('Producción y Oficina', 'Equipos para producción y oficina'),
('Escenografía y Fondos', 'Fondos y soportes para escenografía');

INSERT IGNORE INTO marcaEquipo (nomMarca) VALUES
('Sony'),
('Sigma'),
('DJI'),
('Zhiyun'),
('Epson'),
('Genérico');

-- EQUIPOS

-- Cámaras
INSERT INTO equipo (idCateEquipo, idMarca, modelo, cantDisponible, estado) VALUES
(1, 1, 'Sony A6400 con lente kit', 2, 'EnUso'),
(1, 1, 'Sony A6700 con lente kit', 1, 'EnUso'),
(1, 1, 'Sony FX30 con lente kit', 1, 'EnUso'),
(1, 3, 'Dron DJI Mini 4 Pro', 2, 'EnUso');

-- Lentes
INSERT INTO equipo (idCateEquipo, idMarca, modelo, cantDisponible, estado) VALUES
(2, 2, 'Sigma 18–50 mm', 1, 'EnUso'),
(2, 2, 'Sigma 24–70 mm', 1, 'EnUso'),
(2, 1, 'Sony 18–135 mm kit', 1, 'EnUso'),
(2, 1, 'Sony 30 mm', 1, 'EnUso');

-- Iluminación
INSERT INTO equipo (idCateEquipo, idMarca, modelo, cantDisponible, estado) VALUES
(3, 6, 'Luz LED', 4, 'EnUso'),
(3, 6, 'Softbox', 1, 'EnUso'),
(3, 6, 'Difusor', 1, 'EnUso'),
(3, 6, 'Flash V1', 1, 'EnUso'),
(3, 6, 'X2T – Transmisor de flash', 1, 'EnUso');

-- Audio
INSERT INTO equipo (idCateEquipo, idMarca, modelo, cantDisponible, estado) VALUES
(4, 6, 'Micrófono (modelo no especificado)', 1, 'EnUso');

-- Estabilización y Soporte
INSERT INTO equipo (idCateEquipo, idMarca, modelo, cantDisponible, estado) VALUES
(5, 6, 'Gimbal Mini Crane 3', 1, 'EnUso'),
(5, 4, 'Zhiyun Weebill S', 1, 'EnUso'),
(5, 4, 'Zhiyun Weebill 3S', 1, 'EnUso'),
(5, 6, 'Trípode estándar', 2, 'EnUso'),
(5, 6, 'Trípode 2.10 m regulable', 5, 'EnUso');

-- Almacenamiento
INSERT INTO equipo (idCateEquipo, idMarca, modelo, cantDisponible, estado) VALUES
(6, 6, 'Memoria SD 128 GB', 6, 'EnUso'),
(6, 6, 'Memoria SD 32 GB', 2, 'EnUso');

-- Computadoras y Edición
INSERT INTO equipo (idCateEquipo, idMarca, modelo, cantDisponible, descripcion, caracteristica, estado) VALUES
(7, 6, 'PC Ryzen 7 con tarjeta gráfica', 4, 'Estación de trabajo para edición', 'RAM: 48 GB, Almacenamiento: 1 TB', 'EnUso');

-- Producción y Oficina
INSERT INTO equipo (idCateEquipo, idMarca, modelo, cantDisponible, estado) VALUES
(8, 5, 'Impresora Epson L8180', 1, 'EnUso'),
(8, 6, 'Guillotina A3', 1, 'EnUso'),
(8, 6, 'Enmicadora A3', 1, 'EnUso');

-- Escenografía y Fondos
INSERT INTO equipo (idCateEquipo, idMarca, modelo, cantDisponible, estado) VALUES
(9, 6, 'Porta fondo de papel', 1, 'EnUso'),
(9, 6, 'Porta fondo de tela', 1, 'EnUso');