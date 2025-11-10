USE ishume;

-- PERSONAS ADICIONALES (Clientes)
INSERT INTO personas (apellidos, nombres, tipodoc, numerodoc, telprincipal, telalternativo, direccion, referencia) VALUES 
('Huamán Quispe', 'Sandra Milagros', 'DNI', '76541289', '945123678', NULL, 'Av. Los Álamos 890, Villa María del Triunfo', 'Casa de tres pisos, portón verde'),
('Ccama Flores', 'Eduardo Martín', 'DNI', '69874523', '978456789', '921345678', 'Jr. Los Pinos 345, Independencia', 'Frente a la bodega El Buen Sabor'),
('Quispe Mamani', 'Patricia Elena', 'DNI', '74125896', '932147896', NULL, 'Av. Túpac Amaru 2345, Carabayllo', 'Conjunto habitacional Los Rosales'),
('Torres Ccama', 'Javier Alonso', 'DNI', '70589634', '921456987', '965789123', 'Calle Las Orquídeas 678, Los Olivos', 'Casa esquina, color beige'),
('Paucar Quispe', 'Melissa Vanessa', 'DNI', '73698745', '965123987', NULL, 'Av. Universitaria 3456, San Martín de Porres', 'Edificio Los Laureles, dpto 204'),
('Mendoza Torres', 'Cristian David', 'DNI', '67452189', '978456987', '945789321', 'Jr. San Juan 234, Magdalena', 'Casa colonial restaurada'),
('Silva Paucar', 'Daniela Lucía', 'DNI', '75896325', '954123987', NULL, 'Av. La Mar 1890, Pueblo Libre', 'Edificio moderno, piso 6'),
('Díaz Huamán', 'Rodrigo Andrés', 'DNI', '68741256', '987321987', '912789654', 'Calle Los Cedros 456, San Miguel', 'Casa con jardín frontal'),
('Rojas Mendoza', 'Valeria Sofía', 'DNI', '72589636', '921789987', NULL, 'Av. Salaverry 2890, Jesús María', 'Torre empresarial, piso 12'),
('Flores Ccama', 'Sebastián Mateo', 'DNI', '70123456', '978456987', '965123456', 'Jr. Huancavelica 567, Cercado de Lima', 'Local comercial anexo');

-- EMPRESAS ADICIONALES
INSERT INTO empresas (ruc, razonsocial, direccion, telefono) VALUES 
('20963258741', 'Inversiones Turísticas del Sur SAC', 'Av. Larco 890, Miraflores', '016543210'),
('20741963852', 'Corporación Hotelera Premium EIRL', 'Jr. Las Flores 456, San Borja', '015432109'),
('20852741963', 'Eventos Sociales Elegantes SA', 'Av. Conquistadores 2345, San Isidro', '014321098'),
('20369852741', 'Producciones Multimedia Lima SRL', 'Calle Los Creativos 678, Miraflores', '013210987');

-- CLIENTES ADICIONALES
INSERT INTO clientes (idpersona, idempresa) VALUES 
(29, NULL), (30, NULL), (NULL, 9), (31, NULL), (32, NULL),
(NULL, 10), (33, NULL), (34, NULL), (NULL, 11), (35, NULL),
(36, NULL), (37, NULL), (NULL, 12), (38, NULL);

-- COTIZACIONES ADICIONALES (Febrero - Marzo 2026)
INSERT INTO cotizaciones (idcliente, idtipocontrato, idusuariocrea, fechacotizacion, fechaevento, idtipoevento) VALUES 
-- Febrero 2026
(28, 1, 2, '2026-01-10', '2026-02-05', 1),  -- Boda Sandra Huamán
(29, 1, 3, '2026-01-12', '2026-02-08', 2),  -- Quinceañero Eduardo Ccama
(30, 4, 1, '2026-01-15', '2026-02-12', 3),  -- Evento Corporativo
(31, 1, 2, '2026-01-18', '2026-02-14', 1),  -- Boda Javier Torres (San Valentín)
(32, 1, 3, '2026-01-20', '2026-02-18', 2),  -- Quinceañero Melissa Paucar
(33, 1, 2, '2026-01-22', '2026-02-21', 1),  -- Boda Cristian Mendoza
(35, 1, 3, '2026-01-25', '2026-02-25', 2),  -- Quinceañero Daniela Silva
(36, 4, 1, '2026-01-28', '2026-02-28', 4),  -- Conferencia Empresa
-- Marzo 2026
(37, 1, 2, '2026-02-01', '2026-03-05', 1),  -- Boda Rodrigo Díaz
(38, 1, 3, '2026-02-03', '2026-03-08', 2),  -- Quinceañero Valeria Rojas
(39, 4, 1, '2026-02-05', '2026-03-12', 3),  -- Evento Corporativo
(40, 1, 2, '2026-02-08', '2026-03-15', 1);  -- Boda Sebastián Flores

