<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/Inventario-editar.css') ?>">

<!-- Contenido Principal - Editar Equipo -->
<div class="container-fluid px-4">
    <!-- Header Mejorado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <div class="header-icon-container bg-warning-gradient rounded-circle me-3">
                <i class="fas fa-edit text-white"></i>
            </div>
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">Editar Equipo</h1>
                <p class="text-muted mb-0">Actualiza la información del equipo existente</p>
            </div>
        </div>
        <a href="/inventario" class="btn btn-outline-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>
            Volver al Inventario
        </a>
    </div>

    <!-- Información Actual -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Información Actual del Equipo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <?php if (!empty($equipo['imgEquipo'])): ?>
                                <img src="/<?= esc($equipo['imgEquipo']) ?>" 
                                     class="img-fluid rounded shadow-sm mb-3 current-equipment-image" 
                                     alt="<?= esc($equipo['modelo']) ?>">
                            <?php else: ?>
                                <div class="current-equipment-placeholder rounded mb-3">
                                    <i class="fas fa-camera fa-2x"></i>
                                    <p class="small mt-2 mb-0">Sin imagen</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="small text-muted fw-semibold">Modelo</label>
                                    <p class="fw-bold text-gray-800 mb-0"><?= esc($equipo['modelo']) ?></p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted fw-semibold">Categoría</label>
                                    <p class="fw-bold text-gray-800 mb-0"><?= esc($equipo['nomCate']) ?></p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted fw-semibold">Marca</label>
                                    <p class="fw-bold text-gray-800 mb-0"><?= esc($equipo['nomMarca']) ?></p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted fw-semibold">Estado</label>
                                    <div>
                                        <?php
                                        $badgeConfig = [
                                            'Nuevo' => 'badge-new',
                                            'EnUso' => 'badge-in-use',
                                            'EnMantenimiento' => 'badge-maintenance',
                                            'Dañado' => 'badge-damaged',
                                            'Otro' => 'badge-other'
                                        ];
                                        $badgeClass = $badgeConfig[$equipo['estado']] ?? 'badge-other';
                                        ?>
                                        <span class="status-badge <?= $badgeClass ?>">
                                            <?= $equipo['estado'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted fw-semibold">Cantidad</label>
                                    <p class="fw-bold text-gray-800 mb-0"><?= $equipo['cantDisponible'] ?> unidades</p>
                                </div>
                                <?php if (!empty($equipo['sku'])): ?>
                                <div class="col-sm-6">
                                    <label class="small text-muted fw-semibold">SKU</label>
                                    <p class="fw-bold text-gray-800 mb-0"><?= esc($equipo['sku']) ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Edición -->
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Editar Información del Equipo
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="formEditarEquipo" action="/inventario/actualizar/<?= $equipo['idEquipo'] ?>" method="POST" enctype="multipart/form-data" novalidate>
                        <?= csrf_field() ?>
                        
                        <!-- Información Básica -->
                        <div class="section-header mb-4">
                            <h6 class="text-primary fw-semibold mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Información Básica
                            </h6>
                        </div>
                        
                        <div class="row g-3">
                            <!-- Categoría -->
                            <div class="col-md-6">
                                <label for="idCateEquipo" class="form-label fw-semibold">
                                    Categoría <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="idCateEquipo" name="idCateEquipo" required>
                                    <option value="">Seleccionar categoría...</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria['idCateEquipo'] ?>" 
                                                <?= (old('idCateEquipo', $equipo['idCateEquipo']) == $categoria['idCateEquipo']) ? 'selected' : '' ?>>
                                            <?= esc($categoria['nomCate']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Marca -->
                            <div class="col-md-6">
                                <label for="idMarca" class="form-label fw-semibold">
                                    Marca <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="idMarca" name="idMarca" required>
                                    <option value="">Seleccionar marca...</option>
                                    <?php foreach ($marcas as $marca): ?>
                                        <option value="<?= $marca['idMarca'] ?>" 
                                                <?= (old('idMarca', $equipo['idMarca']) == $marca['idMarca']) ? 'selected' : '' ?>>
                                            <?= esc($marca['nomMarca']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <!-- Modelo -->
                            <div class="col-md-8">
                                <label for="modelo" class="form-label fw-semibold">
                                    Modelo <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="modelo" 
                                       name="modelo" 
                                       value="<?= old('modelo', $equipo['modelo']) ?>"
                                       placeholder="Ej: Sony A6400 con lente kit"
                                       required
                                       minlength="2"
                                       maxlength="70">
                            </div>

                            <!-- Cantidad Disponible -->
                            <div class="col-md-4">
                                <label for="cantDisponible" class="form-label fw-semibold">
                                    Cantidad <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="cantDisponible" 
                                       name="cantDisponible" 
                                       value="<?= old('cantDisponible', $equipo['cantDisponible']) ?>"
                                       min="0"
                                       required>
                            </div>
                        </div>

                        <!-- Información Técnica -->
                        <div class="section-header mb-4 mt-5">
                            <h6 class="text-primary fw-semibold mb-3">
                                <i class="fas fa-cogs me-2"></i>
                                Información Técnica
                            </h6>
                        </div>

                        <div class="row g-3">
                            <!-- SKU -->
                            <div class="col-md-6">
                                <label for="sku" class="form-label fw-semibold">
                                    SKU
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="sku" 
                                       name="sku" 
                                       value="<?= old('sku', $equipo['sku']) ?>"
                                       placeholder="Ej: CAM-SONY-001"
                                       maxlength="50">
                            </div>

                            <!-- Número de Serie -->
                            <div class="col-md-6">
                                <label for="numSerie" class="form-label fw-semibold">
                                    Número de Serie
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="numSerie" 
                                       name="numSerie" 
                                       value="<?= old('numSerie', $equipo['numSerie']) ?>"
                                       placeholder="Ej: SN123456789"
                                       maxlength="100">
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <!-- Estado -->
                            <div class="col-md-6">
                                <label for="estado" class="form-label fw-semibold">
                                    Estado <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="">Seleccionar estado...</option>
                                    <option value="Nuevo" <?= (old('estado', $equipo['estado']) == 'Nuevo') ? 'selected' : '' ?>>Nuevo</option>
                                    <option value="EnUso" <?= (old('estado', $equipo['estado']) == 'EnUso') ? 'selected' : '' ?>>En Uso</option>
                                    <option value="EnMantenimiento" <?= (old('estado', $equipo['estado']) == 'EnMantenimiento') ? 'selected' : '' ?>>En Mantenimiento</option>
                                    <option value="Dañado" <?= (old('estado', $equipo['estado']) == 'Dañado') ? 'selected' : '' ?>>Dañado</option>
                                    <option value="Otro" <?= (old('estado', $equipo['estado']) == 'Otro') ? 'selected' : '' ?>>Otro</option>
                                </select>
                            </div>

                            <!-- Fecha de Compra -->
                            <div class="col-md-6">
                                <label for="fechaCompra" class="form-label fw-semibold">
                                    Fecha de Compra
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fechaCompra" 
                                       name="fechaCompra" 
                                       value="<?= old('fechaCompra', $equipo['fechaCompra']) ?>">
                            </div>
                        </div>

                        <!-- Fecha de Uso -->
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label for="fechaUso" class="form-label fw-semibold">
                                    Fecha de Primer Uso
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fechaUso" 
                                       name="fechaUso" 
                                       value="<?= old('fechaUso', $equipo['fechaUso']) ?>">
                            </div>
                        </div>

                        <!-- Descripciones -->
                        <div class="section-header mb-4 mt-5">
                            <h6 class="text-primary fw-semibold mb-3">
                                <i class="fas fa-align-left me-2"></i>
                                Descripciones
                            </h6>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-4">
                            <label for="descripcion" class="form-label fw-semibold">
                                Descripción
                            </label>
                            <textarea class="form-control" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="3"
                                      placeholder="Descripción general del equipo..."
                                      maxlength="255"><?= old('descripcion', $equipo['descripcion']) ?></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <small class="text-muted">Descripción breve del equipo</small>
                                <small class="text-muted">
                                    <span id="descripcionCount"><?= strlen($equipo['descripcion'] ?? '') ?></span>/255 caracteres
                                </small>
                            </div>
                        </div>

                        <!-- Características -->
                        <div class="mb-4">
                            <label for="caracteristica" class="form-label fw-semibold">
                                Características Técnicas
                            </label>
                            <textarea class="form-control" 
                                      id="caracteristica" 
                                      name="caracteristica" 
                                      rows="4"
                                      placeholder="Especificaciones técnicas detalladas..."><?= old('caracteristica', $equipo['caracteristica']) ?></textarea>
                        </div>

                        <!-- Imagen -->
                        <div class="section-header mb-4 mt-5">
                            <h6 class="text-primary fw-semibold mb-3">
                                <i class="fas fa-image me-2"></i>
                                Imagen del Equipo
                            </h6>
                        </div>

                        <!-- Imagen Actual -->
                        <?php if (!empty($equipo['imgEquipo'])): ?>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Imagen Actual</label>
                            <div class="d-flex align-items-start gap-3">
                                <div class="current-image-container">
                                    <img src="/<?= esc($equipo['imgEquipo']) ?>" 
                                         class="current-image-preview"
                                         alt="Imagen actual">
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-2">
                                        <strong>Archivo:</strong> 
                                        <span class="text-muted"><?= basename($equipo['imgEquipo']) ?></span>
                                    </p>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarImagenActual()">
                                        <i class="fas fa-trash me-1"></i>Eliminar imagen actual
                                    </button>
                                    <div class="form-text text-muted small mt-1">
                                        Al eliminar la imagen actual, se quitará la asociación con este equipo.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="imagen_file" class="form-label fw-semibold">
                                    <?= !empty($equipo['imgEquipo']) ? 'Cambiar Imagen' : 'Subir Imagen' ?>
                                </label>
                                <input type="file" 
                                       class="form-control" 
                                       id="imagen_file" 
                                       name="imagen_file" 
                                       accept="image/*">
                                <div class="form-text text-muted small">
                                    Formatos: JPG, PNG, GIF. Tamaño máximo: 5MB
                                    <?= !empty($equipo['imgEquipo']) ? '. Reemplazará la imagen actual.' : '' ?>
                                </div>
                                
                                <!-- Preview de nueva imagen -->
                                <div id="imagePreview" class="mt-3" style="display: none;">
                                    <div class="image-preview-container">
                                        <img id="previewImg" src="" alt="Preview" class="image-preview">
                                        <button type="button" class="btn-remove-preview" onclick="removeImagePreview()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="imgEquipo" class="form-label fw-semibold">
                                    O URL de Imagen
                                </label>
                                <input type="url" 
                                       class="form-control" 
                                       id="imgEquipo" 
                                       name="imgEquipo" 
                                       value="<?= old('imgEquipo', $equipo['imgEquipo']) ?>"
                                       placeholder="https://ejemplo.com/imagen-equipo.jpg">
                                <div class="form-text text-muted small">
                                    Enlace a imagen externa (tiene prioridad sobre archivo subido)
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row mt-5 pt-4 border-top">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="/inventario" class="btn btn-outline-secondary btn-lg px-4">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-outline-danger btn-lg px-4 me-3" onclick="confirmarEliminacion()">
                                            <i class="fas fa-trash me-2"></i>Eliminar
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-lg px-4" id="btnActualizar">
                                            <i class="fas fa-save me-2"></i>Actualizar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer ?>

<!-- Estilos CSS para Editar -->


<!-- JavaScript para Editar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formEditarEquipo');
    const descripcionTextarea = document.getElementById('descripcion');
    const descripcionCount = document.getElementById('descripcionCount');
    const imagenInput = document.getElementById('imagen_file');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const btnActualizar = document.getElementById('btnActualizar');

    // Contador de caracteres para descripción
    function updateDescripcionCount() {
        const count = descripcionTextarea.value.length;
        descripcionCount.textContent = count;
        
        if (count > 255) {
            descripcionCount.classList.add('text-danger');
            descripcionTextarea.classList.add('is-invalid');
        } else {
            descripcionCount.classList.remove('text-danger');
            descripcionTextarea.classList.remove('is-invalid');
        }
    }

    descripcionTextarea.addEventListener('input', updateDescripcionCount);
    updateDescripcionCount();

    // Preview de nueva imagen
    imagenInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tipo de archivo
            if (!file.type.startsWith('image/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo no válido',
                    text: 'Por favor seleccione un archivo de imagen válido (JPG, PNG, GIF).'
                });
                this.value = '';
                return;
            }

            // Validar tamaño (5MB)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo muy grande',
                    text: 'El archivo debe ser menor a 5MB.'
                });
                this.value = '';
                return;
            }

            // Mostrar preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Validación en tiempo real (igual que en crear.php)
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });

    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';

        field.classList.remove('is-valid', 'is-invalid');
        
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }

        switch (field.name) {
            case 'idCateEquipo':
                if (!value) {
                    isValid = false;
                    message = 'Debe seleccionar una categoría.';
                }
                break;

            case 'idMarca':
                if (!value) {
                    isValid = false;
                    message = 'Debe seleccionar una marca.';
                }
                break;

            case 'modelo':
                if (!value) {
                    isValid = false;
                    message = 'El modelo es obligatorio.';
                } else if (value.length < 2) {
                    isValid = false;
                    message = 'El modelo debe tener al menos 2 caracteres.';
                } else if (value.length > 70) {
                    isValid = false;
                    message = 'El modelo no puede exceder 70 caracteres.';
                }
                break;

            case 'cantDisponible':
                if (!value) {
                    isValid = false;
                    message = 'La cantidad es obligatoria.';
                } else if (parseInt(value) < 0) {
                    isValid = false;
                    message = 'La cantidad no puede ser negativa.';
                }
                break;

            case 'estado':
                if (!value) {
                    isValid = false;
                    message = 'Debe seleccionar un estado.';
                }
                break;
        }

        if (isValid) {
            if (value || field.hasAttribute('required')) {
                field.classList.add('is-valid');
            }
        } else {
            field.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        }

        return isValid;
    }

    // Envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        let isFormValid = true;
        inputs.forEach(input => {
            if (!validateField(input)) {
                isFormValid = false;
            }
        });

        if (!isFormValid) {
            Swal.fire({
                icon: 'error',
                title: 'Formulario incompleto',
                text: 'Por favor corrija los errores marcados antes de continuar.',
                confirmButtonColor: '#2c5aa0'
            });
            
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
            return;
        }

        Swal.fire({
            title: '¿Actualizar equipo?',
            text: '¿Desea guardar los cambios realizados en este equipo?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2c5aa0',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                btnActualizar.disabled = true;
                btnActualizar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Actualizando...';
                this.submit();
            }
        });
    });
});

