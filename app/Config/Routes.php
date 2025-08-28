<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/personas', 'PersonaController::index');
$routes->get('/personas/crear', 'PersonaController::crear');

// Rutas de Control de Pagos:
$routes->get('/controlpagos', 'ControlPagoController::index');
$routes->get('/controlpagos/crear', 'ControlPagoController::crear');
$routes->post('/controlpagos/guardar', 'ControlPagoController::guardar');