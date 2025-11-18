<!DOCTYPE html>
<html lang="es">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Ishume - Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
  <meta name="csrf-token" content="<?= csrf_hash() ?>" />

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
  <link rel="stylesheet" href="<?= base_url('assets/css/sweetalert-custom.css') ?>">
  <!-- Favicons -->
  <link rel="icon" href="<?= base_url('assets/img/kaiadmin/favicon.ico') ?>" type="image/x-icon" />
  <link rel="apple-touch-icon" href="<?= base_url('assets/img/kaiadmin/logo.png') ?>" />

  <!-- Fonts and icons -->
  <script src="<?= base_url() . 'assets/js/plugin/webfont/webfont.min.js' ?>"></script>
  <script>
    WebFont.load({
      google: { families: ["Public Sans:300,400,500,600,700", "Poppins:300,400,500,600,700,800"] },
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
  <link rel="stylesheet" href="<?= base_url('assets/css/sidebar-custom.css') ?>" />
  
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Chart.js para gráficos -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body>
  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" data-background-color="dark">
      <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
          <h1>ISHUME</h1>
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

            <!-- Separador -->
            <li class="nav-section">
              <span class="sidebar-mini-icon">
                <i class="fa fa-ellipsis-h"></i>
              </span>
              <h4 class="text-section">GESTIÓN PRINCIPAL</h4>
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
                    <a href="<?= base_url('/equipos') ?>">
                      <span class="sub-item">Equipos de Trabajo</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/cronograma') ?>">
                      <span class="sub-item">Cronograma</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/historial') ?>">
                      <span class="sub-item">Historial de actividades</span>
                    </a>
                  </li>
                  <li>
                    <a href="<?= base_url('/equipos/vencidos') ?>">
                      <span class="sub-item text-danger">Proyectos Vencidos</span>
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
            <a href="<?= base_url('/welcome') ?>" class="logo"><span class="logo-text">ISHUME</span></a>
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
              <div class="input-group position-relative">
                <div class="input-group-prepend">
                  <button type="button" class="btn btn-search pe-1" id="btn-buscar-global">
                    <i class="fa fa-search search-icon"></i>
                  </button>
                </div>
                <input 
                  type="text" 
                  id="busqueda-global" 
                  placeholder="Buscar en todo el sistema..." 
                  class="form-control" 
                  autocomplete="off"
                />
                <!-- Dropdown de resultados -->
                <div id="resultados-busqueda" class="dropdown-menu w-100" style="display: none; max-height: 500px; overflow-y: auto;"></div>
              </div>
            </nav>

            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

              <!-- Notificaciones del sistema -->
              <li 
                class="nav-item topbar-icon dropdown hidden-caret" 
                id="notificationDropdown"
                data-api-list="<?= base_url('api/notifications') ?>"
                data-api-recent="<?= base_url('api/notifications/recent') ?>"
                data-api-count="<?= base_url('api/notifications/unread-count') ?>"
                data-api-mark="<?= base_url('api/notifications/mark-read') ?>"
                data-api-mark-all="<?= base_url('api/notifications/mark-all-read') ?>"
              >
                <a class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <i class="fas fa-bell"></i>
                  <span class="notification d-none" id="notificationCount">0</span>
                </a>
                <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notificationDropdown">
                  <li>
                    <div class="dropdown-title d-flex justify-content-between align-items-center">
                      <span>Notificaciones</span>
                      <button type="button" class="btn btn-link btn-sm p-0" id="markAllNotifications">Marcar todas</button>
                    </div>
                  </li>
                  <li>
                    <div class="notif-scroll scrollbar-outer">
                      <div class="notif-center" id="notificationList">
                        <div class="text-muted small text-center py-3">Cargando notificaciones...</div>
                      </div>
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
                  <div class="avatar-xs">
                    <img src="<?= base_url() . 'assets/img/user.png' ?>" alt="Avatar"
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
          <div class="page-category">
            <!-- Antes de cerrar el body -->

<!-- Script de Búsqueda Global -->
<script>
/**
 * Buscador Global del Sistema
 * Utiliza async/await para búsqueda en tiempo real con debounce
 */

// Variable para el timeout del debounce
let timeoutBusqueda = null;

// Elementos del DOM
const inputBusqueda = document.getElementById('busqueda-global');
const btnBuscar = document.getElementById('btn-buscar-global');
const dropdownResultados = document.getElementById('resultados-busqueda');

/**
 * Inicializar buscador cuando el DOM esté listo
 */
document.addEventListener('DOMContentLoaded', function() {
    if (inputBusqueda) {
        // Evento al escribir en el input (con debounce)
        inputBusqueda.addEventListener('input', function(e) {
            const termino = e.target.value.trim();
            
            // Limpiar timeout anterior
            clearTimeout(timeoutBusqueda);
            
            // Si el término tiene menos de 3 caracteres, ocultar resultados
            if (termino.length < 3) {
                ocultarResultados();
                return;
            }
            
            // Esperar 500ms después de que el usuario termine de escribir
            timeoutBusqueda = setTimeout(async () => {
                await buscarEnSistema(termino);
            }, 500);
        });
        
        // Evento al hacer clic en el botón de búsqueda
        btnBuscar.addEventListener('click', async function() {
            const termino = inputBusqueda.value.trim();
            if (termino.length >= 3) {
                await buscarEnSistema(termino);
            }
        });
        
        // Evento Enter en el input
        inputBusqueda.addEventListener('keypress', async function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const termino = inputBusqueda.value.trim();
                if (termino.length >= 3) {
                    await buscarEnSistema(termino);
                }
            }
        });
        
        // Cerrar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!inputBusqueda.contains(e.target) && !dropdownResultados.contains(e.target)) {
                ocultarResultados();
            }
        });
    }
});

