<?= $header ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
/* Estilos personalizados para SweetAlert Toast */
.swal-success-popup {
    font-family: 'Poppins', sans-serif !important;
    border-radius: 12px !important;
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.15) !important;
}

.swal-success-title {
    font-family: 'Poppins', sans-serif !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
    color: #2E7D32 !important;
}

.swal-success-text {
    font-family: 'Poppins', sans-serif !important;
    font-weight: 400 !important;
    font-size: 0.9rem !important;
    color: #4CAF50 !important;
}

/* Detector de sesión expirada */
.session-expired-popup {
    border-radius: 15px !important;
    padding: 2rem !important;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2) !important;
}

.session-expired-title {
    font-family: 'Poppins', sans-serif !important;
    font-weight: 600 !important;
    font-size: 1.4rem !important;
    color: #E63946 !important;
}

.session-expired-text {
    font-family: 'Poppins', sans-serif !important;
    font-weight: 400 !important;
    font-size: 1rem !important;
    color: #666666 !important;
}
</style>
<h2>Welcome</h2>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

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
        sessionCheckInterval = setInterval(checkSessionStatus, 5000);
    </script>
<?= $footer ?>