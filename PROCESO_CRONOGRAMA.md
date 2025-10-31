# MÓDULO DE CRONOGRAMA - DESCRIPCIÓN DEL PROCESO
## Sistema ISHUME - Gestión de Eventos y Servicios Audiovisuales

---

## 📋 ÍNDICE
1. [Descripción General](#descripción-general)
2. [Arquitectura del Módulo](#arquitectura-del-módulo)
3. [Flujo de Ejecución Detallado](#flujo-de-ejecución-detallado)
4. [Componentes del Sistema](#componentes-del-sistema)
5. [Código Implementado](#código-implementado)

---

## 1. DESCRIPCIÓN GENERAL

### Propósito
El módulo de **Cronograma** es un sistema de visualización y gestión de eventos en calendario que permite:
- Visualizar servicios contratados en un calendario interactivo
- Consultar detalles de eventos (fecha, hora, cliente, dirección, estado)
- Actualizar estados de servicios en tiempo real
- Obtener estadísticas y resúmenes semanales
- Filtrar eventos por rangos de fechas

### Tecnologías Utilizadas
- **Backend**: PHP 8.x con CodeIgniter 4
- **Frontend**: FullCalendar.js 5.x
- **Base de Datos**: MySQL 8.x
- **AJAX**: Fetch API para comunicación asíncrona
- **UI**: Bootstrap 5.3.8

---

## 2. ARQUITECTURA DEL MÓDULO

### Patrón de Diseño: MVC (Model-View-Controller)

```
┌─────────────────────────────────────────────────────────────┐
│                    ARQUITECTURA CRONOGRAMA                   │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────┐      ┌──────────────┐      ┌───────────┐ │
│  │   VISTA      │◄─────┤ CONTROLADOR  │◄─────┤  MODELO   │ │
│  │ (Frontend)   │      │  (Backend)   │      │   (DB)    │ │
│  └──────────────┘      └──────────────┘      └───────────┘ │
│         │                      │                     │      │
│         │                      │                     │      │
│  FullCalendar.js        Cronograma.php      CronogramaModel│
│  JavaScript AJAX        Routes Config       MySQL Queries  │
│  HTML/CSS/Bootstrap     Validaciones        Joins Complejos│
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Estructura de Archivos

```
app/
├── Controllers/
│   └── Cronograma.php          # Controlador principal
├── Models/
│   └── CronogramaModel.php     # Modelo de datos
├── Views/
│   └── cronograma/
│       └── index.php           # Vista del calendario
└── Config/
    └── Routes.php              # Configuración de rutas
```

---

## 3. FLUJO DE EJECUCIÓN DETALLADO

### 3.1 PASO 1: Carga Inicial de la Vista

**Secuencia:**
```
Usuario accede a /cronograma
    ↓
Routes.php redirige a Cronograma::index()
    ↓
Controlador inicializa CronogramaModel
    ↓
Obtiene estadísticas del sistema
    ↓
Obtiene próximos 10 servicios
    ↓
Renderiza vista con datos
    ↓
Vista carga FullCalendar.js
```

**Código del Controlador:**
```php
public function index()
{
    try {
        // Inicializar el modelo de cronograma
        if (!isset($this->cronogramaModel)) {
            $this->cronogramaModel = new CronogramaModel();
        }

        // Obtener estadísticas
        $estadisticas = $this->cronogramaModel->getEstadisticas();
        
        // Obtener próximos servicios
        $proximosServicios = $this->cronogramaModel->getProximosServicios(10);

        $data = [
            'header' => view('Layouts/header', ['titulo' => 'Cronograma']),
            'footer' => view('Layouts/footer'),
            'servicios_count' => $estadisticas['servicios_count'],
            'equipos' => $estadisticas['equipos'],
            'tecnicos' => $estadisticas['tecnicos'],
            'proximos' => $proximosServicios
        ];

        return view('cronograma/index', $data);
        
    } catch (\Exception $e) {
        log_message('error', 'Error en cronograma/index: ' . $e->getMessage());
        
        // Datos por defecto en caso de error
        $data = [
            'header' => view('Layouts/header', ['titulo' => 'Cronograma']),
            'footer' => view('Layouts/footer'),
            'servicios_count' => 0,
            'equipos' => 0,
            'tecnicos' => 0,
            'proximos' => []
        ];

        return view('cronograma/index', $data);
    }
}
```

---

### 3.2 PASO 2: Inicialización de FullCalendar

**Secuencia:**
```
Vista cargada en navegador
    ↓
JavaScript inicializa FullCalendar
    ↓
Configura opciones del calendario
    ↓
Define endpoint AJAX para eventos
    ↓
FullCalendar solicita eventos automáticamente
```

**Código JavaScript:**
```javascript
// Inicialización del calendario
var calendarEl = document.getElementById('calendar');
var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    events: {
        url: '<?= base_url('cronograma/eventos') ?>',
        failure: function() {
            console.error('Error al cargar eventos del calendario');
            alert('Error al cargar los eventos del calendario.');
        }
    },
    eventDidMount: function(info) {
        console.log('Evento cargado:', info.event.title, info.event.start);
    },
    eventsSet: function(events) {
        console.log('Total eventos cargados:', events.length);
        if (events.length === 0) {
            console.warn('No se encontraron eventos');
        }
    },
    eventClick: function(info) {
        // Mostrar detalles del evento
        const evento = info.event.extendedProps;
        mostrarDetallesEvento(evento);
    }
});

calendar.render();
```

---

### 3.3 PASO 3: Obtención de Eventos (AJAX)

**Secuencia:**
```
FullCalendar hace petición GET
    ↓
Envía parámetros: start, end (rango de fechas)
    ↓
Routes.php → Cronograma::getEventos()
    ↓
Controlador valida parámetros
    ↓
Llama a CronogramaModel::getEventosCalendario()
    ↓
Modelo ejecuta consulta SQL compleja
    ↓
Formatea datos para FullCalendar
    ↓
Retorna JSON con eventos
    ↓
FullCalendar renderiza eventos en calendario
```

**Código del Endpoint AJAX:**
```php
public function getEventos()
{
    try {
        // Inicializar el modelo de cronograma
        if (!isset($this->cronogramaModel)) {
            $this->cronogramaModel = new CronogramaModel();
        }

        // Obtener parámetros de fecha del calendario
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');

        log_message('info', "getEventos - Parámetros: start=$start, end=$end");

        // Obtener eventos para el calendario
        $eventos = $this->cronogramaModel->getEventosCalendario($start, $end);

        log_message('info', "getEventos - Eventos encontrados: " . count($eventos));

        return $this->response->setJSON($eventos);
        
    } catch (\Exception $e) {
        log_message('error', 'Error en getEventos: ' . $e->getMessage());
        return $this->response->setStatusCode(500)
            ->setJSON(['error' => 'Error al obtener eventos']);
    }
}
```

---

### 3.4 PASO 4: Consulta SQL y Formateo de Datos

**Secuencia del Modelo:**
```
getEventosCalendario($start, $end)
    ↓
Construye consulta SQL con JOINs
    ↓
JOIN: servicioscontratados → cotizaciones → clientes
    ↓
JOIN: clientes → personas/empresas
    ↓
JOIN: servicios, equipos
    ↓
Filtra por rango de fechas
    ↓
Ejecuta consulta
    ↓
Si hay resultados → formatear
    ↓
Si no hay resultados → consulta simplificada
    ↓
Si falla todo → consulta de emergencia
    ↓
Retorna array de eventos formateados
```

**Código del Modelo:**
```php
public function getEventosCalendario($start = null, $end = null)
{
    try {
        $whereClause = "";
        if ($start && $end) {
            $whereClause = "AND sc.fechahoraservicio BETWEEN '$start' AND '$end'";
        }

        // Consulta completa con todos los datos
        $queryCompleta = "
            SELECT 
                sc.idserviciocontratado as id,
                CONCAT(COALESCE(s.servicio, 'Servicio'), ' - ', 
                    CASE 
                        WHEN c.idempresa IS NOT NULL 
                            THEN COALESCE(e.razonsocial, 'Empresa')
                        ELSE CONCAT(COALESCE(p.nombres, 'Cliente'), ' ', 
                                   COALESCE(p.apellidos, ''))
                    END
                ) as title,
                sc.fechahoraservicio as start,
                COALESCE(sc.direccion, 'Sin dirección') as direccion,
                CASE 
                    WHEN c.idempresa IS NOT NULL 
                        THEN COALESCE(e.telefono, 'Sin teléfono')
                    ELSE COALESCE(p.telefono, 'Sin teléfono')
                END as telefono,
                COALESCE(eq.estadoservicio, 'Pendiente') as estado,
                CASE 
                    WHEN COALESCE(eq.estadoservicio, 'Pendiente') = 'Completado' 
                        THEN '#4caf50'
                    WHEN COALESCE(eq.estadoservicio, 'Pendiente') = 'En Proceso' 
                        THEN '#ff9800'
                    WHEN COALESCE(eq.estadoservicio, 'Pendiente') = 'Programado' 
                        THEN '#2196f3'
                    ELSE '#757575'
                END as color
            FROM servicioscontratados sc
            LEFT JOIN cotizaciones cot ON sc.idcotizacion = cot.idcotizacion
            LEFT JOIN contratos con ON cot.idcotizacion = con.idcotizacion
            LEFT JOIN clientes c ON cot.idcliente = c.idcliente
            LEFT JOIN personas p ON c.idpersona = p.idpersona
            LEFT JOIN empresas e ON c.idempresa = e.idempresa
            LEFT JOIN servicios s ON sc.idservicio = s.idservicio
            LEFT JOIN equipos eq ON sc.idserviciocontratado = eq.idserviciocontratado
            WHERE 1=1 $whereClause
            ORDER BY sc.fechahoraservicio ASC
        ";

        $eventos = $this->db->query($queryCompleta)->getResult();
        
        // Si no hay resultados, intentar consulta simplificada
        if (empty($eventos)) {
            $querySimple = "
                SELECT 
                    sc.idserviciocontratado as id,
                    CONCAT('Servicio ID: ', sc.idservicio, ' - ', 
                           DATE_FORMAT(sc.fechahoraservicio, '%d/%m/%Y %H:%i')) as title,
                    sc.fechahoraservicio as start,
                    COALESCE(sc.direccion, 'Sin dirección') as direccion,
                    'Sin teléfono' as telefono,
                    'Pendiente' as estado,
                    '#2196f3' as color
                FROM servicioscontratados sc
                WHERE 1=1 $whereClause
                ORDER BY sc.fechahoraservicio ASC
            ";
            
            $eventos = $this->db->query($querySimple)->getResult();
        }
        
        // Formatear eventos para FullCalendar
        $eventosFormateados = [];
        foreach ($eventos as $evento) {
            $eventosFormateados[] = [
                'id' => $evento->id,
                'title' => $evento->title,
                'start' => $evento->start,
                'color' => $evento->color,
                'extendedProps' => [
                    'direccion' => $evento->direccion,
                    'telefono' => $evento->telefono,
                    'estado' => $evento->estado
                ]
            ];
        }

        return $eventosFormateados;

    } catch (\Exception $e) {
        log_message('error', 'Error en getEventosCalendario: ' . $e->getMessage());
        return [];
    }
}
```

---

### 3.5 PASO 5: Actualización de Estado de Servicio

**Secuencia:**
```
Usuario hace clic en evento
    ↓
Modal muestra detalles del servicio
    ↓
Usuario cambia estado (dropdown)
    ↓
JavaScript captura cambio
    ↓
Petición AJAX POST a /cronograma/actualizar-estado
    ↓
Controlador valida petición AJAX
    ↓
Valida parámetros (id, estado)
    ↓
Llama a CronogramaModel::actualizarEstadoServicio()
    ↓
Modelo actualiza registro en BD
    ↓
Retorna JSON con resultado
    ↓
JavaScript actualiza vista
    ↓
FullCalendar recarga eventos
```

**Código del Controlador:**
```php
public function actualizarEstado()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)
            ->setJSON(['error' => 'Solo peticiones AJAX']);
    }

    $json = $this->request->getJSON(true);
    
    if (!isset($json['id']) || !isset($json['estado'])) {
        return $this->response->setStatusCode(400)
            ->setJSON(['error' => 'Datos incompletos']);
    }

    try {
        if (!isset($this->cronogramaModel)) {
            $this->cronogramaModel = new CronogramaModel();
        }

        $resultado = $this->cronogramaModel->actualizarEstadoServicio(
            $json['id'], 
            $json['estado']
        );
        
        if ($resultado) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Estado actualizado correctamente'
            ]);
        } else {
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => 'Error al actualizar estado']);
        }
        
    } catch (\Exception $e) {
        log_message('error', 'Error en actualizarEstado: ' . $e->getMessage());
        return $this->response->setStatusCode(500)
            ->setJSON(['error' => 'Error al actualizar estado']);
    }
}
```

---

## 4. COMPONENTES DEL SISTEMA

### 4.1 Controlador: Cronograma.php

**Responsabilidades:**
- Gestionar peticiones HTTP
- Validar parámetros de entrada
- Coordinar entre Modelo y Vista
- Manejar errores y excepciones
- Retornar respuestas JSON para AJAX

**Métodos Principales:**
```
index()                 → Vista principal del cronograma
getEventos()           → API endpoint para obtener eventos (AJAX)
serviciosPorFecha()    → Obtener servicios de una fecha específica
resumenSemanal()       → Obtener resumen de la semana
actualizarEstado()     → Actualizar estado de un servicio
```

---

### 4.2 Modelo: CronogramaModel.php

**Responsabilidades:**
- Ejecutar consultas SQL
- Formatear datos para la vista
- Aplicar lógica de negocio
- Gestionar transacciones de BD

**Métodos Principales:**
```
getEstadisticas()              → Obtener contadores del sistema
getProximosServicios($limit)   → Obtener próximos N servicios
getEventosCalendario($start, $end) → Obtener eventos para calendario
getServiciosPorFecha($fecha)   → Filtrar servicios por fecha
getResumenSemanal()            → Resumen de servicios de la semana
actualizarEstadoServicio($id, $estado) → Actualizar estado
```

---

### 4.3 Vista: cronograma/index.php

**Responsabilidades:**
- Renderizar interfaz de usuario
- Inicializar FullCalendar
- Manejar eventos de usuario
- Realizar peticiones AJAX
- Actualizar DOM dinámicamente

**Componentes de la Vista:**
```
1. Header con título y estadísticas
2. Calendario FullCalendar (vista principal)
3. Panel lateral con próximos servicios
4. Modal de detalles de evento
5. Scripts JavaScript para interactividad
```

---

## 5. CÓDIGO IMPLEMENTADO

### 5.1 Configuración de Rutas

```php
// app/Config/Routes.php
$routes->group('cronograma', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Cronograma::index');
    $routes->get('eventos', 'Cronograma::getEventos');
    $routes->get('servicios/(:any)', 'Cronograma::serviciosPorFecha/$1');
    $routes->get('resumen-semanal', 'Cronograma::resumenSemanal');
    $routes->post('actualizar-estado', 'Cronograma::actualizarEstado');
});
```

---

### 5.2 Métodos Adicionales del Modelo

```php
// Obtener estadísticas del sistema
public function getEstadisticas()
{
    $servicios = $this->db->table('servicioscontratados')->countAll();
    $equipos = $this->db->table('equipos')->countAll();
    $tecnicos = $this->db->table('usuarios')
        ->where('tipo_usuario', 'trabajador')
        ->countAllResults();
    
    return [
        'servicios_count' => $servicios,
        'equipos' => $equipos,
        'tecnicos' => $tecnicos
    ];
}