/**
 * Buscar en todo el sistema (async/await)
 */
async function buscarEnSistema(termino) {
    try {
        // Mostrar loading
        mostrarLoading();
        
        // Preparar datos del formulario
        const formData = new FormData();
        formData.append('termino', termino);
        
        // Realizar petición AJAX con fetch
        const response = await fetch('<?= base_url('buscar') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        // Verificar si la respuesta es exitosa
        if (!response.ok) {
            throw new Error('Error en la petición');
        }
        
        // Convertir respuesta a JSON
        const data = await response.json();
        
        // Mostrar resultados
        if (data.success) {
            mostrarResultados(data.resultados, data.total, data.termino);
        } else {
            console.error('Error del servidor:', data);
            mostrarError(data.mensaje || 'Error al realizar la búsqueda');
        }
        
    } catch (error) {
        console.error('Error en búsqueda:', error);
        mostrarError('Error de conexión: ' + error.message);
    }
}

/**
 * Mostrar loading en el dropdown
 */
function mostrarLoading() {
    dropdownResultados.innerHTML = `
        <div class="p-4 text-center">
            <div class="spinner-border spinner-border-sm text-warning" role="status">
                <span class="visually-hidden">Buscando...</span>
            </div>
            <p class="mt-2 mb-0 text-muted small">Buscando en todo el sistema...</p>
        </div>
    `;
    dropdownResultados.style.display = 'block';
    dropdownResultados.classList.add('show');
}

/**
 * Mostrar resultados de la búsqueda
 */
function mostrarResultados(resultados, total, termino) {
    if (total === 0) {
        dropdownResultados.innerHTML = `
            <div class="p-4 text-center">
                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                <p class="mb-0 text-muted">No se encontraron resultados para "<strong>${termino}</strong>"</p>
            </div>
        `;
        dropdownResultados.style.display = 'block';
        dropdownResultados.classList.add('show');
        return;
    }
    
    let html = `
        <div class="px-3 py-2 bg-light border-bottom">
            <small class="text-muted">
                <i class="fas fa-search"></i> 
                Se encontraron <strong>${total}</strong> resultados para "<strong>${termino}</strong>"
            </small>
        </div>
    `;
    
    // Recorrer cada categoría de resultados
    for (const [categoria, items] of Object.entries(resultados)) {
        if (items.length > 0) {
            html += `
                <div class="dropdown-divider my-0"></div>
                <h6 class="dropdown-header text-uppercase fw-bold">
                    ${items[0].categoria}
                </h6>
            `;
            
            items.forEach(item => {
                html += `
                    <a href="${item.url}" class="dropdown-item py-2">
                        <div class="d-flex align-items-start">
                            <div class="me-2">
                                <i class="${item.icono} text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-medium text-dark">${item.titulo}</div>
                                <small class="text-muted">${item.subtitulo}</small>
                            </div>
                        </div>
                    </a>
                `;
            });
        }
    }
    
    dropdownResultados.innerHTML = html;
    dropdownResultados.style.display = 'block';
    dropdownResultados.classList.add('show');
}

/**
 * Mostrar mensaje de error
 */
function mostrarError(mensaje) {
    dropdownResultados.innerHTML = `
        <div class="p-4 text-center">
            <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
            <p class="mb-0 text-muted">${mensaje}</p>
        </div>
    `;
    dropdownResultados.style.display = 'block';
    dropdownResultados.classList.add('show');
}

/**
 * Ocultar dropdown de resultados
 */
function ocultarResultados() {
    dropdownResultados.style.display = 'none';
    dropdownResultados.classList.remove('show');
}
</script>

<!-- Estilos adicionales para el buscador -->
<style>
#resultados-busqueda {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1050;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-top: 4px;
}

#resultados-busqueda .dropdown-item {
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s;
}

#resultados-busqueda .dropdown-item:last-child {
    border-bottom: none;
}

#resultados-busqueda .dropdown-item:hover {
    background-color: #fff8f0;
}

#busqueda-global:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}
</style>

            <!-- Aquí iría el contenido específico de cada página -->