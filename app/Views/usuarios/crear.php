<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/usuarios-crear.css') ?>">

 

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
                            <form id="formExistente" class="needs-validation" novalidate action="<?= base_url('/usuarios/guardar') ?>" method="POST">
                                <input type="hidden" name="tipo_creacion" value="existente">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                                
                                <!-- SECCIÓN 1: DATOS PERSONALES -->
                                <div class="form-section">
                                    <h6 class="section-title">
                                        <i class="fas fa-user-circle"></i>
                                        Datos Personales
                                    </h6>
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
                                    </div>
                                </div>
                                
                                <!-- SECCIÓN 2: SELECCIÓN DE ROL -->
                                <div class="form-section">
                                    <h6 class="section-title">
                                        <i class="fas fa-user-tag"></i>
                                        Selección de Rol
                                    </h6>
                                    <div class="row">
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
                                
                                <!-- SECCIÓN 3: CREDENCIALES DE ACCESO -->
                                <div class="form-section">
                                    <h6 class="section-title">
                                        <i class="fas fa-key"></i>
                                        Credenciales de Acceso
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="nombreusuario_existente" 
                                                       name="nombreusuario" required placeholder=" " 
                                                       pattern="[a-zA-Z0-9_-]{4,20}">
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
                                                       name="email" required placeholder=" ">
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
                            <form id="formNuevo" class="needs-validation" novalidate action="<?= base_url('/usuarios/guardar') ?>" method="POST">
                                <input type="hidden" name="tipo_creacion" value="nuevo">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                                
                                <!-- SECCIÓN 1: DATOS PERSONALES -->
                                <div class="form-section">
                                    <h6 class="section-title">
                                        <i class="fas fa-user-circle"></i>
                                        Datos Personales
                                    </h6>
                                    
                                    <!-- Información de Documento -->
                                    <div class="form-subsection mb-4">
                                        <h6 class="subsection-title">
                                            <i class="fas fa-id-card text-primary"></i>
                                            Documento de Identidad
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="tipodoc" 
                                                           name="tipodoc" value="DNI" readonly required>
                                                    <label for="tipodoc">Tipo de Documento *</label>
                                                </div>
                                                <span class="example-text">Solo se acepta DNI</span>
                                            </div>
                                            <div class="col-md-8 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="numerodoc" 
                                                           name="numerodoc" required placeholder=" " maxlength="8" pattern="[0-9]{8}">
                                                    <label for="numerodoc">Número de Documento *</label>
                                                </div>
                                                <span class="example-text" id="example-numerodoc">Ejemplo: 12345678 (8 dígitos)</span>
                                                <div class="valid-feedback" id="success-numerodoc" style="display: none;">
                                                    <i class="fas fa-check-circle text-success"></i> DNI válido encontrado en RENIEC
                                                </div>
                                                <div class="invalid-feedback" id="error-numerodoc">
                                                    Por favor ingrese un número de documento válido.
                                                </div>
                                                <div class="text-info small" id="loading-numerodoc" style="display: none;">
                                                    <i class="fas fa-spinner fa-spin"></i> Validando DNI con RENIEC...
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Información Personal -->
                                    <div class="form-subsection mb-4">
                                        <h6 class="subsection-title">
                                            <i class="fas fa-user text-success"></i>
                                            Información Personal
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="nombres" 
                                                           name="nombres" required placeholder=" " 
                                                           minlength="2" maxlength="50" readonly required>
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
                                                           minlength="2" maxlength="50" readonly required>
                                                    <label for="apellidos">Apellidos *</label>
                                                </div>
                                                <span class="example-text">Ejemplo: Pérez García (solo letras y espacios, 2-50 caracteres)</span>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese apellidos válidos (solo letras y espacios, 2-50 caracteres).
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Información de Contacto -->
                                    <div class="form-subsection mb-4">
                                        <h6 class="subsection-title">
                                            <i class="fas fa-phone text-info"></i>
                                            Información de Contacto
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="telprincipal">Teléfono Principal *</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">+51</span>
                                                    </div>
                                                    <input type="tel" class="form-control" id="telprincipal" 
                                                           name="telprincipal" required placeholder="987654321" 
                                                           pattern="[0-9]{9}" maxlength="9">
                                                </div>
                                                <span class="example-text">Ejemplo: 987654321 (9 dígitos)</span>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese un teléfono válido.
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="telalternativo">Teléfono Alternativo</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">+51</span>
                                                    </div>
                                                    <input type="tel" class="form-control" id="telalternativo" 
                                                           name="telalternativo" placeholder="912345678" 
                                                           pattern="[0-9]{9}" maxlength="9">
                                                </div>
                                                <span class="example-text">Ejemplo: 912345678 (9 dígitos, opcional)</span>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese un teléfono válido.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="direccion" 
                                                       name="direccion" required placeholder=" " 
                                                       minlength="5" maxlength="150">
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
                                
                                <!-- SECCIÓN 2: SELECCIÓN DE ROL -->
                                <div class="form-section">
                                    <h6 class="section-title">
                                        <i class="fas fa-user-tag"></i>
                                        Selección de Rol
                                    </h6>
                                    <div class="row">
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
                                </div>
                                
                                <!-- SECCIÓN 3: CREDENCIALES DE ACCESO -->
                                <div class="form-section">
                                    <h6 class="section-title">
                                        <i class="fas fa-key"></i>
                                        Credenciales de Acceso
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="nombreusuario_nuevo" 
                                                       name="nombreusuario" required placeholder=" " 
                                                       pattern="[a-zA-Z0-9_-]{4,20}">
                                                <label for="nombreusuario_nuevo">Nombre de Usuario *</label>
                                            </div>
                                            <span class="example-text">Ejemplo: juan.perez, jperez2023 (4-20 caracteres alfanuméricos)</span>
                                            <div class="invalid-feedback" id="error-nombreusuario-nuevo">
                                                Por favor ingrese un nombre de usuario válido (4-20 caracteres alfanuméricos).
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-floating position-relative">
                                                <input type="email" class="form-control" id="email_nuevo" 
                                                       name="email" required placeholder=" ">
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
    
    // Función para actualizar ejemplo de documento (ya no necesaria con campo readonly)
    function updateDocumentExample() {
        // Solo DNI permitido - campo ahora es readonly
        $('#example-numerodoc').text('Ejemplo: 12345678 (8 dígitos)');
    }
    
    // Función simple para toggle de contraseña
    $(document).ready(function() {
        $('.password-toggle').click(function() {
            const $button = $(this);
            const $input = $button.siblings('input');
            const $icon = $button.find('i');
            
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                $input.attr('type', 'password');
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });
    
    // Validación de número de documento (tipodoc ahora es readonly)
    
    $('#numerodoc').on('input', function() {
        validarNumeroDocumento();
    });
    
    function validarNumeroDocumento() {
        const numeroDoc = $('#numerodoc').val();
        const esValido = /^\d{8}$/.test(numeroDoc);
        const mensajeError = 'El DNI debe tener exactamente 8 dígitos numéricos.';
        
        if (numeroDoc && !esValido) {
            $('#numerodoc').get(0).setCustomValidity(mensajeError);
            $('#error-numerodoc').text(mensajeError);
        } else {
            $('#numerodoc').get(0).setCustomValidity('');
        }
    }
    
    // Funciones para manejar el estado del botón
    function setButtonLoading($button, originalText) {
        // Solo poner en "procesando" si el formulario es válido
        $button.prop('disabled', true);
        $button.data('original-text', originalText);
        $button.html('<i class="fas fa-spinner fa-spin me-1"></i> En proceso...');
        
        // Timeout de seguridad: resetear botón después de 30 segundos
        setTimeout(() => {
            if ($button.prop('disabled')) {
                console.warn('Timeout de seguridad: reseteando botón');
                resetButton($button, originalText);
            }
        }, 30000);
    }
    
    function resetButton($button, originalText) {
        $button.prop('disabled', false);
        // Restaurar el texto original
        $button.html(originalText);
    }
    
    // Función de seguridad para resetear botones en caso de error
    function resetAllButtons() {
        $('#formExistente button[type="submit"]').each(function() {
            $(this).prop('disabled', false);
            $(this).html('<i class="fas fa-save me-1"></i> Crear Credenciales');
        });
        $('#formNuevo button[type="submit"]').each(function() {
            $(this).prop('disabled', false);
            $(this).html('<i class="fas fa-save me-1"></i> Crear Personal y Credenciales');
        });
    }
    
    // Validar fortaleza de contraseña y actualizar requisitos visualmente
    $('#password_existente, #password_nuevo').on('input', function() {
        const password = $(this).val();
        const formType = $(this).attr('id') === 'password_existente' ? 'existente' : 'nuevo';
        const strengthBar = $(this).closest('.row').find('.password-strength');
        const strength = calcularFortalezaPassword(password);
        
        console.log('Validando contraseña:', password, 'FormType:', formType);
        
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
        console.log('Actualizando requisitos para:', formType, 'Contraseña:', password);
        
        // Longitud
        if (password.length >= 8) {
            $(`#length-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
            console.log('✓ Longitud cumplida');
        } else {
            $(`#length-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
            console.log('✗ Longitud no cumplida');
        }
        
        // Mayúscula
        if (/[A-Z]/.test(password)) {
            $(`#uppercase-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
            console.log('✓ Mayúscula encontrada');
        } else {
            $(`#uppercase-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
            console.log('✗ Mayúscula no encontrada');
        }
        
        // Minúscula
        if (/[a-z]/.test(password)) {
            $(`#lowercase-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
            console.log('✓ Minúscula encontrada');
        } else {
            $(`#lowercase-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
            console.log('✗ Minúscula no encontrada');
        }
        
        // Número
        if (/[0-9]/.test(password)) {
            $(`#number-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
            console.log('✓ Número encontrado');
        } else {
            $(`#number-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
            console.log('✗ Número no encontrado');
        }
        
        // Símbolo
        if (/[^A-Za-z0-9]/.test(password)) {
            $(`#special-${formType}`).removeClass('requirement-unmet').addClass('requirement-met');
            console.log('✓ Símbolo encontrado');
        } else {
            $(`#special-${formType}`).removeClass('requirement-met').addClass('requirement-unmet');
            console.log('✗ Símbolo no encontrado');
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
        const $submitButton = $(this).find('button[type="submit"]');
        // Guardar el texto original del botón según el formulario
        let originalButtonText = '';
        if ($(this).attr('id') === 'formExistente') {
            originalButtonText = '<i class="fas fa-save me-1"></i> Crear Credenciales';
        } else {
            originalButtonText = '<i class="fas fa-save me-1"></i> Crear Personal y Credenciales';
        }

        // Validar número de documento para formulario nuevo
        if ($(this).attr('id') === 'formNuevo') {
            validarNumeroDocumento();
        }

        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            // El botón mantiene su texto original
            resetButton($submitButton, originalButtonText);
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
            resetButton($submitButton, originalButtonText);
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
            resetButton($submitButton, originalButtonText);
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
                // Cambiar botón a estado de carga solo DESPUÉS de confirmar
                setButtonLoading($submitButton, originalButtonText);
                // Enviar formulario
                guardarUsuario(this, $submitButton, originalButtonText);
            }
            // No necesitamos resetear aquí porque el botón no se ha cambiado aún
        });
    });
    
    // Función para enviar el formulario
    function guardarUsuario(form, $submitButton, originalButtonText) {
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
                resetButton($submitButton, originalButtonText);
                
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
                    // Función de seguridad adicional para errores del servidor
                    setTimeout(() => resetAllButtons(), 100);
                    if (response.errors) {
                        console.error(response.errors);
                        // Mostrar errores específicos en el formulario
                        mostrarErroresFormulario(response.errors, form);
                    }
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                resetButton($submitButton, originalButtonText);
                // Función de seguridad adicional
                setTimeout(() => resetAllButtons(), 100);
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

    // ==================== VALIDACIÓN AUTOMÁTICA DNI CON RENIEC ====================
    let dniValidationTimeout;
    let lastValidatedDni = '';
    let dniValidationInProgress = false;

    // Validación automática del DNI con debounce
    $('#numerodoc').on('input', function() {
        const dni = $(this).val().trim();
        const $input = $(this);
        
        // Limpiar timeout anterior
        clearTimeout(dniValidationTimeout);
        
        // Reset visual states
        resetDniValidationState();
        
        // Validar formato básico
        if (dni.length === 0) {
            return;
        }
        
        if (!/^\d{1,8}$/.test(dni)) {
            showDniError('Solo se permiten números');
            return;
        }
        
        if (dni.length < 8) {
            $('#example-numerodoc').text(`Faltan ${8 - dni.length} dígitos`).addClass('text-muted');
            return;
        } else {
            $('#example-numerodoc').text('Ejemplo: 12345678 (8 dígitos)').removeClass('text-muted');
        }
        
        // Si es el mismo DNI ya validado, no revalidar
        if (dni === lastValidatedDni) {
            return;
        }
        
        // Debounce: esperar 500ms sin escribir
        dniValidationTimeout = setTimeout(() => {
            validateDniWithReniec(dni);
        }, 500);
    });

    /**
     * Generar email automático basado en nombres y apellidos
     */
    function generarEmailAutomatico(nombres, apellidos) {
        console.log('Generando email automático para:', nombres, apellidos);
        
        // Limpiar y normalizar texto
        function limpiarTexto(texto) {
            return texto
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '') // Eliminar acentos
                .replace(/[^a-z\s]/g, '') // Solo letras y espacios
                .trim();
        }
        
        // Procesar nombres y apellidos
        const nombresLimpio = limpiarTexto(nombres || '');
        const apellidosLimpio = limpiarTexto(apellidos || '');
        
        if (!nombresLimpio || !apellidosLimpio) {
            console.warn('Faltan datos para generar email');
            return ''; // No generar email si faltan datos
        }
        
        // Tomar primer nombre y primer apellido
        const primerNombre = nombresLimpio.split(' ')[0];
        const primerApellido = apellidosLimpio.split(' ')[0];
        
        // Generar email: primera letra del nombre + primer apellido + @ishume.com
        const email = `${primerNombre.charAt(0)}${primerApellido}@ishume.com`;
        
        console.log('Email generado:', email);
        return email;
    }

    /**
     * Validar DNI con RENIEC via AJAX
     */
    function validateDniWithReniec(dni) {
        console.log('validateDniWithReniec llamada con DNI:', dni);
        if (dniValidationInProgress) {
            console.log('Validación ya en progreso, saliendo...');
            return;
        }
        
        dniValidationInProgress = true;
        showDniLoading();
        
        $.ajax({
            url: '<?= base_url('usuarios/ajax-check-dni') ?>',
            type: 'POST',
            data: {
                dni: dni,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            timeout: 15000, // 15 segundos timeout
            success: function(response) {
                console.log('AJAX Success - Response:', response);
                dniValidationInProgress = false;
                lastValidatedDni = dni;
                
                if (response.status === 'success') {
                    handleDniSuccess(response);
                } else if (response.status === 'exists_active') {
                    handleDniExistsActive(response);
                } else if (response.status === 'exists_inactive') {
                    handleDniExistsInactive(response);
                } else if (response.status === 'exists_no_user') {
                    handleDniExistsNoUser(response);
                } else {
                    handleDniError(response.message || 'DNI no encontrado');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error - Status:', status, 'Error:', error);
                console.log('AJAX Error - Response:', xhr.responseText);
                dniValidationInProgress = false;
                
                if (status === 'timeout') {
                    handleDniError('Tiempo de espera agotado. Intente nuevamente.');
                } else if (xhr.status === 429) {
                    handleDniError('Demasiadas consultas. Espere un momento e intente nuevamente.');
                } else {
                    handleDniError('Error de conexión. Verifique su internet e intente nuevamente.');
                }
                
                console.error('Error validando DNI:', xhr.responseText);
            }
        });
    }

    /**
     * Manejar DNI válido encontrado en RENIEC
     */
    function handleDniSuccess(response) {
        console.log('handleDniSuccess - Response completa:', response);
        const data = response.data;
        console.log('handleDniSuccess - Data:', data);
        
        // Mostrar éxito
        showDniSuccess('DNI válido encontrado en RENIEC');
        
        // Autocompletar campos con validación robusta
        const nombres = data.nombres || '';
        const apellidos = data.apellidos_completos || 
                         (data.apellido_paterno && data.apellido_materno ? 
                          data.apellido_paterno + ' ' + data.apellido_materno : 
                          data.apellido_paterno || data.apellido_materno || '');
        
        console.log('Nombres extraídos:', nombres);
        console.log('Apellidos extraídos:', apellidos);
        
        $('#nombres').val(nombres).addClass('is-valid');
        $('#apellidos').val(apellidos).addClass('is-valid');
        
        // Generar email automáticamente
        const emailGenerado = generarEmailAutomatico(nombres, apellidos);
        if (emailGenerado) {
            $('#email_nuevo').val(emailGenerado).addClass('is-valid');
        }
        
        // Marcar campos como readonly para evitar modificaciones
        $('#nombres, #apellidos').attr('readonly', true).addClass('bg-light');
        
        // Mostrar aviso de privacidad
        $('#privacy-notice').show();
        // Mostrar notificación elegante
        Swal.fire({
            icon: 'success',
            title: '¡DNI Válido!',
            html: `
                <strong>Datos encontrados en RENIEC:</strong><br>
                <strong>Nombres:</strong> ${data.nombres}<br>
                <strong>Apellidos:</strong> ${data.apellidos_completos}<br>
                <small class="text-muted">Fuente: ${data.source === 'cache' ? 'Cache local' : 'RENIEC'}</small>
            `,
            timer: 4000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            showConfirmButton: false
        });
        
        // Log para debugging
        console.log('DNI validado exitosamente:', data);
    }

    /**
     * Manejar DNI con usuario activo
     */
    function handleDniExistsActive(response) {
        const data = response.data;
        
        showDniError('Este DNI ya tiene credenciales ACTIVAS');
        
        // Mostrar información del usuario activo
        Swal.fire({
            icon: 'error',
            title: 'Usuario Ya Existe',
            html: `
                <div class="text-start">
                    <p><strong>Este DNI ya tiene credenciales activas:</strong></p>
                    <hr>
                    <p><i class="fas fa-user me-2"></i><strong>Nombre:</strong> ${data.nombres} ${data.apellidos}</p>
                    <p><i class="fas fa-id-card me-2"></i><strong>DNI:</strong> ${data.dni}</p>
                    <p><i class="fas fa-envelope me-2"></i><strong>Email:</strong> ${data.email}</p>
                    <p><i class="fas fa-user-circle me-2"></i><strong>Usuario:</strong> ${data.usuario}</p>
                    <p><span class="badge bg-success">Estado: ACTIVO</span></p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Ver Lista de Usuarios',
            cancelButtonText: 'Entendido',
            confirmButtonColor: '#3085d6',
            width: '500px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('usuarios') ?>';
            }
        });
    }

    /**
     * Manejar DNI con usuario desactivado
     */
    function handleDniExistsInactive(response) {
        const data = response.data;
        
        showDniError('Este DNI tiene credenciales DESACTIVADAS');
        
        // Mostrar información del usuario desactivado con opción de reactivar
        Swal.fire({
            icon: 'warning',
            title: 'Usuario Desactivado',
            html: `
                <div class="text-start">
                    <p><strong>Este DNI pertenece a un usuario desactivado:</strong></p>
                    <hr>
                    <p><i class="fas fa-user me-2"></i><strong>Nombre:</strong> ${data.nombres} ${data.apellidos}</p>
                    <p><i class="fas fa-id-card me-2"></i><strong>DNI:</strong> ${data.dni}</p>
                    <p><i class="fas fa-envelope me-2"></i><strong>Email:</strong> ${data.email}</p>
                    <p><i class="fas fa-user-circle me-2"></i><strong>Usuario:</strong> ${data.usuario}</p>
                    <p><span class="badge bg-danger">Estado: DESACTIVADO</span></p>
                    <hr>
                    <p class="text-info"><i class="fas fa-info-circle me-2"></i>¿Desea reactivar este usuario en lugar de crear uno nuevo?</p>
                </div>
            `,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Reactivar Usuario',
            denyButtonText: 'Ver Lista de Usuarios',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745',
            denyButtonColor: '#3085d6',
            width: '550px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Reactivar usuario
                reactivarUsuario(data.idusuario, data.nombres + ' ' + data.apellidos);
            } else if (result.isDenied) {
                window.location.href = '<?= base_url('usuarios') ?>';
            }
        });
    }

    /**
     * Manejar DNI de persona sin usuario
     */
    function handleDniExistsNoUser(response) {
        const data = response.data;
        
        showDniError('Esta persona ya está registrada pero sin credenciales');
        
        // Mostrar información y sugerir crear credenciales para persona existente
        Swal.fire({
            icon: 'info',
            title: 'Persona Ya Registrada',
            html: `
                <div class="text-start">
                    <p><strong>Esta persona ya está en el sistema:</strong></p>
                    <hr>
                    <p><i class="fas fa-user me-2"></i><strong>Nombre:</strong> ${data.nombres} ${data.apellidos}</p>
                    <p><i class="fas fa-id-card me-2"></i><strong>DNI:</strong> ${data.dni}</p>
                    <p><span class="badge bg-warning">Sin credenciales de acceso</span></p>
                    <hr>
                    <p class="text-info"><i class="fas fa-info-circle me-2"></i>Se recomienda crear credenciales para esta persona existente en lugar de registrarla nuevamente.</p>
                </div>
            `,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Crear Credenciales',
            denyButtonText: 'Ver Lista de Personal',
            cancelButtonText: 'Continuar Aquí',
            confirmButtonColor: '#28a745',
            denyButtonColor: '#3085d6',
            width: '550px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Ir al formulario de crear credenciales para personal existente
                window.location.href = '<?= base_url('usuarios/crear/existente') ?>';
            } else if (result.isDenied) {
                window.location.href = '<?= base_url('usuarios') ?>';
            }
            // Si cancela, puede continuar con el formulario actual
        });
    }

    /**
     * Reactivar usuario desactivado
     */
    function reactivarUsuario(idusuario, nombreCompleto) {
        Swal.fire({
            title: 'Reactivando Usuario...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('usuarios/reactivar/') ?>' + idusuario,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Usuario Reactivado!',
                        text: `Las credenciales de ${nombreCompleto} han sido reactivadas exitosamente.`,
                        confirmButtonColor: '#28a745',
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = '<?= base_url('usuarios') ?>';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudo reactivar el usuario',
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    }

    /**
     * Manejar error en validación de DNI
     */
    function handleDniError(message) {
        showDniError(message);
        
        // Limpiar campos autocompletados
        $('#nombres, #apellidos, #email_nuevo').val('').removeClass('is-valid bg-light').attr('readonly', false);
        $('#privacy-notice').hide();
    }

    /**
     * Mostrar estado de carga
     */
    function showDniLoading() {
        resetDniValidationState();
        $('#loading-numerodoc').show();
        $('#numerodoc').removeClass('is-valid is-invalid');
    }

    /**
     * Mostrar éxito en validación
     */
    function showDniSuccess(message) {
        resetDniValidationState();
        $('#success-numerodoc').text(message).show();
        $('#numerodoc').removeClass('is-invalid').addClass('is-valid');
    }

    /**
     * Mostrar error en validación
     */
    function showDniError(message) {
        resetDniValidationState();
        $('#error-numerodoc').text(message);
        $('#numerodoc').removeClass('is-valid').addClass('is-invalid');
        
        // Limpiar campos relacionados
        $('#nombres, #apellidos, #email_nuevo').removeClass('is-valid bg-light').attr('readonly', false);
        $('#privacy-notice').hide();
    }

    /**
     * Resetear estados visuales de validación DNI
     */
    function resetDniValidationState() {
        $('#loading-numerodoc, #success-numerodoc, #privacy-notice').hide();
        $('#error-numerodoc').text('Por favor ingrese un número de documento válido.');
    }

    // Limpiar validación cuando se borra el campo
    $('#numerodoc').on('keyup', function() {
        if ($(this).val().length === 0) {
            resetDniValidationState();
            $('#numerodoc').removeClass('is-valid is-invalid');
            $('#nombres, #apellidos, #email_nuevo').val('').removeClass('is-valid bg-light').attr('readonly', false);
            $('#privacy-notice').hide();
            lastValidatedDni = '';
        }
    });

    // Prevenir envío del formulario si hay validación en progreso
    $('#formNuevo').on('submit', function(e) {
        if (dniValidationInProgress) {
            e.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Validación en Progreso',
                text: 'Por favor espere a que termine la validación del DNI',
                timer: 2000
            });
            return false;
        }
    });
});
    </script>
    
    <?= $footer ?>