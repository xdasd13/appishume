<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/personas', 'PersonaController::index');
$routes->get('/personas/crear', 'PersonaController::crear');

// ==================== RUTAS PARA GESTIÓN DE EQUIPOS ====================
// Rutas para equipos
$routes->get('equipos', 'Equipos::index');
$routes->get('equipos/asignar/(:num)', 'Equipos::asignar/$1');
$routes->post('equipos/guardar', 'Equipos::guardar');
$routes->get('equipos/editar/(:num)', 'Equipos::editar/$1');
$routes->post('equipos/actualizar', 'Equipos::actualizar');
$routes->get('equipos/por-servicio/(:num)', 'Equipos::por_servicio/$1');
$routes->get('equipos/por-usuario/(:num)', 'Equipos::por_usuario/$1');

// Rutas para servicios
$routes->get('servicios/(:num)', 'Servicios::detalle/$1');