// Obtener próximos servicios
public function getProximosServicios($limit = 10)
{
    return $this->db->table('servicioscontratados sc')
        ->select('sc.*, s.servicio, eq.estadoservicio')
        ->join('servicios s', 's.idservicio = sc.idservicio', 'left')
        ->join('equipos eq', 'eq.idserviciocontratado = sc.idserviciocontratado', 'left')
        ->where('sc.fechahoraservicio >=', date('Y-m-d H:i:s'))
        ->orderBy('sc.fechahoraservicio', 'ASC')
        ->limit($limit)
        ->get()
        ->getResult();
}

// Actualizar estado de servicio
public function actualizarEstadoServicio($id, $estado)
{
    return $this->db->table('equipos')
        ->where('idserviciocontratado', $id)
        ->update(['estadoservicio' => $estado]);
}
```

---

## 📊 DIAGRAMA DE FLUJO COMPLETO

```
┌─────────────────────────────────────────────────────────────────┐
│                    FLUJO COMPLETO CRONOGRAMA                     │
└─────────────────────────────────────────────────────────────────┘

[Usuario] → [Accede /cronograma]
    ↓
[Routes.php] → [Cronograma::index()]
    ↓
[Controlador] → [Inicializa CronogramaModel]
    ↓
[Modelo] → [getEstadisticas() + getProximosServicios()]
    ↓
