<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/usuarios-editar.css') ?>">
    <!-- Bootstrap CSS ya se carga en el header -->
    <!-- FontAwesome ya se carga en el header -->
    <!-- jQuery ya se carga en el footer -->
    
    
    
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
                            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

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
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lock text-secondary"></i> Campo no editable
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Teléfono *</label>
                                        <input type="tel" class="form-control" name="telprincipal" 
                                               value="<?= $usuario->telprincipal ?>" 
                                               pattern="[0-9]{9}" maxlength="9" required
                                               placeholder="Ej: 987654321">
                                        <div class="invalid-feedback">Ingrese un teléfono válido (9 dígitos)</div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-edit text-primary"></i> Campo editable
                                        </small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Dirección *</label>
                                        <input type="text" class="form-control" name="direccion" 
                                               value="<?= $usuario->direccion ?>" 
                                               maxlength="200" required
                                               placeholder="Ej: Av. Los Pinos 123, San Isidro">
                                        <div class="invalid-feedback">La dirección es requerida</div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-edit text-primary"></i> Campo editable
                                        </small>
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
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lock text-secondary"></i> El email no puede ser modificado
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección de Cambio de Contraseña -->
                            <div class="form-section">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-lock me-2"></i>Cambio de Contraseña
                                    <small class="text-muted">(Opcional - Solo completar si desea cambiar la contraseña)</small>
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Nueva Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="nueva_password" name="nueva_password" 
                                                   placeholder="Ingrese nueva contraseña (opcional)"
                                                   minlength="8" maxlength="50">
                                            <button class="btn btn-outline-secondary password-toggle" type="button" 
                                                    onclick="togglePassword('nueva_password')" tabindex="-1">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">
                                            La contraseña debe tener al menos 8 caracteres
                                        </div>
                                        <small class="form-text text-muted">
                                            Mín. 8 caracteres, 1 mayúscula, 1 minúscula, 1 número, 1 símbolo
                                        </small>
                                        
                                        <!-- Indicador de fortaleza de contraseña -->
                                        <div class="password-strength-container mt-2" style="display: none;">
                                            <div class="password-strength-bar">
                                                <div class="password-strength-fill" id="strength-fill"></div>
                                            </div>
                                            <small class="password-strength-text" id="strength-text">Fortaleza de contraseña</small>
                                        </div>
                                        
                                        <!-- Requisitos de contraseña -->
                                        <div class="password-requirements mt-2" id="password-requirements" style="display: none;">
                                            <small class="requirement" id="length-req">
                                                <i class="fas fa-times text-danger"></i> Mínimo 8 caracteres
                                            </small><br>
                                            <small class="requirement" id="uppercase-req">
                                                <i class="fas fa-times text-danger"></i> Al menos una mayúscula
                                            </small><br>
                                            <small class="requirement" id="lowercase-req">
                                                <i class="fas fa-times text-danger"></i> Al menos una minúscula
                                            </small><br>
                                            <small class="requirement" id="number-req">
                                                <i class="fas fa-times text-danger"></i> Al menos un número
                                            </small><br>
                                            <small class="requirement" id="special-req">
                                                <i class="fas fa-times text-danger"></i> Al menos un símbolo especial
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Confirmar Nueva Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" 
                                                   placeholder="Confirme la nueva contraseña">
                                            <button class="btn btn-outline-secondary password-toggle" type="button" 
                                                    onclick="togglePassword('confirmar_password')" tabindex="-1">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback" id="confirm-password-feedback">
                                            Las contraseñas no coinciden
                                        </div>
                                        <small class="form-text text-muted">
                                            Debe coincidir con la nueva contraseña
                                        </small>
                                        
                                        <!-- Indicador de coincidencia -->
                                        <div class="password-match mt-2" id="password-match" style="display: none;">
                                            <small class="text-success">
                                                <i class="fas fa-check"></i> Las contraseñas coinciden
                                            </small>
                                        </div>
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
    
    <!-- Bootstrap JS ya se carga en el footer -->
    <!-- SweetAlert2 ya se carga en el footer -->
    <script>
        /**
         * Alternar visibilidad de contraseña
         * @param {string} inputId - ID del campo de contraseña
         */
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                button.setAttribute('title', 'Ocultar contraseña');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                button.setAttribute('title', 'Mostrar contraseña');
            }
        }

        /**
         * Validar fortaleza de contraseña
         * @param {string} password - Contraseña a validar
         * @returns {object} - Objeto con información de validación
         */
        function validatePasswordStrength(password) {
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /\d/.test(password),
                special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
            };

            const metCount = Object.values(requirements).filter(Boolean).length;
            let strength = 'weak';
            
            if (metCount >= 5) strength = 'strong';
            else if (metCount >= 4) strength = 'good';
            else if (metCount >= 3) strength = 'fair';

            return { requirements, strength, score: metCount };
        }

        /**
         * Actualizar indicadores visuales de fortaleza de contraseña
         * @param {string} password - Contraseña actual
         */
        function updatePasswordStrength(password) {
            const strengthContainer = document.querySelector('.password-strength-container');
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');
            const requirementsDiv = document.getElementById('password-requirements');

            if (password.length === 0) {
                strengthContainer.style.display = 'none';
                requirementsDiv.style.display = 'none';
                return;
            }

            strengthContainer.style.display = 'block';
            requirementsDiv.style.display = 'block';

            const validation = validatePasswordStrength(password);
            
            // Actualizar barra de fortaleza
            strengthFill.className = `password-strength-fill ${validation.strength}`;
            
            // Actualizar texto de fortaleza
            const strengthTexts = {
                weak: 'Débil',
                fair: 'Regular',
                good: 'Buena',
                strong: 'Fuerte'
            };
            strengthText.textContent = `Fortaleza: ${strengthTexts[validation.strength]}`;

            // Actualizar requisitos individuales
            const reqElements = {
                length: document.getElementById('length-req'),
                uppercase: document.getElementById('uppercase-req'),
                lowercase: document.getElementById('lowercase-req'),
                number: document.getElementById('number-req'),
                special: document.getElementById('special-req')
            };

            Object.keys(validation.requirements).forEach(req => {
                const element = reqElements[req];
                const icon = element.querySelector('i');
                
                if (validation.requirements[req]) {
                    element.classList.add('met');
                    icon.className = 'fas fa-check text-success';
                } else {
                    element.classList.remove('met');
                    icon.className = 'fas fa-times text-danger';
                }
            });
        }

        /**
         * Validar coincidencia de contraseñas
         */
        function validatePasswordMatch() {
            const newPassword = document.getElementById('nueva_password').value;
            const confirmPassword = document.getElementById('confirmar_password').value;
            const matchIndicator = document.getElementById('password-match');
            const confirmInput = document.getElementById('confirmar_password');

            if (confirmPassword.length === 0) {
                matchIndicator.style.display = 'none';
                confirmInput.classList.remove('is-valid', 'is-invalid');
                return true;
            }

            if (newPassword === confirmPassword && newPassword.length > 0) {
                matchIndicator.style.display = 'block';
                matchIndicator.innerHTML = '<small class="text-success"><i class="fas fa-check"></i> Las contraseñas coinciden</small>';
                confirmInput.classList.remove('is-invalid');
                confirmInput.classList.add('is-valid');
                return true;
            } else {
                matchIndicator.style.display = 'block';
                matchIndicator.innerHTML = '<small class="text-danger"><i class="fas fa-times"></i> Las contraseñas no coinciden</small>';
                confirmInput.classList.remove('is-valid');
                confirmInput.classList.add('is-invalid');
                return false;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editarUsuarioForm');
            const tipoUsuarioSelect = form.querySelector('select[name="tipo_usuario"]');
            const originalTipoUsuario = '<?= $usuario->tipo_usuario ?>';

            // Referencias a campos de contraseña
            const newPasswordInput = document.getElementById('nueva_password');
            const confirmPasswordInput = document.getElementById('confirmar_password');

            // Event listeners para validación de contraseña en tiempo real
            if (newPasswordInput) {
                newPasswordInput.addEventListener('input', function() {
                    updatePasswordStrength(this.value);
                    validatePasswordMatch();
                });

                newPasswordInput.addEventListener('focus', function() {
                    if (this.value.length > 0) {
                        document.querySelector('.password-strength-container').style.display = 'block';
                        document.getElementById('password-requirements').style.display = 'block';
                    }
                });
            }

            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', validatePasswordMatch);
            }

            // Agregar tooltips a los botones de visibilidad
            document.querySelectorAll('.password-toggle').forEach(button => {
                button.setAttribute('title', 'Mostrar contraseña');
            });

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submitBtn = document.getElementById('submitBtn');

                // Prevenir múltiples envíos
                if (submitBtn.disabled) {
                    return;
                }

                // Validación adicional para contraseñas
                const newPassword = newPasswordInput ? newPasswordInput.value : '';
                const confirmPassword = confirmPasswordInput ? confirmPasswordInput.value : '';
                
                // Si se ingresó una nueva contraseña, validar que sea segura
                if (newPassword.length > 0) {
                    const validation = validatePasswordStrength(newPassword);
                    if (validation.score < 4) {
                        Swal.fire({
                            title: 'Contraseña Insegura',
                            text: 'La contraseña debe cumplir al menos 4 de los 5 requisitos de seguridad.',
                            icon: 'warning',
                            confirmButtonColor: '#ffc107',
                            confirmButtonText: 'Entendido'
                        });
                        return;
                    }
                    
                    if (newPassword !== confirmPassword) {
                        Swal.fire({
                            title: 'Contraseñas No Coinciden',
                            text: 'La nueva contraseña y su confirmación deben ser idénticas.',
                            icon: 'error',
                            confirmButtonColor: '#dc3545',
                            confirmButtonText: 'Entendido'
                        });
                        confirmPasswordInput.focus();
                        return;
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
                    
                    // El token CSRF ya está incluido en el formulario
                    
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