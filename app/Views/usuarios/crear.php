<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 2px;
        }
        .requirement-list {
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <?= $header ?>
    
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>Crear Nueva Credencial
                        </h4>
                    </div>
                    <div class="card-body">
                        <form id="crearUsuarioForm" novalidate>
                            <!-- Tipo de Creación -->
                            <div class="form-section">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user-check me-2"></i>Tipo de Creación
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipo_creacion" 
                                                   id="tipo_existente" value="existente" checked>
                                            <label class="form-check-label" for="tipo_existente">
                                                Persona Existente
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipo_creacion" 
                                                   id="tipo_nuevo" value="nuevo">
                                            <label class="form-check-label" for="tipo_nuevo">
                                                Nueva Persona
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección Persona Existente -->
                            <div id="seccionExistente">
                                <div class="form-section">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-users me-2"></i>Seleccionar Persona Existente
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="form-label">Persona *</label>
                                            <select class="form-select" id="idpersona" name="idpersona" required>
                                                <option value="">Seleccionar persona...</option>
                                                <?php foreach ($personas as $persona): ?>
                                                    <option value="<?= $persona->idpersona ?>">
                                                        <?= $persona->apellidos . ', ' . $persona->nombres ?> 
                                                        (<?= $persona->tipodoc ?>: <?= $persona->numerodoc ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">Debe seleccionar una persona</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección Nueva Persona -->
                            <div id="seccionNueva" style="display: none;">
                                <div class="form-section">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-user-plus me-2"></i>Datos de la Nueva Persona
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Nombres *</label>
                                            <input type="text" class="form-control" name="nombres" 
                                                   placeholder="Ingrese los nombres">
                                            <div class="invalid-feedback">Los nombres son obligatorios</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Apellidos *</label>
                                            <input type="text" class="form-control" name="apellidos" 
                                                   placeholder="Ingrese los apellidos">
                                            <div class="invalid-feedback">Los apellidos son obligatorios</div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Tipo de Documento</label>
                                            <select class="form-select" name="tipodoc">
                                                <option value="DNI">DNI</option>
                                                <option value="Carne de Extranjería">Carne de Extranjería</option>
                                                <option value="Pasaporte">Pasaporte</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Número de Documento *</label>
                                            <input type="text" class="form-control" name="numerodoc" 
                                                   placeholder="Ej: 12345678">
                                            <div class="invalid-feedback">El número de documento es obligatorio</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Teléfono Principal *</label>
                                            <input type="tel" class="form-control" name="telprincipal" 
                                                   placeholder="Ej: 987654321">
                                            <div class="invalid-feedback">El teléfono principal es obligatorio</div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Teléfono Alternativo</label>
                                            <input type="tel" class="form-control" name="telalternativo" 
                                                   placeholder="Opcional">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Dirección *</label>
                                            <input type="text" class="form-control" name="direccion" 
                                                   placeholder="Dirección completa">
                                            <div class="invalid-feedback">La dirección es obligatoria</div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <label class="form-label">Referencia</label>
                                            <textarea class="form-control" name="referencia" 
                                                      placeholder="Referencias adicionales" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección Credenciales -->
                            <div class="form-section">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-key me-2"></i>Credenciales de Acceso
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Cargo *</label>
                                        <select class="form-select" name="idcargo" required>
                                            <option value="">Seleccionar cargo...</option>
                                            <?php foreach ($cargos as $cargo): ?>
                                                <option value="<?= $cargo->idcargo ?>"><?= $cargo->cargo ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Debe seleccionar un cargo</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre de Usuario *</label>
                                        <input type="text" class="form-control" name="nombreusuario" 
                                               placeholder="Mínimo 10 caracteres" required>
                                        <div class="invalid-feedback">El nombre de usuario es obligatorio</div>
                                        <small class="form-text text-muted">Solo letras, números y guiones bajos</small>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Email *</label>
                                        <input type="email" class="form-control" name="email" 
                                               placeholder="usuario@ejemplo.com" required>
                                        <div class="invalid-feedback">Debe ingresar un email válido</div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Contraseña *</label>
                                        <input type="password" class="form-control" name="password" 
                                               id="password" placeholder="Mínimo 8 caracteres" required>
                                        <div class="password-strength" id="passwordStrength"></div>
                                        <div class="invalid-feedback">La contraseña es obligatoria</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirmar Contraseña *</label>
                                        <input type="password" class="form-control" name="confirm_password" 
                                               placeholder="Repita la contraseña" required>
                                        <div class="invalid-feedback">Las contraseñas no coinciden</div>
                                    </div>
                                </div>

                                <div class="requirement-list mt-2">
                                    <small class="text-muted">La contraseña debe contener:</small>
                                    <ul class="list-unstyled">
                                        <li><small><span id="reqUppercase">❌</span> Al menos una mayúscula</small></li>
                                        <li><small><span id="reqLowercase">❌</span> Al menos una minúscula</small></li>
                                        <li><small><span id="reqNumber">❌</span> Al menos un número</small></li>
                                        <li><small><span id="reqSpecial">❌</span> Al menos un símbolo especial</small></li>
                                        <li><small><span id="reqLength">❌</span> Mínimo 8 caracteres</small></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="<?= base_url('usuarios') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save me-1"></i> Crear Credencial
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= $footer ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('crearUsuarioForm');
            const tipoExistente = document.getElementById('tipo_existente');
            const tipoNuevo = document.getElementById('tipo_nuevo');
            const seccionExistente = document.getElementById('seccionExistente');
            const seccionNueva = document.getElementById('seccionNueva');
            const passwordInput = document.getElementById('password');
            const passwordStrength = document.getElementById('passwordStrength');

            // Toggle entre secciones
            tipoExistente.addEventListener('change', toggleSections);
            tipoNuevo.addEventListener('change', toggleSections);

            function toggleSections() {
                if (tipoExistente.checked) {
                    seccionExistente.style.display = 'block';
                    seccionNueva.style.display = 'none';
                    // Limpiar validación de sección nueva
                    document.querySelectorAll('#seccionNueva input, #seccionNueva select').forEach(input => {
                        input.removeAttribute('required');
                    });
                } else {
                    seccionExistente.style.display = 'none';
                    seccionNueva.style.display = 'block';
                    // Agregar required a sección nueva
                    document.querySelectorAll('#seccionNueva input[type="text"], #seccionNueva input[type="tel"]').forEach(input => {
                        if (!input.name.includes('telalternativo') && !input.name.includes('referencia')) {
                            input.setAttribute('required', 'true');
                        }
                    });
                }
            }

            // Validación de contraseña en tiempo real
            passwordInput.addEventListener('input', validatePassword);

            function validatePassword() {
                const password = passwordInput.value;
                const hasUppercase = /[A-Z]/.test(password);
                const hasLowercase = /[a-z]/.test(password);
                const hasNumber = /[0-9]/.test(password);
                const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
                const hasLength = password.length >= 8;

                // Actualizar indicadores
                document.getElementById('reqUppercase').textContent = hasUppercase ? '✅' : '❌';
                document.getElementById('reqLowercase').textContent = hasLowercase ? '✅' : '❌';
                document.getElementById('reqNumber').textContent = hasNumber ? '✅' : '❌';
                document.getElementById('reqSpecial').textContent = hasSpecial ? '✅' : '❌';
                document.getElementById('reqLength').textContent = hasLength ? '✅' : '❌';

                // Actualizar barra de fuerza
                let strength = 0;
                if (hasUppercase) strength++;
                if (hasLowercase) strength++;
                if (hasNumber) strength++;
                if (hasSpecial) strength++;
                if (hasLength) strength++;

                const colors = ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#198754'];
                passwordStrength.style.width = (strength * 20) + '%';
                passwordStrength.style.backgroundColor = colors[strength - 1] || '#dc3545';
            }

            // Validación del formulario
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }

                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creando...';

                try {
                    const formData = new FormData(form);
                    const response = await fetch('<?= base_url('usuarios/guardar') ?>', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Credencial creada exitosamente');
                        window.location.href = '<?= base_url('usuarios') ?>';
                    } else {
                        alert('Error: ' + data.message);
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const input = form.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    input.nextElementSibling.textContent = data.errors[field];
                                }
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al crear la credencial');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Crear Credencial';
                }
            });

            // Validación en tiempo real
            form.querySelectorAll('input, select').forEach(input => {
                input.addEventListener('blur', () => {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
</body>
</html>