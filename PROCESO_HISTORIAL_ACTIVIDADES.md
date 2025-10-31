# MÓDULO DE HISTORIAL DE ACTIVIDADES - DESCRIPCIÓN DEL PROCESO
## Sistema ISHUME - Auditoría y Trazabilidad del Tablero Kanban

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
El módulo de **Historial de Actividades** registra automáticamente todos los cambios realizados en el tablero Kanban de equipos:
- Registrar cambios de estado de equipos
- Auditar acciones de usuarios (crear, cambiar_estado, reasignar)
- Consultar historial completo con filtros
- Rastrear quién, cuándo y qué cambió
- Generar reportes de actividad

### Tecnologías Utilizadas
- **Backend**: PHP 8.x con CodeIgniter 4
- **Base de Datos**: MySQL 8.x (tabla auditoria_kanban)
- **Frontend**: Bootstrap 5.3.8, JavaScript ES6
- **AJAX**: Fetch API
- **Notificaciones**: SweetAlert2

---

## 2. ARQUITECTURA DEL MÓDULO

### Patrón: MVC + Helper Library

```
┌────────────────────────────────────────────────────────┐
│         ARQUITECTURA HISTORIAL DE ACTIVIDADES          │
├────────────────────────────────────────────────────────┤
│                                                         │
│  ┌──────────┐    ┌──────────┐    ┌──────────────┐    │
│  │  VISTA   │◄───┤CONTROLADOR│◄───┤    MODELO    │    │
│  │historial/│    │Historial  │    │AuditoriaKanban│   │
│  │index.php │    │   .php    │    │   Model.php  │    │
│  └──────────┘    └──────────┘    └──────────────┘    │
│                                                         │
│              ┌─────────────────┐                       │
│              │ HELPER LIBRARY  │                       │
│              │HistorialHelper  │◄──────────┐           │
│              │     .php        │           │           │
│              └─────────────────┘           │           │
│                      │              ┌──────────┐       │
│                      │              │ EQUIPOS  │       │
│                      └──────────────│Controller│       │
│                                     └──────────┘       │
└────────────────────────────────────────────────────────┘
```

### Estructura de Archivos

```
app/
├── Controllers/
│   ├── Historial.php
│   └── Equipos.php
├── Models/
│   ├── AuditoriaKanbanModel.php
│   └── HistorialActividadesModel.php
├── Libraries/
│   └── HistorialHelper.php
└── Views/
    └── historial/
        └── index.php
```

---

## 3. FLUJO DE EJECUCIÓN DETALLADO

### 3.1 REGISTRO AUTOMÁTICO DE ACTIVIDAD

**Secuencia:**
```
Usuario arrastra tarjeta en Kanban
    ↓
JavaScript captura drag & drop
    ↓
Validación de transición
    ↓
SweetAlert confirmación
    ↓
POST /equipos/actualizar-estado
    ↓
Equipos::actualizarEstado()
    ↓
EquipoModel::cambiarEstadoConAuditoria()
    ↓
AuditoriaKanbanModel::registrarCambio()
    ↓
INSERT en auditoria_kanban
    ↓
Success response
```

**Código del Modelo de Equipos:**
```php
public function cambiarEstadoConAuditoria(
    int $idequipo, 
    string $nuevoEstado, 
    int $idusuario
): bool {
    $db = \Config\Database::connect();
    $db->transStart();
    
    try {
        // 1. Obtener estado anterior
        $equipoActual = $this->find($idequipo);
        $estadoAnterior = $equipoActual['estadoservicio'] ?? 'Pendiente';
        
        // 2. Actualizar estado
        $actualizado = $this->update($idequipo, [
            'estadoservicio' => $nuevoEstado
        ]);
        
        // 3. Registrar en auditoría
        $auditoriaModel = new \App\Models\AuditoriaKanbanModel();
        $auditoriaModel->registrarCambio(
            $idequipo,
            $idusuario,
            'cambiar_estado',
            $estadoAnterior,
            $nuevoEstado
        );
        
        $db->transComplete();
        return $db->transStatus() !== false;
        
    } catch (\Exception $e) {
        $db->transRollback();
        return false;
    }
}
```

---

### 3.2 REGISTRO EN TABLA DE AUDITORÍA

**Código del Modelo:**
```php
public function registrarCambio(
    int $idequipo, 
    int $idusuario, 
    string $accion, 
    ?string $estadoAnterior = null, 
    ?string $estadoNuevo = null
): bool {
    try {
        $db = \Config\Database::connect();
        $fechaActual = $db->query("SELECT NOW() as fecha")->getRow()->fecha;
        
        $data = [
            'idequipo' => $idequipo,
            'idusuario' => $idusuario,
            'accion' => $accion,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'fecha' => $fechaActual
        ];
        
        return $this->insert($data) !== false;
        
    } catch (\Exception $e) {
        log_message('error', "Error auditoría: " . $e->getMessage());
        return false;
    }
}
```