-- CONTRATOS ADICIONALES
INSERT INTO contratos (idcotizacion, idcliente, autorizapublicacion) VALUES 
(20, 28, 1), (21, 29, 0), (22, 30, 1), (23, 31, 1), 
(24, 32, 0), (25, 33, 1), (26, 35, 1), (27, 36, 0),
(28, 37, 1), (29, 38, 1), (30, 39, 0), (31, 40, 1);

-- SERVICIOS CONTRATADOS ADICIONALES

-- Cotización 20: Boda Sandra Huamán (Feb 05, 2026)
INSERT INTO servicioscontratados (idcotizacion, idservicio, cantidad, precio, fechahoraservicio, direccion) VALUES 
(20, 2, 1, 1200.00, '2026-02-05 16:00:00', 'Jardín Botánico de Lima, Av. La Molina'),
(20, 6, 1, 1800.00, '2026-02-05 15:30:00', 'Jardín Botánico de Lima, Av. La Molina'),
(20, 11, 1, 950.00, '2026-02-05 15:00:00', 'Jardín Botánico de Lima, Av. La Molina'),
(20, 14, 1, 1200.00, '2026-02-05 14:30:00', 'Jardín Botánico de Lima, Av. La Molina'),
(20, 18, 75, 2625.00, '2026-02-05 19:00:00', 'Jardín Botánico de Lima, Av. La Molina'),
(20, 22, 1, 650.00, '2026-02-05 21:00:00', 'Jardín Botánico de Lima, Av. La Molina');

-- Cotización 21: Quinceañero Eduardo Ccama (Feb 08, 2026)
INSERT INTO servicioscontratados (idcotizacion, idservicio, cantidad, precio, fechahoraservicio, direccion) VALUES 
(21, 1, 1, 450.00, '2026-02-08 18:00:00', 'Salón de Eventos Los Jardines, Independencia'),
(21, 5, 1, 850.00, '2026-02-08 17:30:00', 'Salón de Eventos Los Jardines, Independencia'),
(21, 10, 1, 400.00, '2026-02-08 17:00:00', 'Salón de Eventos Los Jardines, Independencia'),
(21, 17, 45, 1575.00, '2026-02-08 20:00:00', 'Salón de Eventos Los Jardines, Independencia'),
(21, 22, 1, 650.00, '2026-02-08 21:30:00', 'Salón de Eventos Los Jardines, Independencia');

-- Cotización 22: Evento Corporativo (Feb 12, 2026)
INSERT INTO servicioscontratados (idcotizacion, idservicio, cantidad, precio, fechahoraservicio, direccion) VALUES 
(22, 3, 2, 1600.00, '2026-02-12 09:00:00', 'Hotel Westin, San Isidro'),
(22, 8, 1, 1500.00, '2026-02-12 08:30:00', 'Hotel Westin, San Isidro'),
(22, 12, 1, 750.00, '2026-02-12 08:45:00', 'Hotel Westin, San Isidro'),
(22, 17, 130, 4550.00, '2026-02-12 13:00:00', 'Hotel Westin, San Isidro');

