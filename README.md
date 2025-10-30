# ISHUME - Sistema de Gestión de Eventos

## Descripción General

ISHUME es un sistema integral de gestión empresarial diseñado específicamente para empresas de servicios de eventos. La plataforma permite administrar de manera eficiente todos los aspectos del negocio, desde la cotización inicial hasta la entrega final del servicio, incluyendo la gestión de equipos, control de pagos, inventario y comunicación interna.

El sistema está orientado a empresas que ofrecen servicios de audio, fotografía, iluminación, decoración y catering para eventos como bodas, quinceañeras, eventos corporativos, conferencias y conciertos.

## Características Principales

### Gestión de Clientes y Cotizaciones
- Registro de clientes personas naturales y jurídicas
- Generación de cotizaciones personalizadas
- Gestión de contratos y tipos de eventos
- Historial completo de interacciones con clientes

### Control de Pagos
- Seguimiento de amortizaciones y saldos pendientes
- Registro de transacciones con múltiples métodos de pago
- Gestión de comprobantes de pago
- Reportes financieros detallados

### Gestión de Equipos (Kanban)
- Tablero Kanban para asignación de técnicos a servicios
- Estados de servicio: Pendiente, En Proceso, Completado, Programado
- Priorización automática por fechas de servicio
- Visualización de información completa del cliente y servicio
- Sistema de drag-and-drop para cambios de estado
- Historial de actividades y cambios de estado

### Inventario de Equipos
- Catálogo completo de equipos disponibles
- Control de stock y disponibilidad
- Gestión de categorías, marcas y ubicaciones
- Registro fotográfico de equipos
- Seguimiento de mantenimiento y estado

### Cronograma y Entregas
- Programación de servicios y eventos
- Gestión de entregas con fechas programadas y reales
- Registro de comprobantes de entrega
- Seguimiento de observaciones y estado de entregas

### Sistema de Mensajería Interna
- Comunicación entre usuarios del sistema
- Notificaciones en tiempo real
- Historial de conversaciones
- Gestión de mensajes leídos y no leídos

### Reportes y Análisis
- Reportes de productividad de equipos
- Estadísticas de actividades por período
- Análisis de servicios y eventos
- Reportes financieros personalizables

### Gestión de Usuarios
- Sistema de autenticación seguro con bcrypt
- Roles diferenciados: Administrador y Trabajador
- Validación de DNI con integración a RENIEC
- Generación automática de credenciales
- Control de usuarios activos e inactivos

## Tecnologías Utilizadas

### Backend
- **Framework**: CodeIgniter 4
- **Lenguaje**: PHP 8.1+
- **Base de Datos**: MySQL 8.0+
- **Arquitectura**: MVC (Model-View-Controller)

### Frontend
- **Framework CSS**: Bootstrap 5
- **JavaScript**: jQuery
- **Componentes UI**: SweetAlert2 para notificaciones
- **Interactividad**: AJAX para operaciones asíncronas
- **Drag & Drop**: Funcionalidad nativa para tablero Kanban

### Seguridad
- Hashing de contraseñas con bcrypt
- Protección CSRF (Cross-Site Request Forgery)
- Validación de datos en servidor y cliente
- Control de sesiones seguro
- Sanitización de entradas de usuario

## Requisitos del Sistema

### Servidor
- PHP 8.1 o superior
- MySQL 8.0 o superior
- Apache 2.4+ o Nginx 1.18+
- Composer 2.0+

### Extensiones PHP Requeridas
- intl
- mbstring
- mysqlnd
- json
- libcurl
- gd (para procesamiento de imágenes)

### Configuración Recomendada
- memory_limit: 256M o superior
- upload_max_filesize: 10M o superior
- post_max_size: 10M o superior
- max_execution_time: 300

## Instalación

### 1. Clonar el Repositorio
```bash
git clone https://github.com/xdasd13/appishume.git
cd appishume
```

### 2. Instalar Dependencias
```bash
composer install
```