**Estructura de Tabla:**
```sql
CREATE TABLE auditoria_kanban (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idequipo INT NOT NULL,
    idusuario INT NOT NULL,
    accion VARCHAR(50) NOT NULL,
    estado_anterior VARCHAR(50),
    estado_nuevo VARCHAR(50),
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idequipo) REFERENCES equipos(idequipo),
    FOREIGN KEY (idusuario) REFERENCES usuarios(idusuario),
    INDEX idx_fecha (fecha),
    INDEX idx_usuario (idusuario)
);
```

---

### 3.3 VISUALIZACIÓN DEL HISTORIAL

**Secuencia:**
```
GET /historial
    ↓
Historial::index()
    ↓
obtenerTodoElHistorial($filtro)
    ↓
SQL con 8 JOINs
    ↓
obtenerUsuariosActivos()
    ↓
Renderiza vista
```

**Código del Controlador:**
```php
public function index(): string {        
    $filtroUsuario = $this->request->getGet('usuario') ?? 'todos';
    $historial = $this->auditoriaModel->obtenerTodoElHistorial($filtroUsuario);
    $usuarios = $this->auditoriaModel->obtenerUsuariosActivos();

    $data = [
        'title' => 'Historial de Actividades',
        'historial' => $historial,
        'usuarios' => $usuarios,
        'filtro_usuario' => $filtroUsuario,
        'header' => view('Layouts/header'),
        'footer' => view('Layouts/footer')
    ];

    return view('historial/index', $data);
}
```

---

### 3.4 CONSULTA SQL COMPLEJA

**Código del Modelo:**
```php
public function obtenerTodoElHistorial(
    string $filtroUsuario = 'todos', 
    int $limite = 100
): array {
    $builder = $this->db->table('auditoria_kanban a');
    
    $builder->select('
        a.id, a.fecha, a.accion, a.estado_anterior, a.estado_nuevo,
        CONCAT(p.nombres, " ", p.apellidos) as usuario_nombre,
        u.nombreusuario,
        s.servicio,
        eq.descripcion as equipo_descripcion,
        cat.categoria,
        CASE 
            WHEN cl_p.idpersona IS NOT NULL 
                THEN CONCAT(cl_p.apellidos, ", ", cl_p.nombres)
            WHEN emp.idempresa IS NOT NULL 
                THEN emp.razonsocial
            ELSE "Cliente no identificado"
        END as cliente_nombre
    ');
    
    // 8 JOINs para información completa
    $builder->join('usuarios u', 'a.idusuario = u.idusuario');
    $builder->join('personas p', 'u.idpersona = p.idpersona');
    $builder->join('equipos eq', 'a.idequipo = eq.idequipo');
    $builder->join('servicioscontratados sc', 
                   'eq.idserviciocontratado = sc.idserviciocontratado');
    $builder->join('servicios s', 'sc.idservicio = s.idservicio');
    $builder->join('categorias cat', 's.idcategoria = cat.idcategoria');
    $builder->join('cotizaciones cot', 'sc.idcotizacion = cot.idcotizacion');
    $builder->join('clientes cl', 'cot.idcliente = cl.idcliente');
    $builder->join('personas cl_p', 'cl.idpersona = cl_p.idpersona', 'left');
    $builder->join('empresas emp', 'cl.idempresa = emp.idempresa', 'left');

    if ($filtroUsuario !== 'todos') {
        $builder->where('a.idusuario', $filtroUsuario);
    }

    $builder->orderBy('a.fecha', 'DESC');
    $builder->limit($limite);
    
    return $builder->get()->getResult();
}
```

---

### 3.5 BÚSQUEDA FILTRADA CON AJAX

**Código del Endpoint:**
```php
public function buscarHistorial() {
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(400)->setJSON([
            'success' => false,
            'mensaje' => 'Petición no válida'
        ]);
    }

    try {
        $filtroUsuario = $this->request->getPost('usuario') ?? 'todos';
        $historial = $this->auditoriaModel->obtenerTodoElHistorial($filtroUsuario);
        $historialFormateado = $this->formatearHistorial($historial);

        return $this->response->setJSON([
            'success' => true,
            'historial' => $historialFormateado,
            'total' => count($historialFormateado)
        ]);
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'mensaje' => 'Error al buscar historial'
        ]);
    }
}
```

