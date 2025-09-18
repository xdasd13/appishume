     <?= $header ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        .user-header {
            background: linear-gradient(135deg, #FF8008 0%, #FFC837 100%);
            color: #fff;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 6px 15px rgba(255, 128, 8, 0.3);
            font-family: 'Poppins', sans-serif;
        }

        .form-section {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #FF8008;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .form-section:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(255, 128, 8, 0.2);
        }

        .password-toggle {
            cursor: pointer;
            color: #FF8008;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #e76f00;
        }

    </style>
    
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Encabezado del Usuario -->
                <div class="user-header text-center">
                    <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; background: rgba(255, 81, 0, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: bold;">
                        <?= strtoupper(substr($usuario->nombres, 0, 1) . substr($usuario->apellidos, 0, 1)) ?>
                    </div>
                    <h3><?= $usuario->nombres . ' ' . $usuario->apellidos ?></h3>
                    <p class="mb-0">
                        <span class="badge bg-light text-dark me-2"><?= $usuario->nombreusuario ?></span>
                        <span class="badge bg-info"><?= $usuario->email ?></span>
                    </p>
                </div>

                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-edit me-2"></i>Editar Credenciales
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="editarUsuarioForm" novalidate>
                            <input type="hidden" name="idusuario" value="<?= $usuario->idusuario ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

                            <!-- Información de la Persona (solo lectura) -->
                            <div class="form-section">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Información Personal
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombres</label>
                                        <input type="text" class="form-control" value="<?= $usuario->nombres ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Apellidos</label>
                                        <input type="text" class="form-control" value="<?= $usuario->apellidos ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Documento</label>
                                        <input type="text" class="form-control" 
                                               value="<?= $usuario->tipodoc . ': ' . $usuario->numerodoc ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" value="<?= $usuario->telprincipal ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Dirección</label>
                                        <input type="text" class="form-control" value="<?= $usuario->direccion ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Credenciales de Acceso -->
                            <div class="form-section">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-key me-2"></i>Credenciales de Acceso
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Cargo *</label>
                                        <select class="form-select" name="idcargo" required>
                                            <option value="">Seleccionar cargo...</option>
                                            <?php foreach ($cargos as $cargo): ?>
                                                <option value="<?= $cargo->idcargo ?>" 
                                                    <?= $cargo->idcargo == $usuario->idcargo ? 'selected' : '' ?>>
                                                    <?= $cargo->cargo ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Debe seleccionar un cargo</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tipo de Usuario *</label>
                                        <select class="form-select" name="tipo_usuario" required>
                                            <option value="trabajador" <?= $usuario->tipo_usuario == 'trabajador' ? 'selected' : '' ?>>Trabajador</option>
                                            <option value="admin" <?= $usuario->tipo_usuario == 'admin' ? 'selected' : '' ?>>Administrador</option>
                                        </select>
                                        <div class="invalid-feedback">Debe seleccionar un tipo de usuario</div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre de Usuario</label>
                                        <input type="text" class="form-control" value="<?= $usuario->nombreusuario ?>" readonly>
                                        <small class="form-text text-muted">El nombre de usuario no puede ser modificado</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="<?= $usuario->email ?>" readonly>
                                        <small class="form-text text-muted">El email no puede ser modificado</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Cambio de Contraseña -->
                            <div class="form-section">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-lock me-2"></i>Cambiar Contraseña
                                </h6>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Complete solo si desea cambiar la contraseña. Deje en blanco para mantener la actual.
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Nueva Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="password" 
                                                   id="password" placeholder="Dejar en blanco para no cambiar">
                                            <span class="input-group-text password-toggle" onclick="togglePassword('password')">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        <div class="invalid-feedback">La contraseña debe tener al menos 8 caracteres</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirmar Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="confirm_password" 
                                                   id="confirm_password" placeholder="Confirmar nueva contraseña">
                                            <span class="input-group-text password-toggle" onclick="togglePassword('confirm_password')">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        <div class="invalid-feedback">Las contraseñas no coinciden</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="<?= base_url('usuarios') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Volver
                                </a>
                                <div>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save me-1"></i> Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editarUsuarioForm');
            const tipoUsuarioSelect = form.querySelector('select[name="tipo_usuario"]');
            const originalTipoUsuario = '<?= $usuario->tipo_usuario ?>';

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submitBtn = document.getElementById('submitBtn');

                // Prevenir múltiples envíos
                if (submitBtn.disabled) {
                    return;
                }

                // Validación de contraseña segura si se intenta cambiar
                const password = document.getElementById('password');
                const confirmPassword = document.getElementById('confirm_password');
                let passwordError = '';

                // Limpiar validaciones previas
                password.setCustomValidity('');
                confirmPassword.setCustomValidity('');
                
                if (password.value) {
                    // Requisitos: 8+, mayúscula, minúscula, número, símbolo
                    const length = password.value.length >= 8;
                    const upper = /[A-Z]/.test(password.value);
                    const lower = /[a-z]/.test(password.value);
                    const number = /[0-9]/.test(password.value);
                    const symbol = /[^A-Za-z0-9]/.test(password.value);
                    if (!(length && upper && lower && number && symbol)) {
                        passwordError = 'La contraseña debe tener mínimo 8 caracteres, al menos una mayúscula, una minúscula, un número y un símbolo.';
                        password.setCustomValidity(passwordError);
                    }
                }

                // Validación de confirmación de contraseña solo si se está cambiando
                if (password.value || confirmPassword.value) {
                    if (password.value !== confirmPassword.value) {
                        confirmPassword.setCustomValidity('Las contraseñas no coinciden');
                    }
                }

                // Validar el formulario
                form.classList.add('was-validated');
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Buscar el primer campo inválido y su mensaje
                    const firstInvalid = form.querySelector(':invalid');
                    let errorMsg = 'Por favor corrija los campos marcados en rojo.';
                    
                    if (firstInvalid) {
                        // Si es el campo de contraseña y tiene error específico
                        if (firstInvalid === password && passwordError) {
                            errorMsg = passwordError;
                        } 
                        // Si es un campo con mensaje de validación personalizado
                        else if (firstInvalid.validationMessage) {
                            errorMsg = firstInvalid.validationMessage;
                        }
                        // Asegurarse de que el campo tenga foco
                        setTimeout(() => firstInvalid.focus(), 100);
                    }
                    
                    // Mostrar SweetAlert con el error
                    Swal.fire({
                        title: 'Error de validación',
                        text: errorMsg,
                        icon: 'error',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Entendido'
                    }).then(() => {
                        // Asegurarse de que el botón esté habilitado después del error
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Guardar Cambios';
                    });
                    return false;
                }

                // Verificar si hay cambio de rol
                const nuevoTipoUsuario = tipoUsuarioSelect.value;
                let confirmacionRequerida = false;
                let mensajeConfirmacion = '';
                let iconoConfirmacion = 'question';

                if (originalTipoUsuario !== nuevoTipoUsuario) {
                    confirmacionRequerida = true;
                    if (originalTipoUsuario === 'trabajador' && nuevoTipoUsuario === 'admin') {
                        mensajeConfirmacion = '¿Está seguro de promover este trabajador a Administrador? Tendrá acceso completo al sistema.';
                        iconoConfirmacion = 'warning';
                    } else if (originalTipoUsuario === 'admin' && nuevoTipoUsuario === 'trabajador') {
                        mensajeConfirmacion = '¿Está seguro de cambiar este administrador a Trabajador? Perderá privilegios administrativos.';
                        iconoConfirmacion = 'warning';
                    }
                } else {
                    mensajeConfirmacion = '¿Desea guardar los cambios realizados?';
                }

                // Mostrar confirmación con SweetAlert
                const result = await Swal.fire({
                    title: confirmacionRequerida ? 'Cambio de Rol' : 'Confirmar Cambios',
                    text: mensajeConfirmacion,
                    icon: iconoConfirmacion,
                    showCancelButton: true,
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, guardar cambios',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                });

                if (!result.isConfirmed) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Guardar Cambios';
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';

                try {
                    const formData = new FormData(form);
                    const response = await fetch('<?= base_url('usuarios/actualizar/' . $usuario->idusuario) ?>', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        await Swal.fire({
                            title: '¡Éxito!',
                            text: 'Credenciales actualizadas exitosamente',
                            icon: 'success',
                            confirmButtonColor: '#4e73df',
                            timer: 2000,
                            timerProgressBar: true
                        });
                        window.location.href = '<?= base_url('usuarios') ?>';
                    } else {
                        await Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#4e73df'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    await Swal.fire({
                        title: 'Error',
                        text: 'Error al actualizar las credenciales',
                        icon: 'error',
                        confirmButtonColor: '#4e73df'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Guardar Cambios';
                }
            });


            // Validación de confirmación de contraseña y requisitos en tiempo real
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');

            function validatePasswordRequirements() {
                // Si no hay contraseña, no aplicar validación
                if (!password.value) {
                    password.setCustomValidity('');
                    password.classList.remove('is-invalid', 'is-valid');
                    return;
                }

                const requirements = {
                    length: { test: password.value.length >= 8, message: '8 caracteres' },
                    upper: { test: /[A-Z]/.test(password.value), message: 'mayúscula' },
                    lower: { test: /[a-z]/.test(password.value), message: 'minúscula' },
                    number: { test: /[0-9]/.test(password.value), message: 'número' },
                    symbol: { test: /[^A-Za-z0-9]/.test(password.value), message: 'símbolo' }
                };

                const failedRequirements = Object.entries(requirements)
                    .filter(([_, { test }]) => !test)
                    .map(([_, { message }]) => message);

                if (failedRequirements.length > 0) {
                    const errorMessage = `La contraseña debe tener: ${failedRequirements.join(', ')}`;
                    password.setCustomValidity(errorMessage);
                    password.classList.add('is-invalid');
                    password.classList.remove('is-valid');
                } else {
                    password.setCustomValidity('');
                    password.classList.remove('is-invalid');
                    password.classList.add('is-valid');
                }
            }

            let passwordTimeout = null;
            let confirmTimeout = null;

            password.addEventListener('input', function(e) {
                e.stopPropagation();
                // Cancelar timeout anterior si existe
                if (passwordTimeout) clearTimeout(passwordTimeout);
                
                // Establecer nuevo timeout para la validación
                passwordTimeout = setTimeout(() => {
                    validatePasswordRequirements();
                    // Validar confirmación también
                    if (confirmPassword.value) {
                        if (password.value !== confirmPassword.value) {
                            confirmPassword.setCustomValidity('Las contraseñas no coinciden');
                            confirmPassword.classList.add('is-invalid');
                            confirmPassword.classList.remove('is-valid');
                        } else {
                            confirmPassword.setCustomValidity('');
                            confirmPassword.classList.remove('is-invalid');
                            confirmPassword.classList.add('is-valid');
                        }
                    }
                }, 300); // Esperar 300ms después de la última entrada
            });

            confirmPassword.addEventListener('input', function(e) {
                e.stopPropagation();
                // Cancelar timeout anterior si existe
                if (confirmTimeout) clearTimeout(confirmTimeout);
                
                // Establecer nuevo timeout para la validación
                confirmTimeout = setTimeout(() => {
                    if (!password.value && !confirmPassword.value) {
                        confirmPassword.setCustomValidity('');
                        confirmPassword.classList.remove('is-invalid', 'is-valid');
                        return;
                    }
                    
                    if (password.value !== confirmPassword.value) {
                        confirmPassword.setCustomValidity('Las contraseñas no coinciden');
                        confirmPassword.classList.add('is-invalid');
                        confirmPassword.classList.remove('is-valid');
                    } else {
                        confirmPassword.setCustomValidity('');
                        confirmPassword.classList.remove('is-invalid');
                        confirmPassword.classList.add('is-valid');
                    }
                }, 300); // Esperar 300ms después de la última entrada
            });

            // Validación en tiempo real
            const validateField = (field) => {
                // No validar campos de contraseña vacíos
                if (field.type === 'password' && !field.value) {
                    field.classList.remove('is-invalid', 'is-valid');
                    return;
                }

                // Para otros campos
                if (!field.checkValidity()) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                }
            };

            // Aplicar validación a todos los campos requeridos
            form.querySelectorAll('select, input[required]').forEach(field => {
                ['change', 'input'].forEach(eventType => {
                    field.addEventListener(eventType, (e) => {
                        e.stopPropagation(); // Evitar propagación del evento
                        validateField(field);
                    });
                });
            });
        });
    </script>
    <?= $footer ?>