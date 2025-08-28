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
    telprincipal    CHAR (9) NOT NULL,
    telalternativo  CHAR (9),
    direccion       VARCHAR(150) NOT NULL,
    referencia      VARCHAR(150) NOT NULL
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
    claveacceso VARCHAR(255) NOT NULL ,
    estado TINYINT DEFAULT 1,
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
    CONSTRAINT fk_entregable_servicio FOREIGN KEY (idserviciocontratado) REFERENCES servicioscontratados(idserviciocontratado),
    CONSTRAINT fk_entregable_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona)
);


CREATE TABLE equipos (
    idequipo INT AUTO_INCREMENT PRIMARY KEY,
    idserviciocontratado INT,
    idusuario INT,
    descripcion VARCHAR(200),
    estadoservicio VARCHAR(50),
    CONSTRAINT fk_equipo_servicio FOREIGN KEY (idserviciocontratado) REFERENCES servicioscontratados(idserviciocontratado),
    CONSTRAINT fk_equipo_usuario FOREIGN KEY (idusuario) REFERENCES usuarios(idusuario)
);