### 3. Configurar Variables de Entorno
```bash
cp envNO-BORRAR .env
```

Editar el archivo `.env` con la configuración de su entorno:
```ini
# Base URL
app.baseURL = 'http://localhost/appishume/public/'

# Base de datos
database.default.hostname = localhost
database.default.database = ishume
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.DBPrefix = 
database.default.port = 3306
```

### 4. Crear la Base de Datos
```bash
mysql -u root -p < app/Database/database.sql
```

O importar manualmente el archivo `app/Database/database.sql` desde phpMyAdmin.

### 5. Configurar Permisos
```bash
chmod -R 755 writable/
chmod -R 755 public/uploads/
```

### 6. Configurar Virtual Host (Opcional pero Recomendado)

#### Apache
```apache
<VirtualHost *:80>
    ServerName appishume.test
    DocumentRoot "C:/xampp/htdocs/appishume/public"
    
    <Directory "C:/xampp/htdocs/appishume/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name appishume.test;
    root /var/www/appishume/public;
    
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 7. Acceder al Sistema
Abrir el navegador y acceder a:
```
http://localhost/appishume/public/
```
O si configuró un virtual host:
```
http://appishume.test/
```

### Credenciales por Defecto
- **Usuario**: admin
- **Contraseña**: admin123

**Importante**: Cambiar estas credenciales inmediatamente después del primer acceso.

## Estructura del Proyecto

```
appishume/
├── app/
│   ├── Config/          # Configuración de la aplicación
│   ├── Controllers/     # Controladores MVC
│   │   ├── AuthController.php
│   │   ├── Equipos.php
│   │   ├── UsuariosController.php
│   │   ├── InventarioController.php
│   │   ├── ControlPagoController.php
│   │   ├── EntregasController.php
│   │   ├── MensajeriaController.php
│   │   ├── ReportesController.php
│   │   └── Cronograma.php
│   ├── Models/          # Modelos de datos
│   │   ├── UsuarioModel.php
│   │   ├── EquipoModel.php
│   │   ├── PersonaModel.php
│   │   ├── InventarioEquipoModel.php
│   │   ├── ControlPagoModel.php
│   │   ├── EntregasModel.php
│   │   ├── MensajeModel.php
│   │   └── HistorialActividadesModel.php
│   ├── Views/           # Vistas de la aplicación
│   │   ├── auth/
│   │   ├── usuarios/
│   │   ├── Equipos/
│   │   ├── Inventario/
│   │   ├── ControlPagos/
│   │   ├── entregas/
│   │   ├── mensajeria/
│   │   ├── reportes/
│   │   └── cronograma/
│   ├── Database/        # Scripts SQL
│   │   └── database.sql
│   └── Helpers/         # Funciones auxiliares
├── public/              # Archivos públicos
│   ├── assets/          # CSS, JS, imágenes
│   ├── uploads/         # Archivos subidos
│   └── index.php        # Punto de entrada
├── writable/            # Archivos temporales y logs
├── composer.json        # Dependencias PHP
└── README.md           # Este archivo
```

## Módulos del Sistema

### 1. Autenticación y Usuarios
- Login y logout seguro
- Gestión de sesiones
- Creación de usuarios para personal existente o nuevo
- Validación automática de DNI con RENIEC
- Generación automática de emails corporativos
- Reactivación de usuarios desactivados

### 2. Gestión de Equipos (Kanban)
- Tablero visual con columnas por estado
- Asignación de técnicos a servicios
- Información completa del cliente en cada tarjeta
- Priorización por fecha de servicio
- Historial de cambios de estado
- Auditoría de actividades

### 3. Inventario
- Catálogo de equipos con fotografías
- Control de stock y disponibilidad
- Gestión de categorías y marcas
- Ubicaciones de almacenamiento
- Registro de características técnicas

### 4. Control de Pagos
- Registro de amortizaciones
- Cálculo automático de saldos
- Múltiples métodos de pago
- Comprobantes digitales
- Historial de transacciones

### 5. Entregas
- Programación de entregas
- Registro de fechas reales
- Comprobantes de entrega
- Seguimiento de estado
- Observaciones y notas

### 6. Mensajería
- Chat interno entre usuarios
- Notificaciones en tiempo real
- Historial de conversaciones
- Indicadores de mensajes no leídos

### 7. Reportes
- Productividad de equipos
- Estadísticas por período
- Análisis de servicios
- Reportes personalizables

### 8. Cronograma
- Vista de calendario de eventos
- Programación de servicios
- Gestión de fechas y horarios
- Visualización de disponibilidad

## Características de Seguridad

### Autenticación
- Contraseñas hasheadas con bcrypt
- Validación de fortaleza de contraseña
- Requisitos: mínimo 8 caracteres, mayúsculas, minúsculas, números y símbolos
- Sesiones seguras con regeneración de ID

### Autorización
- Control de acceso basado en roles
- Permisos diferenciados para administradores y trabajadores
- Validación de permisos en cada operación

### Protección de Datos
- Protección CSRF en todos los formularios
- Validación de datos en servidor
- Sanitización de entradas
- Prevención de SQL Injection mediante Query Builder
- Prevención de XSS mediante escape de salidas

### Auditoría
- Registro de actividades en tablero Kanban
- Historial de cambios de estado
- Seguimiento de operaciones críticas
- Logs de sistema

## Integración con Servicios Externos

### RENIEC (Registro Nacional de Identificación y Estado Civil)
- Validación automática de DNI peruano
- Autocompletado de nombres y apellidos
- Cache local para optimización
- Manejo de errores y timeouts

## Flujo de Trabajo Típico

1. **Cotización**: El administrador registra un nuevo cliente y genera una cotización para un evento.

2. **Contrato**: Una vez aprobada la cotización, se genera el contrato correspondiente.

3. **Servicios Contratados**: Se registran los servicios específicos solicitados con fechas y ubicaciones.

4. **Asignación de Técnicos**: En el tablero Kanban, se asignan técnicos a cada servicio contratado.

5. **Control de Pagos**: Se registran las amortizaciones y se hace seguimiento del saldo pendiente.

6. **Gestión de Inventario**: Se verifica la disponibilidad de equipos necesarios para el evento.

7. **Ejecución del Servicio**: Los técnicos actualizan el estado del servicio en el Kanban.

8. **Entrega**: Se registra la entrega del servicio con comprobantes y observaciones.

9. **Reportes**: Se generan reportes de productividad y análisis del evento.

## Mantenimiento y Soporte

### Logs del Sistema
Los logs se encuentran en:
```
writable/logs/
```

### Backup de Base de Datos
Se recomienda realizar backups diarios de la base de datos:
```bash
mysqldump -u root -p ishume > backup_ishume_$(date +%Y%m%d).sql
```

### Actualización del Sistema
```bash
git pull origin main
composer update
```

Revisar el archivo de migraciones para cambios en la base de datos.

## Solución de Problemas Comunes

### Error 404 en todas las rutas
Verificar que el módulo `mod_rewrite` de Apache esté habilitado y que el archivo `.htaccess` exista en la carpeta `public/`.

### Error de conexión a base de datos
Verificar las credenciales en el archivo `.env` y que el servicio MySQL esté activo.

### Permisos denegados en uploads
Ejecutar:
```bash
chmod -R 755 public/uploads/
chmod -R 755 writable/
```

### Sesión expirada constantemente
Verificar la configuración de sesiones en `app/Config/App.php` y ajustar el tiempo de expiración.

## Contribución

Este es un proyecto privado de uso interno. Para reportar problemas o sugerir mejoras, contactar al equipo de desarrollo.

## Licencia

Este proyecto es de uso privado y confidencial. Todos los derechos reservados.

## Contacto y Soporte

Para soporte técnico o consultas sobre el sistema, contactar al administrador del sistema.

---

**Versión**: 1.0.0  
**Última actualización**: Octubre 2025  
**Desarrollado con**: CodeIgniter 4 Framework