function removeImagePreview() {
    document.getElementById('imagen_file').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

function eliminarImagenActual() {
    Swal.fire({
        title: '¿Eliminar imagen actual?',
        text: 'La imagen se eliminará permanentemente. Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('imgEquipo').value = '';
            Swal.fire({
                icon: 'success',
                title: 'Imagen marcada para eliminar',
                text: 'La imagen se eliminará al guardar los cambios.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    });
}

function confirmarEliminacion() {
    Swal.fire({
        title: '¿Eliminar equipo?',
        text: "Esta acción no se puede deshacer. El equipo será eliminado permanentemente del inventario.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarEquipo();
        }
    });
}

function eliminarEquipo() {
    const equipoId = <?= $equipo['idEquipo'] ?>;
    
    fetch(`/inventario/eliminar/${equipoId}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Eliminado',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '/inventario';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al eliminar el equipo'
        });
    });
}

// Mostrar errores de validación del servidor
<?php if (session()->getFlashdata('validation_errors')): ?>
document.addEventListener('DOMContentLoaded', function() {
    const validationErrors = <?= json_encode(session()->getFlashdata('validation_errors')) ?>;
    
    Object.keys(validationErrors).forEach(field => {
        const fieldElement = document.querySelector(`[name="${field}"]`);
        if (fieldElement) {
            fieldElement.classList.add('is-invalid');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = validationErrors[field];
            fieldElement.parentNode.appendChild(errorDiv);
        }
    });

    Swal.fire({
        icon: 'error',
        title: 'Errores de validación',
        text: 'Por favor revise los campos marcados en rojo.',
        confirmButtonColor: '#2c5aa0'
    });
});
<?php endif; ?>

// Mostrar mensajes flash
<?php if (session()->getFlashdata('success')): ?>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: '<?= session()->getFlashdata('success') ?>',
        confirmButtonColor: '#2c5aa0'
    });
});
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= session()->getFlashdata('error') ?>',
        confirmButtonColor: '#2c5aa0'
    });
});
<?php endif; ?>
</script>