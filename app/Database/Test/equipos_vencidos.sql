USE ishumeProyectos;

ALTER TABLE equipos
    MODIFY estadoservicio ENUM('Pendiente','En Proceso','Completado','Programado','Vencido') DEFAULT 'Pendiente';

UPDATE equipos e
INNER JOIN servicioscontratados sc ON sc.idserviciocontratado = e.idserviciocontratado
SET e.estadoservicio = 'Vencido'
WHERE COALESCE(e.estadoservicio, 'Pendiente') NOT IN ('Completado', 'Vencido')
  AND sc.fechahoraservicio < NOW();
