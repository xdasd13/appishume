<?= $header; ?>

<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="card-title"><i class="fas fa-truck-loading mr-2"></i>Registrar Nueva Entrega</h4>
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

                        <!-- Tarjeta con información del usuario actual que realizará la entrega -->
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3">
                                    <?= substr($usuario_actual, 0, 1) ?>
                                </div>
                                <div>
                                    <h6 class="mb-0">Usuario que realizará la entrega: <strong><?= $usuario_actual ?></strong></h6>
                                    <small>La entrega quedará registrada a nombre del usuario actualmente conectado</small>
                                </div>
                            </div>
                        </div>
                        
                        <form action="<?= base_url('entregas/guardar') ?>" method="post" class="form-validate" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <!-- Campo oculto con el ID del usuario actual -->
                            <input type="hidden" name="idusuario_creacion" value="<?= $usuario_id ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="idcontrato" class="form-label">
                                            <i class="fas fa-file-contract mr-2 text-primary"></i>Contrato Pagado *
                                        </label>
                                        <?php if (isset($contrato)): ?>
                                            <input type="hidden" name="idcontrato" value="<?= $contrato['idcontrato'] ?>">
                                            <input type="text" class="form-control" value="Contrato #<?= $contrato['idcontrato'] ?>" readonly>
                                            <small class="form-text text-success">Este contrato está pagado al 100%</small>
                                        <?php else: ?>
                                            <select class="form-control select2" id="idcontrato" name="idcontrato" required>
                                                <option value="">Seleccione un contrato</option>
                                                <?php foreach ($contratos as $contrato): ?>
                                                    <option value="<?= $contrato['idcontrato'] ?>" 
                                                        <?= old('idcontrato') == $contrato['idcontrato'] ? 'selected' : '' ?>>
                                                        Contrato #<?= $contrato['idcontrato'] ?> - 
                                                        <?= $contrato['cliente_nombre'] ?> 
                                                        (<?= date('d/m/Y', strtotime($contrato['fechaevento'])) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="form-text text-muted">Solo se muestran contratos pagados al 100%</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="idserviciocontratado" class="form-label">
                                            <i class="fas fa-briefcase mr-2 text-info"></i>Servicio Contratado *
                                        </label>
                                        <select class="form-control select2" id="idserviciocontratado" name="idserviciocontratado" required <?= isset($servicios) ? '' : 'disabled' ?>>
                                            <?php if (isset($servicios) && !empty($servicios)): ?>
                                                <option value="">Seleccione un servicio</option>
                                                <?php foreach ($servicios as $servicio): ?>
                                                    <option value="<?= $servicio['idserviciocontratado'] ?>">
                                                        <?= $servicio['servicio'] ?> (<?= date('d/m/Y', strtotime($servicio['fechahoraservicio'])) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="">Primero seleccione un contrato</option>
                                            <?php endif; ?>
                                        </select>
                                        <small class="form-text text-muted">Seleccione el servicio ya realizado para programar la entrega</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Nota informativa sobre la fecha automática -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        <strong>Nota:</strong> La fecha y hora de entrega se registrará automáticamente al momento de guardar.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="observaciones" class="form-label">
                                            <i class="fas fa-sticky-note mr-2 text-info"></i>Formato de Entrega *
                                        </label>
                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Ej: USB físico, link digital, cuadros 30x40, etc." required><?= old('observaciones') ?></textarea>
                                        <small class="form-text text-muted">Describa el formato de entrega (físico/digital) y especificaciones</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="comprobante_entrega" class="form-label">
                                            <i class="fas fa-file-pdf mr-2 text-danger"></i>Comprobante de Entrega (PDF) *
                                        </label>
                                        <div class="custom-file mb-2">
                                            <input type="file" class="custom-file-input" id="comprobante_entrega" name="comprobante_entrega" accept=".pdf" required>
                                            <label class="custom-file-label" for="comprobante_entrega">Seleccionar archivo PDF</label>
                                        </div>
                                        <small class="form-text text-muted">Suba el comprobante de entrega en formato PDF (máx. 5MB)</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="confirmacion" required>
                                    <label class="custom-control-label" for="confirmacion">
                                        Confirmo que el servicio ya fue realizado y la información de entrega es correcta
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save mr-2"></i>Registrar Entrega
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
    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap',
        placeholder: 'Seleccione una opción',
        allowClear: true
    });

    // Cargar servicios cuando se selecciona un contrato
    $('#idcontrato').on('change', function() {
        var idcontrato = $(this).val();
        
        if (idcontrato) {
            // Habilitar select de servicios
            $('#idserviciocontratado').prop('disabled', false);
            
            // Limpiar opciones actuales
            $('#idserviciocontratado').empty().append('<option value="">Cargando servicios...</option>');
            
            // Hacer petición AJAX para obtener servicios
            $.ajax({
                url: '<?= base_url("entregas/obtenerServiciosPorContrato/") ?>' + idcontrato,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#idserviciocontratado').empty().append('<option value="">Seleccione un servicio</option>');
                        
                        if (response.servicios.length > 0) {
                            $.each(response.servicios, function(index, servicio) {
                                $('#idserviciocontratado').append(
                                    $('<option>', {
                                        value: servicio.idserviciocontratado,
                                        text: servicio.servicio + ' (' + 
                                              formatDate(servicio.fechahoraservicio) + ')'
                                    })
                                );
                            });
                        } else {
                            $('#idserviciocontratado').append(
                                $('<option>', {
                                    value: '',
                                    text: 'No hay servicios disponibles para este contrato'
                                })
                            ).prop('disabled', true);
                        }
                    } else {
                        alert('Error al cargar los servicios');
                    }
                },
                error: function() {
                    alert('Error de conexión al cargar los servicios');
                    $('#idserviciocontratado').empty().append('<option value="">Error al cargar</option>');
                }
            });
        } else {
            $('#idserviciocontratado').empty().append('<option value="">Primero seleccione un contrato</option>').prop('disabled', true);
        }
    });


    // Formatear fecha para mostrar
    function formatDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('es-ES');
    }
    

    // Validación del formulario con jQuery Validate
    $('.form-validate').validate({
        rules: {
            idcontrato: {
                required: true
            },
            idserviciocontratado: {
                required: true
            },
            observaciones: {
                required: true,
                minlength: 10
            },
            comprobante_entrega: {
                required: true,
                extension: "pdf"
            }
        },
        messages: {
            idcontrato: {
                required: "Por favor seleccione un contrato"
            },
            idserviciocontratado: {
                required: "Por favor seleccione un servicio"
            },
            observaciones: {
                required: "Por favor describa el formato de entrega",
                minlength: "Debe describir mínimo 10 caracteres"
            },
            comprobante_entrega: {
                required: "Por favor seleccione un comprobante PDF",
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

<style>
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        display: block;
    }
    .select2-container--bootstrap .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: 0.375rem 0.75rem;
    }
    .form-validate .form-control {
        border-radius: 6px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
    }
    .form-validate .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #ddd;
    }
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1.1rem;
        border-radius: 6px;
    }
    .custom-checkbox {
        margin-top: 1rem;
    } 
    .avatar {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .alert-info {
        background-color: #eef5ff;
        border-color: #c9e0ff;
    }

    @media (max-width: 768px) {
        .card-header .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .card-header .btn {
            margin-top: 10px;
            margin-left: 0 !important;
            align-self: flex-end;
        }
    }
</style>

<?= $footer; ?>