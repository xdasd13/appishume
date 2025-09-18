<?= $header ?>

<!-- Contenido Principal - Editar Equipo -->
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-edit text-warning me-2"></i>
                    Editar Equipo
                </h2>
                <a href="/inventario" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Inventario
                </a>
            </div>

            <!-- Información actual del equipo -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-2"></i>Información Actual
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <?php if (!empty($equipo['imgEquipo'])): ?>
                                <img src="/<?= esc($equipo['imgEquipo']) ?>" 
                                     class="img-fluid rounded mb-3" 
                                     alt="<?= esc($equipo['modelo']) ?>"
                                     style="max-height: 150px;">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 150px;">
                                    <i class="fas fa-camera fa-2x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <h5><?= esc($equipo['modelo']) ?></h5>
                            <p class="mb-1"><strong>Categoría:</strong> <?= esc($equipo['nomCate']) ?></p>
                            <p class="mb-1"><strong>Marca:</strong> <?= esc($equipo['nomMarca']) ?></p>
                            <p class="mb-1"><strong>Estado:</strong> 
                                <span class="badge bg-primary"><?= esc($equipo['estado']) ?></span>
                            </p>
                            <p class="mb-1"><strong>Cantidad:</strong> <?= $equipo['cantDisponible'] ?></p>
                            <?php if (!empty($equipo['sku'])): ?>
                                <p class="mb-1"><strong>SKU:</strong> <?= esc($equipo['sku']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de edición -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit me-2"></i>Editar Información del Equipo
                    </h6>
                </div>
                <div class="card-body">
                    <form id="formEditarEquipo" action="/inventario/actualizar/<?= $equipo['idEquipo'] ?>" method="POST" enctype="multipart/form-data" novalidate>
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <!-- Categoría -->
                            <div class="col-md-6 mb-3">
                                <label for="idCateEquipo" class="form-label">
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
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Marca -->
                            <div class="col-md-6 mb-3">
                                <label for="idMarca" class="form-label">
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
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Modelo -->
                            <div class="col-md-8 mb-3">
                                <label for="modelo" class="form-label">
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
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Cantidad Disponible -->
                            <div class="col-md-4 mb-3">
                                <label for="cantDisponible" class="form-label">
                                    Cantidad <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="cantDisponible" 
                                       name="cantDisponible" 
                                       value="<?= old('cantDisponible', $equipo['cantDisponible']) ?>"
                                       min="0"
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- SKU -->
                            <div class="col-md-6 mb-3">
                                <label for="sku" class="form-label">
                                    SKU <small class="text-muted">(Opcional)</small>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="sku" 
                                       name="sku" 
                                       value="<?= old('sku', $equipo['sku']) ?>"
                                       placeholder="Ej: CAM001"
                                       maxlength="50">
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Número de Serie -->
                            <div class="col-md-6 mb-3">
                                <label for="numSerie" class="form-label">
                                    Número de Serie <small class="text-muted">(Opcional)</small>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="numSerie" 
                                       name="numSerie" 
                                       value="<?= old('numSerie', $equipo['numSerie']) ?>"
                                       placeholder="Ej: SN123456789"
                                       maxlength="100">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label">
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
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Fecha de Compra -->
                            <div class="col-md-6 mb-3">
                                <label for="fechaCompra" class="form-label">
                                    Fecha de Compra <small class="text-muted">(Opcional)</small>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fechaCompra" 
                                       name="fechaCompra" 
                                       value="<?= old('fechaCompra', $equipo['fechaCompra']) ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Fecha de Uso -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fechaUso" class="form-label">
                                    Fecha de Primer Uso <small class="text-muted">(Opcional)</small>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fechaUso" 
                                       name="fechaUso" 
                                       value="<?= old('fechaUso', $equipo['fechaUso']) ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                Descripción <small class="text-muted">(Opcional)</small>
                            </label>
                            <textarea class="form-control" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="3"
                                      placeholder="Descripción general del equipo..."
                                      maxlength="255"><?= old('descripcion', $equipo['descripcion']) ?></textarea>
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">
                                <span id="descripcionCount">0</span>/255 caracteres
                            </small>
                        </div>

                        <!-- Características -->
                        <div class="mb-3">
                            <label for="caracteristica" class="form-label">
                                Características Técnicas <small class="text-muted">(Opcional)</small>
                            </label>
                            <textarea class="form-control" 
                                      id="caracteristica" 
                                      name="caracteristica" 
                                      rows="4"
                                      placeholder="Especificaciones técnicas detalladas..."><?= old('caracteristica', $equipo['caracteristica']) ?></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Imagen actual -->
                        <?php if (!empty($equipo['imgEquipo'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Imagen Actual</label>
                            <div class="d-flex align-items-center">
                                <img src="/<?= esc($equipo['imgEquipo']) ?>" 
                                     class="img-thumbnail me-3" 
                                     alt="Imagen actual" 
                                     style="max-width: 150px; max-height: 150px;">
                                <div>
                                    <p class="mb-1"><strong>Archivo:</strong> <?= basename($equipo['imgEquipo']) ?></p>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarImagenActual()">
                                        <i class="fas fa-trash me-1"></i>Eliminar imagen actual
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Nueva imagen -->
                        <div class="mb-4">
                            <label for="imagen_file" class="form-label">
                                <?= !empty($equipo['imgEquipo']) ? 'Cambiar Imagen' : 'Imagen del Equipo' ?> 
                                <small class="text-muted">(Opcional)</small>
                            </label>
                            <input type="file" 
                                   class="form-control" 
                                   id="imagen_file" 
                                   name="imagen_file" 
                                   accept="image/*">
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">
                                Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB
                                <?= !empty($equipo['imgEquipo']) ? '. Si selecciona una nueva imagen, reemplazará la actual.' : '' ?>
                            </small>
                            
                            <!-- Preview de nueva imagen -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeImagePreview()">
                                    <i class="fas fa-times"></i> Quitar
                                </button>
                            </div>
                        </div>

                        <!-- URL de imagen alternativa -->
                        <div class="mb-4">
                            <label for="imgEquipo" class="form-label">
                                O URL de Imagen <small class="text-muted">(Alternativo)</small>
                            </label>
                            <input type="url" 
                                   class="form-control" 
                                   id="imgEquipo" 
                                   name="imgEquipo" 
                                   value="<?= old('imgEquipo', $equipo['imgEquipo']) ?>"
                                   placeholder="https://ejemplo.com/imagen.jpg">
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">
                                Si especifica una URL, tendrá prioridad sobre el archivo subido
                            </small>
                        </div>

                        <!-- Botones -->
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="/inventario" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-outline-danger me-2" onclick="confirmarEliminacion()">
                                            <i class="fas fa-trash me-2"></i>Eliminar Equipo
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="btnActualizar">
                                            <i class="fas fa-save me-2"></i>Actualizar Equipo
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

<!-- Scripts JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formEditarEquipo');
    const descripcionTextarea = document.getElementById('descripcion');
    const descripcionCount = document.getElementById('descripcionCount');
    const imagenInput = document.getElementById('imagen_file');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    // Contador de caracteres para descripción
    function updateDescripcionCount() {
        const count = descripcionTextarea.value.length;
        descripcionCount.textContent = count;
        
        if (count > 255) {
            descripcionCount.classList.add('text-danger');
        } else {
            descripcionCount.classList.remove('text-danger');
        }
    }

    descripcionTextarea.addEventListener('input', updateDescripcionCount);
    updateDescripcionCount(); // Inicializar contador

    // Preview de imagen
    imagenInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tipo de archivo
            if (!file.type.startsWith('image/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo no válido',
                    text: 'Por favor seleccione un archivo de imagen válido.'
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
        const feedback = field.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = '';
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
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = message;
            }
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
                text: 'Por favor corrija los errores antes de continuar.'
            });
            return;
        }

        // Mostrar confirmación
        Swal.fire({
            title: '¿Confirmar actualización?',
            text: '¿Desea guardar los cambios realizados al equipo?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Deshabilitar botón y mostrar loading
                const btnActualizar = document.getElementById('btnActualizar');
                btnActualizar.disabled = true;
                btnActualizar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Actualizando...';

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

// Función para eliminar imagen actual
function eliminarImagenActual() {
    Swal.fire({
        title: '¿Eliminar imagen actual?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Limpiar el campo de URL de imagen
            document.getElementById('imgEquipo').value = '';
            
            Swal.fire({
                icon: 'info',
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

// Función para confirmar eliminación del equipo
function confirmarEliminacion() {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Deseas eliminar este equipo del inventario? Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarEquipo();
        }
    });
}

// Función para eliminar equipo
function eliminarEquipo() {
    const equipoId = <?= $equipo['idEquipo'] ?>;
    
    fetch(`/inventario/eliminar/${equipoId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
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
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            
            // Redirigir al inventario
            setTimeout(() => {
                window.location.href = '/inventario';
            }, 1500);
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
    const validationErrors = <?= json_encode(session()->getFlashdata('validation_errors')) ?>;
    let errorMessage = 'Se encontraron los siguientes errores:\n\n';
    
    Object.keys(validationErrors).forEach(field => {
        errorMessage += '• ' + validationErrors[field] + '\n';
        
        // Marcar campo con error
        const fieldElement = document.querySelector(`[name="${field}"]`);
        if (fieldElement) {
            fieldElement.classList.add('is-invalid');
            const feedback = fieldElement.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = validationErrors[field];
            }
        }
    });

    Swal.fire({
        icon: 'error',
        title: 'Errores de validación',
        text: errorMessage,
        confirmButtonText: 'Entendido'
    });
<?php endif; ?>

// Mostrar mensajes flash
<?php if (session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: '<?= session()->getFlashdata('success') ?>',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000
    });
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= session()->getFlashdata('error') ?>'
    });
<?php endif; ?>
</script>

<?= $footer ?>