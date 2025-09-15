<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Estilos originales conservados */
        .card-credential {
            transition: transform 0.2s;
            border: 1px solid #dee2e6;
            border-radius: 10px;
        }
        .card-credential:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .badge-admin {
            background-color: #dc3545;
        }
        .badge-trabajador {
            background-color: #0d6efd;
        }
        .user-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg,rgb(255, 136, 0),rgb(224, 126, 60));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            font-weight: bold;
        }
        
        /* Nuevos estilos para el efecto de label flotante (solo inputs) */
        .form-floating {
            position: relative;
            margin-bottom: 0.5rem;
        }
        
        .form-floating > label {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            padding: 1rem 0.75rem;
            pointer-events: none;
            border: 1px solid transparent;
            transform-origin: 0 0;
            transition: opacity 0.1s ease-in-out, transform 0.1s ease-in-out;
            color: #6e707e;
        }
        
        .form-floating > .form-control {
            height: calc(3.5rem + 2px);
            padding: 1rem 0.75rem;
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
        }
        
        .form-floating > .form-control:focus,
        .form-floating > .form-control:not(:placeholder-shown) {
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            transform: scale(0.85) translateY(-0.7rem) translateX(0.15rem);
            color: #4e73df;
            background-color: white;
            padding: 0 0.5rem;
            margin-left: 0.25rem;
            height: auto;
            width: auto;
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6e707e;
            cursor: pointer;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: #4e73df;
        }
        
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        /* Estilos para los selects (sin efecto flotante) */
        .form-select {
            height: calc(3.5rem + 2px);
            padding: 1rem 0.75rem;
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
        }
        
        .form-select:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #4e73df;
        }
        
        /* Nuevos estilos para ejemplos */
        .example-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.25rem;
            display: block;
        }
        
        .section-title {
            border-bottom: 2px solid #4e73df;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            color: #4e73df;
        }
        
        .form-section {
            background-color: #f8f9fc;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #4e73df;
        }
        
        /* Nuevos estilos para feedback de seguridad */
        .security-feedback {
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        
        .requirement-met {
            color: #28a745;
        }
        
        .requirement-unmet {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <?= $header ?>
    
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-id-card me-2"></i><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                            </h5>
                            <a href="<?= htmlspecialchars(base_url('usuarios'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= ($tipo_creacion === 'existente') ? 'active' : '' ?>" 
                                        id="existente-tab" data-bs-toggle="tab" data-bs-target="#existente" 
                                        type="button" role="tab">
                                    <i class="fas fa-user me-1"></i> Personal Existente
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= ($tipo_creacion === 'nuevo') ? 'active' : '' ?>" 
                                        id="nuevo-tab" data-bs-toggle="tab" data-bs-target="#nuevo" 
                                        type="button" role="tab">
                                    <i class="fas fa-user-plus me-1"></i> Nuevo Personal
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="myTabContent">
                            <!-- Formulario para personal existente -->
                            <div class="tab-pane fade <?= ($tipo_creacion === 'existente') ? 'show active' : '' ?>" 
                                 id="existente" role="tabpanel">
                                <form id="formExistente" class="needs-validation" novalidate>
                                    <input type="hidden" name="tipo_creacion" value="existente">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                    
                                    <div class="form-section">
                                        <h6 class="section-title">Datos del Personal</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="idpersona" class="form-label">Seleccionar Personal *</label>
                                                <select class="form-select" id="idpersona" name="idpersona" required>
                                                    <option value="">Seleccione una persona</option>
                                                    <?php foreach ($personas as $persona): ?>
                                                        <option value="<?= htmlspecialchars($persona->idpersona, ENT_QUOTES, 'UTF-8') ?>">
                                                            <?= htmlspecialchars($persona->nombres . ' ' . $persona->apellidos, ENT_QUOTES, 'UTF-8') ?> 
                                                            (<?= htmlspecialchars($persona->tipodoc, ENT_QUOTES, 'UTF-8') ?>: <?= htmlspecialchars($persona->numerodoc, ENT_QUOTES, 'UTF-8') ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="example-text">Ejemplo: Juan Pérez (DNI: 12345678)</span>
                                                <div class="invalid-feedback">Por favor seleccione una persona.</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="idcargo_existente" class="form-label">Cargo *</label>
                                                <select class="form-select" id="idcargo_existente" name="idcargo" required>
                                                    <option value="">Seleccione un cargo</option>
                                                    <?php foreach ($cargos as $cargo): ?>
                                                        <option value="<?= htmlspecialchars($cargo->idcargo, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($cargo->cargo, ENT_QUOTES, 'UTF-8') ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="example-text">Ejemplo: Administrador, Supervisor, Técnico</span>
                                                <div class="invalid-feedback">Por favor seleccione un cargo.</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-section">
                                        <h6 class="section-title">Credenciales de Acceso</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="nombreusuario_existente" 
                                                           name="nombreusuario" required placeholder=" " 
                                                           pattern="[a-zA-Z0-9_\-]{4,20}">
                                                    <label for="nombreusuario_existente">Nombre de Usuario *</label>
                                                </div>
                                                <span class="example-text">Ejemplo: juan.perez, jperez2023 (4-20 caracteres alfanuméricos)</span>
                                                <div class="invalid-feedback" id="error-nombreusuario-existente">
                                                    Por favor ingrese un nombre de usuario válido (4-20 caracteres alfanuméricos).
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="email" class="form-control" id="email_existente" 
                                                           name="email" required placeholder=" " 
                                                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                                    <label for="email_existente">Email *</label>
                                                </div>
                                                <span class="example-text">Ejemplo: juan.perez@empresa.com</span>
                                                <div class="invalid-feedback">Por favor ingrese un email válido.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating position-relative">
                                                    <input type="password" class="form-control" id="password_existente" 
                                                           name="password" required placeholder=" " minlength="8">
                                                    <label for="password_existente">Contraseña *</label>
                                                    <button type="button" class="password-toggle" id="togglePasswordExistente">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                <span class="example-text">Ejemplo: ClaveSegura123! (mín. 8 caracteres, 1 mayúscula, 1 minúscula, 1 número, 1 símbolo)</span>
                                                
                                                <div class="security-feedback">
                                                    <div id="password-strength-existente" class="password-strength"></div>
                                                    <div id="password-requirements-existente">
                                                        <small class="requirement-unmet" id="length-existente">• Mínimo 8 caracteres</small><br>
                                                        <small class="requirement-unmet" id="uppercase-existente">• Al menos una mayúscula</small><br>
                                                        <small class="requirement-unmet" id="lowercase-existente">• Al menos una minúscula</small><br>
                                                        <small class="requirement-unmet" id="number-existente">• Al menos un número</small><br>
                                                        <small class="requirement-unmet" id="special-existente">• Al menos un símbolo</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="invalid-feedback" id="error-password-existente">
                                                    La contraseña no cumple con los requisitos de seguridad.
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating position-relative">
                                                    <input type="password" class="form-control" id="confirm_password_existente" 
                                                           name="confirm_password" required placeholder=" ">
                                                    <label for="confirm_password_existente">Confirmar Contraseña *</label>
                                                    <button type="button" class="password-toggle" id="toggleConfirmPasswordExistente">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                <span class="example-text">Debe coincidir exactamente con la contraseña anterior</span>
                                                <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Crear Credenciales
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Formulario para nuevo personal -->
                            <div class="tab-pane fade <?= ($tipo_creacion === 'nuevo') ? 'show active' : '' ?>" 
                                 id="nuevo" role="tabpanel">
                                <form id="formNuevo" class="needs-validation" novalidate>
                                    <input type="hidden" name="tipo_creacion" value="nuevo">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                    
                                    <div class="form-section">
                                        <h6 class="section-title">Datos Personales</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="nombres" 
                                                           name="nombres" required placeholder=" " 
                                                           pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]{2,50}">
                                                    <label for="nombres">Nombres *</label>
                                                </div>
                                                <span class="example-text">Ejemplo: Juan Carlos (solo letras y espacios, 2-50 caracteres)</span>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese nombres válidos (solo letras y espacios, 2-50 caracteres).
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="apellidos" 
                                                           name="apellidos" required placeholder=" " 
                                                           pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]{2,50}">
                                                    <label for="apellidos">Apellidos *</label>
                                                </div>
                                                <span class="example-text">Ejemplo: Pérez García (solo letras y espacios, 2-50 caracteres)</span>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese apellidos válidos (solo letras y espacios, 2-50 caracteres).
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-floating">
                                                    <select class="form-select" id="tipodoc" name="tipodoc" required>
                                                        <option value="DNI">DNI</option>
                                                        <option value="Carne de Extranjería">Carne de Extranjería</option>
                                                        <option value="Pasaporte">Pasaporte</option>
                                                    </select>
                                                    <label for="tipodoc">Tipo de Documento *</label>
                                                </div>
                                                <span class="example-text">Seleccione el tipo de documento</span>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="numerodoc" 
                                                           name="numerodoc" required placeholder=" ">
                                                    <label for="numerodoc">Número de Documento *</label>
                                                </div>
                                                <span class="example-text" id="example-numerodoc">Ejemplo: 12345678 (8 dígitos)</span>
                                                <div class="invalid-feedback" id="error-numerodoc">
                                                    Por favor ingrese un número de documento válido.
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-floating">
                                                    <select class="form-select" id="idcargo_nuevo" name="idcargo" required>
                                                        <option value="">Seleccione un cargo</option>
                                                        <?php foreach ($cargos as $cargo): ?>
                                                            <option value="<?= htmlspecialchars($cargo->idcargo, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($cargo->cargo, ENT_QUOTES, 'UTF-8') ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label for="idcargo_nuevo">Cargo *</label>
                                                </div>
                                                <span class="example-text">Ejemplo: Administrador, Supervisor, Técnico</span>
                                                <div class="invalid-feedback">Por favor seleccione un cargo.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="telprincipal" 
                                                           name="telprincipal" required placeholder=" " 
                                                           pattern="[0-9]{7,15}">
                                                    <label for="telprincipal">Teléfono Principal *</label>
                                                </div>
                                                <span class="example-text">Ejemplo: 987654321 (7-15 dígitos)</span>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese un teléfono válido (7-15 dígitos).
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="telalternativo" 
                                                           name="telalternativo" placeholder=" " 
                                                           pattern="[0-9]{7,15}">
                                                    <label for="telalternativo">Teléfono Alternativo</label>
                                                </div>
                                                <span class="example-text">Ejemplo: 912345678 (7-15 dígitos, opcional)</span>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese un teléfono válido (7-15 dígitos).
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-8 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="direccion" 
                                                           name="direccion" required placeholder=" " 
                                                           pattern="[A-Za-z0-9áéíóúÁÉÍÓÚñÑ\s.,\-]{5,150}">
                                                    <label for="direccion">Dirección *</label>
                                                </div>
                                                <span class="example-text">Ejemplo: Av. Principal 123, Urb. Las Flores</span>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese una dirección válida.
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="referencia" 
                                                           name="referencia" placeholder=" " maxlength="150">
                                                    <label for="referencia">Referencia</label>
                                                </div>
                                                <span class="example-text">Ejemplo: Frente al parque (opcional)</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-section">
                                        <h6 class="section-title">Credenciales de Acceso</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="nombreusuario_nuevo" 
                                                           name="nombreusuario" required placeholder=" " 
                                                           pattern="[a-zA-Z0-9_\-]{4,20}">
                                                    <label for="nombreusuario_nuevo">Nombre de Usuario *</label>
                                                </div>
                                                <span class="example-text">Ejemplo: juan.perez, jperez2023 (4-20 caracteres alfanuméricos)</span>
                                                <div class="invalid-feedback" id="error-nombreusuario-nuevo">
                                                    Por favor ingrese un nombre de usuario válido (4-20 caracteres alfanuméricos).
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="email" class="form-control" id="email_nuevo" 
                                                           name="email" required placeholder=" " 
                                                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                                    <label for="email_nuevo">Email *</label>
                                                </div>
                                                <span class="example-text">Ejemplo: juan.perez@empresa.com</span>
                                                <div class="invalid-feedback">Por favor ingrese un email válido.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating position-relative">
                                                    <input type="password" class="form-control" id="password_nuevo" 
                                                           name="password" required placeholder=" " minlength="8">
                                                    <label for="password_nuevo">Contraseña *</label>
                                                    <button type="button" class="password-toggle" id="togglePasswordNuevo">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                <span class="example-text">Ejemplo: ClaveSegura123! (mín. 8 caracteres, 1 mayúscula, 1 minúscula, 1 número, 1 símbolo)</span>
                                                
                                                <div class="security-feedback">
                                                    <div id="password-strength-nuevo" class="password-strength"></div>
                                                    <div id="password-requirements-nuevo">
                                                        <small class="requirement-unmet" id="length-nuevo">• Mínimo 8 caracteres</small><br>
                                                        <small class="requirement-unmet" id="uppercase-nuevo">• Al menos una mayúscula</small><br>
                                                        <small class="requirement-unmet" id="lowercase-nuevo">• Al menos una minúscula</small><br>
                                                        <small class="requirement-unmet" id="number-nuevo">• Al menos un número</small><br>
                                                        <small class="requirement-unmet" id="special-nuevo">• Al menos un símbolo</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="invalid-feedback" id="error-password-nuevo">
                                                    La contraseña no cumple con los requisitos de seguridad.
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating position-relative">
                                                    <input type="password" class="form-control" id="confirm_password_nuevo" 
                                                           name="confirm_password" required placeholder=" ">
                                                    <label for="confirm_password_nuevo">Confirmar Contraseña *</label>
                                                    <button type="button" class="password-toggle" id="toggleConfirmPasswordNuevo">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                <span class="example-text">Debe coincidir exactamente con la contraseña anterior</span>
                                                <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Crear Personal y Credenciales
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= $footer ?>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Función para mostrar alertas con SweetAlert2
            function showAlert(icon, title, text) {
                Swal.fire({
                    icon: icon,
                    title: title,
                    text: text,
                    confirmButtonColor: '#4e73df'
                });
            }
            
            // Actualizar ejemplo según tipo de documento
            $('#tipodoc').on('change', function() {
                validarNumeroDocumento();
                updateDocumentExample();
            });
            
            function updateDocumentExample() {
                const tipoDoc = $('#tipodoc').val();
                let exampleText = '';
                
                if (tipoDoc === 'DNI') {
                    exampleText = 'Ejemplo: 12345678 (8 dígitos)';
                } else if (tipoDoc === 'Carne de Extranjería') {
                    exampleText = 'Ejemplo: A12345678 (8-12 caracteres alfanuméricos)';
                } else if (tipoDoc === 'Pasaporte') {
                    exampleText = 'Ejemplo: AB123456 (6-12 caracteres alfanuméricos)';
                }
                
                $('#example-numerodoc').text(exampleText);
            }
            
            // Función para toggle de visibilidad de contraseña
            function setupPasswordToggle(passwordId, toggleId) {
                const passwordInput = document.getElementById(passwordId);
                const toggleButton = document.getElementById(toggleId);
                
                toggleButton.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Cambiar icono
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }
            
            // Configurar toggles para todas las contraseñas
            setupPasswordToggle('password_existente', 'togglePasswordExistente');
            setupPasswordToggle('confirm_password_existente', 'toggleConfirmPasswordExistente');
            setupPasswordToggle('password_nuevo', 'togglePasswordNuevo');
            setupPasswordToggle('confirm_password_nuevo', 'toggleConfirmPasswordNuevo');
            
            // Validar número de documento según tipo seleccionado
            $('#tipodoc').on('change', function() {
                validarNumeroDocumento();
            });
            
            $('#numerodoc').on('input', function() {
                validarNumeroDocumento();
            });
            
            function validarNumeroDocumento() {
                const tipoDoc = $('#tipodoc').val();
                const numeroDoc = $('#numerodoc').val();
                let esValido = false;
                let mensajeError = '';
                
                if (tipoDoc === 'DNI') {
                    esValido = /^\d{8}$/.test(numeroDoc);
                    mensajeError = 'El DNI debe tener exactamente 8 dígitos numéricos.';
                } else if (tipoDoc === 'Carne de Extranjería') {
                    esValido = /^[a-zA-Z0-9]{8,12}$/.test(numeroDoc);
                    mensajeError = 'El Carné de Extranjería debe tener entre 8 y 12 caracteres alfanuméricos.';
                } else if (tipoDoc === 'Pasaporte') {
                    esValido = /^[a-zA-Z0-9]{6,12}$/.test(numeroDoc);
                    mensajeError = 'El Pasaporte debe tener entre 6 y 12 caracteres alfanuméricos.';
                }
                
                if (numeroDoc && !esValido) {
                    $('#numerodoc').get(0).setCustomValidity(mensajeError);
                    $('#error-numerodoc').text(mensajeError);
                } else {
                    $('#numerodoc').get(0).setCustomValidity('');
                }
            }
            
            // Validar fortaleza de contraseña y actualizar requisitos visualmente
            $('#password_existente, #password_nuevo').on('input', function() {
                const password = $(this).val();
                const formType = $(this).attr('id') === 'password_existente' ? 'existente' : 'nuevo';
                const strengthBar = $(this).closest('.row').find('.password-strength');
                const strength = calcularFortalezaPassword(password);
                
                // Actualizar barra de fortaleza
                strengthBar.css('width', strength.percentage + '%');
                strengthBar.removeClass('bg-danger bg-warning bg-success').addClass(strength.class);
                
                // Actualizar visualización de requisitos
                updatePasswordRequirements(password, formType);
                
                // Validar si cumple requisitos
                if (password.length >= 8 && strength.score >= 3) {
                    $(this).get(0).setCustomValidity('');
                } else {
                    $(this).get(0).setCustomValidity('La contraseña no cumple con los requisitos de seguridad');
                }
            });
            
            function updatePasswordRequirements(password, formType) {
                // Longitud
                if (password.length >= 8) {
                    $(`#length-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
                } else {
                    $(`#length-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
                }
                
                // Mayúscula
                if (/[A-Z]/.test(password)) {
                    $(`#uppercase-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
                } else {
                    $(`#uppercase-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
                }
                
                // Minúscula
                if (/[a-z]/.test(password)) {
                    $(`#lowercase-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
                } else {
                    $(`#lowercase-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
                }
                
                // Número
                if (/[0-9]/.test(password)) {
                    $(`#number-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
                } else {
                    $(`#number-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
                }
                
                // Símbolo
                if (/[^A-Za-z0-9]/.test(password)) {
                    $(`#special-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
                } else {
                    $(`#special-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
                }
            }
            
            function calcularFortalezaPassword(password) {
                let score = 0;
                
                // Longitud mínima
                if (password.length >= 8) score++;
                
                // Contiene letra minúscula
                if (/[a-z]/.test(password)) score++;
                
                // Contiene letra mayúscula
                if (/[A-Z]/.test(password)) score++;
                
                // Contiene número
                if (/[0-9]/.test(password)) score++;
                
                // Contiene símbolo
                if (/[^A-Za-z0-9]/.test(password)) score++;
                
                // Calcular porcentaje y clase
                let percentage = (score / 5) * 100;
                let strengthClass = 'bg-danger';
                
                if (score >= 4) {
                    strengthClass = 'bg-success';
                } else if (score >= 3) {
                    strengthClass = 'bg-warning';
                }
                
                return {
                    score: score,
                    percentage: percentage,
                    class: strengthClass
                };
            }
            
            // Validar que las contraseñas coincidan
            $('#confirm_password_existente, #confirm_password_nuevo').on('input', function() {
                const formId = $(this).closest('form').attr('id');
                const passwordField = formId === 'formExistente' ? '#password_existente' : '#password_nuevo';
                const password = $(passwordField).val();
                const confirmPassword = $(this).val();
                
                if (password !== confirmPassword) {
                    $(this).get(0).setCustomValidity('Las contraseñas no coinciden');
                } else {
                    $(this).get(0).setCustomValidity('');
                }
            });
            
            // Validación de formularios
            $('#formExistente, #formNuevo').on('submit', function(e) {
                e.preventDefault();
                
                // Validar número de documento para formulario nuevo
                if ($(this).attr('id') === 'formNuevo') {
                    validarNumeroDocumento();
                }
                
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    this.classList.add('was-validated');
                    
                    // Mostrar alerta de error
                    showAlert('error', 'Error de validación', 'Por favor complete todos los campos correctamente.');
                    return;
                }
                
                // Validar que las contraseñas coincidan
                const formId = $(this).attr('id');
                const passwordField = formId === 'formExistente' ? '#password_existente' : '#password_nuevo';
                const confirmField = formId === 'formExistente' ? '#confirm_password_existente' : '#confirm_password_nuevo';
                
                const password = $(passwordField).val();
                const confirmPassword = $(confirmField).val();
                
                if (password !== confirmPassword) {
                    $(confirmField).get(0).setCustomValidity('Las contraseñas no coinciden');
                    $(confirmField).get(0).reportValidity();
                    showAlert('error', 'Contraseñas no coinciden', 'Las contraseñas ingresadas no coinciden.');
                    return;
                } else {
                    $(confirmField).get(0).setCustomValidity('');
                }
                
                // Validar fortaleza de contraseña
                const strength = calcularFortalezaPassword(password);
                if (password.length < 8 || strength.score < 3) {
                    $(passwordField).get(0).setCustomValidity('La contraseña no cumple con los requisitos de seguridad');
                    $(passwordField).get(0).reportValidity();
                    showAlert('error', 'Contraseña débil', 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo.');
                    return;
                }
                
                // Mostrar confirmación con SweetAlert
                Swal.fire({
                    title: '¿Está seguro?',
                    text: '¿Desea guardar los datos del usuario?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Enviar formulario
                        guardarUsuario(this);
                    }
                });
            });
            
            // Resetear validación al cambiar de pestaña
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                $('#formExistente, #formNuevo').removeClass('was-validated');
            });
            
            // Función para enviar el formulario
            function guardarUsuario(form) {
                const formData = new FormData(form);
                
                // Mostrar loading
                Swal.fire({
                    title: 'Procesando',
                    text: 'Guardando información del usuario...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                
                $.ajax({
                    url: '<?= base_url('usuarios/guardar') ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            showAlert('success', '¡Éxito!', 'Usuario creado correctamente.');
                            // Limpiar formulario después del éxito
                            $(form)[0].reset();
                            $(form).removeClass('was-validated');
                            $('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                            setTimeout(function() {
                                window.location.href = '<?= base_url('usuarios') ?>';
                            }, 2000);
                        } else {
                            // Mostrar error pero mantener los datos del formulario
                            showAlert('error', 'Error de validación', response.message || 'Por favor revise los campos marcados en rojo.');
                            if (response.errors) {
                                console.error(response.errors);
                                // Mostrar errores específicos en el formulario
                                mostrarErroresFormulario(response.errors, form);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        showAlert('error', 'Error', 'Error en la solicitud: ' + error);
                    }
                });
            }
            
            function mostrarErroresFormulario(errors, form) {
                // Solo limpiar errores en el formulario específico
                $(form).find('.is-invalid').removeClass('is-invalid');
                $(form).find('.invalid-feedback').hide();
                
                // Mostrar nuevos errores
                for (const field in errors) {
                    const errorMessage = errors[field];
                    const inputElement = $(form).find(`[name="${field}"]`);
                    
                    if (inputElement.length) {
                        inputElement.addClass('is-invalid');
                        let feedbackElement = inputElement.siblings('.invalid-feedback');
                        if (feedbackElement.length === 0) {
                            feedbackElement = inputElement.parent().find('.invalid-feedback');
                        }
                        if (feedbackElement.length > 0) {
                            feedbackElement.text(errorMessage).show();
                        }
                        
                        // Scroll al primer campo con error
                        if ($(form).find('.is-invalid').first().is(inputElement)) {
                            inputElement[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                            inputElement.focus();
                        }
                    }
                }
                
                // Marcar el formulario como validado para mostrar estilos
                $(form).addClass('was-validated');
            }
            
            // Cargar datos de persona seleccionada
            $('#idpersona').on('change', function() {
                const idpersona = $(this).val();
                if (idpersona) {
                    $.get('<?= base_url('usuarios/obtenerPersona/') ?>' + idpersona, function(response) {
                        if (response.success) {
                            // Puedes usar estos datos para prellenar información si es necesario
                            console.log('Datos de persona:', response.data);
                        }
                    });
                }
            });
            
            // Inicializar ejemplos de documento
            updateDocumentExample();
        });
    </script>
</body>
</html>