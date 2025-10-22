</div>
          <!-- <footer class="footer">
            <div class="container-fluid d-flex justify-content-between">
              <nav class="pull-left">
                <ul class="nav">
                  <li class="nav-item">
                    <a class="nav-link" href="<?= base_url() . 'http://www.themekita.com'?>">
                      ThemeKita
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#"> Help </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#"> Licenses </a>
                  </li>
                </ul>
              </nav>
              <div class="copyright">
                2024, made with <i class="fa fa-heart heart text-danger"></i> by
                <a href="<?= base_url() . 'http://www.themekita.com'?>">ThemeKita</a>
              </div>
              <div>
                Distributed by
            </div>
          </footer> 
        </div>
      </div>
      <!--   Core JS Files - Orden correcto   -->
      <script src="<?=base_url() . 'assets/js/core/jquery-3.7.1.min.js'?>"></script>
      <script src="<?= base_url() . 'assets/js/core/popper.min.js'?>"></script>
      <!-- Solo una versión de Bootstrap JS -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
      <!-- jQuery Scrollbar -->
      <script src="<?= base_url() . 'assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js'?>"></script>

      <!-- Chart JS -->

      <!-- jQuery Sparkline -->
      <script src=" <?= base_url() . 'assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js'?>"></script>

      <!-- Chart Circle -->
      <script src="<?= base_url(). 'assets/js/plugin/chart-circle/circles.min.js'?>"></script>

      <!-- Datatables -->
      <script src="<?= base_url() . 'assets/js/plugin/datatables/datatables.min.js'?>"></script>

      <!-- Bootstrap Notify -->
      <script src="<?= base_url() . 'assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js'?>"></script>

      <!-- jQuery Vector Maps -->
      <script src="<?= base_url() . 'assets/js/plugin/jsvectormap/jsvectormap.min.js'?>"></script>
      <script src="<?= base_url() . 'assets/js/plugin/jsvectormap/world.js'?>"></script>

      <!-- Google Maps Plugin -->
      <script src="<?= base_url() . 'assets/js/plugin/gmaps/gmaps.js'?>"></script>

      <!-- Select2 -->
      <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
      
      <!-- jQuery Validate -->
      <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
      
      <!-- Sweet Alert -->
      <script src="<?= base_url() . 'assets/js/plugin/sweetalert/sweetalert.min.js'?>"></script>
      <!-- SweetAlert2 -->
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

      <!-- Script para prevenir errores de scrollbar -->
      <script>
        // Prevenir errores de scrollbar si el plugin no está disponible
        $(document).ready(function() {
          // Verificar si el plugin scrollbar está disponible
          if (typeof $.fn.scrollbar !== 'function') {
            // Si no está disponible, crear una función vacía para evitar errores
            $.fn.scrollbar = function() { return this; };
          }
        });
      </script>

      <!-- Kaiadmin JS -->
      <script src="<?= base_url() . 'assets/js/kaiadmin.min.js'?>"></script>
      <!-- Custom JS - Cargado después de jQuery -->
      <script src="<?= base_url('assets/js/custom.js') ?>"></script>

      <!-- Script global para logout -->
      <script>
        function confirmLogout(event) {
          event.preventDefault();

          Swal.fire({
            title: '¿Cerrar Sesión?',
            text: '¿Estás seguro de que deseas cerrar tu sesión?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#FF6B00',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cerrar sesión',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            background: '#ffffff',
            customClass: {
              popup: 'logout-confirm-popup',
              title: 'logout-confirm-title',
              content: 'logout-confirm-text',
              confirmButton: 'logout-confirm-btn',
              cancelButton: 'logout-cancel-btn'
            }
          }).then((result) => {
            if (result.isConfirmed) {
              // Mostrar notificación de cierre exitoso
              Swal.fire({
                title: 'Sesión Cerrada',
                text: 'Has cerrado sesión correctamente',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                background: '#ffffff',
                customClass: {
                  popup: 'logout-success-popup',
                  title: 'logout-success-title',
                  content: 'logout-success-text'
                }
              }).then(() => {
                window.location.href = '<?= base_url('auth/logout') ?>';
              });
            }
          });
        }
      </script>

   

    </body>
  </html>