[Controlador] → [Renderiza vista con datos]
    ↓
[Vista] → [Carga FullCalendar.js]
    ↓
[FullCalendar] → [Petición AJAX GET /cronograma/eventos?start=X&end=Y]
    ↓
[Controlador] → [getEventos()]
    ↓
[Modelo] → [getEventosCalendario($start, $end)]
    ↓
[SQL] → [SELECT con múltiples JOINs]
    ↓
[Modelo] → [Formatea datos para FullCalendar]
    ↓
[Controlador] → [Retorna JSON]
    ↓
[FullCalendar] → [Renderiza eventos en calendario]
    ↓
[Usuario] → [Click en evento]
    ↓
[JavaScript] → [Muestra modal con detalles]
    ↓
[Usuario] → [Cambia estado]
    ↓
[JavaScript] → [POST /cronograma/actualizar-estado]
    ↓
[Controlador] → [actualizarEstado()]
    ↓
[Modelo] → [UPDATE equipos SET estadoservicio]
    ↓
[Controlador] → [Retorna JSON success]
    ↓
[JavaScript] → [Recarga eventos del calendario]
    ↓
[FullCalendar] → [Actualiza vista]
```

---

## ✅ RESUMEN DE OPERACIONES

### Operaciones Principales:
1. **Carga Inicial**: Renderizar vista con estadísticas
2. **Obtención de Eventos**: Consulta SQL + formateo JSON
3. **Visualización**: FullCalendar renderiza eventos
4. **Interacción**: Click en evento muestra detalles
5. **Actualización**: Cambio de estado vía AJAX
6. **Recarga**: Calendario se actualiza automáticamente

### Flujo de Datos:
```
Base de Datos → Modelo → Controlador → JSON → Vista → FullCalendar → Usuario
```

### Tecnologías por Capa:
- **Presentación**: HTML, CSS, Bootstrap, FullCalendar.js
- **Lógica**: JavaScript (AJAX), PHP (CodeIgniter 4)
- **Datos**: MySQL, Query Builder de CodeIgniter

---

**Fecha de Documentación**: 30 de Octubre, 2025  
**Versión del Sistema**: ISHUME 2.0  
**Autor**: ISHUME Development Team
