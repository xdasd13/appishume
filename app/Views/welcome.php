<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/welcome.css') ?>">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<h2>Welcome</h2>
<!-- Bootstrap JS ya se carga en el footer -->
<script>
        // Configuración del Toast de SweetAlert2
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        // Mostrar notificación de login exitoso con tipografía mejorada
        <?php if (session()->getFlashdata('success')): ?>
            Toast.fire({
                icon: "success",
                title: "¡Sesión iniciada correctamente!",
                text: "<?= addslashes(session()->getFlashdata('success')) ?>",
                customClass: {
                    popup: 'swal-success-popup',
                    title: 'swal-success-title',
                    content: 'swal-success-text'
                }
            });
        <?php endif; ?>

        // Mostrar notificación de error (acceso denegado, etc.)
        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Acceso Denegado',
                text: '<?= addslashes(session()->getFlashdata('error')) ?>',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#d33',
                timer: 5000,
                timerProgressBar: true,
                customClass: {
                    popup: 'swal2-popup',
                    title: 'swal2-title',
                    content: 'swal2-content'
                }
            });
        <?php endif; ?>

        // Detector de sesión expirada
        let sessionCheckInterval;
        
        function checkSessionStatus() {
            fetch('<?= base_url('auth/check-session') ?>', {
                method: 'GET',
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (!data.valid) {
                    clearInterval(sessionCheckInterval);
                    showSessionExpiredAlert();
                }
            })
            .catch(error => {
                console.log('Error verificando sesión:', error);
            });
        }

        function showSessionExpiredAlert() {
            Swal.fire({
                title: 'Sesión Expirada',
                text: 'Tu sesión ha expirado por inactividad. Serás redirigido al login.',
                icon: 'warning',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: true,
                confirmButtonText: 'Ir al Login',
                confirmButtonColor: '#FF6B00',
                background: '#ffffff',
                customClass: {
                    popup: 'session-expired-popup',
                    title: 'session-expired-title',
                    content: 'session-expired-text'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('login') ?>';
                }
            });
        }

        // Iniciar verificación de sesión cada 5 minutos
        sessionCheckInterval = setInterval(checkSessionStatus, 3600);
    </script>
<?= $footer ?>