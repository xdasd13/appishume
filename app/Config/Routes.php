<?php

use App\Controllers\ControlPagoController;
use App\Controllers\Equipos;
use App\Controllers\EntregasController;
use App\Controllers\Servicios;
use App\Controllers\InventarioController;

// Rutas de autenticación
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('auth/authenticate', 'AuthController::authenticate');
$routes->get('auth/logout', 'AuthController::logout');
$routes->get('auth/check-session', 'AuthController::checkSession');

// Ruta principal después del login
$routes->get('welcome', 'Home::index', ['filter' => 'auth']);
$routes->get('dashboard', 'AuthController::dashboard', ['filter' => 'auth']);

// ==================== RUTAS DE MENSAJERÍA (TODOS LOS USUARIOS AUTENTICADOS) ====================
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Páginas principales de mensajería
    $routes->get('mensajeria', 'MensajeriaController::index');
    $routes->get('mensajeria/enviar', 'MensajeriaController::enviar');
    $routes->get('mensajeria/conversacion/(:num)', 'MensajeriaController::conversacion/$1');
    $routes->get('mensajeria/configuracion', 'MensajeriaController::configuracion');
    
    // APIs AJAX para mensajería
    $routes->post('mensajeria/procesarEnvio', 'MensajeriaController::procesarEnvio');
    $routes->post('mensajeria/eliminarMensaje', 'MensajeriaController::eliminarMensaje');
    $routes->get('mensajeria/buscarUsuarios', 'MensajeriaController::buscarUsuarios');
    $routes->get('mensajeria/getConversaciones', 'MensajeriaController::getConversaciones');
    $routes->get('mensajeria/getMensajesConversacion/(:num)', 'MensajeriaController::getMensajesConversacion/$1');
    
    // APIs AJAX para notificaciones
    $routes->get('mensajeria/getMensajesNoLeidos', 'MensajeriaController::getMensajesNoLeidos');
    $routes->get('mensajeria/getNotificacionesNoLeidas', 'MensajeriaController::getNotificacionesNoLeidas');
    $routes->get('mensajeria/getNotificacionesRecientes', 'MensajeriaController::getNotificacionesRecientes');
    $routes->post('mensajeria/marcarNotificacionLeida', 'MensajeriaController::marcarNotificacionLeida');
    $routes->post('mensajeria/marcarTodasNotificacionesLeidas', 'MensajeriaController::marcarTodasNotificacionesLeidas');
    $routes->post('mensajeria/actualizarConfiguracion', 'MensajeriaController::actualizarConfiguracion');
});



// Rutas SIMPLES para usuarios (sin autenticación por ahora)
$routes->get('usuarios-simple', 'UsuariosControllerSimple::index');
$routes->get('usuarios-simple/crear', 'UsuariosControllerSimple::crear');
$routes->post('usuarios-simple/guardar', 'UsuariosControllerSimple::guardar');
$routes->delete('usuarios-simple/eliminar/(:num)', 'UsuariosControllerSimple::eliminar/$1');

// ==================== RUTAS ADMINISTRATIVAS (SOLO ADMINISTRADORES) ====================
$routes->group('', ['filter' => 'admin'], function($routes) {
    // Gestión de trabajadores
    $routes->get('auth/crear-trabajador', 'AuthController::crearTrabajador');
    $routes->post('auth/crear-trabajador', 'AuthController::crearTrabajador');
    
    // Gestión completa de usuarios (CRUD)
    $routes->get('usuarios', 'UsuariosController::index');
    $routes->get('usuarios/crear/(:any)', 'UsuariosController::crear/$1');
    $routes->get('usuarios/crear', 'UsuariosController::crear');
    $routes->post('usuarios/guardar', 'UsuariosController::guardar');
    $routes->get('usuarios/editar/(:num)', 'UsuariosController::editar/$1');
    $routes->post('usuarios/actualizar/(:num)', 'UsuariosController::actualizar/$1');
    $routes->get('usuarios/obtener-persona/(:num)', 'UsuariosController::obtenerPersona/$1');
    $routes->post('usuarios/eliminar/(:num)', 'UsuariosController::eliminar/$1');
    $routes->post('usuarios/reactivar/(:num)', 'UsuariosController::reactivar/$1');
    $routes->delete('usuarios/eliminar-permanente/(:num)', 'UsuariosController::eliminarPermanente/$1');
    $routes->get('usuarios/personas-sin-usuario', 'UsuariosController::getPersonasSinUsuario');
    
    // Validación RENIEC (solo admins pueden validar DNIs)
    $routes->post('usuarios/ajax-check-dni', 'UsuariosController::ajaxCheckDni');
    $routes->get('usuarios/reniec-stats', 'UsuariosController::reniecStats');
    
    // Validación de teléfonos (solo admins pueden validar teléfonos)
    $routes->post('usuarios/validarTelefono', 'UsuariosController::validarTelefono');
    $routes->post('usuarios/infoTelefono', 'UsuariosController::infoTelefono');
    $routes->get('usuarios/testValidacion', 'UsuariosController::testValidacion');
    
    // Control de Pagos (SOLO ADMINISTRADORES)
    $routes->get('controlpagos', [ControlPagoController::class, 'index']);
    $routes->get('controlpagos/crear', [ControlPagoController::class, 'crear']);
    $routes->post('controlpagos/guardar', [ControlPagoController::class, 'guardar']);
    $routes->get('controlpagos/ver/(:num)', [ControlPagoController::class, 'ver']);
    $routes->get('controlpagos/por-contrato/(:num)', [ControlPagoController::class, 'porContrato']);
    $routes->get('controlpagos/infoContrato/(:num)', [ControlPagoController::class, 'infoContrato']);
    $routes->get('controlpagos/descargarComprobante/(:num)', [ControlPagoController::class, 'descargarComprobante']);
    $routes->get('controlpagos/generarVoucher/(:num)', [ControlPagoController::class, 'generarVoucher']);
});

