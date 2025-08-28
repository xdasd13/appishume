// Rutas para la gestión de entregas
$routes->get('/entregas', 'EntregasController::index');
$routes->get('/entregas/ver/(:num)', 'EntregasController::ver/$1');
$routes->get('/entregas/crear', 'EntregasController::crear');
$routes->post('/entregas/guardar', 'EntregasController::guardar');
$routes->get('/entregas/editar/(:num)', 'EntregasController::editar/$1');
$routes->post('/entregas/actualizar/(:num)', 'EntregasController::actualizar/$1');
$routes->get('/entregas/eliminar/(:num)', 'EntregasController::eliminar/$1');

<<<<<<< HEAD
// Rutas de Control de Pagos:
$routes->get('/controlpagos', 'ControlPagoController::index');
$routes->get('/controlpagos/crear', 'ControlPagoController::crear');
$routes->post('/controlpagos/guardar', 'ControlPagoController::guardar');
=======
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
<<<<<<< HEAD
=======
>>>>>>> 0e94c8b13293bb5e8ddd1916005e8f1e7443cf19
>>>>>>> 05bdfaf5a94b95d8439a50bfb9388a9a7943077f
