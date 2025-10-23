<?= $header; ?>

<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title"><i class="fas fa-edit mr-2"></i>Editar Entrega #<?= $entrega['identregable'] ?></h4>
                            <a href="<?= base_url('entregas') ?>" class="btn btn-secondary btn-round ml-3">
                                <i class="fas fa-arrow-left mr-2"></i>Volver
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (session('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Error de validación:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach (session('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (session('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <?= session('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Información del servicio -->
                        <div class="alert alert-info mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-1">Cliente:</h6>
                                    <p class="mb-2"><strong><?= $entrega['nombre_cliente'] ?> <?= $entrega['apellido_cliente'] ?></strong></p>
                                    
                                    <h6 class="mb-1">Servicio:</h6>
                                    <p class="mb-0"><strong><?= $entrega['servicio'] ?></strong></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1">Fecha del servicio:</h6>
                                    <p class="mb-2"><strong><?= date('d/m/Y H:i', strtotime($entrega['fechahoraservicio'])) ?></strong></p>
                                    
                                    <h6 class="mb-1">Estado actual:</h6>
                                    <p class="mb-0">
                                        <span class="badge badge-<?= ($entrega['estado'] == 'completada') ? 'success' : (($entrega['dias_restantes'] < 0) ? 'danger' : 'warning') ?>">
                                            <?= $entrega['estado_visual'] ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <form action="<?= base_url('entregas/actualizar/'.$entrega['identregable']) ?>" method="post" class="form-validate" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="fechahoraentrega" class="form-label">
                                            <i class="fas fa-calendar-alt mr-2 text-success"></i>Fecha y Hora de Entrega *
                                        </label>
                                        <div class="input-group">
                                            <input type="datetime-local" class="form-control" id="fechahoraentrega" name="fechahoraentrega" value="<?= str_replace(' ', 'T', $entrega['fechahoraentrega']) ?>" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Modifique la fecha y hora programada para la entrega</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="observaciones" class="form-label">
                                            <i class="fas fa-sticky-note mr-2 text-info"></i>Formato de Entrega *
                                        </label>
                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" required><?= $entrega['observaciones'] ?></textarea>
                                        <small class="form-text text-muted">Describa el formato de entrega (físico/digital) y especificaciones</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="comprobante_entrega" class="form-label">
                                            <i class="fas fa-file-pdf mr-2 text-danger"></i>Comprobante de Entrega (PDF)
                                        </label>
                                        
                                        <?php if (!empty($entrega['comprobante_entrega'])): ?>
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center">
                                                    <a href="<?= base_url('uploads/comprobantes_entrega/'.$entrega['comprobante_entrega']) ?>" class="btn btn-sm btn-info mr-3" target="_blank">
                                                        <i class="fas fa-file-pdf mr-1"></i> Ver comprobante actual
                                                    </a>
                                                    <span class="text-muted">Subir un nuevo PDF solo si desea reemplazar el actual</span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="custom-file mb-2">
                                            <input type="file" class="custom-file-input" id="comprobante_entrega" name="comprobante_entrega" accept=".pdf">
                                            <label class="custom-file-label" for="comprobante_entrega">
                                                <?= (!empty($entrega['comprobante_entrega'])) ? 'Cambiar PDF' : 'Seleccionar PDF' ?>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">Archivo PDF con el comprobante de entrega (máx. 5MB)</small>
                                        
                                        <!-- Contenedor para la vista previa del PDF -->
                                        <div id="pdf-preview-container" class="mt-3" style="display: none;">
                                            <div class="card">
                                                <div class="card-header bg-light py-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0"><i class="fas fa-eye mr-2"></i>Vista previa del PDF</h6>
                                                        <button type="button" class="btn btn-sm btn-danger" id="remove-pdf">
                                                            <i class="fas fa-times"></i> Quitar
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-0" style="height: 400px;">
                                                    <iframe id="pdf-preview" style="width: 100%; height: 100%; border: none;"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                                </button>
                                <a href="<?= base_url('entregas') ?>" class="btn btn-outline-secondary btn-lg ml-2">
                                    <i class="fas fa-times mr-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Previsualizar PDF cuando se selecciona
    $('#comprobante_entrega').on('change', function() {
        var file = this.files[0];
        var fileName = file ? file.name : '';
        $(this).next('.custom-file-label').html(fileName || '<?= (!empty($entrega['comprobante_entrega'])) ? 'Cambiar PDF' : 'Seleccionar PDF' ?>');
        
        // Mostrar vista previa si es un PDF
        if (file && file.type === 'application/pdf') {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $('#pdf-preview').attr('src', e.target.result);
                $('#pdf-preview-container').fadeIn();
            };
            
            reader.readAsDataURL(file);
        } else {
            $('#pdf-preview-container').hide();
        }
    });

    // Botón para quitar el PDF
    $('#remove-pdf').on('click', function() {
        $('#comprobante_entrega').val('');
        $('.custom-file-label').html('<?= (!empty($entrega['comprobante_entrega'])) ? 'Cambiar PDF' : 'Seleccionar PDF' ?>');
        $('#pdf-preview-container').hide();
    });

    // Validación del formulario con jQuery Validate
    $('.form-validate').validate({
        rules: {
            fechahoraentrega: {
                required: true
            },
            observaciones: {
                required: true,
                minlength: 10
            },
            comprobante_entrega: {
                extension: "pdf"
            }
        },
        messages: {
            fechahoraentrega: {
                required: "La fecha de entrega es requerida"
            },
            observaciones: {
                required: "Por favor describa el formato de entrega",
                minlength: "Debe describir mínimo 10 caracteres"
            },
            comprobante_entrega: {
                extension: "Solo se permiten archivos PDF"
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
});
</script>



<?= $footer; ?>