**JavaScript de Búsqueda:**
```javascript
function buscarHistorial() {
    const loading = document.getElementById('loading');
    const tabla = document.getElementById('tabla-container');
    const filtro = document.getElementById('filtroUsuario').value;
    
    loading.style.display = 'block';
    tabla.style.opacity = '0.5';
    
    fetch('<?= base_url('historial/buscar') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            usuario: filtro,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        loading.style.display = 'none';
        tabla.style.opacity = '1';
        if (data.success) {
            actualizarTabla(data.historial);
        }
    });
}
```

---

## 4. COMPONENTES DEL SISTEMA

### 4.1 Controlador: Historial.php

**Métodos:**
- `index()` - Vista principal
- `buscarHistorial()` - Endpoint AJAX
- `formatearHistorial()` - Formatear JSON
- `obtenerNombreDia()` - Día de semana
- `obtenerTextoAccion()` - Texto descriptivo

### 4.2 Modelo: AuditoriaKanbanModel.php

**Métodos:**
- `registrarCambio()` - Insertar auditoría
- `obtenerTodoElHistorial()` - Consulta completa
- `obtenerUsuariosActivos()` - Lista usuarios
- `obtenerHistorialPorEquipo()` - Por equipo
- `obtenerHistorialPorFecha()` - Por rango

### 4.3 Helper: HistorialHelper.php

**Métodos:**
- `registrarCambioEstado()` - Alto nivel
- `registrarAsignacion()` - Asignaciones
- `registrarCompletacion()` - Completaciones
- `generarDescripcionCambioEstado()` - Descripciones

---

## 5. INTEGRACIÓN CON EQUIPOS

### Flujo Completo de Integración

```
Tablero Kanban (Vista)
    ↓
Drag & Drop evento
    ↓
Equipos::actualizarEstado() (Controlador)
    ↓
EquipoModel::cambiarEstadoConAuditoria() (Modelo)
    ↓
├─ UPDATE equipos (cambio de estado)
└─ AuditoriaKanbanModel::registrarCambio() (auditoría)
    ↓
Historial::index() (Visualización)
```

### Código de Integración en Equipos

```php
// app/Controllers/Equipos.php
public function actualizarEstado(): \CodeIgniter\HTTP\ResponseInterface {
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(400)
            ->setJSON(['success' => false]);
    }

    $input = json_decode($this->request->getBody(), true);
    $equipoId = (int)($input['id'] ?? 0);
    $nuevoEstado = $input['estado'] ?? '';

    try {
        $usuarioId = session()->get('idusuario') 
                  ?? session()->get('usuario_id');
        
        $actualizado = $this->equipoModel->cambiarEstadoConAuditoria(
            $equipoId, 
            $nuevoEstado, 
            $usuarioId
        );
        
        return $this->response->setJSON([
            'success' => $actualizado,
            'message' => $actualizado 
                ? 'Estado actualizado correctamente'
                : 'Error al actualizar estado'
        ]);
        
    } catch (\Exception $e) {
        log_message('error', 'Error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error interno'
        ]);
    }
}
```

---

## 📊 DIAGRAMA DE FLUJO COMPLETO

```
[Usuario] → [Drag & Drop en Kanban]
    ↓
[JavaScript] → [Validación transición]
    ↓
[SweetAlert] → [Confirmación usuario]
    ↓
[AJAX POST] → [/equipos/actualizar-estado]
    ↓
[Equipos::actualizarEstado()]
    ↓
[EquipoModel::cambiarEstadoConAuditoria()]
    ↓
[Transacción DB]
    ├─ UPDATE equipos
    └─ INSERT auditoria_kanban
    ↓
[Response JSON] → [Success]
    ↓
[Usuario] → [GET /historial]
    ↓
[Historial::index()]
    ↓
[AuditoriaKanbanModel::obtenerTodoElHistorial()]
    ↓
[SQL con 8 JOINs]
    ↓
[Vista con tabla HTML]
    ↓
[Usuario] → [Filtrar por usuario]
    ↓
[AJAX POST] → [/historial/buscar]
    ↓
[Historial::buscarHistorial()]
    ↓
[Response JSON filtrado]
    ↓
[JavaScript actualiza tabla]
```

---

## ✅ RESUMEN

### Operaciones Principales:
1. **Registro Automático**: Cada cambio se audita
2. **Transacciones**: UPDATE + INSERT atómico
3. **Consultas Complejas**: 8 JOINs para info completa
4. **Búsqueda Filtrada**: AJAX en tiempo real
5. **Visualización**: Tabla responsiva con detalles

### Flujo de Datos:
```
Kanban → Equipos → EquipoModel → AuditoriaModel → DB
DB → AuditoriaModel → Historial → Vista → Usuario
```

---

**Fecha**: 30 de Octubre, 2025  
**Versión**: ISHUME 2.0  
**Autor**: ISHUME Development Team
