# Módulo de Inventario de Equipos Audiovisuales - ISHUME

## Descripción
Sistema completo de gestión de inventario para equipos audiovisuales desarrollado en CodeIgniter 4 con Bootstrap 5.3 y SweetAlert2.

## Funcionalidades Implementadas

### ✅ CRUD Completo
- **Listar equipos**: Vista en cards con filtros avanzados
- **Crear equipo**: Formulario con validaciones completas
- **Editar equipo**: Formulario pre-cargado con datos existentes
- **Eliminar equipo**: Confirmación con SweetAlert

### ✅ Características Principales
- **Gestión de imágenes**: Subida de archivos y URLs
- **Validaciones robustas**: Cliente y servidor
- **Filtros dinámicos**: Por categoría, marca, estado y modelo
- **Estadísticas en tiempo real**: Dashboard con métricas
- **Generación automática de SKU**: Si no se especifica
- **Modal de detalles**: Vista completa del equipo
- **Responsive design**: Compatible con todos los dispositivos

### ✅ Integración SweetAlert2
- Notificaciones toast elegantes
- Confirmaciones para acciones críticas
- Estados de carga con spinners
- Manejo de errores visual

## Estructura de Archivos

### Modelos
- `app/Models/InventarioEquipoModel.php` - Modelo principal del inventario
- `app/Models/CateEquipoModel.php` - Gestión de categorías
- `app/Models/MarcaEquipoModel.php` - Gestión de marcas
- `app/Models/UbicacionModel.php` - Gestión de ubicaciones

### Controlador
- `app/Controllers/InventarioController.php` - Lógica completa CRUD

### Vistas
- `app/Views/Inventario/listar.php` - Lista con cards y filtros
- `app/Views/Inventario/crear.php` - Formulario de creación
- `app/Views/Inventario/editar.php` - Formulario de edición

### Configuración
- Rutas agregadas en `app/Config/Routes.php`
- Directorio de uploads: `public/uploads/equipos/`

## Base de Datos

### Tablas Principales
- `equipo` - Equipos del inventario
- `cateEquipo` - Categorías de equipos
- `marcaEquipo` - Marcas de equipos
- `ubicacion` - Ubicaciones físicas
- `equipoUbicacion` - Relación equipo-ubicación
- `movimientoEquipo` - Historial de movimientos

### Campos del Equipo
- `idEquipo` - ID único
- `idCateEquipo` - Categoría (FK)
- `idMarca` - Marca (FK)
- `modelo` - Modelo del equipo
- `descripcion` - Descripción general
- `caracteristica` - Especificaciones técnicas
- `sku` - Código único interno
- `numSerie` - Número de serie físico
- `cantDisponible` - Cantidad disponible
- `estado` - Estado (Nuevo, EnUso, EnMantenimiento, Dañado, Otro)
- `fechaCompra` - Fecha de compra
- `fechaUso` - Fecha de primer uso
- `imgEquipo` - Ruta de imagen

## Rutas Disponibles

### Principales
- `GET /inventario` - Lista de equipos
- `GET /inventario/crear` - Formulario de creación
- `POST /inventario/guardar` - Guardar nuevo equipo
- `GET /inventario/editar/{id}` - Formulario de edición
- `POST /inventario/actualizar/{id}` - Actualizar equipo
- `DELETE /inventario/eliminar/{id}` - Eliminar equipo

### APIs AJAX
- `GET /inventario/ver/{id}` - Detalles del equipo
- `GET /inventario/buscar` - Búsqueda con filtros
- `GET /inventario/estadisticas` - Estadísticas del inventario

## Validaciones Implementadas

### Servidor (CodeIgniter)
- Categoría y marca obligatorias
- Modelo: 2-70 caracteres
- SKU único (opcional)
- Número de serie único (opcional)
- Cantidad >= 0
- Estado válido del enum
- Fechas en formato correcto

### Cliente (JavaScript)
- Validación en tiempo real
- Feedback visual inmediato
- Contador de caracteres
- Validación de archivos de imagen
- Confirmaciones antes de envío

## Características de Seguridad

- **Validación CSRF**: Protección contra ataques
- **Sanitización**: Escape de datos con `esc()`
- **Validación de archivos**: Tipo y tamaño de imágenes
- **Query Builder**: Prevención de inyección SQL
- **Manejo de errores**: Logs detallados

## Uso del Sistema

### Acceso
Navegar a `/inventario` para acceder al módulo principal.

### Agregar Equipo
1. Clic en "Agregar Equipo"
2. Completar formulario obligatorio
3. Subir imagen (opcional)
4. Confirmar creación

### Buscar/Filtrar
- Usar filtros por categoría, marca, estado
- Búsqueda por modelo en tiempo real
- Limpiar filtros con un clic

### Ver Detalles
- Clic en botón "Ver" en cualquier card
- Modal con información completa
- Imagen ampliada si disponible

### Editar/Eliminar
- Botones de acción en cada card
- Confirmación antes de eliminar
- Formulario pre-cargado para edición

## Tecnologías Utilizadas

- **Backend**: PHP 8+ con CodeIgniter 4
- **Frontend**: Bootstrap 5.3
- **JavaScript**: Vanilla JS + SweetAlert2
- **Base de datos**: MySQL
- **Validación**: HTML5 + CodeIgniter Validation
- **Iconos**: Font Awesome 6

## Notas de Implementación

- Patrón MVC estricto
- Código comentado y documentado
- Responsive design
- Accesibilidad considerada
- Manejo de errores robusto
- Integración con sistema existente ISHUME

## Próximas Mejoras Sugeridas

- Paginación para grandes inventarios
- Exportación a Excel/PDF
- Códigos QR para equipos
- Historial de movimientos
- Alertas de mantenimiento
- Integración con sistema de préstamos
