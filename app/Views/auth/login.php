<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Iniciar Sesión' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-orange: #FF6B00;
            --primary-orange-hover: #FF7F32;
            --text-primary: #333333;
            --text-secondary: #888888;
            --error-color: #E63946;
            --success-color: #4CAF50;
            --background-white: #FFFFFF;
            --border-light: #E5E5E5;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #FF6B00 0%, #FF7F1A 25%, #FFA65C 60%, #FFF4EB 100%);
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            padding: 3rem 2.5rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-header h1 {
            color: #333333;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0 0 0.5rem 0;
        }

        .login-header p {
            color: #888888;
            font-size: 0.9rem;
            margin: 0;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem 3rem 1rem 1rem;
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fafafa;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: #FF6B00;
            background: white;
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
        }

        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #888888;
            transition: color 0.3s ease;
        }

        .form-control:focus + .input-icon {
            color: #FF6B00;
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #FF6B00, #FF7F32);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 0, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .alert-danger {
            background: #fef2f2;
            color: #E63946;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #4CAF50;
            border: 1px solid #bbf7d0;
        }

        /* Estilos personalizados para SweetAlert */
        .swal-loading-popup {
            border-radius: 12px !important;
            padding: 2rem !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }

        .swal-loading-title {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 600 !important;
            font-size: 1.3rem !important;
            color: #333333 !important;
        }

        .swal-loading-text {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 400 !important;
            font-size: 0.95rem !important;
            color: #666666 !important;
        }
    </style>
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
