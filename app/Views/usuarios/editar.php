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

                // Validar el formulario
                form.classList.add('was-validated');
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Buscar el primer campo inválido y su mensaje
                    const firstInvalid = form.querySelector(':invalid');
                    let errorMsg = 'Por favor corrija los campos marcados en rojo.';
                    
                    if (firstInvalid) {
                        // Si es un campo con mensaje de validación personalizado
                        if (firstInvalid.validationMessage) {
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
                let confirmacionConfig = {
                    title: 'Confirmar Cambios',
                    text: '¿Desea guardar los cambios realizados?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, guardar cambios',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    confirmButtonColor: '#FF6B00',
                    cancelButtonColor: '#6c757d'
                };

                // Personalizar la confirmación si hay cambio de rol
                if (originalTipoUsuario !== nuevoTipoUsuario) {
                    confirmacionConfig.title = 'Cambio de Rol';
                    confirmacionConfig.icon = 'warning';
                    
                    if (originalTipoUsuario === 'trabajador' && nuevoTipoUsuario === 'admin') {
                        confirmacionConfig.html = `
                            <div class="text-center">
                                <i class="fas fa-user-shield fa-3x text-warning mb-3"></i>
                                <p>¿Está seguro de promover este trabajador a <strong>Administrador</strong>?</p>
                                <p class="text-warning"><small>Esta acción otorgará acceso completo al sistema.</small></p>
                                <div class="mt-3 p-2 bg-light rounded">
                                    <i class="fas fa-info-circle text-info"></i>
                                    <small class="d-block">El usuario tendrá acceso a todas las funciones administrativas</small>
                                </div>
                            </div>
                        `;
                    } else if (originalTipoUsuario === 'admin' && nuevoTipoUsuario === 'trabajador') {
                        confirmacionConfig.html = `
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-user-minus fa-3x text-danger mb-3"></i>
                                    <h5 class="text-danger">¡Atención!</h5>
                                </div>
                                <p>¿Está seguro de cambiar el rol de <strong>Administrador</strong> a <strong>Trabajador</strong>?</p>
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Consecuencias del cambio:</strong>
                                    <ul class="text-start mb-0 mt-2">
                                        <li>Perderá acceso a funciones administrativas</li>
                                        <li>No podrá gestionar otros usuarios</li>
                                        <li>Acceso limitado a reportes y configuraciones</li>
                                    </ul>
                                </div>
                                <p class="text-danger"><small>Esta acción requiere confirmación especial</small></p>
                            </div>
                        `;
                        confirmacionConfig.confirmButtonText = 'Sí, remover privilegios';
                        confirmacionConfig.confirmButtonColor = '#dc3545';
                    }
                }

                // Mostrar confirmación con SweetAlert
                const result = await Swal.fire({
                    ...confirmacionConfig,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    showClass: {
                        popup: 'animate__animated animate__fadeIn'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOut'
                    }
                });

                if (!result.isConfirmed) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Guardar Cambios';
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';

                try {
                    // Preparar datos del formulario
                    const formData = new FormData(form);
                    
                    // Agregar el token CSRF para CodeIgniter 4
                    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                    
                    // Usar fetch API en lugar de jQuery para mayor consistencia
                    const response = await fetch('<?= base_url('usuarios/actualizar/' . $usuario->idusuario) ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        const successConfig = {
                            title: '¡Cambios Guardados!',
                            html: `
                                <div class="text-center">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <p>Los cambios han sido guardados exitosamente</p>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'OK'
                        };

                        if (nuevoTipoUsuario !== originalTipoUsuario) {
                            const roleChangeMessage = nuevoTipoUsuario === 'trabajador' 
                                ? `
                                    <div class="text-center">
                                        <i class="fas fa-user fa-3x text-primary mb-3"></i>
                                        <h5 class="mb-3">Rol Actualizado</h5>
                                        <p>El usuario ahora es <strong>TRABAJADOR</strong></p>
                                        <div class="alert alert-info mt-3">
                                            <small>
                                                <i class="fas fa-info-circle"></i>
                                                Los cambios en los permisos serán efectivos inmediatamente
                                            </small>
                                        </div>
                                    </div>
                                `
                                : `
                                    <div class="text-center">
                                        <i class="fas fa-user-shield fa-3x text-success mb-3"></i>
                                        <h5 class="mb-3">¡Promoción Exitosa!</h5>
                                        <p>El usuario ahora es <strong>ADMINISTRADOR</strong></p>
                                        <div class="alert alert-success mt-3">
                                            <small>
                                                <i class="fas fa-check-circle"></i>
                                                Se han otorgado todos los privilegios administrativos
                                            </small>
                                        </div>
                                    </div>
                                `;
                            successConfig.html = roleChangeMessage;
                        }

                        await Swal.fire(successConfig);
                        // Redirigir después de confirmar
                        window.location.href = '<?= base_url('usuarios') ?>';
                    } else {
                        throw new Error(data.message || 'Ha ocurrido un error al guardar los cambios');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: error.message || 'Error al procesar la solicitud',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Guardar Cambios';
                }
            });

            // Validación en tiempo real
            const validateField = (field) => {
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
                        e.stopPropagation();
                        validateField(field);
                    });
                });
            });
        });
    </script>
    <?= $footer ?>