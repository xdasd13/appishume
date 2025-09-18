<?php

use App\Controllers\ControlPagoController;
use App\Controllers\Equipos;
use App\Controllers\EntregasController;
use App\Controllers\Servicios;

// Rutas de autenticación
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('auth/authenticate', 'AuthController::authenticate');
$routes->get('auth/logout', 'AuthController::logout');
$routes->get('auth/check-session', 'AuthController::checkSession');

// Ruta principal después del login
$routes->get('welcome', 'Home::index', ['filter' => 'auth']);
$routes->get('test-usuarios', 'AuthController::testUsuarios');
$routes->get('test_debug', 'AuthController::testDebug');
$routes->get('test_simple', 'AuthController::testSimple');




// Rutas SIMPLES para usuarios (sin autenticación por ahora)
$routes->get('usuarios-simple', 'UsuariosControllerSimple::index');
$routes->get('usuarios-simple/crear', 'UsuariosControllerSimple::crear');
$routes->post('usuarios-simple/guardar', 'UsuariosControllerSimple::guardar');
$routes->delete('usuarios-simple/eliminar/(:num)', 'UsuariosControllerSimple::eliminar/$1');

// Rutas protegidas para administrador
$routes->group('', ['filter' => 'auth:admin'], function($routes) {
    $routes->get('auth/crear-trabajador', 'AuthController::crearTrabajador');
    $routes->post('auth/crear-trabajador', 'AuthController::crearTrabajador');
    
    // Rutas para gestión de usuarios
    $routes->get('usuarios', 'UsuariosController::index');
    // En app/Config/Routes.php
    $routes->get('usuarios/crear/(:any)', 'UsuariosController::crear/$1');
    $routes->get('usuarios/crear', 'UsuariosController::crear'); // Ruta por defecto
    $routes->post('usuarios/guardar', 'UsuariosController::guardar');
    $routes->get('usuarios/editar/(:num)', 'UsuariosController::editar/$1');
    $routes->post('usuarios/actualizar/(:num)', 'UsuariosController::actualizar/$1');
    $routes->get('usuarios/obtener-persona/(:num)', 'UsuariosController::obtenerPersona/$1');
    $routes->post('usuarios/eliminar/(:num)', 'UsuariosController::eliminar/$1');
    $routes->get('usuarios/personas-sin-usuario', 'UsuariosController::getPersonasSinUsuario');
});

// Rutas protegidas para trabajador
$routes->group('', ['filter' => 'auth:trabajador'], function($routes) {
    $routes->get('trabajador/dashboard', 'AuthController::trabajadorDashboard');
    $routes->post('auth/actualizar-estado', 'AuthController::actualizarEstado');
});

// Ruta original del sistema
$routes->get('home', 'Home::index');

// Rutas para la gestión de entregas
$routes->get('/entregas', 'EntregasController::index');
$routes->get('/entregas/ver/(:num)', 'EntregasController::ver/$1');
$routes->get('/entregas/crear', 'EntregasController::crear');
$routes->post('/entregas/guardar', 'EntregasController::guardar');
$routes->get('/entregas/editar/(:num)', 'EntregasController::editar/$1');
$routes->post('/entregas/actualizar/(:num)', 'EntregasController::actualizar/$1');
$routes->get('/entregas/eliminar/(:num)', 'EntregasController::eliminar/$1');
$routes->get('/entregas/pendientes', 'EntregasController::pendientes');

// Rutas de Control de Pagos: //
$routes->get('controlpagos', [ControlPagoController::class, 'index']);
$routes->get('controlpagos/crear', [ControlPagoController::class, 'crear']);
$routes->post('controlpagos/guardar', [ControlPagoController::class, 'guardar']);
$routes->get('controlpagos/ver/(:num)', [ControlPagoController::class, 'ver']);
$routes->get('controlpagos/por-contrato/(:num)', [ControlPagoController::class, 'porContrato']);
$routes->get('controlpagos/infoContrato/(:num)', [ControlPagoController::class, 'infoContrato']);
$routes->get('controlpagos/descargarComprobante/(:num)', [ControlPagoController::class, 'descargarComprobante']);
$routes->get('controlpagos/generarVoucher/(:num)', [ControlPagoController::class, 'generarVoucher']);

// ==================== RUTAS PARA GESTIÓN DE EQUIPOS ====================
// Rutas para equipos
$routes->get('equipos', 'Equipos::index');
$routes->get('equipos/asignar/(:num)', 'Equipos::asignar/$1');
$routes->post('equipos/guardar', 'Equipos::guardar');
$routes->get('equipos/editar/(:num)', 'Equipos::editar/$1');
$routes->post('equipos/actualizar', 'Equipos::actualizar');
$routes->get('equipos/por-servicio/(:num)', 'Equipos::por_servicio/$1');
$routes->get('equipos/por-usuario/(:num)', 'Equipos::por_usuario/$1');
$routes->post('equipos/verificar-disponibilidad', 'Equipos::verificarDisponibilidad');
$routes->post('equipos/actualizar-estado', 'Equipos::actualizarEstado');

// ==================== RUTAS PARA CRONOGRAMA Y PROYECTOS ====================
// Rutas principales de cronograma
$routes->get('cronograma', 'Cronograma::index');
$routes->get('cronograma/proyectos', 'Cronograma::proyectos');
$routes->get('proyectos', 'Cronograma::todosLosProyectos');
$routes->get('cronograma/proyecto/(:num)', 'Cronograma::verProyecto/$1');

// APIs AJAX para cronograma
$routes->get('cronograma/eventos', 'Cronograma::getEventos');
$routes->get('cronograma/servicios-fecha/(:segment)', 'Cronograma::serviciosPorFecha/$1');
$routes->post('cronograma/actualizar-estado', 'Cronograma::actualizarEstado');
$routes->get('cronograma/resumen-semanal', 'Cronograma::resumenSemanal');
$routes->get('cronograma/proyectos-estado/(:segment)', 'Cronograma::proyectosPorEstado/$1');

// Ruta de prueba para debugging
$routes->get('test-db', 'Cronograma::testDatabase');

// ==================== RUTAS PARA INVENTARIO DE EQUIPOS ====================
// Rutas principales del inventario
$routes->get('inventario', 'InventarioController::index');
$routes->get('inventario/crear', 'InventarioController::create');
$routes->post('inventario/guardar', 'InventarioController::store');
$routes->get('inventario/editar/(:num)', 'InventarioController::edit/$1');
$routes->post('inventario/actualizar/(:num)', 'InventarioController::update/$1');
$routes->delete('inventario/eliminar/(:num)', 'InventarioController::delete/$1');

// APIs AJAX para inventario
$routes->get('inventario/ver/(:num)', 'InventarioController::ver/$1');
$routes->get('inventario/buscar', 'InventarioController::buscar');
$routes->get('inventario/estadisticas', 'InventarioController::estadisticas');

// Rutas para servicios
$routes->get('servicios/(:num)', 'Servicios::detalle/$1');