-- Cotización 23: Boda San Valentín Javier Torres (Feb 14, 2026)
INSERT INTO servicioscontratados (idcotizacion, idservicio, cantidad, precio, fechahoraservicio, direccion) VALUES 
(23, 2, 1, 1200.00, '2026-02-14 18:00:00', 'Casa Hacienda San Valentín, Pachacamac'),
(23, 7, 1, 2200.00, '2026-02-14 17:30:00', 'Casa Hacienda San Valentín, Pachacamac'),
(23, 11, 1, 950.00, '2026-02-14 17:00:00', 'Casa Hacienda San Valentín, Pachacamac'),
(23, 14, 1, 1200.00, '2026-02-14 16:30:00', 'Casa Hacienda San Valentín, Pachacamac'),
(23, 18, 95, 3325.00, '2026-02-14 20:00:00', 'Casa Hacienda San Valentín, Pachacamac'),
(23, 22, 1, 650.00, '2026-02-14 23:00:00', 'Casa Hacienda San Valentín, Pachacamac'),
(23, 24, 1, 1200.00, '2026-02-14 22:00:00', 'Casa Hacienda San Valentín, Pachacamac');

-- CONTROL DE PAGOS ADICIONALES
INSERT INTO controlpagos (idcontrato, saldo, amortizacion, deuda, idtipopago, numtransaccion, fechahora, idusuario, dni_pagador, nombre_pagador) VALUES 
-- Contrato 20 (Boda Sandra - Total: 8425) - Adelanto 50%
(20, 8425.00, 4212.50, 4212.50, 2, 'TXN20260115001', '2026-01-15 10:30:00', 2, '76541289', 'Sandra Milagros Huamán Quispe'),

-- Contrato 21 (Quinceañero Eduardo - Total: 3925) - Adelanto 50%
(21, 3925.00, 1962.50, 1962.50, 5, 'YAPE20260118001', '2026-01-18 14:20:00', 3, '69874523', 'Eduardo Martín Ccama Flores'),

-- Contrato 22 (Evento Corporativo - Total: 8400) - Adelanto 30%
(22, 8400.00, 2520.00, 5880.00, 2, 'TXN20260120001', '2026-01-20 09:15:00', 1, NULL, 'Inversiones Turísticas del Sur SAC'),

-- Contrato 23 (Boda San Valentín - Total: 10725) - COMPLETAMENTE PAGADO
(23, 10725.00, 5362.50, 5362.50, 2, 'TXN20260122001', '2026-01-22 11:45:00', 2, '70589634', 'Javier Alonso Torres Ccama'),
(23, 5362.50, 5362.50, 0.00, 2, 'TXN20260210001', '2026-02-10 16:30:00', 2, '70589634', 'Javier Alonso Torres Ccama');

-- EQUIPOS ADICIONALES
INSERT INTO equipos (idserviciocontratado, idusuario, descripcion, estadoservicio) VALUES 
-- Servicios de cotización 20 (Boda Sandra)
(55, 3, 'Sistema de sonido profesional: mezcladora digital, micrófonos inalámbricos Shure', 'Programado'),
(56, 4, 'Cobertura fotográfica premium: Canon EOS R6, lentes profesionales', 'Programado'),

-- Servicios de cotización 21 (Quinceañero Eduardo)
(61, 3, 'Audio para quinceañero: sistema compacto, DJ incluido', 'Programado'),
(62, 5, 'Fotografía y video: cobertura completa del evento', 'Programado'),

-- Servicios de cotización 22 (Evento Corporativo)
(66, 2, 'Transmisión corporativa: cámaras 4K, streaming profesional', 'Programado'),
(67, 4, 'Fotografía corporativa: retratos y cobertura de conferencia', 'Programado'),

-- Servicios de cotización 23 (Boda San Valentín)
(70, 3, 'Sistema de audio premium para boda temática', 'Programado'),
(71, 4, 'Cobertura fotográfica y cinematográfica completa', 'Programado');

-- CONSULTAS DE VERIFICACIÓN
SELECT '=== DATOS DE PRUEBA INSERTADOS EXITOSAMENTE ===' as mensaje;
SELECT 'Total de personas adicionales:' as info, COUNT(*) as cantidad FROM personas WHERE idpersona > 28;
SELECT 'Total de contratos adicionales:' as info, COUNT(*) as cantidad FROM contratos WHERE idcontrato > 19;
SELECT 'Total de pagos adicionales:' as info, COUNT(*) as cantidad FROM controlpagos WHERE idpagos > 7;