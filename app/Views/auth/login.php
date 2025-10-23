<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Iniciar Sesión' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/login.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Iniciar Sesión</h1>
            <p>Ingresa tus credenciales para acceder</p>
        </div>

        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success) && $success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <?= $success ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/authenticate') ?>" method="POST" id="loginForm">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <input type="text" class="form-control" id="login" name="login" 
                       placeholder="Usuario o Email" value="<?= old('login') ?>" required>
                <i class="fas fa-user input-icon"></i>
            </div>

            <div class="form-group">
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Contraseña" required>
                <i class="fas fa-lock input-icon"></i>
            </div>

            <button type="submit" class="btn-login">
                Iniciar Sesión
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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

        // Mostrar notificaciones según mensajes del servidor
        <?php if (isset($error) && $error): ?>
            Toast.fire({
                icon: "error",
                title: "Error de autenticación",
                text: "<?= addslashes($error) ?>"
            });
        <?php endif; ?>

        <?php if (isset($success) && $success): ?>
            Toast.fire({
                icon: "success",
                title: "Sesión iniciada correctamente",
                text: "<?= addslashes($success) ?>"
            });
        <?php endif; ?>

        // Mostrar notificación de cierre de sesión si viene de logout
        <?php if (session()->getFlashdata('logout_success')): ?>
            Toast.fire({
                icon: "info",
                title: "Sesión cerrada",
                text: "Has cerrado sesión correctamente"
            });
        <?php endif; ?>

        // Validación del formulario
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const login = document.getElementById('login').value;
            const password = document.getElementById('password').value;

            if (!login || !password) {
                e.preventDefault();
                Toast.fire({
                    icon: "warning",
                    title: "Campos incompletos",
                    text: "Por favor, completa todos los campos"
                });
                return false;
            }

            // Mostrar notificación de carga elegante mientras se procesa el login
            Swal.fire({
                title: 'Iniciando sesión',
                text: 'Verificando credenciales...',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                background: '#ffffff',
                color: '#333333',
                customClass: {
                    popup: 'swal-loading-popup',
                    title: 'swal-loading-title',
                    content: 'swal-loading-text'
                },
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    </script>
</body>
</html>