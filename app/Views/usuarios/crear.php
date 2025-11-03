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
                        <a href="<?= htmlspecialchars(base_url('usuarios'), ENT_QUOTES, 'UTF-8') ?>"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= ($tipo_creacion === 'existente') ? 'active' : '' ?>"
                                id="existente-tab" data-bs-toggle="tab" data-bs-target="#existente" type="button"
                                role="tab">
                                <i class="fas fa-user me-1"></i> Personal Existente
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= ($tipo_creacion === 'nuevo') ? 'active' : '' ?>" id="nuevo-tab"
                                data-bs-toggle="tab" data-bs-target="#nuevo" type="button" role="tab">
                                <i class="fas fa-user-plus me-1"></i> Nuevo Personal
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <!-- Formulario para personal existente -->
                        <div class="tab-pane fade <?= ($tipo_creacion === 'existente') ? 'show active' : '' ?>"
                            id="existente" role="tabpanel">
                            <form id="formExistente" class="needs-validation" novalidate
                                action="<?= base_url('/usuarios/guardar') ?>" method="POST">
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
                                                    <option
                                                        value="<?= htmlspecialchars($persona->idpersona, ENT_QUOTES, 'UTF-8') ?>">
                                                        <?= htmlspecialchars($persona->nombres . ' ' . $persona->apellidos, ENT_QUOTES, 'UTF-8') ?>
                                                        (<?= htmlspecialchars($persona->tipodoc, ENT_QUOTES, 'UTF-8') ?>:
                                                        <?= htmlspecialchars($persona->numerodoc, ENT_QUOTES, 'UTF-8') ?>)
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
                                                    <option
                                                        value="<?= htmlspecialchars($cargo->idcargo, ENT_QUOTES, 'UTF-8') ?>">
                                                        <?= htmlspecialchars($cargo->cargo, ENT_QUOTES, 'UTF-8') ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="example-text">Ejemplo: Administrador, Supervisor,
                                                Técnico</span>
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
                                                    pattern="[a-zA-Z0-9_\-]{4,20}">
                                                <label for="nombreusuario_existente">Nombre de Usuario *</label>
                                            </div>
                                            <span class="example-text">Ejemplo: juan.perez, jperez2023 (4-20 caracteres
                                                alfanuméricos)</span>
                                            <div class="invalid-feedback" id="error-nombreusuario-existente">
                                                Por favor ingrese un nombre de usuario válido (4-20 caracteres
                                                alfanuméricos).
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
                                                <button type="button" class="password-toggle"
                                                    id="togglePasswordExistente">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <span class="example-text">Ejemplo: ClaveSegura123! (mín. 8 caracteres, 1
                                                mayúscula, 1 minúscula, 1 número, 1 símbolo)</span>

                                            <div class="security-feedback">
                                                <div id="password-strength-existente" class="password-strength"></div>
                                                <div id="password-requirements-existente">
                                                    <small class="requirement-unmet" id="length-existente">• Mínimo 8
                                                        caracteres</small><br>
                                                    <small class="requirement-unmet" id="uppercase-existente">• Al menos
                                                        una mayúscula</small><br>
                                                    <small class="requirement-unmet" id="lowercase-existente">• Al menos
                                                        una minúscula</small><br>
                                                    <small class="requirement-unmet" id="number-existente">• Al menos un
                                                        número</small><br>
                                                    <small class="requirement-unmet" id="special-existente">• Al menos
                                                        un símbolo</small>
                                                </div>
                                            </div>

                                            <div class="invalid-feedback" id="error-password-existente">
                                                La contraseña no cumple con los requisitos de seguridad.
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-floating position-relative">
                                                <input type="password" class="form-control"
                                                    id="confirm_password_existente" name="confirm_password" required
                                                    placeholder=" ">
                                                <label for="confirm_password_existente">Confirmar Contraseña *</label>
                                                <button type="button" class="password-toggle"
                                                    id="toggleConfirmPasswordExistente">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <span class="example-text">Debe coincidir exactamente con la contraseña
                                                anterior</span>
                                            <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <button type="submit" class="btn btn-primary" id="btnSubmitExistente" disabled>
                                        <i class="fas fa-save me-1"></i> Crear Credenciales
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Formulario para nuevo personal -->
                        <div class="tab-pane fade <?= ($tipo_creacion === 'nuevo') ? 'show active' : '' ?>" id="nuevo"
                            role="tabpanel">
                            <form id="formNuevo" class="needs-validation" novalidate
                                action="<?= base_url('/usuarios/guardar') ?>" method="POST">
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
                                                    <input type="text" class="form-control" id="tipodoc" name="tipodoc"
                                                        value="DNI" readonly required>
                                                    <label for="tipodoc">Tipo de Documento *</label>
                                                </div>
                                                <span class="example-text">Solo se acepta DNI</span>
                                            </div>
                                            <div class="col-md-8 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="numerodoc"
                                                        name="numerodoc" required placeholder=" " maxlength="8"
                                                        pattern="[0-9]{8}">
                                                    <label for="numerodoc">Número de Documento *</label>
                                                </div>
                                                <span class="example-text" id="example-numerodoc">Ejemplo: 12345678 (8
                                                    dígitos)</span>
                                                <div class="valid-feedback" id="success-numerodoc"
                                                    style="display: none;">
                                                    <i class="fas fa-check-circle text-success"></i> DNI válido
                                                    encontrado en RENIEC
                                                </div>
                                                <div class="invalid-feedback" id="error-numerodoc">
                                                    Por favor ingrese un número de documento válido.
                                                </div>
                                                <div class="text-info small" id="loading-numerodoc"
                                                    style="display: none;">
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
                                                    <input type="text" class="form-control" id="nombres" name="nombres"
                                                        required placeholder=" " minlength="2" maxlength="50" readonly
                                                        required>
                                                    <label for="nombres">Nombres *</label>
                                                </div>
                                                <span class="example-text">Ejemplo: Juan Carlos (solo letras y espacios,
                                                    2-50 caracteres)</span>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese nombres válidos (solo letras y espacios, 2-50
                                                    caracteres).
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="apellidos"
                                                        name="apellidos" required placeholder=" " minlength="2"
                                                        maxlength="50" readonly required>
                                                    <label for="apellidos">Apellidos *</label>
                                                </div>
                                                <span class="example-text">Ejemplo: Pérez García (solo letras y
                                                    espacios, 2-50 caracteres)</span>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese apellidos válidos (solo letras y espacios, 2-50
                                                    caracteres).
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
                                                        name="telalternativo" placeholder="912345678" pattern="[0-9]{9}"
                                                        maxlength="9">
                                                </div>
                                                <span class="example-text">Ejemplo: 912345678 (9 dígitos,
                                                    opcional)</span>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese un teléfono válido.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="direccion" name="direccion"
                                                    required placeholder=" " minlength="5" maxlength="150">
                                                <label for="direccion">Dirección *</label>
                                            </div>
                                            <span class="example-text">Ejemplo: Av. Principal 123, Urb. Las
                                                Flores</span>
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
                                                        <option
                                                            value="<?= htmlspecialchars($cargo->idcargo, ENT_QUOTES, 'UTF-8') ?>">
                                                            <?= htmlspecialchars($cargo->cargo, ENT_QUOTES, 'UTF-8') ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label for="idcargo_nuevo">Cargo *</label>
                                            </div>
                                            <span class="example-text">Ejemplo: Administrador, Supervisor,
                                                Técnico</span>
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
                                                    pattern="[a-zA-Z0-9_\-]{4,20}">
                                                <label for="nombreusuario_nuevo">Nombre de Usuario *</label>
                                            </div>
                                            <span class="example-text">Ejemplo: juan.perez, jperez2023 (4-20 caracteres
                                                alfanuméricos)</span>
                                            <div class="invalid-feedback" id="error-nombreusuario-nuevo">
                                                Por favor ingrese un nombre de usuario válido (4-20 caracteres
                                                alfanuméricos).
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-floating position-relative">
                                                <input type="email" class="form-control" id="email_nuevo" name="email"
                                                    required placeholder=" ">
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
                                            <span class="example-text">Ejemplo: ClaveSegura123! (mín. 8 caracteres, 1
                                                mayúscula, 1 minúscula, 1 número, 1 símbolo)</span>

                                            <div class="security-feedback">
                                                <div id="password-strength-nuevo" class="password-strength"></div>
                                                <div id="password-requirements-nuevo">
                                                    <small class="requirement-unmet" id="length-nuevo">• Mínimo 8
                                                        caracteres</small><br>
                                                    <small class="requirement-unmet" id="uppercase-nuevo">• Al menos una
                                                        mayúscula</small><br>
                                                    <small class="requirement-unmet" id="lowercase-nuevo">• Al menos una
                                                        minúscula</small><br>
                                                    <small class="requirement-unmet" id="number-nuevo">• Al menos un
                                                        número</small><br>
                                                    <small class="requirement-unmet" id="special-nuevo">• Al menos un
                                                        símbolo</small>
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
                                                <button type="button" class="password-toggle"
                                                    id="toggleConfirmPasswordNuevo">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <span class="example-text">Debe coincidir exactamente con la contraseña
                                                anterior</span>
                                            <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <button type="submit" class="btn btn-primary" id="btnSubmitNuevo" disabled>
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
<!-- Módulos JavaScript-->
<script src="<?= base_url('assets/js/modules/usuarios/config.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/usuarios/validation.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/usuarios/ui-handler.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/usuarios/api-client.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/usuarios/dni-validator.js') ?>"></script>
<script src="<?= base_url('assets/js/modules/usuarios/main.js') ?>"></script>
<?= $footer ?>