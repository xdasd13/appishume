<!DOCTYPE html>
<html lang="es">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Ishume - Sistema de Gestión de Proyectos</title>
  <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<style></style>

  <!-- CONTROL DE PAGOS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- FIN CONTROL DE PAGOS -->

  <!-- En el header -->
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
  <!-- Responsive Fixes -->
  <link rel="stylesheet" href="<?= base_url('assets/css/responsive-fixes.css') ?>">
  <!-- Overflow Fixes -->
  <link rel="stylesheet" href="<?= base_url('assets/css/overflow-fixes.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/sweetalert-custom.css') ?>">
  <link rel="icon" href="<?= base_url() . 'assets/img/kaiadmin/favicon.ico' ?>" type="image/x-icon" />

  <!-- Fonts and icons -->
  <script src="<?= base_url() . 'assets/js/plugin/webfont/webfont.min.js' ?>"></script>
  <script>
    WebFont.load({
      google: { families: ["Public Sans:300,400,500,600,700"] },
      custom: {
        families: [
          "Font Awesome 5 Solid",
          "Font Awesome 5 Regular",
          "Font Awesome 5 Brands",
          "simple-line-icons",
        ],
        urls: ["<?= base_url() . 'assets/css/fonts.min.css' ?>"],
      },
      active: function () {
        sessionStorage.fonts = true;
      },
    });
  </script>

  <!-- CSS Files -->
  <link rel="stylesheet" href="<?= base_url() . '/assets/css/bootstrap.min.css' ?>" />
  <link rel="stylesheet" href="<?= base_url() . '/assets/css/plugins.min.css' ?>" />
  <link rel="stylesheet" href="<?= base_url() . '/assets/css/kaiadmin.min.css' ?>" />
  <link rel="stylesheet" href="<?= base_url() . 'assets/css/demo.css' ?>" />
  
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Sistema de Notificaciones -->
  <script src="<?= base_url('js/notificaciones.js') ?>"></script>
  
  <!-- Estilos para notificaciones -->
  <style>
    .notification-item {
      padding: 10px 15px;
      border-bottom: 1px solid #f0f0f0;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .notification-item:hover {
      background-color: #f8f9fa;
    }

    .notification-item:last-child {
      border-bottom: none;
    }

    .notification-item.unread {
      background-color: #e3f2fd;
      border-left: 3px solid #2196f3;
    }

    .notification-icon {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 10px;
      font-size: 0.9em;
    }

    .notification-icon.mensaje {
      background-color: #4e73df;
      color: white;
    }

    .notification-icon.mensaje_importante {
      background-color: #ffc107;
      color: #333;
    }

    .notification-icon.mensaje_urgente {
      background-color: #dc3545;
      color: white;
    }

    .notification-icon.sistema {
      background-color: #6c757d;
      color: white;
    }

    .notification-content {
      flex: 1;
    }

    .notification-title {
      font-weight: bold;
      margin-bottom: 2px;
      font-size: 0.9em;
    }

    .notification-message {
      font-size: 0.8em;
      color: #666;
      margin-bottom: 2px;
    }

    .notification-time {
      font-size: 0.7em;
      color: #999;
    }

    .badge {
      animation: pulse 1s ease-in-out;
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.2); }
      100% { transform: scale(1); }
    }

    /* Toast container */
    .toast-container {
      z-index: 9999;
    }

    /* Scrollbar personalizado para dropdowns */
    .dropdown-menu::-webkit-scrollbar {
      width: 6px;
    }

    .dropdown-menu::-webkit-scrollbar-track {
      background: #f1f1f1;
    }

    .dropdown-menu::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }

    .dropdown-menu::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" data-background-color="dark">
      <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
          <a href="<?= base_url('/welcome') ?>" class="logo">
            <img src="<?= base_url() . 'assets/img/kaiadmin/logo_light.svg' ?>" alt="IShume Logo" class="navbar-brand"
              height="20" />
          </a>
          <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar">
              <i class="gg-menu-right"></i>
            </button>
            <button class="btn btn-toggle sidenav-toggler">
              <i class="gg-menu-left"></i>
            </button>
          </div>
          <button class="topbar-toggler more">
            <i class="gg-more-vertical-alt"></i>
          </button>
        </div>
      </div>
      <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
          <ul class="nav nav-secondary">

            <!-- Dashboard -->
            <li class="nav-item">
              <a href="<?= base_url('welcome') ?>">
                <i class="fas fa-home"></i>
                <p>Dashboard</p>
              </a>
            </li>

            <!-- Mensajería -->
            <li class="nav-item">
              <a href="<?= base_url('mensajeria') ?>">
                <i class="fas fa-comments"></i>
                <p>Mensajería</p>
                <span id="badge-mensajes-sidebar" class="badge bg-danger ms-1" style="display: none; font-size: 0.7em;">0</span>
              </a>
            </li>

            <!-- Separador -->
            <li class="nav-section">
              <span class="sidebar-mini-icon">
                <i class="fa fa-ellipsis-h"></i>
              </span>
              <h4 class="text-section">GESTIÓN PRINCIPAL</h4>
            </li>

            <!-- Clientes -->
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#clientes">
                <i class="fas fa-users"></i>
                <p>Gestión de Clientes</p>
                <span class="caret"></span>
              </a>
              <div class="collapse" id="clientes">
                <ul class="nav nav-collapse">
                  <li>
                    <a href="<?= base_url('/personas') ?>">
                      <span class="sub-item">Personas Naturales</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/empresas') ?>">
                      <span class="sub-item">Empresas</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/clientes') ?>">
                      <span class="sub-item">Lista de Clientes</span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>

            <!-- Servicios -->
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#servicios">
                <i class="fas fa-briefcase"></i>
                <p>Servicios</p>
                <span class="caret"></span>
              </a>
              <div class="collapse" id="servicios">
                <ul class="nav nav-collapse">
                  <li>
                    <a href="<?= base_url('/categorias') ?>">
                      <span class="sub-item">Categorías</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/servicios') ?>">
                      <span class="sub-item">Catálogo de Servicios</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/servicios/precios') ?>">
                      <span class="sub-item">Gestión de Precios</span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>

            <!-- Inventario -->
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#inventario">
                <i class="fa-solid fa-boxes-stacked"></i>
                <p>Inventario</p>
                <span class="caret"></span>
              </a>
              <div class="collapse" id="inventario">
                <ul class="nav nav-collapse">
                  <li>
                    <a href="<?= base_url('/inventario') ?>">
                      <span class="sub-item">Lista Inventario</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/inventario/crear') ?>">
                      <span class="sub-item">Agregar equipo</span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>

            <!-- Separador -->
            <li class="nav-section">
              <span class="sidebar-mini-icon">
                <i class="fa fa-ellipsis-h"></i>
              </span>
              <h4 class="text-section">OPERACIONES</h4>
            </li>

            <!-- Cotizaciones y Contratos -->
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#comercial">
                <i class="fas fa-file-contract"></i>
                <p>Gestión Comercial</p>
                <span class="caret"></span>
              </a>
              <div class="collapse" id="comercial">
                <ul class="nav nav-collapse">
                  <li>
                    <a href="<?= base_url('/cotizaciones/nueva') ?>">
                      <span class="sub-item">Nueva Cotización</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/cotizaciones') ?>">
                      <span class="sub-item">Lista Cotizaciones</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/contratos') ?>">
                      <span class="sub-item">Contratos</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/contratos/pendientes') ?>">
                      <span class="sub-item">Contratos Pendientes</span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>

            <!-- Proyectos en Ejecución -->
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#proyectos">
                <i class="fas fa-project-diagram"></i>
                <p>Proyectos</p>
                <span class="caret"></span>
              </a>
              <div class="collapse" id="proyectos">
                <ul class="nav nav-collapse">
                  <li>
                    <a href="<?= base_url('/cronograma/proyectos') ?>">
                      <span class="sub-item">Proyectos Activos</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/servicios-contratados') ?>">
                      <span class="sub-item">Servicios Contratados</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/equipos') ?>">
                      <span class="sub-item">Equipos de Trabajo</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/cronograma') ?>">
                      <span class="sub-item">Cronograma</span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>

            <!-- Entregas -->
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#entregas">
                <i class="fas fa-truck"></i>
                <p>Entregas</p>
                <span class="caret"></span>
              </a>
              <div class="collapse" id="entregas">
                <ul class="nav nav-collapse">
                  <li>
                    <a href="<?= base_url('/entregas') ?>">
                      <span class="sub-item">Lista de Entregas</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/entregas/crear') ?>">
                      <span class="sub-item">Crear Entrega</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/entregas/historial') ?>">
                      <span class="sub-item">Entregas Completadas</span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>

            <!-- Separador -->
            <li class="nav-section">
              <span class="sidebar-mini-icon">
                <i class="fa fa-ellipsis-h"></i>
              </span>
              <h4 class="text-section">FINANZAS</h4>
            </li>

            <!-- Pagos y Facturación -->
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#finanzas">
                <i class="fas fa-dollar-sign"></i>
                <p>Control Financiero</p>
                <span class="caret"></span>
              </a>
              <div class="collapse" id="finanzas">
                <ul class="nav nav-collapse">
                  <li>
                    <a href="<?= base_url('/controlpagos/crear') ?>">
                      <span class="sub-item">Registrar Pago</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/controlpagos') ?>">
                      <span class="sub-item">Control de Pagos</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/pagos/pendientes') ?>">
                      <span class="sub-item">Pagos Pendientes</span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>


            <!-- Separador -->
            <li class="nav-section">
              <span class="sidebar-mini-icon">
                <i class="fa fa-ellipsis-h"></i>
              </span>
              <h4 class="text-section">CONFIGURACIÓN</h4>
            </li>

            <!-- Configuraciones del Sistema -->
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#configuracion">
                <i class="fas fa-cog"></i>
                <p>Configuración</p>
                <span class="caret"></span>
              </a>
              <div class="collapse" id="configuracion">
                <ul class="nav nav-collapse">
                  <li>
                    <a href="<?= base_url('/usuarios') ?>">
                      <span class="sub-item">Usuarios</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/cargos') ?>">
                      <span class="sub-item">Cargos</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/tipos-contrato') ?>">
                      <span class="sub-item">Tipos de Contrato</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/condiciones') ?>">
                      <span class="sub-item">Condiciones</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/tipos-evento') ?>">
                      <span class="sub-item">Tipos de Evento</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/tipos-pago') ?>">
                      <span class="sub-item">Métodos de Pago</span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>

            <!-- Reportes -->
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#reportes">
                <i class="fas fa-chart-bar"></i>
                <p>Reportes</p>
                <span class="caret"></span>
              </a>
              <div class="collapse" id="reportes">
                <ul class="nav nav-collapse">
                  <li>
                    <a href="<?= base_url('/reportes') ?>">
                      <span class="sub-item">Reportes Dinámicos</span>
                    </a>  
                  </li>
                </ul>
              </div>
            </li>

          </ul>
        </div>
      </div>
    </div>
    <!-- End Sidebar -->

    <div class="main-panel">
      <div class="main-header">
        <div class="main-header-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="<?= base_url('/welcome') ?>" class="logo">
              <img src="<?= base_url() . 'assets/img/kaiadmin/logo_light.svg' ?>" alt="IShume Logo" class="navbar-brand"
                height="20" />
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
        </div>
        <!-- Navbar Header -->
        <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
          <div class="container-fluid">
            <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
              <div class="input-group">
                <div class="input-group-prepend">
                  <button type="submit" class="btn btn-search pe-1">
                    <i class="fa fa-search search-icon"></i>
                  </button>
                </div>
                <input type="text" placeholder="Buscar proyectos, clientes, servicios..." class="form-control" />
              </div>
            </nav>

            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

              <!-- Notificaciones de Mensajes -->
              <li class="nav-item topbar-icon dropdown hidden-caret">
                <a class="nav-link dropdown-toggle" href="#" id="dropdown-mensajes" role="button" data-bs-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <i class="fas fa-envelope"></i>
                  <span id="badge-mensajes" class="notification" style="display: none;">0</span>
                </a>
                <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="dropdown-mensajes" style="min-width: 300px;">
                  <li>
                    <div class="dropdown-title">
                      Mensajes
                    </div>
                  </li>
                  <li>
                    <div id="mensajes-dropdown-content">
                      <div class="text-center p-3">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p class="mb-0 text-muted">Cargando mensajes...</p>
                      </div>
                    </div>
                  </li>
                  <li>
                    <a class="see-all" href="<?= base_url('mensajeria') ?>">Ver todos los mensajes<i
                        class="fa fa-angle-right"></i>
                    </a>
                  </li>
                </ul>
              </li>

              <!-- Notificaciones del Sistema -->
              <li class="nav-item topbar-icon dropdown hidden-caret">
                <a class="nav-link dropdown-toggle" href="#" id="dropdown-notificaciones" role="button" data-bs-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <i class="fas fa-bell"></i>
                  <span id="badge-notificaciones" class="notification" style="display: none;">0</span>
                </a>
                <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="dropdown-notificaciones" style="min-width: 350px;">
                  <li>
                    <div class="dropdown-title">
                      Notificaciones
                    </div>
                  </li>
                  <li>
                    <div id="notificaciones-dropdown-content">
                      <div class="text-center p-3">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p class="mb-0 text-muted">Cargando notificaciones...</p>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="d-flex justify-content-between px-3 py-2">
                      <a href="#" class="btn btn-sm btn-outline-primary" onclick="marcarTodasNotificacionesLeidas()">
                        <i class="fas fa-check-double me-1"></i> Marcar todas como leídas
                      </a>
                      <a href="<?= base_url('mensajeria/configuracion') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-cog me-1"></i> Configurar
                      </a>
                    </div>
                  </li>
                </ul>
              </li>

              <!-- Acciones Rápidas -->
              <li class="nav-item topbar-icon dropdown hidden-caret">
                <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                  <i class="fas fa-plus-circle"></i>
                </a>
                <div class="dropdown-menu quick-actions animated fadeIn">
                  <div class="quick-actions-header">
                    <span class="title mb-1">Acciones Rápidas</span>
                    <span class="subtitle op-7">Crear Nuevo</span>
                  </div>
                  <div class="quick-actions-scroll scrollbar-outer">
                    <div class="quick-actions-items">
                      <div class="row m-0">
                        <a class="col-6 col-md-4 p-0" href="<?= base_url('/cotizaciones/nueva') ?>">
                          <div class="quick-actions-item">
                            <div class="avatar-item bg-primary rounded-circle">
                              <i class="fas fa-file-alt"></i>
                            </div>
                            <span class="text">Cotización</span>
                          </div>
                        </a>
                        <a class="col-6 col-md-4 p-0" href="<?= base_url('/controlpagos/crear') ?>">
                          <div class="quick-actions-item">
                            <div class="avatar-item bg-warning rounded-circle">
                              <i class="fas fa-dollar-sign"></i>
                            </div>
                            <span class="text">Pago</span>
                          </div>
                        </a>
                        <a class="col-6 col-md-4 p-0" href="<?= base_url('/entregas/crear') ?>">
                          <div class="quick-actions-item">
                            <div class="avatar-item bg-danger rounded-circle">
                              <i class="fas fa-truck"></i>
                            </div>
                            <span class="text">Entrega</span>
                          </div>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </li>

              <!-- Usuario -->
              <li class="nav-item topbar-user dropdown hidden-caret">
                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                  <div class="avatar-sm">
                    <img src="<?= base_url() . 'assets/img/profile.jpg' ?>" alt="Avatar"
                      class="avatar-img rounded-circle" />
                  </div>
                  <span class="profile-username">
                    <span class="op-7">Hola,</span>
                    <span class="fw-bold"><?= session('usuario_nombre_corto') ?? 'Usuario' ?></span>
                  </span>
                </a>
                <ul class="dropdown-menu dropdown-user animated fadeIn">
                  <div class="dropdown-user-scroll scrollbar-outer">
                    <li>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="<?= base_url('/perfil') ?>">Mi Perfil</a>
                      <a class="dropdown-item" href="<?= base_url('/configuracion') ?>">Configuración</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="#" onclick="confirmLogout(event)">Cerrar Sesión</a>
                    </li>
                  </div>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </div>

      <div class="container">
        <div class="page-inner">
          <div class="page-header">
            <h4 class="page-title">Dashboard</h4>
            <ul class="breadcrumbs">
              <li class="nav-home">
                <a href="#">
                  <i class="icon-home"></i>
                </a>
              </li>
              <li class="separator">
                <i class="icon-arrow-right"></i>
              </li>
              <li class="nav-item">
                <a href="#">Pages</a>
              </li>
              <li class="separator">
                <i class="icon-arrow-right"></i>
              </li>
              <li class="nav-item">
                <a href="#">Starter Page</a>
              </li>
            </ul>
          </div>
          <div class="page-category">
            <!-- Antes de cerrar el body -->

            <!-- Aquí iría el contenido específico de cada página -->
            
            <!-- JavaScript para manejar notificaciones -->
            <script>
            // Funciones para manejar el dropdown de notificaciones
            $(document).ready(function() {
                // Cargar notificaciones cuando se abre el dropdown
                $('#dropdown-notificaciones').on('show.bs.dropdown', function() {
                    cargarNotificacionesRecientes();
                });
                
                // Cargar mensajes cuando se abre el dropdown
                $('#dropdown-mensajes').on('show.bs.dropdown', function() {
                    cargarMensajesRecientes();
                });
            });

            function cargarNotificacionesRecientes() {
                $.get('<?= base_url('mensajeria/getNotificacionesRecientes') ?>', { limit: 5 })
                    .done(function(response) {
                        if (response.success) {
                            mostrarNotificaciones(response.data);
                        } else {
                            mostrarErrorNotificaciones('Error al cargar notificaciones');
                        }
                    })
                    .fail(function() {
                        mostrarErrorNotificaciones('Error de conexión');
                    });
            }

            function cargarMensajesRecientes() {
                // Esta función se implementaría para cargar mensajes recientes
                // Por ahora mostramos un mensaje de placeholder
                $('#mensajes-dropdown-content').html(`
                    <div class="text-center p-3">
                        <i class="fas fa-envelope fa-2x text-muted mb-2"></i>
                        <p class="mb-0 text-muted">No hay mensajes recientes</p>
                        <a href="<?= base_url('mensajeria') ?>" class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-comments me-1"></i> Ir a Mensajería
                        </a>
                    </div>
                `);
            }

            function mostrarNotificaciones(notificaciones) {
                const container = $('#notificaciones-dropdown-content');
                
                if (notificaciones.length === 0) {
                    container.html(`
                        <div class="text-center p-3">
                            <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                            <p class="mb-0 text-muted">No hay notificaciones</p>
                        </div>
                    `);
                    return;
                }
                
                let html = '';
                notificaciones.forEach(function(notificacion) {
                    const iconClass = getIconClass(notificacion.tipo);
                    const isUnread = !notificacion.leida;
                    const unreadClass = isUnread ? 'unread' : '';
                    
                    html += `
                        <div class="notification-item ${unreadClass}" onclick="marcarNotificacionLeida(${notificacion.id})">
                            <div class="d-flex align-items-start">
                                <div class="notification-icon ${notificacion.tipo}">
                                    <i class="${iconClass}"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">${notificacion.titulo}</div>
                                    <div class="notification-message">${notificacion.mensaje}</div>
                                    <div class="notification-time">${notificacion.tiempo_transcurrido}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                container.html(html);
            }

            function mostrarErrorNotificaciones(mensaje) {
                $('#notificaciones-dropdown-content').html(`
                    <div class="text-center p-3">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                        <p class="mb-0 text-muted">${mensaje}</p>
                    </div>
                `);
            }

            function getIconClass(tipo) {
                switch(tipo) {
                    case 'mensaje':
                        return 'fas fa-envelope';
                    case 'mensaje_importante':
                        return 'fas fa-exclamation-triangle';
                    case 'mensaje_urgente':
                        return 'fas fa-exclamation-circle';
                    case 'sistema':
                        return 'fas fa-cog';
                    default:
                        return 'fas fa-bell';
                }
            }

            function marcarNotificacionLeida(notificacionId) {
                // Obtener el token CSRF del meta tag
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                
                $.ajax({
                    url: '<?= base_url('mensajeria/marcarNotificacionLeida') ?>',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        notificacion_id: notificacionId
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remover la clase unread
                            $(`[onclick="marcarNotificacionLeida(${notificacionId})"]`).removeClass('unread');
                            
                            // Actualizar contador
                            if (window.notificacionesManager) {
                                window.notificacionesManager.forceCheck();
                            }
                        }
                    }
                });
            }

            function marcarTodasNotificacionesLeidas() {
                // Obtener el token CSRF del meta tag
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                
                $.ajax({
                    url: '<?= base_url('mensajeria/marcarTodasNotificacionesLeidas') ?>',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remover todas las clases unread
                            $('.notification-item').removeClass('unread');
                            
                            // Actualizar contador
                            if (window.notificacionesManager) {
                                window.notificacionesManager.forceCheck();
                            }
                            
                            // Mostrar mensaje de éxito
                            Swal.fire({
                                title: 'Éxito',
                                text: 'Todas las notificaciones han sido marcadas como leídas',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    }
                });
            }

            // Función para actualizar contadores desde el sistema de notificaciones
            function actualizarContadoresHeader(notificaciones, mensajes) {
                // Actualizar badge de notificaciones
                const badgeNotificaciones = $('#badge-notificaciones');
                if (notificaciones.total > 0) {
                    badgeNotificaciones.text(notificaciones.total).show();
                } else {
                    badgeNotificaciones.hide();
                }
                
                // Actualizar badge de mensajes
                const badgeMensajes = $('#badge-mensajes');
                if (mensajes.total > 0) {
                    badgeMensajes.text(mensajes.total).show();
                } else {
                    badgeMensajes.hide();
                }
                
                // Actualizar badge del sidebar
                const badgeMensajesSidebar = $('#badge-mensajes-sidebar');
                if (mensajes.total > 0) {
                    badgeMensajesSidebar.text(mensajes.total).show();
                } else {
                    badgeMensajesSidebar.hide();
                }
            }
            </script>