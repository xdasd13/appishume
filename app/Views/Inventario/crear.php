<?= $header ?>
<link rel="stylesheet" href="<?= base_url('assets/css/Inventario-crear.css') ?>">

<!-- Contenido Principal - Crear Equipo -->
<div class="container-fluid px-4">
    <!-- Header Mejorado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <div class="header-icon-container bg-success-gradient rounded-circle me-3">
                <i class="fas fa-plus text-white"></i>
            </div>
            <div>
                <h1 class="h3 mb-1 text-gray-900 fw-bold">Agregar Nuevo Equipo</h1>
                <p class="text-muted mb-0">Completa la información del nuevo equipo</p>
            </div>
        </div>
        <a href="/inventario" class="btn btn-outline-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>
            Volver al Inventario
        </a>
    </div>

    <!-- Formulario Mejorado -->
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Información del Equipo
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="formCrearEquipo" action="/inventario/guardar" method="POST" enctype="multipart/form-data" novalidate>
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
                                        <option value="<?= $categoria['idCateEquipo'] ?>" <?= old('idCateEquipo') == $categoria['idCateEquipo'] ? 'selected' : '' ?>>
                                            <?= esc($categoria['nomCate']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text text-muted small">Selecciona la categoría del equipo</div>
                            </div>

                            <!-- Marca -->
                            <div class="col-md-6">
                                <label for="idMarca" class="form-label fw-semibold">
                                    Marca <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="idMarca" name="idMarca" required>
                                    <option value="">Seleccionar marca...</option>
                                    <?php foreach ($marcas as $marca): ?>
                                        <option value="<?= $marca['idMarca'] ?>" <?= old('idMarca') == $marca['idMarca'] ? 'selected' : '' ?>>
                                            <?= esc($marca['nomMarca']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text text-muted small">Selecciona la marca del equipo</div>
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
                                       value="<?= old('modelo') ?>"
                                       placeholder="Ej: Sony A6400 con lente kit"
                                       required
                                       minlength="2"
                                       maxlength="70">
                                <div class="form-text text-muted small">Nombre o modelo específico del equipo</div>
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
                                       value="<?= old('cantDisponible', 1) ?>"
                                       min="0"
                                       required>
                                <div class="form-text text-muted small">Cantidad disponible en inventario</div>
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
                                       value="<?= old('sku') ?>"
                                       placeholder="Ej: CAM-SONY-001"
                                       maxlength="50">
                                <div class="form-text text-muted small">Código único identificador (se genera automáticamente si se deja vacío)</div>
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
                                       value="<?= old('numSerie') ?>"
                                       placeholder="Ej: SN123456789"
                                       maxlength="100">
                                <div class="form-text text-muted small">Número de serie físico del equipo</div>
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
                                    <option value="Nuevo" <?= old('estado') == 'Nuevo' ? 'selected' : '' ?>>Nuevo</option>
                                    <option value="EnUso" <?= old('estado') == 'EnUso' ? 'selected' : '' ?>>En Uso</option>
                                    <option value="EnMantenimiento" <?= old('estado') == 'EnMantenimiento' ? 'selected' : '' ?>>En Mantenimiento</option>
                                    <option value="Dañado" <?= old('estado') == 'Dañado' ? 'selected' : '' ?>>Dañado</option>
                                    <option value="Otro" <?= old('estado') == 'Otro' ? 'selected' : '' ?>>Otro</option>
                                </select>
                                <div class="form-text text-muted small">Estado actual del equipo</div>
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
                                       value="<?= old('fechaCompra') ?>">
                                <div class="form-text text-muted small">Fecha de adquisición del equipo</div>
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
                                      placeholder="Descripción general del equipo y sus características principales..."
                                      maxlength="255"><?= old('descripcion') ?></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <div class="form-text text-muted small">Descripción breve del equipo</div>
                                <small class="text-muted">
                                    <span id="descripcionCount">0</span>/255 caracteres
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
                                      placeholder="Especificaciones técnicas detalladas, características especiales, accesorios incluidos..."><?= old('caracteristica') ?></textarea>
                            <div class="form-text text-muted small">Detalles técnicos y especificaciones del equipo</div>
                        </div>

                        <!-- Imagen -->
                        <div class="section-header mb-4 mt-5">
                            <h6 class="text-primary fw-semibold mb-3">
                                <i class="fas fa-image me-2"></i>
                                Imagen del Equipo
                            </h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="imagen_file" class="form-label fw-semibold">
                                    Subir Imagen
                                </label>
                                <input type="file" 
                                       class="form-control" 
                                       id="imagen_file" 
                                       name="imagen_file" 
                                       accept="image/*">
                                <div class="form-text text-muted small">
                                    Formatos: JPG, PNG, GIF. Tamaño máximo: 5MB
                                </div>
                                
                                <!-- Preview de imagen -->
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
                                       value="<?= old('imgEquipo') ?>"
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
                                    <button type="submit" class="btn btn-primary btn-lg px-4" id="btnGuardar">
                                        <i class="fas fa-save me-2"></i>Guardar Equipo
                                    </button>
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

<!-- Estilos CSS para el Formulario -->


<!-- JavaScript para el Formulario -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCrearEquipo');
    const descripcionTextarea = document.getElementById('descripcion');
    const descripcionCount = document.getElementById('descripcionCount');
    const imagenInput = document.getElementById('imagen_file');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const btnGuardar = document.getElementById('btnGuardar');

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

    // Preview de imagen
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

    // Validación en tiempo real
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

    // Función de validación individual
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';

        // Limpiar estados anteriores
        field.classList.remove('is-valid', 'is-invalid');
        
        // Remover mensajes de error existentes
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }

        // Validaciones específicas
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

            case 'sku':
                if (value && value.length > 50) {
                    isValid = false;
                    message = 'El SKU no puede exceder 50 caracteres.';
                }
                break;

            case 'numSerie':
                if (value && value.length > 100) {
                    isValid = false;
                    message = 'El número de serie no puede exceder 100 caracteres.';
                }
                break;

            case 'descripcion':
                if (value && value.length > 255) {
                    isValid = false;
                    message = 'La descripción no puede exceder 255 caracteres.';
                }
                break;
        }

        // Aplicar clase y mensaje
        if (isValid) {
            if (value || field.hasAttribute('required')) {
                field.classList.add('is-valid');
            }
        } else {
            field.classList.add('is-invalid');
            // Crear elemento de mensaje de error
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

        // Validar todos los campos
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
            
            // Scroll al primer error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
            return;
        }

        // Mostrar confirmación
        Swal.fire({
            title: '¿Crear nuevo equipo?',
            text: '¿Desea agregar este equipo al inventario?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2c5aa0',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, crear equipo',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Deshabilitar botón y mostrar loading
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';

                // Enviar formulario
                this.submit();
            }
        });
    });
});

// Función para quitar preview de imagen
function removeImagePreview() {
    document.getElementById('imagen_file').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

// Mostrar errores de validación del servidor
<?php if (session()->getFlashdata('validation_errors')): ?>
document.addEventListener('DOMContentLoaded', function() {
    const validationErrors = <?= json_encode(session()->getFlashdata('validation_errors')) ?>;
    
    Object.keys(validationErrors).forEach(field => {
        const fieldElement = document.querySelector(`[name="${field}"]`);
        if (fieldElement) {
            fieldElement.classList.add('is-invalid');
            
            // Crear mensaje de error
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = validationErrors[field];
            fieldElement.parentNode.appendChild(errorDiv);
        }
    });

    // Mostrar alerta general
    Swal.fire({
        icon: 'error',
        title: 'Errores de validación',
        text: 'Por favor revise los campos marcados en rojo.',
        confirmButtonColor: '#2c5aa0'
    });
});
<?php endif; ?>

// Mostrar mensajes flash
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