// ==================== RUTAS PARA TRABAJADORES Y SUPERVISORES ====================
$routes->group('', ['filter' => 'trabajador'], function($routes) {
    // Dashboard específico para trabajadores
    $routes->get('trabajador/dashboard', 'AuthController::trabajadorDashboard');
    $routes->post('auth/actualizar-estado', 'AuthController::actualizarEstado');
    
    // Gestión de equipos (trabajadores pueden ver y actualizar estados)
    $routes->get('equipos', 'Equipos::index');
    $routes->get('equipos/asignar/(:num)', 'Equipos::asignar/$1');
    $routes->get('equipos/editar/(:num)', 'Equipos::editar/$1');
    $routes->get('equipos/por-servicio/(:num)', 'Equipos::porServicio/$1');
    $routes->get('equipos/porServicio/(:num)', 'Equipos::porServicio/$1');
    $routes->get('equipos/por-usuario/(:num)', 'Equipos::porUsuario/$1');
    $routes->post('equipos/actualizar-estado', 'Equipos::actualizarEstado');
    
    // Cronograma (trabajadores pueden ver sus asignaciones)
    $routes->get('cronograma', 'Cronograma::index');
    $routes->get('cronograma/proyectos', 'Cronograma::proyectos');
    $routes->get('cronograma/debug-proyectos', 'Cronograma::debugProyectos'); // Ruta temporal de debug
    $routes->get('proyectos', 'Cronograma::todosLosProyectos');
    $routes->get('cronograma/proyecto/(:num)', 'Cronograma::verProyecto/$1');
    $routes->get('cronograma/eventos', 'Cronograma::getEventos');
    $routes->get('cronograma/servicios-fecha/(:segment)', 'Cronograma::serviciosPorFecha/$1');
});

// ==================== RUTAS PÚBLICAS (CON AUTENTICACIÓN BÁSICA) ====================
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Ruta principal después del login
    $routes->get('Home', 'Home::index');
    
    // Entregas (requiere autenticación pero no rol específico)
    $routes->get('entregas', 'EntregasController::index');
    $routes->get('entregas/crear', 'EntregasController::crear');
    $routes->post('entregas/guardar', 'EntregasController::guardar');
    $routes->get('entregas/editar/(:num)', 'EntregasController::editar/$1');
    $routes->post('entregas/actualizar/(:num)', 'EntregasController::actualizar/$1');
    $routes->get('entregas/eliminar/(:num)', 'EntregasController::eliminar/$1');
    $routes->get('entregas/ver/(:num)', 'EntregasController::ver/$1');
    $routes->get('entregas/obtenerServiciosPorContrato/(:num)', 'EntregasController::obtenerServiciosPorContrato/$1');
    $routes->get('entregas/historial', 'EntregasController::historial');
    $routes->get('entregas/imprimir/(:num)', 'EntregasController::imprimir/$1');
});

// ==================== RUTAS ADMINISTRATIVAS ADICIONALES ====================
$routes->group('', ['filter' => 'admin'], function($routes) {
    // Gestión avanzada de equipos (solo admins pueden crear/modificar equipos)
    $routes->post('equipos/guardar', 'Equipos::guardar');
    $routes->post('equipos/saveEquipo', 'Equipos::guardar'); // Alias para compatibilidad
    $routes->post('equipos/actualizar', 'Equipos::actualizar');
    $routes->post('equipos/verificar-disponibilidad', 'Equipos::verificarDisponibilidad');
    
    // APIs administrativas de cronograma
    $routes->post('cronograma/actualizar-estado', 'Cronograma::actualizarEstado');
    $routes->get('cronograma/resumen-semanal', 'Cronograma::resumenSemanal');
    $routes->get('cronograma/proyectos-estado/(:segment)', 'Cronograma::proyectosPorEstado/$1');
    
    // Inventario (solo administradores)


// Rutas del Inventario
$routes->group('inventario', function($routes) {
    $routes->get('/', [InventarioController::class, 'index']);
    $routes->get('crear', [InventarioController::class, 'create']);
    $routes->post('guardar', [InventarioController::class, 'store']);
    $routes->get('editar/(:num)', [InventarioController::class, 'edit']);
    $routes->post('actualizar/(:num)', [InventarioController::class, 'update']);
    $routes->delete('eliminar/(:num)', [InventarioController::class, 'delete']);
    $routes->get('buscar', [InventarioController::class, 'buscar']);
    $routes->get('ver/(:num)', [InventarioController::class, 'ver']);
    $routes->get('estadisticas', [InventarioController::class, 'estadisticas']);
});
    
});


// ==================== RUTAS PÚBLICAS DE SERVICIOS ====================
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Servicios (requiere autenticación básica)
    $routes->get('servicios/(:num)', 'Servicios::detalle/$1');
    
    // Sistema de Reportes Dinámicos (usuarios autenticados)
    $routes->get('reportes', 'ReportesController::index');
    $routes->post('reportes/generar', 'ReportesController::generar');
    $routes->post('reportes/exportarPDF', 'ReportesController::exportarPDF');
    $routes->post('reportes/exportarExcel', 'ReportesController::exportarExcel');
});