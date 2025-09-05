<?php

use App\Controllers\ControlPagoController;
use App\Controllers\Equipos;
use App\Controllers\EntregasController;
use App\Controllers\Servicios;

$routes->get('/', 'Home::index');

// Rutas para la gestión de entregas
$routes->get('/entregas', 'EntregasController::index');
$routes->get('/entregas/ver/(:num)', 'EntregasController::ver/$1');
$routes->get('/entregas/crear', 'EntregasController::crear');
$routes->post('/entregas/guardar', 'EntregasController::guardar');
$routes->get('/entregas/editar/(:num)', 'EntregasController::editar/$1');
$routes->post('/entregas/actualizar/(:num)', 'EntregasController::actualizar/$1');
$routes->get('/entregas/eliminar/(:num)', 'EntregasController::eliminar/$1');
$routes->get('/entregas/pendientes', 'EntregasController::pendientes');

// Rutas de Control de Pagos:
$routes->get('/controlpagos', 'ControlPagoController::index');
$routes->get('/controlpagos/crear', 'ControlPagoController::crear');
$routes->post('/controlpagos/guardar', 'ControlPagoController::guardar');
$routes->get('/controlpagos/ver/(:num)', 'ControlPagoController::ver/$1');


// ==================== RUTAS PARA GESTIÓN DE EQUIPOS ====================
// Rutas para equipos
$routes->get('equipos', 'Equipos::index');
$routes->get('equipos/asignar/(:num)', 'Equipos::asignar/$1');
$routes->post('equipos/guardar', 'Equipos::guardar');
$routes->get('equipos/editar/(:num)', 'Equipos::editar/$1');
$routes->post('equipos/actualizar', 'Equipos::actualizar');

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

// Rutas para servicios
$routes->get('servicios/(:num)', 'Servicios::detalle/$1');